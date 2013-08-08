<?php

namespace Chipin\Test;

require_once dirname(dirname(__FILE__)) . '/harness.php';
require_once 'spare-parts/string.php';    # beginsWith

use \SpareParts\Test\HttpRedirect, \SpareParts\Test\HttpNotFound;

class SigninTests extends WebappTestingHarness {

  function testSigninAndSignoutProcess() {
    newUser('gary@test.com', 'gary', 'lollipop');
    $this->get('/');
    $this->clickLink("//a[contains(@href, 'signin')]");
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
    $this->get('/');
    $this->clickLink("//a[contains(@href, 'signout')]");
    $this->assertContains("//div[contains(text(), 'signed out')]");
    try {
      $this->get('/dashboard/');
      fail("Dashboard should not be available after signing out!");
    } catch (HttpRedirect $_) { /* That's what we're expecting ... */ }
      catch (HttpNotFound $_) { /* Or a "not found". */ }
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
