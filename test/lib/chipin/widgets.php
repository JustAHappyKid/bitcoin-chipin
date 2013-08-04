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
  $w->currency = 'BTC';
  $w->goal = 4;
  $w->save();
  $reloaded = Widget::getByID($w->id);
  assertTrue($reloaded->progress > 49 && $reloaded->progress < 51);

  # Case where widget uses fiat as base currency.
  setPriceForBTC(Currencies\USD(), 100);
  $w->bitcoinAddress = $addr;
  $w->currency = Currencies\USD();
  $w->goal = 600;
  $w->save();
  $reloaded = Widget::getByID($w->id);
  $expected = (200 / 600) * 100;
  assertTrue($reloaded->progress > floor($expected) && $reloaded->progress < ceil($expected));
}

function setPriceForBTC($currency, $price) {
  DB\delete('ticker_data', 'currency = ?', array($currency));
  DB\insertOne('ticker_data', array('currency' => $currency, 'last_price' => $price));
}
