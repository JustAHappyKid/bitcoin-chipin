<?php

require_once 'chipin/widgets.php';

use \Chipin\Widgets, \Chipin\Widgets\Widget, \SpareParts\Webapp\RequestContext;

class DashboardController extends \Chipin\WebFramework\Controller {

  function index() {
    // $widgets = Widgets\getByOwner($this->user);

    /* XXX: This now done by cron-task.
    $tenMinutes = 60 * 10;
    $lastProgressUpdate = @ $_SESSION['lastProgressUpdate'];
    if (empty($lastProgressUpdate) || $lastProgressUpdate + $tenMinutes < time()) {
      foreach ($widgets as $w) Widgets\updateProgress($w);
      $widgets = Widgets\getByOwner($user);
      $_SESSION['lastProgressUpdate'] = time();
    }
    */

    $widgets = Widget::getManyByOwner($this->user);
    $active = array(); $ended = array();
    foreach ($widgets as $w) {
      if ($w->hasEnded()) {
        $ended []= $w;
      } else {
        $active []= $w;
      }
    }

    return $this->render('dashboard.diet-php', null,
      array('allWidgets' => $widgets, 'activeWidgets' => $active, 'endedWidgets' => $ended));
  }

  public function endWidget(RequestContext $context) {
    $w = Widget::getByOwnerAndID($this->user, $context->takeNextPathComponent());
    if ($this->isPostRequest()) {
      Widgets\endWidget($w);
      return $this->redirect('/dashboard/');
    } else {
      return $this->render('end-widget.diet-php', null, array('widget' => $w));
    }
  }
}
