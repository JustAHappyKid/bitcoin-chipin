<?php

namespace Chipin\Widgets;

require_once 'my-php-libs/database.php';
require_once 'chipin/users.php';
require_once 'chipin/bitcoin.php';

use \MyPHPLibs\Database as DB, \User, \Chipin\Bitcoin;

class Widget {
  public $id, $ownerID, $title, $about, $ending, $goal, $currency, $raised,
    $width, $height, $color, $bitcoinAddress;

  # XXX: This one returns an object...
  # TODO: Make other functions return Widget object.
  public static function getByOwnerAndID(User $owner, $id) {
    $w = getByID($id);
    if ($w['owner_id'] != $owner->id) {
      throw new NoSuchWidget("Widget with ID $id is not owned by user with ID {$owner->id}");
    }
    $obj = new Widget;
    return $obj->populateFromArray($w);
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
    return $this;
  }

  public function save() {
    $this->ending = date("Y-m-d", strtotime($this->ending));
    $values = array(
      'owner_id' => $this->ownerID, 'title' => $this->title, 'about' => $this->about,
      'goal' => $this->goal, 'currency' => $this->currency, 'raised' => '0', 'progress' => 0,
      'ending' => $this->ending, 'address' => $this->bitcoinAddress,
      'width' => $this->width, 'height' => $this->height, 'color' => $this->color,
      // TODO: support country
      //'country' => $countryCode 
    );
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

function getWidgetById($id) { return getByID($id); }

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
  $id = DB\insertOne('widgets', $data, true);
  return $id;
}

function updateByID($id, Array $data) {
  DB\updateByID('widgets', $id, $data);
}

function updateProgress($row) {
  $balance = Bitcoin\getBalance($row['address'], $row['currency']);
  $progress = $balance / $row['goal'] * 100;
  DB\query("UPDATE widgets SET progress = ?, raised = ? WHERE id = ?",
           array($progress, $balance, $row['id']));
}

class NoSuchWidget extends \Exception {}
