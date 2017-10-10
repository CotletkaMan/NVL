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

class teamlistJSModel extends JSPRO_Models
{
	var $_data = null;
	var $_lists = null;
	var $_total = null;

	var $_pagination = null;
	var $limit = null;
	var $limitstart = null;
	var $_params = null;
	var $s_id = null;
    var $title = null;
    var $p_title = null;
	
	function __construct()
	{
		parent::__construct();
		
		$this->s_id = $this->mainframe->getUserStateFromRequest( 'com_joomsport.sidselt', 'sidselt', -1, 'int' );
        $this->title = JFactory::getDocument()->getTitle();
		//if($this->s_id){
			$this->mainframe->setUserState('com_joomsport.sidselt',-1);
		//}
		if($this->s_id == '-1'){
			$this->s_id = JRequest::getVar( 'sid', 0, '', 'int' );
		}
		
		// Get the pagination request variables
		$this->limit	=$this->mainframe->getUserStateFromRequest( 'com_joomsport.tl_jslimit', 'jslimit', 20, 'int' );
		$this->limitstart	= JRequest::getVar( 'page', 1,'', 'int' );
		$this->limitstart = intval($this->limitstart)>1?$this->limitstart:1;
		
		$this->getPagination();
		
		$this->getData();
	}

	function getData()
	{
		//title
        $this->p_title = JText::_('BLFA_TEAM_LIST');
        $this->_params = $this->JS_PageTitle($this->title?$this->title:$this->p_title);

		$this->getDataNew();
		$this->getFiltersTeam();
		
		$this->_lists["enbl_extra"] = 0;
		if($this->s_id){
			$this->_lists["unable_reg"] = $this->unblSeasonReg();
		}
		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],@$this->_lists["unable_reg"],$this->s_id,1);
		
		$this->_lists["tourn_name"] = $this->getTournName($this->s_id);
		
	}
	
	
	function getFiltersTeam(){
		$is_tourn = array();
		$is_tourn[] = JHTML::_('select.option',0,  JText::_('BLFA_ALL'), 'id', 's_name' ); 
		$query = "SELECT * FROM #__bl_tournament WHERE published = '1' AND t_single='0' ORDER BY name";
		$this->db->setQuery($query);
		$tourn = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		$javascript = " onchange='document.adminForm.submit();'";
		$jqre = '<select name="sidselt" id="sid" class="'.(js_mobile::isMobile()?'form-control':'styled jfsubmit').'" size="1" '.$javascript.'>';
		$jqre .= '<option value=0"">'.JText::_('BLFA_ALL').'</option>';
		for($i=0;$i<count($tourn);$i++){
			$is_tourn2 = array();
			$query = "SELECT s.s_id as id,s.s_name as s_name FROM #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id WHERE s.published = '1' AND t.id=".$tourn[$i]->id."  ORDER BY s.ordering";
			$this->db->setQuery($query);
			$rows = $this->db->loadObjectList();
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
			if(count($rows)){
				$jqre .= '<optgroup label="'.htmlspecialchars($tourn[$i]->name).'">';
				for($g=0;$g<count($rows);$g++){
					$jqre .= '<option value="'.$rows[$g]->id.'" '.(($rows[$g]->id == $this->s_id)?"selected":"").'>'.$rows[$g]->s_name.'</option>';
				}
				$jqre .= '</optgroup>';
			}
		}
		$jqre .= '</select>';

		$this->_lists['tourn'] = $jqre;
	}
	
	function getDataNew()
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
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
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
		
		if($this->s_id){
			$query = "SELECT * FROM #__bl_teams as t, #__bl_season_teams as st"
					." WHERE t.id=st.team_id AND st.season_id = ".$this->s_id
					."  ORDER BY t_name";
		}else{
			$query = "SELECT * FROM #__bl_teams ORDER BY t_name";
		}
		return $query;
	}
	
}	