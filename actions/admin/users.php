<?php

require_once 'chipin/users.php';
use \Chipin\User;

class AdminUsersController extends \Chipin\WebFramework\Controller {

  function index() {
    $users = User::loadAll();
    return $this->render('admin/users.diet-php', null, array('users' => $users));
  }
}

return 'AdminUsersController';
