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
      $reg = new Application_Model_Register();
      $reg->updateUsersPassword(array('password' => $hashedPass), $this->auth->getIdentity()->id);
      $this->view->success = true;
    }
    $this->view->assign('identity', $this->auth->getIdentity());
  }
}
