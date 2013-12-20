<?php

require_once dirname(dirname(__FILE__)) . '/layout.php';

class ContactUsPage extends Layout {
  function innerContent() { ?>
    <div id="contact-us" class="container">
      <h2 class="section_header">Contact Us</h2>
      <div class="row">
        <div class="offset3 span6">
          <? if (isset($this->errorMessage) && $this->errorMessage) { ?>
            <div class="alert alert-error">
              <?= $this->errorMessage ?>
            </div>
          <? } ?>
          <form method="post" action="/contact-us/" class="form-horizontal">
            <div class="control-group">
              <label class="control-label" for="inputName">Your Name</label>
              <div class="controls">
                <input type="text" id="inputName" name="name" />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="inputEmail">Email</label>
              <div class="controls">
                <input type="text" id="inputEmail" name="email" />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="inputComment">Comments</label>
              <div class="controls">
                <textarea id="inputComment" name="comments"></textarea>
              </div>
            </div>
            <div style="text-align: right;">
              <button type="submit" class="btn">Send</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <? }
}
