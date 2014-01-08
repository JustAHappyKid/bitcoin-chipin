<?php

namespace Chipin\BlockchainDotInfo;

require_once 'spare-parts/web-client/http-simple.php';  # HttpSimple\get
require_once 'spare-parts/types.php';                   # isInteger
require_once 'spare-parts/string.php';                  # beginsWith
require_once 'chipin/bitcoin.php';                      # InvalidAddress

use \SpareParts\WebClient\HttpSimple as Http, \SpareParts\WebClient\HttpConnectionError,
  \Chipin\Bitcoin\InvalidAddress;

function getBalanceInSatoshis($address) {
  $result = Http\get('http://blockchain.info/q/addressbalance/' . $address);
  if (isInteger($result)) {
    return intval($result);
  } else {
    $e = strtolower($result);
    if ($e == 'checksum does not validate' || beginsWith($e, 'illegal character') ||
        in_array($e, array('input to short', 'input too short'))) {
      throw new InvalidAddress("$address appears to be an invalid Bitcoin address");
    } else if (contains($e, '<title>blockchain.info | 522: Connection timed out</title>') &&
               contains($e, 'CloudFlare')) {
      throw new HttpConnectionError("Connection to blockchain.info timed out " .
                                    "(reported by CloudFlare)");
    } else {
      throw new \Exception("Unexpected result received from blockchain.info when " .
        "attempting to get balance of address $address: $result");
    }
  }
}
