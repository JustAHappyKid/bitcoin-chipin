<?php

require_once dirname(dirname(__FILE__)) . '/layout.php';

abstract class WidgetWizLayout extends Layout {

  abstract protected function contentForThisStep();
  abstract protected function stepNumber();
  abstract protected function showPreview();
  abstract protected function button();

  function userIsAuthenticated() { return true; }

  function htmlHeadExtras() { ?>

    <? /* TODO: Move this somewhere more appropriate! */ ?>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />

    <? /* XXX: Is this still needed? */ ?>
    <link href="<?=PATH;?>js/plugins/smartwizard/smart_wizard.modified.css" rel="stylesheet" />
    <!-- <script src="<?=PATH;?>js/widget-wiz.js"></script> -->

    <? /* XXX: Are these actually used??  I'm pretty sure they're not. */ ?>
    <link href="<?=PATH;?>js/plugins/msgGrowl/css/msgGrowl.css" rel="stylesheet" />
    <script src="<?=PATH;?>js/plugins/msgGrowl/js/msgGrowl.js"></script>

    <style>

      #ui-datepicker-div {
        z-index: 500 !important;
      }

      /*.stepContainer {
        height: 410px!important;
      }*/

      /*.msgGrowl-container {
        z-index: 500 !important;
      }*/

      .span8 {
        width: 978px;
      }

    </style>

  <? }

  function innerContent() { ?>

    <input type="hidden" id="path" value="<?php echo PATH; ?>"/>

    <? $words = array(1 => 'one', 2 => 'two', 3 => 'three'); ?>

    <div id="content">
      <div class="container">
        <div class="row">
          <div class="span8">
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

                          <span class="<?= $num == $this->stepNumber() ? 'selected' : '' ?>">
                          <!-- <a href="./step-<?= $words[$num] ?>#step-<?= $num ?>"
                            class="<?= $num == $this->stepNumber() ? 'selected' : '' ?>"> -->
                            <div class="wizard-step-number"><?= $num ?></div>
                            <div class="wizard-step-label"><?= $label ?></div>
                            <div class="wizard-step-bar"></div>
                          <!-- </a> -->
                          </span>
                        </li>
                      <? endforeach; ?>
                    </ul>

                    <?= $this->contentForThisStep() ?>

                    <div style="text-align: right; border-top: 1px solid #ddd; padding: 20px 0;">
                      <?= $this->button() ?>
                    </div>

                  </div> <!-- /wizard -->
                </form>
              </div> <!-- /widget-content -->
            </div> <!-- /widget -->
          </div> <!-- /.span8 -->
        </div> <!-- /row -->
      </div> <!-- /.container -->
    </div> <!-- /#content -->

    <div id="footer">
      <div class="container">
        &copy; 2012 Propel UI, all rights reserved.
      </div> <!-- /.container -->
    </div> <!-- /#footer -->

  <? }
}
