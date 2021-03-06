<?php

namespace Chipin\Test;

require_once dirname(__FILE__) . '/harness.php';

/**
 * In this test-class/file we're just "touching" the (primarily) static pages, such
 * as the homepage, to make sure they at least render without anything blowing up.
 */
class StaticPagesTests extends WebappTestingHarness {

  function testThemPages() {
    $this->createHomepageWidgets();
    $this->get('/');
    $this->get('/about/');
    $this->get('/about/learn');
    $this->get('/about/partners');
    $this->get('/about/faq');
    $this->get('/about/privacy-policy');
    $this->get('/about/terms');
  }
}
