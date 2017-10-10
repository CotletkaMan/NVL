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

class list_countrJSModel extends JSPRO_Models
{
	
	var $_data = null;
	var $_lists = null;
	var $_total = null;

	var $_pagination = null;
	var $limit = null;
	var $limitstart = null;

    var $cid = null;
	function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();

        $this->cid = JRequest::getVar( 'countryid', '', 'GET', 'int' );

		// Get the pagination request variables
		$this->limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$this->limitstart	= $mainframe->getUserStateFromRequest( 'com_joomsport.limitstart_countr', 'limitstart', 0, 'int' );
		
		// In case limit has been changed, adjust limitstart accordingly
		$this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
		$this->getPagination();
		
		$this->getData();
	}

	function getData()
	{
        if($this->cid){
            $query = "SELECT * FROM #__bl_countries WHERE id = ".$this->cid;
            $this->db->setQuery($query);
            $this->_lists["country"] = $this->db->loadObject();

        }

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
	
	function _getList($query){
		$this->db->setQuery($query,$this->limitstart, $this->limit);
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
		
		$query = "SELECT * FROM #__bl_countries ";
	
		$query .= $orderby;
		

		return $query;
	}

	function _buildContentOrderBy()
	{
		
		$mainframe = JFactory::getApplication();

		$this->_lists["sortfield"]	= $mainframe->getUserStateFromRequest( 'com_joomsport.list_countr_field', 'sortfield', 'country', 'string' );
		$this->_lists["sortway"]	= $mainframe->getUserStateFromRequest( 'com_joomsport.list_countr_way', 'sortway', 'ASC', 'string' );
		
		$orderby 	= ' ORDER BY '.$this->_lists["sortfield"].' '.$this->_lists["sortway"];
		

		return $orderby;
	}

    function saveCountr(){
        $countryid = JRequest::getVar( 'countryid', '', 'POST', 'int' );
        $country = JRequest::getVar( 'country', '', 'POST', 'string' );
        $code = JRequest::getVar( 'ccode', '', 'POST', 'string' );

        if($countryid){ //update
            $query = "UPDATE #__bl_countries SET ccode = '".$code."',country = '".$country."' WHERE id = ".$countryid;
            $this->db->setQuery($query);
            $this->db->query();
        }else if(!$countryid && $country){ //insert
            $query = "INSERT INTO #__bl_countries(ccode,country) VALUES('".$code."','".$country."')";
            $this->db->setQuery($query);
            $this->db->query();
        }

    }
    function deleteCountr(){
        $cid = JRequest::getVar( 'cid', array(0), '', 'array' );

        $cids = implode(',',$cid);
        $query = "DELETE FROM #__bl_countries WHERE id IN(".$cids.")";
        $this->db->setQuery($query);
        $this->db->query();
    }
	
}