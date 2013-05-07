<?php

require_once 'chipin/widgets.php';
require_once 'chipin/bitcoin.php';

use \Chipin\Widgets, \Chipin\Bitcoin;

class WidgetsController extends Zend_Controller_Action {

  private $auth = null;

  public function init() {
    $this->auth = Zend_Auth::getInstance();

    if (!$this->auth->hasIdentity()) {
      $this->_redirect(PATH.'signin/');
    }

    /*
    $identity = $this->auth->getIdentity();
    if (isset($identity->role) && $identity->role == 'admin') {
      $this->_redirect(PATH.'admin/index/');
    }
    */

    $this->view->assign('identity', $identity);
  }

  private function getUser() { return $this->auth->getIdentity(); }

  public function indexAction() { }

  public function createAction() { }

  public function ajaxsaveAction() {
    $this->_helper->layout->disableLayout();

    $owner_id = $this->auth->getIdentity()->id;
    $width = $this->_getParam("width", "");
    $height = $this->_getParam("height", "");
    $address = $this->_getParam("address", "");

    // if create new widget is not working it can be because of this; check if array elements are ok
    $ipInfoJSON = file_get_contents('http://api.easyjquery.com/ips/?ip='. $_SERVER['REMOTE_ADDR']);
    $location = json_decode($ipInfoJSON, true);
    $countryCode = $location['Country'];

    $widget = new Application_Model_Widgets();
    $widget->setIdentity($this->auth->getIdentity()->id);

    $edit = $this->_getParam("edit_widget", 0);
    if ($edit) {
      $widget_id = $this->_getParam("widget_id", "");
      //$widget->updateWidgetById(array(
      Widgets\updateByID($widget_id, array(
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
      )); //, $widget_id);
    } else {
      //$widget_id = $widget->addNewWidget(array(
      $widget_id = Widgets\addNewWidget(array(
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

  public function editAction() {
    $id = $this->_getParam("id", "");
    $owner_id = $this->_getParam("owner_id", "");
    /*
    $w = new Application_Model_Widgets();
    $w->setIdentity($owner_id);
    $widget = $w->getWidgetById($id);
    */
    $widget = Widgets\Widget::getByOwnerAndID($this->getUser(), $id);
    $this->view->assign("widget", $widget);
  }

  public function ajaxcheckaddressAction() {
    $this->_helper->layout->disableLayout();
    $address = $this->_getParam('address');
    try {
      $balance = Bitcoin\getBalance($address);
      echo ($balance == 0 ? 'true' : 'false');
    } catch (Bitcoin\InvalidAddress $_) {
      echo 'false';
    }
    /*
    $balance = file_get_contents("http://blockexplorer.com/q/checkaddress/".$address."/");
    if ($balance == '00')
      echo 'true';
    else
      echo 'false';
    */
  }

  public function deleteAction() {
    $id = $this->_getParam('id', '');
    $widget = new Application_Model_Widgets();
    $widget->setIdentity($this->auth->getIdentity()->id);
    $widget->deleteWidgetById($id);
    $this->_redirect(PATH.'dashboard/');
  }

  public function endAction() {
    $id = $this->_getParam('id', '');
    $widget = new Application_Model_Widgets();
    $widget->setIdentity($this->auth->getIdentity()->id);
    $widget->endWidget($id);
    $this->_redirect(PATH.'dashboard/');
  }
}
