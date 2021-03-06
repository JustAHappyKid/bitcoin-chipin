<?php

require_once dirname(dirname(__FILE__)) . '/layout.php';

use \SpareParts\Webapp\Forms\Form;

class Signup extends Layout {

  /** @var Form */
  public $form;

  function htmlHeadExtras() { ?>
    <link rel="stylesheet" type="text/css" href="/measure-theme/css/signup.css" />
    <link rel="stylesheet" type="text/css" href="/css/components/signup.css" />
  <?php }

  function innerContent() { ?>

    <?= $this->validationJavascript(); ?>

    <div id="box_sign">
      <div class="container">
        <div class="span12 box_wrapper">
          <div class="span12 box">
            <div class="head">
              <div class="bitcoinchipin-logo">
                <img src="<?=PATH?>img/logo.jpg" alt="Bitcoin Chipin" />
              </div>
              <h4>Let's get you an account</h4>
            </div>
            <div class="form">
              <form id="signup-form" action="<?=PATH;?>account/signup" method="post"
                    onsubmit="return validateForm();">
                <?php if ($this->form->hasErrors()): ?>
                  <div id="form-error" class="alert alert-error">
                    <a class="close" data-dismiss="alert" href="#">×</a>
                    <h4 class="alert-heading">Error!</h4>
                    <?= current($this->form->getErrors()) ?>
                  </div>
                <?php endif; ?>
                <div id="password-error" class="alert alert-error" style="display: none;">
                  <a class="close" data-dismiss="alert" href="#">×</a>
                  <h4 class="alert-heading">Error!</h4>
                  Passwords don't match.
                </div>
                <div id="username-error" class="alert alert-error" style="display: none;">
                  <a class="close" data-dismiss="alert" href="#">×</a>
                  <h4 class="alert-heading">Error!</h4>
                  You must provide a username!
                </div>
                <?php /* if (isset($this->captchaIncorrect) && $this->captchaIncorrect): ?>
                  <div class="alert alert-error">
                    <a class="close" data-dismiss="alert" href="#">×</a>
                    <h4 class="alert-heading">Error!</h4>
                    Captcha code incorrectly identified. Please try again!
                  </div>
                <?php endif; */ ?>
                <div class="account-fields">
                  <div class="field">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value=""
                          placeholder="Username" class="login" />
                  </div> <!-- /field -->
                  <div class="field">
                    <label for="email">Email Address:</label>
                    <input type="text" id="email" name="email" value="" placeholder="Email" class="login"/>
                  </div> <!-- /field -->
                  <div class="field">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password1" value=""
                          placeholder="Password" class="login"/>
                  </div> <!-- /field -->
                  <div class="field">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="password2" value=""
                          placeholder="Confirm Password" class="login"/>
                  </div> <!-- /field -->
                  <?php /* <div class="field captcha-field">
                    <?= $this->captcha->render() ?>
                    <input type="hidden" name="captcha-id" id="captcha-id"
                          value="<?= $this->captcha->getId() ?>" />
                    <input type="text" name="captcha-input" id="captcha-input" value=""
                          placeholder="Captcha">
                    <div style="clear: both;"> </div>
                  </div> */ ?>
                  <div class="field newsletter-options">
                    <label for="chipin-updates">
                      <input type="checkbox" id="chipin-updates" name="chipin-updates" />
                      Receive news updates from BitcoinChipin.com
                    </label>
                    <label for="memorydealers-updates">
                      <input type="checkbox" id="memorydealers-updates"
                             name="memorydealers-updates" />
                      Receive news updates from MemoryDealers.com
                    </label>
                  </div>
                </div> <!-- /account-fields -->
                <div class="login-actions">
                  <?php /* <span class="login-checkbox" style="display: none;">
                    <input id="Field" name="Field" type="checkbox" class="field login-checkbox"
                          value="First Choice" tabindex="4" />
                    <label class="choice" for="Field">I have read and agree with the Terms of Use.</label>
                  </span> */ ?>
                  <!-- <button class="button btn btn-primary btn-large">Register</button> -->
                  <input type="submit" class="btn btn-large" value="Register" />
                </div> <!-- /.login-actions -->
              </form>
            </div> <!-- /.form -->
          </div> <!-- /.box -->

          <p class="already login-extra">
            Already have an account? <a href="<?=PATH;?>account/signin">Sign in here.</a>
          </p>

        </div> <!-- /.box_wrapper -->
      </div> <!-- /.container -->
    </div> <!-- /#box_sign -->

  <?php }

  function validationJavascript() { ?>
    <script type="text/javascript">
      function validateForm() {
        var usernameError = $("#username-error");
        if ($("#username").val() == "") {
          usernameError.show();
          return false;
        } else {
          if (usernameError.size() != 0) {
            usernameError.hide();
          }
        }
        var passInput = $("#password");
        var passError = $("#password-error");
        if (passInput.val() == '' || passInput.val() != $("#confirm_password").val()) {
          passError.show();
          return false;
        } else {
          if (passError.size() >= 6 && passError.size() <= 22) {
            passError.hide();
          }
        }
        return true;
      }
    </script>
  <?php }
}
