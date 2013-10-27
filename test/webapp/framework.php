<?php

namespace Chipin\Test;

use \SpareParts\Webapp\MaliciousRequestException;

require_once dirname(__FILE__) . '/harness.php';

class WebFrameworkTests extends WebappTestingHarness {

  function testHandlingOfArrayInGlobalCookieVar() {
    $_COOKIE['junk'] = array('abc', 'def');
    try {
      $this->get('/about/faq');
    } catch (MaliciousRequestException $_) { /* We'll take that. */ }
    $_COOKIE = array();
  }
}
