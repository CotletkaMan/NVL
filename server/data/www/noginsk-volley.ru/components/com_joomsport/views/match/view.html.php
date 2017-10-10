<?php
/*------------------------------------------------------------------------
# JoomSport Professional 
# ------------------------------------------------------------------------
# BearDev development company 
# Copyright (C) 2011 JoomSport.com. All Rights Reserved.
# @license - http://joomsport.com/news/license.html GNU/GPL
# Websites: http://www.JoomSport.com 
# Technical Support:  Forum - http://joomsport.com/helpdesk/
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once __DIR__ . DIRECTORY_SEPARATOR  . '..' . DIRECTORY_SEPARATOR  . 'base.php';
/**
 * HTML View class for the Registration component
 *
 * @package		Joomla
 * @subpackage	Registration
 * @since 1.0
 */
class joomsportViewmatch extends JSBaseView
{
	function display($tpl = null)
	{
		$this->_model->getData();
		$lists = $this->_model->_lists;
		
		$params = $this->_model->_params;
		
		$lists["t_single"] = $this->_model->t_single;
		$lists["t_type"] = $this->_model->t_type;
		$jver = $this->_model->getVer();
		
		$this->assignRef('params',		$params); 

		$this->assignRef('lists', $lists);
		$this->assignRef('jver', $jver);
		
		parent::display($tpl);
		//require_once(dirname(__FILE__).'/tmpl/default'.$tpl.'.php');
	}
}
