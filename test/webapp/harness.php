<?php

namespace Chipin\Test;

require_once 'my-php-libs/test/webapp.php'; # WebappTestingHarness

# XXX: Phase out use of these global constants??
define('PATH', 'https://test.org/');
define('APPLICATION_ENV', 'testing');

# XXX: Required for /xxx-tmp-test-hook/login, until we phase that out in
# XXX: favor of a proper login mechanism.
define('TESTING', true);

global $__frontControllerForTesting;
$webappDir = dirname(dirname(dirname(__FILE__)));
require_once "$webappDir/lib/chipin/route-request.php";
$__frontControllerForTesting = new \Chipin\WebFramework\FrontController($webappDir);

abstract class WebappTestingHarness extends \MyPHPLibs\Test\WebappTestingHarness {

  protected function domain() { return 'test.org'; }

  protected function loginAsNormalUser() {
    $un = 'user-' . uniqid();
    $u = newUser($email = $un . '@example.com', $username = $un, $password = 'abc123');
    $this->post('/xxx-tmp-test-hook/login', array('un' => $un));
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
