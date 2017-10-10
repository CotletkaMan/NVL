<?php
/*
 * ------------------------------------------------------------------------
 * Yt FrameWork for Joomla 3.0
 * ------------------------------------------------------------------------
 * Copyright (C) 2009 - 2012 The YouTech JSC. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: The YouTech JSC
 * Websites: http://www.smartaddons.com - http://www.cmsportal.net
 * ------------------------------------------------------------------------
*/
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
// Add css config to <head>...</head>

$doc->addStyleDeclaration('
body.'.$yt->template.'{
	background-color:'.$yt->getParam('bgcolor').' ;
	color:'.$yt->getParam('textcolor').' ;
}
#yt_slideshow:after{
	background-color:'.$yt->getParam('bgcolor').' ;
}
body.'.$yt->template.' a{
	color:'.$yt->getParam('linkcolor').' ;
}
#yt_slideshow,
#yt_header{
	background-color:'.$yt->getParam('header-bgcolor').' ;
}
#yt_footer,
#yt_spotlight2{
	background-color:'.$yt->getParam('footer-bgcolor').' ;
}
');
// Add class pattern to element wrap
?>
<script type="text/javascript">
	$jsmart(document).ready(function($){  
		/* Begin: add class pattern for element */
		var headerbgimage = '<?php echo $yt->getParam('header-bgimage');?>';
		var footerbgimage = '<?php echo $yt->getParam('footer-bgimage');?>';
		if(headerbgimage){
			$('#yt_header').addClass(headerbgimage);
			$('#yt_slideshow').addClass(headerbgimage);
		}
		if(footerbgimage){
			$('#yt_footer').addClass(footerbgimage);
			$('#yt_spotlight2').addClass(footerbgimage);
		}
		/* End: add class pattern for element */
	});
</script>
<?php
// Include cpanel
if( $yt->getParam('showCpanel') ) {
	include_once (__DIR__.'/cpanel.php');
	
	$doc->addStyleSheet($yt->templateurl().'css/cpanel.css','text/css');
	$doc->addStyleSheet($yt->templateurl().'asset/minicolors/jquery.miniColors.css','text/css');
	$doc->addScript($yt->templateurl().'js/ytcpanel.js');
	$doc->addScript($yt->templateurl().'asset/minicolors/jquery.miniColors.min.js');
?>
	<script type="text/javascript">
    $jsmart(document).ready(function($){
        /* Begin: Enabling miniColors */
        //$('.color-picker').miniColors();
		$('.body-backgroud-color .color-picker').miniColors({
			change: function(hex, rgb) {
				$('body').css('background-color', hex); 
				createCookie(TMPL_NAME+'_'+($(this).attr('name').match(/^ytcpanel_(.*)$/))[1], hex, 365);
			}
		});
		$('.link-color .color-picker').miniColors({
			change: function(hex, rgb) {
				$('body a').css('color', hex);
				createCookie(TMPL_NAME+'_'+($(this).attr('name').match(/^ytcpanel_(.*)$/))[1], hex, 365);
			}
		});
		$('.text-color .color-picker').miniColors({
			change: function(hex, rgb) {
				$('body').css('color', hex);
				createCookie(TMPL_NAME+'_'+($(this).attr('name').match(/^ytcpanel_(.*)$/))[1], hex, 365);
			}
		});
		$('.header-backgroud-color .color-picker').miniColors({
			change: function(hex, rgb) {
				$('#yt_header').css('background-color', hex);
				$('#yt_slideshow').css('background-color', hex);
				createCookie(TMPL_NAME+'_'+($(this).attr('name').match(/^ytcpanel_(.*)$/))[1], hex, 365);
			}
		});
		$('.footer-backgroud-color .color-picker').miniColors({
			change: function(hex, rgb) {
				$('#yt_spotlight2').css('background-color', hex);
				$('#yt_footer').css('background-color', hex);
				createCookie(TMPL_NAME+'_'+($(this).attr('name').match(/^ytcpanel_(.*)$/))[1], hex, 365);
			}
		});
		/* End: Enabling miniColors */
		/* Begin: Set click pattern */
		function patternClick(el, paramCookie, assign){
			$(el).click(function(){
				oldvalue = $(this).parent().find('.active').html();
				$(el).removeClass('active');
				$(this).addClass('active');
				value = $(this).html();
				if(assign.length > 0){
					for($i=0; $i < assign.length; $i++){
						$(assign[$i]).removeClass(oldvalue);
						$(assign[$i]).addClass(value);
					}
				}
				if(paramCookie){
					$('input[name$="ytcpanel_'+paramCookie+'"]').attr('value', value);
					createCookie(TMPL_NAME+'_'+paramCookie, value, 365);
					
				}
			});
	
		}
        patternClick('.header-backgroud-image .pattern', 'header-bgimage', Array('#yt_header', '#yt_slideshow'));
        patternClick('.footer-backgroud-image .pattern', 'footer-bgimage', Array('#yt_spotlight2', '#yt_footer'));
        /* End: Set click pattern */
		function templateSetting(array){
			if(array['0']){
				$('.body-backgroud-color input.miniColors').attr('value', array['0']);
				$('.body-backgroud-color a.miniColors-trigger').css('background-color', array['0']);
				$('input.ytcpanel_bgcolor').attr('value', array['0']);
			}
			if(array['1']){
				$('.link-color input.miniColors').attr('value', array['1']);
				$('.link-color a.miniColors-trigger').css('background-color', array['1']);
				$('input.ytcpanel_linkcolor').attr('value', array['1']);
			}
			if(array['2']){
				$('.text-color input.miniColors').attr('value', array['2']);
				$('.text-color a.miniColors-trigger').css('background-color', array['2']);
				$('input.ytcpanel_textcolor').attr('value', array['2']);
			}
			if(array['3']){
				$('.header-backgroud-color input.miniColors').attr('value', array['3']);
				$('.header-backgroud-color a.miniColors-trigger').css('background-color', array['3']);
				$('input.ytcpanel_header-bgcolor').attr('value', array['3']);
			}
			if(array['4']){
				$('.header-backgroud-image .pattern').removeClass('active');
				$('.header-backgroud-image .pattern.'+array['4']).addClass('active');
				$('input[name$="ytcpanel_header-bgimage"]').attr('value', array['4']);
			}
			if(array['5']){
				$('.footer-backgroud-color input.miniColors').attr('value', array['5']);
				$('.footer-backgroud-color a.miniColors-trigger').css('background-color', array['5']);
				$('input.ytcpanel_footer-bgcolor').attr('value', array['5']);
			}
			if(array['6']){
				$('.footer-backgroud-image .pattern').removeClass('active');
				$('.footer-backgroud-image .pattern.'+array['6']).addClass('active');
				$('input[name$="ytcpanel_footer-bgimage"]').attr('value', array['6']);
			}
		}
		var array 		= Array('bgcolor','linkcolor','textcolor','header-bgcolor','header-bgimage','footer-bgcolor','footer-bgimage');
		var array_red 	= Array('#fafafa','#C43737','#333','#e3e3e3','pattern_h1','#400505','pattern_2');
		var array_green = Array('#fff','#28ab00','#666','#e3e3e3','pattern_h4','#2b570c','pattern_4');
		var array_blue = Array('','#068ceb','#666','#e3e3e3','pattern_h1','#1B4879','pattern_3');
		var array_gray 	= Array('#ffffff','#0095fe','#666666','#e3e3e3','pattern_h4','#26292b','pattern_1');
		
		$('.theme-color.red').click(function(){
			$($(this).parent().find('.active')).removeClass('active'); $(this).addClass('active');
			createCookie(TMPL_NAME+'_'+'sitestyle', $(this).html().toLowerCase(), 365);
			templateSetting(array_red);
			onCPApply();
		});
		$('.theme-color.green').click(function(){
			$($(this).parent().find('.active')).removeClass('active'); $(this).addClass('active');
			createCookie(TMPL_NAME+'_'+'sitestyle', $(this).html().toLowerCase(), 365);
			templateSetting(array_green);
			onCPApply();
		});
		$('.theme-color.blue').click(function(){
			$($(this).parent().find('.active')).removeClass('active'); $(this).addClass('active');
			createCookie(TMPL_NAME+'_'+'sitestyle', $(this).html().toLowerCase(), 365);
			templateSetting(array_blue);
			onCPApply();
		});
		$('.theme-color.gray').click(function(){
			$($(this).parent().find('.active')).removeClass('active'); $(this).addClass('active');
			createCookie(TMPL_NAME+'_'+'sitestyle', $(this).html().toLowerCase(), 365);
			templateSetting(array_gray);
			onCPApply();
		});
		
    });
    </script>
<?php
}
// Show back to top
if( $yt->getParam('showBacktotop') ) {
    include_once (__DIR__.'/backtotop.php');
}
?>