<?php

require_once 'my-php-libs/database.php';
use \MyPHPLibs\Database as DB;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

  public function _initAdminEmail() {
    $options = $this->getOptions();
    $e = trim(@$options['admin']['email']);
    if (empty($e) && APPLICATION_ENV != 'development') {
      echo "Configuration parameter admin.email is not set! This is necessary to ensure " .
        "someone is notified of any errors that occur.";
      exit(-1);
    }
    define('ADMIN_EMAIL', $e);
  }

  public function _initDatabaseConn() {
    $options = $this->getOptions();
    $params = $options['resources']['db']['params'];
    DB\setConnectionParams($driver = 'mysql', $dbName = $params['dbname'],
      $username = $params['username'], $password = $params['password'],
      $host = $params['host']);
  }

  public function _initEmailMechanism() {
    $tr = new Zend_Mail_Transport_Smtp('localhost');
    Zend_Mail::setDefaultTransport($tr);
  }

  public function _initUserShit() {
    require_once 'chipin/users.php';  # User model
  }
}
