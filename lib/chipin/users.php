<?php

namespace Chipin;

require_once 'spare-parts/types.php';
require_once 'spare-parts/database.php';

use \SpareParts\Database as DB, \Exception, \DateTime;

class User {

  public $id, $email, $username, $passwordEncrypted;

  /** @var DateTime $createdAt */
  public $createdAt;

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

  public static function loadAll() {
    return array_map(
      function($row) { $u = new User; $u->populateFromRow($row); return $u; },
      DB\selectAllRows('users'));
  }

  public function populateFromRow($row) {
    $this->username = $row['username'];
    $this->id = $row['id'];
    $this->email = $row['email'];
    $this->passwordEncrypted = $row['password'];
    $this->createdAt = new DateTime($row['created_at']);
    return $this;
  }

  public function updatePassword($encryptedPass) {
    DB\query('UPDATE users SET password = ? WHERE id = ?', array($encryptedPass, $this->id));
  }
}

class NoSuchUser extends Exception {}
