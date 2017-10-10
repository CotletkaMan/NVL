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

class player_listJSModel extends JSPRO_Models
{
	
	var $_data = null;
	var $_lists = null;
	var $_total = null;

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
            $this->limitstart	= $mainframe->getUserStateFromRequest( 'com_joomsport.limitstart_players', 'limitstart', 0, 'int' );
        }
        $f_team	= $this->mainframe->getUserStateFromRequest( 'com_joomsport.filter_team', 'f_team', 0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		$this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
		$this->getPagination($f_team);
		
		$this->getData($f_team);
		$this->getPTeamList($f_team);
	}

	function getData($f_team)
	{
		if (empty($this->_data))
		{
			$query = $this->_buildQuery($f_team);
			$this->_data = $this->_getList($query);
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
		}
		
		return $this->_data;
	}

	function getTotal($f_team)
	{
		if (empty($this->_total))
		{
			$query = $this->_buildQuery($f_team);
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
	
	function getPagination($f_team)
	{
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal($f_team), $this->limitstart, $this->limit );
		}

		return $this->_pagination;
	}

	function _buildQuery($f_team)
	{
		$orderby	= $this->_buildContentOrderBy();
		$mainframe = JFactory::getApplication();

		$this->_lists["js_filter_search"]	= $mainframe->getUserStateFromRequest( 'com_joomsport.player_list_filter', 'js_filter_search', '', 'string' );
	
		
		if($f_team){
			$query = "
					SELECT DISTINCT (
					p.id
					), p . * , u.username, c.country, c.ccode, tt.t_name
					FROM #__bl_players AS p
					LEFT JOIN #__bl_countries AS c ON c.id = p.country_id
					LEFT JOIN #__users AS u ON p.usr_id = u.id, #__bl_players_team AS t, #__bl_teams AS tt
					WHERE t.confirmed='0' AND p.id = t.player_id
					AND t.team_id =".$f_team."
					AND tt.id =".$f_team;
					
					if($this->_lists["js_filter_search"]){
						$query .= " AND (p.first_name LIKE '%".($this->_lists["js_filter_search"])."%' OR p.last_name LIKE '%".($this->_lists["js_filter_search"])."%' OR c.country LIKE '%".($this->_lists["js_filter_search"])."%')";
					}

		}
		else{
			
			$query = "SELECT DISTINCT p . * ,c.ccode,GROUP_CONCAT(DISTINCT t.t_name) as t_name,  u.username, c.country
				FROM 
				#__bl_players AS p
				LEFT JOIN #__bl_countries AS c ON c.id = p.country_id
				LEFT JOIN #__users AS u ON p.usr_id = u.id
				LEFT JOIN #__bl_players_team AS pt ON (p.id = pt.player_id AND pt.confirmed = '0') LEFT OUTER JOIN #__bl_teams as t ON t.id = pt.team_id";
				
				if($this->_lists["js_filter_search"]){
					$query .= " WHERE (p.first_name LIKE '%".($this->_lists["js_filter_search"])."%' OR p.last_name LIKE '%".($this->_lists["js_filter_search"])."%' OR c.country LIKE '%".($this->_lists["js_filter_search"])."%')";
				}

				$query .= " GROUP BY p.id";
		}
		
		
		$query .= $orderby;
		
		return $query;
	}

	function _buildContentOrderBy()
	{
		
		$mainframe = JFactory::getApplication();

		$this->_lists["sortfield"]	= $mainframe->getUserStateFromRequest( 'com_joomsport.player_list_field', 'sortfield', 'first_name', 'string' );
		$this->_lists["sortway"]	= $mainframe->getUserStateFromRequest( 'com_joomsport.player_list_way', 'sortway', 'ASC', 'string' );
		
		$sort = ($this->_lists["sortfield"] == 'first_name')?'first_name '.$this->_lists["sortway"].',last_name '.$this->_lists["sortway"]:($this->_lists["sortfield"].' '.$this->_lists["sortway"]);
		
		$orderby 	= ' ORDER BY '.$sort;
		

		return $orderby;
	}
	
	function getPTeamList($f_team){
		$javascript = 'onchange = "document.adminForm.limitstart.value=0;document.adminForm.submit();"';
		$is_team = array();
		$query = "SELECT t.id as id,t.t_name FROM #__bl_teams as t ORDER BY t.t_name";
		$this->db->setQuery($query);
		$team = $this->db->loadObjectList();
		$is_team[] = JHTML::_('select.option',  0, JText::_('BLBE_SELTEAM'), 'id', 't_name' ); 
		$teamis = array_merge($is_team,$team);
		$this->_lists['teams1'] = JHTML::_('select.genericlist',   $teamis, 'f_team', 'class="inputbox" size="1"'.$javascript, 'id', 't_name', $f_team);
	}

	//
	function delPlayer($cid){
		if(count($cid)){
			$cids = implode(',',$cid);
			$query = "DELETE FROM `#__bl_players` WHERE id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();

            $query = "DELETE p,ap FROM #__bl_photos as p, #__bl_assign_photos as ap WHERE ap.cat_id IN (".$cids.") AND p.id = ap.photo_id  AND ap.cat_type = 1";
            $this->db->setQuery($query);
            $this->db->query();
			
			$query = "DELETE FROM `#__bl_match_events` WHERE player_id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();

            $query = "SELECT id FROM `#__bl_extra_filds` WHERE type = 0";
            $this->db->setQuery($query);
            $fid = $this->db->loadColumn();
            if(count($fid)){
                $fids = implode(',',$fid);
                $query = "DELETE FROM `#__bl_extra_values` WHERE uid IN (".$cids.") AND f_id IN (".$fids.")";
                $this->db->setQuery($query);
                $this->db->query();
            }
			$query = "SELECT s.s_id FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.t_id=t.id AND t_single = 1";
			$this->db->setQuery($query);
			$sid = $this->db->loadColumn();
			if(count($sid)){
                array_push($sid,'-1');
                $sids = implode(',',$sid);

				$query = "SELECT id FROM `#__bl_matchday` WHERE s_id IN (".$sids.")";
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