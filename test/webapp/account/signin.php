<?php

namespace Chipin\Test;

require_once dirname(dirname(__FILE__)) . '/harness.php';
require_once 'spare-parts/string.php';    # beginsWith

use \SpareParts\Test\HttpRedirect;

class SigninTests extends WebappTestingHarness {

  function testSigninProcess() {
    newUser('gary@test.com', 'gary', 'lollipop');
    /*
    XXX; Commented out until landing page is moved out of Zend Framework's territory...
    $this->get('/');
    $this->clickLink("//a[contains(@href, 'signin')]");
    */
    $this->get('/account/signin');
    # XXX: It's required we catch the redirect for now, as the Zend Framework presently renders
    # XXX: the Dashboard, and we don't have ZF testing support.
    $this->followRedirects(false);
    try {
      $this->submitForm($this->getForm('signin-form'),
        array('username' => 'gary', 'password' => 'lollipop'));
    } catch (HttpRedirect $e) {
      assertTrue(beginsWith($e->path, '/dashboard'));
    }
  }

  function testAttemptingToSigninWithIncorrectPassword() {
    newUser('jimmy@test.com', 'jimmy', 'strawberries');
    $this->get('/account/signin');
    # XXX: It's required we catch the redirect for now, as the Zend Framework presently renders
    # XXX: the Dashboard, and we don't have ZF testing support.
    $this->followRedirects(false);
    try {
      $this->submitFormExpectingErrors($this->getForm(),
        array('username' => 'jimmy', 'password' => 'incorrect'));
    } catch (HttpRedirect $e) {
      fail("Did not expect redirect");
    }
  }

  protected function getForm($formId = null) {
    return parent::getForm($formId ? $formId : 'signin-form');
  }
}
