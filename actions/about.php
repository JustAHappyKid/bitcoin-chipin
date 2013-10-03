<?php

class AboutController extends \Chipin\WebFramework\Controller {

  function index() {
    return $this->render('about/about-us.php');
  }

  function faq() {
    return $this->render('about/faq.php');
  }

  function privacyPolicy() {
    return $this->render('about/privacy-policy.php');
  }

  function terms() {
    return $this->render('about/terms.php');
  }

  function partners() {
    return $this->render('about/partners.php');
  }

  function learn() {
    return $this->render('about/learn.php');
  }
}

return 'AboutController';
