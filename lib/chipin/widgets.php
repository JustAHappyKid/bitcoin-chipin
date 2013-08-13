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
  public $goal, $raised;

  /** @var Amount */
  public $goalAmnt;

  /** @var Amount */
  public $raisedAmnt;

  # XXX: This one returns an object...
  # TODO: Make other functions return Widget object.
  public static function getByOwnerAndID(User $owner, $id) {
    $w = self::getByID($id);
    if ($w->ownerID != $owner->id) {
      throw new NoSuchWidget("Widget with ID $id is not owned by user with ID {$owner->id}");
    }
    return $w;
  }

  public static function getByID($id) {
    return Widget::getByQuery('id = ?', array($id));
    /*
    $rows = select('id = ?', array($id));
    if (count($rows) == 0) {
      throw new NoSuchWidget("Could not find widget with ID $id");
    }
    $r = current($rows);
    $obj = new Widget;
    $obj->populateFromArray($r);
    return $obj;
    */
  }

  public static function getByURI(User $owner, $uriID) {
    # XXX: What about (unlikely) use-case where this could match two widgets?
    return Widget::getByQuery('owner_id = ? AND (id = ? OR uri_id = ?)',
      array($owner->id, $uriID, $uriID));
  }

  public static function getByQuery($query, Array $params) {
    $rows = select($query, $params);
    if (count($rows) == 0) {
      throw new NoSuchWidget("Could not find widget matching query '$query' with following " .
        "parameters: " . asString($params));
    }
    $r = current($rows);
    $obj = new Widget;
    $obj->populateFromArray($r);
    return $obj;
  }

  public static function getAll() {
    return array_map(
      function($row) {
        $o = new Widget;
        $o->populateFromArray($row);
        return $o; },
      getAll());
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

  public function save() {
//    $this->ending = date("Y-m-d", strtotime($this->ending));
    if (is_string($this->ending)) $this->ending = new DateTime($this->ending);
    else if (!($this->ending instanceof DateTime))
      throw new Exception("'ending' attribute should be string or DateTime object");
    $values = array(
      'owner_id' => $this->ownerID, 'title' => $this->title,
      'uri_id' => $this->uriID, 'about' => $this->about,
      'goal' => $this->goal, 'currency' => $this->currency, 'raised' => null, 'progress' => null,
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

/** @deprecated */
function getByOwner(User $owner) {
  return select('owner_id = ?', array($owner->id));
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

function updateProgressForObj(Widget $w) {
  $balance = Bitcoin\getBalance($w->bitcoinAddress, $w->currency);
  $progress = ($balance / $w->goal) * 100;
  DB\query("UPDATE widgets SET progress = ?, raised = ? WHERE id = ?",
           array($progress, $balance, $w->id));
}

function updateProgress($row) {
  $balance = Bitcoin\getBalance($row['address'], $row['currency']);
  $progress = $balance / $row['goal'] * 100;
  DB\query("UPDATE widgets SET progress = ?, raised = ? WHERE id = ?",
           array($progress, $balance, $row['id']));
}

function endWidget(Widget $w) {
  updateByID($w->id, array('ending' => date("Y-m-d", time() - (1 * 24 * 60 * 60))));
}

function allowedColors() { return array('white', 'silver', 'blue', 'dark'); }

class NoSuchWidget extends \Exception {}
