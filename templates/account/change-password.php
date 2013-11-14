<?php

require_once dirname(dirname(__FILE__)) . '/layout.php';

class ChangePassword extends Layout {

  function htmlHeadExtras() { ?>
    <link href="<?= PATH ?>css/components/signin.css" rel="stylesheet" type="text/css" />
  <? }

  function innerContent() { ?>

    <?= $this->validationJavascript(); ?>

    <div id="content"> <div class="container">
      <div class="account-container login">
        <div class="content clearfix">

          <? if ($this->newPassword): ?>
            <div class="alert alert-success">
              Great &mdash; we've reset your password to
              <strong><?= $this->newPassword ?></strong>...
              but please use the form below to change it to something you'll remember!
            </div>
          <? endif; ?>

          <form id="change-password-form" action="<?=PATH;?>account/change-password/"
                method="post" onsubmit="return validateForm();">

            <h1>Change password</h1>

            <div id="password-error" class="alert alert-error" style="display: none;">
              <a class="close" data-dismiss="alert" href="#">×</a>
              <h4 class="alert-heading">Error!</h4>
              Passwords don't match.
            </div>

            <? if ($this->form->hasErrors()): ?>
              <div id="password-error" class="alert alert-error">
                <a class="close" data-dismiss="alert" href="#">×</a>
                <h4 class="alert-heading">Error!</h4>
                <?= current($this->form->getErrors()) ?>
              </div>
            <? endif; ?>

            <? if (isset($this->success) && $this->success): ?>
              <div class="alert alert-success">
                <a class="close" data-dismiss="alert" href="#">×</a>
                <h4 class="alert-heading">Success!</h4>
                Password was changed successfully.
              </div>
            <? endif; ?>

            <div class="account-fields" style="margin-top: 25px;">

              <div class="field">
                <label for="current-password">Current password:</label>
                <input type="password" id="current-password" name="current-password"
                       value="<?= $this->newPassword ? $this->newPassword : '' ?>"
                       placeholder="Your current password" class="login password-field"/>
              </div>

              <div class="field">
                <label for="password">New password:</label>
                <input type="password" id="password" name="password" value=""
                       placeholder="New password" class="login password-field"/>
              </div>

              <div class="field">
                <label for="confirm-password">Confirm new assword:</label>
                <input type="password" id="confirm-password" name="confirm-password" value=""
                       placeholder="Confirm new password" class="login password-field"/>
              </div>

            </div> <!-- /account-fields -->

            <div class="login-actions">
              <button class="button btn btn-secondary btn-large"
                      style="margin-left: 260px; float: none !important;">Save</button>
            </div>

          </form>

        </div> <!-- /.content -->
      </div> <!-- /.account-container -->
    </div> </div> <!-- /#content -->

  <? }

  function validationJavascript() { ?>
    <script type="text/javascript">
      function validateForm() {
        if ($("#password").val() == '' || $("#password").val() != $("#confirm-password").val()) {
          $("#password-error").show();
          return false;
        } else {
          if ($("#password-error").size() != 0) {
            $("#password-error").hide();
          }
        }
      }
    </script>
  <? }
}
