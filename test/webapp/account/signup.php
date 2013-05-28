<?php

namespace Chipin\Test;

require_once dirname(dirname(__FILE__)) . '/harness.php';
require_once 'chipin/users.php';          # User
require_once 'spare-parts/string.php';    # beginsWith
require_once 'spare-parts/database.php';  # countRows

use \SpareParts\Test\HttpRedirect, \SpareParts\Database as DB, \User;

class SignupTests extends WebappTestingHarness {

  function testSignupProcess() {
    /*
    XXX; Commented out until landing page is moved out of Zend Framework's territory...
    $this->get('/');
    $this->clickLink("//a[contains(@href, 'signup')]");
    */
    $this->get('/account/signup');
    # XXX: It's required we catch the redirect for now, as the Zend Framework presently renders
    #      the Dashboard, and we don't have ZF testing support.
    $this->followRedirects(false);
    try {
      $this->submitForm($this->getForm(),
        array('username' => 'sammy', 'email' => 'sam@test.com', 'password1' => 'luckystars',
              'password2' => 'luckystars', 'captcha-input' => 'ab12'));
    } catch (HttpRedirect $e) {
      assertTrue(beginsWith($e->path, '/dashboard'));
    }
    $u = User::loadFromUsername('sammy');
    assertEqual('sam@test.com', $u->email);
    /*
    $this->logout();
    $this->login('sammy', 'luckystars');
    $this->get('/dashboard/');
    assertTrue(beginsWith($this->getCurrentPath(), '/dashboard'));
    */
  }

  function testOneEmailAddressMayNotBeAssociatedWithMultipleAccounts() {
    $u = newUser($email = 'josh@example.com', $username = 'joshers', $password = 'abc123');
    $this->get('/account/signup');
    $this->submitFormExpectingErrors($this->getForm(),
      array('username' => 'bigkid', 'email' => 'josh@example.com', 'password1' => 't0pS33cret',
            'password2' => 't0pS33cret', 'captcha-input' => '1234'));
    $this->assertContains("//div[contains(., 'already have an account')]");
    assertEqual(1, DB\countRows('users', 'email = ?', array('josh@example.com')));
  }

  protected function getForm() {
    return parent::getForm('signup-form');
  }
}
