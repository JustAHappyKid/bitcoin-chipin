<?php

require_once dirname(dirname(__FILE__)) . '/layout.php';

abstract class WidgetWizLayout extends Layout {

  abstract protected function contentForThisStep();
  abstract protected function stepNumber();
  abstract protected function showPreview();
  abstract protected function buttons();

  protected function htmlHeadExtras() { ?>

    <? /* TODO: Move this somewhere more appropriate! */ ?>
    <script src="/jquery-ui/ui/minified/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="/jquery-ui/themes/smoothness/jquery-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="/css/components/widget-wiz.css" />

  <? }

  function innerContent() { ?>

    <input type="hidden" id="path" value="<?php echo PATH; ?>"/>

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

                  <div id="wizard">

                    <ul class="wizard-steps">
                      <? $steps = array(1 => 'Basic Info', 2 => 'Customize', 3 => 'Installation'); ?>
                      <? foreach ($steps as $num => $label): ?>
                        <li>

                          <div class="step <?= $num == $this->stepNumber() ? 'selected' : '' ?>">
                          <!-- <a href="./step-<?= $this->words[$num] ?>#step-<?= $num ?>"
                            class="<?= $num == $this->stepNumber() ? 'selected' : '' ?>"> -->
                            <div class="wizard-step-number"><?= $num ?></div>
                            <div class="wizard-step-label"><?= $label ?></div>
                            <div class="wizard-step-bar"></div>
                          <!-- </a> -->
                          </div>
                        </li>
                      <? endforeach; ?>
                    </ul>

                    <?= $this->allTheShitWithForm() ?>

                  </div> <!-- /#wizard -->

              </div> <!-- /.widget-content -->
            </div> <!-- /.widget -->
          </div> <!-- /.span12 -->
        </div> <!-- /row -->
      </div> <!-- /.container -->
    </div> <!-- /#content -->

  <? }

  protected function allTheShitWithForm() { ?>
    <? $action = '/widget-wiz/step-' . $this->words[$this->stepNumber()]; ?>
    <form id="widgetForm" action="<?= $action ?>"
          method="POST" class="form-horizontal">
      <?= $this->contentForThisStep() ?>

      <div class="form-actions">
        <?= $this->buttons() ?>
      </div>
    </form>
  <? }

  /*protected function contentForThisStep() { ?>

  <? }*/

  protected $words = array(1 => 'one', 2 => 'two', 3 => 'three');
}
