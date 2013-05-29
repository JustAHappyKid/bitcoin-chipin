<?php

class AboutController extends \Chipin\WebFramework\Controller {
  function index() {
    return $this->render('about/about-us.php', 'AboutUsPage');
  }
  function faq() {
    return $this->render('about/faq.php', 'FaqPage');
  }
}

return 'AboutController';
