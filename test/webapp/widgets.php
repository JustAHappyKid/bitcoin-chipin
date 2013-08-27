<?php

namespace Chipin\Test;

require_once dirname(__FILE__) . '/harness.php';  # WebappTestingHarness
require_once 'chipin/widgets.php';                # Widget

use \Chipin\Widgets;

class WidgetTests extends WebappTestingHarness {

  function testRenderingWidget() {
    $w = getWidget();
    $this->get("/widgets/by-id/{$w->id}");
    $goalDiv = current($this->xpathQuery("//div[@class='goal']"));
    assertTrue(contains($goalDiv->textContent, '100.00 USD') ||
               contains($goalDiv->textContent, '100 USD'));
  }

  function testWidgetPreview() {
    $ds = Widgets\allowedSizes();
    $descriptions = array('This+is+to+test+preview+mode.', str_repeat('words+', 100));
    foreach ($descriptions as $about) {
      $this->get("/widgets/preview?title=Oh+Bother&goal=3&currency=BTC&" .
        "about=$about&ending=2020-07-25&address=" . $this->btcAddr() . "&" .
        "width={$ds[0]->width}&height={$ds[0]->height}&color=silver");
      $goalDiv = current($this->xpathQuery("//div[@class='goal']"));
      assertTrue(contains($goalDiv->textContent, '3 BTC') ||
                 contains($goalDiv->textContent, '3.0 BTC'));

    }
  }

  private function btcAddr() { return '1PUPt26votHesaGwSApYtGVTfpzvs8AxVM'; }
}
