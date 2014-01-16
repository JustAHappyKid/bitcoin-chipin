<?php

require_once 'chipin/blockchain-dot-info.php';  # BlockchainDotInfo

use \Chipin\BlockchainDotInfo, \SpareParts\WebClient\NetworkError;

function testBalanceLookupElegantlyDetectsSiteMaintenanceAtBlockchainDotInfo() {
  # Special address for mocking a "Site Under Maintenance" response from Blockchain.info.
  $address = '1TestForDowntimeX1iTDhViXbrogKqzbt';
  try {
    BlockchainDotInfo\getBalanceInSatoshis($address);
  } catch (NetworkError $e) {
    assertTrue(contains(strtolower($e->getMessage()), 'maintenance'));
  }
}
