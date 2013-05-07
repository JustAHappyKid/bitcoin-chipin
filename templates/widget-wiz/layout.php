<?php

require_once dirname(dirname(__FILE__)) . '/layout.php';

abstract class WidgetWizLayout extends Layout {

  abstract protected function contentForThisStep();
  abstract protected function stepNumber();
  abstract protected function showPreview();
  abstract protected function button();

  function innerContent() { ?>

    <script src="<?=PATH;?>js/plugins/timepicker/jquery.ui.timepicker.min.js?v=1.0"></script>
    <link href="<?=PATH;?>js/plugins/timepicker/jquery.ui.timepicker.css?v=1.0" rel="stylesheet" />

    <link href="<?=PATH;?>css/pages/calendar.css" rel="stylesheet" />

    <link href="<?=PATH;?>js/plugins/smartwizard/smart_wizard.modified.css" rel="stylesheet" />
    <script src="<?=PATH;?>js/widget-wiz.js"></script>

    <link href="<?=PATH;?>js/plugins/msgGrowl/css/msgGrowl.css" rel="stylesheet" />
    <script src="<?=PATH;?>js/plugins/msgGrowl/js/msgGrowl.js"></script>

    <style>

      #ui-datepicker-div {
	      z-index: 500 !important;
      }

      .stepContainer {
	      height: 410px!important;
      }

      .msgGrowl-container {
	      z-index: 500 !important;
      }

      .span8 {
	      width: 978px;
      }

    </style>

    <script type="text/javascript" charset="utf-8">
	    $(document).ready(function(){

		    $('#wizard').smartWizard({
			    transitionEffect: 'slideleft',
			    enableFinishButton: false
		    });

		    $("#widget-size, #widget-color, #widget-title, #widget-about, #widget-want-to-raise, #widget-end-date, #currency, #widget-bitcoin-address").change(function(){

			    var size = $('#widget-size').val();
			    var s = size.split('x');

			    $('#widget-preview, #widget-preview2').attr('src', $('#path').val()+"client/widget"+size+"?preview=true&color="+$('#widget-color').val()+"&address="+$('#widget-bitcoin-address').val()+"&title="+$('#widget-title').val()+"&goal="+$("#widget-want-to-raise").val()+"&about="+$("#widget-about").val()+"&ending="+$('#widget-end-date').val()+"&currency="+$('#currency').val());
			    $('#widget-preview, #widget-preview2').attr('width', s[0]);
			    $('#widget-preview, #widget-preview2').attr('height', s[1]);
		    });

	    });

    </script>

    <input type="hidden" id="path" value="<?php echo PATH; ?>"/>

    <? $words = array(1 => 'one', 2 => 'two', 3 => 'three'); ?>

    <div id="content">
	    <div class="container">
		    <div class="row">
			    <div class="span8">
	          		<div class="widget">
					    <div class="widget-header">
						    <h3>
							    <span class="icon-magic"></span>
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
										      <a href="./step-<?= $words[$num] ?>#step-<?= $num ?>"
										         class="<?= $num == $this->stepNumber() ? 'selected' : '' ?>">
											      <div class="wizard-step-number"><?= $num ?></div>
											      <div class="wizard-step-label"><?= $label ?></div>
											      <div class="wizard-step-bar"></div>
										      </a>
									      </li>
									    <? endforeach; ?>
								    </ul>

                    <? if ($this->showPreview()): ?>
				          		<?= $this->contentForThisStep() ?>
                    <? else: ?>
				          		<?= $this->contentForThisStep() ?>
                    <? endif; ?>
                    
					        		<div style="text-align: right; border-top: 1px solid #ddd; padding: 20px 0;">
					        		  <?= $this->button() ?>
					        		</div>
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
