<?php

namespace Chipin\Bitcoin;

require_once 'spare-parts/database.php';
require_once 'spare-parts/web-client/http-simple.php';  # HttpSimple\get
require_once 'spare-parts/types.php';                   # isInteger

use \Exception, \SpareParts\Database as DB; // \SpareParts\WebClient\HttpSimple as Http;

function getBalance($address, $currency = 'BTC') {
  $row = DB\selectExactlyOne('bitcoin_addresses', 'address = ?', array($address));
  $btcBalance = intval($row['satoshis']) / 100000000;
  $balanceWithPrecision = $currency == 'BTC' ? $btcBalance : fromBTC($btcBalance, $currency);
  /*
  $balance = substr($balanceWithPrecision, 0, 4);
  return $balance;
  */
  return $balanceWithPrecision;
}

/*
function getBalanceFromBlockchainDotInfo($address, $currency = 'BTC') {
  $result = Http\get('http://blockchain.info/q/addressbalance/' . $address);
  if (!isInteger($result)) {
    throw new InvalidAddress("$address appears to be an invalid Bitcoin address");
  }
  $btcBalance = intval($result) / 100000000;
  $balanceWithPrecision = $currency == 'BTC' ? $btcBalance : fromBTC($btcBalance, $currency);
  $balance = substr($balanceWithPrecision, 0, 4);
  return $balance;
}
*/

function toBTC($currency, $amount) {
  $perBTC = lastPriceForOneBTC($currency);
  return ($amount / $perBTC);
}

/*
function toBTC($currency, $amount) {
  return (float) Http\get("http://blockchain.info/tobtc?currency=$currency&value=$amount");
}
*/

function fromBTC($amountInBTC, $currency) {
  $perBTC = lastPriceForOneBTC($currency);
  return ($amountInBTC * $perBTC);
}

function lastPriceForOneBTC($currency) {
  $row = DB\selectExactlyOne('ticker_data', 'currency = ?', array($currency));
  return doubleval($row['last_price']);
}

/*
function fromBTC($amountInBTC, $currency) {
  $btcPriceOfOneUnit = toBTC($currency, 1);
  return $amountInBTC / $btcPriceOfOneUnit;
}
*/

//class InvalidAddress extends Exception {}

function isValidAddress() {
  $result = Http\get('http://blockchain.info/q/addressbalance/' . $address);
  return isInteger($result);
}
