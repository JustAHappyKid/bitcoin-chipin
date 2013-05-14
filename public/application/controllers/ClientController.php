<?php

require_once 'my-php-libs/string.php';  # withoutPrefix
require_once 'chipin/bitcoin.php';      # toBTC, getBalance
require_once 'chipin/widgets.php';      # getWidgetById

use \Chipin\Bitcoin, \Chipin\Widgets;

class ClientController extends Zend_Controller_Action {

  public function init() {
    $this->_helper->layout->disableLayout();

    if ($this->_getParam("preview")) {
      $action = $this->_getParam("action");
      $dimensions = withoutPrefix($action, 'widget');
      list($this->view->width, $this->view->height) = explode('x', $dimensions);
      $this->view->address = $this->_getParam("address", "");
      $this->view->color = $this->_getParam("color", "E0DCDC,707070");
      $this->view->title = stripslashes($this->_getParam("title", ""));
      $this->view->goal = $this->_getParam("goal", "0");
      $this->view->start = $this->_getParam("start", date("mm/dd/yy"));
      $this->view->ending = $this->_getParam("ending", date("mm/dd/yy"));
      $this->view->about = stripslashes($this->_getParam("about", ""));
      $this->view->currency = $this->_getParam("currency", "");
      if (!empty($this->view->address))
        $this->view->raised = Bitcoin\getBalance($this->view->address, $this->view->currency);
      if ($this->view->goal)
        $this->view->progress = $this->view->raised / $this->view->goal * 100;
    } else {
      $id = $this->_getParam("id", "");
      $owner_id = $this->_getParam("owner_id", "");
      $widget = Widgets\getWidgetById($id);
      $widget['raised'] = Bitcoin\getBalance($widget['address'], $widget['currency']);
      $widget['progress'] = $widget['raised'] / $widget['goal'] * 100;
      foreach ($widget as $key => $value)
        $this->view->$key = $value;
    }

    if ($this->view->currency == "BTC") {
      $this->view->other_currency = "USD";
      $this->view->other_raised = $this->btcToDollars($this->view->raised);
      $this->view->other_goal = $this->btcToDollars($this->view->goal);
    } else {
      $this->view->goal = number_format($this->view->goal, 2);
      $this->view->other_currency = "BTC";
      $other_goal = Bitcoin\toBTC($this->view->currency, $this->view->goal);
      $this->view->other_goal = substr($other_goal, 0, 5);
      $other_raised = Bitcoin\toBTC($this->view->currency, $this->view->raised);
      $this->view->other_raised = substr($other_raised, 0, 5);
    }

    // XXX: What's this all about? Related to Flash widget implementation?
    if ($this->_getParam("flash", 0) == 1) {
      exit(json_encode($this->view->getVars()));
    }
  }

  private function btcToDollars($amountInBTC) {
    return $this->dollarValue(Bitcoin\fromBTC($amountInBTC, 'USD'));
  }

  private function dollarValue($amount) {
    $parts = explode('.', strval($amount));
    list($dollars, $cents) = count($parts) == 2 ? $parts : array($parts[0], '00');
    return $dollars . '.' . substr($cents, 0, 2);
  }

  public function indexAction() {}

  public function widget250x250Action() {}
  public function widget120x60Action() {}
  public function widget125x125Action() {}
  public function widget160x250Action() {}
  public function widget220x220Action() {}
  public function widget234x60Action() {}
}
