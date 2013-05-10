<?php

namespace Chipin\Passwords;

require_once 'my-php-libs/password-hashing.php';  # password_hash, password_verify
require_once 'my-php-libs/string.php';            # withoutPrefix

function hash($password) {
  return 'bcrypt' . password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
}

function isValid($password, $hash) {
  $unprefixedHash = withoutPrefix($hash, 'bcrypt');
  return password_verify($password, $unprefixedHash);
}
