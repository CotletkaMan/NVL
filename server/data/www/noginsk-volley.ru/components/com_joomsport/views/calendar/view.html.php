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

require_once __DIR__ . '/../base.php';

class joomsportViewCalendar extends JSBaseView
{
	function display($tpl = null)
	{
		$this->_model->getData();
		$lists = $this->_model->_lists;
		$tmpl = JRequest::getVar( 'tmpl', '', '', 'string' );
		
		$params = $this->_model->_params;

        $p_title = $this->_model->p_title;
        $title = $this->_model->title;

        $pagination =  $this->_model->_pagination;

		$lists["t_single"] = $this->_model->t_single;
		$lists["s_id"] = $this->_model->s_id;
		if(!$tpl){
			$tpl = $this->_model->_layout;
		}
		$this->assignRef('params',		$params);
		$this->assignRef('ptitle',		$p_title);
		$this->assignRef('title',		$title);
		$this->assignRef('tmpl', $tmpl);
		$this->assignRef('lists', $lists);
		$this->assignRef('page',	$pagination);
		
		parent::display($tpl);
		//require_once(dirname(__FILE__).'/tmpl/default'.$tpl.'.php');
	}
}
