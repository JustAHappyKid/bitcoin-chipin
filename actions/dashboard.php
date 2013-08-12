<?php

// require_once 'chipin/users.php';
require_once 'chipin/widgets.php';

use \Chipin\Widgets;

class DashboardController extends \Chipin\WebFramework\Controller {

  function index() {
if (empty($this->user)) throw new Exception("No user?");
    $widgets = Widgets\getByOwner($this->user);

    /* XXX: This now done by cron-task.
    $tenMinutes = 60 * 10;
    $lastProgressUpdate = @ $_SESSION['lastProgressUpdate'];
    if (empty($lastProgressUpdate) || $lastProgressUpdate + $tenMinutes < time()) {
      foreach ($widgets as $w) Widgets\updateProgress($w);
      $widgets = Widgets\getByOwner($user);
      $_SESSION['lastProgressUpdate'] = time();
    }
    */

    $active = array(); $ended = array();
    foreach ($widgets as $w) {
      if ((time()-(60*60*24)) < strtotime($w['ending'])) {
        $active []= $w;
      } else {
        $ended []= $w;
      }
    }

    /*
    $this->view->assign('allWidgets', $widgets);
    $this->view->assign('activeWidgets', $active);
    $this->view->assign('endedWidgets', $ended);
    */

    return $this->render('dashboard.diet-php', null,
      array('allWidgets' => $widgets, 'activeWidgets' => $active, 'endedWidgets' => $ended));
  }
}
