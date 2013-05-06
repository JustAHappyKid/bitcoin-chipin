<?php

require_once dirname(__FILE__) . '/layout.php';

class StepThree extends WidgetWizLayout {

  protected function stepNumber() { return 3; }
  protected function showPreview() { return true; }
  protected function button() {
    return '<a href="/dashboard/">Back to my dashboard</a>';
  }

  function contentForThisStep() { ?>
    <div id="step-3">
	    <h3>Step 3: You're done! Just copy and paste the below code into your website.</h3>
	    <br />
	    <br />
	    <div class="row-fluid">
		    <div class="span6">
		      <!-- Javascript version -->
		      <textarea style="height: 110px; width: 400px;" class="input-large"
				            id="javascript-version" rows="3"
				    ><?= $this->renderIframeIfWidget($this->widget) ?></textarea>
		      <? /* <div class="control-group">
			      <label class="control-label" for="javascript-version"></label>
			      <div class="controls">
				      <textarea style="height: 110px; width: 400px;" class="input-large"
				                id="javascript-version" rows="3"></textarea>
			      </div>
		      </div> */ ?>
		    </div> <!-- /span6 -->
		    <div class="span5 offset1">
			    <div id="final-widget" class="well" style="text-align: center;">
				    <?= $this->renderIframeIfWidget($this->widget); ?>
			    </div>
		    </div> <!-- /span6 -->
	    </div> <!-- /row-fluid -->		
    </div> <!-- /step -->
  <? }

  private function renderIframeIfWidget($widget) {
    if ($widget) {
      return
        '<iframe id="widget-preview2" ' .
                'src="' . PATH . 'client/widget' .
                     $widget->width . 'x' . $widget->height . '?id=' . $widget->id . '" ' .
                'frameborder="no" framespacing="0" scrolling="no" ' .
                'width="' . $widget->width . '" height="' . $widget->height . '"></iframe>';
    } else { return 'no widg?'; }
  }

}
