<?php

namespace Chipin\Test;

require_once dirname(dirname(__FILE__)) . '/harness.php';
require_once 'chipin/users.php';          # User
require_once 'spare-parts/string.php';    # beginsWith
require_once 'spare-parts/database.php';  # countRows

use \SpareParts\Database as DB, \Chipin\User, \SpareParts\Test\UnexpectedHttpResponseCode;

class SignupTests extends WebappTestingHarness {

  function testSignupProcess() {
    $this->createHomepageWidgets();
    $this->get('/');
    $this->clickLink("//a[contains(@href, 'signup')]");
    $this->get('/account/signup');
    $this->followRedirects(true);
    $this->submitForm($this->getForm(),
      array('username' => 'sammy', 'email' => 'sam@test.com', 'password1' => 'luckystars',
            'password2' => 'luckystars', /*'captcha-input' => 'ab12',*/
            'memorydealers-updates' => 'on'));
    $u = User::loadFromUsername('sammy');
    assertEqual('sam@test.com', $u->email);
    $subscriptions = current(DB\simpleSelect('subscriptions', 'user_id = ?', array($u->id)));
    assertFalse(empty($subscriptions), 'Expected to find record in subscriptions table for user');
    assertTrue($subscriptions['chipin'] == false || $subscriptions['chipin'] == 0);
    assertTrue($subscriptions['memorydealers'] == true || $subscriptions['memorydealers'] == 1);
    $this->logout();
    $this->login('sammy', 'luckystars');
    $this->get('/dashboard/');
    assertTrue(beginsWith($this->getCurrentPath(), '/dashboard'));
  }

  function testSignupFormDoesNotBreakIfGivenUsernameAlreadyExistsInDB() {
    $this->get('/account/signup');
    $u = getUser();
    $this->submitFormExpectingErrors($this->getForm(),
      array('username' => $u->username, 'email' => uniqid() . '@test.org',
            'password1' => 'chocolate', 'password2' => 'chocolate', /*'captcha-input' => 'ab12'*/));
  }

  function testOneEmailAddressMayNotBeAssociatedWithMultipleAccounts() {
    newUser($email = 'josh@example.com', $username = 'joshers', $password = 'abc123');
    $this->get('/account/signup');
    $this->submitFormExpectingErrors($this->getForm(),
      array('username' => 'bigkid', 'email' => 'josh@example.com', 'password1' => 't0pS33cret',
            'password2' => 't0pS33cret', /*'captcha-input' => '1234'*/));
    $this->assertContains("//div[contains(., 'already have an account')]");
    assertEqual(1, DB\countRows('users', 'email = ?', array('josh@example.com')));
  }

  function testSubmittingSignupFormWhenSessionHasExpired() {
    $this->get('/account/signup');
    $this->clearSession();
    try {
      $this->submitFormExpectingErrors($this->getForm(),
        array('username' => 'fruity-tootie', 'email' => uniqid() . '@test.com',
          'password1' => 'apples', 'password2' => 'apples'));
    } catch (UnexpectedHttpResponseCode $_) { }
    $this->assertContains("//div[contains(@class, 'error') and contains(., 'session expired')]");
  }

  protected function getForm($formId = null) {
    return parent::getForm($formId ? $formId : 'signup-form');
  }
}
