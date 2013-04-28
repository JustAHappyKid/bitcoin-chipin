<?php

require_once 'my-php-libs/database.php';
use \MyPHPLibs\Database as DB;

class Application_Model_Register {

  public function createUser($data) {
    DB\insertOne('users', $data);
  }

}
