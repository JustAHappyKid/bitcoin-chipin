<?php

abstract class Layout {

  abstract function userIsAuthenticated();
  abstract function innerContent();

  function htmlHeadExtras() {}

  function content() {
    ?><!DOCTYPE html>
    <html lang="en">
      <head>

        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>BitcoinChipin.com</title>

        <? $themeDir = '/measure-theme'; ?>
        <link rel="stylesheet" type="text/css" href="/measure-theme/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="/measure-theme/css/theme.css" />
        <link rel="stylesheet" type="text/css" href="/css/measure-theme-overrides.css" />
        <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900,300italic,400italic,700italic,900italic'
              rel='stylesheet' type='text/css' />
        </style>

        <? /* TODO: only include this for necessary pages (Dashboard and Widget Wizard) */ ?>
        <link rel="stylesheet" type="text/css" href="/css/components/dashboard.css" />

        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script src="<?= $themeDir ?>/js/bootstrap.min.js"></script>
        <script src="<?= $themeDir ?>/js/theme.js"></script>

        <? $this->htmlHeadExtras(); ?>

        <? if (APPLICATION_ENV == 'production'): ?>
          <script type="text/javascript">
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-39873638-1']);
            _gaq.push(['_trackPageview']);
            (function() {
              var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
              ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') +
                '.google-analytics.com/ga.js';
              var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();
          </script>
        <? endif; ?>

      </head>

      <body>

        <? if ($this->userIsAuthenticated()): ?>
<? /*
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
*/ ?>
        <? endif; ?>

        <? require dirname(__FILE__) . '/nav-bar.php'; ?>

        <?= $this->innerContent(); ?>

        <div id="footer">
          <div class="container">
            <div class="row">
              <div class="span8">
                <h3>Bitcoin resources</h3>
                  <div class="blog_post">
                    <div class="row">
                      <div class="span2">
                        <a href="bitcoins.html">
                          <img src="img/bitcoin.png" alt="post image" class="img-circle" />
                        </a>
                      </div>
                      <div class="span4">
                        <a href="http://www.weusecoins.com/" class="title">WeUseCoins</a>
                        <div class="intro">
                          "Our mission is to support Bitcoin by making it easier for
                          new users to get started."
                        </div>
                      </div>
        							<div class="span4">
                        <a href="http://bitcoin.cbtnuggets.com/" class="title">
                          Bitcoin Training Series from CBT Nuggets
                        </a>
                        <div class="intro">
                          "Learn what it is, how it works, and how you can get started
                          using it in this groundbreaking CBT Nuggets series with trainer
                          Keith Barker"
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="span4 contact_us">
                  <h3>Contact Us</h3>
                  <form class="form-horizontal">
                    <div class="field">
                      <div class="label_field">
                        <label for="inputName">Name</label>
                      </div>
                      <input type="text" id="inputName" />
                    </div>
                    <div class="field">
                      <div class="label_field">
                        <label for="inputEmail">Email</label>
                      </div>
                      <input type="text" id="inputEmail" />
                    </div>
                    <div class="field">
                      <div class="label_field">
                        <label for="inputCity">City</label>
                      </div>
                      <input type="text" id="inputCity" />
                    </div>
                    <div class="field">
                      <div class="label_field">
                        <label for="inputComment">Comments</label>
                      </div>
                      <textarea name="comments"></textarea>
                    </div>
                    <button type="submit" class="btn">Send</button>
                  </form>
                </div>
              </div> <!-- /.contact_us -->
            <hr/>
            <div class="row copyright">
              <div class="span5">
                <h3>Memorydealers</h3>
                <a href="#" class="social fb"><i class="i_facebook"></i></a>
                <a href="#" class="social tw"><i class="i_twitter"></i></a>
                <a href="#" class="social yt"><i class="i_youtube"></i></a>
              </div>
              <div class="span2 offset5 copy">
                <p>&#169; 2013 - Bitcoinchipin.com</p>
              </div>
            </div>
          </div>
        </div>

      </body>
    </html>
  <? }
}
