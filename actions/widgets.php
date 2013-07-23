<?php

require_once 'chipin/widgets.php';
require_once 'chipin/bitcoin.php';
require_once 'chipin/currency.php';
require_once 'spare-parts/types.php'; # at

use \SpareParts\Webapp\RequestContext, \Chipin\Widgets\Widget, \Chipin\Bitcoin,
  \Chipin\Currency;

class WidgetsController extends \Chipin\WebFramework\Controller {

  function byId(RequestContext $context) {
    $widget = Widget::getByID($context->takeNextPathComponent());
    require_once 'spare-parts/template/base.php';

    $vars = array();
    $vars['display'] = at($_GET, 'display', 'overview');
    $vars['title'] = $widget->title;
    $vars['about'] = $widget->about;
    $vars['currency'] = $widget->currency;
    $vars['goal'] = Currency\displayAmount($widget->goal, $widget->currency);
        //number_format($widget->goal, $widget->currency == "BTC" ? 4 : 2);
    $vars['raised'] = Currency\displayAmount($this->getAmountRaised($widget), $widget->currency);
    $vars['progress'] = $widget->raised / $widget->goal * 100;
    $this->setAltCurrencyValues($widget, $vars);
    $vars['bitcoinAddress'] = $widget->bitcoinAddress;

    # XXX: New template engine stuff...
    $tplDir = dirname(dirname(__FILE__)) . '/templates';
    $tplContext = new \SpareParts\Template\Context($tplDir, $vars);
    return \SpareParts\Template\renderFile('widgets/350x310.php', $tplContext);
  }

  function preview() {
     /* $action = $this->_getParam("action");
      $dimensions = withoutPrefix($action, 'widget');
      list($this->view->width, $this->view->height) = explode('x', $dimensions);
      $this->view->address = $this->_getParam("address", "");
      $this->view->color = $this->_getParam("color", "E0DCDC,707070");
      $this->view->title = stripslashes($this->_getParam("title", ""));
      $this->view->goal = $this->_getParam("goal", "0");
      $this->view->start = $this->_getParam("start", date("mm/dd/yy"));
      $this->view->ending = $this->_getParam("ending", date("mm/dd/yy"));
      $this->view->about = stripslashes($this->_getParam("about", ""));
      $this->view->currency = $this->_getParam("currency", "");
      if (!empty($this->view->address))
        $this->view->raised = Bitcoin\getBalance($this->view->address, $this->view->currency);
      if ($this->view->goal)
        $this->view->progress = $this->view->raised / $this->view->goal * 100;*/
  }

  private function setAltCurrencyValues(Widget $widget, Array & $vars) {
    if ($widget->currency == "BTC") {
      $vars['altCurrency'] = "USD";
      $vars['altGoal'] = $this->btcToDollars($widget->goal);
      $vars['altRaised'] = $this->btcToDollars($widget->raised);
    } else {
      $vars['altCurrency'] = "BTC";
      $goalInBTC = Bitcoin\toBTC($widget->currency, $widget->goal);
      $vars['altGoal'] = Currency\displayAmount($goalInBTC, 'BTC');
          //number_format($goalInBTC, 4); //substr($other_goal, 0, 5);
      $raisedInBTC = Bitcoin\toBTC($widget->currency, $widget->raised);
      $vars['altRaised'] = Currency\displayAmount($raisedInBTC, 'BTC');
          //number_format($raisedInBTC, 4); //substr($other_raised, 0, 5);
    }
  }

  private function btcToDollars($amountInBTC) {
    return $this->dollarValue(Bitcoin\fromBTC($amountInBTC, 'USD'));
  }

  private function dollarValue($amount) {
    $parts = explode('.', strval($amount));
    list($dollars, $cents) = count($parts) == 2 ? $parts : array($parts[0], '00');
    return $dollars . '.' . substr($cents, 0, 2);
  }

  private function getAmountRaised(Widget $widget) {
    try {
      return Bitcoin\getBalance($widget->bitcoinAddress, $widget->currency);
    } catch (\SpareParts\WebClient\NetworkError $_) {
      return $widget->raised;
    }
  }
}

return 'WidgetsController';