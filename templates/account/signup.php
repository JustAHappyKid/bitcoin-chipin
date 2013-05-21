<?php

require_once dirname(dirname(__FILE__)) . '/layout.php';

class Signup extends Layout {

  function userIsAuthenticated() { return false; }

  function innerContent() { ?>

    <link href="/css/components/signin.css" rel="stylesheet" type="text/css" />
    <?= $this->validationJavascript(); ?>

    <style type="text/css">
      #captcha-input{
        width: 170px;
        float: left;
        margin-left: 30px;
      }

      .field img{
        float: left;
        height: 40px;
      }

      body {
        background: white url("<?=PATH;?>img/bkg_body.gif") 50% 0 repeat-x;
      }

      .page {
        background: url("<?=PATH;?>img/bkg_top_bar.png") no-repeat scroll 0 0 white;
        box-shadow: 0 1px 5px #AAA;
        margin: 20px auto;
        padding: 17px;
        width: 979px;
        min-height: 600px;
      }
    </style>

    <div class="page">
      <div class="account-container register">
        <div class="bitcoinchipin-logo">
          <img src="<?=PATH?>img/logo.jpg" alt="Bitcoin Chipin" />
        </div>
        <div class="content clearfix">
          <form action="<?=PATH;?>account/signup" onsubmit="return validateForm();" method="post">
            <h1>Create Your Account</h1>			
            <? /* <div class="login-social" style="display: none;">
              <p>Sign in using social network:</p>
              <div class="twitter">
                <a href="#" class="btn_1">Login with Twitter</a>				
              </div>
              <div class="fb">
                <a href="#" class="btn_2">Login with Facebook</a>				
              </div>
            </div> */ ?>
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
            <?php if (isset($this->captchaIncorrect) && $this->captchaIncorrect): ?>
              <div class="alert alert-error">
                <a class="close" data-dismiss="alert" href="#">×</a>
                <h4 class="alert-heading">Error!</h4>
                Captcha code incorrectly identified. Please try again!
              </div>
            <?php endif; ?>
            <div class="login-fields">
              <p>Create your free account:</p>
              <div class="field">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="" placeholder="Username" class="login" />
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
              <div class="field">
                <?= $this->captcha->render() ?>
                <input type="hidden" name="captcha-id" id="captcha-id"
                       value="<?= $this->captcha->getId() ?>" />
                <input type="text" name="captcha-input" id="captcha-input" value=""
                       placeholder="Captcha">
                <div style="clear: both;"> </div>
              </div>
            </div> <!-- /login-fields -->
            <div class="login-actions">
              <? /* <span class="login-checkbox" style="display: none;">
                <input id="Field" name="Field" type="checkbox" class="field login-checkbox"
                       value="First Choice" tabindex="4" />
                <label class="choice" for="Field">I have read and agree with the Terms of Use.</label>
              </span> */ ?>
              <button class="button btn btn-primary btn-large">Register</button>
            </div> <!-- .login-actions -->
          </form>
        </div> <!-- /content -->
        
      </div> <!-- /account-container -->

      <!-- Text Under Box -->
      <div class="login-extra">
        Already have an account? <a href="<?=PATH;?>signin/index/">Sign in here.</a>
      </div> <!-- /login-extra -->

    </div>

  <? }

  function validationJavascript() { ?>
    <script type="text/javascript">
      function validateForm() {
        if ($("#username").val() == ""){
          $("#username-error").show();
          return false;
        } else {
          if ($("#username-error").size() != 0) {
            $("#username-error").hide();
          }
        }        
        if ($("#password").val() == '' || $("#password").val() != $("#confirm_password").val()) {
          $("#password-error").show();
          return false;
        } else {
          if ($("#password-error").size() >= 6 && $("#password-error").size() <= 22) {
            $("#password-error").hide();
          }
        }
        return true;
      }
    </script>
  <? }
}
