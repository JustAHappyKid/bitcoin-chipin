<?php

require_once 'spare-parts/template/base.php';
use \SpareParts\Template;

class IndexController extends \Chipin\WebFramework\Controller {
  function index() {
    return $this->render('index.php', 'IndexPage');
    /*
    $tplDir = dirname(dirname(__FILE__)) . '/templates';
    return Template\renderFromFile("$tplDir/index.php", array());
    */
  }
}

return 'IndexController';
