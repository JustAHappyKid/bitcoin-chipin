<?php

require_once dirname(dirname(__FILE__)) . '/layout.php';

class AboutUsPage extends Layout {

  function userIsAuthenticated() { return false; }

  function htmlHeadExtras() { ?>
    <link rel="stylesheet" type="text/css" href="/measure-theme/css/external-pages.css" />
  <? }

  function innerContent() { ?>
    <div id="about" class="about_page">
      <div class="container">
        <h2 class="section_header">
          <hr class="left visible-desktop" />
          <span>About Us</span>
          <hr class="right visible-desktop" />
        </h2>
        <div class="row">
          <div class="span12">
            <h3 class="intro">
              TODO, put real content here.
            </h3>
          </div>
        </div>
        <div class="row">
          <div class="span6">
            <p>Here are many variations of passages of Lorem Ipsum available, but the
              majority have suffered alteration in some form, by injected humour, or
              randomised words which.</p>
            <p>These don't look even slightly believable, do they? If you are going to
              use a passage of Lorem Ipsum,
              you need to be sure there isn't anything embarrassing hidden in the
              middle of text.</p>
          </div>
          <div class="span6">
            <p>Here are many variations of passages of Lorem Ipsum available, but the
              majority have suffered alteration in some form, by injected humour, or
              randomised words which.</p>
            <p>These don't look even slightly believable, do they? If you are going to
              use a passage of Lorem Ipsum,
              you need to be sure there isn't anything embarrassing hidden in the
              middle of text.</p>
          </div>
        </div>
      </div>
    </div>
  <? }
}
