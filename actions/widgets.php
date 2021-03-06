<?php

require_once 'chipin/widgets.php';
require_once 'chipin/users.php';
require_once 'chipin/bitcoin.php';
require_once 'chipin/currency.php';
require_once 'chipin/webapp/routes.php';
require_once 'spare-parts/time/intervals.php';  # readInterval
require_once 'spare-parts/types.php';           # at
require_once 'spare-parts/template/base.php';   # Template\renderFile

use \SpareParts\Webapp\RequestContext, \SpareParts\Template, \SpareParts\Time,
  \Chipin\Widgets, \Chipin\Widgets\Widget, \Chipin\User, \Chipin\Bitcoin, \Chipin\Currency,
  \Chipin\Currency\Amount, \Chipin\WebFramework\Routes, \Chipin\Log, \Chipin\BlockchainDotInfo;

class WidgetsController extends \Chipin\WebFramework\Controller {

  function u(RequestContext $context) {
    $username = $context->takeNextPathComponent();
    $uriID = $context->takeNextPathComponentOrNull();
    try {
      if (empty($uriID)) {
        $user = User::loadFromUsername($username);
        return $this->render('widgets-for-user.diet-php',
          array('user' => $user, 'widgets' => Widget::getManyByOwner($user)));
      } else {
        $widget = Widget::getByURI(User::loadFromUsername($username), $uriID);
        return $this->renderWidgetObj($widget);
      }
    } catch (\Chipin\NoSuchUser $_) {
      return $this->pageNotFound("No such user");
    } catch (\Chipin\Widgets\NoSuchWidget $_) {
      return $this->pageNotFound("No such widget");
    }
  }

  function byId(RequestContext $context) {
    try {
      $widget = Widget::getByID($context->takeNextPathComponent());
      $owner = $widget->getOwner();
      if (empty($owner->username)) # If user has no username, just show the widget...
        return $this->renderWidgetObj($widget);
      else # ...otherwise, redirect to more "user-friendly" URL...
        return $this->redirect('/widgets/u/' . $owner->username . '/' .
          ($widget->uriID ? $widget->uriID : $widget->id));

    } catch (Widgets\NoSuchWidget $_) {
      return $this->pageNotFound("No widget found having the specified ID");
    }
  }

  function about(RequestContext $context) {
    return $this->render('widgets/about.diet-php',
      array('widget' => $this->takeWidgetFromURI($context)));
  }

  function progress(RequestContext $c) {
    try {
      $w = $this->takeWidgetFromURI($c);
      $fiveSecs = Time\readInterval('5 seconds');
      $w->updateBalance(Bitcoin\getBalance($w->bitcoinAddress, $maxCacheAge = $fiveSecs));
      return $this->textResponse($w->progressPercent);
    } catch (\SpareParts\WebClient\NetworkError $e) {
      Log\notice("Caught " . get_class($e) . " when attempting to check Bitcoin-address balance: " .
        $e->getMessage());
      return $this->textResponse(
        "Network error occurred when trying to check address balance", 503);
    }
  }

  function amountRaised(RequestContext $c) {
    $w = $this->takeWidgetFromURI($c);
    $currency = at($_GET, 'currency', 'BTC');
    $a = $currency == 'BTC' ? $w->raisedBTC : Bitcoin\fromBTC($w->raisedBTC, $currency);
    $amount = new Amount($currency, $a);
    return $this->textResponse(strval($amount));
  }

  function preview() {
    $vars = $_GET;
    $vars['previewOnly'] = true;
    $vars['display'] = 'overview';
    $vars['bitcoinAddress'] = $vars['address'];
    $vars['goal'] = new Amount($_GET['currency'], (float) $_GET['goal']);
    $vars['raised'] = new Amount($_GET['currency'],
                                 (float) at($_GET, 'raised', $vars['goal']->numUnits / 4));
    $this->setAltCurrencyValues($vars['goal'], $vars['raised'], $vars);
    $vars['progressPercent'] = $vars['raised']->numUnits / $vars['goal']->numUnits * 100;
    $ds = Widgets\allowedSizes();
    $vars['width'] = at($_GET, 'width', $ds[0]->width);
    $vars['height'] = at($_GET, 'height', $ds[0]->height);
    $vars['color'] = at($_GET, 'color', Widgets\defaultColor());
    $vars['widgetID'] = null;
    return $this->renderWidgetArr($vars);
  }

  private function renderWidgetObj(Widget $widget) {
    $vars = array();
    $vars['previewOnly'] = false;
    $vars['display'] = at($_GET, 'display', 'overview');
    $vars['widgetID'] = $widget->id;
    $vars['width'] = $widget->width;
    $vars['height'] = $widget->height;
    $vars['color'] = $widget->color;
    $vars['title'] = $widget->title;
    $vars['about'] = $widget->about;
    $vars['currency'] = $widget->currency;
    $vars['goal'] = $widget->goalAmnt;
    $vars['raised'] = $widget->raisedAmnt;
    $vars['lastBalance'] = $widget->raisedSatoshis;
    $vars['progressPercent'] = $widget->progressPercent;
    $this->setAltCurrencyValues($widget->goalAmnt, $widget->raisedAmnt, $vars);
    $vars['bitcoinAddress'] = $widget->bitcoinAddress;
    $vars['checkProgressURI'] = Routes\checkWidgetProgress($widget);
    $vars['checkBalanceURI'] = BlockchainDotInfo\balanceLookupURL($widget->bitcoinAddress);
    $vars['amountRaisedURI'] = Routes\amountRaised($widget);
    return $this->renderWidgetArr($vars);
  }

  private function renderWidgetArr(Array $vars) {
    $w = $vars['width']; $h = $vars['height'];
    if (!Widgets\validDimensions($w, $h)) {
      $ds = Widgets\allowedSizes();
      $w = $ds[0]->width; $h = $ds[0]->height;
      # TODO: Log warning that a request for invalid widget dimensions was made.
    }
    return $this->renderDietTpl("widgets/{$w}x{$h}.diet-php", $vars);
  }

  private function takeWidgetFromURI(RequestContext $context) {
    try {
      return Widget::getByID($context->takeNextPathComponent());
    } catch (\Chipin\Widgets\NoSuchWidget $_) {
      return $this->pageNotFound("No such widget");
    }
  }

  private function renderDietTpl($tpl, Array $vars) {
    $tplDir = dirname(dirname(__FILE__)) . '/templates';
    $tplContext = new Template\Context($tplDir, $vars);
    return Template\renderFile($tpl, $tplContext);
  }

  private function setAltCurrencyValues(Amount $goal, Amount $raised, Array & $vars) {
    if ($goal->currencyCode == "BTC") {
      $vars['altGoal'] = $this->btcToDollars($goal->numUnits);
      $vars['altRaised'] = $this->btcToDollars($raised->numUnits);
    } else {
      $goalInBTC = Bitcoin\toBTC($goal->currencyCode, $goal->numUnits);
      $vars['altGoal'] = new Amount('BTC', $goalInBTC); //Currency\displayAmount($goalInBTC, 'BTC');
      $raisedInBTC = Bitcoin\toBTC($raised->currencyCode, $raised->numUnits);
      $vars['altRaised'] = new Amount('BTC', $raisedInBTC); //Currency\displayAmount($raisedInBTC, 'BTC');
    }
  }

  private function btcToDollars($amountInBTC) {
//    return Currency\displayAmount(Bitcoin\fromBTC($amountInBTC, 'USD'), 'USD');
    return new Amount('USD', Bitcoin\fromBTC($amountInBTC, 'USD'));
  }
}

return 'WidgetsController';
