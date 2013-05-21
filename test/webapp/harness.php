<?php

namespace Chipin\Test;

require_once 'spare-parts/test/webapp.php'; # WebappTestingHarness

# XXX: Phase out use of these global constants??
define('PATH', 'https://test.org/');
define('APPLICATION_ENV', 'testing');

# XXX: Required for /xxx-tmp-test-hook/login, until we phase that out in
# XXX: favor of a proper login mechanism.
define('TESTING', true);

global $__frontControllerForTesting;
$webappDir = dirname(dirname(dirname(__FILE__)));
require_once "$webappDir/lib/chipin/webapp/route-request.php";
$__frontControllerForTesting = new \Chipin\WebFramework\FrontController($webappDir);

abstract class WebappTestingHarness extends \MyPHPLibs\Test\WebappTestingHarness {

  function setUp() {
    clearDB();
    $this->followRedirects();
  }

  protected function domain() { return 'test.org'; }

  /**
   * XXX: This is temporary hack until we have login form reimplemented in "spare-parts"
   *      framework.
   */
  protected function login($username, $_password) {
    $this->post('/xxx-tmp-test-hook/login', array('un' => $username));
  }

  protected function loginAsNormalUser() {
    $un = 'user-' . uniqid();
    $u = newUser($email = $un . '@example.com', $username = $un, $password = 'abc123');
    $this->login($un, $password);
    return $u;
  }

  protected function dispatchToWebapp() {
    global $__frontControllerForTesting;
    $r = $__frontControllerForTesting->handleRequest();
    // var_dump($r);
    return $r;
  }

  protected function getValidationErrors() {
    echo("WARN: getValidationErrors needs to be properly implemented!\n");
    return array();
  }
}
