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

class group_listJSModel extends JSPRO_Models
{
	
	var $_data = null;
	var $_lists = null;
	var $_total = null;

	var $_pagination = null;
	var $limit = null;
	var $limitstart = null;
	var $season_id = null;

	function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();

		// Get the pagination request variables
		$this->limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$this->limitstart	= $mainframe->getUserStateFromRequest( 'com_joomsport.limitstart_groups', 'limitstart', 0, 'int' );
		$this->season_id	= $mainframe->getUserStateFromRequest( 'com_joomsport.s_id', 's_id', 0, 'int' );
		// In case limit has been changed, adjust limitstart accordingly
		$this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
		$this->getPagination();
		
		$this->getData();
		$this->getFilterGr();
	}

	function getData()
	{
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			
			$this->_data = $this->_getList($query, $this->limitstart, $this->limit);
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
		}
		
		return $this->_data;
	}

	function getTotal()
	{
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}
	function _getListCount($query){
		$this->db->setQuery($query);
		$tot = $this->db->loadObjectList();
		return count($tot);
	}
	
	function _getList($query,$limitstart,$limit){
		$this->db->setQuery($query,$limitstart,$limit);
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
		
		$orderby	= $this->_buildContentOrderBy();
		
		$query = "SELECT g.*,s.s_name,t.name as t_name
		            FROM #__bl_seasons as s,#__bl_tournament as t,#__bl_groups as g
		            WHERE s.s_id=g.s_id AND s.t_id=t.id ".($this->season_id?" AND s.s_id=".$this->season_id:"");
	
		$query .= $orderby;
		

		return $query;
	}
	
	function getFilterGr(){

		$javascript = 'onchange = "document.adminForm.limitstart.value=0;document.adminForm.submit();"';                                                            //UPDATE//
        $query = "SELECT s.s_id as id, s.s_name
                    FROM #__bl_tournament as t, #__bl_seasons as s
                    WHERE s.t_id = t.id AND s.s_groups = '1' AND t.id={tourid}
                    ORDER BY t.name, s.s_name";

        $this->_lists['tourn'] = $this->getSeasDList($this->season_id, $query, 0, $javascript,1, 's_id');
	}

	function _buildContentOrderBy()
	{
		
		$mainframe = JFactory::getApplication();

		$this->_lists["sortfield"]	= $mainframe->getUserStateFromRequest( 'com_joomsport.group_list_field', 'sortfield', 'g.ordering', 'string' );
		$this->_lists["sortway"]	= $mainframe->getUserStateFromRequest( 'com_joomsport.group_list_way', 'sortway', 'ASC', 'string' );
		
		$orderby 	= ' ORDER BY '.$this->_lists["sortfield"].' '.$this->_lists["sortway"];
		

		return $orderby;
	}
	
	
}