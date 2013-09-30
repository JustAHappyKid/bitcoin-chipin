<?php

namespace Chipin\Test;

require_once 'spare-parts/test/webapp.php'; # WebappTestingHarness
require_once 'spare-parts/database.php';    # query

use \Chipin\WebFramework\FrontController, \SpareParts\Database as DB;

# XXX: Phase out use of these global constants??
define('PATH', 'https://test.org/');
define('APPLICATION_ENV', 'testing');

# XXX: Required for /xxx-tmp-test-hook/login, until we phase that out in
# XXX: favor of a proper login mechanism.
define('TESTING', true);

global $__frontControllerForTesting;
$webappDir = dirname(dirname(dirname(__FILE__)));
require_once "$webappDir/lib/chipin/webapp/route-request.php";
$__frontControllerForTesting = new FrontController($webappDir);

abstract class WebappTestingHarness extends \SpareParts\Test\WebappTestingHarness {

  function setUp() {
    parent::setUp();
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

  protected function logout() {
    $_SESSION = array();
  }

  /**
   * The homepage expects widgets having IDs 1, 2, 3, and 4 to exist, as it uses these
   * as "sample widgets".
   */
  protected function createHomepageWidgets() {
    for ($id = 1; $id <= 4; $id++) {
      $w = getWidget();
      DB\query("UPDATE widgets SET id = $id WHERE id = ?", array($w->id));
    }
  }

  protected function dispatchToWebapp() {
    global $__frontControllerForTesting;
    $r = $__frontControllerForTesting->handleRequest();
    return $r;
  }

  protected function getValidationErrors() {
    $nodes = $this->xpathQuery("//*[contains(@class, 'error')]");
    return array_filter($nodes,
      function($n) {
        # If the node has a "display:none" CSS definition, toss it.
        return !preg_match('/\\bdisplay:\\s*none;/', $n->getAttribute('style')); });
  }
}
