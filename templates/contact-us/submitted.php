<?php

require_once dirname(dirname(__FILE__)) . '/layout.php';

class ContactUsSubmittedPage extends Layout {
  function innerContent() { ?>
    <div id="contact-us" class="container">
      <h2 class="section_header">Contact Us</h2>
      <div class="row">
        <div class="offset3 span6">
          <div class="alert alert-success">
            <strong>Thanks for writing!</strong> We've received your inquiry and will try to get
            back to you soon if a response is needed.
          </div>
        </div>
      </div>
    </div>
  <? }
}
