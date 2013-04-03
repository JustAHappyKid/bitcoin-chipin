<?php

class AccountController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->auth = Zend_Auth::getInstance();
		
		if(!$this->auth->hasIdentity())
			$this->_redirect(PATH.'signin/index/');

    }

    public function indexAction()
    {
        // action body
    }

    public function changepasswordAction()
    {
        // action body
        if($this->_getParam("change", "") == "true")
		{
			$reg = new Application_Model_Register();
			
			$bcrypt = new Application_Model_Bcrypt(20);
			$password = $bcrypt->hash($this->_getParam("password"));
			
			$reg->updateUsersPassword(array('password' => $password), $this->auth->getIdentity()->id);
			$this->view->success = true;
		}
        
        $this->view->assign('identity', $this->auth->getIdentity());
    }


}



