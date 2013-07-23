<?php

namespace Chipin\WebFramework;

require_once 'spare-parts/webapp/base-framework.php';
require_once 'chipin/users.php';

# Make sure \Chipin\WebFramework\Controller is in scope.
require_once 'chipin/webapp/controller.php';

# XXX: To make sure the Widget class is in scope when the session begins...
require_once 'chipin/widgets.php';

use \SpareParts\Webapp\AccessForbidden;

function routeRequestForApp() {
  $siteDir = dirname(dirname(dirname(dirname(__FILE__))));
  $fc = new FrontController($siteDir);
  $fc->go();
}

class FrontController extends \SpareParts\Webapp\FrontController {

  protected function sessionLifetimeInSeconds() { return 60 * 60 * 24 * 3; /* 3 days good? */ }
  protected function info($msg) {}
  protected function notice($msg) {}
  protected function warn($msg) {}

  protected function renderAndOutputPage($page) {

    //XXX!!!
    return null;
/*
    $response = new ResponseObj;
    $response->statusCode = 200;

    // XXX: Is this right???
    $response->contentType = $page->contentType;

    if ($page->layout) {
      $smarty = createSmartyInstance();
      $smarty->assign('page', $page);
      $smarty->assign('successfulLogin', at($_SESSION, 'successfulLogin'));
      unset($_SESSION['successfulLogin']);
      $response->content = $smarty->fetch($page->layout);
    } else {
      $response->content = $page->body;
    }

    return $response;
*/
  }

  protected function nameOfSessionCookie() { return 'PHPSESSID'; }
  protected function getUserForCurrentRequest() { return @ $_SESSION['Zend_Auth']['storage']; }

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
    $openSections = array('about', 'widgets');
    $openPaths = array(array(''), array('contact-us'),
      array('account', 'signup'), array('account', 'signin')
      /*array('account', 'logout'), array('account', 'login')*/);
    if ($cmd == array('xxx-tmp-test-hook', 'login') && defined('TESTING') && TESTING === true) {
      return true;
    } else if (in_array($cmd, $openPaths) || in_array(current($cmd), $openSections)) {
      return true;
    } else {
      return false;
    }
  }

  protected function pathIsOpenToAuthenticatedUser($cmd) {
    $allowed = array();
    return in_array($cmd, $allowed) || $cmd[0] == 'widget-wiz' || $cmd[0] == 'account';
  }

  /*
  protected function renderStaticPage($path) {
    $page = $this->getDefaultPageForRequest();
    $page->body =
      '<div style="float: left; width: 68%;">' . WebFramework\renderTemplate($path) . '</div>' .
      WebFramework\renderTemplate('toolbox-side-panel.tpl',
        array('user' => $this->getUserForCurrentRequest()));
    return $this->renderAndOutputPage($page);
  }
  */

  /*
  protected function getNotFoundPage() {
    $page = $this->getDefaultPageForRequest();
    $page->layout = 'main-layout.tpl';
    $page->title = 'Resource not found - DownsizeDC.org';
    $page->styleFiles = array('/styles/common.css', '/styles/main.css');
    $page->showFoundersSidebar = false;
    $page->body = '
      <div id="campaigns-list-page">
        <div class="error">Sorry &ndash; we couldn\'t find the page you requested.
          But please consider joining our campaigns to Downsize DC.
        </div>
        ' . renderCampaignList($this->createSmartyInstance()) . '
      </div>';
    return $page;
  }
  */
}
