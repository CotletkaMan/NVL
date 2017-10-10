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
class joomsportViewplayer extends JSBaseView
{
	function display($tpl = null)
	{
		$this->_model->getData();
		$lists = $this->_model->_lists;
		
		
		$params = $this->_model->_params;

        $p_title = $this->_model->p_title;
        $title = $this->_model->title;
		
		$this->assignRef('params',		$params);
        $this->assignRef('ptitle',		$p_title);
        $this->assignRef('title',		$title);
		$this->assignRef('lists', $lists);
		$this->assignRef('s_id', $this->_model->s_id);
		$this->assignRef('page', $this->_model->_pagination);
		parent::display($tpl);
		//require_once(dirname(__FILE__).'/tmpl/default'.$tpl.'.php');
	}
}
