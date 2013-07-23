<?php

namespace Chipin\Test;

use SpareParts\Test\HttpNotFound;

require_once dirname(dirname(__FILE__)) . '/harness.php';

class AdminBasicTests extends WebappTestingHarness {

  function testAccessingWhenNotLoggedIn() {
    $this->logout();
    try {
      $this->get('/admin/users/');
    } catch (HttpNotFound $_) { /* That's good. */ }
  }
}
