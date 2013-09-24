<?php

namespace Chipin\Log;

require_once 'spare-parts/log.php';

function configure() {
  configureLogging('/var/local/log/bitcoinchipin.com/webapp.log');
}

function debug($m)  { return logMsg('debug', $m); }
function info($m)   { return logMsg('info', $m); }
function warn($m)   { return logMsg('warn', $m); }
function error($m)  { return logMsg('error', $m); }
