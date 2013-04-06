<?php

require_once 'my-php-libs/url.php';                     # constructUrlFromRelativeLocation
require_once 'my-php-libs/webapp/current-request.php';  # CurrentRequest\getURL

use \MyPHPLibs\URL, \MyPHPLibs\Webapp\CurrentRequest;

class IndexController extends Zend_Controller_Action {

  private $auth;

  public function init() {
    $this->auth = Zend_Auth::getInstance();
    if (!$this->auth->hasIdentity()) {
      $this->relativeRedirect('/signin/index/');
    }
    if ($this->auth->getIdentity()->role == 'admin') {
      $this->relativeRedirect('/admin/index/');
    }
    $this->relativeRedirect('/dashboard/index/');
  }

  public function indexAction() { }

  protected function relativeRedirect($path, $code = 302) {
    $host = $_SERVER['HTTP_HOST'];
    if (substr($path, 0, strlen($host) == $host)) {
      throw new Exception("redirect should be called with a relative or absolute path, " .
        "without the HTTP_HOST as a prefix");
    }
    //throw new DoRedirect($path, $code);
    $url = URL\constructUrlFromRelativeLocation(CurrentRequest\getURL(), $path);
    $this->_redirect($url);
  }
}
