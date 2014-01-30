<?php

# XXX
$_SERVER['REQUEST_URI'] = '/index.php/1000';

//require_once "$libsDir/chipin/env/init.php";
//\Chipin\Environment\init($confFiles);

$libsDir = dirname(dirname(__FILE__)) . '/lib/';
require_once "$libsDir/chipin/short-url.php";

$pathParts = explode('/', $_SERVER['REQUEST_URI']);
$shortID = current(array_filter($pathParts,
  function($p) { return !empty($p) && $p != 'index.php'; }));

# TODO: Make decodeID raises InvalidArgumentException if not a valid "short ID".
$wid = \Chipin\ShortURL\decodeID($shortID);
echo "wid: $wid\n";
header("Location: https://bitcoinchipin.com/widgets/by-id/$wid");
