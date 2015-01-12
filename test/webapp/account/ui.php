<?php

namespace Chipin\Test;

require_once dirname(dirname(__FILE__)) . '/harness.php';
require_once 'spare-parts/string.php';    # contains

use \SpareParts\Test\HttpRedirect, \SpareParts\Test\HttpNotFound;

class AccountRelatedUITests extends WebappTestingHarness {

  function setUp() {
    parent::setUp();
    $this->followRedirects(true);
  }

  /**
   * A user that isn't logged in shouldn't see the "Your Account" menu (including, "Your Widgets",
   * "Change Password", etc).
   */
  function testSigninYourAccountMenuIsNotDisplayedToNonAuthenticatedUser() {
    $this->createHomepageWidgets();
    $this->get('/account/signout');
    $this->get('/');
    assertFalse(contains($this->currentPageContent(), "Your Account"));
    assertFalse(contains($this->currentPageContent(), "Change Password"));
    assertFalse(contains($this->currentPageContent(), "Sign Out"));
    assertTrue(contains($this->currentPageContent(), "Sign In"));
  }
}
