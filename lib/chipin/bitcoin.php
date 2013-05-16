<?php

namespace Chipin\Bitcoin;

require_once 'spare-parts/web-client/http-simple.php';  # HttpSimple\get
require_once 'spare-parts/types.php';                   # isInteger

use \Exception, \MyPHPLibs\WebClient\HttpSimple as Http;

function getBalance($address, $currency = 'BTC') {
  $result = Http\get('http://blockchain.info/q/addressbalance/' . $address);
  if (!isInteger($result)) {
    throw new InvalidAddress("$address appears to be an invalid Bitcoin address");
  }
  $btcBalance = intval($result) / 100000000;
  /*
  $balanceWithPrecision = Http\get('http://blockchain.info/tobtc' .
                                   '?currency=' . $currency . '&value=' . $value);
  */
  $balanceWithPrecision = $currency == 'BTC' ? $btcBalance : fromBTC($btcBalance, $currency);
  $balance = substr($balanceWithPrecision, 0, 4);
  return $balance;
}

function toBTC($currency, $amount) {
  return (float) Http\get("http://blockchain.info/tobtc?currency=$currency&value=$amount");
}

function fromBTC($amountInBTC, $currency) {
  $btcPriceOfOneUnit = toBTC($currency, 1);
  return $amountInBTC / $btcPriceOfOneUnit;
}

class InvalidAddress extends Exception {}
