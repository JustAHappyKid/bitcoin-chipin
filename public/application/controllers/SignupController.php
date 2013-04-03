<?php

class SignupController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        $this->_helper->layout->setLayout('layout1');
        
        $form = new Application_Form_Signup();
		$this->view->form = $form;
		
		if($this->_getParam("captcha", "") == "error")
			$this->view->captcha = true;
    }

    public function signupAction()
    {
        // action body
        $this->_helper->layout->disableLayout();
		
        $username = $this->_getParam("username");
		//$password = md5($this->_getParam("password"));
		
		
		////////////////////////////////////////////
		////////////////////////////////////////////
		$bcrypt = new Application_Model_Bcrypt(20);
		$password = $bcrypt->hash($this->_getParam("password"));
		////////////////////////////////////////////
		////////////////////////////////////////////
		
		
		$email = $this->_getParam("email");
		/*
		$captcha = $this->_getParam('captcha');
		$form = new Application_Form_Signup();
		if(!$form->isValid($captcha))
			$this->_redirect(PATH.'signup/index/captcha/error/');
		*/
		if($username != '' && $password != '')
		{
			$register = new Application_Model_Register();
			$register->createUser(array(
				'username' => $username,
				'password' => $password,
				'email' => $email
			));
			
			$dbAdapter = Zend_Db_Table::getDefaultAdapter();
			$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
			
			$authAdapter->setTableName("users");
			$authAdapter->setIdentityColumn("username");
			$authAdapter->setCredentialColumn("password");
			
			
			$authAdapter->setIdentity($username);
			$authAdapter->setCredential($password);
			
			$auth = Zend_Auth::getInstance();
			$result = $auth->authenticate($authAdapter);
			
			if($result->isValid())
			{
				$identity = $authAdapter->getResultRowObject();
				
				$authStorage = $auth->getStorage();
				$authStorage->write($identity);

				if($identity->role == 'admin'){
					$this->_redirect(PATH.'admin/index/');
				}

				$this->_redirect(PATH."dashboard/index/");
			}
			else
			{
				$this->_redirect(PATH.'signin/index/');
			}
		}
		else
		{
			$this->_redirect(PATH.'signin/index/');
		}
	
    }

}

?>