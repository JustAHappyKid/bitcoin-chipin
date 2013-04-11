<?php

class SigninController extends Zend_Controller_Action {

  public function init() {
    $this->_helper->layout->setLayout('layout1');
  }

  public function indexAction() {
    $cp = $this->_getParam('cp', 0);
    if (isset($cp) && $cp) {
      $code = $this->_getParam('c');
      $userID = $this->_getParam('u');

      $reg = new Application_Model_Register();
      //$result = $reg->IfExpired($code, $userID);
      if ($reg->isValidConfirmation($code, $userID)) {
        //$bcrypt = new Application_Model_Bcrypt(20);
        //$password = $bcrypt->hash($this->_getParam("password"));
        $hashedPass = $this->passwordHash($this->_getParam("password"));
        $reg->updateUsersPassword(array('password' => $hashedPass), $userID);
        $reg->removeConfirmationLink($code, $userID);
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
    //$bcrypt = new Application_Model_Bcrypt(15);
    if ($request->isPost()) {
      $username = $this->_getParam('username');
      $password = $this->_getParam('password');
      if (!empty($username) && !empty($password)) {
        $existingHash = $this->getStoredHashForUser($username);
        //$isGood = $bcrypt->verify($password, $existingHash);
        $isGood = $this->passwordVerify($password, $existingHash);
        if ($isGood) {
          $dbAdapter = Zend_Db_Table::getDefaultAdapter();
          $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
          
          $authAdapter->setTableName("users");
          $authAdapter->setIdentityColumn("username");
          $authAdapter->setCredentialColumn("password");
          
          $authAdapter->setIdentity($username);
          //$authAdapter->setCredential($bcrypt->hash($password));
          $authAdapter->setCredential($this->passwordHash($password));
          
          $auth = Zend_Auth::getInstance();
          $result = $auth->authenticate($authAdapter);
          
          if ($result->isValid()) {
            $identity = $authAdapter->getResultRowObject();
            
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
    $reg = new Application_Model_Register();
    //$result = $reg->IfExpired($code, $userID);
    if ($reg->isValidConfirmation($code, $userID)) {
      $this->_redirect(PATH . 'signin/change/c/'.$code.'/u/' . $userID);
    } else {
      $this->_redirect(PATH . 'signin/expired/c/'.$code.'/u/' . $userID);
    }
  }

  public function expiredAction() {
    $code = $this->_getParam('c');
    $user_id = $this->_getParam('u');
    $reg = new Application_Model_Register();
    $reg->removeConfirmationLink($code, $user_id);
    //..
  }

  public function changeAction() {
    $code = $this->_getParam('c');
    $userID = $this->_getParam('u');
    $reg = new Application_Model_Register();
    //$result = $reg->IfExpired($code, $userID);
    if ($reg->isValidConfirmation($code, $userID)) {
      $this->view->assign('user_id', $userID);
      $this->view->assign('code', $code);
    } else {
      $this->_redirect(PATH . 'signin/expired/c/'.$code.'/u/' . $userID);
    }
    //...
  }

  private function getStoredHashForUser($username) {
    $_dbTable = Zend_Db_Table::getDefaultAdapter();
    $select = $_dbTable->select()
      ->from('users', 'password')
      ->where('username = "'.$username.'"');
    $result = $_dbTable->fetchAll($select);
    return $result[0]['password'];
  }

  private function passwordHash($password) {
    return password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
  }

  private function passwordVerify($password, $hash) {
    return password_verify($password, $hash);
  }
}
