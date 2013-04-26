<?php

namespace Chipin\Bitcoin;

require_once 'my-php-libs/web-client/http-simple.php';
use \MyPHPLibs\WebClient\HttpSimple as Http;

function getBalance($address, $currency = 'BTC') {
  $btcBalance = Http\get('http://blockchain.info/q/addressbalance/' . $address) / 100000000;
  /*
  $balanceWithPrecision = Http\get('http://blockchain.info/tobtc' .
                                   '?currency=' . $currency . '&value=' . $value);
  */
  $balanceWithPrecision = $currency == 'BTC' ? $btcBalance : toBTC($currency, $btcBalance);
  $balance = substr($balanceWithPrecision, 0, 4);
  return $balance;
}

function toBTC($currency, $amount) {
  return (float) Http\get('http://blockchain.info/tobtc?currency=USD&value=1');
}

function fromBTC($amountInBTC, $currency) {
  $btcPriceOfOneUnit = toBTC($currency, 1);
  return $amountInBTC / $btcPriceOfOneUnit;
}
