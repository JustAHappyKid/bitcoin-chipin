<?php

namespace Chipin\Test;

require_once dirname(__FILE__) . '/harness.php';  # WebappTestingHarness
require_once 'chipin/bitcoin.php';                # satoshisPerBTC
require_once 'chipin/currencies.php';             # Currencies\*
require_once 'chipin/widgets.php';                # Widget, allowedSizes, ...
require_once 'spare-parts/database.php';          # insertOne, ...

use \Chipin\Widgets, \Chipin\Bitcoin, \Chipin\Currencies, \SpareParts\Database as DB, \DateTime;
use SpareParts\Test\HttpNotFound;

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

  private function browseToWidget(Widgets\Widget $w) {
    return $this->get("/widgets/by-id/{$w->id}");
  }

  private function assertProgressBarReads($percent) {
    $elems = $this->xpathQuery("//*[@class='status-bar-container']//*[@class='bar']");
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
