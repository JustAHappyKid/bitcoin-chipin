<?php

require_once 'my-php-libs/types.php';
require_once 'my-php-libs/database.php';

use \MyPHPLibs\Database as DB;

class User {
  public $id, $email, $username; //, $password;
  
  function __construct() { }

  public static function loadFromID($id) {
    return User::loadFromQuery('id = ?', array($id));
  }

  public static function loadFromUsername($un) {
    return User::loadFromQuery('username = ?', array($un));
  }

  public static function loadFromQuery($query, $params) {
    $rows = DB\simpleSelect('users', $query, $params);
    if (count($rows) == 0) {
      throw new NoSuchUser("Could not find user for query '$query' and parameters " .
        asString($params));
    }
    $user = new User;
    $user->populateFromRow(current($rows));
    return $user;
  }

  public function populateFromRow($row) {
    $this->username = $row['username'];
    $this->id = $row['id'];
    $this->email = $row['email'];
    return $this;
  }
}

class NoSuchUser extends Exception {}
