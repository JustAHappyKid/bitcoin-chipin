<?php

require_once dirname(dirname(__FILE__)) . '/layout.php';

abstract class WidgetWizLayout extends Layout {

  abstract protected function contentForThisStep();
  abstract protected function stepNumber();
  abstract protected function showPreview();
  abstract protected function buttons();

  function htmlHeadExtras() { ?>

    <? /* TODO: Move this somewhere more appropriate! */ ?>
    <script src="/jquery-ui/ui/minified/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="/jquery-ui/themes/smoothness/jquery-ui.min.css" />

    <? /* XXX: Remove this once we're sure it's no longer needed...
      <link href="/js/plugins/smartwizard/smart_wizard.modified.css" rel="stylesheet" />
    */ ?>

    <link rel="stylesheet" type="text/css" href="/css/components/widget-wiz.css" />

  <? }

  function innerContent() { ?>

    <input type="hidden" id="path" value="<?php echo PATH; ?>"/>

    <? $words = array(1 => 'one', 2 => 'two', 3 => 'three'); ?>

    <div id="content">
      <div class="container">
        <div class="row">
          <div class="span12">
            <div class="widget">
              <div class="widget-header">
                <h3>
                  <span class="icon-wrench"></span>
                  <!-- <span class="icon-magic"></span> -->
                  <?= ($this->widget->id == null) ? 'Create a new widget' : 'Edit your widget' ?>
                </h3>
              </div> <!-- /widget-header -->
              <div class="widget-content">
                <? $action = '/widget-wiz/step-' . $words[$this->stepNumber()]
                    // ($this->widget->id != null ? "?w={$this->widget->id}" : "")
                ?>
                <form id="widgetForm" action="<?= $action ?>"
                      method="POST" class="form-horizontal">
                  <div id="wizard" class="swMain">
                    <ul class="wizard-steps">
                      <? $steps = array(1 => 'Basic Info', 2 => 'Customize', 3 => 'Installation'); ?>
                      <? foreach ($steps as $num => $label): ?>
                        <li>

                          <div class="step <?= $num == $this->stepNumber() ? 'selected' : '' ?>">
                          <!-- <a href="./step-<?= $words[$num] ?>#step-<?= $num ?>"
                            class="<?= $num == $this->stepNumber() ? 'selected' : '' ?>"> -->
                            <div class="wizard-step-number"><?= $num ?></div>
                            <div class="wizard-step-label"><?= $label ?></div>
                            <div class="wizard-step-bar"></div>
                          <!-- </a> -->
                          </div>
                        </li>
                      <? endforeach; ?>
                    </ul>

                    <?= $this->contentForThisStep() ?>

                    <div class="form-actions">
                      <?= $this->buttons() ?>
                    </div>

                  </div> <!-- /wizard -->
                </form>
              </div> <!-- /widget-content -->
            </div> <!-- /widget -->
          </div> <!-- /.span8 -->
        </div> <!-- /row -->
      </div> <!-- /.container -->
    </div> <!-- /#content -->

  <? }

  protected function widgetIframe($src, $width = null, $height = null, $id = null) {
    return
      '<iframe ' . ($id ? "id=\"$id\" " : '') .
              'src="' . $src . '" ' .
              'frameborder="no" framespacing="0" scrolling="no" ' .
              'width="' . $width . '" height="' . $height . '"></iframe>';
  }
}
