<?php

require_once dirname(__FILE__) . '/layout.php';
require_once 'chipin/widgets.php';

use \Chipin\Widgets\Widget;

class StepThree extends WidgetWizLayout {

  protected function stepNumber() { return 3; }
  protected function showPreview() { return true; }
  protected function button() {
    return '<a href="/dashboard/">Back to my dashboard</a>';
  }

  function contentForThisStep() { ?>
    <div id="step-3">
      <h3>Step 3: You're done! Just copy and paste the below code into your website.</h3>
      <div>
        <div style="float: left;"> <!-- div for form inputs -->
          <textarea style="height: 110px; width: 400px;" class="input-large"
                    id="javascript-version" rows="3"
            ><?= htmlspecialchars($this->iframeForWidget($this->widget)) ?></textarea>
        </div>
        <div style="float: left; margin: 0 0 25px 75px;">
          <?= $this->iframeForWidget($this->widget); ?>
        </div>
        <div class="clearfix"> </div>
      </div> <!-- /XXX -->		
    </div> <!-- /step -->
  <? }

  private function iframeForWidget(Widget $widget) {
    $src = PATH . 'widgets/by-id/' . $widget->id;
    return $this->widgetIframe($src, $widget->width, $widget->height);
  }
}
