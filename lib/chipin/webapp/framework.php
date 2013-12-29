<?php

namespace Chipin\WebFramework;

require_once 'spare-parts/webapp/base-framework.php';
require_once 'spare-parts/webapp/filters/csrf-guard.php'; # CSRFGuard
require_once 'spare-parts/types.php';                     # asString
require_once 'chipin/users.php';
require_once 'chipin/env/log.php';

# Make sure \Chipin\WebFramework\Controller is in scope.
require_once 'chipin/webapp/controller.php';

# XXX: To make sure the Widget class is in scope when the session begins...
require_once 'chipin/widgets.php';

use \SpareParts\Webapp\AccessForbidden, \SpareParts\Webapp\HttpResponse,
  \SpareParts\Webapp\Filter, \SpareParts\Webapp\Filters, \Chipin\Log;

function routeRequestForApp() {
  $siteDir = dirname(dirname(dirname(dirname(__FILE__))));
  $fc = new FrontController($siteDir);
  $fc->go();
}

class FrontController extends \SpareParts\Webapp\FrontController {

  protected function filters() {
    return array(new Filters\CSRFGuard(), new AddFrameOptionsHeader());
  }

  protected function info($msg)   { Log\info($msg); }
  protected function notice($msg) { Log\notice($msg); }
  protected function warn($msg)   { Log\warn($msg); }

  protected function sessionLifetimeInSeconds() { return 60 * 60 * 24 * 3; /* 3 days good? */ }
  protected function nameOfSessionCookie() { return 'PHPSESSID'; }

  protected function getUserForCurrentRequest() {
    # XXX: Temporary measure as we phase out this "Zend_Auth" stuff...
    if (isset($_SESSION['Zend_Auth']['storage'])) {
      $_SESSION['user'] = $_SESSION['Zend_Auth']['storage'];
      unset($_SESSION['Zend_Auth']['storage']);
    }
    return @ $_SESSION['user'];
  }

  protected function checkAccessPrivileges($cmd, $user) {
    if ($this->pathIsOpenToAll($cmd)) {
      return true;
    } else if (empty($user) && $this->pathIsOpenToAuthenticatedUser($cmd)) {
      // TODO: Enable this...
      //throw new DoRedirect('/account/login?next=' . $_SERVER['REQUEST_URI']);
    } else if (!empty($user) && $this->pathIsOpenToAuthenticatedUser($cmd)) {
      return true;
    } else if ($cmd[0] == 'admin') {
      $admins = array('chris@easyweaze.net', 'alex.khajehtoorian@gmail.com',
                      'roger@memorydealers.com');
      if (!empty($user) && in_array($user->email, $admins)) return true;
      else throw new AccessForbidden;
    }
    throw new AccessForbidden;
  }

  protected function pathIsOpenToAll($cmd) {
    $openSections = array('about', 'bitcoin', 'widgets', 'widget-wiz');
    $openPaths = array(array(''), array('contact-us'),
      array('account', 'signup'), array('account', 'signin'), array('account', 'signout'),
      array('account', 'lost-pass'), array('account', 'pass-reset'));
    if (in_array($cmd, $openPaths) || in_array(current($cmd), $openSections)) {
      return true;
    } else {
      return false;
    }
  }

  protected function pathIsOpenToAuthenticatedUser($cmd) {
    $specificPages = array();
    $openSections = array(/*'widget-wiz',*/ 'account', 'dashboard');
    return in_array($cmd, $specificPages) || in_array($cmd[0], $openSections);
  }
}

class AddFrameOptionsHeader implements Filter {
  public function incoming() {}
  public function outgoing(HttpResponse $response) {
    if (!beginsWith($_SERVER['REQUEST_URI'], '/widgets/')) {
      $response->addHeader('X-Frame-Options', 'DENY');
    }
  }
}
