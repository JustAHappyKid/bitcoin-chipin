<?php

namespace Chipin\Test;

require_once 'spare-parts/test/webapp.php'; # WebappTestingHarness
require_once 'spare-parts/database.php';    # query

use \Chipin\WebFramework\FrontController, \SpareParts\Database as DB, \SpareParts\Webapp,
  \SpareParts\Test\HttpRedirect;

# XXX: Phase out use of these global constants??
define('PATH', 'https://test.org/');
define('APPLICATION_ENV', 'testing');

$webappDir = dirname(dirname(dirname(__FILE__)));
require_once "$webappDir/lib/chipin/webapp/framework.php";

abstract class WebappTestingHarness extends \SpareParts\Test\WebappTestingHarness {

  function setUp() {
    parent::setUp();
    clearDB();
    $this->followRedirects();
  }

  protected function domain() { return 'test.org'; }

  protected function login($username, $password) {
    try {
      $this->get('/account/signin');
      $this->submitForm($this->getForm('signin-form'),
        array('username' => $username, 'password' => $password));
    } catch (HttpRedirect $_) { /* That's okay -- we should be redirected to the Dashboard. */ }
  }

  protected function loginAsNormalUser() {
    $un = 'user-' . uniqid();
    $u = newUser($email = $un . '@example.com', $username = $un, $password = 'abc123');
    $this->login($un, $password);
    return $u;
  }

  protected function logout() {
    try {
      $this->get('/account/signout');
    } catch (HttpRedirect $_) { /* That can happen if already logged out. */ }
  }

  /**
   * The homepage expects widgets having IDs 1, 2, 3, and 4 to exist, as it uses these
   * as "sample widgets".
   */
  protected function createHomepageWidgets() {
    for ($id = 1; $id <= 4; $id++) {
      $w = getWidget();
      DB\query("UPDATE widgets SET id = ? WHERE id = ?", array($id, $w->id));
    }
  }

  protected function dispatchToWebapp() {
    global $__frontControllerForTesting;
    $__frontControllerForTesting->go();
    return $__frontControllerForTesting->lastResponse;
  }

  protected function getValidationErrors() {
    $nodes = $this->findElements("//*[contains(@class, 'error')]");
    return array_filter($nodes,
      function(\DOMElement $e) {
        # If the node has a "display:none" CSS definition, toss it.
        return !preg_match('/\\bdisplay:\\s*none;/', $e->getAttribute('style')); });
  }
}

class FrontControllerForTesting extends FrontController {
  public $lastResponse;
  protected function outputHttpResponse(Webapp\HttpResponse $r) {
    return $this->lastResponse = $r;
  }
  protected function sessionStart() { /* Do nothing -- override call to session_start */ }
}

global $__frontControllerForTesting;
$__frontControllerForTesting = new FrontControllerForTesting($webappDir);
