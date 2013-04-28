<?php

require_once dirname(__FILE__) . '/PasswordEnabledController.php';

class AccountController extends PasswordEnabledController {

  public function init() {
    $this->auth = Zend_Auth::getInstance();
    if (!$this->auth->hasIdentity())
      $this->_redirect(PATH.'signin/index/');
  }

  public function changepasswordAction() {
    if ($this->_getParam("change", "") == "true") {
      $hashedPass = $this->passwordHash($this->_getParam("password"));
      $user = $this->auth->getIdentity();
      $user->updatePassword($hashedPass);
      $this->view->success = true;
    }
    $this->view->assign('identity', $this->auth->getIdentity());
  }
}
