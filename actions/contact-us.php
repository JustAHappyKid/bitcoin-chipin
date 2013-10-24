<?php

require_once 'spare-parts/email.php';       # sendTextEmail
require_once 'spare-parts/validation.php';  # isValidEmailAddr

use \SpareParts\Validation as V;

class ContactUsController extends \Chipin\WebFramework\Controller {

  private $sendCommentsTo = 'alex.khajehtoorian@gmail.com, chris@easyweaze.net';

  function index() {
    if ($this->isPostRequest() && V\isValidEmailAddr($_POST['email'])) {
      $c = $_POST['comments'];
      if (strlen($c) < 25 || count(explode(' ', $c)) < 5) {
        return $this->render('contact-us/form.php', array('commentTooShort' => true));
      } else {
        sendTextEmail('webmaster@bitcoinchipin.com', $this->sendCommentsTo,
          'A BitcoinChipin.com inquiry',
          "Someone has submitted the Contact form at BitcoinChipin.com. Details follow...\n\n" .
          "Name: {$_POST['name']}\n" .
          "Email: {$_POST['email']}\n" .
          "Comments:\n" . $_POST['comments']);
        return $this->render('contact-us/submitted.php');
      }
    } else {
      return $this->render('contact-us/form.php');
    }
  }
}

return 'ContactUsController';
