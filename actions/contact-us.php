<?php

require_once 'spare-parts/email.php';       # sendTextEmail
require_once 'spare-parts/validation.php';  # isValidEmailAddr

use \SpareParts\Validation as V;

class ContactUsController extends \Chipin\WebFramework\Controller {

  private $sendCommentsTo = 'alex.khajehtoorian@gmail.com, chris@easyweaze.net';

  function index() {
    if ($this->isPostRequest() && V\isValidEmailAddr($_POST['email']) &&
        strlen($_POST['comments']) > 0) {
      sendTextEmail('webmaster@bitcoinchipin.com', $this->sendCommentsTo,
        'A BitcoinChipin.com inquiry',
        "Someone named '{$_POST['name']}' has filled in the Contact Us form at\n" .
        "BitcoinChipin.com. His/her comments follow...\n\n----------\n\n" . $_POST['comments']);
      return $this->render('contact-us/submitted.php', 'ContactUsSubmittedPage');
    } else {
      return $this->render('contact-us/form.php', 'ContactUsPage');
    }
  }
}

return 'ContactUsController';
