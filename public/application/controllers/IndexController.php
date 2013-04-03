<?php

class IndexController extends Zend_Controller_Action
{

	private $auth;

    public function init()
    {
        /* Initialize action controller here */
        
        $this->auth = Zend_Auth::getInstance();
		
		if(!$this->auth->hasIdentity())
			$this->_redirect(PATH.'signin/index/');
		
		if($this->auth->getIdentity()->role == 'admin'){
			$this->_redirect(PATH.'admin/index/');
		}
		
		$this->_redirect(PATH.'dashboard/index/');
    }

    public function indexAction()
    {
        // action body
    }

}



?>