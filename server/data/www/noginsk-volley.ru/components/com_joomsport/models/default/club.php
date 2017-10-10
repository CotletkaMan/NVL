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

class clubJSModel extends JSPRO_Models
{
	var $_lists = null;
	var $club_id = null;
    var $title = null;
    var $p_title = null;
	
	function __construct()
	{
		parent::__construct();
		
		$this->club_id = JRequest::getVar( 'id', 0, '', 'int' );
        $this->title = JFactory::getDocument()->getTitle();

        $query = "SELECT c_name FROM #__bl_club WHERE id = ".$this->club_id;
        $this->db->setQuery($query);
        $club_id = $this->db->loadResult();
        if (!$club_id)
        {
            JError::raiseError( 403, JText::_('Access Forbidden') );
            return;
        }
		
	}

	function getData()
	{
		
		$query = "SELECT * FROM #__bl_club WHERE id = '".$this->club_id."'";
		$this->db->setQuery($query);
		$club = $this->db->loadObject();
		$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
		$this->_lists["club"] = $club;
		//title
        $this->p_title = $club->c_name;
		//$this->_params = $this->JS_PageTitle($club->v_name);
        $this->_params = $this->JS_PageTitle($this->title?$this->title:$this->p_title);
		
		$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 6 AND cat_id = ".$this->club_id;
		$this->db->setQuery($query);
		$photos = $this->db->loadObjectList();
		$this->_lists["photos"] = $photos;
		
		$def_img = '';
		if($club->def_img){
			$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$club->def_img;
			$this->db->setQuery($query);
			$def_img = $this->db->loadResult();
		}else if(isset($photos[0])){
			$def_img = $photos[0]->filename;
		}
		$this->_lists["def_img"] = $def_img;
		
		$this->_lists['ext_fields'] = $this->getAddFields($this->club_id,'4',"club");
		
		$this->_lists["teams_season"] = $this->teamsToModer();;
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],0,null,1);
		
		$query = "SELECT * FROM  #__bl_teams as t WHERE club_id = ".$this->club_id;
		$this->db->setQuery($query);
		$this->_lists["teams"] = $this->db->loadObjectList();
		
		//social buttons
		$ogimg = '';
		if($this->_lists["def_img"] && is_file('media/bearleague/'.$this->_lists["def_img"])){
			$ogimg = JURI::base().'media/bearleague/'.$this->_lists["def_img"];
		}
		$this->_lists['socbut'] = $this->getSocialButtons('jsbp_club',$club->c_name,$ogimg,htmlspecialchars(strip_tags($this->_lists["club"]->c_descr)));
	}
	
}	