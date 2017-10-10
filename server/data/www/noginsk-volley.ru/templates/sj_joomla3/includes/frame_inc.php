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
// Check intralled Yt Plugin
defined( 'YT_FRAMEWORK' ) or die(JTEXT::_("INSTALL_YT_PLUGIN"));
// Object of class YtTemplate(incluedes/yt_template.class.php)
global $yt;
$yt = new YtTemplate($this);
// Get Itemid
$Itemid = JRequest::getInt('Itemid');
// Returns a reference to the global document object
$doc = JFactory::getDocument();
//
$ytrtl = $yt->getParam('direction');
$float1 = ($ytrtl == 'rtl')?' float:right; ':' float:left; ';
$float2 = ($ytrtl == 'rtl')?' float:left; ':' float:right; ';
// BEGIN get param template
$font_name                  = $yt->getParam('font_name');
$fontsize                   = $yt->getParam('fontsize');
$windows_main_layout		= $yt->getParam('default_main_layout');
$override_layouts			= $yt->getParam('override_layouts');
$setGeneratorTag			= $yt->getParam('setGeneratorTag');
$googleWebFont 				= $yt->getParam('googleWebFont');
$googleWebFontTargets		= $yt->getParam('googleWebFontTargets');
// END get param template
// Include Class YtRenderXML
if($windows_main_layout=='-1' || $windows_main_layout=='') die(JTEXT::_("SELECT_LAYOUT_NOW"));
include_once (__DIR__.'/yt_renderxml.php');

$boolOverride = false;
$override_b1 = array(); $override_b1 = explode(' | ', $override_layouts); 
if( count($override_b1)>1 || ($override_b1['0']!='' && count($override_b1)==1) ) { 
	$override_b2 = array();
	for($i=0; $i<count($override_b1); $i++){
		$override_b2[] = explode(':', $override_b1[$i]);
	}
	if( !empty($override_b2) ){
		foreach($override_b2 as $o){
			if($Itemid == $o[0]){$boolOverride = true; $layoutItem = trim($o[1]);}
		}
	}
}

if($boolOverride == true){ // Window Overwrite Layouts
	$yt_render = new YtRenderXML($layoutItem.'.xml');
}else{ // Window Layout default
	$yt_render = new YtRenderXML($windows_main_layout);  
}

// Set GeneratorTag
$this->setGenerator($setGeneratorTag);

/*** Javascript ***/
$doc->addScript($yt->templateurl().'js/yt-script.js');
if(isset($yt_render->arr_TH['script'])){
	foreach($yt_render->arr_TH['script'] as $tagScript){
		$doc->addScript($yt->templateurl().'js/'.$tagScript);;	
	}
}
// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');
if (!defined('SMART_NOCONFLICT')){
	define('SMART_NOCONFLICT', 1);
	$doc->addScript($yt->templateurl().'js/jsmart.noconflict.js');
}
// For param enableJquery
/*if($yt->getParam('enableJquery')=='1'){
	if (!defined('SMART_JQUERY')){
		define('SMART_JQUERY', 1);
		$doc->addScript($yt->templateurl().'js/jquery-1.7.2.min.js');
	}
	if (!defined('SMART_NOCONFLICT')){
		define('SMART_NOCONFLICT', 1);
		$doc->addScript($yt->templateurl().'js/jsmart.noconflict.js');
	}
}*/


?>