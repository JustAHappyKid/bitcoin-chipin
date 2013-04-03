// Annotation overlay effect
$(document).ready(function () {
	//main div
	var $portfolio	= $('#portfolio');
	
	//click event for the image : 
	//show the overlay
	$portfolio.find('.image_wrap').bind('click',function(){
		var $elem	= $(this);
		var $image	= $elem.find('img:first');
		$image.stop(true)
				.animate({
				'width'	:'400px',
				'height':'400px'
				},250);
			
	//the overlay is the next element
	var opacity	= '1';
	if($.browser.msie)
		opacity	= '0.5'
	$elem.next()
		 .stop(true)
		 .animate({
			'width'		:'500px',
			'height'	:'500px',
			'marginTop'	:'-250px',
			'marginLeft':'-250px',
			'opacity'	:opacity
		},250,function(){
			//fade in the annotations
			$(this).show().find('img').fadeIn();
		});
});
				
//click event for the overlay :
//show the image again and hide the overlay
$portfolio.find('.zoom_overlay').bind('click',function(){
	var $elem	= $(this);
	var $image	= $elem.prev()
						 .find('img:first');
	//hide overlay
	$elem.find('img')
		 .hide()
		 .end()
		 .stop(true)
		 .animate({
			'width'		:'400px',
			'height'	:'400px',
			'marginTop'	:'-200px',
			'marginLeft':'-200px',
			'opacity'	:'0'
		 },125,function(){
			//hide overlay
			$(this).hide();
		 });
		 
	//show image	 
	$image.stop(true)
			.animate({
			'width':'500px',
			'height':'500px'
			},250);
	});
});
						
// Accordion
$(function() {
    $( "#accordion" ).accordion();
});

$(document).ready(function (){
	//$('.fly-container').animate({opacity:1, marginLeft: "0px"}, 4000); 

	var allPanels = $('.faq-info > dd');
    
  	$('.faq-info > dt').click(function() {
    	allPanels.slideUp();
    	$(this).next().slideDown();
    	return false;
  	});

  	$('.theme .options li a').click(function() {
    	$('#widget-preview .widget').addClass('no-display');
    	$('.theme .options li').removeClass('selected-widget');
    	$(this).parent().addClass('selected-widget');
    	var WidgetName = "#" + $(this).attr('id') + "-main" ;
    	$(WidgetName).removeClass('no-display');
    	return false;
  	});

  	//$('#fly-container').animate({opacity:1, marginLeft: "0px"}, 4000); 
});
