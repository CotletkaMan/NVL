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
?>
<?php
// Body Font family
$doc->addStyleDeclaration('body.'.$yt->template.'{font-size:'.$fontsize.'}');
if(trim($font_name)!=''){
	$doc->addStyleDeclaration('body.'.$yt->template.'{font-family:'.$font_name.',sans-serif;}');
}

// Google Font & Element use

if ($googleWebFont != "" && $googleWebFont != " " && strtolower($googleWebFont)!="none") {
	$doc->addStyleSheet('http://fonts.googleapis.com/css?family='.str_replace(" ","+",$googleWebFont).'');
	$googleWebFontFamily = strpos($googleWebFont, ':')?substr($googleWebFont, 0, strpos($googleWebFont, ':')):$googleWebFont;
	if(trim($googleWebFontTargets)!="")
		$doc->addStyleDeclaration('  '.$googleWebFontTargets.'{font-family:'.$googleWebFontFamily.', serif !important}');
}
?>
    <link rel="stylesheet" href="<?php echo $yt->templateurl().'css/prettify.css';?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo $yt->templateurl().'asset/bootstrap/css/bootstrap.min.css';?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo $yt->templateurl().'asset/bootstrap/css/bootstrap-responsive.min.css';?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo $yt->templateurl().'css/fonts.css';?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo $yt->templateurl().'css/animations.css';?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo $yt->templateurl().'css/template.css';?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo $yt->templateurl().'css/color/'.$yt->getParam('sitestyle').'.css';?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo $yt->templateurl().'css/yt-bootstrap-responsive.css';?>" type="text/css" />
<?php    
if(isset($yt_render->arr_TH['stylesheet'])){
	foreach($yt_render->arr_TH['stylesheet'] as $tagStyle){
	?>
	<link rel="stylesheet" href="<?php echo $yt->templateurl().'css/'.$tagStyle;?>" type="text/css" />
    <?php 
	}
}
if ($yt->isIE()){ 
	if($yt->ieversion()==8){
		$doc->addStyleSheet($yt->templateurl().'css/template-ie8.css','text/css');
	}
	if($yt->ieversion()==9){
		$doc->addStyleSheet($yt->templateurl().'css/template-ie9.css','text/css');
	}
}
if($ytrtl == 'rtl'){
	?>
	<link rel="stylesheet" href="<?php echo $yt->templateurl().'css/template_rtl.css';?>" type="text/css" />
    <?php
}
?>
<script src="<?php echo $yt->templateurl().'js/prettify.js' ?>" type="text/javascript"></script>
    <!--<script src="<?php //echo $yt->templateurl().'asset/bootstrap/js/less-1.3.0.min.js'?>" type="text/javascript"></script>-->
    <!--<script src="<?php //echo $yt->templateurl().'asset/bootstrap/js/bootstrap.js' ?>" type="text/javascript"></script>-->
<?php
if ($yt->isIE()){ 
	if($yt->ieversion()<=8){
?>
	<!--Support html5-->
	<script src="<?php echo $yt->templateurl().'js/modernizr.min.js' ?>" type="text/javascript"></script>	
	<!--Support Media Query-->
	<script src="<?php echo $yt->templateurl().'js/respond.min.js' ?>" type="text/javascript"></script>	
<?php
	}
}
?>
	<script src="<?php echo $yt->templateurl().'/js/yt-extend.js' ?>" type="text/javascript"></script>	
<?php
$doc->addCustomTag('
<script type="text/javascript">
	function MobileRedirectUrl(){
	  window.location.href = document.getElementById("yt-mobilemenu").value;
	}
	
</script>
');

if($yt->getParam('enableGoogleAnalytics')=='1' && $yt->getParam('googleAnalyticsTrackingID')!='' ){
?>  
	<!--For param enableGoogleAnalytics-->
	<script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(["_setAccount", "'.$yt->getParam('googleAnalyticsTrackingID').'"]);
        _gaq.push(["_trackPageview"]);
        (function() {
        var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;
        ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
        var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);
        })();
    </script>		
<?php
}
?>
	<script type="text/javascript">
        var TMPL_NAME = '<?php echo $yt->template; ?>';
    </script>	
    <!--[ if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"> </ script>
    <[endif] -->
