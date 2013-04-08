<?php

class DashboardController extends Zend_Controller_Action {

  private $auth;

  public function init() {
    $this->auth = Zend_Auth::getInstance();
    if (!$this->auth->hasIdentity()) {
      $this->_redirect(PATH . 'signin/');
    } else {
      $this->view->assign('identity', $this->auth->getIdentity());
    }
  }

  public function indexAction() {
    $widgets = new Application_Model_Widgets();
    $widgets->setIdentity($this->auth->getIdentity()->id);
    $w = $widgets->getUserWidgets();

    $k = 0;
    foreach ($w as $key) {
      if ((time()-(60*60*24)) < strtotime($key['ending'])) $k++;
    }

    $this->view->assign('widgets', $w);
    $this->view->assign('all', count($w));
    $this->view->assign('in_progress', $k);
    $this->view->assign('ended',  count($w) - $k);
  }
}
