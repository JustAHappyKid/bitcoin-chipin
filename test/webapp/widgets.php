<?php

namespace Chipin\Test;

require_once dirname(__FILE__) . '/harness.php';  # WebappTestingHarness
require_once 'chipin/bitcoin.php';                # satoshisPerBTC
require_once 'chipin/currencies.php';             # Currencies\*
require_once 'chipin/widgets.php';                # Widget, allowedSizes, ...
require_once 'chipin/webapp/routes.php';          # Routes\*
require_once 'spare-parts/database.php';          # insertOne, ...

use \Chipin\Widgets, \Chipin\Bitcoin, \Chipin\Currencies, \Chipin\WebFramework\Routes,
  \SpareParts\Database as DB, \SpareParts\Test\HttpNotFound,
  \SpareParts\Test\UnexpectedHttpResponseCode, \DateTime;

class WidgetTests extends WebappTestingHarness {

  function testRenderingWidget() {
    $w = getWidget();
    # Test each widget size...
    foreach (Widgets\allowedSizes() as $d) {
      $w->width = $d->width;
      $w->height = $d->height;
      $w->save();
      $this->browseToWidget($w);
      $goalDiv = current($this->xpathQuery("//div[@class='goal']"));
      assertTrue(contains($goalDiv->textContent, '100.00 USD') ||
                 contains($goalDiv->textContent, '100 USD'));
    }
  }

  function testWidgetPreview() {
    $ds = Widgets\allowedSizes();
    $descriptions = array('This is to test preview mode.', str_repeat('words ', 100));
    foreach ($descriptions as $about) {
      $this->get("/widgets/preview?title=Oh+Bother&goal=3&currency=BTC&" .
        "about=" . urlencode($about) . "&ending=2020-07-25&address=" . $this->btcAddr() . "&" .
        "width={$ds[0]->width}&height={$ds[0]->height}&color=silver");
      $this->assertContains("//div[contains(., '" . substr($about, 0, 15) . "')]");
      $goalDiv = current($this->xpathQuery("//div[@class='goal']"));
      assertTrue(contains($goalDiv->textContent, '3 BTC') ||
                 contains($goalDiv->textContent, '3.0 BTC'));
    }
  }

  function testDisplayingAltCurrency() {
    $w = getWidget();
    foreach (array(array(Currencies\USD(), Currencies\BTC()),
                   array(Currencies\BTC(), Currencies\USD())) as $pair) {
      $w->currency = $pair[0];
      $w->save();
      $altCurrency = $pair[1];
      $this->browseToWidget($w);
      foreach ($this->xpathQuery("//div[@class='alt-amount']") as $div) {
        assertTrue(contains($div->textContent, $altCurrency),
          "Alt-currency should be listed with unit $altCurrency");
      }
    }
  }

  function testRenderingVariousAmounts() {
    $cases = array(
      array(Currencies\BTC(), 0.01908, array('0.019 BTC', '0.02 BTC', '19 mBTC')),
      array(Currencies\BTC(), 0.0, array('0 BTC', '0.0 BTC'))
    );
    foreach ($cases as $case) {
      list($currency, $btcAmount, $acceptableRenderings) = $case;
      $w = getWidget();
      $w->currency = $currency;
      $w->save();
      $this->setBalance($w->bitcoinAddress, $btcAmount);
      $this->browseToWidget($w);
      $raisedDiv = current($this->xpathQuery("//div[@class='raised']"));
      $matches = array_filter($acceptableRenderings,
        function($t) use($raisedDiv) {
          return contains($raisedDiv->textContent, $t); });
      $matchFound = (1 == count($matches));
      assertTrue($matchFound,
        "Should render as one of following: " . implode(", ", $acceptableRenderings));
    }
  }

  function testProgressBar() {
    $w = getWidget();
    $w->setGoal(4, 'BTC');
    $w->save();
    foreach (array(array(0, 0), array(2, 50), array(5, 100)) as $c) {
      $this->setBalance($w->bitcoinAddress, $c[0]);
      $this->browseToWidget($w);
      $this->assertProgressBarReads($c[1]);
    }
  }

  function testAttemptingToAccessWidgetsForNonExistantUser() {
    foreach (array('', 'jimmy-peterson-759') as $uname) {
      try {
        $this->get("/widgets/u/$uname");
      } catch (HttpNotFound $_) { /* A-okay */ }
    }
  }

  function testAttemptingToAccessWidgetWhichUserDoesNotHave() {
    $u = getUser();
    try {
      $this->get("/widgets/u/{$u->username}/" . uniqid());
    } catch (HttpNotFound $_) { /* That's acceptable. */ }
  }

  /**
   * Make sure these paths/URIs don't cause breakage, as they once did.
   */
  function testSomeInvalidURIs() {
    $this->expect404onGET("/widgets/by-id");
    $this->expect404onGET("/widgets/about");
    $this->expect404onGET("/widgets/by-id/");
    $this->expect404onGET("/widgets/by-id/0");
    $this->expect404onGET("/widgets/by-id/-4095");
  }

  private function expect404onGET($path) {
    try {
      $this->get($path);
      fail("Expected to get 404/not-found response");
    } catch (HttpNotFound $_) { /* That's what we're looking for. */ }
  }

  function testAccessingWidgetWhoseOwnerHasNoUsernameSet() {
    $u = getUser();
    $u->username = null;
    DB\updateByID('users', $u->id, array('username' => null));
    $w = getWidget($u);
    $this->get("/widgets/by-id/{$w->id}");
    $this->assertContains("//div[contains(., '{$w->title}')]");
  }

  function testActionForCheckingAddressBalanceViaJavascript() {
    $w = getWidget();
    $this->get(Routes\amountRaised($w));
    $this->get(Routes\amountRaised($w) . '?currency=BTC');
    $this->get(Routes\amountRaised($w) . '?currency=USD');
    $this->get(Routes\amountRaised($w) . '?currency=CNY');
  }

  /**
   * In the case that there's some sort of communication problem with attempting to check
   * the given Bitcoin-address balance (via Blockchain.info), we want to make sure that
   * does not lead to an exception reaching the top level.
   */
  function testActionForCheckingWidgetProgressViaJavascriptElegantlyHandlesNetworkError() {
    $w = getWidget();
    $w->bitcoinAddress = '1AkZUyVHtVsU6ZmAu1iSDhYiXbqFgKqzbt';
    $w->save();
    try {
      $this->get(Routes\checkWidgetProgress($w));
    } catch (UnexpectedHttpResponseCode $_) { /* We'll accept that. */ }
  }

  private function browseToWidget(Widgets\Widget $w) {
    return $this->get("/widgets/by-id/{$w->id}");
  }

  private function assertProgressBarReads($percent) {
    $elems = $this->findElements("//*[@class='status-bar-container']//*[@class='bar']");
    assertEqual("width: {$percent}%;", $elems[0]->getAttribute('style'));
  }

  private function setBalance($address, $btc) {
    DB\delete('bitcoin_addresses', 'address = ?', array($address));
    DB\insertOne('bitcoin_addresses',
      array('address' => $address, 'satoshis' => $btc * Bitcoin\satoshisPerBTC(),
            'updated_at' => new DateTime('now')));
  }

  private function btcAddr() { return '1PUPt26votHesaGwSApYtGVTfpzvs8AxVM'; }
}
