<?php

require_once dirname(__FILE__) . '/layout.php';
require_once 'spare-parts/webapp/forms.php';
require_once 'chipin/widgets.php';
require_once 'chipin/currency.php';

use \SpareParts\Webapp\Forms\SelectField, \Chipin\Widgets\Widget, \Chipin\Currency;

class StepOne extends WidgetWizLayout {

  /** @var Widget */
  public $widget;

  protected function stepNumber() { return 1; }
  protected function showPreview() { return false; }
  protected function buttons() {
    return
      ($this->widget->id ?
        '<button class="btn btn-large btn-secondary" onclick="return validateForm();"
                 id="save-and-return" name="save-and-return" value="t"
                 >Save and Return to Dashboard</button>' : '') .
      '<button class="btn btn-large btn-primary" onclick="return validateForm();"
               id="next-step">Next Step</button>';
  }

  function contentForThisStep() { ?>

    <script type="text/javascript" charset="utf-8">
	    $(document).ready(function() {
		    $('#widget-end-date').datepicker({ dateFormat: 'yy-mm-dd', minDate: +1 });
      });
    </script>

    <?= $this->javascriptValidation(1) ?>

    <div id="step-1">
      <h3>Step 1: Basic Information</h3>
      <? if ($this->form && $this->form->hasErrors()): ?>
        <? foreach ($this->form->getErrors() as $e): ?>
          <div class="alert alert-error"><?= $e ?></div>
        <? endforeach; ?>
      <? endif; ?>
      <br />
      <div class="row-fluid">
        <div class="span6" style="width: 100%;">
          <div class="control-group">
            <label class="control-label" for="widget-title">Title of Widget</label>
            <div class="controls">
              <input type="text" class="input-large" id="widget-title" name="title" maxlength="30"
                     value="<?= htmlspecialchars($this->widget->title) ?>" />
              <span class="help-inline error-msg">Please provide a title.</span>
            </div>
          </div>
          <div class="control-group" id="widget-goal">
            <label class="control-label" for="widget-want-to-raise">Amount to Raise</label>
            <div class="controls">
              <? $goal = isset($this->widget->goalAmnt) ?
                   Currency\trimZeros($this->widget->goalAmnt->numUnits) : ""; ?>
              <input type="text" class="input-small" id="widget-want-to-raise"
                     name="goal" value="<?= $goal ?>"/>
              &nbsp;
              <?
                $s = new SelectField('currency', 'Currency',
                  # TODO: Use official currency "registry" here (Chipin\Currencies\codes())
                  array('USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP', 'CNY' => 'CNY',
                        'CAD' => 'CAD', 'JPY' => 'JPY', 'BTC' => 'BTC'));
                $s->setID('currency')->setAttribute('style', 'width: 6em;');
                $s->setValue(empty($this->widget->currency) ? 'USD' : $this->widget->currency);
              ?>
              <?= $s->renderInputHtml() ?>
              <span class="help-inline error-msg">Please enter a valid (numeric) amount.</span>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="widget-end-date">End Date</label>
            <div class="controls">
              <input type="text" class="input-small" id="widget-end-date" name="ending"
                     value="<?= $this->widget->endingDateAsString() ?>"/>
              <span class="help-inline error-msg">Please correct the error.</span>
            </div>
          </div>
          <div class="control-group" id="bitcoin-addr-control-group">
            <label class="control-label" for="widget-bitcoin-address">Bitcoin Address</label>
            <div class="controls">
              <input type="text" class="input-large bitcoin-address" id="widget-bitcoin-address"
                     name="bitcoinAddress" value="<?= $this->widget->bitcoinAddress ?>" />
              <span class="help-inline error-msg">Please provide a valid Bitcoin address.</span>
              <span class="help-inline not-error">
                Please use a new, dedicated Bitcoin address for each
                widget. In this way, we are able to measure the amount that has been donated
                to your cause.</span>
            </div>
          </div>
        </div> <!-- /span6 -->
        <div class="span5 offset1">
          <!--
          <div class="well">

          </div>
          -->
        </div> <!-- /span5 -->
      </div> <!-- /row-fluid -->
    </div> <!-- /step-1 -->

  <? }

  function javascriptValidation($stepNum) { ?>
    <script type="text/javascript">

      function validateForm() {
        var errors = 0;
        var elements = $("#step-" + <?= $stepNum ?>).find(':input');
			  elements.each(function() {
		  		if ($(this).is("input:text")) {
		  		  var container = $(this).parent().parent();
					  if ($(this).val() == "") {
						  container.addClass("error");
						  $(this).siblings().show();
						  errors++;
					  } else if ($(this).val() != "" && $(this).parent().parent().hasClass("error")) {
						  container.removeClass("error");
						  container.find('.error-msg').hide();
						  //$(this).siblings().hide();
					  }
				  }
			  });

			  var invalidBitcoinAddr = false;
				$.ajax({
					url: '<?= PATH ?>widget-wiz/valid-btc-addr/' + $("#widget-bitcoin-address").val(),
					type: 'get',
					dataType: 'text',
					async: false,
					success: function(data) {
						if (data != 'true') invalidBitcoinAddr = true;
					}
				});
			  if (invalidBitcoinAddr) {
					$("#bitcoin-addr-control-group").addClass("error");
					$("#bitcoin-addr-control-group .not-error").hide();
					$("#bitcoin-addr-control-group .error-msg").show();
					return false;
			  }

			  var amountToRaise = $("#widget-want-to-raise").val();
			  if (!isNumber(amountToRaise)) {
			  	$("#widget-goal").addClass("error");
			  	$("#widget-goal .error-msg").show();
			  	errors++;
			  }

			  if (errors > 0) return false; else return true;
      }
      
      function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
      }

    </script>
  <? }

}
