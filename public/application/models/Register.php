<?php

require_once 'my-php-libs/database.php';
use \MyPHPLibs\Database as DB;

class Application_Model_Register {

  public function createUser($data) {
    DB\insertOne('users', $data);
  }

  public function updateUsersPassword($password, $uid) {
    // $this->_dbTable->update('users', $data, $id);
    DB\query('UPDATE users SET password = ? WHERE id = ?', array($password, $uid));
  }
}
