<?php

namespace Chipin\Currency;

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
