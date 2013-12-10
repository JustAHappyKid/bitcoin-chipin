<?php

namespace Chipin\Environment;

require_once 'spare-parts/global-utils.php';  # at, tail, head
require_once 'spare-parts/database.php';

use \SpareParts\Database as DB;

function init($confFiles) {
  $conf = readConfig($confFiles);
  $e = trim(at($conf, 'admin.email'));
  if (empty($e) && APPLICATION_ENV != 'development') {
    echo "Configuration parameter admin.email is not set! This is necessary to ensure " .
      "someone is notified of any errors that occur.";
    exit(-1);
  }
  error_reporting(E_ALL);
  require_once 'spare-parts/error-handling.php';
  \SpareParts\ErrorHandling\initErrorHandling($e);
//  \SpareParts\ErrorHandling\initErrorHandling('chriswagner@downsizedc.org');

  require_once 'chipin/env/log.php';
  \Chipin\Log\configure();

  //define('ADMIN_EMAIL', $e);
  $dbParam = function($p) use($conf) { return $conf['resources.db.params.' . $p]; };
  DB\setConnectionParams($driver = 'mysql', $dbName = $dbParam('dbname'),
    $username = $dbParam('username'), $password = $dbParam('password'),
    $host = $dbParam('host'));
//  $params = $options['resources']['db']['params'];
//  DB\setConnectionParams($driver = 'mysql', $dbName = $params['dbname'],
//    $username = $params['username'], $password = $params['password'],
//    $host = $params['host']);

//    $tr = new Zend_Mail_Transport_Smtp('localhost');
//    Zend_Mail::setDefaultTransport($tr);

  # Make sure User class is accessible...
  require_once 'chipin/users.php';

  if (APPLICATION_ENV == 'development') require_once 'chipin/debug.php';
}

/**
 * @param array $files Configuration files, from lowest to highest precedence.
 * @return array An associative array mapping config variables to respective values.
 */
function readConfig(Array $files) {
  if (count($files) == 0) return array();
  else return array_merge(readConfig(tail($files)), parse_ini_file(head($files)));
//  $values = array();
//  foreach ($files as $f) {
//    $values = array_merge($values, parse_ini_file($f));
//  }
//  return $values;
}
