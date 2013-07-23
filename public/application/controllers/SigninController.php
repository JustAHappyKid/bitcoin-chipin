<?php

require_once dirname(__FILE__) . '/PasswordEnabledController.php';
require_once 'chipin/conf-codes.php';

use \Chipin\ConfCodes;

class SigninController extends PasswordEnabledController {

  public function init() {
    $this->_helper->layout->setLayout('no-auth-layout');
  }

  public function signoutAction() {
    $auth = Zend_Auth::getInstance();
    $auth->clearIdentity();
    $this->_redirect(PATH.'account/signin');
  }

  public function remindAction() {
    $email = $this->_getParam('email');
    if ($email) {
      $user = new Application_Model_Users();
      if($user->sendLinkForChangingPassword($email))
        $this->_redirect(PATH . 'account/signin');
      else
        $this->view->assign('failure', true);
    }
  }

  public function approveAction() {
    $code = $this->_getParam('code');
    $userID = $this->_getParam('user_id');
    if ($this->isValidConfirmationCode($code, $userID)) {
      $this->_redirect(PATH . 'signin/change/c/'.$code.'/u/' . $userID);
    } else {
      $this->_redirect(PATH . 'signin/expired/c/'.$code.'/u/' . $userID);
    }
  }

  public function expiredAction() {
    $code = $this->_getParam('c');
    $user_id = $this->_getParam('u');
    $this->removeConfirmationCode($code, $user_id);
  }

  public function changeAction() {
    $code = $this->_getParam('c');
    $userID = $this->_getParam('u');
    if ($this->isValidConfirmationCode($code, $userID)) {
      $this->view->assign('user_id', $userID);
      $this->view->assign('code', $code);
    } else {
      $this->_redirect(PATH . 'signin/expired/c/'.$code.'/u/' . $userID);
    }
  }

  private function isValidConfirmationCode($code, $_ = null) {
    return ConfCodes\isValidCode($code);
  }

  private function removeConfirmationCode($code, $_ = null) {
    ConfCodes\removeCode($code);
  }
}
