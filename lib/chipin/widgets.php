<?php

namespace Chipin\Widgets;

require_once 'spare-parts/database.php';
require_once 'spare-parts/database/paranoid.php';
require_once 'chipin/users.php';
require_once 'chipin/bitcoin.php';

use \SpareParts\Database as DB, \SpareParts\Database\Paranoid as ParanoidDB,
  \Chipin\User, \Chipin\Bitcoin;

class Widget {

  public $id, $ownerID, $title, $about, $ending, $goal, $currency, $raised,
    $width, $height, $color, $bitcoinAddress, $countryCode;

  # XXX: This one returns an object...
  # TODO: Make other functions return Widget object.
  public static function getByOwnerAndID(User $owner, $id) {
    $w = self::getByID($id);
    if ($w->ownerID != $owner->id) {
      throw new NoSuchWidget("Widget with ID $id is not owned by user with ID {$owner->id}");
    }
    return $w;
//    $obj = new Widget;
//    $obj->populateFromArray($w);
//    return $obj;
  }

  public static function getByID($id) {
    $rows = select('id = ?', array($id));
    if (count($rows) == 0) {
      throw new NoSuchWidget("Could not find widget with ID $id");
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
    $this->bitcoinAddress = $a['address'];
    $this->id = (int) $a['id'];
    $this->ownerID = (int) $a['owner_id'];
    $this->countryCode = $a['country'];
    return $this;
  }

  public function save() {
    $this->ending = date("Y-m-d", strtotime($this->ending));
    $values = array(
      'owner_id' => $this->ownerID, 'title' => $this->title, 'about' => $this->about,
      'goal' => $this->goal, 'currency' => $this->currency, 'raised' => '0', 'progress' => 0,
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

function getByOwner(User $owner) {
  return select('owner_id = ?', array($owner->id));
}

function select($whereClause, $params = array()) {
  return DB\select('*, DATE_FORMAT(ending, "%m/%d/%Y") as ending', 'widgets',
                   $whereClause, $params);
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

class NoSuchWidget extends \Exception {}
