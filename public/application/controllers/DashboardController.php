<?php

require_once 'chipin/widgets.php';
use \Chipin\Widgets;

class DashboardController extends Zend_Controller_Action {

  private $auth;

  public function init() {
    $this->auth = Zend_Auth::getInstance();
    if (!$this->auth->hasIdentity()) {
      $this->_redirect(PATH . 'signin/');
    } else {
      $this->view->assign('identity', $this->auth->getIdentity());
      $this->view->assign('user', $this->auth->getIdentity());
    }
  }

  public function indexAction() {
    /*
    $widgets = new Application_Model_Widgets();
    $widgets->setIdentity($this->auth->getIdentity()->id);
    $w = $widgets->getUserWidgets();
    */

    $user = $this->auth->getIdentity();
    $widgets = Widgets\getByOwner($user);

    $tenMinutes = 60 * 10;
    $lastProgressUpdate = @ $_SESSION['lastProgressUpdate'];
    if (empty($lastProgressUpdate) || $lastProgressUpdate + $tenMinutes < time()) {
      foreach ($widgets as $w) Widgets\updateProgress($w);
      $widgets = Widgets\getByOwner($user);
      $_SESSION['lastProgressUpdate'] = time();
    }

    $active = array(); $ended = array();
    foreach ($widgets as $w) {
      if ((time()-(60*60*24)) < strtotime($w['ending'])) {
        $active []= $w;
      } else {
        $ended []= $w;
      }
    }

    $this->view->assign('allWidgets', $widgets);
    $this->view->assign('activeWidgets', $active);
    $this->view->assign('endedWidgets', $ended);
  }
}
