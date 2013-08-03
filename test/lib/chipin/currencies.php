<?php

require_once 'chipin/currencies.php';
use \Chipin\Currencies;

function testCurrenciesRegistry() {
  assertTrue(in_array('USD', Currencies\codes()));
  assertTrue(in_array('EUR', Currencies\codes()));
  assertTrue(in_array('BTC', Currencies\codes()));
}
