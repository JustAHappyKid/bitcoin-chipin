<?php

namespace Chipin\Environment;

use \SpareParts\Database as DB;

function init($confFiles) {
  $libsDir = dirname(dirname(dirname(__FILE__)));
  set_include_path($libsDir . PATH_SEPARATOR . get_include_path());
  requireStandardLibs();
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
  require_once 'chipin/env/log.php';
  \Chipin\Log\configure();
  $dbParam = function($p) use($conf) { return $conf['resources.db.params.' . $p]; };
  DB\setConnectionParams($driver = 'mysql', $dbName = $dbParam('dbname'),
    $username = $dbParam('username'), $password = $dbParam('password'),
    $host = $dbParam('host'));
  if (APPLICATION_ENV == 'development') require_once 'chipin/debug.php';
}

function requireStandardLibs() {
  require_once 'spare-parts/global-utils.php';  # at, tail, head
  require_once 'spare-parts/database.php';
  require_once 'spare-parts/string.php';        # Standard string-related functions are available.
  require_once 'chipin/users.php';              # User class
}

/**
 * @param array $files Configuration files, from lowest to highest precedence.
 * @return array An associative array mapping config variables to respective values.
 */
function readConfig(Array $files) {
  if (count($files) == 0) return array();
  else return array_merge(parse_ini_file(head($files)), readConfig(tail($files)));
}
