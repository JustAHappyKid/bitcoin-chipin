<?php

class Application_Form_Signup extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */

		$captcha = new Zend_Form_Element_Captcha('captcha', array(
			'captcha' => array(
				'captcha' => 'Image',
				'wordLen' => 4,
				'timeout' => 300,
				'width' => '100',
				'height' => '41',
				'font' => 'fonts/arial.ttf',
				'imgUrl' => PATH .'images/captcha',
                'imgDir' => 'images/captcha'
			)
		));
		
		$captcha->setDecorators(array(
			array('Description', array('escape' => false, 'tag' => 'div')),
			array('HtmlTag', array('tag' => 'div', 'class' => 'field')),
		))->setAttrib('placeholder', 'Captcha');
		$this->addElement($captcha);
		
    }


}

