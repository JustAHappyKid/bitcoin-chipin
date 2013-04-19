<?php

require_once 'my-php-libs/password-hashing.php';
require_once 'my-php-libs/string.php';

abstract class PasswordEnabledController extends Zend_Controller_Action {

  protected function passwordHash($password) {
    return 'bcrypt' . password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
  }

  protected function passwordVerify($password, $hash) {
    $unprefixedHash = withoutPrefix($hash, 'bcrypt');
    return password_verify($password, $unprefixedHash);
  }
}
