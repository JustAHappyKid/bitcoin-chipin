<?php

namespace Chipin\WebFramework;

require_once 'spare-parts/webapp/base-controller.php';
require_once 'spare-parts/webapp/forms.php';            # Form
//require_once 'spare-parts/reflection.php';              # getClassesDefinedInFile
require_once 'spare-parts/template/base.php';           # Template\Context
require_once 'chipin/users.php';                        # User

use \SpareParts\Webapp\Forms\Form, \Chipin\User, \SpareParts\Template;

class Controller extends \SpareParts\Webapp\Controller {

  /** @var User */
  public $user;

  protected function webappDir() {
    return dirname(dirname(dirname(dirname(__FILE__))));
  }

  protected function docRoot() {
    return pathJoin($this->webappDir(), 'public');
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

  protected function render($tplFile, $className = null, Array $vars = array()) {
    $pathToTpl = $this->templatePath($tplFile);
    if (endsWith($tplFile, '.php')) {
      require_once $pathToTpl;
      if ($className == null) {
        $className = withoutSuffix(basename($tplFile), '.php') . 'Page';
      }
      $tplObj = new $className;
      $tplObj->user = $this->user;
      foreach ($vars as $v => $value) $tplObj->$v = $value;
      ob_start();
      $tplObj->content();
      $pgContent = ob_get_contents();
      ob_end_clean();
      return $pgContent;
    } else if (endsWith($tplFile, '.diet-php')) {
      # XXX: Experimental Diet PHP support!
      if (empty($vars['user'])) $vars['user'] = $this->user;
      $tplContext = new Template\Context($this->templatesDir(), $vars);
      return Template\renderFile($tplFile, $tplContext);
    } else {
      throw new \InvalidArgumentException("Template file `$tplFile` has unexpected extension");
    }
//      $className = Reflection\getClassesDefinedInFile($pathToTpl);
  }

  private function templatePath($tpl) {
    return pathJoin($this->templatesDir(), $tpl);
  }

  private function templatesDir() {
    return pathJoin($this->webappDir(), 'templates');
  }
}
