<?php

require_once dirname(__FILE__) . '/layout.php';
require_once 'spare-parts/webapp/forms.php';

use \SpareParts\Webapp\Forms\SelectField;

class StepTwo extends WidgetWizLayout {

  protected function stepNumber() { return 2; }
  protected function showPreview() { return true; }
  protected function button() {
    return '<button class="button btn btn-secondary btn-large">Save Widget</button>';
  }

  function contentForThisStep() { ?>

    <?= $this->updatePreviewJavascript() ?>

    <div id="step-2">
      <h3>Step 2: Customize Widget</h3>
      <br />
      <div>
        <div style="float: left;"> <!-- div for form inputs -->
          <div class="control-group">
            <label class="control-label" for="widget-about">
              About the widget
            </label>
            <div class="controls">
              <textarea class="input-large" id="widget-about" rows="3" maxlength="1000"
                        name="about" style="resize: none;height: 60px;width: 260px;"
                        ><?= $this->widget->about ?></textarea>
            </div>
          </div>
          <!-- TODO: Make 'size' and 'color' "sticky"!!! -->
          <div class="control-group">
            <label class="control-label" for="userlanguage">Size</label>
            <div class="controls">
              <?
                $size = new SelectField('size', 'Size',
                  array("250x250" => "250 x 250", "120x60"  => "120 x 60",
                        "125x125" => '125 x 125', "160x250" => "160 x 250",
                        "220x220" => "220 x 220", "234x60"  => "234 x 60"));
                $size->setID('widget-size');
                if ($this->widget->width) {
                  $size->setValue($this->widget->width . 'x' . $this->widget->height);
                }
              ?>
              <?= $size->renderInputHtml() ?>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="userlanguage">
              Color
            </label>
            <div class="controls">
              <?
                $color = new SelectField('color', 'Color',
                  array("A9DB80,96C56F" => "Green", "A67939,845108" => "Brown",
                        "3093C7,1C5A85" => "Blue", "E0DCDC,707070"  => "Grey",
                        "F62B2B,D20202" => "Red"));
                $color->setID('widget-color');
                if ($this->widget->color) $color->setValue($this->widget->color);
              ?>
              <?= $color->renderInputHtml() ?>
            </div>
          </div>
        </div> <!-- END div for form inputs -->
        <div style="float: left; margin: 0 0 0 100px;">
          <div class="well" style="text-align: center;">
            <iframe id="widget-preview" src="<?= PATH . 'widget-wiz/preview-current' ?>"
                    frameborder='no' framespacing='0' scrolling='no'
                    width="250" height="250"></iframe>
          </div>
        </div> <!-- END div for preview -->
        <div class="clearfix"> </div>
      </div> <!-- / XXX -->
    </div> <!-- /step -->

  <? }

  function updatePreviewJavascript() { ?>
    <script type="text/javascript" charset="utf-8">
      $(document).ready(function() {
        $("#widget-about, #widget-size, #widget-color").change(function(){
          var size = $('#widget-size').val();
          var s = size.split('x');
          $('#widget-preview').attr('src',
            $('#path').val() + "widget-wiz/preview-current" +
            "?color=" + $('#widget-color').val() + "&about="+$("#widget-about").val() +
            "&width=" + s[0] + "&height=" + s[1]);
          $('#widget-preview').attr('width', s[0]);
          $('#widget-preview').attr('height', s[1]);
        });
      });
    </script>
  <? }

}
