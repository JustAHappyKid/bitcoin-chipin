<?php

require_once dirname(__FILE__) . '/layout.php';

class StepOne extends WidgetWizLayout {

  protected function stepNumber() { return 1; }
  protected function showPreview() { return false; }
  protected function button() {
    return '
        <button class="button btn btn-secondary btn-large">Next Step</button>
      ';
  }

function contentForThisStep() { ?>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		$('#widget-end-date').datepicker({ dateFormat: 'mm/dd/yy', minDate: +1 });
  });
</script>

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
          <span class="help-inline">Please correct the error.</span>
        </div>
      </div>
      <div class="control-group" id="widget-goal">
        <label class="control-label" for="widget-want-to-raise">I want to raise</label>
        <div class="controls">
          <input type="text" class="input-small" id="widget-want-to-raise" name="goal"
                 value="<?= $this->widget->goal ?>"/>
          <span class="help-inline">Please enter a valid number.</span>
        </div>
      </div>
      <!-- TODO: Currency should be "sticky" and remember its value!! -->
      <div class="control-group">
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
      </div>
      <div class="control-group">
        <label class="control-label" for="widget-end-date">End Date</label>
        <div class="controls">
          <input type="text" class="input-small" id="widget-end-date" name="ending"
                 value="<?= $this->widget->ending ?>"/>
          <span class="help-inline">Please correct the error.</span>
        </div>
      </div>
      <div class="control-group" id="bitcoin_address_val">
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

}
