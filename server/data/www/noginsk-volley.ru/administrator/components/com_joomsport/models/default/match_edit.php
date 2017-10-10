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

class match_editJSModel extends JSPRO_Models
{
	
	var $_data = null;
	var $_lists = null;
	var $_mode = 1;
	var $_id = null;
	var $s_id = null;
	function __construct()
	{
		parent::__construct();
	
		$this->getData();
	}

	function getData()
	{
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		$is_id = (int) $cid[0];
		
		if(!$is_id){
			return false;
		}

		$row 	= new JTableMatch($this->db);
		$row->load($is_id);
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		
		$query = "SELECT s_id FROM #__bl_matchday  WHERE id = ".$row->m_id;
		$this->db->setQuery($query);
		$season_id = $this->db->loadResult();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}


        $this->s_id = $season_id;

        $this->_lists['t_type'] = $this->getMatchDayType($row);

		if($season_id != -1){
			$tourn = $this->getSeasAttr($season_id);
			
			$this->_lists['t_single'] = $tourn->t_single;
			//$this->_lists['t_type'] = $tourn->t_type;
			$this->_lists['t_type'] = $this->getMatchDayType($row);
			$this->_lists['s_enbl_extra'] = $tourn->s_enbl_extra;
		}else{
			$tourn = new stdClass();
			$this->_lists['t_type'] = 0;
			$this->_lists['s_enbl_extra'] = 0;
			//$tourn->t_type=0;
			if($row->m_single == 1){
				$tourn->t_single = 1;
				$this->_lists['t_single'] = 1;
			}else{
				$tourn->t_single = 0;
				$this->_lists['t_single'] = 0;
			}
			
			$tourn->s_enbl_extra = 0;
		}

		if($this->_lists['t_type'] == 1 || $this->_lists['t_type'] == 2){
			$query = "SELECT m_name FROM #__bl_matchday  WHERE id = ".$row->m_id;
			$this->db->setQuery($query);
			$this->_lists['mday'] = $this->db->loadResult();
		}else{
			$is_matchday = array();
			$query = "SELECT * FROM #__bl_matchday  WHERE s_id = ".$season_id." ORDER BY m_name";
			$this->db->setQuery($query);
			$mday = $this->db->loadObjectList();
			$is_matchday[] = JHTML::_('select.option',  0, JText::_('BLBE_SELMATCHDAY'), 'id', 'm_name' );
			if(count($mday)){
				$mdayis = array_merge($is_matchday,$mday);
			}	
			$this->_lists['mday'] = JHTML::_('select.genericlist',   $mdayis, 'm_id', 'class="chzn-done" size="1"', 'id', 'm_name', $row->m_id);
	
		}
		
		$this->getPlEvent();
		$this->getTeamEvent();
		
		if($tourn->t_single){
			$query = "SELECT CONCAT(first_name,' ',last_name) FROM #__bl_players WHERE id= ".$row->team1_id;
			$this->db->setQuery($query);
			$team_1 = $this->db->loadResult();
			
			$query = "SELECT CONCAT(first_name,' ',last_name) FROM #__bl_players WHERE id= ".$row->team2_id;
			$this->db->setQuery($query);
			$team_2 = $this->db->loadResult();
		}else{
			$query = "SELECT t_name FROM #__bl_teams WHERE id= ".$row->team1_id;
			$this->db->setQuery($query);
			$team_1 = $this->db->loadResult();
			
			$query = "SELECT t_name FROM #__bl_teams WHERE id= ".$row->team2_id;
			$this->db->setQuery($query);
			$team_2 = $this->db->loadResult();
		}

		$this->_lists['teams1'] = $team_1;
		$this->_lists['teams2'] = $team_2;
		$is_player = array();
		if($tourn->t_single){
			$is_player[] = JHTML::_('select.option',  0, JText::_('BLBE_SELPLAYER'), 'id', 'p_name' );
            if($row->team1_id != -1){
			    $is_player[] = JHTML::_('select.option',  $row->team1_id, $team_1, 'id', 'p_name' );
            }
            if($row->team2_id != -1){
			    $is_player[] = JHTML::_('select.option',  $row->team2_id, $team_2, 'id', 'p_name' );
            }
			$ev_pl = $is_player;
			$this->_lists['players'] = JHTML::_('select.genericlist',   $ev_pl, 'playerz_id', 'class="chzn-done" size="1" style="width:190px;"', 'id', 'p_name', 0);
		}else{
			$query = "SELECT CONCAT(p.id,'*',s.team_id) as id,CONCAT(p.first_name,' ',p.last_name) as p_name,p.id as pid
			            FROM #__bl_players as p, #__bl_players_team as s
			            WHERE s.confirmed='0' AND s.player_join='0' AND s.player_id = p.id
			            AND s.team_id = ".$row->team1_id." AND s.season_id=".$season_id
                        ." ORDER BY p.first_name,p.last_name";
			if($season_id == -1){
				$query = "SELECT DISTINCT(p.id),p.id as id,
				            CONCAT(p.first_name,' ',p.last_name) as p_name,p.id as pid
				            FROM #__bl_players as p, #__bl_players_team as s
				            WHERE s.confirmed='0' AND s.player_join='0' AND s.player_id = p.id
				            ORDER BY p.first_name,p.last_name";


			}
			$this->db->setQuery($query) ;
		
			$players_1 = $this->db->loadObjectList();

		//--	
			$mjarr1 = array(-1);
			for($i=0; $i<count($players_1);$i++){
				$mjarr1[] = $players_1[$i]->pid;
			}

			$query = "SELECT CONCAT(p.id,'*',s.team_id) as id,"
                        ." CONCAT(p.first_name,' ',p.last_name) as p_name, p.id as pid"
						." FROM #__bl_players as p, #__bl_squard as s"
						." WHERE s.player_id NOT IN(".implode(',',$mjarr1).") AND p.id=s.player_id"
                        ." AND s.match_id='".$row->id."' AND s.team_id='".$row->team1_id."'"
						." ORDER BY p.first_name,p.last_name";
			$this->db->setQuery($query);
			$squard1 = $this->db->loadObjectList();

			if(count($squard1)){
				$players_1 = array_merge($players_1,$squard1);
			}
		//--
			$query = "SELECT CONCAT(p.id,'*',s.team_id) as id,"
                        ." CONCAT(p.first_name,' ',p.last_name) as p_name,p.id as pid"
                        ." FROM #__bl_players as p, #__bl_players_team as s"
                        ." WHERE s.confirmed='0' AND s.player_join='0' AND s.player_id = p.id"
                        ." AND s.team_id = ".$row->team2_id." AND s.season_id=".$season_id
                        ." ORDER BY p.first_name,p.last_name";
			if($season_id == -1){
				$query = "SELECT DISTINCT(p.id),p.id as id,"
                        ." CONCAT(p.first_name,' ',p.last_name) as p_name,p.id as pid"
                        ." FROM #__bl_players as p, #__bl_players_team as s "
                        ." WHERE s.confirmed='0' AND s.player_join='0' AND s.player_id = p.id"
                        ." ORDER BY p.first_name,p.last_name";
			
			}
			$this->db->setQuery($query) ;
		
			$players_2 = $this->db->loadObjectList();
		//--	
			$mjarr2 = array(-1);
			for($i=0; $i<count($players_2);$i++){
				$mjarr2[] = $players_2[$i]->pid;
			}
			

			$query = "SELECT CONCAT(p.id,'*',s.team_id) as id,CONCAT(p.first_name,' ',p.last_name) as p_name, p.id as pid"
						." FROM #__bl_players as p, #__bl_squard as s"
						." WHERE s.player_id NOT IN(".implode(',',$mjarr2).") AND p.id=s.player_id"
                        ." AND s.match_id='".$row->id."' AND s.team_id='".$row->team2_id."'"
						." ORDER BY p.first_name,p.last_name";
				$this->db->setQuery($query);
				$squard2 = $this->db->loadObjectList();

			if(count($squard2)){
				$players_2 = array_merge($players_2,$squard2);
			}
		//--
			$is_player[] = JHTML::_('select.option',  0, JText::_('BLBE_SELPLAYER'), 'id', 'p_name' ); 
		
			$is_player[] = JHTML::_('select.optgroup',  $team_1, 'id', 'p_name' ); 
		
			$is_player2[] = JHTML::_('select.optgroup',  $team_2, 'id', 'p_name' ); 
			
			
			$jqre = '<select name="playerz_id" id="playerz_id" style="width:190px;" class="chzn-done" size="1">';
			$jqre .= '<option value="0">'.JText::_('BLBE_SELPLAYER').'</option>';
			$jqre .= '<optgroup label="'.$team_1.'">';
			for($g=0;$g<count($players_1);$g++){
				$jqre .= '<option value="'.$players_1[$g]->id.'*'.$row->team1_id.'">'.$players_1[$g]->p_name.'</option>';
			}
			$jqre .= '</optgroup>';
			$jqre .= '<optgroup label="'.$team_2.'">';
			for($g=0;$g<count($players_2);$g++){
				$jqre .= '<option value="'.$players_2[$g]->id.'*'.$row->team2_id.'">'.$players_2[$g]->p_name.'</option>';
			}
			$jqre .= '</optgroup>';
			$jqre .= '</select>';
			
			$this->_lists['players'] = $jqre;
		}
		
		if(!$tourn->t_single){
			$this->_lists["pl1"] = $players_1;
			$this->_lists["pl2"] = $players_2;
			$is_player_sq[] = JHTML::_('select.option',  0, JText::_('BLBE_SELPLAYER'), 'pid', 'p_name' ); 
			if(count($players_1)){
				$ev_pl = array_merge($is_player_sq,$players_1);
			}else{
				$ev_pl = $is_player_sq;
			}
			$this->_lists['players_team1'] = JHTML::_('select.genericlist',   $ev_pl, 'playersq1_id', 'class="inputbox chzn-done" size="1" style="width:150px;"', 'pid', 'p_name', 0);
			$this->_lists['players_team1_out'] = JHTML::_('select.genericlist',   $ev_pl, 'playersq1_out_id', 'class="inputbox chzn-done" size="1" style="width:150px;"', 'pid', 'p_name', 0);
			$this->_lists['players_team1_res'] = JHTML::_('select.genericlist',   $ev_pl, 'playersq1_id_res', 'class="inputbox chzn-done" size="1" style="width:150px;"', 'pid', 'p_name', 0);
			if(count($players_2)){
				$ev_pl = array_merge($is_player_sq,$players_2);
			}else{
				$ev_pl = $is_player_sq;
			}
			$this->_lists['players_team2'] = JHTML::_('select.genericlist',   $ev_pl, 'playersq2_id', 'class="inputbox chzn-done" size="1" style="width:150px;"', 'pid', 'p_name', 0);
			$this->_lists['players_team2_out'] = JHTML::_('select.genericlist',   $ev_pl, 'playersq2_out_id', 'class="inputbox chzn-done" size="1" style="width:150px;"', 'pid', 'p_name', 0);
			
			$this->_lists['players_team2_res'] = JHTML::_('select.genericlist',   $ev_pl, 'playersq2_id_res', 'class="inputbox chzn-done" size="1" style="width:150px;"', 'pid', 'p_name', 0);
		}
		
		$is_team[] = JHTML::_('select.option',  0, JText::_('BLBE_SELTEAM'), 'id', 'p_name' ); 
		if($row->team1_id != -1){
            $is_team[] = JHTML::_('select.option', $row->team1_id, $team_1, 'id', 'p_name' );
        }
        if($row->team2_id != -1){
            $is_team[] = JHTML::_('select.option', $row->team2_id, $team_2, 'id', 'p_name' );
        }
		//$is2_player[] = '</optgroup>';$lists['players']
		$this->_lists['sel_team'] = JHTML::_('select.genericlist',   $is_team, 'teamz_id', 'class="inputbox chzn-done" size="1" style="width:190px;"', 'id', 'p_name', 0);
		$javascriptus = ($this->_lists['t_type'] == 1 || $this->_lists['t_type'] == 2)?' onclick="javascript:chng_disbl_aet();"':'';
		$this->_lists['extra'] 		= JHTML::_('select.booleanlist',  'is_extra', 'class="inputbox chzn-done" '.$javascriptus, $row->is_extra );
		$this->_lists['is_extra'] = $row->is_extra;//UPDATE
		$js = 'onclick="enblnp();"';
		$this->_lists['new_points'] 	= JHTML::_('select.booleanlist',  'new_points', 'class="inputbox" '.$js, $row->new_points );
		$this->_lists['m_played'] 		= JHTML::_('select.booleanlist',  'm_played', 'class="inputbox"', $row->m_played );

        $pl_list = $this->_lists['t_single']?array($row->team1_id,$row->team2_id):array_merge($mjarr1,$mjarr2);
        $query = "SELECT me.*,ev.e_name,CONCAT(p.first_name,' ',p.last_name) as p_name"
                    ." FROM  #__bl_events as ev , #__bl_players as p, #__bl_match_events as me"
                    ." WHERE me.player_id = p.id AND ev.player_event = '1'"
                    ." AND  me.e_id = ev.id AND me.match_id = ".$row->id
                    ." ".(count($pl_list)?"AND me.player_id IN(".implode(',',$pl_list).")":'')." ORDER BY me.eordering, CAST(me.minutes AS UNSIGNED),p.first_name,p.last_name";
		$this->db->setQuery($query);
		$this->_lists['m_events'] = $this->db->loadObjectList();
//update

        $t_list = !$this->_lists['t_single']?array($row->team1_id,$row->team2_id):array();
		$query = "SELECT me.*,ev.e_name,p.t_name as p_name,p.id as pid"
                    ." FROM  #__bl_events as ev, #__bl_teams as p , #__bl_match_events as me"
                    ." WHERE me.t_id = p.id AND ev.player_event = '0' AND  me.e_id = ev.id AND me.match_id = ".$row->id."  ".(count($t_list)?"AND me.t_id IN(".implode(',',$t_list).")":'')
                    ." ORDER BY me.eordering,p.t_name";
		$this->db->setQuery($query);
		//echo mysql_error();die();
		$this->_lists['t_events'] = $this->db->loadObjectList();
		
		$this->_lists['photos'] = $this->getPhotos(3,$row->id);
		
		///-----EXTRAFIELDS---//
		$this->_lists['ext_fields'] = $this->getAdditfields(2,$row->id);
		
		///--------MAPS--------------///
		$query = "SELECT m.*,mp.m_score1,mp.m_score2"
                ." FROM #__bl_seas_maps as sm, #__bl_maps as m "
                ." LEFT JOIN #__bl_mapscore as mp ON m.id=mp.map_id AND mp.m_id=".$row->id
                ." WHERE m.id=sm.map_id AND sm.season_id=".$season_id
                ." ORDER BY m.id";
		$this->db->setQuery($query);
		$this->_lists['maps'] = $this->db->loadObjectList();
		
		//----squard----//
		
		$query = "SELECT p.id FROM #__bl_players as p, #__bl_squard as s "
                ." WHERE p.id=s.player_id AND s.match_id=".$row->id." AND s.team_id={$row->team1_id}"
                ." AND s.mainsquard = '1'";
		$this->db->setQuery($query);
		$this->_lists['squard1'] = $this->db->loadColumn();
		$query = "SELECT p.id FROM #__bl_players as p, #__bl_squard as s "
                ." WHERE p.id=s.player_id AND s.match_id=".$row->id
                ." AND s.team_id={$row->team2_id} AND s.mainsquard = '1'";
		$this->db->setQuery($query);
		$this->_lists['squard2'] = $this->db->loadColumn();
		$query = "SELECT p.id FROM #__bl_players as p, #__bl_squard as s WHERE p.id=s.player_id AND s.match_id=".$row->id." AND s.team_id={$row->team1_id} AND s.mainsquard = '0'";
		$this->db->setQuery($query);
		$this->_lists['squard1_res'] = $this->db->loadColumn();
		$query = "SELECT p.id FROM #__bl_players as p, #__bl_squard as s WHERE p.id=s.player_id AND s.match_id=".$row->id." AND s.team_id={$row->team2_id} AND s.mainsquard = '0'";
		$this->db->setQuery($query);
		$this->_lists['squard2_res'] = $this->db->loadColumn();
		//subs in
		$query = "SELECT s.*,CONCAT(p1.first_name,' ',p1.last_name) as plin,CONCAT(p2.first_name,' ',p2.last_name) as plout FROM #__bl_subsin as s, #__bl_players as p1, #__bl_players as p2 WHERE p1.id=s.player_in AND p2.id=s.player_out AND s.match_id=".$row->id." AND s.team_id={$row->team1_id} ORDER BY s.minutes";
		$this->db->setQuery($query);
		$this->_lists['subsin1'] = $this->db->loadObjectList();
		$query = "SELECT s.*,CONCAT(p1.first_name,' ',p1.last_name) as plin,CONCAT(p2.first_name,' ',p2.last_name) as plout FROM #__bl_subsin as s, #__bl_players as p1, #__bl_players as p2 WHERE p1.id=s.player_in AND p2.id=s.player_out AND s.match_id=".$row->id." AND s.team_id={$row->team2_id} ORDER BY s.minutes";
		$this->db->setQuery($query);
		$this->_lists['subsin2'] = $this->db->loadObjectList();
		
		//venue
		$is_venue[] = JHTML::_('select.option',  0, JText::_('BLBE_SELVENUE'), 'id', 'v_name' ); 
		$query = "SELECT * FROM #__bl_venue ORDER BY v_name";
		$this->db->setQuery($query);
		$venue = $this->db->loadObjectList();
		if(count($venue)){
			$is_venue = array_merge($is_venue,$venue);
		}
		$this->_lists['venue'] = JHTML::_('select.genericlist',   $is_venue, 'venue_id', 'class="chzn-done" size="1"', 'id', 'v_name', $row->venue_id);
        $this->_lists["post_max_size"] = $this->getValSettingsServ("post_max_size");
		
		$this->_data = $row;
		
	}
    protected function getMatchDayType($row){
        $query = "SELECT t_type FROM #__bl_matchday WHERE id=".$row->m_id;
        $this->db->setQuery($query);
        return $t_type = $this->db->loadResult();
    }
	protected function getPlEvent(){
		$is_event = array();
		$query = "SELECT * FROM #__bl_events WHERE player_event = '1' ORDER BY e_name";
		$this->db->setQuery($query);
		$events = $this->db->loadObjectList();
		$is_event[] = JHTML::_('select.option',  0, JText::_('BLBE_SELEVENT'), 'id', 'e_name' );
		if(count($events)){
			$is_event = array_merge($is_event,$events);
		}
		$this->_lists['events'] = JHTML::_('select.genericlist',   $is_event, 'event_id', 'class="inputbox chzn-done" size="1" style="width:170px;"', 'id', 'e_name', 0);
	}
	
	protected function getTeamEvent(){
		$is_event = array();
		$query = "SELECT * FROM #__bl_events WHERE player_event = '0' ORDER BY e_name";
		$this->db->setQuery($query);
		$events = $this->db->loadObjectList();
		$is_event[] = JHTML::_('select.option',  0, JText::_('BLBE_SELEVENT'), 'id', 'e_name' );
		if(count($events)){
			$is_event = array_merge($is_event,$events);
		}
		$this->_lists['team_events'] = JHTML::_('select.genericlist',   $is_event, 'tevent_id', 'class="inputbox chzn-done" size="1" style="width:170px;"', 'id', 'e_name', 0);
	}
	
	public function saveMatch(){
        $mainframe = JFactory::getApplication();
			$post		= JRequest::get( 'post' );
			$post['match_descr'] = JRequest::getVar( 'match_descr', '', 'post', 'string', JREQUEST_ALLOWRAW );
			$row 	= new JTableMatch($this->db);
			$row->m_date = JRequest::getVar( 'm_date', '', 'post', 'string', JREQUEST_ALLOWRAW );
			$row->m_time = JRequest::getVar( 'd_time', '', 'post', 'string', JREQUEST_ALLOWRAW );
			
			if (!$row->bind( $post )) {
				JError::raiseError(500, $row->getError() );
			}
			if(isset($_POST['penwin']) && count($_POST['penwin'])){
			//var_dump($_POST['penwin']);die();
				$row->p_winner = intval($_POST['penwin'][0]);
			}else{
				$row->p_winner = 0;
			}
			if (!$row->check()) {
				JError::raiseError(500, $row->getError() );
			}
			// if new item order last in appropriate group
			if (!$row->store()) {
				JError::raiseError(500, $row->getError() );
			}
			$row->checkin();

			
			$query = "SELECT s_id FROM #__bl_matchday as md, #__bl_match as m  WHERE md.id=m.m_id AND m.id = ".$row->id;
			$this->db->setQuery($query);
			$season_id = $this->db->loadResult();

			$tourn = $this->getSeasAttr($season_id);

			$lt_type = $this->_lists['t_type'];
				
			
			
			$row->load($row->id);
			
			
			if($lt_type == 1 || $lt_type == 2){
				
				$team_win = ($row->score1 > $row->score2)?$row->team1_id:$row->team2_id;
				$team_loose = ($row->score1 > $row->score2)?$row->team2_id:$row->team1_id;
				if(!empty($row->p_winner)){
                    $team_win = ($row->p_winner == $row->team1_id)?$row->team1_id:$row->team2_id;
                    $team_loose = ($team_win == $row->team1_id)?$row->team2_id:$row->team1_id;

                }

                if($row->is_extra){
                    $team_win = ($row->aet1 > $row->aet2)?$row->team1_id:$row->team2_id;
                    $team_loose = ($row->aet1 > $row->aet2)?$row->team2_id:$row->team1_id;
                }
				
				$query = "UPDATE #__bl_match SET team1_id=".$team_win."  WHERE m_id = ".$row->m_id." AND k_stage > ".$row->k_stage." AND team1_id = ".$team_loose;
				$this->db->setQuery($query);
				$this->db->query();
				$query = "UPDATE #__bl_match SET team2_id=".$team_win."  WHERE m_id = ".$row->m_id." AND k_stage > ".$row->k_stage." AND team2_id = ".$team_loose;
				$this->db->setQuery($query);
				$this->db->query();	
					
				if($row->m_played == 0){
					$query = "UPDATE #__bl_match SET m_played = '0' WHERE m_id = ".$row->m_id." AND k_stage > ".$row->k_stage." AND (team1_id = ".$row->team1_id." OR team2_id = ".$row->team1_id." OR team1_id = ".$row->team2_id." OR team2_id = ".$row->team2_id.")";
					$this->db->setQuery($query);
					$this->db->query();
					$query = "UPDATE #__bl_match SET team1_id = '0' WHERE m_id = ".$row->m_id." AND k_stage > ".$row->k_stage." AND (team1_id = ".$row->team1_id." OR team1_id = ".$row->team2_id.")";
					$this->db->setQuery($query);
					$this->db->query();
					$query = "UPDATE #__bl_match SET team2_id = '0' WHERE m_id = ".$row->m_id." AND k_stage > ".$row->k_stage." AND (team2_id = ".$row->team1_id." OR team2_id = ".$row->team2_id.")";
					$this->db->setQuery($query);
					$this->db->query();
				}
				
			}
			$eordering=0;
			$me_arr = array();
			if(isset($_POST['new_eventid']) && count($_POST['new_eventid'])){
				for ($i=0; $i< count($_POST['new_eventid']); $i++){
					if(!intval($_POST['em_id'][$i])){
						$new_event = $_POST['new_eventid'][$i];
						
						$plis = explode('*',$_POST['new_player'][$i]);
						
						$query = "INSERT INTO #__bl_match_events(e_id,player_id,match_id,ecount,minutes,t_id,eordering) VALUES(".$new_event.",".intval($plis[0]).",".$row->id.",".intval($_POST['e_countval'][$i]).",".floatval($_POST['e_minuteval'][$i]).",".intval($plis[1]).",".$eordering.")";
						$this->db->setQuery($query);
						$this->db->query();
						$me_arr[] = $this->db->insertid();
					}else{
						$query = "SELECT * FROM #__bl_match_events WHERE id=".intval($_POST['em_id'][$i]);
						$this->db->setQuery($query);
						$event_bl = $this->db->loadObjectList();
						if(count($event_bl)){
							$query = "UPDATE #__bl_match_events SET minutes=".floatval($_POST['e_minuteval'][$i]).",ecount=".intval($_POST['e_countval'][$i]).",eordering=".$eordering." WHERE id=".intval($_POST['em_id'][$i]);
							$this->db->setQuery($query);
							$this->db->query();
							$me_arr[] = intval($_POST['em_id'][$i]);
						}
					}
				$eordering++;	
				}
				
			}
				
		/////////////
		$eordering_t = 0;
		$me_arr_t = array();
		if(isset($_POST['new_teventid']) && count($_POST['new_teventid'])){
			for ($i=0; $i< count($_POST['new_teventid']); $i++){
				if(!intval($_POST['et_id'][$i])){
					$new_event = $_POST['new_teventid'][$i];
					$query = "INSERT INTO #__bl_match_events(e_id,t_id,match_id,ecount,minutes,eordering) VALUES(".$new_event.",".$_POST['new_tplayer'][$i].",".$row->id.",".intval($_POST['et_countval'][$i]).",'0',".$eordering.")";
					$this->db->setQuery($query);
					$this->db->query();
					$me_arr_t[] = $this->db->insertid();
				}else{
					$query = "SELECT * FROM #__bl_match_events WHERE id=".intval($_POST['et_id'][$i]);
					$this->db->setQuery($query);
					$event_bl = $this->db->loadObjectList();
					if(count($event_bl)){
						$query = "UPDATE #__bl_match_events SET ecount=".intval($_POST['et_countval'][$i]).",eordering=".$eordering_t." WHERE id=".intval($_POST['et_id'][$i]);
						$this->db->setQuery($query);
						$this->db->query();
						$me_arr_t[] = intval($_POST['et_id'][$i]);
					}
				}
			$eordering_t++;
			}
		}

		 $query = "DELETE FROM #__bl_match_events WHERE match_id = ".$row->id;
			if(count($me_arr)){ $query.=" AND id NOT IN (".implode(',',$me_arr).")";}
			if(count($me_arr_t)){ $query.=" AND id NOT IN (".implode(',',$me_arr_t).")";}
			$this->db->setQuery($query);
			$this->db->query();

	////////////
        $query = "SELECT p.id FROM #__bl_assign_photos as ph, #__bl_photos as p WHERE p.id = ph.photo_id AND ph.cat_type = 3 AND ph.cat_id = ".$row->id;
        $this->db->setQuery($query);
        $in_id = $this->db->loadColumn();

		$query = "DELETE FROM #__bl_assign_photos WHERE cat_type = 3 AND cat_id = ".$row->id;
		$this->db->setQuery($query);
		$this->db->query();
		if(isset($_POST['photos_id']) && count($_POST['photos_id'])){
			for($i = 0; $i < count($_POST['photos_id']); $i++){
				$photo_id = intval($_POST['photos_id'][$i]);
				$photo_name = addslashes(strval($_POST['ph_names'][$i]));
				$query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$photo_id.",".$row->id.",3)";
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
				$query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$img1->id.",".$row->id.",3)";
				$this->db->setQuery($query);
				$this->db->query();
			 }
		}
        }else{
            if($_FILES['player_photo_1']['error'] == 1){
                $mainframe->redirect( 'index.php?option=com_joomsport&task=match_edit&cid[]='.$row->id,JText::_( 'BLBE_WRNGPHOTO' ),'warning');
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
                    $query = "INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$img2->id.",".$row->id.",3)";
                    $this->db->setQuery($query);
                    $this->db->query();
                 }
            }
        }else{
            if($_FILES['player_photo_2']['error'] == 1){
                $mainframe->redirect( 'index.php?option=com_joomsport&task=match_edit&cid[]='.$row->id,JText::_( 'BLBE_WRNGPHOTO' ),'warning');
            }
        }
		
		//-------extra fields-----------//
		if(isset($_POST['extraf']) && count($_POST['extraf'])){
			foreach($_POST['extraf'] as $p=>$dummy){
				$query = "DELETE FROM #__bl_extra_values WHERE f_id = ".$_POST['extra_id'][$p]." AND uid = ".$row->id;
				$this->db->setQuery($query);
				$this->db->query();
				if($_POST['extra_ftype'][$p] == '2'){
					$query = "INSERT INTO #__bl_extra_values(f_id,uid,fvalue_text) VALUES(".$_POST['extra_id'][$p].",".$row->id.",'".addslashes($_POST['extraf'][$p])."')";
				}else{
					$query = "INSERT INTO #__bl_extra_values(f_id,uid,fvalue) VALUES(".$_POST['extra_id'][$p].",".$row->id.",'".addslashes($_POST['extraf'][$p])."')";
				}
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
		
		//-----SQUARD--------///
		
		$query = "DELETE FROM #__bl_squard WHERE match_id = ".$row->id;
			$this->db->setQuery($query);
			$this->db->query();
		if(isset($_POST['t1_squard']) && count($_POST['t1_squard'])){
			for ($i=0; $i< count($_POST['t1_squard']); $i++){
				$new_event = $_POST['t1_squard'][$i];
				$query = "INSERT INTO #__bl_squard(match_id,team_id,player_id,mainsquard) VALUES(".$row->id.",".$row->team1_id.",".$new_event.",'1')";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
		
		if(isset($_POST['t2_squard']) && count($_POST['t2_squard'])){
			for ($i=0; $i< count($_POST['t2_squard']); $i++){
				$new_event = $_POST['t2_squard'][$i];
				$query = "INSERT INTO #__bl_squard(match_id,team_id,player_id,mainsquard) VALUES(".$row->id.",".$row->team2_id.",".$new_event.",'1')";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
		if(isset($_POST['t1_squard_res']) && count($_POST['t1_squard_res'])){
			for ($i=0; $i< count($_POST['t1_squard_res']); $i++){
				$new_event = $_POST['t1_squard_res'][$i];
				$query = "INSERT INTO #__bl_squard(match_id,team_id,player_id,mainsquard) VALUES(".$row->id.",".$row->team1_id.",".$new_event.",'0')";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
		if(isset($_POST['t2_squard_res']) && count($_POST['t2_squard_res'])){
			for ($i=0; $i< count($_POST['t2_squard_res']); $i++){
				$new_event = $_POST['t2_squard_res'][$i];
				$query = "INSERT INTO #__bl_squard(match_id,team_id,player_id,mainsquard) VALUES(".$row->id.",".$row->team2_id.",".$new_event.",'0')";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
			//subs in
		$query = "DELETE FROM #__bl_subsin WHERE match_id=".$row->id;
		$this->db->setQuery($query);
		$this->db->query();	
		if(isset($_POST['playersq1_id_arr']) && count($_POST['playersq1_id_arr'])){
			for ($i=0; $i< count($_POST['playersq1_id_arr']); $i++){
				$player_in = intval($_POST['playersq1_id_arr'][$i]);
				$player_out = intval($_POST['playersq1_out_id_arr'][$i]);
				$minutes = intval($_POST['minutes1_arr'][$i]);
				$query = "INSERT INTO #__bl_subsin(match_id,team_id,player_in,player_out,minutes,season_id) VALUES(".$row->id.",".$row->team1_id.",".$player_in.",".$player_out.",'".$minutes."',".$season_id.")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
		if(isset($_POST['playersq2_id_arr']) && count($_POST['playersq2_id_arr'])){
			for ($i=0; $i< count($_POST['playersq2_id_arr']); $i++){
				$player_in = intval($_POST['playersq2_id_arr'][$i]);
				$player_out = intval($_POST['playersq2_out_id_arr'][$i]);
				$minutes = intval($_POST['minutes2_arr'][$i]);
				$query = "INSERT INTO #__bl_subsin(match_id,team_id,player_in,player_out,minutes,season_id) VALUES(".$row->id.",".$row->team2_id.",".$player_in.",".$player_out.",'".$minutes."',".$season_id.")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
		
		//--
		
		$query = "DELETE  FROM #__bl_mapscore WHERE m_id = ".$row->id;
			$this->db->setQuery($query);
			$this->db->query();
		if(isset($_POST['mapid']) && count($_POST['mapid'])){
			for ($i=0; $i< count($_POST['mapid']); $i++){
				$new_event = $_POST['mapid'][$i];
				$query = "INSERT INTO #__bl_mapscore(m_id,map_id,m_score1,m_score2) VALUES(".$row->id.",".$new_event.",".intval($_POST['t1map'][$i]).",".intval($_POST['t2map'][$i]).")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
		
		$this->_id = $row->id;
	}
	
	function deleteMday($cid){
		if(count($cid)){
			$cids = implode(',',$cid);
			$this->db->setQuery("DELETE FROM #__bl_matchday WHERE id IN (".$cids.")");
			$this->db->query();
			$this->db->setQuery("DELETE FROM #__bl_match WHERE m_id IN (".$cids.")");
			$this->db->query();
		}
	}
	
	function getMdID(){
		if($this->_id){
			$query = "SELECT m_id FROM #__bl_match WHERE id=".$this->_id;
			$this->db->setQuery($query);
			return $this->db->loadResult();
		}
	}
	
	
}