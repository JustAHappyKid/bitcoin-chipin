<?php

require_once dirname(dirname(__FILE__)) . '/layout.php';

abstract class AboutPageLayout extends Layout {

  function htmlHeadExtras() { ?>
    <link rel="stylesheet" type="text/css" href="/measure-theme/css/external-pages.css" />
    <link rel="stylesheet" type="text/css" href="/css/components/about.css" />
  <?php }

  function pageHeader($content) { ?>
    <h2 class="section_header">
      <hr class="left visible-desktop" />
      <span><?= htmlspecialchars($content) ?></span>
      <hr class="right visible-desktop" />
    </h2>
  <?php }
}
