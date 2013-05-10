<?php

require_once 'my-php-libs/types.php';
require_once 'my-php-libs/database.php';

use \MyPHPLibs\Database as DB;

class User {
  public $id, $email, $username, $passwordEncrypted;

  function __construct() { }

  public static function loadFromID($id) {
    return User::loadFromQuery('id = ?', array($id));
  }

  public static function loadFromUsername($un) {
    return User::loadFromQuery('username = ?', array($un));
  }

  public static function loadFromEmailAddr($e) {
    return User::loadFromQuery('LOWER(email) = LOWER(?)', array($e));
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
    $this->passwordEncrypted = $row['password'];
    return $this;
  }

  public function updatePassword($encryptedPass) {
    DB\query('UPDATE users SET password = ? WHERE id = ?', array($encryptedPass, $this->id));
  }
}

class NoSuchUser extends Exception {}
