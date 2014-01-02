<?php

require_once 'chipin/widgets.php';
require_once 'chipin/bitcoin.php';
require_once 'chipin/currencies.php';
require_once 'spare-parts/database.php';

use \Chipin\Widgets\Widget, \Chipin\Bitcoin, \Chipin\Currencies, \SpareParts\Database as DB;

function testProgressProperlyCalculated() {
  $w = getWidget();
  $addr = getBitcoinAddr($btcBalance = 2);

  # Case where widget uses BTC as base currency.
  $w->bitcoinAddress = $addr;
  $w->setGoal(4, Currencies\BTC());
  $w->save();
  $reloaded = Widget::getByID($w->id);
  assertTrue($reloaded->progressPercent > 49 && $reloaded->progressPercent < 51);

  # Case where widget uses fiat as base currency.
  setPriceForBTC(Currencies\USD(), 100);
  $w->bitcoinAddress = $addr;
  $w->setGoal(600, Currencies\USD());
  $w->save();
  $reloaded = Widget::getByID($w->id);
  $expected = (200 / 600) * 100;
  assertTrue($reloaded->progressPercent > floor($expected) &&
             $reloaded->progressPercent < ceil($expected));
}

function testDeterminingWhetherWidgetHasEndedOrNot() {
  $w = getWidget();
  $w->ending = new DateTime('-1 day');
  assertTrue($w->hasEnded());
  $w->ending = new DateTime((new DateTime('now'))->format('Y-m-d'));
  assertFalse($w->hasEnded());
  $w->ending = new DateTime('+1 day');
  assertFalse($w->hasEnded());
}

function setPriceForBTC($currency, $price) {
  DB\delete('ticker_data', 'currency = ?', array($currency));
  DB\insertOne('ticker_data', array('currency' => $currency, 'last_price' => $price));
}
