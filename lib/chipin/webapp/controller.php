<?php

namespace Chipin\WebFramework;

require_once 'my-php-libs/webapp/base-controller.php';

class Controller extends \MyPHPLibs\Webapp\Controller {

  protected function render($tplFile, $className, Array $vars = array()) {
    require_once $this->templatePath($tplFile);
    $tplObj = new $className;
    foreach ($vars as $v => $value) $tplObj->$v = $value;
    ob_start();
    $tplObj->content();
    $pgContent = ob_get_contents();
    ob_end_clean();
    return $pgContent;
  }

  private function templatePath($tpl) {
    $baseWebappDir = dirname(dirname(dirname(dirname(__FILE__))));
    return "$baseWebappDir/templates/$tpl";
  }
}

return 'AccountController';
