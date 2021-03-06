<?php

require_once 'spare-parts/template/Renderable.php';
require_once 'chipin/widgets.php';

use \Chipin\Widgets\Widget;

abstract class Layout implements \SpareParts\Template\Renderable {

  protected $__vars;

  function __render($vars) {
    foreach ($vars as $n => $v) {
      $this->$n = $v;
    }
    $this->__vars = $vars;
    return $this->content();
  }

  abstract function innerContent();

  protected function htmlHeadExtras() {}

  function content() {
    ?><!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>BitcoinChipin.com</title>
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

        <?php $themeDir = '/measure-theme'; ?>
        <link rel="stylesheet" type="text/css" href="/measure-theme/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="/measure-theme/css/theme.css" />
        <link rel="stylesheet" type="text/css" href="/css/measure-theme-overrides.css" />
        <link rel="stylesheet" type="text/css" href="/css/chipin-common.css" />
        <link href='//fonts.googleapis.com/css?family=Lato:300,400,700,900,300italic,400italic,700italic,900italic'
              rel='stylesheet' type='text/css' />

        <script src="/js/jquery-latest.js"></script>
        <script src="<?= $themeDir ?>/js/bootstrap.min.js"></script>
        <script src="<?= $themeDir ?>/js/theme.js"></script>

        <?php $this->htmlHeadExtras(); ?>

        <?php if (APPLICATION_ENV == 'production'): ?>
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
        <?php endif; ?>
      </head>
      <body>
        <?= $this->body() ?>
      </body>
    </html>
  <?php }

  protected function body() {
    require dirname(__FILE__) . '/nav-bar.php';
    $this->innerContent();
    require dirname(__FILE__) . '/footer.php';
  }

  protected function widgetURL(Widget $widget) {
    return PATH . 'widgets/by-id/' . $widget->id;
  }

  protected function widgetIframe(Widget $w, $src = null, $id = null) {
    if ($src == null) $src = $this->widgetURL($w);
    return
      '<iframe ' . ($id ? "id=\"$id\" " : '') .
        'src="' . htmlspecialchars($src) . '" ' .
        'frameborder="no" framespacing="0" scrolling="no" allowTransparency="true" ' .
        'width="' . ($w->width + 5) . '" height="' . ($w->height + 5) . '"></iframe>';
  }
}
