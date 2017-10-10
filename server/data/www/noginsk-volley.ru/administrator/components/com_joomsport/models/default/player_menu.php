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
// No direct access.
defined('_JEXEC') or die;

require(dirname(__FILE__).'/../models.php');

class player_menuJSModel extends JSPRO_Models
{
	
	var $_data = null;

	var $_total = null;
	var $_lists = null;
	var $_pagination = null;
	var $limit = null;
	var $limitstart = null;
	var $seas_id = null;

	function __construct()
	{
		parent::__construct();
		JHtml::_('behavior.framework');
		$mainframe = JFactory::getApplication();
		
		$this->seas_id = JRequest::getVar( 'seas_id', null, 'get', 'int' );
		$this->session =  JFactory::getSession();

	
		if(!empty($this->seas_id) or $this->seas_id == '0'){
			$this->session->set('sid_pl', $this->seas_id);
		}else{
			$this->seas_id = $this->session->get('sid_pl');
		}
		


		// Get the pagination request variables
		$this->limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$this->limitstart	= $mainframe->getUserStateFromRequest( 'com_joomsport.limitstart', 'limitstart', 0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		$this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
		$this->getPagination();
		$this->getData();
	}

	function getData()
	{
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$data = $this->_getList($query);
			if(count($data) == 0){
				$query = "SELECT DISTINCT p.* FROM #__bl_players as p, #__bl_players_team as sp WHERE sp.season_id = '".$this->seas_id."' AND sp.player_id = p.id ORDER BY p.first_name,p.last_name";
				$data = $this->_getList($query);
			}
			$this->_data = $data;
		}

		return $this->_data;
	}

	function getTotal()
	{
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$total = $this->_getListCount($query);
			if($total == 0){
				$query = "SELECT DISTINCT p.* FROM #__bl_players as p, #__bl_players_team as sp WHERE sp.season_id = '".$this->seas_id."' AND sp.player_id = p.id ORDER BY p.first_name,p.last_name";
				$total = $this->_getListCount($query);
			}
			$this->_total = $total;
	
		}

		return $this->_total;
	}
	function _getListCount($query){
		$this->db->setQuery($query);
		$tot = $this->db->loadObjectList();
		return count($tot);
	}
	
	function _getList($query){
		$this->db->setQuery($query, $this->limitstart, $this->limit);
		$tot = $this->db->loadObjectList();
		return $tot;
	}
	
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->limitstart, $this->limit );
		}

		return $this->_pagination;
	}

	function _buildQuery()
	{
		$tfilt_id = JRequest::getVar( 'tfilt_id', 0, 'post', 'int' );
		// $seas_id = JRequest::getVar( 'seas_id', 0, 'get', 'int' );

		$query = ($this->seas_id == 0)?("SELECT DISTINCT * FROM #__bl_players ORDER BY first_name,last_name"):("SELECT DISTINCT * FROM #__bl_players as p, #__bl_season_players as sp WHERE sp.season_id = '".$this->seas_id."' AND sp.player_id = p.id ORDER BY p.first_name,p.last_name");
	// print_r($query);
		return $query;
	}
	
}