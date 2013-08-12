<?php

namespace Chipin\Test;

require_once dirname(__FILE__) . '/harness.php';  # WebappTestingHarness
require_once 'chipin/widgets.php';                # Widget
require_once 'spare-parts/database.php';          # query
require_once 'spare-parts/time/intervals.php';    # readInterval

use \DateTime, \SpareParts\Time, \SpareParts\Database as DB, \Chipin\Widgets\Widget;

class DashboardTests extends WebappTestingHarness {

  private $user;

  function setUp() {
    parent::setUp();
    $this->user = $this->loginAsNormalUser();
  }

  function testDashboard() {
    $w1 = getWidget($this->user);
    $threeDaysAgo = (new DateTime('now'))->sub(Time\readInterval('3 days'));
    $this->updateEndingDate($w1, $threeDaysAgo);
    $w2 = getWidget($this->user);
    $inOneHour = (new DateTime('now'))->add(Time\readInterval('1 hour'));
    $this->updateEndingDate($w2, $inOneHour);
    $w3 = getWidget($this->user);
    $manana = (new DateTime('now'))->add(Time\readInterval('1 day'));
    $this->get('/dashboard/');
    
    # TODO: Test that each widget shows up under appropriate tabs...
  }

  private function updateEndingDate(Widget $w, DateTime $dt) {
    DB\query("UPDATE widgets SET ending = ? WHERE id = ?", array($dt, $w->id));
  }
}

