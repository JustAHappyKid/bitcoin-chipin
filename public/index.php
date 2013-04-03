<?php

function qaq($qaq){
	echo "<pre>";
	print_r($qaq);
	echo "</pre>";
}

//define('PATH', 'http://localhost/bitcoinchipin.com/public/');
define('PATH', 'http://bitcoinchipin.com/');

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
if (file_exists($zendDir . '/Zend/Application.php')) {
  // Add Zend Framework to 'include_path'...
  set_include_path(realpath($zendDir) . PATH_SEPARATOR . get_include_path());
} else {
  echo "Could not locate Zend Framework (or, specifically, the expected Application.php file). " .
    "Zend Framework version 1.x required.";
  exit(-1);
}

require_once 'Zend/Application.php';

// Create application, bootstrap, and run...
$appConfig = APPLICATION_PATH . '/configs/application.ini';
$application = new Zend_Application(APPLICATION_ENV, $appConfig);
$application->bootstrap()->run();
