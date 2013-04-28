<?php

require_once 'chipin/users.php';
require_once 'my-php-libs/database.php';

use \MyPHPLibs\Database as DB;

DB\setConnectionParams($driver = 'mysql', $dbName = 'chipin_test',
  $username = 'chipin_test', $password = 'password', $host = 'localhost');

function testUpdatePassword() {
  clearDB();
  $u1 = newUser('a@test.org', 'a', 'mysecretpass');
  $u2 = newUser('b@test.org', 'b', 'sumthingsecret');
  $u1->updatePassword('newpass');
  $rows = DB\selectAllRows('users');
  assertNotEqual($rows[0]['password'], $rows[1]['password']);
}

function clearDB() {
  DB\delete('widgets', 'TRUE', array());
  DB\delete('confirmation_codes', 'TRUE', array());
  DB\delete('users', 'TRUE', array());
}

function newUser($email, $username, $pw) {
  # XXX: Note, we're not hashing passwords here...
  $uid = DB\insertOne('users',
    array('email' => $email, 'username' => $username, 'password' => $pw), true);
  return User::loadFromID($uid);
}
