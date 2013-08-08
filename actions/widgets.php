<?php

require_once 'chipin/widgets.php';
require_once 'chipin/bitcoin.php';
require_once 'chipin/currency.php';
require_once 'spare-parts/types.php';         # at
require_once 'spare-parts/template/base.php'; # Template\renderFile

use \Exception, \SpareParts\Webapp\RequestContext, \SpareParts\Template,
  \Chipin\Widgets\Widget, \Chipin\Bitcoin, \Chipin\Currency, \Chipin\Currency\Amount;

class WidgetsController extends \Chipin\WebFramework\Controller {

  function byId(RequestContext $context) {
    $widget = Widget::getByID($context->takeNextPathComponent());
    $vars = array();
    $vars['display'] = at($_GET, 'display', 'overview');
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
    return $this->renderDietTpl('widgets/350x310.diet-php', $vars);
  }

  function preview() {
    if (at($_GET, 'width', '350') != '350' || at($_GET, 'height', '310') != '310')
      throw new Exception("Only 350x310 widget supported at this time!");
    $vars = $_GET;
    $vars['display'] = 'overview';
    $vars['raised'] = '0';
    $vars['bitcoinAddress'] = $vars['address'];
    $vars['goal'] = new Amount($_GET['currency'], $_GET['goal']);
    $vars['raised'] = new Amount($_GET['currency'], $vars['goal']->numUnits / 4);
    $this->setAltCurrencyValues($vars['goal'], $vars['raised'], $vars);
    $vars['progress'] = 25;
    return $this->renderDietTpl('widgets/350x310.diet-php', $vars);
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

  /*
  private function setAltCurrencyValues(Widget $widget, Array & $vars) {
    if ($widget->currency == "BTC") {
      $vars['altCurrency'] = "USD";
      $vars['altGoal'] = $this->btcToDollars($widget->goal);
      $vars['altRaised'] = $this->btcToDollars($widget->raised);
    } else {
      $vars['altCurrency'] = "BTC";
      $goalInBTC = Bitcoin\toBTC($widget->currency, $widget->goal);
      $vars['altGoal'] = Currency\displayAmount($goalInBTC, 'BTC');
      $raisedInBTC = Bitcoin\toBTC($widget->currency, $widget->raised);
      $vars['altRaised'] = Currency\displayAmount($raisedInBTC, 'BTC');
    }
  }
  */

  private function btcToDollars($amountInBTC) {
    //return $this->dollarValue(Bitcoin\fromBTC($amountInBTC, 'USD'));
    Currency\displayAmount(Bitcoin\fromBTC($amountInBTC, 'USD'), 'USD');
  }

  /*
  private function dollarValue($amount) {
    $parts = explode('.', strval($amount));
    list($dollars, $cents) = count($parts) == 2 ? $parts : array($parts[0], '00');
    return $dollars . '.' . substr($cents, 0, 2);
  }
  */

  private function getAmountRaised(Widget $widget) {
    try {
      return Bitcoin\getBalance($widget->bitcoinAddress, $widget->currency);
    } catch (\SpareParts\WebClient\NetworkError $_) {
      return $widget->raised;
    }
  }
}

return 'WidgetsController';
