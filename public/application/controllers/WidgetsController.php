<?php

class WidgetsController extends Zend_Controller_Action
{

    private $auth = null;

    public function init()
    {
        /* Initialize action controller here */
        $this->auth = Zend_Auth::getInstance();
		
		if(!$this->auth->hasIdentity()){
			$this->_redirect(PATH.'signin/');
		}
		
		$identity = $this->auth->getIdentity();
		if (isset($identity->role) && $identity->role == 'admin'){
			$this->_redirect(PATH.'admin/index/');
		}
		
		$this->view->assign('identity', $identity);
    }

    public function indexAction()
    {
        // action body
    }

    public function createAction()
    {
        // action body
    }

    public function ajaxsaveAction()
    {
        // action body
        
        $this->_helper->layout->disableLayout();
		
		$owner_id = $this->auth->getIdentity()->id;
        $width = $this->_getParam("width", "");
		$height = $this->_getParam("height", "");
		$address = $this->_getParam("address", "");
		
		// if create new widget is not working it can be because of this, check if array elements are ok
		$location = json_decode(file_get_contents('http://api.easyjquery.com/ips/?ip='. $_SERVER['REMOTE_ADDR']), true);
		$countryCode = $location['Country'];
		
		$widget = new Application_Model_Widgets();
		$widget->setIdentity($this->auth->getIdentity()->id);
		

		$edit = $this->_getParam("edit_widget", 0);
		if($edit){
			$widget_id = $this->_getParam("widget_id", "");
			$widget->updateWidgetById(array(
				'owner_id' => $owner_id,
				'progress' => 0,
				'title' => $this->_getParam("title", ""),
				'ending' => date("Y-m-d", strtotime($this->_getParam("ending", ""))),
				'goal' => $this->_getParam("goal", ""),
				'currency' => $this->_getParam("currency", ""),
				'raised' => '0',
				'width' => $width,
				'height' => $height,
				'color' => $this->_getParam("color", ""),
				'address' => $address,
				'about' => $this->_getParam("about", ""),
				'country' => $countryCode 
			), $widget_id);
		}
		else{
			$widget_id = $widget->addNewWidget(array(
				'owner_id' => $owner_id,
				'progress' => 0,
				'title' => $this->_getParam("title", ""),
				'ending' => date("Y-m-d", strtotime($this->_getParam("ending", ""))),
				'goal' => $this->_getParam("goal", ""),
				'currency' => $this->_getParam("currency", ""),
				'raised' => '0',
				'width' => $width,
				'height' => $height,
				'color' => $this->_getParam("color", ""),
				'address' => $address,
				'about' => $this->_getParam("about", ""),
				'country' => $countryCode,
				'created' => date('Y-m-d H:i:s')
			));
		}
		
		echo json_encode(array(
			"address" => $address,
			"width" => $width,
			"height" => $height,
			"owner_id" => $owner_id,
			"id" => $widget_id
		));
    }

    public function editAction()
    {
        // action body
		$id = $this->_getParam("id", "");
		$owner_id = $this->_getParam("owner_id", "");
		$w = new Application_Model_Widgets();
		$w->setIdentity($owner_id);
		
		$widget = $w->getWidgetById($id);
		$this->view->assign("widget", $widget);
    }

    public function ajaxcheckaddressAction()
    {
        // action body
        $this->_helper->layout->disableLayout();
        
        $address = $this->_getParam('address');
		$balance = file_get_contents("http://blockexplorer.com/q/checkaddress/".$address."/");
		if($balance == '00')
			echo 'true';
		else
			echo 'false';
    }

    public function deleteAction()
    {
        // action body
        $id = $this->_getParam('id', '');
		$widget = new Application_Model_Widgets();
		$widget->setIdentity($this->auth->getIdentity()->id);
		
		$widget->deleteWidgetById($id);
		
		$this->_redirect(PATH.'dashboard/');
    }

    public function endAction()
    {
        // action body
        $id = $this->_getParam('id', '');
		$widget = new Application_Model_Widgets();
		$widget->setIdentity($this->auth->getIdentity()->id);
		
		$widget->endWidget($id);
		
		$this->_redirect(PATH.'dashboard/');
    }


}


?>
