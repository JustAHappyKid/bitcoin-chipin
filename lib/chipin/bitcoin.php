<?php

namespace Chipin\Bitcoin;

require_once 'spare-parts/database.php';                # selectExactlyOne, insertOne, ...
require_once 'spare-parts/types.php';                   # isInteger
require_once 'chipin/blockchain-dot-info.php';          # BlockchainDotInfo

use \Exception, \DateTime, \SpareParts\Database as DB, \Chipin\BlockchainDotInfo;

function getBalance($address, $currency = 'BTC', \DateInterval $maxCacheAge = null) {
  $satoshis = null;
  try {
    $row = DB\selectExactlyOne('bitcoin_addresses', 'address = ?', array($address));
    if ($maxCacheAge) {
      $updatedAt = new DateTime($row['updated_at']);
      $expiresAt = $updatedAt->add($maxCacheAge);
      $now = new DateTime('now');
      if ($expiresAt->getTimestamp() > $now->getTimestamp())
        $satoshis = intval($row['satoshis']);
    } else {
      $satoshis = intval($row['satoshis']);
    }
  } catch (DB\NoMatchingRecords $_) { }
  if ($satoshis == null) {
    $satoshis = BlockchainDotInfo\getBalanceInSatoshis($address);
    cacheBalance($address, $satoshis);
  }
  $btcBalance = $satoshis / satoshisPerBTC();
  $balanceWithPrecision = $currency == 'BTC' ? $btcBalance : fromBTC($btcBalance, $currency);
  return $balanceWithPrecision;
}

/**
 * Store the balance of the given Bitcoin address to our local database for quick
 * access in the near future.
 */
function cacheBalance($address, $satoshis) {
  try {
    DB\transaction(function () use ($address, $satoshis) {
      DB\delete('bitcoin_addresses', 'address = ?', array($address));
      DB\insertOne('bitcoin_addresses', array('address' => $address, 'satoshis' => $satoshis,
        'updated_at' => new DateTime('now')));
    });
  } catch (\PDOException $e) {
    # If it's an exception about a deadlock, we'll ignore it -- it's probably due to
    # two processes trying to update the record at the same time.
    if (!contains(strtolower($e->getMessage()), "deadlock found")) throw $e;
  }
}

function toBTC($currency, $amount) {
  $perBTC = lastPriceForOneBTC($currency);
  return ($amount / $perBTC);
}

function fromBTC($amountInBTC, $currency) {
  $perBTC = lastPriceForOneBTC($currency);
  return ($amountInBTC * $perBTC);
}

function lastPriceForOneBTC($currency) {
  $rows = DB\simpleSelect('ticker_data', 'currency = ?', array($currency));
  $row = current($rows);
  if (empty($row))
    throw new DB\NoMatchingRecords("Could not find exchange-rate (in 'ticker_data' table) " .
                                   "for currency '$currency''");
  return doubleval($row['last_price']);
}

function satoshisPerBTC() { return 100000000; }

class InvalidAddress extends Exception {}
