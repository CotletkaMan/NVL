<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemYt extends JPlugin {
	function plgSystemYt(&$subject, $pluginconfig) {
		/*global $app;*/
		define('YT_FRAMEWORK', 1);
		parent::__construct($subject, $pluginconfig);
		
	}
	function onAfterInitialise() {
		include_once dirname(__FILE__).'/includes/libs/resize/tool.php';
	}
	function onContentPrepareForm($form, $data){
		if($form->getName()=='com_menus.item'){
			JForm::addFormPath(JPATH_SITE.'/plugins/system/yt/includes/libs/menu/params');
			$form->loadFile('params', false);
		}
	}
	function onAfterRender() {
		global $app;		 //var_dump($app); die();
		$document = JFactory::getDocument();
		$option   = JRequest::getVar('option', '');
		$task	  = JRequest::getVar('task', '');
		
		if($app->isSite() && $document->_type == 'html' && !$app->getCfg('offline') && (!($option == 'com_content' && $task =='edit'))){
			require_once('includes/libs/yt-minify.php');
			$yt_mini = new YT_Minify;
			
			if($app->getTemplate(true)->params->get('optimizeCSS', 0)) $yt_mini->optimizecss();
			if($app->getTemplate(true)->params->get('optimizeJS', 0)) $yt_mini->optimizejs();
			if($app->getTemplate(true)->params->get('optimizeHTML', 0)) $yt_mini->optimizehtml();
			
			$type	= JRequest::getVar('type');
			$action = JRequest::getVar('action');
			if($type == 'plugin' && $action == 'clearCache')
				$yt_mini->clearCache();
				
		}else{
		
			$uri 	= str_replace(JPATH_SITE, JURI::base(), dirname(__FILE__));
			$uri 	= str_replace(DIRECTORY_SEPARATOR , '/', str_replace("/administrator/", "", $uri));  //die($uri);
			$html   = "";
			$html 	= '<script language="javascript" type="text/javascript" src="'.$uri.'/includes/assets/clearcache.js"></script>';
			if($this->params->get('show_sjhelp', 0)==1){
				require_once('includes/assets/menu-sjhelp.php');	
			}
			$buffer = JResponse::getBody ();
			$buffer = preg_replace('/<\/head>/', $html . "\n</head>", $buffer);
			JResponse::setBody($buffer);							
		}

	}
}