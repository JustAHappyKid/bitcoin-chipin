<?php

if (!defined('APPLICATION_ENV')) {
  $appEnv = getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development';
  define('APPLICATION_ENV', $appEnv);
}

$libsDir = dirname(dirname(__FILE__)) . '/lib/';
//set_include_path($libsDir . PATH_SEPARATOR . get_include_path());
require_once "$libsDir/chipin/env/init.php";
$baseConfDir = dirname(dirname(__FILE__));
$confFiles = array("$baseConfDir/default-config.ini");
if (file_exists("$baseConfDir/local-config.ini")) $confFiles []= "$baseConfDir/local-config.ini";
\Chipin\Environment\init($confFiles);

require_once 'spare-parts/webapp/current-request.php';
require_once 'chipin/webapp/framework.php';

use \SpareParts\Webapp\CurrentRequest;

define('PATH', CurrentRequest\getProtocol() . '://' . CurrentRequest\getHost() . '/');
\Chipin\WebFramework\routeRequestForApp();
