<?php

require_once 'spare-parts/template/base.php';
use \SpareParts\Template;

class IndexController extends \Chipin\WebFramework\Controller {
  function index() {
    return $this->render('index.php');
  }
}

return 'IndexController';
