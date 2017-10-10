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

class seasonlistJSModel extends JSPRO_Models
{
	var $_data = null;
	var $_lists = null;
	var $_total = null;

	var $_pagination = null;
	var $limit = null;
	var $limitstart = null;
	var $_params = null;
	var $filtr_tourn = null;
    var $oreg		= null;
    var $title = null;
    var $p_title = null;
	
	function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();
        $this->title = JFactory::getDocument()->getTitle();

		$this->filtr_tourn		= $mainframe->getUserStateFromRequest( 'global.list.filtr_tourn', 'filtr_tourn', 0, 'int' );
		// Get the pagination request variables
		$this->limit	= JRequest::getVar( 'jslimit', 20,'', 'int' );
		$this->limitstart	= JRequest::getVar( 'page', 1,'', 'int' );
		$this->limitstart = intval($this->limitstart)>1?$this->limitstart:1;
		
		$this->oreg = JRequest::getVar( 'oreg', 0,'', 'int' );
		
		// In case limit has been changed, adjust limitstart accordingly

		$this->getPagination();
		
		$this->getData();
		$this->getnewData();
        $this->p_title = JText::_('BLFA_SEAS_LIST');
        $this->_params = $this->JS_PageTitle($this->title?$this->title:$this->p_title);
		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],0,null,1);
		$this->getTourList();
		
	}
	function getnewData(){
		for($i=0;$i<count($this->_data);$i++){
			$season_par = $this->_data[$i];
			$reg_start = mktime(substr($season_par->reg_start,11,2),substr($season_par->reg_start,14,2),0,substr($season_par->reg_start,5,2),substr($season_par->reg_start,8,2),substr($season_par->reg_start,0,4));
			$reg_end = mktime(substr($season_par->reg_end,11,2),substr($season_par->reg_end,14,2),0,substr($season_par->reg_end,5,2),substr($season_par->reg_end,8,2),substr($season_par->reg_end,0,4));
			$this->_data[$i]->unable_reg = 0;
			if($season_par->t_single){
				$query = "SELECT COUNT(*) FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".$season_par->s_id;
			}else{
				$query = "SELECT COUNT(*) FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".$season_par->s_id;
			}
			$this->db->setQuery($query);
			$this->_data[$i]->part_count = $this->db->loadResult();
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
			if($season_par->s_reg && ($this->_data[$i]->part_count < $season_par->s_participant || $season_par->s_participant == 0) && ($reg_start <= time() && (time() <= $reg_end || $season_par->reg_end == '0000-00-00 00:00:00'))){
				$this->_data[$i]->unable_reg = 1;
			}
		}
	}
	
	function getData()
	{
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query);
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
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		return count($tot);
	}
	
	function _getList($query){
		$this->db->setQuery($query, ($this->limitstart-1)*$this->limit, $this->limit);
		$tot = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		return $tot;
	}
	
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			$this->_pagination = new JS_Pagination($this->getTotal(),$this->limitstart,$this->limit);
		}
		return $this->_pagination;
	}

	function _buildQuery()
	{
		
		$query = "SELECT s.*,t.*, CONCAT(s.s_name,' ',t.name) as name"
				." FROM #__bl_tournament as t, #__bl_seasons as s"
				." WHERE s.published = '1' AND t.published = '1' AND s.t_id = t.id"
				.($this->filtr_tourn?" AND t.id='".$this->filtr_tourn."'":"")
				." ORDER BY s.ordering,s.s_name,t.name";
		return $query;
	}
    
	function getTourList(){
		$is_tourn = array();
		$is_tourn[] = JHTML::_('select.option',0,  JText::_('BLFA_ALL'), 'id', 'name' ); 
		$javascript = 'onchange = "document.adminForm.limitstart.value=0;document.adminForm.submit();"';
		
		$query = "SELECT * FROM #__bl_tournament WHERE published = '1' ORDER BY name";
		$this->db->setQuery($query);
		$tourn = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		if(count($tourn)){
			$is_tourn = array_merge($is_tourn,$tourn);
		}
		$this->_lists['tourn'] = JHTML::_('select.genericlist',   $is_tourn, 'filtr_tourn', (js_mobile::isMobile()?'class="form-control"':'class="styled jfsubmit"').' size="1" '.$javascript, 'id', 'name', $this->filtr_tourn );
		
	}
	
	
}	