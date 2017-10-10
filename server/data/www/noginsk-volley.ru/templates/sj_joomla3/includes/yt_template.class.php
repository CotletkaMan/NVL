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

// Include class Browser
include_once (__DIR__.'/browser.php');
// Class YtTemplate
class YtTemplate {
	var $_tpl = null;
	var $is_mobile = false;
	var $template = '';
	var $layout = '';
	var $browser = '';
	var $type = '';
	var $_params_cookie = array();
	var $float1 = '';
	var $float2 = '';
	var $arr_style = array();
		
	function YtTemplate ($template =null) {
		$this->_tpl = $template;
		$this->template = $template->template;
		$this->type = $this->getType();
		$this->browser = $this->browser();
		$_params_cookie =array(
						  'direction', 
						  'fontsize',
						  'font_name',
						  'sitestyle',
						  'bgcolor',
						  'linkcolor',
						  'textcolor',
						  'header-bgimage',
						  'header-bgcolor',
						  'footer-bgcolor',
						  'footer-bgimage',
						  'default_main_layout',
						  'menustyle',
						  'googleWebFont'
		);
		foreach ($_params_cookie as $k) {
			$this->_params_cookie[$k] = $this->_tpl->params->get($k);
		}		
		$this->getUserSetting(); //print_r($this->_params_cookie); die();
		
		$browser = new Browser();
		$this->is_mobile = $browser->isMobile();
		
		$this->float1 = ($this->getParam('direction') == 'rtl')?'float:right':'float:left';
		$this->float2 = ($this->getParam('direction') == 'rtl')?'float:left':'float:right';
		
	}
	// Get setting of user
	/* Save setting of user on frontend to cookie*/
	function getUserSetting(){
		$exp = time() + 60*60*24*355;
		if (isset($_COOKIE[$this->template.'_tpl']) && $_COOKIE[$this->template.'_tpl'] == $this->template){
			foreach($this->_params_cookie as $k=>$v) {
				$kc = $this->template.'_'.$k;
				
				if( $k == $this->type.'_main_layout' ){
					if( JRequest::getVar('main_layout')!= null ){
						$v = JRequest::getVar('main_layout');
						@setcookie ($kc, $v, $exp, '/');
					}else{
						if (isset($_COOKIE[$kc])){
							$v = $_COOKIE[$kc];
						}
					}
				}else{ 
					if (JRequest::getVar($k)!= null){
						$v = JRequest::getVar($k);
						@setcookie ($kc, $v, $exp, '/');
					}else{
						if (isset($_COOKIE[$kc])){
							$v = $_COOKIE[$kc];
						}
					}
					
				}
				$this->setParam($k, $v); 
			}
			
		}else{
			@setcookie ($this->template.'_tpl', $this->template, $exp, '/');
		}
		return $this;
	}
	// Get param template
	function getParam ($param, $default='') {		
		if (isset($this->_params_cookie[$param])) { 	
			return $this->_params_cookie[$param];
		}
		return $this->_tpl->params->get($param, $default);
	}
	// Set param template
	function setParam ($param, $value) {
		$this->_params_cookie[$param] = $value;
	}
	// Set cookie for param
	function set_cookie($name, $value = "") {
		$expires = time() + 60*60*24*365;
		setcookie($name, $value, $expires,"/","");
	}
	// Get cookie of param
	function get_cookie($item) {
		if (isset($_COOKIE[$item]))
			return urldecode($_COOKIE[$item]);
		else
			return false;
	}
	// Check browser is IE6
	function isIE6 () {
		$msie='/msie\s(5\.[5-9]|[6]\.[0-9]*).*(win)/i';
		return isset($_SERVER['HTTP_USER_AGENT']) &&
			preg_match($msie,$_SERVER['HTTP_USER_AGENT']) &&
			!preg_match('/opera/i',$_SERVER['HTTP_USER_AGENT']);
	}
	// Check version of IE
	/* Return version of IE */
	function ieversion() {
		preg_match('/MSIE ([0-9]\.[0-9])/', $_SERVER['HTTP_USER_AGENT'], $reg);
		if(!isset($reg[1])) {
			return -1;
		} else {
			return floatval($reg[1]);
		}
	}
	// Check browser is IE
	/* return boolean */
	function isIE () {
		$msie='/msie/i';
		return isset($_SERVER['HTTP_USER_AGENT']) &&
			preg_match($msie,$_SERVER['HTTP_USER_AGENT']) &&
			!preg_match('/opera/i',$_SERVER['HTTP_USER_AGENT']);
	}
	// Return name of browser
	function browser () {
		$agent = $_SERVER['HTTP_USER_AGENT'];
		if ( strpos($agent, 'Gecko') )
		{
		   if ( strpos($agent, 'Netscape') )
		   {
		     $browser = 'NS';
		   }
		   else if ( strpos($agent, 'Firefox') )
		   {
		     $browser = 'FF';
		   }
		   else if( strpos($agent, 'Chrome') )
		   {
			   $browser = 'CHRO';
		   }
		   else if( strpos($agent, 'Safari') )
		   {
			   $browser = 'SAFAR';
		   }
		   else
		   {
		     $browser = 'Moz';
		   }
		}
		else if ( strpos($agent, 'MSIE') && !preg_match('/opera/i',$agent) )
		{
			 $msie='/msie\s(7\.[0-9]).*(win)/i';
		   	 if (preg_match($msie,$agent)) $browser = 'IE7';
		   	 else $browser = 'IE6';
		}
		else if ( preg_match('/opera/i',$agent) )
		{
		     $browser = 'OPE';
		}
		else
		{
		   $browser = 'Others';
		}
		return $browser;
	}
	// Return url of site
	function baseurl(){
		 return JURI::base();
	}
	// Return url of template
	function templateurl() {
		return JURI::base()."templates/".$this->template.'/';
	}
	// Check page is home page or not
	function isHomePage(){
		$db  = JFactory::getDBO();  
		$db->setQuery("SELECT home FROM #__menu WHERE id=" . intval(JRequest::getCmd( 'Itemid' )));
		$db->query();
		return  $db->loadResult();
	}
	// Get Copyright Template
	/* Render as mod_footer */
	function getCopyright(){
		$app		= JFactory::getApplication();
		$date		= JFactory::getDate();
		$cur_year	= $date->format('Y');
		$csite_name	= $app->getCfg('sitename');
		
		if (JString::strpos(JText :: _('MOD_FOOTER_LINE1'), '%date%')) {
			$line1 = str_replace('%date%', $cur_year, JText :: _('MOD_FOOTER_LINE1'));
		}
		else {
			$line1 = JText :: _('MOD_FOOTER_LINE1');
		}
		
		if (JString::strpos($line1, '%sitename%')) {
			$lineone = str_replace('%sitename%', $csite_name, $line1);
		}
		else {
			$lineone = $line1;
		}
		?>
        <!-- 
        You CAN NOT remove (or unreadable) those links without permission. Removing the link and template sponsor Please visit smartaddons.com or contact with e-mail (contact@ytcvn.com) If you don't want to link back to smartaddons.com, you can always pay a link removal donation. This will allow you to use the template link free on one domain name. Also, kindly send me the site's url so I can include it on my list of verified users. 
        -->
        <div class="footer1"><?php echo $lineone; ?>   <a target="_blank" title="" href="/"></a></div>
        <div class="footer2"><?php echo JText::_('MOD_FOOTER_LINE2'); ?></div>
        <?php
	}
	// Get link of footer, link for template of yt framework
	function getLinkFooter(){
		?>
        <div class="validate lang">
        	<ul>
            	<li class="first"><a target="_blank" href="http://jigsaw.w3.org/css-validator/validator?profile=css3"><span>CSS Valid</span></a></li>
                <li><a target="_blank" href="http://validator.w3.org/check/referer"><span>XHTML Valid</span></a></li>
                <li><a href="?direction=ltr"><span>LTR</span></a></li>
                <li class="last"><a href="?direction=rtl"><span>RTL</span></a></li>
            </ul>
        </div>
        <?php
	}
	// Render logo
	function getLogo(){
		$app = JFactory::getApplication();
		if ($this->getParam('logoType')=='image'):  
			if($this->getParam('overrideLogoImage')!=''):
				$url = $this->baseurl().$this->getParam('overrideLogoImage');
			else:
				if(is_file($this->templateurl().'images/'.$this->getParam('sitestyle').'/logo.png')){
					$url = $this->templateurl().'images/'.$this->getParam('sitestyle').'/logo.png';
				}else{
					$url = $this->templateurl().'images/logo.png';
				}			
			endif;	
		?>
            <h1 class="logo">
                <a href="index.php" title="<?php echo $this->getParam('logoText'); ?>">
                	<img alt="<?php echo $this->getParam('logoText'); ?>" src="<?php echo $url; ?>"/>
                </a>
            </h1>
        <?php					
		else:
            $logoText = (trim($this->getParam('logoText'))=='') ? $app->getCfg('sitename') : $this->getParam('logoText');
            $sloganText = (trim($this->getParam('sloganText'))=='') ? JText::_('SITE SLOGAN') : $this->getParam('sloganText');	?>
            <h1 class="logo-text">
                <a href="index.php" title="<?php echo $app->getCfg('sitename'); ?>"><span><?php echo $logoText; ?></span></a>
            </h1>
            <p class="site-slogan"><?php echo $sloganText;?></p>
        <?php 
		endif;
	}
	// Render menu
	function getMenu(){
		$menubase = dirname(dirname(__FILE__)). '/menusys';
		include_once $menubase . '/ytloader.php';
		$browser = new Browser();
		$templateMenuBase = new YTMenuBase(
		array(
			'menutype'	=> $this->getParam('menutype'),
			'menustyle'	=> $this->getParam('menustyle'),
			'moofxduration'	=> $this->getParam('moofxduration'),
			'moofx'		=> $this->getParam('moofx'),
			'jsdropline'=> $this->getParam('jsdropline', 0),
			'activeslider'=> $this->getParam('activeslider', 0),
			'startlevel'=> $this->getParam('startlevel',0),
			'endlevel'	=> $this->getParam('endlevel',9),
			'direction'	=> $this->getParam('direction'),
			'basepath'	=> str_replace('\\', '/', $menubase)
		));
		$templateMenuBase->getMenu()->getContent();	
		$templateMenuBase = new YTMenuBase(
		array(
			'menutype'	=> $this->getParam('menutype'),
			'menustyle'	=> 'mobile',
			'moofxduration'	=> $this->getParam('moofxduration'),
			'moofx'		=> $this->getParam('moofx'),
			'jsdropline'=> $this->getParam('jsdropline', 0),
			'activeslider'=> $this->getParam('activeslider', 0),
			'startlevel'=> $this->getParam('startlevel',0),
			'endlevel'	=> $this->getParam('endlevel',9),
			'direction'	=> $this->getParam('direction'),
			'basepath'	=> str_replace('\\', '/', $menubase)
		));
		$templateMenuBase->getMenu()->getContent();
	}
	// Get control fontsize
	function getControlFontSize(){
		?>
        
        <ul class="yt-fontsize clearfix">
        	<li class="label">Font size:</li>
            <li class="dec" style="cursor: pointer;" title="Decrease Text Size" onclick="switchFontSize('<?php echo $this->template."_fontsize";?>','dec'); return false;">Decrease</li>
            <li class="reset" title="Reset Text Size" style="cursor: pointer;" onclick="switchFontSize('<?php echo $this->template."_fontsize";?>',<?php echo $this->_tpl->params->get('fontsize');?>); return false;">Reset</li>
            <li class="inc" title="Increase Text Size" style="cursor: pointer;" onclick="switchFontSize('<?php echo $this->template."_fontsize";?>','inc'); return false;">Increase</li>
         </ul>
         <script type="text/javascript">
		 	var CurrentFontSize=parseInt('<?php echo $this->getParam('fontsize');?>');
			var DefaultFontSize=parseInt('<?php echo $this->_tpl->params->get('fontsize');?>')
         </script>
        <?php
	}
	// render possition has attribute group in positions of block
	function renPositionsGroup($position, $type='', $special = null){
		$elStyle   = '';
		$class     = '';
		$more_attr = '';
		$doc       = JFactory::getDocument();
		// Element style
		$elStyle  .= ($position['height']!='')?'height:'.$position['height'].';':'';
		if ( $elStyle!='' ) {
			$elStyle = ' style="'.$elStyle.'"';
		}
		if($position['group']=='main'){
			$prefx = $special['mainprefix'];
		}else{
			$prefx = '';
		}
		// Element class
		if(isset($position[$prefx.'class']) && $position[$prefx.'class']!=''){
			$class .= ' class="'.$position[$prefx.'class'].'"';
		}
		// Element attribute
		
		$more_attr .= (isset($position[$prefx.'data-wide']))?' data-wide="'.$position[$prefx.'data-wide'].'"':'';
		$more_attr .= (isset($position[$prefx.'data-normal']))?' data-normal="'.$position[$prefx.'data-normal'].'"':'';
		$more_attr .= (isset($position[$prefx.'data-tablet']))?' data-tablet="'.$position[$prefx.'data-tablet'].'"':'';
		$more_attr .= (isset($position[$prefx.'data-stablet']))?' data-stablet="'.$position[$prefx.'data-stablet'].'"':'';
		$more_attr .= (isset($position[$prefx.'data-mobile']))?' data-mobile="'.$position[$prefx.'data-mobile'].'"':'';
		
		if ( $position['type'] == 'modules' ) { 
			$has_suffix = false;
			if ( isset($position['group']) && $position['group'] == 'main' && $this->getParam('layoutsuffix') != '' )
				if ( $doc->countModules($position['value'] . '-' . $this->getParam('layoutsuffix')) )
					$has_suffix = true;
			if ( $has_suffix ) {
				$this->renModulePos($position, $elStyle, $class, $more_attr, $position['value'] . '-' . $this->getParam('layoutsuffix'));
			} else {
				if ( $doc->countModules($position['value']) )
					$this->renModulePos($position, $elStyle, $class, $more_attr, '');
			}	
		} elseif ($position['type'] == 'component' && $type=='main'){ 
			if ( $this->getParam('hideMainContent')=='1'
				&& $this->isHomePage() ){
			?>
            	<span style="display:none">Hide Main content block</span>
            <?php
			} else {
				$this->renComponent($elStyle, $class, $more_attr);
			}
		} elseif ( $position['type'] == 'html' ) { 
			$this->renHTMLPos($position, $elStyle, $class, $more_attr);
		} elseif ( $position['type'] == 'feature' ) {
			$this->renFeaturePos($position, $elStyle, $class, $more_attr);
		} elseif ( $position['type']=='message' ) { ?>
        	<div<?php echo $class; ?>>
				<jdoc:include type="message" />
            </div>
        <?php
		}
	}
	// render possition no attribute group in positions of block nomarl
	function renPositionsNormal($positions, $countModules){
		$doc = JFactory::getDocument();
		$countend = 0;
		foreach( $positions as $position ){
			$elStyle   = '';
			$class     = '';
			$more_attr = '';
			// Element style
			
			if($elStyle!="")
				$elStyle = " style='".$elStyle."'";
			if($position['type'] == 'modules'){
				if($doc->countModules($position['value'])>0)
					$countend ++;
			}else{
				$countend ++;
			}
			// Element class
			if($position['class']!=''){
				$class .= ' class="'.$position['class'].'"';
			}
			// Element attribute
			$more_attr .= (isset($position['data-wide']))?' data-wide="'.$position['data-wide'].'"':'';
			$more_attr .= (isset($position['data-normal']))?' data-normal="'.$position['data-normal'].'"':'';
			$more_attr .= (isset($position['data-tablet']))?' data-tablet="'.$position['data-tablet'].'"':'';
			$more_attr .= (isset($position['data-stablet']))?' data-stablet="'.$position['data-stablet'].'"':'';
			$more_attr .= (isset($position['data-mobile']))?' data-mobile="'.$position['data-mobile'].'"':'';
			
			if($position['type'] == 'modules'){
				if( $doc->countModules($position['value']) )
					$this->renModulePos($position, $elStyle, $class, $more_attr);
			}elseif($position['type'] == 'html'){
				$this->renHTMLPos($position, $elStyle, $class, $more_attr);
			}elseif($position['type'] == 'feature'){
				$this->renFeaturePos($position, $elStyle, $class, $more_attr);
			}elseif($position['type']=='message'){ ?>
            	<div<?php echo $class; ?>>
                	<jdoc:include type="message" />
                </div>
            <?php
            }
		}
	}
	// render possition no attribute group in positions of block content
	function renPositionsContentNoGroup($position){
		$doc       = &JFactory::getDocument();
		$elStyle   = '';
		$class     = '';
		$more_attr = '';
		// Element style
		if($position['height']!=""){
			if($elStyle!=""){
				$elStyle .= 'height:'.$position['height'].";";
			}else{
				$elStyle .= 'height:'.$position['height'].";";
			}
		}
		if($elStyle!="")
			$elStyle = " style='".$elStyle."'";
		// Element class
		if($position['class']!=''){
			$class .= ' class="'.$position['class'].'"';
		}
		// Element attribute
		$more_attr .= (isset($position['data-wide']))?' data-wide="'.$position['data-wide'].'"':'';
		$more_attr .= (isset($position['data-normal']))?' data-normal="'.$position['data-normal'].'"':'';
		$more_attr .= (isset($position['data-tablet']))?' data-tablet="'.$position['data-tablet'].'"':'';
		$more_attr .= (isset($position['data-stablet']))?' data-stablet="'.$position['data-stablet'].'"':'';
		$more_attr .= (isset($position['data-mobile']))?' data-mobile="'.$position['data-mobile'].'"':'';
		
		if($position['type'] == 'modules'){
			if( $doc->countModules($position['value']) ){
				$this->renModulePos($position, $elStyle, $class);
			}
		}elseif($position['type'] == 'html'){
			$this->renHTMLPos($position, $elStyle, $class, $more_attr);
		}elseif($position['type'] == 'feature'){
			$this->renFeaturePos($position, $elStyle, $class, $more_attr);
		}elseif($position['type']=='message'){ ?>
        	<div<?php echo $class; ?>>
				<jdoc:include type="message" />
            </div>
		<?php
		}
	}
	//render position with type: modules
	function renModulePos ($position, $elementstyle, $elementclass='', $more_attr='', $positionnamesuffix='', $yorn='1' ) {
		if($yorn == '1'){
		?>
		<div id="<?php echo $position['value']; ?>"<?php echo $elementstyle; ?><?php echo $elementclass; ?><?php echo $more_attr;?>>
			<!--<div class="yt-position-inner">-->
				<jdoc:include type="modules" name="<?php echo ($positionnamesuffix=='')?$position['value']:$positionnamesuffix; ?>" style="<?php echo $position['style'];?>" />
			<!--</div>-->
		</div>
		<?php
		}else{ ?>
			<jdoc:include type="modules" name="<?php echo ($positionnamesuffix=='')?$position['value']:$positionnamesuffix; ?>" style="<?php echo $position['style'];?>" />
        <?php    
		}
	}
	//render position with type: html
	function renHTMLPos ($position, $elementstyle, $elementclass='', $more_attr='' ) {
		?>
		<div<?php echo $elementclass; ?><?php echo $elementstyle; ?><?php echo $more_attr; ?>>
			<?php echo $position['value']; ?>
        </div>
		<?php
	}
	//render position with type: feature
	function renFeaturePos ($position, $elementstyle, $elementclass='', $more_attr='' ) {
		?>
		<div id="<?php echo "yt_".strtolower(substr($position['value'], 1))."position";?>"<?php echo $elementclass; ?><?php echo $elementstyle; ?><?php echo $more_attr; ?>>
			<?php
            if($position['value'] == '@logo'){
                echo $this->getLogo();
            }elseif($position['value'] == '@fontsize'){
                echo $this->getControlFontSize();
            }elseif($position['value'] == '@menu'){
                 $this->getMenu();
            }elseif($position['value'] == '@linkFooter'){
                $this->getLinkFooter();
            }elseif($position['value'] == '@copyright'){
                $this->getCopyright();
            }
            ?>
        </div>
		<?php
	}
	//render position with type: component
	function renComponent ($elementstyle, $elementclass='', $more_attr='') {
		?>
        <div id="yt_component"<?php echo $elementclass; ?><?php echo $elementstyle; ?><?php echo $more_attr; ?>>
            <div class="component-inner"><div class="component-inner2">
                <jdoc:include type="component" />
            </div></div>
        </div>
		<?php 
	}
	// get type device
	/* return type device*/
	function getType () {
		global $Itemid, $option;
		$type = 'default';
		$mobile = $this->mobile_device_detect();
		if ($mobile) $type =  $mobile;
		return $type;
		
	}
	function mobile_device_detect () {
		$special = array('jigs', 'w3c ', 'w3c-', 'w3c_');
		if (in_array(strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4)), $special)) return false;
		return $this->mobile_detect_layout('iphone','android','opera','blackberry','palm','windows');
	}
	
	function mobile_detect_layout($iphone=true,$android=true,$opera=true,$blackberry=true,$palm=true,$windows=true,$mobileredirect=false,$desktopredirect=false){

		  $mobile_browser   = false; // set mobile browser as false till we can prove otherwise
		  $user_agent       = $_SERVER['HTTP_USER_AGENT']; // get the user agent value - this should be cleaned to ensure no nefarious input gets executed
		  $accept           = $_SERVER['HTTP_ACCEPT']; // get the content accept value - this should be cleaned to ensure no nefarious input gets executed

	  switch(true){ // using a switch against the following statements which could return true is more efficient than the previous method of using if statements
	
		case (preg_match('/ipod/i',$user_agent)||preg_match('/iphone/i',$user_agent)); // we find the words iphone or ipod in the user agent
		  $mobile_browser = $iphone; // mobile browser is either true or false depending on the setting of iphone when calling the function
		  $status = 'Apple';
		  if(substr($iphone,0,4)=='http'){ // does the value of iphone resemble a url
			$mobileredirect = $iphone; // set the mobile redirect url to the url value stored in the iphone value
		  } // ends the if for iphone being a url
		break; // break out and skip the rest if we've had a match on the iphone or ipod
	
		case (preg_match('/android/i',$user_agent));  // we find android in the user agent
		  $mobile_browser = $android; // mobile browser is either true or false depending on the setting of android when calling the function
		  $status = 'Android';
		  if(substr($android,0,4)=='http'){ // does the value of android resemble a url
			$mobileredirect = $android; // set the mobile redirect url to the url value stored in the android value
		  } // ends the if for android being a url
		break; // break out and skip the rest if we've had a match on android
	
		case (preg_match('/opera mini/i',$user_agent)); // we find opera mini in the user agent
		  $mobile_browser = $opera; // mobile browser is either true or false depending on the setting of opera when calling the function
		  $status = 'Opera';
		  if(substr($opera,0,4)=='http'){ // does the value of opera resemble a rul
			$mobileredirect = $opera; // set the mobile redirect url to the url value stored in the opera value
		  } // ends the if for opera being a url
		break; // break out and skip the rest if we've had a match on opera
	
		case (preg_match('/blackberry/i',$user_agent)); // we find blackberry in the user agent
		  $mobile_browser = $blackberry; // mobile browser is either true or false depending on the setting of blackberry when calling the function
		  $status = 'Blackberry';
		  if(substr($blackberry,0,4)=='http'){ // does the value of blackberry resemble a rul
			$mobileredirect = $blackberry; // set the mobile redirect url to the url value stored in the blackberry value
		  } // ends the if for blackberry being a url
		break; // break out and skip the rest if we've had a match on blackberry
	
		case (preg_match('/(pre\/|palm os|palm|hiptop|avantgo|fennec|plucker|xiino|blazer|elaine)/i',$user_agent)); // we find palm os in the user agent - the i at the end makes it case insensitive
		  $mobile_browser = $palm; // mobile browser is either true or false depending on the setting of palm when calling the function
		  $status = 'Palm';
		  if(substr($palm,0,4)=='http'){ // does the value of palm resemble a rul
			$mobileredirect = $palm; // set the mobile redirect url to the url value stored in the palm value
		  } // ends the if for palm being a url
		break; // break out and skip the rest if we've had a match on palm os
	
		case (preg_match('/(iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile)/i',$user_agent)); // we find windows mobile in the user agent - the i at the end makes it case insensitive
		  $mobile_browser = $windows; // mobile browser is either true or false depending on the setting of windows when calling the function
		  $status = 'Windows Smartphone';
		  if(substr($windows,0,4)=='http'){ // does the value of windows resemble a rul
			$mobileredirect = $windows; // set the mobile redirect url to the url value stored in the windows value
		  } // ends the if for windows being a url
		break; // break out and skip the rest if we've had a match on windows
	
		case (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i',$user_agent)); // check if any of the values listed create a match on the user agent - these are some of the most common terms used in agents to identify them as being mobile devices - the i at the end makes it case insensitive
		  $mobile_browser = true; // set mobile browser to true
		  $status = 'Mobile matched on piped preg_match';
		break; // break out and skip the rest if we've preg_match on the user agent returned true
	
		case ((strpos($accept,'text/vnd.wap.wml')>0)||(strpos($accept,'application/vnd.wap.xhtml+xml')>0)); // is the device showing signs of support for text/vnd.wap.wml or application/vnd.wap.xhtml+xml
		  $mobile_browser = true; // set mobile browser to true
		  $status = 'Mobile matched on content accept header';
		break; // break out and skip the rest if we've had a match on the content accept headers
	
		case (isset($_SERVER['HTTP_X_WAP_PROFILE'])||isset($_SERVER['HTTP_PROFILE'])); // is the device giving us a HTTP_X_WAP_PROFILE or HTTP_PROFILE header - only mobile devices would do this
		  $mobile_browser = true; // set mobile browser to true
		  $status = 'Mobile matched on profile headers being set';
		break; // break out and skip the final step if we've had a return true on the mobile specfic headers
	
		case (in_array(strtolower(substr($user_agent,0,4)),array('1207'=>'1207','3gso'=>'3gso','4thp'=>'4thp','501i'=>'501i','502i'=>'502i','503i'=>'503i','504i'=>'504i','505i'=>'505i','506i'=>'506i','6310'=>'6310','6590'=>'6590','770s'=>'770s','802s'=>'802s','a wa'=>'a wa','acer'=>'acer','acs-'=>'acs-','airn'=>'airn','alav'=>'alav','asus'=>'asus','attw'=>'attw','au-m'=>'au-m','aur '=>'aur ','aus '=>'aus ','abac'=>'abac','acoo'=>'acoo','aiko'=>'aiko','alco'=>'alco','alca'=>'alca','amoi'=>'amoi','anex'=>'anex','anny'=>'anny','anyw'=>'anyw','aptu'=>'aptu','arch'=>'arch','argo'=>'argo','bell'=>'bell','bird'=>'bird','bw-n'=>'bw-n','bw-u'=>'bw-u','beck'=>'beck','benq'=>'benq','bilb'=>'bilb','blac'=>'blac','c55/'=>'c55/','cdm-'=>'cdm-','chtm'=>'chtm','capi'=>'capi','cond'=>'cond','craw'=>'craw','dall'=>'dall','dbte'=>'dbte','dc-s'=>'dc-s','dica'=>'dica','ds-d'=>'ds-d','ds12'=>'ds12','dait'=>'dait','devi'=>'devi','dmob'=>'dmob','doco'=>'doco','dopo'=>'dopo','el49'=>'el49','erk0'=>'erk0','esl8'=>'esl8','ez40'=>'ez40','ez60'=>'ez60','ez70'=>'ez70','ezos'=>'ezos','ezze'=>'ezze','elai'=>'elai','emul'=>'emul','eric'=>'eric','ezwa'=>'ezwa','fake'=>'fake','fly-'=>'fly-','fly_'=>'fly_','g-mo'=>'g-mo','g1 u'=>'g1 u','g560'=>'g560','gf-5'=>'gf-5','grun'=>'grun','gene'=>'gene','go.w'=>'go.w','good'=>'good','grad'=>'grad','hcit'=>'hcit','hd-m'=>'hd-m','hd-p'=>'hd-p','hd-t'=>'hd-t','hei-'=>'hei-','hp i'=>'hp i','hpip'=>'hpip','hs-c'=>'hs-c','htc '=>'htc ','htc-'=>'htc-','htca'=>'htca','htcg'=>'htcg','htcp'=>'htcp','htcs'=>'htcs','htct'=>'htct','htc_'=>'htc_','haie'=>'haie','hita'=>'hita','huaw'=>'huaw','hutc'=>'hutc','i-20'=>'i-20','i-go'=>'i-go','i-ma'=>'i-ma','i230'=>'i230','iac'=>'iac','iac-'=>'iac-','iac/'=>'iac/','ig01'=>'ig01','im1k'=>'im1k','inno'=>'inno','iris'=>'iris','jata'=>'jata','java'=>'java','kddi'=>'kddi','kgt'=>'kgt','kgt/'=>'kgt/','kpt '=>'kpt ','kwc-'=>'kwc-','klon'=>'klon','lexi'=>'lexi','lg g'=>'lg g','lg-a'=>'lg-a','lg-b'=>'lg-b','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-f'=>'lg-f','lg-g'=>'lg-g','lg-k'=>'lg-k','lg-l'=>'lg-l','lg-m'=>'lg-m','lg-o'=>'lg-o','lg-p'=>'lg-p','lg-s'=>'lg-s','lg-t'=>'lg-t','lg-u'=>'lg-u','lg-w'=>'lg-w','lg/k'=>'lg/k','lg/l'=>'lg/l','lg/u'=>'lg/u','lg50'=>'lg50','lg54'=>'lg54','lge-'=>'lge-','lge/'=>'lge/','lynx'=>'lynx','leno'=>'leno','m1-w'=>'m1-w','m3ga'=>'m3ga','m50/'=>'m50/','maui'=>'maui','mc01'=>'mc01','mc21'=>'mc21','mcca'=>'mcca','medi'=>'medi','meri'=>'meri','mio8'=>'mio8','mioa'=>'mioa','mo01'=>'mo01','mo02'=>'mo02','mode'=>'mode','modo'=>'modo','mot '=>'mot ','mot-'=>'mot-','mt50'=>'mt50','mtp1'=>'mtp1','mtv '=>'mtv ','mate'=>'mate','maxo'=>'maxo','merc'=>'merc','mits'=>'mits','mobi'=>'mobi','motv'=>'motv','mozz'=>'mozz','n100'=>'n100','n101'=>'n101','n102'=>'n102','n202'=>'n202','n203'=>'n203','n300'=>'n300','n302'=>'n302','n500'=>'n500','n502'=>'n502','n505'=>'n505','n700'=>'n700','n701'=>'n701','n710'=>'n710','nec-'=>'nec-','nem-'=>'nem-','newg'=>'newg','neon'=>'neon','netf'=>'netf','noki'=>'noki','nzph'=>'nzph','o2 x'=>'o2 x','o2-x'=>'o2-x','opwv'=>'opwv','owg1'=>'owg1','opti'=>'opti','oran'=>'oran','p800'=>'p800','pand'=>'pand','pg-1'=>'pg-1','pg-2'=>'pg-2','pg-3'=>'pg-3','pg-6'=>'pg-6','pg-8'=>'pg-8','pg-c'=>'pg-c','pg13'=>'pg13','phil'=>'phil','pn-2'=>'pn-2','pt-g'=>'pt-g','palm'=>'palm','pana'=>'pana','pire'=>'pire','pock'=>'pock','pose'=>'pose','psio'=>'psio','qa-a'=>'qa-a','qc-2'=>'qc-2','qc-3'=>'qc-3','qc-5'=>'qc-5','qc-7'=>'qc-7','qc07'=>'qc07','qc12'=>'qc12','qc21'=>'qc21','qc32'=>'qc32','qc60'=>'qc60','qci-'=>'qci-','qwap'=>'qwap','qtek'=>'qtek','r380'=>'r380','r600'=>'r600','raks'=>'raks','rim9'=>'rim9','rove'=>'rove','s55/'=>'s55/','sage'=>'sage','sams'=>'sams','sc01'=>'sc01','sch-'=>'sch-','scp-'=>'scp-','sdk/'=>'sdk/','se47'=>'se47','sec-'=>'sec-','sec0'=>'sec0','sec1'=>'sec1','semc'=>'semc','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','sk-0'=>'sk-0','sl45'=>'sl45','slid'=>'slid','smb3'=>'smb3','smt5'=>'smt5','sp01'=>'sp01','sph-'=>'sph-','spv '=>'spv ','spv-'=>'spv-','sy01'=>'sy01','samm'=>'samm','sany'=>'sany','sava'=>'sava','scoo'=>'scoo','send'=>'send','siem'=>'siem','smar'=>'smar','smit'=>'smit','soft'=>'soft','sony'=>'sony','t-mo'=>'t-mo','t218'=>'t218','t250'=>'t250','t600'=>'t600','t610'=>'t610','t618'=>'t618','tcl-'=>'tcl-','tdg-'=>'tdg-','telm'=>'telm','tim-'=>'tim-','ts70'=>'ts70','tsm-'=>'tsm-','tsm3'=>'tsm3','tsm5'=>'tsm5','tx-9'=>'tx-9','tagt'=>'tagt','talk'=>'talk','teli'=>'teli','topl'=>'topl','hiba'=>'hiba','up.b'=>'up.b','upg1'=>'upg1','utst'=>'utst','v400'=>'v400','v750'=>'v750','veri'=>'veri','vk-v'=>'vk-v','vk40'=>'vk40','vk50'=>'vk50','vk52'=>'vk52','vk53'=>'vk53','vm40'=>'vm40','vx98'=>'vx98','virg'=>'virg','vite'=>'vite','voda'=>'voda','vulc'=>'vulc','w3c '=>'w3c ','w3c-'=>'w3c-','wapj'=>'wapj','wapp'=>'wapp','wapu'=>'wapu','wapm'=>'wapm','wig '=>'wig ','wapi'=>'wapi','wapr'=>'wapr','wapv'=>'wapv','wapy'=>'wapy','wapa'=>'wapa','waps'=>'waps','wapt'=>'wapt','winc'=>'winc','winw'=>'winw','wonu'=>'wonu','x700'=>'x700','xda2'=>'xda2','xdag'=>'xdag','yas-'=>'yas-','your'=>'your','zte-'=>'zte-','zeto'=>'zeto','acs-'=>'acs-','alav'=>'alav','alca'=>'alca','amoi'=>'amoi','aste'=>'aste','audi'=>'audi','avan'=>'avan','benq'=>'benq','bird'=>'bird','blac'=>'blac','blaz'=>'blaz','brew'=>'brew','brvw'=>'brvw','bumb'=>'bumb','ccwa'=>'ccwa','cell'=>'cell','cldc'=>'cldc','cmd-'=>'cmd-','dang'=>'dang','doco'=>'doco','eml2'=>'eml2','eric'=>'eric','fetc'=>'fetc','hipt'=>'hipt','http'=>'http','ibro'=>'ibro','idea'=>'idea','ikom'=>'ikom','inno'=>'inno','ipaq'=>'ipaq','jbro'=>'jbro','jemu'=>'jemu','java'=>'java','jigs'=>'jigs','kddi'=>'kddi','keji'=>'keji','kyoc'=>'kyoc','kyok'=>'kyok','leno'=>'leno','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-g'=>'lg-g','lge-'=>'lge-','libw'=>'libw','m-cr'=>'m-cr','maui'=>'maui','maxo'=>'maxo','midp'=>'midp','mits'=>'mits','mmef'=>'mmef','mobi'=>'mobi','mot-'=>'mot-','moto'=>'moto','mwbp'=>'mwbp','mywa'=>'mywa','nec-'=>'nec-','newt'=>'newt','nok6'=>'nok6','noki'=>'noki','o2im'=>'o2im','opwv'=>'opwv','palm'=>'palm','pana'=>'pana','pant'=>'pant','pdxg'=>'pdxg','phil'=>'phil','play'=>'play','pluc'=>'pluc','port'=>'port','prox'=>'prox','qtek'=>'qtek','qwap'=>'qwap','rozo'=>'rozo','sage'=>'sage','sama'=>'sama','sams'=>'sams','sany'=>'sany','sch-'=>'sch-','sec-'=>'sec-','send'=>'send','seri'=>'seri','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','siem'=>'siem','smal'=>'smal','smar'=>'smar','sony'=>'sony','sph-'=>'sph-','symb'=>'symb','t-mo'=>'t-mo','teli'=>'teli','tim-'=>'tim-','tosh'=>'tosh','treo'=>'treo','tsm-'=>'tsm-','upg1'=>'upg1','upsi'=>'upsi','vk-v'=>'vk-v','voda'=>'voda','vx52'=>'vx52','vx53'=>'vx53','vx60'=>'vx60','vx61'=>'vx61','vx70'=>'vx70','vx80'=>'vx80','vx81'=>'vx81','vx83'=>'vx83','vx85'=>'vx85','wap-'=>'wap-','wapa'=>'wapa','wapi'=>'wapi','wapp'=>'wapp','wapr'=>'wapr','webc'=>'webc','whit'=>'whit','winw'=>'winw','wmlb'=>'wmlb','xda-'=>'xda-',))); // check against a list of trimmed user agents to see if we find a match
		  $mobile_browser = true; // set mobile browser to true
		  $status = 'Mobile matched on in_array';
		break; // break even though it's the last statement in the switch so there's nothing to break away from but it seems better to include it than exclude it
	
		default;
		  $mobile_browser = false; // set mobile browser to false
		  $status = 'Desktop / full capability browser';
		break; // break even though it's the last statement in the switch so there's nothing to break away from but it seems better to include it than exclude it
	
	  } // ends the switch
	
	  // tell adaptation services (transcoders and proxies) to not alter the content based on user agent as it's already being managed by this script
	//  header('Cache-Control: no-transform'); // http://mobiforge.com/developing/story/setting-http-headers-advise-transcoding-proxies
	//  header('Vary: User-Agent, Accept'); // http://mobiforge.com/developing/story/setting-http-headers-advise-transcoding-proxies
	
	  // if redirect (either the value of the mobile or desktop redirect depending on the value of $mobile_browser) is true redirect else we return the status of $mobile_browser
	  if($redirect = ($mobile_browser==true) ? $mobileredirect : $desktopredirect){
		header('Location: '.$redirect); // redirect to the right url for this device
		exit;
	  }else{
		return $mobile_browser; // will return either true or false
	  }
	
	} // ends function mobile_device_detect
	
	
}

?>