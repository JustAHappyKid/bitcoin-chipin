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

    $k = 0;
    foreach ($widgets as $key) {
      if ((time()-(60*60*24)) < strtotime($key['ending'])) $k++;
    }

    $this->view->assign('widgets', $widgets);
    $this->view->assign('all', count($widgets));
    $this->view->assign('in_progress', $k);
    $this->view->assign('ended',  count($widgets) - $k);
  }
}
