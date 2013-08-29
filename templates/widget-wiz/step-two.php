<?php

require_once dirname(__FILE__) . '/layout.php';
require_once 'spare-parts/webapp/forms.php';

use \SpareParts\Webapp\Forms\SelectField;

class StepTwo extends WidgetWizLayout {

  protected function stepNumber() { return 2; }
  protected function showPreview() { return true; }
  protected function buttons() {
    return '
      <a      class="btn btn-large" href="/widget-wiz/step-one">Previous Step</a>
      <button class="btn btn-large btn-primary">Save Widget</button>';
  }

  function contentForThisStep() { ?>

    <?= $this->updatePreviewJavascript() ?>

    <div id="step-2">
      <h3>Step 2: Customize Widget</h3>
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
          <div class="control-group">
            <label class="control-label" for="userlanguage">
              Color Theme
            </label>
            <div class="controls" style="margin-top: 6px;">
              <?
                $color = new SelectField('color', 'Color',
                  array("white" => "Plain & Pleasant", "silver" => "Silvery Servant",
                        "blue" => "Baby-Blue Winter", "dark" => "Dark & Dastardly"));
                $color->setID('widget-color');
                if ($this->widget->color) $color->setValue($this->widget->color);
              ?>
              <?= $color->renderInputHtml() ?>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="userlanguage">Size</label>
            <div class="controls" style="margin-top: 6px;">
              <?
                $size = new SelectField('size', 'Size',
                  array("350x310" => "350 x 310", "200x300" => "200 x 300",
                        "200x200"  => "200 x 200"));
                $size->setID('widget-size');
                if ($this->widget->width) {
                  $size->setValue($this->widget->width . 'x' . $this->widget->height);
                }
              ?>
              <?= $size->renderInputHtml()  ?>
            </div>
          </div>
        </div> <!-- END div for form inputs -->
        <div style="float: left; margin: 0 0 25px 75px;">
          <div style="text-align: center;">
            <?= $this->widgetIframe($this->widget, $src = '/widget-wiz/preview-current',
                                    $id = 'widget-preview') ?>
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
          $('#widget-preview').attr('width',  parseInt(s[0]) + 5);
          $('#widget-preview').attr('height', parseInt(s[1]) + 5);
        });
      });
    </script>
  <? }

}
