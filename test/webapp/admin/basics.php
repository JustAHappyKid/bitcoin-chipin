<?php

namespace Chipin\Test;

require_once dirname(dirname(__FILE__)) . '/harness.php';
require_once 'spare-parts/database.php';

use \SpareParts\Test\HttpNotFound, \SpareParts\Database as DB;

class AdminBasicTests extends WebappTestingHarness {

  function testAccessingWhenNotLoggedIn() {
    $this->followRedirects(false);
    $this->logout();
    try {
      $this->get('/admin/users/');
    } catch (HttpNotFound $_) { /* That's good. */ }
  }

  function testUsersOverviewScreen() {
    newUser('some-user@example.com', 'some-user', 'abc');
    $u2 = newUser('other-user@example.com', 'other-user', 'def');
    DB\updateByID('users', $u2->id, array('created_at' => new \DateTime('now')));
    newUser('chris@easyweaze.net', 'chris', 'secret!');
    $this->login('chris', 'secret!');
    $this->get('/admin/users/');
  }
}
