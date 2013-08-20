<?php

require_once 'chipin/widgets.php';
require_once 'chipin/users.php';
require_once 'chipin/bitcoin.php';
require_once 'chipin/currency.php';
require_once 'spare-parts/types.php';         # at
require_once 'spare-parts/template/base.php'; # Template\renderFile

use \SpareParts\Webapp\RequestContext, \SpareParts\Template,
  \Chipin\Widgets, \Chipin\Widgets\Widget, \Chipin\User, \Chipin\Bitcoin, \Chipin\Currency,
  \Chipin\Currency\Amount;

class WidgetsController extends \Chipin\WebFramework\Controller {

  function u(RequestContext $context) {
    $username = $context->takeNextPathComponent();
    $uriID = $context->takeNextPathComponent();
    $widget = Widget::getByURI(User::loadFromUsername($username), $uriID);
    $vars = array();
    $vars['display'] = at($_GET, 'display', 'overview');
    $vars['width'] = $widget->width;
    $vars['height'] = $widget->height;
    $vars['color'] = $widget->color;
    $vars['title'] = $widget->title;
    $vars['about'] = $widget->about;
    $vars['currency'] = $widget->currency;
    $vars['goal'] = $widget->goalAmnt;
    $vars['raised'] = $widget->raisedAmnt;
    $vars['progress'] = $widget->progress;
    $this->setAltCurrencyValues($widget->goalAmnt, $widget->raisedAmnt, $vars);
    // $this->setAltCurrencyValues($widget, $vars);
    $vars['bitcoinAddress'] = $widget->bitcoinAddress;
//    return $this->renderDietTpl('widgets/350x310.diet-php', $vars);
    return $this->renderWidget($vars);
  }

  function byId(RequestContext $context) {
    $widget = Widget::getByID($context->takeNextPathComponent());
    return $this->redirect('/widgets/u/' . $widget->getOwner()->username . '/' .
      ($widget->uriID ? $widget->uriID : $widget->id));
  }

  function preview() {
    $vars = $_GET;
    $vars['display'] = 'overview';
    $vars['raised'] = '0';
    $vars['bitcoinAddress'] = $vars['address'];
    $vars['goal'] = new Amount($_GET['currency'], $_GET['goal']);
    $vars['raised'] = new Amount($_GET['currency'], $vars['goal']->numUnits / 4);
    $this->setAltCurrencyValues($vars['goal'], $vars['raised'], $vars);
    $vars['progress'] = 25;
    $ds = Widgets\allowedSizes();
    $vars['width'] = at($_GET, 'width', $ds[0]->width);
    $vars['height'] = at($_GET, 'height', $ds[0]->height);
    return $this->renderWidget($vars);
  }

  private function renderWidget(Array $vars) {
    $w = $vars['width']; $h = $vars['height'];
    if (!Widgets\validDimensions($w, $h)) {
      $ds = Widgets\allowedSizes();
      $w = $ds[0]->width; $h = $ds[0]->height;
      # TODO: Log warning that a request for invalid widget dimensions was made.
    }
    return $this->renderDietTpl("widgets/{$w}x{$h}.diet-php", $vars);
  }

  private function renderDietTpl($tpl, Array $vars) {
    $tplDir = dirname(dirname(__FILE__)) . '/templates';
    $tplContext = new Template\Context($tplDir, $vars);
    return Template\renderFile($tpl, $tplContext);
  }

  private function setAltCurrencyValues(Amount $goal, Amount $raised, Array & $vars) {
    if ($goal->currencyCode == "BTC") {
      $vars['altCurrency'] = "USD";
      $vars['altGoal'] = $this->btcToDollars($goal->numUnits);
      $vars['altRaised'] = $this->btcToDollars($raised->numUnits);
    } else {
      $vars['altCurrency'] = "BTC";
      $goalInBTC = Bitcoin\toBTC($goal->currencyCode, $goal->numUnits);
      $vars['altGoal'] = Currency\displayAmount($goalInBTC, 'BTC');
      $raisedInBTC = Bitcoin\toBTC($raised->currencyCode, $raised->numUnits);
      $vars['altRaised'] = Currency\displayAmount($raisedInBTC, 'BTC');
    }
  }

  private function btcToDollars($amountInBTC) {
    Currency\displayAmount(Bitcoin\fromBTC($amountInBTC, 'USD'), 'USD');
  }
}

return 'WidgetsController';
