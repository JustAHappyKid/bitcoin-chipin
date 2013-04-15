<?php

/**
 * F*** Zend Framework -- I can't even have common base-classes for controllers?
 */

require_once 'my-php-libs/password-hashing.php';

abstract class PasswordEnabledController extends Zend_Controller_Action {

  protected function passwordHash($password) {
    return password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
  }

  protected function passwordVerify($password, $hash) {
    return password_verify($password, $hash);
  }
}
