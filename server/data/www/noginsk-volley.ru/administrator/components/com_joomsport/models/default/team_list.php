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

class team_listJSModel extends JSPRO_Models
{
	
	var $_data = null;

	var $_total = null;
	var $lists = null;
	var $_pagination = null;
	var $limit = null;
	var $limitstart = null;

	function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();

		// Get the pagination request variables
		$this->limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$this->limitstart = 0;
        if (! $mainframe->input->getInt('is_search')) {
            $this->limitstart= $mainframe->getUserStateFromRequest('com_joomsport.limitstart_teams', 'limitstart', 0, 'int');
        }

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
			$this->_data = $this->_getList($query);
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
	
	function _getList($query){
		$this->db->setQuery($query,$this->limitstart,$this->limit);
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
		$mainframe = JFactory::getApplication();

		$this->_lists["js_filter_search"]	= $mainframe->getUserStateFromRequest( 'com_joomsport.team_list_filter', 'js_filter_search', '', 'string' );
	
		$orderby	= $this->_buildContentOrderBy();

		$query = ' SELECT * '
			. ' FROM #__bl_teams '
		;
		
		if($this->_lists["js_filter_search"]){
			$query .= " WHERE (t_name LIKE '%".($this->_lists["js_filter_search"])."%' OR t_city LIKE '%".($this->_lists["js_filter_search"])."%') ";
		}
		$query .= $orderby;

		return $query;
	}

	function _buildContentOrderBy()
	{
		
		$mainframe = JFactory::getApplication();

		$this->_lists["sortfield"]	= $mainframe->getUserStateFromRequest( 'com_joomsport.team_list_field', 'sortfield', 't_name', 'string' );
		$this->_lists["sortway"]	= $mainframe->getUserStateFromRequest( 'com_joomsport.team_list_way', 'sortway', 'ASC', 'string' );
		
		$orderby 	= ' ORDER BY '.$this->_lists["sortfield"].' '.$this->_lists["sortway"];
		

		return $orderby;
	}

	///delete
	function delTeam($cid){
		if(count($cid)){
			$cids = implode(',',$cid);
			$query = "DELETE FROM `#__bl_teams` WHERE id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
//"DELETE m,p,ap FROM #__bl_match as m, #__bl_photos as p, #__bl_assign_photos as ap WHERE m.m_id IN (".$cids.") AND p.id = ap.photo_id AND ap.cat_type = 3 AND ap.cat_id = m.id";
            $query = "DELETE p,ap FROM #__bl_photos as p, #__bl_assign_photos as ap WHERE ap.cat_id IN (".$cids.") AND p.id = ap.photo_id  AND ap.cat_type = 2";
            $this->db->setQuery($query);
            $this->db->query();
			
			$query = "DELETE FROM `#__bl_match_events` WHERE t_id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
			
			$query = "DELETE FROM `#__bl_moders` WHERE tid IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();

            $query = "SELECT id FROM `#__bl_extra_filds` WHERE type = 1";
            $this->db->setQuery($query);
            $fid = $this->db->loadColumn();
            if(count($fid)){
                $fids = implode(',',$fid);
                $query = "DELETE FROM `#__bl_extra_values` WHERE uid IN (".$cids.") AND f_id IN (".$fids.")";
                $this->db->setQuery($query);
                $this->db->query();
            }
			$query = "SELECT s.s_id FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.t_id=t.id AND t_single = 0";
			$this->db->setQuery($query);
			$sid = $this->db->loadColumn();
			if(count($sid)){
				array_push($sid,'-1');
				$sids = implode(',',$sid);
				$query = "SELECT id FROM #__bl_matchday WHERE s_id IN (".$sids.")";
				$this->db->setQuery($query);
				$mdid = $this->db->loadColumn();
				
				if(count($mdid)){
					$mdids = implode(',',$mdid);
						$query = "DELETE FROM `#__bl_match` WHERE m_id IN (".$mdids.") AND (team1_id IN (".$cids.") OR team2_id IN (".$cids."))";
						$this->db->setQuery($query);
						$this->db->query();
				}
			}	
			
		}	
	}
}