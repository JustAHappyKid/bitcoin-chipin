<?php

require_once 'spare-parts/email.php';       # sendTextEmail
require_once 'spare-parts/validation.php';  # isValidEmailAddr

use \SpareParts\Validation as V;

class ContactUsController extends \Chipin\WebFramework\Controller {

  private $sendCommentsTo = 'alex.khajehtoorian@gmail.com, chris@easyweaze.net';

  function index() {
    $name = at($_POST, 'name');
    $email = at($_POST, 'email');
    $c = at($_POST, 'comments');
    if ($this->isPostRequest() && V\isValidEmailAddr($email)) {
      if (strlen($c) < 25 || count(explode(' ', $c)) < 5) {
        return $this->render('contact-us/form.php', array('commentTooShort' => true));
      } else {
        sendTextEmail('webmaster@bitcoinchipin.com', $this->sendCommentsTo,
          'A BitcoinChipin.com inquiry',
          "Someone has submitted the Contact form at BitcoinChipin.com. Details follow...\n\n" .
          "Name: $name\n" .
          "Email: $email\n\n" .
          "Comments:\n" . $c);
        return $this->render('contact-us/submitted.php');
      }
    } else {
      return $this->render('contact-us/form.php');
    }
  }
}

return 'ContactUsController';
