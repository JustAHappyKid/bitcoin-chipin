<?php

require_once dirname(dirname(__FILE__)) . '/layout.php';

class SigninPage extends Layout {

  function userIsAuthenticated() { return false; }

  function htmlHeadExtras() { ?>

    <link href="/css/components/signin.css" rel="stylesheet" type="text/css" />

    <script type="text/javascript">

      function checkTerms() {
        $("#login-error").hide();
        if ($("#username").val() == "" || $("#password").val() == "") {
          $("#username-password-error").show();
          return false;
        }
        return true;
      }

    </script>
  <? }

  function innerContent() { ?>

    <div class="account-container login">
      <div class="bitcoinchipin-logo">
        <img src="<?=PATH;?>img/logo.jpg" />
      </div>
      <div class="content clearfix">
        <!--suppress HtmlUnknownTarget -->
        <form id="signin-form" action="/account/signin" method="post"
              onsubmit="return checkTerms();">

          <h1>Sign In</h1>
          <div id="username-password-error" class="alert alert-error" style="display: none;">
            <a class="close" data-dismiss="alert" href="#">×</a>
            <h4 class="alert-heading">Error!</h4>
            Invalid username or password!
          </div>
          <?php if (isset($this->success) && $this->success):?>
            <div class="alert alert-success">
              <a class="close" data-dismiss="alert" href="#">×</a>
              <h4 class="alert-heading">Check your inbox!</h4>
              A confirmation link has been sent to your email address.
              Check there for further instructions!
            </div>
          <?php endif;?>
          <?php if (isset($this->failure) && $this->failure): ?>
            <div id="login-error" class="alert alert-error">
              <a class="close" data-dismiss="alert" href="#">×</a>
              <h4 class="alert-heading">Error!</h4>
              Invalid username or password!
            </div>
          <?php endif; ?>
          <?php if (isset($this->passwordReset) && $this->passwordReset): ?>
            <div class="alert alert-success">
              <h4 class="alert-heading">Success!</h4>
              <a class="close" data-dismiss="alert" href="#">×</a>
              Your password has been updated successfully!
            </div>
          <?php endif; ?>
          <div class="login-fields">

            <p>Sign in using your registered account:</p>

            <div class="field">
              <label for="username">Username:</label>
              <input type="text" id="username" name="username" value="" placeholder="Username"
                     class="login username-field" />
            </div> <!-- /field -->

            <div class="field">
              <label for="password">Password:</label>
              <input type="password" id="password" name="password" value="" placeholder="Password"
                     class="login password-field"/>
            </div> <!-- /password -->

          </div> <!-- /login-fields -->

          <div class="login-actions">

            <button class="button btn btn-secondary btn-large">Sign In</button>

          </div> <!-- .actions -->

        </form>

      </div> <!-- /content -->

    </div> <!-- /account-container -->

    <div class="login-extra">
      Don't have an account? <a href="<?=PATH;?>account/signup">Sign up</a>.<br/>
      <a href="<?=PATH;?>account/lost-pass">Forget your password?</a>
    </div>
  <? }
}
