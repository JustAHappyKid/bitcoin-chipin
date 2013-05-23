<?php

abstract class Layout {

  abstract function innerContent();
  abstract function userIsAuthenticated();

  function content() {
    ?><!DOCTYPE html>
    <html lang="en">
      <head>

        <? /* TODO: Need to include Analytics snippet and what else?? */ ?>

        <? /* require dirname(dirname(__FILE__)) . '/public/application/layouts/scripts/head-common.phtml'; */ ?>

        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>BitcoinChipin.com</title>

        <? $themeDir = '/measure-theme'; ?>
        <link rel="stylesheet" type="text/css" href="/measure-theme/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="/measure-theme/css/theme.css" />
        <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900,300italic,400italic,700italic,900italic'
              rel='stylesheet' type='text/css' />

        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script src="<?= $themeDir ?>/js/bootstrap.min.js"></script>
        <script src="<?= $themeDir ?>/js/theme.js"></script>

      </head>

      <body>

        <? if ($this->userIsAuthenticated()): ?>
          <div id="nav">
            <div class="container">
              <div class="nav-collapse">
                <ul class="nav">
                  <li class="nav-icon">
                    <a href="<?=PATH;?>">
                      <i class="icon-home"></i>
                      <span>Home</span>
                    </a>
                  </li>
                </ul>
                <ul class="nav pull-right">
                  <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                      <i class="icon-external-link"></i>
                      Account
                      <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a href="<?=PATH.'account/changepassword/';?>">Change password</a></li>
                      <li><a href="<?=PATH.'signin/signout/';?>">Sign out</a></li>
                    </ul>
                  </li>
                </ul>
              </div> <!-- /.nav-collapse -->
            </div> <!-- /.container -->
          </div> <!-- /#nav -->
        <? endif; ?>

        <div class="navbar navbar-fixed-top">
          <div class="navbar-inner">
            <div class="container">
              <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </a>
              <a class="brand" href="index.html" style="min-height: 35px;">
                <!-- <img src="img/logo.png" alt="logo" /> -->
              </a>
              <div class="nav-collapse collapse">
                <ul class="nav pull-right">
                  <li><a href="/about/">About us</a></li>
                  <li><a href="/about/contact">Contact us</a></li>
                  <!-- <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                          External Pages
                          <b class="caret"></b>
                      </a>
                      <ul class="dropdown-menu">
                          <li><a href="features.html">Features</a></li>
                          <li><a href="pricing.html">Pricing</a></li>
                          <li><a href="portfolio.html">Portfolio</a></li>
                          <li><a href="coming-soon.html">Coming Soon</a></li>
                          <li><a href="aboutus.html">About us</a></li>
                          <li><a href="contact.html">Contact us</a></li>
                          <li><a href="faq.html">FAQ</a></li>
                      </ul>
                  </li> -->
                  <li><a class="btn-header" href="/account/signup">Sign up</a></li>
                  <li><a class="btn-header" href="/signin/index/">Sign in</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <?= $this->innerContent(); ?>

      </body>
    </html>
  <? }
}
