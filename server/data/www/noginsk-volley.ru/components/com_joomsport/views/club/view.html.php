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
jimport( 'joomla.application.component.view');

class joomsportViewclub extends JViewLegacy
{
	var $_model = null;
	function __construct(& $model){
		$this->_model = $model;
		parent::__construct();
	}
	function display($tpl = null)
	{
		$this->_model->getData();
		$lists = $this->_model->_lists;
        $p_title = $this->_model->p_title;
		
		$params = $this->_model->_params;

		$this->assignRef('params',		$params);
        $this->assignRef('ptitle',		$p_title);

		$this->assignRef('lists', $lists);
		parent::display($tpl);
		//require_once(dirname(__FILE__).'/tmpl/default'.$tpl.'.php');
	}
}
