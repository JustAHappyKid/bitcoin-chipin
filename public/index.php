<?php

function qaq($qaq){
	echo "<pre>";
	print_r($qaq);
	echo "</pre>";
}

// Define path to application directory
if (!defined('APPLICATION_PATH')) {
  define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));
}

if (!defined('APPLICATION_ENV')) {
  $appEnv = getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development';
  define('APPLICATION_ENV', $appEnv);
}

define('DOC_ROOT', dirname(APPLICATION_PATH));

// Look for environment variable indicating where Zend Framework is installed, defaulting to
// the parent-directory of this directory if needed, and assert the framework is there...
$zendDir = getenv('ZEND_FRAMEWORK_DIR') ? getenv('ZEND_FRAMEWORK_DIR') :
  realpath(dirname(__FILE__) . '/../');
$libsDir = dirname(dirname(__FILE__)) . '/lib/';
if (!file_exists($zendDir . '/Zend/Application.php')) {
  echo "Could not locate Zend Framework (or, specifically, the expected Application.php file). " .
    "Zend Framework version 1.x required.";
  exit(-1);
} else if (!is_dir($libsDir)) {
  echo "Environment variable 'MYPHPLIBS_DIR' must be set to location of 'my-php-libs'.";
  exit(-1);
} else {
  // Add Zend Framework to 'include_path'...
  set_include_path($libsDir . PATH_SEPARATOR . realpath($zendDir) .
    PATH_SEPARATOR . get_include_path());
}

require_once 'my-php-libs/error-handling.php';
set_error_handler('\\MyPHPLibs\\ErrorHandling\\errorHandler');

require_once 'my-php-libs/webapp/current-request.php';
require_once 'my-php-libs/string.php';

use \MyPHPLibs\Webapp\CurrentRequest;

define('PATH', CurrentRequest\getProtocol() . '://' . CurrentRequest\getHost() . '/');

require_once 'Zend/Application.php';
$baseConfDir = dirname(dirname(__FILE__));
$confFiles = array("$baseConfDir/default-config.ini");
if (file_exists("$baseConfDir/local-config.ini")) $confFiles []= "$baseConfDir/local-config.ini";
$application = new Zend_Application(APPLICATION_ENV, array('config' => $confFiles));

if (beginsWith(CurrentRequest\getPath(), '/widget-wiz/')) {
  # NOTE: We still need to run the 'bootstrap' stuff even if we're bypassing Zend's
  # routing mechanism and et al.
  $application->bootstrap();
  require_once dirname(dirname(__FILE__)) . '/lib/chipin/route-request.php';
  \Chipin\WebFramework\routeRequestForApp();
} else {
  $application->bootstrap()->run();
}
