<?php

require_once 'chipin/users.php';
require_once 'chipin/widgets.php';
require_once 'chipin/bitcoin.php';
require_once 'chipin/short-url.php';
require_once 'spare-parts/database.php';
require_once 'spare-parts/url.php';
require_once 'spare-parts/web-client/http-simple.php';

use \Chipin\Widgets, \Chipin\Widgets\Widget, \Chipin\Bitcoin, \Chipin\Log, \Chipin\User,
  \Chipin\ShortURL, \SpareParts\URL, \SpareParts\WebClient\HttpSimple, \SpareParts\Webapp\Forms,
  \SpareParts\Database as DB;

class WidgetWizController extends \Chipin\WebFramework\Controller {

  function init() {
    if ($this->isPostRequest()) {
      foreach ($_POST as $n => $v) $_POST[$n] = strip_tags($v);
    }
  }

  function index() {
    return $this->redirect('/widget-wiz/step-one');
  }

  function create() {
    $this->clearWidgetInSession();
    return $this->redirect('/widget-wiz/step-one');
  }

  function stepOne() {
    $widget = $this->getWidget();
    $f = new Forms\Form('post');
    $f->addSection('step-one', array(
      Forms\newBasicTextField('title', 'Title'),
      Forms\newDollarAmountField('goal', 'Goal')->
        minAmount(0.001)->
        maxAmount(self::maxGoal, "Sorry, at the present time " . self::maxGoal .
                                 " is the maximum allowed goal."),
      Forms\newDateField('ending', 'End Date')->nameForValidation("Ending Date field"),
      Forms\newBasicTextField('bitcoinAddress', 'Bitcoin Address')
    ));
    if ($this->isPostRequestAndFormIsValid($f)) {
      foreach (array('title', 'ending', 'bitcoinAddress') as $v) {
        $widget->$v = $_POST[$v];
      }
      $widget->setGoal($_POST['goal'], $_POST['currency']);

      # TODO: Test for already-existing 'uriID' with same value and owner!
      # TODO: Should we add support for redirecting from old 'uriID' handles (in the
      #       case where a widget's title has been changed)?
      $widget->uriID = URL\titleToUrlComponent($widget->title);

      if (at($_POST, 'save-and-return')) {
        $widget->save();
        return $this->redirect('/dashboard/');
      } else {
        $this->storeWidgetInSession($widget);
        return $this->redirect('/widget-wiz/step-two');
      }
    } else {
      return $this->renderStep('step-one.php', $widget, $f);
    }
  }

  function stepTwo() {
    $widget = $this->requireWidget();
    if ($this->isPostRequest()) {
      $user = $this->getActiveUser();
      if (empty($user)) {
        $uid = DB\insertOne('users', array('created_at' => new DateTime('now')), $returnId = true);
        $user = User::loadFromID($uid);
        $this->setActiveUser($user);
      }
      $widget->ownerID = $user->id;
      $widget->about = $_POST['about'];
      list($widget->width, $widget->height) = explode('x', $_POST['size']);
      $widget->color = $_POST['color'];
      $widget->countryCode = $this->getCountryCodeForIP();
      $widget->save();
      //$widget->updateProgress();
      $this->storeWidgetInSession($widget);
      return $this->redirect("/widget-wiz/step-three?w={$widget->id}");
    } else {
      return $this->renderStep('step-two.php', $widget);
    }
  }

  function stepThree() {
    $widget = $this->requireWidget();
    $this->clearWidgetInSession();
    return $this->renderStep('step-three.php', $widget);
  }

  function previewCurrent() {
    $w = $this->getWidget();
    foreach (array('about', 'width', 'height', 'color') as $var) {
      if (isset($_GET[$var])) $w->$var = $_GET[$var];
    }
    return $this->redirect($this->widgetPreviewURL($w));
  }

  private function widgetPreviewURL(Widget $w) {
    $width = $w->width ? $w->width : 350;
    $height = $w->height ? $w->height : 310;
    $vars = array('width' => $width, 'height' => $height, 'title' => $w->title,
      'goal' => empty($w->goalAmnt) ? 0 : $w->goalAmnt->numUnits, 'currency' => $w->currency,
      'about' => $w->about, 'color' => $w->color,
      'ending' => $w->endingDateAsString(), 'address' => $w->bitcoinAddress);
    return "/widgets/preview" . URL\makeQueryString($vars);
  }

  private function storeWidgetInSession(Widget $w) {
    $_SESSION['unsaved-widget'] = $w;
  }

  private function getWidget() {
    if (isset($_GET['w'])) {
      # Looks like we're editing a widget...
      $user = $this->getActiveUser();
      if (empty($user)) {
        $_SESSION['authenticationRequired'] = true;
        return $this->redirect("/account/signin");
      } else {
        $w = Widget::getByOwnerAndID($user, $_GET['w']);
        $this->storeWidgetInSession($w);
        return $w;
      }
    } else {
      $w = at($_SESSION, 'unsaved-widget', null);
      if (isset($w) && isset($w->ownerID) && empty($this->user)) {
        $this->clearWidgetInSession();
        $w = null;
      }
      if (empty($w)) $w = new Widget;
      $w->color = Widgets\defaultColor();
      $w->width = Widgets\defaultSize()->width;
      $w->height = Widgets\defaultSize()->height;
      return $w;
    }
  }

  private function requireWidget() {
    if (empty($_GET['w']) && empty($_SESSION['unsaved-widget'])) {
      throw new \SpareParts\Webapp\DoRedirect('/widget-wiz/step-one');
    } else {
      return $this->getWidget();
    }
  }

  private function clearWidgetInSession() {
    unset($_SESSION['unsaved-widget']);
  }

  private function renderStep($tplFile, Widget $widget, Forms\Form $form = null) {
    return $this->render("widget-wiz/$tplFile",
      array('widget' => $widget, 'form' => $form, 'user' => $this->user,
            'previewURL' => $this->widgetPreviewURL($widget),
            'shortURL' => ShortURL\urlForWidget($widget)));
  }

  private function getCountryCodeForIP() {
    try {
      $ip = $_SERVER['REMOTE_ADDR'];
      $rawJSON = HttpSimple\get('https://freegeoip.net/json/' . $ip);
      $info = json_decode($rawJSON);
      if (!is_object($info) || !isset($info->country_code)) {
        Log\warn("Web-service at FreeGeoIP.net did not return expected value for IP address $ip");
        return null;
      } else {
        return $info->country_code;
      }
    } catch (\SpareParts\WebClient\NetworkError $e) {
      Log\error("Network error occurred when attempting to lookup IP adress info: " .
        $e->getMessage());
      return null;
    }
  }

  const maxGoal = 95000;
}

return 'WidgetWizController';
