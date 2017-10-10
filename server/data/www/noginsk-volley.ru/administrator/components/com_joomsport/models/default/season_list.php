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

class season_listJSModel extends JSPRO_Models
{
	
	var $_data = null;

	var $_total = null;
	var $_lists = null;
	var $_pagination = null;
	var $limit = null;
	var $limitstart = null;

	function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();

		// Get the pagination request variables
		$this->limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$this->limitstart	= $mainframe->getUserStateFromRequest( 'com_joomsport.limitstart_seasons', 'limitstart', 0, 'int' );

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
		$mainframe = JFactory::getApplication();
		$tfilt_id = JRequest::getVar( 'tfilt_id', 0, 'post', 'int' );
		
		$this->_lists["sortfield"]	= $mainframe->getUserStateFromRequest( 'com_joomsport.seas_list_field', 'listsortfield', '', 'string' );
		$this->_lists["sortway"]	= $mainframe->getUserStateFromRequest( 'com_joomsport.seas_list_way', 'listsortway', 'ASC', 'string' );
		
	//print_r($this->_lists["sortfield"]);
		//$query = "SHOW columns FROM #__bl_seasons WHERE field = 'ordering'";
     
      //$this->db->setQuery($query);
      //$rows = $this->db->loadObjectList();
	  //print_r($rows);
	  
		//$orderby 	= ' ORDER BY IF('.$this->_lists["sortfield"].','.$this->_lists["sortfield"].',10000000000) '.$this->_lists["sortway"]; //ORDER BY IF(field!=0, field, 10000000000)
		if($this->_lists["sortfield"] == ''){
			$orderby = " ORDER BY s.ordering ".$this->_lists["sortway"].",s.s_name ".$this->_lists["sortway"].",t.name ".$this->_lists["sortway"]."";
		}else{
			$orderby 	= ($this->_lists["sortfield"] == 'ordering' || $this->_lists["sortfield"] == 's.ordering')?(' ORDER BY CAST('.$this->_lists["sortfield"].' AS UNSIGNED) '.$this->_lists["sortway"].',s.s_name'):(' ORDER BY '.$this->_lists["sortfield"].' '.$this->_lists["sortway"]);
		}
		$query = "SELECT s.*,s.s_id as id,t.name FROM #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id ".($tfilt_id?" WHERE t.id=".$tfilt_id:"")." ".$orderby;

		return $query;
	}
	function getFilter(){
		$is_tourn = array();
		$tfilt_id = JRequest::getVar( 'tfilt_id', 0, 'post', 'int' );
		$javascript = 'onchange = "document.adminForm.limitstart.value=0;document.adminForm.submit();"';
		$query = "SELECT * FROM #__bl_tournament ORDER BY name";
		$this->db->setQuery($query);
		$tourn = $this->db->loadObjectList();
		$is_tourn[] = JHTML::_('select.option',  0, JText::_('BLBE_SELTOURNAMENT'), 'id', 'name' ); 
		$tourn_is = array_merge($is_tourn,$tourn);
		$this->_lists['tourn'] = JHTML::_('select.genericlist',   $tourn_is, 'tfilt_id', 'class="inputbox" size="1"'.$javascript, 'id', 'name', $tfilt_id );
	}
	function js_publish($table,$cid){
		if(count($cid)){
			$cids = implode(',',$cid);
			$query = "UPDATE `".$table."` SET published = '1' WHERE s_id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
		}
		
	}
	
	function js_unpublish($table,$cid){
		if(count($cid)){
			$cids = implode(',',$cid);
			$query = "UPDATE `".$table."` SET published = '0' WHERE s_id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
	
	function js_delete($table,$cid,$task=''){
		if(count($cid)){
			$cids = implode(',',$cid);
			$query = "DELETE FROM `".$table."` WHERE s_id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();

            if($table == "#__bl_seasons"){
                $query = "SELECT id FROM `#__bl_extra_filds` WHERE type = 3";
                $this->db->setQuery($query);
                $fid = $this->db->loadColumn();
                if(count($fid)){
                    $fids = implode(',',$fid);
                    $query = "DELETE FROM `#__bl_extra_values` WHERE uid IN (".$cids.") AND f_id IN (".$fids.")";
                    $this->db->setQuery($query);
                    $this->db->query();
                }

                $query = "DELETE FROM `#__bl_feadmins` WHERE season_id IN (".$cids.")";
                $this->db->setQuery($query);
                $this->db->query();

               // $query = "DELETE FROM #__bl_matchday, #__bl_match USING #__bl_matchday  LEFT JOIN #__bl_match ON #__bl_matchday.id = #__bl_match.m_id WHERE #__bl_matchday.s_id IN (".$cids.")";
               // $this->db->setQuery($query);
                //$this->db->query();

                $query = "DELETE p,ap,m,md FROM #__bl_photos as p, #__bl_assign_photos as ap,#__bl_match as m,#__bl_matchday as md  WHERE md.id=m.m_id AND p.id = ap.photo_id AND ap.cat_type = 3 AND ap.cat_id = m.id AND s_id IN (".$cids.")";
                $this->db->setQuery($query);
                $this->db->query();

            }
		}
	}
}