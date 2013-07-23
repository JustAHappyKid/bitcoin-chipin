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
        <link href='//fonts.googleapis.com/css?family=Lato:300,400,700,900,300italic,400italic,700italic,900italic'
              rel='stylesheet' type='text/css' />

        <? /* TODO: Only include these when needed (i.e., for Dashboard and/or Widget Wizard) */ ?>
        <link rel="stylesheet" type="text/css" href="/css/components/dashboard.css" />
        <link rel="stylesheet" type="text/css" href="/css/components/widget-wiz.css" />

        <script src="/js/jquery-latest.js"></script>
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
        <? require dirname(__FILE__) . '/nav-bar.php'; ?>
        <?= $this->innerContent(); ?>
        <? require dirname(__FILE__) . '/footer.php'; ?>
      </body>
    </html>
  <? }
}
