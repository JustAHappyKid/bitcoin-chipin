<?php

require_once 'chipin/bitcoin.php';
require_once 'chipin/blockchain-dot-info.php';  # BlockchainDotInfo
require_once 'spare-parts/database.php';        # delete, insertOne

use \Chipin\Bitcoin, \Chipin\BlockchainDotInfo, \SpareParts\Database as DB;

function testGetBalanceUsesLocallyCachedValueWhenAppropriate() {
  $address = '1K7dyLY6arFRXBidQhrtnyqksqJZdj2F37';
  $actualBalance = BlockchainDotInfo\getBalanceInSatoshis($address);
  $cachedBalance = $actualBalance + 1000;
  DB\delete('bitcoin_addresses', 'address = ?', array($address));
  DB\insertOne('bitcoin_addresses',
    array('address' => $address,
          'satoshis' => $cachedBalance,
          'updated_at' => new DateTime('now')));
  $balance = Bitcoin\getBalance($address, null);
  assertEqual($cachedBalance, $balance->numSatoshis);
  assertEqual($cachedBalance / Bitcoin\satoshisPerBTC(), $balance->numBTC);
}

function testToBTCFunction() {
  $priceOfDollar = Bitcoin\toBTC('USD', 1);
  $f = floatval($priceOfDollar);
  assertTrue($f > 0);
  // assertEqual(floatval($priceOfDollar), floatval(strval($priceOfDollar)));
}

function testInvalidAddressesDoNotValidate() {
  assertFalse(Bitcoin\isValidAddress('1abc'));
  assertFalse(Bitcoin\isValidAddress('1abcdefghijklmnopqrstuvwxyzABC1123XYZ'));
  assertFalse(Bitcoin\isValidAddress('[]'));
}

function testValidatingAddressWhenBlockchainDotInfoCannotBeReached() {
  assertTrue(Bitcoin\isValidAddress('17yguvbpsfBE5Ec8MjFNnLbUmkYEEhWSm4'));
}
