<?php

namespace Chipin\Currency;

require_once 'chipin/currencies.php';

use \InvalidArgumentException, \Chipin\Currencies;

# XXX: Rename to SumOfMoney?
class Amount {

  /** @var Currency */
  //public $currency;

  public $currencyCode;

  /** @var int */
  public $numUnits;

  function __construct($c, $u) {
    if (!in_array($c, Currencies\codes()))
      throw new InvalidArgumentException("'$c' is not a recognized currency-code");
    if (!is_numeric($u))
      throw new InvalidArgumentException("Second parameter must be valid numeric value");
    $this->currencyCode = $c;
    $this->numUnits = doubleval($u);
  }

  function __toString() {
    return displayAmount($this->numUnits, $this->currencyCode);
  }
}

function displayAmount($amount, $currency) {
  $maxDecimalPlaces = 2;
  if ($currency == 'BTC') {
    $millibits = ($amount - floor($amount)) * 1000;
    $maxDecimalPlaces = strlen((string) ceil($millibits));
  } else if ($currency == 'JPY') {
    $maxDecimalPlaces = 0;
  } else if ($amount > 100) {
    # If it's a fiat currency (other than JPY), don't bother displaying cents/pence
    # when amount is over 100 dollars/pounds/euros/etc.
    $maxDecimalPlaces = 0;
  }
  return number_format($amount, $maxDecimalPlaces) . ' ' . $currency;
}

function trimZeros($num) {
  if (!is_numeric($num)) throw new InvalidArgumentException("Only numeric values accepted");
  $numStr = (string) $num;
  if (!contains($num, '.')) {
    return ltrim($numStr, '0');
  } else {
    return trim($num, '0.');
  }
}
