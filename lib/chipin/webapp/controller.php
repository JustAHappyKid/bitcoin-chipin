<?php

namespace Chipin\WebFramework;

require_once 'spare-parts/webapp/base-controller.php';
require_once 'spare-parts/webapp/forms.php';            # Form

use \SpareParts\Webapp\Forms\Form;

class Controller extends \SpareParts\Webapp\Controller {

  protected function docRoot() {
    return dirname(dirname(dirname(dirname(__FILE__)))) . '/public';
  }

  protected function formIsValid(Form $form, $vars) {
    if (!$form->hasBeenValidated()) $form->validate($vars);
    return $form->isValid();
  }

  protected function isPostRequestAndFormIsValid(Form $form) {
    if ($this->isPostRequest()) {
      return $this->formIsValid($form, $_POST);
    } else {
      return false;
    }
  }

  protected function render($tplFile, $className, Array $vars = array()) {
    require_once $this->templatePath($tplFile);
    $tplObj = new $className;
    $tplObj->user = $this->user;
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
