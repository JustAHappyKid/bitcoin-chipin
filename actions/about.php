<?php

class AboutController extends \Chipin\WebFramework\Controller {

  function index() {
    return $this->render('about/about-us.php', 'AboutUsPage');
  }

  function faq() {
    return $this->render('about/faq.php', 'FaqPage');
  }

  function privacyPolicy() {
    return $this->render('about/privacy-policy.php', 'PrivacyPolicyPage');
  }

  function terms() {
    return $this->render('about/terms.php', 'TermsPage');
  }
  
  function resources() {
    return $this->render('about/resources.php', 'ResourcesPage');
  }
}

return 'AboutController';
