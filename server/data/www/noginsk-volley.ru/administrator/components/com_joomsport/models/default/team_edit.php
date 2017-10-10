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

class team_editJSModel extends JSPRO_Models
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
		$mainframe = JFactory::getApplication();;
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		$is_id = $cid[0];
		$seasf_id	= $mainframe->getUserStateFromRequest( 'com_joomsport.seasf_id', 'seasf_id', 0, 'int' );
		$row 	= new JTableTeams($this->db);
		$row->load($is_id);
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
        //$this->mainframe->enqueueMessage(JText::_( 'ОШИБКА!!' ));
		if($is_id){
			$query = "SELECT CONCAT(t.name,' ',s.s_name) as name,s.s_id as id FROM #__bl_season_teams as st, #__bl_seasons as s, #__bl_tournament as t WHERE s.t_id = t.id AND st.season_id=s.s_id AND st.team_id=".$is_id." ORDER BY s.s_id";
			$query2 = "SELECT COUNT(*) FROM #__bl_season_teams as st, #__bl_seasons as s, #__bl_tournament as t WHERE s.t_id = t.id AND st.season_id=s.s_id AND s.s_id = ".$seasf_id." AND st.team_id=".$is_id." ORDER BY s.s_id";
			$this->db->setQuery($query2);
			if(!$this->db->LoadResult()){
				$seasf_id = -1;
			}
			$this->db->setQuery($query);
			$seaspl = $this->db->LoadObjectList();
		}else{
			$seasf_id = -1;
			$seaspl = array();
		}


		$this->_lists['is_seas'] = $seaspl;
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		$javascripts = 'onchange="javascript:document.adminForm.submit();"';
		
		if(count($seaspl) && $seasf_id < 1){
			$seasf_id = $seaspl[0]->id;
			
		}
		if(count($seaspl)){
			$query = "SELECT s.s_name,s.s_id as id FROM #__bl_season_teams as st, #__bl_seasons as s, #__bl_tournament as t WHERE s.t_id = t.id AND st.season_id=s.s_id AND st.team_id=".$is_id." AND t.id={tourid} ORDER BY s.s_name";
			
			$this->_lists['seasf'] = $this->getSeasDList($seasf_id, $query, false, $javascripts,1, 'seasf_id');
			//JHTML::_('select.genericlist',   $seaspl, 'seasf_id', 'class="inputbox" size="1" id="seasf_id" '.$javascripts, 'id', 'name', $seasf_id );
		}else{
			// Set it to friendly season
			$this->_lists['seasf'] = '<input type="hidden" name="seasf_id" value="-1" />';
			//$this->_lists['seasf'] = JHTML::_('input.hidden', 'seasf_id', -1);
		}
		
		$this->_lists['photos'] = array();
        $this->_lists['bonuses'] = array();
        $this->_lists['ext_fields'] = array();

        if (! empty($row->id)) {
            $this->_lists['photos'] = $this->getPhotos(2,$row->id);

            $query = "SELECT st.*,CONCAT(t.name,' ',s.s_name) as name FROM  #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id, #__bl_season_teams as st WHERE s.s_id = st.season_id AND st.team_id=".$row->id." ";
            $this->db->setQuery($query);
            $this->_lists["bonuses"] = $this->db->loadObjectList();



            ///-----EXTRAFIELDS---//
            $this->_lists['ext_fields'] = $this->getAdditfields(1,$row->id,$seasf_id);
        }
        if(!$is_id){
            $this->getSeasListNew();
        }
        //venue
        $is_venue[] = JHTML::_('select.option',  0, JText::_('BLBE_SELVENUE'), 'id', 'v_name' );
        $query = "SELECT * FROM #__bl_venue ORDER BY v_name";
        $this->db->setQuery($query);
        $venue = $this->db->loadObjectList();
        if(count($venue)){
            $is_venue = array_merge($is_venue,$venue);
        }
        $this->_lists['venue'] = JHTML::_('select.genericlist',   $is_venue, 'venue_id', 'class="inputbox" size="1"', 'id', 'v_name', $row->venue_id);

	//	//club
		
        $is_club[] = JHTML::_('select.option',  0, JText::_('BLBE_SELCLUB'), 'id', 'c_name' );
        $query = "SELECT c.* FROM #__bl_club as c ORDER BY c_name";
        $this->db->setQuery($query);
        $club = $this->db->loadObjectList();
        if(count($club)){
            $is_club = array_merge($is_club,$club);
        }
        $this->_lists['club'] = JHTML::_('select.genericlist',   $is_club, 'club_id', 'class="inputbox" size="1"', 'id', 'c_name', $row->club_id);
    //    
		//---Players--///
        $this->getTPlayers($row->id, $seasf_id);
        $this->_lists["post_max_size"] = $this->getValSettingsServ("post_max_size");

		$this->_data = $row;
	}
	
	protected function getSeasListNew(){
		$query = "SELECT CONCAT(t.name,' ',s.s_name) as name,s.s_id as id FROM #__bl_seasons as s, #__bl_tournament as t WHERE s.t_id = t.id AND t.t_single='0' ORDER BY t.name,s.s_name";
		$this->db->setQuery($query);
		$seasall = $this->db->loadObjectList();
		$this->_lists['seasall'] = @JHTML::_('select.genericlist',   $seasall, 'seas_all', ' size="10" multiple', 'id', 'name', 0 );
		$this->_lists['seasall_add'] = @JHTML::_('select.genericlist',   array(), 'seas_all_add[]', ' size="10" multiple', 'id', 'name', 0 );
	}
	
	protected function getTPlayers($s_id,$seasf_id)
	{
        $plint = array();
        if (! empty($s_id)) {
            $query = "SELECT p.id FROM #__bl_players as p, #__bl_players_team as t WHERE t.confirmed='0' AND t.player_id=p.id AND t.team_id=".$s_id." AND t.season_id=".$seasf_id;
            $this->db->setQuery($query);
            $plint = $this->db->loadColumn();
        }

		$query = "SELECT CONCAT(first_name,' ',last_name) as name,id FROM #__bl_players ".(count($plint)?" WHERE id NOT IN (".implode(',',$plint).")":"")." ORDER BY first_name,last_name";
		$this->db->setQuery($query);
		$playerz = $this->db->loadObjectList();
		$is_pl[] = JHTML::_('select.option',  0, JText::_('BLBE_SELPLAYER'), 'id', 'name' ); 
		$playerz = array_merge($is_pl,$playerz);

		$this->_lists['player'] = JHTML::_('select.genericlist',   $playerz, 'playerz_id', 'class="chosen-select" size="1" id="playerz"', 'id', 'name', 0 );
        if ($s_id) {
			$query = "SELECT p.id,CONCAT(p.first_name,' ',p.last_name) as name FROM #__bl_players as p, #__bl_players_team as t WHERE t.confirmed='0' AND t.player_join='0' AND t.season_id = ".$seasf_id." AND t.player_id=p.id AND t.team_id=".$s_id;
			$this->db->setQuery($query);
			$this->_lists['team_players'] = $this->db->loadObjectList();
		} else {
			$this->_lists['team_players'] = array();
		}	
	}
	
	public function saveTeam(){
		$post		= JRequest::get( 'post' );
        $mainframe = JFactory::getApplication();
		$post['t_descr'] = JRequest::getVar( 't_descr', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$post['def_img'] = JRequest::getVar( 'ph_default', 0, 'post', 'int' );
		$post['t_yteam'] = JRequest::getVar( 't_yteam', 0, 'post', 'int' );
		$row 	= new JTableTeams($this->db);
		$istlogo = JRequest::getVar( 'istlogo', 0, 'post', 'int' );
		$seasf_id	= JRequest::getVar( 'seasf_id', 0, 'post', 'int' );
		if(!$istlogo){
			$post['t_emblem'] = '';
		}
		if(isset($_FILES['t_logo']['name']) && $_FILES['t_logo']['tmp_name'] != '' && isset($_FILES['t_logo']['tmp_name'])){
			$bl_filename = strtolower($_FILES['t_logo']['name']);
			$ext = pathinfo($_FILES['t_logo']['name']);
			$bl_filename = "bl".time().rand(0,3000).'.'.$ext['extension'];
			$bl_filename = str_replace(" ","",$bl_filename);
			//echo $bl_filename;
			 if($this->uploadFile($_FILES['t_logo']['tmp_name'], $bl_filename)){
				$post['t_emblem'] = $bl_filename;
			 }
		}
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
        $query = "SELECT p.id FROM #__bl_assign_photos as ph, #__bl_photos as p WHERE p.id = ph.photo_id AND ph.cat_type = 2 AND ph.cat_id = ".$row->id;
        $this->db->setQuery($query);
        $in_id = $this->db->loadColumn();

		$query = "DELETE FROM #__bl_assign_photos WHERE cat_type = 2 AND cat_id = ".$row->id;
		$this->db->setQuery($query);
		$this->db->query();
		if(isset($_POST['photos_id']) && count($_POST['photos_id'])){
			for($i = 0; $i < count($_POST['photos_id']); $i++){
				$photo_id = intval($_POST['photos_id'][$i]);
				$photo_name = addslashes(strval($_POST['ph_names'][$i]));
				$query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$photo_id.",".$row->id.",2)";
				$this->db->setQuery($query);
				$this->db->query();
				$query = "UPDATE #__bl_photos SET ph_name = '".($photo_name)."' WHERE id = ".$photo_id;
				$this->db->setQuery($query);
				$this->db->query();

                $key = array_search($_POST['photos_id'][$i], $in_id);
                //print_r($key);die;
                if(is_int($key)){
                    unset($in_id[$key]);
                }
			}
		}
        if(count($in_id)){
            $query = "DELETE FROM #__bl_photos WHERE id IN(".implode(',',$in_id).")";
            $this->db->setQuery($query);
            $this->db->query();
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
                    $query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$img1->id.",".$row->id.",2)";
                    $this->db->setQuery($query);
                    $this->db->query();
                 }
            }
        }else{
            if($_FILES['player_photo_1']['error'] == 1){
                $mainframe->redirect( 'index.php?option=com_joomsport&task=team_edit&cid[]='.$row->id,JText::_( 'BLBE_WRNGPHOTO' ),'warning');
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
                    $query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$img2->id.",".$row->id.",2)";
                    $this->db->setQuery($query);
                    $this->db->query();
                 }
            }
        }else{
            if($_FILES['player_photo_2']['error'] == 1){
                $mainframe->redirect( 'index.php?option=com_joomsport&task=team_edit&cid[]='.$row->id,JText::_( 'BLBE_WRNGPHOTO' ),'warning');
            }
        }
		//-------extra fields-----------//
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
					if($_POST['extra_ftype'][$p] == '2'){
						$query = "INSERT INTO #__bl_extra_values(f_id,uid,fvalue_text,season_id) VALUES(".$_POST['extra_id'][$p].",".$row->id.",'".addslashes($_POST['extraf'][$p])."',{$db_season})";
					}else{
						$query = "INSERT INTO #__bl_extra_values(f_id,uid,fvalue,season_id) VALUES(".$_POST['extra_id'][$p].",".$row->id.",'".addslashes($_POST['extraf'][$p])."',{$db_season})";
					}
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
		
		//-------Bonuses points----//
		if(isset($_POST['sids']) && count($_POST['sids'])){
			for($p=0;$p<count($_POST['sids']);$p++){
				$query = "UPDATE #__bl_season_teams SET bonus_point = ".intval($_POST['bonuses'][$p])." WHERE season_id=".$_POST['sids'][$p]." AND team_id=".$row->id;
				$this->db->setQuery($query);
				$this->db->query();
			}
		}	
		
		
		//-------Players----//
		$assignedPlayers = $_POST['teampl'];
		// $seasf_id = -1 is a friendly match
		$isValidSeason = ($seasf_id == -1 || $seasf_id > 0);
		if ($isValidSeason) {
			if($seasf_id) {
				$query = "DELETE FROM #__bl_players_team WHERE confirmed='0' AND team_id=".$row->id." AND season_id=".$seasf_id;
				$this->db->setQuery($query);
				$this->db->query();
                if(!empty($assignedPlayers)){
                    for($p=0;$p<count($assignedPlayers);$p++){
                        $query = "INSERT INTO #__bl_players_team(team_id,player_id,season_id) VALUES(".$row->id.",".intval($assignedPlayers[$p]).",".$seasf_id.")";
                        $this->db->setQuery($query);
                        $this->db->query();
                    }
                }
			}
		}
		//---add seasons
		if(isset($_POST['seas_all_add']) && count($_POST['seas_all_add'])){
			for($p=0;$p<count($_POST['seas_all_add']);$p++){
				$query = "INSERT INTO #__bl_season_teams(season_id,team_id) VALUES(".$_POST['seas_all_add'][$p].",".$row->id.")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}	
		
		$this->_id = $row->id;
	}
	
}