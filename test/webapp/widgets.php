<?php

namespace Chipin\Test;

require_once dirname(__FILE__) . '/harness.php';  # WebappTestingHarness
require_once 'chipin/widgets.php';                # Widget

use \Chipin\Widgets\Widget, \DateTime;

class WidgetTests extends WebappTestingHarness {

  function testRenderingWidget() {
    $u = getUser();
    $w = new Widget;
    $w->ownerID = $u->id;
    $w->title = "Test Widget";
    $w->ending = new DateTime('2020-06-30');
    $w->goal = 100;
    $w->currency = 'USD';
    $w->raised = 30;
//    $w->width = XXX?
//    $w->height = XXX?
//    $w->color = XXX?
    $w->bitcoinAddress = $this->btcAddr();
    $w->countryCode = 'CA';
    $w->about = "This is a test widget!";
    $w->save();
    $this->get("/widgets/by-id/{$w->id}");
  }

  private function btcAddr() { return '1PUPt26votHesaGwSApYtGVTfpzvs8AxVM'; }
}
