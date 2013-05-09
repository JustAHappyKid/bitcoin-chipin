<?php

require_once dirname(__FILE__) . '/layout.php';

class StepOne extends WidgetWizLayout {

  protected function stepNumber() { return 1; }
  protected function showPreview() { return false; }
  protected function button() {
    return '
        <button class="button btn btn-secondary btn-large" onclick="return validateForm();">Next Step</button>
      ';
  }

  function contentForThisStep() { ?>

    <script type="text/javascript" charset="utf-8">
	    $(document).ready(function() {
		    $('#widget-end-date').datepicker({ dateFormat: 'mm/dd/yy', minDate: +1 });
      });
    </script>

    <?= $this->javascriptValidation(1) ?>

    <div id="step-1">
      <h3>Step 1: Basic Information</h3>
      <br />
      <div class="row-fluid">
        <div class="span6" style="width: 100%;">
          <div class="control-group">
            <label class="control-label" for="widget-title">Title of Widget</label>
            <div class="controls">
              <input type="text" class="input-large" id="widget-title" name="title" maxlength="50"
                     value="<?= $this->widget->title ?>" />
              <span class="help-inline">Please provide a title.</span>
            </div>
          </div>
          <div class="control-group" id="widget-goal">
            <label class="control-label" for="widget-want-to-raise">Amount to Raise</label>
            <div class="controls">
              <input type="text" class="input-small" id="widget-want-to-raise" name="goal"
                     value="<?= $this->widget->goal ?>"/>
              &nbsp;
              <!-- TODO: Currency should be "sticky" and remember its value!! -->
              <select id="currency" name="currency" style="width: 6em;">
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
                <option value="GBP">GBP</option>
                <option value="CNY">CNY</option>
                <option value="CAD">CAD</option>
                <option value="JPY">JPY</option>
                <option value="BTC">BTC</option>
              </select>
              <span class="help-inline">Please enter a valid (numeric) amount.</span>
            </div>
          </div>
          <!-- <div class="control-group">
            <label class="control-label" for="widget-end-date">Currency</label>
            <div class="controls">
              <select id="currency" name="currency" style="width: 6em;">
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
                <option value="GBP">GBP</option>
                <option value="CNY">CNY</option>
                <option value="CAD">CAD</option>
                <option value="JPY">JPY</option>
                <option value="BTC">BTC</option>
              </select>
            </div>
          </div> -->
          <div class="control-group">
            <label class="control-label" for="widget-end-date">End Date</label>
            <div class="controls">
              <input type="text" class="input-small" id="widget-end-date" name="ending"
                     value="<?= $this->widget->ending ?>"/>
              <span class="help-inline">Please correct the error.</span>
            </div>
          </div>
          <div class="control-group" id="bitcoin-addr-control-group">
            <label class="control-label" for="widget-bitcoin-address">Bitcoin Address</label>
            <div class="controls">
              <input type="text" class="input-large" id="widget-bitcoin-address"
                     name="bitcoinAddress" value="<?= $this->widget->bitcoinAddress ?>" />
              <span class="help-inline">Please provide a valid Bitcoin address.</span>
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
        $elements = $("#step-" + <?= $stepNum ?>).find(':input');
			  $elements.each(function() {
		  		if ($(this).is("input:text")) {
					  if ($(this).val() == "") {
						  $(this).parent().parent().addClass("error");
						  $(this).siblings().show();
						  errors++;
					  } else if ($(this).val() != "" && $(this).parent().parent().hasClass("error")) {
						  $(this).parent().parent().removeClass("error");
						  $(this).siblings().hide();
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
					return false;
			  }

			  var amountToRaise = $("#widget-want-to-raise").val();
			  if (!isNumber(amountToRaise)) {
			  	$("#widget-goal").addClass("error");
			  	$("#widget-goal .help-inline").show();
			  	errors++;
			  }

			  if (errors > 0) return false;
      }
      
      function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
      }

    </script>
  <? }

}
