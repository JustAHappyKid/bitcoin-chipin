<?php

namespace Chipin\Test;

/**
 * In this test-class/file we're just "touching" the (primarily) static pages, such
 * as the homepage, to make sure they at least render without anything blowing up.
 */
class StaticPagesTests extends WebappTestingHarness {

  function testThemPages() {
    $this->get('/');
    $this->get('/about/');
  }
}
