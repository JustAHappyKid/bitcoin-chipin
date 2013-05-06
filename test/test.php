#! /usr/bin/env php
<?php


use \MyPHPLibs\Test, \MyPHPLibs\Database as DB;

function main($argc, $argv) {
  $testDir = realpath(dirname(__FILE__));
  $libsDir = realpath(dirname($testDir) . '/lib');
  set_include_path($libsDir . PATH_SEPARATOR . get_include_path());
  require_once 'my-php-libs/database.php';
  DB\setConnectionParams($driver = 'mysql', $dbName = 'chipin_test',
    $username = 'chipin_test', $password = 'password', $host = 'localhost');
  require_once dirname($testDir) . '/lib/my-php-libs/test/base-framework.php';
  $filesToIgnore = array('test.php');
  Test\testScriptMain($testDir, $filesToIgnore, $argc, $argv);
}

function clearDB() {
  DB\delete('widgets', 'TRUE', array());
  DB\delete('confirmation_codes', 'TRUE', array());
  DB\delete('users', 'TRUE', array());
}

function newUser($email, $username, $pw) {
  require_once 'chipin/users.php';
  # XXX: Note, we're not hashing passwords here...
  $uid = DB\insertOne('users',
    array('email' => $email, 'username' => $username, 'password' => $pw), true);
  return User::loadFromID($uid);
}

main($argc, $argv);
