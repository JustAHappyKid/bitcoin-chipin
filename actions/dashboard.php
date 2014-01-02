<?php

require_once 'chipin/widgets.php';

use \Chipin\Widgets, \Chipin\Widgets\Widget, \SpareParts\Webapp\RequestContext;

class DashboardController extends \Chipin\WebFramework\Controller {

  function index() {
    $widgets = Widget::getManyByOwner($this->user);
    $active = array(); $ended = array();
    foreach ($widgets as $w) {
      if ($w->hasEnded()) {
        $ended []= $w;
      } else {
        $active []= $w;
      }
    }
    return $this->render('dashboard.diet-php',
      array('allWidgets' => $widgets, 'activeWidgets' => $active, 'endedWidgets' => $ended,
            'successMessage' => $this->takeFromSession('successMessage')));
  }

  public function endWidget(RequestContext $context) {
    $w = Widget::getByOwnerAndID($this->user, $context->takeNextPathComponent());
    if ($this->isPostRequest()) {
      Widgets\endWidget($w);
      return $this->redirect('/dashboard/');
    } else {
      return $this->render('end-widget.diet-php', array('widget' => $w));
    }
  }
}
