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
if (js_mobile::isMobile()){
    require(dirname(__FILE__).'/../../includes/pagination_mobile.php');
} else {
    require(dirname(__FILE__).'/../../includes/pagination.php');
}

class admin_teamJSModel extends JSPRO_Models
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

		$this->limit	= $mainframe->getUserStateFromRequest( 'com_joomsport.tm_jslimit','jslimit', 20, 'int' );
		$this->limitstart	= JRequest::getVar( 'page', 1,'', 'int' );
		$this->limitstart = intval($this->limitstart)>1?$this->limitstart:1;
		$this->season_id	= $mainframe->getUserStateFromRequest( 'com_joomsport.sid', 'sid', 0, 'int' );

	}

	function getData()
	{
		
		$this->getPagination();
		$this->_params = $this->JS_PageTitle(JText::_('BLFA_TEAM_EDIT'));
		$query = "SELECT DISTINCT(t.id),t.* "
				." FROM #__bl_teams as t, #__bl_seasons as s, #__bl_season_teams as st, #__bl_tournament as tr"
				." WHERE s.s_id=st.season_id AND st.team_id = t.id AND s.t_id = tr.id AND s.s_id=".$this->season_id
				." ORDER BY t.t_name";
	
		$this->db->setQuery($query, ($this->limitstart-1)*$this->limit, $this->limit);
		$rows = $this->db->loadObjectList();
		$this->_data = $rows;
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		$tourn = $this->getTournOpt($this->season_id);
		$this->_lists['tournname'] = $tourn->name;

		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],0,null,0);
		
		$this->_lists["jssa_addexteam"] = $this->getJS_Config('jssa_addexteam');
		
		if($this->_lists["jssa_addexteam"] == 1){
			$query = "SELECT DISTINCT(t.id),t.t_name "
					." FROM #__bl_teams as t "
					." WHERE t.id NOT IN (SELECT team_id FROM #__bl_season_teams WHERE season_id=".$this->season_id.")"
					." ORDER BY t.t_name";
		
			$this->db->setQuery($query);
			$teams_ex = $this->db->loadObjectList();
			$is_data[] = JHTML::_('select.option', "0", JText::_("BLFA_SELTEAM"), 'id', 't_name' );
			if(count($teams_ex)){
				$is_data = array_merge($is_data,$teams_ex);
			}

			$this->_lists['teams_ex'] = JHTML::_('select.genericlist',   $is_data, 'teams_ex', 'class="inputbox" size="1"', 'id', 't_name', 0 );

		}
		
		$this->_lists["jssa_editteam"] = $this->getJS_Config("jssa_editteam");
		$this->_lists["jssa_delteam"] = $this->getJS_Config("jssa_delteam");
		
	}
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			$this->_pagination = new JS_Pagination($this->getTotal(),$this->limitstart,$this->limit);
		}

		return $this->_pagination;
	}
	function getTotal(){
		$query = "SELECT COUNT(DISTINCT(t.id))"
				." FROM #__bl_teams as t, #__bl_seasons as s, #__bl_season_teams as st, #__bl_tournament as tr"
				." WHERE s.s_id=st.season_id AND st.team_id = t.id AND s.t_id = tr.id AND s.s_id=".$this->season_id
				." ORDER BY t.t_name";

		$this->db->setQuery($query);

		$this->_total = $this->db->loadResult();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		
		return $this->_total;
	}
	
	function SaveAdmExTeam(){
		$teams_ex = JRequest::getVar( 'teams_ex', 0, 'post', 'int' );
		if($teams_ex){
			$query = "INSERT INTO #__bl_season_teams(season_id,team_id) VALUES(".$this->season_id.",".intval($teams_ex).")";
			$this->db->setQuery($query);
			$this->db->query();
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
			
		}
		
	}
	
}
