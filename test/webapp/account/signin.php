<?php

namespace Chipin\Test;

require_once dirname(dirname(__FILE__)) . '/harness.php';
require_once 'spare-parts/string.php';    # beginsWith

use \SpareParts\Test\HttpRedirect, \SpareParts\Test\HttpNotFound;

class SigninTests extends WebappTestingHarness {

  function setUp() {
    parent::setUp();
    $this->followRedirects(true);
  }

  function testSigninAndSignoutProcess() {
    newUser('gary@test.com', 'gary', 'lollipop');
    $this->createHomepageWidgets();
    $this->get('/');
    $this->clickLink("//a[contains(@href, 'signin')]");
    $this->get('/account/signin');
    $this->submitForm($this->getForm('signin-form'),
      array('username' => 'gary', 'password' => 'lollipop'));
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
    $this->submitFormExpectingErrors($this->getForm(),
      array('username' => 'jimmy', 'password' => 'incorrect'));
  }

  protected function getForm($formId = null) {
    return parent::getForm($formId ? $formId : 'signin-form');
  }
}
