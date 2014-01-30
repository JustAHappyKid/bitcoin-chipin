<?php

require_once 'chipin/blockchain-dot-info.php';  # BlockchainDotInfo

use \Chipin\BlockchainDotInfo, \SpareParts\WebClient\NetworkError;

function testBalanceLookupElegantlyDetectsSiteMaintenanceAtBlockchainDotInfo() {
  # Special address for mocking a "Site Under Maintenance" response from Blockchain.info.
  $address = '1TestForDowntimeX1iTDhViXbrogKqzbt';
  try {
    BlockchainDotInfo\getBalanceInSatoshis($address);
    fail("Expected to get exception for lookup on address $address");
  } catch (NetworkError $e) {
    assertTrue(contains(strtolower($e->getMessage()), 'maintenance'));
  }
}

function testBalanceLookupElegantlyHandlesUnhelpfulResponsesFromBlockchainDotInfo() {
  $addresses = array('1GivesBlankContentiTDhViXbrogKqzbt', '1GivesLockWaitTimeoutTDhViXbrogKqzbt');
  foreach ($addresses as $a) {
    try {
      BlockchainDotInfo\getBalanceInSatoshis($a);
      fail("Expected to get exception for lookup on address $a");
    } catch (NetworkError $e) {

    }
  }
}
