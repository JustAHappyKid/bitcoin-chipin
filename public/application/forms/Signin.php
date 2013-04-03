<?php

class Application_Form_Signin extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        
        $this->setMethod('POST');
		$this->setAction('../auth/signin/');
		
		$username = new Zend_Form_Element_Text('username');
		$username->setLabel('Username')->setAttribs(array('class' => 'login username-field', 'placeholder' => 'Usernanme'));
		$username->setDecorators(array(
			array('Label', array('escape' => false, 'placement' => 'append')),
			array('ViewHelper'),
			array('Errors'),
			array('Description', array('escape' => false, 'tag' => 'div')),
			array('HtmlTag', array('class' => 'field')),
		));
		
		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('Password')->setAttribs(array('class' => 'login password-field', 'placeholder' => 'Password'));
		
		$this->addDisplayGroup(array($username, $password), 'login-fields');
		
		$submit = new Zend_Form_Element_Button('Submit');
		$submit->setAttribs(array('class' => 'button btn btn-secondary btn-large', 'type' => 'submit'));
			
		$this->addElements(array($username, $password, $submit));
        
        
        
    }


}

