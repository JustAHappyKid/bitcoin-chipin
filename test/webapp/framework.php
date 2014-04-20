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

  function testHandlingOfArrayInCSRFVar() {
    try {
      $this->post('/account/signin',
        array('password' => 'g00dPa$$w0rD', 'username' => 'vhchfkqm',
              '__sp_guard_name' => array('$acunetix' => '1'),
              '__sp_guard_token' => '5b9084e2710c4cdac625ce23debfe'));
    } catch (UnexpectedHttpResponseCode $e) {
      # Okay, we'll take that...
      assertTrue($e->statusCode >= 400);
    }
  }

  function testHtmlPurifierHandlingOfSubArrayInGET() {
    $_SERVER['REQUEST_URI'] = '/about/';
    $_POST = array();
    $_GET = array('rsargs' => array("-99 UNION SELECT 896054186,2"));
    $filter = new \Chipin\WebFramework\HtmlPurifierFilter;
    $filter->incoming();
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
