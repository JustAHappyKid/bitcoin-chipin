/*
 * SmartWizard 2.0 plugin
 * jQuery Wizard control Plugin
 * by Dipu 
 * 
 * http://www.techlaboratory.net 
 * http://tech-laboratory.blogspot.com
 */

 
(function($){
    $.fn.smartWizard = function(action) {
        var options = $.extend({}, $.fn.smartWizard.defaults, action);
        var args = arguments;
        
        return this.each(function(){
                var obj = $(this);
                var curStepIdx = options.selected;
                var steps = $("ul > li > a", obj); // Get all anchors in this array
                var contentWidth = 0;
                var loader,msgBox,elmActionBar,elmStepContainer,btNext,btPrevious,btFinish;
                
                elmActionBar = $('.actionBar',obj);
                if(elmActionBar.length == 0){
                  elmActionBar = $('<div></div>').addClass("actionBar");                
                }

                msgBox = $('.msgBox',obj);
                if(msgBox.length == 0){
                  msgBox = $('<div class="msgBox"><div class="content"></div><a href="#" class="close">X</a></div>');
                  elmActionBar.append(msgBox);                
                }
                
                $('.close',msgBox).click(function() {
                    msgBox.fadeOut("normal");
                    return false;
                });
                

                // Method calling logic
                if (!action || action === 'init' || typeof action === 'object') {
                  init();
                }else if (action === 'showMessage') {
                  //showMessage(Array.prototype.slice.call( args, 1 ));
                  var ar =  Array.prototype.slice.call( args, 1 );
                  showMessage(ar[0]);
                  return true;
                }else if (action === 'setError') {
                  var ar =  Array.prototype.slice.call( args, 1 );
                  setError(ar[0].stepnum,ar[0].iserror);
                  return true;
                } else {
                  $.error( 'Method ' +  action + ' does not exist' );
                }
                
                function init(){
                  var allDivs =obj.children('div'); //$("div", obj);                
                  obj.children('ul').addClass("anchor");
                  allDivs.addClass("content");
                  // Create Elements
                  loader = $('<div>Loading</div>').addClass("loader");
                  elmActionBar = $('<div></div>').addClass("actionBar");
                  elmStepContainer = $('<div></div>').addClass("stepContainer");
                  btNext = $('<a>'+options.labelNext+'</a>').attr("href","#").addClass("buttonNext btn btn-tertiary");
                  btPrevious = $('<a>'+options.labelPrevious+'</a>').attr("href","#").addClass("buttonPrevious btn");
                  btFinish = $('<a>'+options.labelFinish+'</a>').attr("href","#").addClass("buttonFinish btn btn-primary");
                  
                  // highlight steps with errors
                  if(options.errorSteps && options.errorSteps.length>0){
                    $.each(options.errorSteps, function(i, n){
                      setError(n,true);
                    });
                  }


                  elmStepContainer.append(allDivs);
                  elmActionBar.append(loader);
                  obj.append(elmStepContainer);
                  obj.append(elmActionBar); 
                  elmActionBar.append(btFinish).append(btNext).append(btPrevious); 
                  contentWidth = elmStepContainer.width();

                  $(btNext).click(function() {
                      doForwardProgress();
                      return false;
                  }); 
                  $(btPrevious).click(function() {
                      doBackwardProgress();
                      return false;
                  }); 
                  $(btFinish).click(function() {
                      if(!$(this).hasClass('buttonDisabled')){
                         if($.isFunction(options.onFinish)) {
                            if(!options.onFinish.call(this,$(steps))){
                              return false;
                            }
                         }else{
                           var frm = obj.parents('form');
                           if(frm && frm.length){
                             frm.submit();
                           }                         
                         }                      
                      }

                      return false;
                  }); 
                  
                  $(steps).bind("click", function(e){
                      if(steps.index(this) == curStepIdx){
                        return false;                    
                      }
                      var nextStepIdx = steps.index(this);
                      var isDone = steps.eq(nextStepIdx).attr("isDone") - 0;
                      if(isDone == 1){
                        LoadContent(nextStepIdx);                    
                      }
                      return false;
                  }); 
                  
                  // Enable keyboard navigation                 
                  if(options.keyNavigation){
                      $(document).keyup(function(e){
                          if(e.which==39){ // Right Arrow
                            doForwardProgress();
                          }else if(e.which==37){ // Left Arrow
                            doBackwardProgress();
                          }
                      });
                  }
                  //  Prepare the steps
                  prepareSteps();
                  // Show the first slected step
                  LoadContent(curStepIdx);                  
                }

                function prepareSteps(){
                  if(!options.enableAllSteps){
                    $(steps, obj).removeClass("selected").removeClass("done").addClass("disabled"); 
                    $(steps, obj).attr("isDone",0);                 
                  }else{
                    $(steps, obj).removeClass("selected").removeClass("disabled").addClass("done"); 
                    $(steps, obj).attr("isDone",1); 
                  }

            	    $(steps, obj).each(function(i){
                        $($(this).attr("href"), obj).hide();
                        $(this).attr("rel",i+1);
                  });
                }
                
                function LoadContent(stepIdx){
                    var selStep = steps.eq(stepIdx);
                    var ajaxurl = options.contentURL;
                    var hasContent =  selStep.data('hasContent');
                    stepNum = stepIdx+1;
                    if(ajaxurl && ajaxurl.length>0){
                       if(options.contentCache && hasContent){
                           showStep(stepIdx);                          
                       }else{
                           $.ajax({
                            url: ajaxurl,
                            type: "POST",
                            data: ({step_number : stepNum}),
                            dataType: "text",
                            beforeSend: function(){ loader.show(); },
                            error: function(){loader.hide();},
                            success: function(res){ 
                              loader.hide();       
                              if(res && res.length>0){  
                                 selStep.data('hasContent',true);            
                                 $($(selStep, obj).attr("href"), obj).html(res);
                                 showStep(stepIdx);
                              }
                            }
                          }); 
                      }
                    }else{
                      showStep(stepIdx);
                    }
                }
                
                function showStep(stepIdx){
                    var selStep = steps.eq(stepIdx); 
                    var curStep = steps.eq(curStepIdx);
                    if(stepIdx != curStepIdx){
                      if($.isFunction(options.onLeaveStep)) {
                        if(!options.onLeaveStep.call(this,$(curStep))){
                          return false;
                        }
                      }
                    }     
                    elmStepContainer.height($($(selStep, obj).attr("href"), obj).outerHeight());               
                    if(options.transitionEffect == 'slide'){
                      $($(curStep, obj).attr("href"), obj).slideUp("fast",function(e){
                            $($(selStep, obj).attr("href"), obj).slideDown("fast");
                            curStepIdx =  stepIdx;                        
                            SetupStep(curStep,selStep);
                          });
                    } else if(options.transitionEffect == 'fade'){                      
                      $($(curStep, obj).attr("href"), obj).fadeOut("fast",function(e){
                            $($(selStep, obj).attr("href"), obj).fadeIn("fast");
                            curStepIdx =  stepIdx;                        
                            SetupStep(curStep,selStep);                           
                          });                    
                    } else if(options.transitionEffect == 'slideleft'){
                        var nextElmLeft = 0;
                        var curElementLeft = 0;
                        if(stepIdx > curStepIdx){
                            nextElmLeft1 = contentWidth + 10;
                            nextElmLeft2 = 0;
                            curElementLeft = 0 - $($(curStep, obj).attr("href"), obj).outerWidth();
                        } else {
                            nextElmLeft1 = 0 - $($(selStep, obj).attr("href"), obj).outerWidth() + 20;
                            nextElmLeft2 = 0;
                            curElementLeft = 10 + $($(curStep, obj).attr("href"), obj).outerWidth();
                        }
                        if(stepIdx == curStepIdx){
                            nextElmLeft1 = $($(selStep, obj).attr("href"), obj).outerWidth() + 20;
                            nextElmLeft2 = 0;
                            curElementLeft = 0 - $($(curStep, obj).attr("href"), obj).outerWidth();                           
                        }else{
                            $($(curStep, obj).attr("href"), obj).animate({left:curElementLeft},"fast",function(e){
                              $($(curStep, obj).attr("href"), obj).hide();
                            });                       
                        }

                        $($(selStep, obj).attr("href"), obj).css("left",nextElmLeft1);
                        $($(selStep, obj).attr("href"), obj).show();
                        $($(selStep, obj).attr("href"), obj).animate({left:nextElmLeft2},"fast",function(e){
                          curStepIdx =  stepIdx;                        
                          SetupStep(curStep,selStep);                      
                        });
                    } else{
                        $($(curStep, obj).attr("href"), obj).hide(); 
                        $($(selStep, obj).attr("href"), obj).show();
                        curStepIdx =  stepIdx;                        
                        SetupStep(curStep,selStep);
                    }
                    return true;
                }
                
                function SetupStep(curStep,selStep){
                   $(curStep, obj).removeClass("selected");
                   $(curStep, obj).addClass("done");
                   
                   $(selStep, obj).removeClass("disabled");
                   $(selStep, obj).removeClass("done");
                   $(selStep, obj).addClass("selected");
                   $(selStep, obj).attr("isDone",1);
                   adjustButton();
                   if($.isFunction(options.onShowStep)) {
                      if(!options.onShowStep.call(this,$(selStep))){
                        return false;
                      }
                   } 
                }                
                
                function doForwardProgress(){
                  var nextStepIdx = curStepIdx + 1;
                  
                  var errors = 0;
                  
                  $elements = $("#step-"+nextStepIdx).find(':input');
				  $elements.each(function(){
			  		if($(this).is("input:text")){
						if($(this).val() == ""){
							$(this).parent().parent().addClass("error");
							errors++;
						}else if($(this).val() != "" && $(this).parent().parent().hasClass("error")){
							$(this).parent().parent().removeClass("error");
						}
					}
				  });
				  
				  var path = $("#path").val();
				  var invalid_bitcoin_address = false;
				  
				$.ajax({
					url: path + 'widgets/ajaxcheckaddress/?address=' + $("#widget-bitcoin-address").val(),
					type: 'get',
					dataType: 'html',
					async: false,
					success: function(data) {
						if(data == 'true')
							invalid_bitcoin_address = true;
					} 
				});
				  
				  if(!invalid_bitcoin_address){
					/*
					$.msgGrowl({
						title: 'Error',
						text: 'Invalid bitcoin address.',
						position: 'bottom-right',
						type: 'error'
					});
					*/
					$("#bitcoin_address_val").addClass("error");
				  	return false;
				  }
				  
				  var want_to_raise = $("#widget-want-to-raise").val();
				  console.log();
				  if(typeof want_to_raise != 'number' && !isFinite(want_to_raise)){
				  	$("#widget-goal").addClass("error");
				  	errors++;
				  }
				  
				  if(errors>0)
					return false;
				  
				  if(nextStepIdx == 2){
				  	
				  	var size = $('#widget-size').val();
					var s = size.split('x');
				  	var src = $("#widget-preview").attr("src");
				  	src = src.split("client/widget"+size+"?preview=true&");

					var url = path + 'widgets/ajaxsave/?'+src[1]+'&width=' + s[0] + '&height=' + s[1] + '&widget_id=' +  $("#widget_id").val() + '&edit_widget=1';

					$.ajax({
						url: url,
						type: 'get',
						dataType: 'html',
						async: false,
						success: function(data) {
				  			var obj = jQuery.parseJSON(data);
				  			var iSrc = path+"client/widget"+size+"?id="+obj.id+"&owner_id="+obj.owner_id;
				  			var iframe = "<iframe id='widget-preview' frameborder='no' framespacing='0' scrolling='no' src='"+iSrc+"' width='"+obj.width+"' height='"+obj.height+"'></iframe>";
							/*
							var flash = 
								'<style type="text/css" media="screen"> html, body  { height:100%; }body { margin:0; padding:0; overflow:auto; text-align:center; background-color: #ffffff; }object:focus { outline:none; }#flashContent { display:none; }</style>'+	
								'<link rel="stylesheet" type="text/css" href="'+path+'flash/history/history.css" />'+
								'<script type="text/javascript" src="'+path+'flash/history/history.js"></script>'+
								'<script type="text/javascript" src="'+path+'flash/swfobject.js"></script>'+
								'<script type="text/javascript">'+
								'var swfVersionStr = "11.4.0";'+
								'var xiSwfUrlStr = "'+path+'flash/playerProductInstall.swf";'+
								'var flashvars = {url:"http://bitcoinchipin.com.haykaghabekyan.com/client/widget'+size+'?id='+obj.id+'",owner_id:"'+obj.owner_id+'",flash:"1"};'+
								'var params = {};params.quality = "high";params.bgcolor = "#ffffff";params.allowscriptaccess = "sameDomain";params.allowfullscreen = "true";var attributes = {};attributes.id = "WebWidget";attributes.name = "WebWidget";attributes.align = "middle";swfobject.embedSWF("'+path+'flash/WebWidget.swf", "flashContent", "100%", "100%", swfVersionStr, xiSwfUrlStr, flashvars, params, attributes);swfobject.createCSS("#flashContent", "display:block;text-align:left;");</script>'+
								'<div id="flashContent"></div>';
							*/
							var flash = 
								'<embed '+ 
									'allowScriptAccess="always" '+
									'src="http://bitcoinchipin.com.haykaghabekyan.com/flash/WebWidget.swf" '+
									'flashVars="url='+iSrc+'&flash=1" '+
									'type="application/x-shockwave-flash" '+
									'wmode="transparent" '+
									'width="'+ obj.width +'" '+
									'height="'+ obj.height +'"'+
								'></embed>';

				  			$("#javascript-version").text(iframe);
				  			$("#flash-version").text(flash);
						}
					});
				  }
                  
                  if(steps.length <= nextStepIdx){
                    if(!options.cycleSteps){
                      return false;
                    }
                    nextStepIdx = 0;
                  }
                  LoadContent(nextStepIdx);
                }
                
                function doBackwardProgress(){
                  var nextStepIdx = curStepIdx-1;
                  if(0 > nextStepIdx){
                    if(!options.cycleSteps){
                      return false;
                    }
                    nextStepIdx = steps.length - 1;
                  }
                  LoadContent(nextStepIdx);
                }  
                
                function adjustButton(){
                  if(!options.cycleSteps){                
                    if(0 >= curStepIdx){
                      $(btPrevious).addClass("buttonDisabled");
                    }else{
                      $(btPrevious).removeClass("buttonDisabled");
                    }
                    if((steps.length-1) <= curStepIdx){
                     $(btPrevious).addClass("buttonDisabled");
                     $(btNext).addClass("buttonDisabled");
                     $(btPrevious).remove();
                     $(btNext).remove();
                    }else{
                      $(btNext).removeClass("buttonDisabled");
                    }
                  }
                  // Finish Button 
                  if(!steps.hasClass('disabled') || options.enableFinishButton){
                    $(btFinish).removeClass("buttonDisabled");
                  }else{
                    $(btFinish).addClass("buttonDisabled");
                  }                  
                }
                
                function showMessage(msg){
                  $('.content',msgBox).html(msg);
              		msgBox.show();
                }
                
                function setError(stepnum,iserror){
                  if(iserror){                    
                    $(steps.eq(stepnum-1), obj).addClass('error')
                  }else{
                    $(steps.eq(stepnum-1), obj).removeClass("error");
                  }                                   
                }                        
        });  
    };  
 
    // Default Properties and Events
    $.fn.smartWizard.defaults = {
          selected: 0,  // Selected Step, 0 = first step   
          keyNavigation: true, // Enable/Disable key navigation(left and right keys are used if enabled)
          enableAllSteps: false,
          transitionEffect: 'fade', // Effect on navigation, none/fade/slide/slideleft
          contentURL:null, // content url, Enables Ajax content loading
          contentCache:true, // cache step contents, if false content is fetched always from ajax url
          cycleSteps: false, // cycle step navigation
          enableFinishButton: false, // make finish button enabled always
          errorSteps:[],    // Array Steps with errors
          labelNext:'Next',
          labelPrevious:'Previous',
          labelFinish:'Finish',          
          onLeaveStep: null, // triggers when leaving a step
          onShowStep: null,  // triggers when showing a step
          onFinish: null  // triggers when Finish button is clicked
    };    
    
})(jQuery);