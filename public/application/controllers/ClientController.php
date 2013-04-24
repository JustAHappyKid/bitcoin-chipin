<?php

require_once 'my-php-libs/string.php'; # withoutPrefix

class ClientController extends Zend_Controller_Action {

  public function init() {
    $this->_helper->layout->disableLayout();

    if ($this->_getParam("preview")) {

      $action = $this->_getParam("action");
      $dimensions = withoutPrefix($action, 'widget');
      list($this->view->width, $this->view->height) = $dimensions;

      /*
      $size = explode('widget', $action);
      $size = explode('x', $size[1]);
      $this->view->width = $size[0];
      $this->view->height = $size[1];
      */

      $this->view->address = $this->_getParam("address", "");
      $this->view->raised = $this->getContentUsingCURL('http://blockchain.info/q/addressbalance/'.$this->view->address)/100000000;
      $this->view->color = $this->_getParam("color", "E0DCDC,707070");
      $this->view->title = stripslashes($this->_getParam("title", ""));
      $this->view->goal = $this->_getParam("goal", "0");
      $this->view->start = $this->_getParam("start", date("mm/dd/yy"));
      $this->view->ending = $this->_getParam("ending", date("mm/dd/yy"));
      $this->view->about = stripslashes($this->_getParam("about", ""));
      $this->view->currency = $this->_getParam("currency", "");
      $this->view->progress = $this->view->raised / $this->view->goal * 100;
    } else {
      $id = $this->_getParam("id", "");
      $owner_id = $this->_getParam("owner_id", "");
      $w = new Application_Model_Widgets();
      $w->setIdentity($owner_id);

      $widget = $w->getWidgetById($id);
      foreach ($widget as $key => $value)
        $this->view->$key = $value;
    }

    if ($this->view->currency == "BTC") {
      $this->view->other_currency = "USD";
      $one_bitcoin_value = $this->getContentUsingCURL('http://blockchain.info/tobtc?currency=USD&value=1');
      $this->view->other_raised = $this->view->raised / $one_bitcoin_value;
      $this->view->other_goal = substr($this->view->goal / $one_bitcoin_value, 0, 5);
    } else {
      $this->view->other_currency = "BTC";
      $other_raised = $this->getContentUsingCURL('http://blockchain.info/tobtc?currency=' . $this->view->currency . '&value='.$this->view->raised);
      $this->view->other_raised = substr($other_raised, 0, 5);
      $other_goal = $this->getContentUsingCURL('http://blockchain.info/tobtc?currency=' . $this->view->currency . '&value='.$this->view->goal);
      $this->view->other_goal = substr($other_goal, 0, 5);
    }

    if ($this->_getParam("flash", 0) == 1) {
      exit(json_encode($this->view->getVars()));
    }
  }

  public function getContentUsingCURL($url) {
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
  }

  public function indexAction() {}

  public function widget250x250Action() {}
  public function widget120x60Action() {}
  public function widget125x125Action() {}
  public function widget160x250Action() {}
  public function widget220x220Action() {}
  public function widget234x60Action() {}
}
