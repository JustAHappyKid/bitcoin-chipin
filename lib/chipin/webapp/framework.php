<?php

namespace Chipin\WebFramework;

require_once 'spare-parts/webapp/base-framework.php';
require_once 'spare-parts/webapp/filters/csrf-guard.php'; # CSRFGuard
require_once 'spare-parts/types.php';                     # asString
require_once 'spare-parts/string.php';                    # endsWith
require_once 'chipin/users.php';
require_once 'chipin/env/log.php';

# Make sure \Chipin\WebFramework\Controller is in scope.
require_once 'chipin/webapp/controller.php';

# XXX: To make sure the Widget class is in scope when the session begins...
require_once 'chipin/widgets.php';

use \SpareParts\Webapp\AccessForbidden, \SpareParts\Webapp\HttpResponse,
  \SpareParts\Webapp\Filter, \SpareParts\Webapp\Filters, \Chipin\Log,
  \SpareParts\Webapp\MaliciousRequestException, \SpareParts\Template, \SpareParts\Reflection;

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

  protected function handleMaliciousRequest(MaliciousRequestException $e) {
    $c = null;
    if (isset($_POST['__sp_guard_name'])) {
      $c = renderTemplate('malicious/try-again.diet-php');
    } else {
      $c = renderTemplate('malicious/bad.diet-php');
    }
    return $this->simpleHtmlResponse(400, $c);
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

function renderTemplate($tplFile, Array $vars = array()) {
  $pathToTpl = templatePath($tplFile);
  if (endsWith($tplFile, '.php')) {
    require_once $pathToTpl;
    $classes = Reflection\getClassesDefinedInFile($pathToTpl);
    if (count($classes) == 0) {
      throw new \Exception("No classes defined in file $pathToTpl");
    } else if (count($classes) > 1) {
      throw new \Exception("Multiple classes defined in file $pathToTpl");
    }
    $className = current($classes);
    $tplObj = new $className;
    return renderTemplateObject($tplObj, $vars);
  } else if (endsWith($tplFile, '.diet-php')) {
    $tplContext = new Template\Context(templatesDir(), $vars);
    return Template\renderFile($tplFile, $tplContext);
  } else {
    throw new \InvalidArgumentException("Template file `$tplFile` has unexpected extension");
  }
}

function renderTemplateObject(Template\Renderable $tpl, Array $vars) {
  ob_start();
  $tpl->__render($vars);
  $pgContent = ob_get_contents();
  ob_end_clean();
  return $pgContent;
}

function templatePath($tpl) {
  return pathJoin(templatesDir(), $tpl);
}

function templatesDir() {
  return pathJoin(dirname(dirname(dirname(dirname(__FILE__)))), 'templates');
}
