<?php

namespace Chipin\Bitcoin;

require_once 'spare-parts/database.php';                # selectExactlyOne, insertOne, ...
require_once 'spare-parts/types.php';                   # isInteger
require_once 'chipin/blockchain-dot-info.php';          # BlockchainDotInfo
require_once 'chipin/currency.php';                     # Amount

use \Exception, \DateTime, \SpareParts\Database as DB, \Chipin\BlockchainDotInfo,
  \Chipin\Currency\Amount, \Chipin\Log;
use SpareParts\WebClient\NetworkError;

function getBalance($address, /*$currency = 'BTC',*/ \DateInterval $maxCacheAge = null) {
  if (empty($address) || trim($address) == '')
    throw new InvalidAddress("Empty string/value is not valid");
  $satoshis = null;
  $updatedAt = null;
  $needsUpdated = false;
  try {
    $row = DB\selectExactlyOne('bitcoin_addresses', 'address = ?', array($address));
    $satoshis = intval($row['satoshis']);
    if ($maxCacheAge) {
      $updatedAt = new DateTime($row['updated_at']);
      $expiresAt = $updatedAt->add($maxCacheAge);
      $now = new DateTime('now');
      if ($expiresAt->getTimestamp() < $now->getTimestamp())
        $needsUpdated = true;
    }
  } catch (DB\NoMatchingRecords $_) {
    $needsUpdated = true;
  }
  if ($needsUpdated) {
    # Since we don't want to have two or three (or more) processes all trying to query
    # Blockchain.info at the same time, we use a lock to assure only one attempt is made
    # to update the cache.
    $lockObtained = withLock($address,
      function() use($address) {
        $satoshis = BlockchainDotInfo\getBalanceInSatoshis($address);
        cacheBalance($address, $satoshis);
      });
    if (!$lockObtained) {
      if ($satoshis === null) {
        Log\error("Failed to obtain lock for and no cached balance exists for Bitcoin address " .
          "$address; defaulting to zero");
      }
      $oneHourAgo = new DateTime('1 hour ago');
      if ($updatedAt && $updatedAt->getTimestamp() < $oneHourAgo->getTimestamp()) {
        Log\error("Balance for Bitcoin address $address has not been updated for " .
          "more than one hour");
      }
    }
  }
//  $btcBalance = $satoshis / satoshisPerBTC();
//  $balanceWithPrecision = $currency == 'BTC' ? $btcBalance : fromBTC($btcBalance, $currency);
//  return $balanceWithPrecision;
  return new AmountOfBitcoin($satoshis);
}

function isValidAddress($address) {
  try {
    getBalance($address);
    return true;
  } catch (InvalidAddress $_) {
    return false;
  } catch (NetworkError $_) {
    # If we can't connect to Blockchain.info to get a full, proper validation, we'll
    # just do a simplistic check locally...
    return preg_match('@^1[0-9a-zA-Z]{30,40}$@', $address) == 1;
  }
}

function withLock($bitcoinAddr, \Closure $action) {
  $lockName = "bitcoin-address-$bitcoinAddr";
  $r = DB\queryAndFetchAll("SELECT GET_LOCK('$lockName', 0)");
  if ($r[0][0] == 1) {
    $action();
    DB\query("SELECT RELEASE_LOCK('$lockName')");
    return true;
  } else {
    return false;
  }
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

class AmountOfBitcoin extends Amount {
  public $numSatoshis, $numBTC;
  function __construct($satoshis) {
    $this->numSatoshis = $satoshis;
    $btcBalance = $satoshis / satoshisPerBTC();
    parent::__construct('BTC', $btcBalance);
    $this->numBTC = $this->numUnits;
  }
}

class InvalidAddress extends Exception {}
