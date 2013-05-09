<?php

abstract class Layout {

  abstract function innerContent();

  function content() {
    ?><!DOCTYPE html>
    <html lang="en">
      <head>
        <? require dirname(dirname(__FILE__)) . '/public/application/layouts/scripts/head-common.phtml'; ?>

        <style type="text/css">

          #nav .container, #footer .container {
	          width: 1013px;
          }

          #content .container {
	          background: url("<?= PATH ?>img/bkg_top_bar.png") no-repeat scroll 0 0 white;
	          box-shadow: 0 1px 5px #AAA;
	          width: 979px;
	          padding: 17px;
	          margin-left: auto;
	          margin-right: auto;
          }

        </style>
      </head>

      <body>

        <div id="nav">
	        <div class="container">
		        <div class="nav-collapse">
			        <ul class="nav">
				        <li class="nav-icon">
					        <a href="<?=PATH;?>">
						        <i class="icon-home"></i>
						        <span>Home</span>
					        </a>	    				
				        </li>
			        </ul>
			        <ul class="nav pull-right">
				        <li class="dropdown">					
					        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
						        <i class="icon-external-link"></i>
						        Account
						        <b class="caret"></b>
					        </a>
					        <ul class="dropdown-menu">
						        <li><a href="<?=PATH.'account/changepassword/';?>">Change password</a></li>
						        <li><a href="<?=PATH.'signin/signout/';?>">Sign out</a></li>
					        </ul>    				
				        </li>
			        </ul>
		        </div> <!-- /.nav-collapse -->
	        </div> <!-- /.container -->
        </div> <!-- /#nav -->

        <? /*global $content;*/ ?>
        <?= $this->innerContent(); ?>

      </body>
    </html>
  <? }
}
