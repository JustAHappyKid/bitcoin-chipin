<?php

require_once 'my-php-libs/types.php'; # for access to 'asString'

require_once 'my-php-libs/log.php';
configureLogging('/var/local/log/chipin-dev/webapp.log');
function debug($m)  { return logMsg('debug', $m); }
function info($m)   { return logMsg('info', $m); }
function error($m)  { return logMsg('error', $m); }
