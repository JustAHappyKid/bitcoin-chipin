<?php

require_once 'chipin/widgets.php';
require_once 'chipin/bitcoin.php';
require_once 'spare-parts/url.php';
require_once 'spare-parts/web-client/http-simple.php';

use \Chipin\Widgets, \Chipin\Widgets\Widget, \Chipin\Bitcoin,
  \SpareParts\URL, \SpareParts\Webapp\HttpResponse, \SpareParts\WebClient\HttpSimple;

class WidgetWizController extends \Chipin\WebFramework\Controller {

  function index() {
    return $this->redirect('/widget-wiz/step-one');
  }

  function create() {
    $this->clearWidgetInSession();
    return $this->redirect('/widget-wiz/step-one');
  }

  function stepOne() {
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
      $widget->countryCode = $this->getCountryCodeForIP();
      $widget->save();
      Widgets\updateProgressForObj($widget);
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
    foreach (array('about', 'width', 'height', 'color') as $var) {
      if (isset($_GET[$var])) $w->$var = $_GET[$var];
    }
    return $this->redirect($this->widgetPreviewURL($w));
  }

  /**
   * This action is used by JavaScript to validate given Bitcoin address.
   */
  public function validBtcAddr() {
    $address = $this->context->takeNextPathComponent();
    $valid = null;
    try {
      $balance = Bitcoin\getBalance($address);
      //echo ($balance == 0 ? 'true' : 'false');
      $valid = true;
    } catch (Bitcoin\InvalidAddress $_) {
      $valid = false;
    }
    $resp = new HttpResponse;
    $resp->statusCode = 200;
    $resp->contentType = 'text/plain';
    $resp->content = $valid ? 'true' : 'false';
    return $resp;
  }

  public function end() {
    if ($this->isPostRequest()) {
      $w = Widget::getByOwnerAndID($this->user, $_POST['id']);
      Widgets\endWidget($w);
    }
    return $this->redirect('/dashboard/');
  }

  private function widgetPreviewURL(Widget $w) {
    $width = $w->width ? $w->width : 250;
    $height = $w->height ? $w->height : 250;
    $vars = array(
      'title' => $w->title, 'goal' => $w->goal, 'currency' => $w->currency,
      'about' => $w->about, 'color' => $w->color,
      'ending' => $w->ending, 'address' => $w->bitcoinAddress, 'preview' => 'true');
    return "/client/widget{$width}x{$height}" . URL\makeQueryString($vars);
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

  private function renderStep($tplFile, $className, Widget $widget /*, $error = null*/) {
    /*
    global $content;
    $content = '';
    if ($error) $content .= '<div class="alert alert-error">' . $error . '</div>';
    */
    return $this->render("widget-wiz/$tplFile", $className,
      array('widget' => $widget, 'previewURL' => $this->widgetPreviewURL($widget)));
  }

  private function getCountryCodeForIP() {
    $ipInfoJSON = HttpSimple\get('http://api.easyjquery.com/ips/?ip='. $_SERVER['REMOTE_ADDR']);
    // $ipInfoJSON = file_get_contents('http://api.easyjquery.com/ips/?ip='. $_SERVER['REMOTE_ADDR']);
    $location = json_decode($ipInfoJSON, true);
    return $location['Country'];
  }
}

return 'WidgetWizController';
