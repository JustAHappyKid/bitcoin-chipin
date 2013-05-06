<?php

# XXX: This action is a temporary measure to allow us to test actions (i.e., widget-wiz)
# XXX: that require being logged in... Eventually we'll properly implement login support
# XXX: for test cases.

class TmpTestHook extends \Chipin\WebFramework\Controller {

  function login() {
    if (!defined('TESTING') || TESTING != true) throw new PageNotFound;
    $u = User::loadFromUsername($_POST['un']);
    $_SESSION['Zend_Auth']['storage'] = $u;
  }
}

return 'TmpTestHook';
