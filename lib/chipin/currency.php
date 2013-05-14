<?php

namespace Chipin\Currency;

function displayAmount($amount, $currency) {
  $maxDecimalPlaces = 2;
  if ($currency == 'BTC') {
    $millibits = ($amount - floor($amount)) * 1000;
    $maxDecimalPlaces = strlen((string) ceil($millibits));
  } else if ($currency == 'JPY') {
    $maxDecimalPlaces = 0;
  }
  return number_format($amount, $maxDecimalPlaces) . ' ' . $currency;
}
