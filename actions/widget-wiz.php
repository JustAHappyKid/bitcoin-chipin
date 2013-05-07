<?php

require_once 'chipin/widgets.php';
use \Chipin\Widgets\Widget;

class WidgetWizController extends \Chipin\WebFramework\Controller {

  function index() {
    return $this->redirect('/widget-wiz/step-one');
  }

  function create() {
    $this->clearWidgetInSession();
    return $this->redirect('/widget-wiz/step-one');
  }

  function stepOne() {
    /*
    $form = new WebForm('post');
    $form->addSection('password', array(
      newPasswordField('password1', 'Password')->
        required('Please provide a password'),
      newPasswordField('password2', 'Re-enter password')->
        required('Please confirm your password by entering it again.')->
        shouldMatch('password1', "The two entered passwords do not match.")));
    $form->addSubmitButton('Save Password');
    $this->templateVar('form', $form);
    */
    $widget = $this->getWidget();
    if ($this->isPostRequest()) {
      foreach (array('title', 'goal', 'currency', 'ending', 'bitcoinAddress') as $v) {
        $widget->$v = $_POST[$v];
      }
      $widget->title = $_POST['title'];
      $this->storeWidgetInSession($widget);
      return $this->redirect('/widget-wiz/step-two');
    } else {
      return $this->renderStep('step-one.php', 'StepOne', $widget);
    }
  }

  function stepTwo() {
    $widget = $this->getWidget();
    if ($this->isPostRequest()) {
      $widget->ownerID = $this->user->id;
      $widget->about = $_POST['about'];
      list($widget->width, $widget->height) = explode('x', $_POST['size']);
      $widget->color = $_POST['color'];
      $widget->save();
      $this->storeWidgetInSession($widget);
      return $this->redirect('/widget-wiz/step-three');
    } else {
      return $this->renderStep('step-two.php', 'StepTwo', $widget);
    }
  }

  function stepThree() {
    $widget = $this->getWidget();
    $this->clearWidgetInSession();
    return $this->renderStep('step-three.php', 'StepThree', $widget);
  }

  function previewCurrent() {
    $w = $this->getWidget();
    return $this->redirect($this->widgetPreviewURL($w));
  }

  private function widgetPreviewURL(Widget $w) {
    $width = $w->width ? $w->width : 250;
    $height = $w->height ? $w->height : 250;
    return "/client/widget{$width}x{$height}?" .
      "title={$w->title}&goal={$w->goal}&currency={$w->currency}&" .
      "ending={$w->ending}&addrss={$w->bitcoinAddress}&preview=true";
  }

  private function storeWidgetInSession(Widget $w) {
    $_SESSION['unsaved-widget'] = $w;
  }

  private function getWidget() {
    if (isset($_GET['w'])) {
      # Looks like we're editing a widget...
      $w = Widget::getByOwnerAndID($this->user, $_GET['w']);
      $this->storeWidgetInSession($w);
      return $w;
    } else {
      $w = at($_SESSION, 'unsaved-widget', null);
      return $w == null ? new Widget : $w;
    }
  }

  private function clearWidgetInSession() {
    unset($_SESSION['unsaved-widget']);
  }

  private function renderStep($tplFile, $className, Widget $widget, $error = null) {
    global $content;
    $content = '';
    if ($error) $content .= '<div class="alert alert-error">' . $error . '</div>';
    require_once $this->templatePath("widget-wiz/$tplFile");
    ob_start();
    $tplObj = new $className;
    $tplObj->widget = $widget;
    $tplObj->previewURL = $this->widgetPreviewURL($widget);
    $tplObj->content();
    $pgContent = ob_get_contents();
    ob_end_clean();
    return $pgContent;
  }

  private function templatePath($tpl) {
    return dirname(dirname(__FILE__)) . "/templates/$tpl"; }
}

return 'WidgetWizController';
