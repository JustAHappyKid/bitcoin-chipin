<?php

require_once dirname(__FILE__) . '/PasswordEnabledController.php';
require_once 'chipin/conf-codes.php';

use \Chipin\ConfCodes;

class SigninController extends PasswordEnabledController {

  public function init() {
    $this->_helper->layout->setLayout('no-auth-layout');
  }

  public function indexAction() {
    $cp = $this->_getParam('cp', 0);
    if (isset($cp) && $cp) {
      $code = $this->_getParam('c');
      $userID = $this->_getParam('u');

      $reg = new Application_Model_Register();
      if ($this->isValidConfirmationCode($code, $userID)) {
        $hashedPass = $this->passwordHash($this->_getParam("password"));
        $reg->updateUsersPassword($hashedPass, $userID);
        $this->removeConfirmationCode($code, $userID);
      } else {
        $this->_redirect(PATH . 'signin/expired/c/'.$code.'/u/' . $userID);
      }
    }

    $form = new Application_Form_Signin();
    $this->view->success = $this->_getParam("s");
    $this->view->form = $form;

    if ($this->_getParam('signin', '') == 'failure') {
      $this->view->assign('failure', true);
    }
    if ($this->_getParam('remind', '') == 'success') {
      $this->view->assign('success', true);
    }
  }

  public function authAction() {
    $request = $this->getRequest();
    if ($request->isPost()) {
      $username = $this->_getParam('username');
      $password = $this->_getParam('password');
      if (!empty($username) && !empty($password)) {
        $existingHash = $this->getStoredHashForUser($username);
        $isGood = $this->passwordVerify($password, $existingHash);
        if ($isGood) {
          $dbAdapter = Zend_Db_Table::getDefaultAdapter();
          $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
          
          $authAdapter->setTableName("users");
          $authAdapter->setIdentityColumn("username");
          $authAdapter->setCredentialColumn("password");
          
          $authAdapter->setIdentity($username);
          $authAdapter->setCredential($this->passwordHash($password));
          
          $auth = Zend_Auth::getInstance();
          $result = $auth->authenticate($authAdapter);
          
          // XXX !!!
          //if ($result->isValid()) {
          if ($isGood) {
            // XXX !!!
            // $identity = $authAdapter->getResultRowObject();
            $identity = User::loadFromUsername($username);
            
            $authStorage = $auth->getStorage();
            $authStorage->write($identity);
                      
            if($identity->role == 'admin') {
              $this->_redirect(PATH.'admin/');
            }
            
            $this->_redirect(PATH.'dashboard/');
          } else {
            $this->_redirect(PATH.'signin/index/signin/failure/');
          }
        } else {
          $this->_redirect(PATH.'signin/index/signin/failure/');
        }
      } else {
        $this->_redirect(PATH.'signin/index/');
      }
    }
  }

  public function signoutAction() {
    $auth = Zend_Auth::getInstance();
    $auth->clearIdentity();
    $this->_redirect(PATH.'signin/index/');
  }

  public function remindAction() {
    $email = $this->_getParam('email');
    if ($email) {
      $user = new Application_Model_Users();
      if($user->sendLinkForChangingPassword($email))
        $this->_redirect(PATH . 'signin/index/remind/success/');
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

  private function isValidConfirmationCode($code, $uid) {
    return ConfCodes\isValidCode($code);
  }

  private function removeConfirmationCode($code, $_) {
    ConfCodes\removeCode($code);
  }

  private function getStoredHashForUser($username) {
    $_dbTable = Zend_Db_Table::getDefaultAdapter();
    $select = $_dbTable->select()
      ->from('users', 'password')
      ->where('username = "'.$username.'"');
    $result = $_dbTable->fetchAll($select);
    return $result[0]['password'];
  }
}
