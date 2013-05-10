<?php

require_once 'chipin/passwords.php';          # isValid, hash
require_once 'my-php-libs/webapp/forms.php';  # Form, newPasswordField, etc.

use \Chipin\Passwords, \MyPHPLibs\Webapp\Forms;

class AccountController extends \Chipin\WebFramework\Controller {

  /*
  function register() {
    $form = new WebForm('post');
    $form->addSection('password', array(
      newBasicTextField('username', 'Username')->
        minLength(5, "Sorry, the minimum allowed length for a username is five characters."),
      newEmailAddressField('email', "Email Address"),
      newPasswordField('password1', 'Password')->
        required('Please provide a password'),
      newPasswordField('password2', 'Re-enter password')->
        required('Please confirm your password by entering it again.')->
        shouldMatch('password1', "The two entered passwords do not match.")));
    $form->addSubmitButton('Register');
    $this->templateVar('form', $form);
    if ($this->isPostRequestAndFormIsValid($form)) {
      xxx;
    } else {
      return $this->render('registetr.php');
    }
  }
  */

  function changePassword() {
    $user = $this->user;
    $form = new Forms\Form('post');
    $passwordField = Forms\newPasswordField('password', 'Password');
    $confirmPassField = new PasswordConfirmField('confirm-password', 'Re-enter password');
    $form->addSection('change-password', array(
      Forms\newPasswordField('current-password', 'Current Password')->
        required('Please authenticate by entering your current password.')->
        addValidation(function($_, $pass) use($user) {
          return Passwords\isValid($pass, $user->passwordEncrypted) ?
            array() : array("Your current password is incorrect!");
        }),
      $passwordField->
        addValidation(function($_, $pass) {
          return (strlen($pass) < 5) ?
            array('Password must be at least five (5) characters long.') : array();
        }),
      $confirmPassField->required('Please confirm the password by entering it a second time.')));
    $success = false;
    if ($this->isPostRequestAndFormIsValid($form)) {
      $hashedPass = Passwords\hash($form->getValue("password"));
      $user->updatePassword($hashedPass);
      $success = true;
    }
    return $this->render('account/change-password.php', 'ChangePassword',
      array('form' => $form, 'success' => $success));
  }
}

class PasswordConfirmField extends Forms\PasswordField {
  protected function validateWhenNotEmpty(Array $submittedValues, $pass2) {
    return ($submittedValues['password'] === $pass2) ?
      array() : array('The two passwords entered do not match!');
  }
}

return 'AccountController';
