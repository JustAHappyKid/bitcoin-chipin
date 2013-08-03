<?php

require_once 'chipin/currency.php';
use \Chipin\Currency;

function testDisplayAmountFunction() {
  assertEqual('0.123 BTC',  displayAmount(0.123,  'BTC'));
  assertEqual('1.654 BTC',  displayAmount(1.654,  'BTC'));
  assertEqual('3.121 BTC',  displayAmount(3.1212, 'BTC'));
  assertEqual('13.25 USD',  displayAmount(13.25,  'USD'));
  assertEqual('13.25 USD',  displayAmount(13.253, 'USD'));
}

/* Thin wrapper... */
function displayAmount($a, $c) { return Currency\displayAmount($a, $c); }

function testTrimZeros() {
  $go = function($a) { return Currency\trimZeros($a); };
  assertEqual('2',      $go(2.00000));
  assertEqual('2',      $go('2.00000'));
  assertEqual('72.55',  $go(72.55000));
  assertEqual('200',    $go(200));
  assertEqual('7500',   $go('007500'));
}
