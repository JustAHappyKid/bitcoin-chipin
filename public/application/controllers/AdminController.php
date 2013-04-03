<?php

class AdminController extends Zend_Controller_Action
{

    private $auth = null;

    public function init()
    {
        /* Initialize action controller here */
        $this->auth = Zend_Auth::getInstance();
		
		if(!$this->auth->hasIdentity()){
			$this->_redirect(PATH.'signin/index/');
		}
		
		$this->view->assign('identity', $this->auth->getIdentity());
		
		$this->_helper->layout->setLayout('admin');
    }

    public function indexAction()
    {
        // action body
		$statistcis = new Application_Model_Statistics();
		
		$this->view->assign('widgets', $statistcis->getRecentWidgets());
		$this->view->assign('total_widgets_created', $statistcis->getTotalNumberOfWidgetsCreated());
		$this->view->assign('new_widgets', $statistcis->getNewWidgetsStatistics());
		$this->view->assign('ending_widgets', $statistcis->getEndingWidgetsStatistics());
		$this->view->assign('top_countries', $statistcis->getTopCountries());
		$this->view->assign('fastest_growing_widgets', $statistcis->getFastestGrowingWidgets());
		$this->view->assign('top_active_users', $statistcis->getTopActiveUsers());
    }

    public function listingAction()
    {
        // action body
        $statistcis = new Application_Model_Statistics();
		
		$this->view->assign('widgets', $statistcis->getAllWidgets());
    }

    public function widgetsAction()
    {
        // action body
    }

    public function endAction()
    {
        // action body
        $id = $this->_getParam('id', '');
		$widget = new Application_Model_Widgets();
		$widget->setIdentity($this->auth->getIdentity()->id);
		
		$widget->endWidget($id);
		
		$this->_redirect(PATH.'admin/');
    }


}







