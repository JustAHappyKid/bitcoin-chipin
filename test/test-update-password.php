<?php

require_once 'chipin/users.php';
require_once 'my-php-libs/database.php';

use \MyPHPLibs\Database as DB;

function testUpdatePassword() {
  clearDB();
  $u1 = newUser('a@test.org', 'a', 'mysecretpass');
  $u2 = newUser('b@test.org', 'b', 'sumthingsecret');
  $u1->updatePassword('newpass');
  $rows = DB\selectAllRows('users');
  assertNotEqual($rows[0]['password'], $rows[1]['password']);
}
