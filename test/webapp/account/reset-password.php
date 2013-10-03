<?php

namespace Chipin\Test;

require_once dirname(dirname(__FILE__)) . '/harness.php';
require_once 'spare-parts/database.php';  # selectExactlyOne

use \SpareParts\Database as DB;

class ResetPasswordTest extends WebappTestingHarness {

  function testResettingPassword() {
    $email = 'MixedCaseAddress@example.org';
    $u = getUser($email);
    $this->logout();
    $this->followRedirects();
    $this->get('/account/signin');
    $this->clickLink("//a[contains(text(), 'Forget your password')]");
    # We specify the address using different cAsINg to make sure things aren't case-sensitive.
    $this->submitForm($this->getForm('lost-pass'),
      array('email' => 'Mixedcaseaddress@example.org'));
    /*
    TODO: Make this test more comprehensive -- but first we need a properly "stubbed-out"
          email for test framemwork...
    $sentEmails = $this->getSentEmails();
    assertTrue(count($sentEmails) == 1);
    $matches = array();
    preg_match('@https?://[-.a-z]+(/[^\\s]+)@', $sentEmails[0]->message, $matches);
    $relativeURI = $matches[1];
    $this->get($relativeURI);
    $this->submitForm($this->getForm(),
      array('password' => 'nu3vo', 'confirmPassword' => 'nu3vo'));
    $this->logout();
    $this->assertNotLoggedIn();
    $this->login($email, 'nu3vo');
    $this->assertLoggedIn($u->email);
    */
    $row = DB\selectExactlyOne('confirmation_codes', 'user_id = ?', array($u->id));
    $this->get('/account/pass-reset?c=' . $row['code']);
    $this->submitForm($this->getForm('change-password-form'),
      array('password' => 'n00p@ss', 'confirm-password' => 'n00p@ss'));
  }

  function testProvidingBogusConfCodeToPasswordResetURI() {
    $this->get('/account/pass-reset?c=' . uniqid("bla-nonsense-"));
  }
}
