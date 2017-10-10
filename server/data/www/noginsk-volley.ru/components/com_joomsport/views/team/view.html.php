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

class joomsportViewteam extends JSBaseView
{
	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$Itemid = JRequest::getInt('Itemid'); 
   
	JHTML::_('behavior.modal', 'a.team-images');
		$pathway  = $mainframe->getPathway();
		$document = JFactory::getDocument();
		$params	= $mainframe->getParams();
	 	// Page Title
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$menu	= $menus->getActive();
		
		$this->_model->getData();
		$lists = $this->_model->_lists;
		$lists["field"] = $this->_model->sortfield;
		$lists["dest"] = $this->_model->sortdest;
		$tid = $this->_model->team_id;
		
		$db		= JFactory::getDBO();
        $title = JFactory::getDocument()->getTitle();
		
		
		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object( $menu )) {
			$menu_params = new JRegistry;//new JParameter( $menu->params );
			if (!$menu_params->get( 'page_title')) {
				$params->set('page_title',	$title);
			}
		} else {
			$params->set('page_title',	$title);
		}
		$document->setTitle( $params->get( 'page_title' ) );
		$pathway->addItem( JText::_( $title ));
		// table league

		$pagination =  $this->_model->_pagination;
		
		
		$this->assignRef('params',		$params); 
		
		$this->assignRef('lists',		$lists);
		$this->assignRef('tid',		$tid);
        $this->assignRef('ptitle',		$lists["team"]->t_name );
		$this->assignRef('page',	$pagination);
		$this->assignRef('page2',	$this->_model->_pagination2);
		$this->assignRef('s_id',		$s_id);
		$this->assignRef('curcal',		$curcal);
		parent::display($tpl);
		//require_once(dirname(__FILE__).'/tmpl/default'.($tpl?"_".$tpl:"").'.php');
	}
}
