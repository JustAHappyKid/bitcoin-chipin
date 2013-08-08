<?php

namespace Chipin\Bitcoin;

require_once 'spare-parts/database.php';
require_once 'spare-parts/web-client/http-simple.php';  # HttpSimple\get
require_once 'spare-parts/types.php';                   # isInteger
require_once 'chipin/blockchain-dot-info.php';          # BlockchainDotInfo

use \Exception, \DateTime, \SpareParts\Database as DB, \Chipin\BlockchainDotInfo;

function getBalance($address, $currency = 'BTC') {
  $satoshis = null;
  try {
    $row = DB\selectExactlyOne('bitcoin_addresses', 'address = ?', array($address));
    $satoshis = intval($row['satoshis']);
  } catch (DB\NoMatchingRecords $_) {
    $satoshis = BlockchainDotInfo\getBalanceInSatoshis($address);
    DB\insertOne('bitcoin_addresses', array('address' => $address, 'satoshis' => $satoshis,
                                            'updated_at' => new DateTime('now')));
  }
  $btcBalance = $satoshis / satoshisPerBTC();
  $balanceWithPrecision = $currency == 'BTC' ? $btcBalance : fromBTC($btcBalance, $currency);
  /*
  $balance = substr($balanceWithPrecision, 0, 4);
  return $balance;
  */
  return $balanceWithPrecision;
}

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

/*
function isValidAddress() {
  $result = Http\get('http://blockchain.info/q/addressbalance/' . $address);
  return isInteger($result);
}
*/

function satoshisPerBTC() { return 100000000; }

class InvalidAddress extends Exception {}
