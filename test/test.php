#! /usr/bin/env php
<?php


use \MyPHPLibs\Test;

function main($argc, $argv) {
  $testDir = realpath(dirname(__FILE__));
  $libsDir = realpath(dirname($testDir) . '/lib');
  set_include_path($libsDir . PATH_SEPARATOR . get_include_path());
  require_once dirname($testDir) . '/lib/my-php-libs/test/base-framework.php';
  $filesToIgnore = array('test.php');
  Test\testScriptMain($testDir, $filesToIgnore, $argc, $argv);
}

main($argc, $argv);
