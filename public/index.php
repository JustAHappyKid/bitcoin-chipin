<?php

function qaq($qaq){
	echo "<pre>";
	print_r($qaq);
	echo "</pre>";
}

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

// Define application environment

//production

defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

//qaq('APPLICATION_PATH: ' . APPLICATION_PATH);

// Look for environment variable indicating where Zend Framework is installed, defaulting to
// the parent-directory of this directory if needed, and assert the framework is there...
$zendDir = getenv('ZEND_FRAMEWORK_DIR') ? getenv('ZEND_FRAMEWORK_DIR') :
  realpath(dirname(__FILE__) . '/../');
$myPhpLibsDir = dirname(dirname(__FILE__)) . '/lib/';
if (!file_exists($zendDir . '/Zend/Application.php')) {
  echo "Could not locate Zend Framework (or, specifically, the expected Application.php file). " .
    "Zend Framework version 1.x required.";
  exit(-1);
} else if (!is_dir($myPhpLibsDir)) {
  echo "Environment variable 'MYPHPLIBS_DIR' must be set to location of 'my-php-libs'.";
  exit(-1);
} else {
  // Add Zend Framework to 'include_path'...
  set_include_path($myPhpLibsDir . PATH_SEPARATOR . realpath($zendDir) .
    PATH_SEPARATOR . get_include_path());
}

require_once 'my-php-libs/webapp/current-request.php';
use \MyPHPLibs\Webapp\CurrentRequest;

//define('PATH', 'http://localhost/bitcoinchipin.com/public/');
//define('PATH', 'http://bitcoinchipin.com/');
define('PATH', CurrentRequest\getProtocol() . '://' . CurrentRequest\getHost() . '/');

// Create application, bootstrap, and run...
require_once 'Zend/Application.php';
$appConfig = APPLICATION_PATH . '/configs/application.ini';
$application = new Zend_Application(APPLICATION_ENV, $appConfig);
$application->bootstrap()->run();
