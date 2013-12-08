<?php

namespace Chipin\Test;

use \SpareParts\Webapp\MaliciousRequestException, \SpareParts\Test\UnexpectedHttpResponseCode;

require_once dirname(__FILE__) . '/harness.php';

class WebFrameworkTests extends WebappTestingHarness {

  function testHandlingOfArrayInGlobalCookieVar() {
    $_COOKIE['junk'] = array('abc', 'def');
    try {
      $this->get('/about/faq');
    } catch (MaliciousRequestException $_) {
      /* We'll take that. */
    } catch (UnexpectedHttpResponseCode $e) {
      /* Or that, if code matches expected... */
      assertTrue($e->statusCode >= 400);
    }
    $_COOKIE = array();
  }

  /**
   * The X-Frame-Options HTTP header (with a value of "DENY") should be included by default
   * on all pages. Only the chipin widgets themselves should exclude this option, as they will
   * be embedded in iframes on other websites.
   */
  function testInclusionOfFrameOptionsHeader() {
    foreach (array('/about/', '/account/signup', '/widget-wiz/step-one') as $uri) {
      $r = $this->get($uri);
      $hs = $r->getValuesForHeader('X-Frame-Options');
      $value = strtolower(head($hs));
      assertTrue($value == 'deny' || $value == 'sameorigin');
    }
    $w = getWidget();
    $r = $this->get("/widgets/by-id/{$w->id}");
    assertEmpty($r->getValuesForHeader('X-Frame-Options'));
  }
}
