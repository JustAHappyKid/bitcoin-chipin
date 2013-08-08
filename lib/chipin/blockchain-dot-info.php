<?php

namespace Chipin\BlockchainDotInfo;

require_once 'spare-parts/web-client/http-simple.php';  # HttpSimple\get
require_once 'spare-parts/types.php';                   # isInteger
require_once 'chipin/bitcoin.php';                      # InvalidAddress

use \SpareParts\WebClient\HttpSimple as Http, \Chipin\Bitcoin\InvalidAddress;

function getBalanceInSatoshis($address) {
  $result = Http\get('http://blockchain.info/q/addressbalance/' . $address);
  if (!isInteger($result)) {
    throw new InvalidAddress("$address appears to be an invalid Bitcoin address");
  }
  return intval($result);
}
