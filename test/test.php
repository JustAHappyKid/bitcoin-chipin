#! /usr/bin/env php
<?php

use \SpareParts\Test, \SpareParts\Database as DB, \Chipin\User, \Chipin\Passwords;

function main($argc, $argv) {
  $testDir = realpath(dirname(__FILE__));
  $libsDir = realpath(dirname($testDir) . '/lib');
  set_include_path($libsDir . PATH_SEPARATOR . get_include_path());

  set_include_path(
    dirname(__FILE__) . '/mock/lib' . PATH_SEPARATOR .
    # XXX: Zend Framework required for now, unfortunately (for Captcha lib, at least)...
    '/usr/local/lib/php5/ZendFramework-1/library/' .
    PATH_SEPARATOR . get_include_path());

  error_reporting(E_ALL);

  require_once 'spare-parts/database.php';
  DB\setConnectionParams($driver = 'mysql', $dbName = 'chipin_test',
    $username = 'chipin_test', $password = 'password', $host = 'localhost');
  require_once dirname($testDir) . '/lib/spare-parts/test/base-framework.php';
  $filesToIgnore = array('test.php');
  Test\testScriptMain($testDir, $filesToIgnore, $argc, $argv);
}

function clearDB() {
  DB\delete('widgets', 'TRUE', array());
  DB\delete('confirmation_codes', 'TRUE', array());
  DB\delete('subscriptions', 'TRUE', array());
  DB\delete('users', 'TRUE', array());
}

function newUser($email, $username, $pw) {
  require_once 'chipin/users.php';
  require_once 'chipin/passwords.php';
  # XXX: Note, we're not hashing passwords here...
  $uid = DB\insertOne('users',
    array('email' => $email, 'username' => $username, 'password' => Passwords\hash($pw)), true);
  return User::loadFromID($uid);
}

main($argc, $argv);
