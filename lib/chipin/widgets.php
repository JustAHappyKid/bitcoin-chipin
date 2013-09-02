<?php

namespace Chipin\Widgets;

require_once 'spare-parts/database.php';
require_once 'spare-parts/database/paranoid.php';
require_once 'spare-parts/types.php';             # asString
require_once 'chipin/users.php';                  # User
require_once 'chipin/bitcoin.php';
require_once 'chipin/currency.php';

use \SpareParts\Database as DB, \SpareParts\Database\Paranoid as ParanoidDB,
  \Chipin\User, \Chipin\Bitcoin, \Chipin\Currency\Amount, \DateTime, \Exception;

class Widget {

  public $id, $ownerID, $title, $uriID, $about, $ending, $currency, $raisedBTC,
    $width, $height, $color, $bitcoinAddress, $countryCode;

  /** @deprecated */
  private $goal, $raised;

  /** @var Amount */
  public $goalAmnt;

  /** @var Amount */
  public $raisedAmnt;

  public static function getByOwnerAndID(User $owner, $id) {
    $w = self::getByID($id);
    if ($w->ownerID != $owner->id) {
      throw new NoSuchWidget("Widget with ID $id is not owned by user with ID {$owner->id}");
    }
    return $w;
  }

  public static function getByID($id) {
    return Widget::getOneByQuery('id = ?', array($id));
  }

  public static function getByURI(User $owner, $uriID) {
    # XXX: What about (unlikely) use-case where this could match two widgets?
    return Widget::getOneByQuery('owner_id = ? AND (id = ? OR uri_id = ?)',
      array($owner->id, $uriID, $uriID));
  }

  public static function getOneByQuery($query, Array $params) {
    $ws = Widget::getManyByQuery($query, $params);
    if (count($ws) == 0) {
      throw new NoSuchWidget("Could not find widget matching query '$query' with following " .
        "parameters: " . asString($params));
    }
    return current($ws);
  }

  public static function getManyByQuery($query, Array $params) {
    return array_map(
      function($row) {
        $obj = new Widget;
        $obj->populateFromArray($row);
        return $obj; },
      select($query, $params));
  }

  /** @return Widget[] */
  public static function getAll() { return self::getManyByQuery('TRUE', array()); }

  public static function getManyByOwner(User $owner) {
    return self::getManyByQuery('owner_id = ?', array($owner->id));
  }

  public function populateFromArray(Array $a) {
    foreach ($a as $key => $val) {
      if (is_string($key)) $this->$key = $val;
    }
    $this->ending = new \DateTime($a['ending']);
    $this->bitcoinAddress = $a['address'];
    $this->id = (int) $a['id'];
    $this->uriID = $a['uri_id'];
    $this->ownerID = (int) $a['owner_id'];
    $this->countryCode = $a['country'];
    //$this->satoshisRaised = $a['satoshis'];
    $this->goalAmnt = new Amount($this->currency, $a['goal']);
    $this->raisedBTC = Bitcoin\getBalance($this->address);
    $raisedInBaseCurrency = $this->currency == 'BTC' ?
      $this->raisedBTC : Bitcoin\fromBTC($this->raisedBTC, $this->currency);
    $this->raisedAmnt = new Amount($this->currency, $raisedInBaseCurrency);
    $goalInBTC = $this->currency == 'BTC' ? $a['goal'] :
      Bitcoin\toBTC($this->currency, $a['goal']);
    $this->progress = ($this->raisedBTC / $goalInBTC) * 100;
    $this->color = in_array($a['color'], allowedColors()) ? $a['color'] : 'white';
    return $this;
  }

  function __get($attr) {
    if ($attr == 'goal') return $this->goalAmnt->numUnits;
    else throw new \InvalidArgumentException("Widget has no attribute '$attr'");
  }

  public function setGoal($numUnits, $currency) {
    $this->goalAmnt = new Amount($currency, $numUnits);
    $this->currency = $currency;
  }

  public function save() {
//    $this->ending = date("Y-m-d", strtotime($this->ending));
    if (is_string($this->ending)) $this->ending = new DateTime($this->ending);
    else if (!($this->ending instanceof DateTime))
      throw new Exception("'ending' attribute should be string or DateTime object");
    $values = array(
      'owner_id' => $this->ownerID, 'title' => $this->title,
      'uri_id' => $this->uriID, 'about' => $this->about,
      'goal' => $this->goalAmnt->numUnits, 'currency' => $this->currency,
      'raised' => null, 'progress' => null,
      'ending' => $this->ending, 'address' => $this->bitcoinAddress,
      'width' => $this->width, 'height' => $this->height, 'color' => $this->color,
      'country' => $this->countryCode);
    if (isset($this->id)) {
      updateByID($this->id, $values);
    } else {
      $values['created'] = date('Y-m-d H:i:s');
      $this->id = addNewWidget($values);
    }
  }

  public function updateProgress() {
    $balance = Bitcoin\getBalance($this->bitcoinAddress, $this->currency);
    $progress = ($balance / $this->goalAmnt->numUnits) * 100;
    DB\query("UPDATE widgets SET progress = ?, raised = ? WHERE id = ?",
      array($progress, $balance, $this->id));
  }

  function getOwner() {
    return User::loadFromID($this->ownerID);
  }
  
  function endingDateAsString() {
    if (empty($this->ending)) {
      return '';
    } else if (is_string($this->ending)) {
      return $this->ending;
    } else if ($this->ending instanceof DateTime) {
      return $this->ending->format('Y-m-d');
    } else {
      throw new Exception("'ending' attribute must be string or DateTime object");
    }
  }

  function hasEnded() {
    $timestampOfEndDate = strtotime($this->ending->format('Y-m-d'));
    $timestampOfToday = strtotime(strftime('%Y-%m-%d'));
    return $timestampOfToday > $timestampOfEndDate;
  }

  public function setDimensions($w, $h) {
    if (!validDimensions($w, $h))
      throw new \InvalidArgumentException("{$w}x{$h} is not a valid widget size");
    $this->width = $w;
    $this->height = $h;
  }
}

function getAll() {
  return select('TRUE');
}

/** @deprecated Being phased out in favor of Widget::getByID */
function getWidgetById($id) { return getByID($id); }

/** @deprecated Being phased out in favor of Widget::getByID */
function getByID($id) {
  $rows = select('id = ?', array($id));
  if (count($rows) == 0) {
    throw new NoSuchWidget("Could not find widget with ID $id");
  }
  return current($rows);
}

function select($whereClause, $params = array()) {
  return DB\queryAndFetchAll(
    'SELECT w.*, DATE_FORMAT(w.ending, "%Y-%m-%d") as ending, a.satoshis
     FROM widgets w LEFT JOIN bitcoin_addresses a ON a.address = w.address
     WHERE ' . $whereClause, $params);
}

function addNewWidget($data) {
  $id = ParanoidDB\insertOne('widgets', $data, true);
  return $id;
}

function updateByID($id, Array $data) {
  ParanoidDB\updateByID('widgets', $id, $data);
}

function endWidget(Widget $w) {
  updateByID($w->id, array('ending' => date("Y-m-d", time() - (1 * 24 * 60 * 60))));
}

function allowedColors() { return array('white', 'silver', 'blue', 'dark'); }

class Dimensions {
  public $width, $height;
  function __construct($w, $h) { $this->width = $w; $this->height = $h; }
  function __toString() { return $this->width . 'x' . $this->height; }
}

/** @return Dimensions[] */
function allowedSizes() { return array(new Dimensions(350, 310), new Dimensions(200, 300),
                                       new Dimensions(200, 200)); }
function validDimensions($w, $h) {
  $matches = array_filter(allowedSizes(),
    function($d) use($w, $h) { return $d->width == $w && $d->height == $h; });
  return (count($matches) > 0);
}

class NoSuchWidget extends \Exception {}
