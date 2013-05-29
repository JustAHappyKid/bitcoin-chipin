<?php

class AboutController extends \Chipin\WebFramework\Controller {
  function index() {
    return $this->render('about/about-us.php', 'AboutUsPage');
  }
}

return 'AboutController';
