<?php

namespace Chipin\Test;

require_once dirname(__FILE__) . '/harness.php';  # WebappTestingHarness

use \SpareParts\Webapp\MaliciousRequestException, \SpareParts\Test\UnexpectedHttpResponseCode;

class ContactFormTests extends WebappTestingHarness {

  function setUp() {
    parent::setUp();
    $this->createHomepageWidgets();
  }

  function testMaliciousContentIsRejected() {
    $badStuff = array('../../../etc/passwd', "1';select pg_sleep(4); --",
      ".././../.../.././windows/win.ini", "1some_inexistent_file_with_long_name%00.jpg",
      "-1 or 94=94");
    foreach ($badStuff as $b) {
      $this->get('/');
      try {
        $this->submitFormExpectingErrors($this->getForm(),
          array('name' => 'Fred', 'email' => 'fred@test.org', 'comments' => $b));
      } catch (MaliciousRequestException $_)  {
        /* We'll take that. */
      } catch (UnexpectedHttpResponseCode $e) {
        /* Or that, if it's an appropriate response code. */
        assertTrue($e->statusCode >= 400);
      }
    }
//    $this->get('/');
//    try {
//      $this->submitFormExpectingErrors($this->getForm(),
//        array('name' => '"+response.write(9395793*9073153)+"', 'email' => 'jim@test.net',
//              'comments' => "hi guys, what is the policy about posting nonsense?"));
//    } catch (MaliciousRequestException $_) { /* We'll take that. */ }
  }

  function testLinkMarkupRejected() {
    $c = '<a href=http://www.olsontek.com/?q=node/7>air Jordan 12 retro taxi</a> Also you can ' .
      'enable it to be straight into a healthy smoothies (using only purified liquid naturally).';
    $this->getContactUsPage();
    $this->submitFormExpectingErrors($this->getForm(),
      array('name' => 'Jim Jones', 'email' => 'jim@jones.com', 'comments' => $c));
  }

  function testThatCommentsMustBeOfReasonableLength() {
    foreach (array('1', ')', 'hello') as $comments) {
      $this->get('/');
      $this->submitFormExpectingErrors($this->getForm(),
        array('name' => 'John', 'email' => 'j@test.com', 'comments' => $comments));
    }
  }

  function testAbsenceOfFormVariableIsElegantlyHandled() {
    $formVars = array('comments', 'email', 'name');
    foreach ($formVars as $v) {
      $this->get('/');
      try {
        $values = array('name' => 'Fred', 'email' => 'fred@test.org',
          'comments' => "Hey - why don't you guys add this great new feature that I want?");
        unset($values[$v]);
        $this->post('/contact-us/', $values);
      } catch (MaliciousRequestException $_) { /* We'll take that. */ }
        catch (UnexpectedHttpResponseCode $_) { /* Or that. */ }
    }
  }

  function testAccessingContactUsPage() {
    $this->get('/contact-us/');
  }

  /**
   * Just a small layer of abstraction, in case we were to move the contact-form out
   * of the site footer.
   */
  private function getContactUsPage() {
    return $this->get('/');
  }
}
