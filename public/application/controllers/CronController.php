<?php

class CronController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
    }

    public function indexAction()
    {
        // action body
		
		$widget = new Application_Model_Widgets();
		$widget->updateWidgetsProgress();
    }


}

