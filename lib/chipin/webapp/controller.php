<?php

namespace Chipin\WebFramework;

require_once 'spare-parts/webapp/base-controller.php';
require_once 'spare-parts/webapp/forms.php';            # Form
require_once 'spare-parts/reflection.php';              # getClassesDefinedInFile
require_once 'spare-parts/template/base.php';           # Template\Context
require_once 'chipin/users.php';                        # User

use \SpareParts\Webapp\Forms\Form, \Chipin\User, \SpareParts\Webapp\HttpResponse;

class Controller extends \SpareParts\Webapp\Controller {

  /** @var User */
  public $user;

  /**
   * The "active user" is the User object/record for the current user, if one has already
   * been created for said user. The difference between this and the 'user' attribute
   * (of Controller) is that this "active user" *may not* have yet "registered"... I.e.,
   * the "active user" could be a user that has only created a widget (or two) but has not
   * yet provided an email address, password, etc.
   */
  protected function getActiveUser() {
    return $this->user ? $this->user : at($_SESSION, 'unregistered-user');
  }

  protected function setActiveUser(User $u) {
    $_SESSION['unregistered-user'] = $u;
  }

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

  protected function textResponse($txt, $code = 200) {
    $resp = new HttpResponse;
    $resp->statusCode = $code;
    $resp->contentType = 'text/plain';
    $resp->content = strval($txt);
    return $resp;
  }

  protected function render($tplFile, Array $vars = array()) {
    if (empty($vars['user'])) $vars['user'] = $this->user;
    return renderTemplate($tplFile, $vars);
  }

  protected function setAuthenticatedUser(User $user) {
    $_SESSION['user'] = $user;
  }
}
