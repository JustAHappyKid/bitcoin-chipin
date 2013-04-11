<?php

class AccountController extends Zend_Controller_Action {

  public function init() {
    $this->auth = Zend_Auth::getInstance();
    if (!$this->auth->hasIdentity())
      $this->_redirect(PATH.'signin/index/');
  }

  public function changepasswordAction() {
    if ($this->_getParam("change", "") == "true") {
      /*
      $bcrypt = new Application_Model_Bcrypt(20);
      $password = $bcrypt->hash($this->_getParam("password"));
      */
      $hashedPass = $this->passwordHash($this->_getParam("password"));
      $reg = new Application_Model_Register();
      $reg->updateUsersPassword(array('password' => $hashedPass), $this->auth->getIdentity()->id);
      $this->view->success = true;
    }
    $this->view->assign('identity', $this->auth->getIdentity());
  }

  private function passwordHash($password) {
    return password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
  }
}
