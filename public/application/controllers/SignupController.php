<?php

require_once 'my-php-libs/password-hashing.php';

class SignupController extends Zend_Controller_Action {

  protected function passwordHash($password) {
    return password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
  }

  protected function passwordVerify($password, $hash) {
    return password_verify($password, $hash);
  }

  public function indexAction() {
    $this->_helper->layout->setLayout('no-auth-layout');
    $form = new Application_Form_Signup();
    $this->view->form = $form;
    if($this->_getParam("captcha", "") == "error")
        $this->view->captcha = true;
  }

  public function signupAction() {
    $this->_helper->layout->disableLayout();

    $username = $this->_getParam("username");
    $pw = $this->_getParam("password");
    $passwordHashed = $this->passwordHash($this->_getParam("password"));

    $email = $this->_getParam("email");
    /*
    $captcha = $this->_getParam('captcha');
    $form = new Application_Form_Signup();
    if(!$form->isValid($captcha))
      $this->_redirect(PATH.'signup/index/captcha/error/');
    */
    if ($username != '' && $pw != '') {

      $register = new Application_Model_Register();
      $register->createUser(array(
        'username' => $username,
        'password' => $passwordHashed,
        'email' => $email
      ));

      $dbAdapter = Zend_Db_Table::getDefaultAdapter();
      $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
      
      $authAdapter->setTableName("users");
      $authAdapter->setIdentityColumn("username");
      $authAdapter->setCredentialColumn("password");

      $authAdapter->setIdentity($username);
      $authAdapter->setCredential($passwordHashed);
      
      $auth = Zend_Auth::getInstance();
      $result = $auth->authenticate($authAdapter);
      
      if($result->isValid()) {
        $identity = $authAdapter->getResultRowObject();
        
        $authStorage = $auth->getStorage();
        $authStorage->write($identity);

        if($identity->role == 'admin') {
          $this->_redirect(PATH.'admin/index/');
        }

        $this->_redirect(PATH."dashboard/index/");
      } else {
        $this->_redirect(PATH.'signin/index/');
      }
    }
    else {
      $this->_redirect(PATH.'signin/index/');
    }
  }
}
