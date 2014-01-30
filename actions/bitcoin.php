<?php

require_once 'chipin/users.php';
require_once 'chipin/widgets.php';
require_once 'chipin/bitcoin.php';
require_once 'spare-parts/database.php';
require_once 'spare-parts/time/intervals.php';          # readInterval
require_once 'spare-parts/url.php';
require_once 'spare-parts/web-client/http-simple.php';  # HttpSimple

use \Chipin\Bitcoin,
  \SpareParts\URL, \SpareParts\WebClient\HttpSimple, \SpareParts\Webapp\Forms,
  \SpareParts\Database as DB, \SpareParts\Time;

/**
 * This controller deals with all requests for obtaining info from the Bitcoin network
 * via JavaScript (i.e., AJAX requests), including address balances.
 */
class BitcoinController extends \Chipin\WebFramework\Controller {

  public function addressBalance() {
    $address = $this->context->takeNextPathComponent();
    $fiveSecs = Time\readInterval('5 seconds');
    try {
      return $this->textResponse(Bitcoin\getBalance($address, 'BTC', $maxCacheAge = $fiveSecs));
    } catch (Bitcoin\InvalidAddress $_) {
      return $this->textResponse('Invalid Bitcoin address', $code = 400);
    }
  }

  /**
   * Is the given Bitcoin address valid?
   */
  public function validAddress() {
    $address = $this->context->takeNextPathComponentOrNull();
    return $this->textResponse($this->isValidAddress($address) ? 'true' : 'false');
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
