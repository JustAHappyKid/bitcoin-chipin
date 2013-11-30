<?php

require_once dirname(__FILE__) . '/layout.php';
require_once 'chipin/widgets.php';

use \Chipin\Widgets\Widget;

class StepThree extends WidgetWizLayout {

  protected function stepNumber() { return 3; }
  protected function showPreview() { return true; }
  protected function buttons() {
    //return '<a href="/dashboard/">Back to my dashboard</a>';
    $wid = $this->widget->id;
    return '
      <a class="btn btn-large" href="/widget-wiz/step-two?w=' . $wid . '">Previous Step</a>
      <a class="btn btn-large btn-info" href="/dashboard/">Proceed to Dashboard</a>';
  }

  protected function allTheShitWithForm() { ?>

    <div id="widgetForm">
      <?= $this->htmlToEmbedAndPreviewFrame() ?>
      <div class="form-actions">
        <?= $this->buttons() ?>
      </div>
    </div>

    <? if (empty($this->user) || empty($this->user->email)) { ?>
      <div id="step-4">
        <h3>Step 4: Create an account to manage your widgets</h3>
        <p>It's not a requirement, but if you create an account you can always come back
          to modify your widget or create new ones.</p>
        <div style="width: 75%; margin: 35px auto;">
          <form action="/account/signup" method="POST" class="form-horizontal" id="signup-form">
            <div class="control-group">
              <label class="control-label" for="email">Email Address</label>
              <div class="controls">
                <input type="text" id="email" name="email" value="" class="login" />
              </div>
            </div>
            <div class="control-group">
              <label for="username" class="control-label">Username</label>
              <div class="controls">
                <input type="text" id="username" name="username" value=""
                       placeholder="(Optional)" class="login" />
                <div class="help-block">
                  With a username, we'll give you a unique URL where others
                  can find all your widgets &mdash; for example,
                  <code><?= $_SERVER['HTTP_HOST'] ?>/widgets/u/my-great-username/</code>.</div>
              </div>
            </div>
            <div class="control-group">
              <label for="password" class="control-label">Password</label>
              <div class="controls">
                <input type="password" id="password" name="password1" />
              </div>
            </div>
            <div class="control-group">
              <label for="confirm-password" class="control-label">Confirm Password</label>
              <div class="controls">
                <input type="password" id="confirm-password" name="password2" class="login" />
              </div>
            </div>
            <div class="form-actions" style="text-align: right; background-color: inherit;">
              <button class="button btn btn-primary btn-large">Register</button>
              <!-- <input type="submit" class="btn btn-large" value="Register" /> -->
            </div>
          </form>
        </div>
      </div>
    <? } ?>

  <? }

  //function contentForThisStep() {
  protected function htmlToEmbedAndPreviewFrame() { ?>
    <div id="step-3">
      <h3>Step 3: You're done! Just copy and paste the below code into your website.</h3>
      <div>
        <div style="float: left;"> <!-- div for form inputs -->
          <textarea style="height: 110px; width: 400px;" class="input-large"
                    id="javascript-version" rows="3"
            ><?= htmlspecialchars($this->iframeForWidget($this->widget)) ?></textarea>
        </div>
        <div style="float: left; margin: 0 0 25px 75px;">
          <?= $this->iframeForWidget($this->widget); ?>
        </div>
        <div class="clearfix"> </div>
      </div>
    </div> <!-- /step -->
  <? }

  protected function contentForThisStep() {}

  private function iframeForWidget(Widget $widget) {
    return $this->widgetIframe($widget);
  }
}
