<?php

namespace Chipin\BlockchainDotInfo;

require_once 'spare-parts/web-client/http-simple.php';  # HttpSimple\get
require_once 'spare-parts/types.php';                   # isInteger
require_once 'spare-parts/string.php';                  # beginsWith
require_once 'chipin/bitcoin.php';                      # InvalidAddress

use \SpareParts\WebClient\HttpSimple as Http, \Chipin\Bitcoin\InvalidAddress;

function getBalanceInSatoshis($address) {
  $result = Http\get('http://blockchain.info/q/addressbalance/' . $address);
  if (isInteger($result)) {
    return intval($result);
  } else {
    $e = strtolower($result);
    if ($e == 'checksum does not validate' || beginsWith($e, 'illegal character') ||
        in_array($e, array('input to short', 'input too short')))
      throw new InvalidAddress("$address appears to be an invalid Bitcoin address");
    else
      throw new \Exception("Unexpected result received from blockchain.info when " .
        "attempting to get balance of address $address: $result");
  }
}
