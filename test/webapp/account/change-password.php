<?php

namespace Chipin\Test;

// require_once 'spare-parts/database.php';
require_once dirname(dirname(__FILE__)) . '/harness.php';
require_once 'chipin/passwords.php';

use \User, \Chipin\Passwords;

class ChangePasswordTest extends WebappTestingHarness {

  function testChangingPassword() {
    $userB4 = $this->loginAsNormalUser();
    assertFalse(Passwords\isValid('n00p@ss', $userB4->passwordEncrypted));
    $this->get('/account/change-password');
    $this->submitForm($this->getForm(),
      array('current-password' => 'abc123', 'password' => 'n00p@ss',
            'confirm-password' => 'n00p@ss'));
    $userAfter = User::loadFromID($userB4->id);
    assertTrue(Passwords\isValid('n00p@ss', $userAfter->passwordEncrypted));
  }
}
