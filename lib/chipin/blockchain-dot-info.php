<?php

namespace Chipin\BlockchainDotInfo;

require_once 'spare-parts/web-client/http-simple.php';  # HttpSimple\get
require_once 'spare-parts/types.php';                   # isInteger
require_once 'spare-parts/string.php';                  # beginsWith
require_once 'chipin/bitcoin.php';                      # InvalidAddress

use \SpareParts\WebClient\HttpClient, \SpareParts\WebClient\NetworkError,
  \Chipin\Bitcoin\InvalidAddress;

function getBalanceInSatoshis($address) {
//  $content = Http\get('http://blockchain.info/q/addressbalance/' . $address);
  $response = (new HttpClient())->get('http://blockchain.info/q/addressbalance/' . $address);
  if ($response->statusCode == 200 && isInteger($response->content)) {
    return intval($response->content);
  } else {
    $e = strtolower($response->content);
    preg_match('@<title>(.*)</title>@', $response->content, $matches);
    $title = at($matches, 1);
    if ($e == 'checksum does not validate' || beginsWith($e, 'illegal character') ||
        in_array($e, array('input to short', 'input too short'))) {
      throw new InvalidAddress("$address appears to be an invalid Bitcoin address");
    } else if (contains($e, 'cloudflare') && !empty($title)) {
      throw new NetworkError("CloudFlare-reported problem at blockchain.info: $title");
    } else if (!empty($title)) {
      throw new NetworkError("Unknown error when attempting to check address-balance " .
        "for ($address) via blockchain.info: $title");
    } else {
      throw new \Exception("Unexpected result received from blockchain.info when " .
        "attempting to get balance of address $address: {$response->content}");
    }
  }
}
