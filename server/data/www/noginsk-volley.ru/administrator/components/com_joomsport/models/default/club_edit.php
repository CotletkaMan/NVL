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

class club_editJSModel extends JSPRO_Models
{
	
	var $_data = null;
	var $_lists = null;
	var $_mode = 1;
	var $_id = null;
	function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();
	
		$this->getData();
	}

	function getData()
	{
		
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		$is_id = $cid[0];
		
		$row 	= new JTableClub($this->db);
		$row->load($is_id);
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		
		$this->_lists['ext_fields'] = $this->getAdditfields(4,$row->id);
		
		$this->_lists['photos'] = $this->getPhotos(6,$row->id);

        $this->_lists["post_max_size"] = $this->getValSettingsServ("post_max_size");

		$this->_data = $row;
		
	}

	public function saveClub(){
        $mainframe = JFactory::getApplication();
		$post		= JRequest::get( 'post' );
		$post['c_descr'] = JRequest::getVar( 'c_descr', '', 'post', 'string', JREQUEST_ALLOWRAW );
		//$post['def_img'] = JRequest::getVar( 'c_def_img', 0, 'post', 'int' );
		$post['def_img'] = JRequest::getVar( 'ph_default', 0, 'post', 'int' );
		$row 	= new JTableClub($this->db);

		if (!$row->bind( $post )) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
		// if new item order last in appropriate group
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$row->checkin();
		$query = "DELETE FROM #__bl_assign_photos WHERE cat_type = 6 AND cat_id = ".$row->id;
		$this->db->setQuery($query);
		$this->db->query();
		if(isset($_POST['photos_id']) && count($_POST['photos_id'])){
			for($i = 0; $i < count($_POST['photos_id']); $i++){
				$photo_id = intval($_POST['photos_id'][$i]);
				$photo_name = addslashes(strval($_POST['ph_names'][$i]));
				$query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$photo_id.",".$row->id.",6)";
				$this->db->setQuery($query);
				$this->db->query();
				$query = "UPDATE #__bl_photos SET ph_name = '".($photo_name)."' WHERE id = ".$photo_id;
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
        if($_FILES['player_photo_1']['size']){
            if(isset($_FILES['player_photo_1']['name']) && $_FILES['player_photo_1']['tmp_name'] != '' && isset($_FILES['player_photo_1']['tmp_name'])){
                $bl_filename = strtolower($_FILES['player_photo_1']['name']);
                $ext = pathinfo($_FILES['player_photo_1']['name']);
                $bl_filename = "bl".time().rand(0,3000).'.'.$ext['extension'];
                $bl_filename = str_replace(" ","",$bl_filename);
                //echo $bl_filename;
                 if($this->uploadFile($_FILES['player_photo_1']['tmp_name'], $bl_filename)){
                    $post1['ph_filename'] = $bl_filename;
                    $img1 = new JTablePhotos($this->db);
                    $img1->id = 0;
                    if (!$img1->bind( $post1 )) {
                        JError::raiseError(500, $img1->getError() );
                    }
                    if (!$img1->check()) {
                        JError::raiseError(500, $img1->getError() );
                    }
                    // if new item order last in appropriate group
                    if (!$img1->store()) {
                        JError::raiseError(500, $img1->getError() );
                    }
                    $img1->checkin();
                    $query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$img1->id.",".$row->id.",6)";
                    $this->db->setQuery($query);
                    $this->db->query();
                 }
            }
        }else{
            if($_FILES['player_photo_1']['error'] == 1){
                $mainframe->redirect( 'index.php?option=com_joomsport&task=venue_edit&cid[]='.$row->id,JText::_( 'BLBE_WRNGPHOTO' ),'warning');
            }
        }
        if($_FILES['player_photo_2']['size']){
            if(isset($_FILES['player_photo_2']['name']) && $_FILES['player_photo_2']['tmp_name'] != ''  && isset($_FILES['player_photo_2']['tmp_name'])){
                 $bl_filename = strtolower($_FILES['player_photo_2']['name']);
                $ext = pathinfo($_FILES['player_photo_2']['name']);
                $bl_filename = "bl".time().rand(0,3000).'.'.$ext['extension'];
                $bl_filename = str_replace(" ","",$bl_filename);
                 if($this->uploadFile($_FILES['player_photo_2']['tmp_name'], $bl_filename)){
                    $post2['ph_filename'] = $bl_filename;
                    $img2 = new JTablePhotos($this->db);
                    $img2->id = 0;
                    if (!$img2->bind( $post2 )) {
                        JError::raiseError(500, $img2->getError() );
                    }
                    if (!$img2->check()) {
                        JError::raiseError(500, $img2->getError() );
                    }
                    // if new item order last in appropriate group
                    if (!$img2->store()) {
                        JError::raiseError(500, $img2->getError() );
                    }
                    $img2->checkin();
                    $query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$img2->id.",".$row->id.",6)";
                    $this->db->setQuery($query);
                    $this->db->query();
                 }
            }
        }else{
            if($_FILES['player_photo_2']['error'] == 1){
                $mainframe->redirect( 'index.php?option=com_joomsport&task=venue_edit&cid[]='.$row->id,JText::_( 'BLBE_WRNGPHOTO' ),'warning');
            }
        }
		
		if(isset($_POST['extraf']) && count($_POST['extraf'])){
			foreach($_POST['extraf'] as $p=>$dummy){
				if(intval($_POST['extra_id'][$p])){
					$query = "SELECT season_related FROM `#__bl_extra_filds` WHERE id='".intval($_POST['extra_id'][$p])."'";
					$this->db->setQuery($query);
					$season_related = $this->db->loadResult();
					
					$db_season = $season_related?$seasf_id:0;
					
					$query = "DELETE FROM #__bl_extra_values WHERE f_id = ".$_POST['extra_id'][$p]." AND uid = ".$row->id." AND season_id=".$db_season;
					$this->db->setQuery($query);
					$this->db->query();

                    $_POST['extraf'][$p] = $this->db->quote($_POST['extraf'][$p]);
					if($_POST['extra_ftype'][$p] == '2'){
						$query = "INSERT INTO #__bl_extra_values(f_id,uid,fvalue_text,season_id) VALUES(".$_POST['extra_id'][$p].",".$row->id.",".$_POST['extraf'][$p].",{$db_season})";
					}else{
						$query = "INSERT INTO #__bl_extra_values(f_id,uid,fvalue,season_id) VALUES(".$_POST['extra_id'][$p].",".$row->id.",".$_POST['extraf'][$p].",{$db_season})";
					}
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
		
		$this->_id = $row->id;
	}
	
}