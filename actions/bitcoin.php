<?php

require_once 'chipin/users.php';
require_once 'chipin/widgets.php';
require_once 'chipin/bitcoin.php';
require_once 'spare-parts/database.php';
require_once 'spare-parts/url.php';
require_once 'spare-parts/web-client/http-simple.php';

use \Chipin\Bitcoin,
  \SpareParts\URL, \SpareParts\Webapp\HttpResponse, \SpareParts\WebClient\HttpSimple,
  \SpareParts\Webapp\Forms, \SpareParts\Database as DB;

/**
 * This controller deals with all requests for obtaining info from the Bitcoin network
 * via JavaScript (i.e., AJAX requests), including address balances.
 */
class BitcoinController extends \Chipin\WebFramework\Controller {

  /**
   * Is the given Bitcoin address valid?
   */
  public function validAddress() {
    $address = $this->context->takeNextPathComponent();
    $resp = new HttpResponse;
    $resp->statusCode = 200;
    $resp->contentType = 'text/plain';
    $resp->content = $this->isValidAddress($address) ? 'true' : 'false';
    return $resp;
  }

  private function isValidAddress($address) {
    try {
      Bitcoin\getBalance($address);
      return true;
    } catch (Bitcoin\InvalidAddress $_) {
      return false;
    }
  }
}
