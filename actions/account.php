<?php

require_once 'chipin/passwords.php';                    # isValid, hash
require_once 'spare-parts/database.php';                # insertOne
require_once 'spare-parts/url.php';                     # constructUrlFromRelativeLocation
require_once 'spare-parts/webapp/current-request.php';  # CurrentRequest\...
require_once 'spare-parts/webapp/forms.php';            # Form, newPasswordField, etc.
require_once 'Zend/Captcha/Image.php';
require_once 'Zend/Auth.php';

use \Chipin\User, \Chipin\NoSuchUser, \Chipin\Passwords,
  \SpareParts\Webapp\Forms as F, \SpareParts\URL,
  \SpareParts\Webapp\CurrentRequest, \SpareParts\Database as DB;

class AccountController extends \Chipin\WebFramework\Controller {

  /* TODO: Validate CAPTCHA properly! */
  function signup() {
    $form = new F\Form('post');
    $form->addSection('password', array(
      F\newBasicTextField('username', 'Username')->
        minLength(5, "Sorry, the minimum allowed length for a username is five characters."),
      F\newEmailAddressField('email', "Email Address")->addValidation(
        function($email) {
          try {
            User::loadFromEmailAddr($email);
            return array("It looks like you already have an account here, registered under " .
                         "that email address. If you've forgotten your password, you can " .
                         "<a href=\"/signin/remind/\">reset it here</a>.");
          } catch (NoSuchUser $_) { return array(); }
        }
      ),
      F\newPasswordField('password1', 'Password')->
        required('Please provide a password'),
      F\newPasswordField('password2', 'Re-enter password')->
        required('Please confirm your password by entering it again.')->
        shouldMatch('password1', "The two entered passwords do not match."),
      F\newCheckboxField('chipin-updates', '', $checked = false),
      F\newCheckboxField('memorydealers-updates', '', $checked = false)));
    $form->addSubmitButton('Register');
    if ($this->isPostRequestAndFormIsValid($form)) {
      $username = $form->getValue("username");
      $passwordHashed = Passwords\hash($form->getValue("password1"));
      $email = $form->getValue("email");
      DB\insertOne('users', array('username' => $username, 'password' => $passwordHashed,
                                  'email' => $email, 'created_at' => new DateTime('now')));
      $user = User::loadFromUsername($username);
      $_SESSION['Zend_Auth']['storage'] = $user;
      DB\insertOne('subscriptions', array('user_id' => $user->id,
        'chipin' => $form->getValue('chipin-updates') ? 1 : 0,
        'memorydealers' => $form->getValue('memorydealers-updates') ? 1 : 0));
      return $this->redirect("/dashboard/index/");
    } else {
      $captcha = $this->getCaptchaTool();
      return $this->render('account/signup.php', 'Signup',
        array('form' => $form, 'captcha' => $captcha));
    }
  }

  function signin() {
    $failure = false;
    if ($this->isPostRequest()) {
      $username = $_POST['username'];
      $password = $_POST['password'];
      $existingHash = $this->getStoredHashForUser($username);
      $isGood = Passwords\isValid($password, $existingHash);
      if ($isGood) {

        /*
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName("users");
        $authAdapter->setIdentityColumn("username");
        $authAdapter->setCredentialColumn("password");

        $authAdapter->setIdentity($username);
        $authAdapter->setCredential($this->passwordHash($password));

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);*/

        $user = User::loadFromUsername($username);

        /*$auth = Zend_Auth::getInstance();
        $authStorage = $auth->getStorage();
        $authStorage->write($user);*/

        $_SESSION['Zend_Auth']['storage'] = $user;
        return $this->redirect('/dashboard/');
      } else {
        $failure = true;
      }
    }
    return $this->render('account/signin.php', null, array('failure' => $failure));
  }

  function changePassword() {
    $user = $this->user;
    $form = new F\Form('post');
    $passwordField = F\newPasswordField('password', 'Password');
    $confirmPassField = new PasswordConfirmField('confirm-password', 'Re-enter password');
    $form->addSection('change-password', array(
      F\newPasswordField('current-password', 'Current Password')->
        required('Please authenticate by entering your current password.')->
        addValidation(function($pass) use($user) {
          return Passwords\isValid($pass, $user->passwordEncrypted) ?
            array() : array("Your current password is incorrect!");
        }),
      $passwordField->
        addValidation(function($pass) {
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

  private function getStoredHashForUser($username) {
    $rows = DB\select('password', 'users', 'username = ?', array($username));
    return count($rows) == 0 ? null : $rows[0]['password'];
  }

  private function getCaptchaTool() {
    $localPathToCaptchas = $this->docRoot() . '/' . $this->captchasDir;
    if (!is_dir($localPathToCaptchas)) {
      throw new Exception("Directory for storing captcha images does not exist");
    } else if (!is_writable($localPathToCaptchas)) {
      throw new Exception("Directory for storing captchas is not writable by application");
    }
    $thisURL = CurrentRequest\getURL();
    $captchasURL = URL\constructUrlFromRelativeLocation($thisURL, '/' . $this->captchasDir);
    $c = new HackedCaptchaTool;
    $c->setWordlen(4);
    $c->setWidth(100);
    $c->setHeight(41);
    $c->setFont($this->docRoot() . '/fonts/arial.ttf');
    // $c->setFont('fonts/arial.ttf');
    //'timeout' => 300,
    $c->setImgDir($this->docRoot() . '/' . $this->captchasDir);
    // $c->setImgDir($this->captchasDir);
    $c->setImgUrl($captchasURL);
    $c->generate();
    $_SESSION['captchas'][$c->getId()] = $c->getWord();
    return $c;
  }

  private $captchasDir = 'images/captcha';
}

class PasswordConfirmField extends F\PasswordField {
  protected function validateWhenNotEmpty(Array $submittedValues, $pass2) {
    return ($submittedValues['password'] === $pass2) ?
      array() : array('The two passwords entered do not match!');
  }
}

/**
 * In order to avoid Zend Framework's hokey session crap trying to start a new
 * PHP session, we override a few methods...
 */
class HackedCaptchaTool extends Zend_Captcha_Image {
  public function getSession() {
    $s = new stdClass;
    $s->word = null;
    return $s;
  }
}

return 'AccountController';
