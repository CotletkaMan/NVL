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
//require(dirname(__FILE__).'/knockout.php');
require(JPATH_SITE.'/components/com_joomsport/models/default/knockout.php');
require_once(dirname(__FILE__).'/../../classes/class-matchday-race.php');

class matchday_editJSModel extends JSPRO_Models
{
	
	var $_data = null;
	var $_lists = null;
	var $_mode = 1;
	var $_id = null;
        var $knock_type = null;
	function __construct()
	{
		parent::__construct();

	
		$this->getData();
	}

	function getData()
	{
		$mainframe = JFactory::getApplication();;
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );

                $t_type 		= JRequest::getVar( 't_type', 0, '', 'int' );////-------
                $this->knock_type = new JS_Knockout();


		$is_id = $cid[0];
		$season_id	= $mainframe->getUserStateFromRequest( 'com_joomsport.s_id', 's_id', 0, 'int' );
		
                
                
		$row 	= new JTableMday($this->db);
		$row->load($is_id);
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		$lists = array();	
		$s_id = $row->s_id?$row->s_id:$season_id;
		$tourn = $this->getSeasAttr($s_id);
                
                
                $this->_data = $row;
                $this->_lists['s_id'] = $s_id;
                
                
                if(empty( $t_type)){
                    $t_type = $this->getMatchDayType($row);
                   // print_r($t_type);
                }
		
		if($s_id != -1){
			$this->_lists['t_single'] = $tourn->t_single;
			//$this->_lists['t_type'] = $tourn->t_type;
			$this->_lists['t_type'] = $t_type;
			$this->_lists['tourn'] = $tourn->name;
			$this->_lists['s_enbl_extra'] = $tourn->s_enbl_extra;
		}else{
                        $tourn = new JObject();
			$this->_lists['t_single'] = 0;
			$tourn->t_single = 0;
			$this->_lists['tourn'] = JText::_('BLBE_FRIENDLYMATCH');
			$this->_lists['t_type'] = 0;
			//$tourn->t_type = 0;
                        $t_type = 0;
			$this->_lists['s_enbl_extra'] = 1;
		}
                
                // version 3.3 get tournament type
                if($tourn->tournament_type == '1'){
                    $md = new MatchdayRace($is_id, $tourn);
                    $this->_lists['race'] = $md->editMd();
                    return;
                }
                
                
                
                
		
		$this->getPlList();
		
		if($row->id){
			$match = $this->getMatches($row,$tourn,$s_id, $t_type);
            $matchDE = $this->getMatchesDE($row,$tourn,$s_id, $t_type);
		}else{
			$match = null;
		}
		//betting
		$this->_lists['avail_betting'] = $this->isBet();
		$this->_lists['betevents'] = array();
		if($this->isBet()){
			$this->_lists['betevents'] = $this->getBetEvents($s_id);
			$matchesid = array();
			if ($match) {
				foreach($match as $m) {
					$matchesid[] = $m->id;
				}
			}
			$this->_lists['matchbetevents'] = $this->getMatchesBetEvents($matchesid);

		}
        ///venue
        $is_venue[] = JHTML::_('select.option',  0, JText::_('BLBE_SELVENUE'), 'id', 'v_name' );
        $query = "SELECT * FROM #__bl_venue ORDER BY v_name";
        $this->db->setQuery($query);
        $venue = $this->db->loadObjectList();
        if(count($venue)){
            $is_venue = array_merge($is_venue,$venue);
        }

        //$row->venue_name = JHTML::_('select.genericlist',   $is_venue, 'venue_id[]', 'class="chzn-done" size="1"', 'id', 'v_name', $row->venue_id);
        if ($match) {
            foreach($match as $m) {
                $m->venue_name = JHTML::_('select.genericlist',   $is_venue, 'venue_id[]', 'class="chzn-done" size="1"', 'id', 'v_name', $m->venue_id);
            }
        }

        $this->_lists["match"] = $match;
	
		$this->_lists['venue_name'] = JHTML::_('select.genericlist',   $is_venue, 'venue_id_new[]', 'class="chzn-done" size="1"', 'id', 'v_name');
		
		$is_team = array();
		
		if($tourn->t_single){
			$query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.first_name";
		}else{
			$query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.t_name";
		}
		if($s_id == -1){
			$query = "SELECT * FROM #__bl_teams ORDER BY t_name";
		}
		$this->db->setQuery($query);
		$team = $this->db->loadObjectList();
		$is_team[] = JHTML::_('select.option',  0, ($tourn->t_single?JText::_('BLBE_SELPLAYER'):JText::_('BLBE_SELTEAM')), 'id', 't_name' ); 
		$teamis = array_merge($is_team,$team);
		$this->_lists['teams1'] = JHTML::_('select.genericlist',   $teamis, 'teams1', 'class="inputbox chzn-done" size="1" id="teams1" style="width:180px;"', 'id', 't_name', 0 );
		$this->_lists['teams2'] = JHTML::_('select.genericlist',   $teamis, 'teams2', 'class="inputbox chzn-done" size="1" id="teams2" style="width:180px;"', 'id', 't_name', 0 );
		$this->_lists['is_playoff'] 		= JHTML::_('select.booleanlist',  'is_playoff', 'class="inputbox"', $row->is_playoff );
////////
		if(count($match) && $t_type==2 && $row->k_format){ ////
			$this->_lists['knock_layout'] = $this->knock_type->getKnockDE($row,$tourn,$match,$s_id,$matchDE,$this->get_kn_cfg(),0);
		}else if(count($match) && $t_type==1 && $row->k_format){
            $this->_lists['knock_layout'] = $this->knock_type->getKnock($row,$tourn,$match,$s_id,$this->get_kn_cfg(),0);
        }
		
		//$this->getKnockFormat($row, $this->_lists['t_type']);
        $this->_lists['format'] = $this->knock_type->getKnockFormat($row, $this->_lists['t_type']);


		
		
	}

///get mday type
    protected function getMatchDayType($row){
        if($row->id && $row->s_id){
            $query = "SELECT t_type FROM #__bl_matchday WHERE id=".$row->id." AND s_id = ".$row->s_id."";
            $this->db->setQuery($query);
            return $t_type = $this->db->loadResult();
        }


    }
	//betting
	    protected function getMatchesBetEvents($matchesid){
        $query = "SELECT bbc.*"
                ."\n FROM #__bl_betting_events bbe"
                ."\n INNER JOIN #__bl_betting_coeffs bbc ON bbc.idevent=bbe.id"
                ."\n WHERE bbc.idmatch IN (".implode(',', $matchesid).")";

        $this->db->setQuery($query);
        $matchevents = $this->db->loadObjectList();
        $resultme = array();
        if ($matchevents) {
            foreach($matchevents as $me) {
                $resultme[$me->idmatch][$me->idevent] = $me;
            }
        }

        return $resultme;
    }
    
    protected function getBetEvents($s_id) {
        $query = "SELECT bbe.*"
                ."\n FROM #__bl_betting_events bbe"
                ."\n INNER JOIN #__bl_betting_templates_events bbte ON bbte.idevent=bbe.id"
                ."\n INNER JOIN #__bl_betting_templates bbt ON bbt.id=bbte.idtemplate"
                ."\n INNER JOIN #__bl_seasons bs ON bs.s_id=bbte.idtemplate AND bs.s_id=$s_id"
                ."\n ORDER BY bbe.type ASC";
        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

	protected function getPlList(){
		$is_pl[] = JHTML::_('select.option',  0, JText::_('BLBE_SELPLAYER'), 'id', 'name' ); 
		$query = "SELECT id,CONCAT(first_name,' ',last_name) as name FROM #__bl_players ORDER BY first_name,last_name";
		$this->db->setQuery($query);
		$pl = $this->db->loadObjectList();
		if(count($pl)){
			$is_pl = array_merge($is_pl,$pl);
		}
		$this->_lists['plmd'] = JHTML::_('select.genericlist',   $is_pl, 'plmd', 'class="inputbox chzn-done" size="1" id="plmd" style="width:180px;"', 'id', 'name', 0 );
		$this->_lists['plmd_away'] = JHTML::_('select.genericlist',   $is_pl, 'plmd_away', 'class="inputbox chzn-done" size="1" id="plmd_away" style="width:180px;"', 'id', 'name', 0 );
		
	}
	
	protected function getMatches(& $row,& $tourn, $s_id, $t_type){
		$orderby = $t_type?"m.k_stage,m.k_ordering":"m.id";
		if($s_id == -1){
			$query = "SELECT m.*,CONCAT(t.first_name,' ',t.last_name) as home_team, CONCAT(t2.first_name,' ',t2.last_name) as away_team,IF(m.score1>m.score2,CONCAT(t.first_name,' ',t.last_name),CONCAT(t2.first_name,' ',t2.last_name)) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid ,IF(m.score1<m.score2,CONCAT(t.first_name,' ',t.last_name),CONCAT(t2.first_name,' ',t2.last_name)) as looser, IF(m.score1<m.score2,t.id,t2.id) as looserid"
			." FROM #__bl_match as m LEFT JOIN #__bl_players as t ON t.id = m.team1_id  LEFT JOIN #__bl_players as t2 ON t2.id = m.team2_id"
			." WHERE m.m_id = ".$row->id." AND m.m_single = '1' AND m.k_type = '0'"
			." ORDER BY ".$orderby;
			$this->db->setQuery($query);
			$match = $this->db->loadObjectList();
			
			$query = "SELECT m.*,t.t_name as home_team, t2.t_name as away_team,IF(m.score1>m.score2,t.t_name,t2.t_name) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid ,IF(m.score1<m.score2,t.t_name,t2.t_name) as looser, IF(m.score1<m.score2,t.id,t2.id) as looserid"
						." FROM #__bl_match as m LEFT JOIN #__bl_teams as t ON t.id = m.team1_id LEFT JOIN #__bl_teams as t2 ON t2.id = m.team2_id"
						." WHERE m.m_id = ".$row->id." AND m.m_single = '0' AND m.k_type = '0'"
						." ORDER BY ".$orderby;
			$this->db->setQuery($query);
			$match_t = $this->db->loadObjectList();
			if(count($match)){
			
				$match_t = array_merge($match_t,$match);
			}
			return $match_t;
		}else{
			
			if($tourn->t_single){
				$query = "SELECT m.*,CONCAT(t.first_name,' ',t.last_name) as home_team, CONCAT(t2.first_name,' ',t2.last_name) as away_team,IF(m.score1>m.score2,CONCAT(t.first_name,' ',t.last_name),CONCAT(t2.first_name,' ',t2.last_name)) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid ,IF(m.score1<m.score2,CONCAT(t.first_name,' ',t.last_name),CONCAT(t2.first_name,' ',t2.last_name)) as looser, IF(m.score1<m.score2,t.id,t2.id) as looserid"
						." FROM #__bl_match as m LEFT JOIN #__bl_players as t ON t.id = m.team1_id  LEFT JOIN #__bl_players as t2 ON t2.id = m.team2_id"
						.(($row->id)?" WHERE m.m_id = ".$row->id." AND m.k_type = '0'":" WHERE m.k_type = '0'")
						." ORDER BY ".$orderby;
			
			}else{
				$query = "SELECT m.*,t.t_name as home_team, t2.t_name as away_team,IF(m.score1>m.score2,t.t_name,t2.t_name) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid, IF(m.score1<m.score2,t.t_name,t2.t_name) as looser, IF(m.score1<m.score2,t.id,t2.id) as looserid"
						." FROM #__bl_match as m LEFT JOIN #__bl_teams as t ON t.id = m.team1_id LEFT JOIN #__bl_teams as t2 ON t2.id = m.team2_id"
						.(($row->id)?" WHERE m.m_id = ".$row->id." AND m.k_type = '0'":" WHERE m.k_type = '0'")
						." ORDER BY ".$orderby;
			}
			
			$this->db->setQuery($query);
			return $this->db->loadObjectList();
		}	
	}
    protected function getMatchesDE(& $row,& $tourn, $s_id, $t_type){
        $orderby = $t_type?"m.k_stage,m.k_ordering":"m.id";
        if($s_id == -1){
            $query = "SELECT m.*,CONCAT(t.first_name,' ',t.last_name) as home_team, CONCAT(t2.first_name,' ',t2.last_name) as away_team,IF(m.score1>m.score2,CONCAT(t.first_name,' ',t.last_name),CONCAT(t2.first_name,' ',t2.last_name)) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid ,IF(m.score1<m.score2,CONCAT(t.first_name,' ',t.last_name),CONCAT(t2.first_name,' ',t2.last_name)) as looser, IF(m.score1<m.score2,t.id,t2.id) as looserid"
                ." FROM #__bl_match as m LEFT JOIN #__bl_players as t ON t.id = m.team1_id  LEFT JOIN #__bl_players as t2 ON t2.id = m.team2_id"
                ." WHERE m.m_id = ".$row->id." AND m.m_single = '1' AND m.k_type = '1'"
                ." ORDER BY ".$orderby;
            $this->db->setQuery($query);
            $match = $this->db->loadObjectList();

            $query = "SELECT m.*,t.t_name as home_team, t2.t_name as away_team,IF(m.score1>m.score2,t.t_name,t2.t_name) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid ,IF(m.score1<m.score2,t.t_name,t2.t_name) as looser, IF(m.score1<m.score2,t.id,t2.id) as looserid"
                ." FROM #__bl_match as m LEFT JOIN #__bl_teams as t ON t.id = m.team1_id LEFT JOIN #__bl_teams as t2 ON t2.id = m.team2_id"
                ." WHERE m.m_id = ".$row->id." AND m.m_single = '0' AND m.k_type = '1'"
                ." ORDER BY ".$orderby;
            $this->db->setQuery($query);
            $match_t = $this->db->loadObjectList();
            if(count($match)){

                $match_t = array_merge($match_t,$match);
            }
            return $match_t;
        }else{

            if($tourn->t_single){
                $query = "SELECT m.*,CONCAT(t.first_name,' ',t.last_name) as home_team, CONCAT(t2.first_name,' ',t2.last_name) as away_team,IF(m.score1>m.score2,CONCAT(t.first_name,' ',t.last_name),CONCAT(t2.first_name,' ',t2.last_name)) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid ,IF(m.score1<m.score2,CONCAT(t.first_name,' ',t.last_name),CONCAT(t2.first_name,' ',t2.last_name)) as looser, IF(m.score1<m.score2,t.id,t2.id) as looserid"
                    ." FROM #__bl_match as m LEFT JOIN #__bl_players as t ON t.id = m.team1_id  LEFT JOIN #__bl_players as t2 ON t2.id = m.team2_id"
                    .(($row->id)?" WHERE m.m_id = ".$row->id." AND m.k_type = '1'":" WHERE  AND m.k_type = '1'")
                    ." ORDER BY ".$orderby;

            }else{
                $query = "SELECT m.*,t.t_name as home_team, t2.t_name as away_team,IF(m.score1>m.score2,t.t_name,t2.t_name) as winner, IF(m.score1>m.score2,t.id,t2.id) as winnerid, IF(m.score1<m.score2,t.t_name,t2.t_name) as looser, IF(m.score1<m.score2,t.id,t2.id) as looserid"
                    ." FROM #__bl_match as m LEFT JOIN #__bl_teams as t ON t.id = m.team1_id LEFT JOIN #__bl_teams as t2 ON t2.id = m.team2_id"
                    .(($row->id)?" WHERE m.m_id = ".$row->id." AND m.k_type = '1'":" WHERE  AND m.k_type = '1'")
                    ." ORDER BY ".$orderby;
            }

            $this->db->setQuery($query);
            return $this->db->loadObjectList();
        }
    }

	public function orderMDay(){
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$order		= JRequest::getVar( 'order', array(), 'post', 'array' );
		
		$row		= new JTableMday($this->db);;
		$total		= count( $cid );
		
		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}
		// update ordering values
		for ($i = 0; $i < $total; $i++)
		{
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					return JError::raiseError( 500, $this->db->getErrorMsg() );
				}
			
				
			}
		}
	}
	
	public function saveMday(){
		
		$t_single = JRequest::getVar( 't_single', 0, 'post', 'int' );
		$t_knock = JRequest::getVar( 't_knock', 0, 'post', 'int' );
		
		$post		= JRequest::get( 'post' );
		$post['k_format'] = JRequest::getVar( 'format_post', 0, 'post', 'int' );
                $post['t_type'] = JRequest::getVar( 't_knock', 0, 'post', 'int' );
                $post['m_descr'] = JRequest::getVar( 'm_descr', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$row 	= new JTableMday($this->db);
		if (!$row->bind( $post )) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
		
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$row->checkin();
                
                
                // version 3.3 get tournament type
                $tourn = $this->getSeasAttr($row->s_id);
                if($tourn->tournament_type == '1'){
                    $md = new MatchdayRace($row->id, $tourn);
                    $md->saveMd();
                    $this->_id = $row->id;
                    return;
                }
                
                
		// save match
		$mj = 0;

		
		$prevarr = array();
		
		if($t_knock){
						
			if(isset($_POST['teams_kn']) && count($_POST['teams_kn'])){
		
				foreach($_POST['teams_kn'] as $home_team){
					$match 	= new JTableMatch($this->db);
		
					$match->load(isset($_POST['match_id'][$mj])?$_POST['match_id'][$mj]:0);
		
					$match->m_id = $row->id;
		
					$match->team1_id = intval($home_team);
//update	
					$match->team2_id = intval($_POST['teams_kn_aw'][$mj]);
					if($_POST['res_kn_1'][$mj] != ''){
						$match->score1 = intval($_POST['res_kn_1'][$mj]);
					}
					if($_POST['res_kn_1_aw'][$mj] != ''){
						$match->score2 = intval($_POST['res_kn_1_aw'][$mj]);
					}
					$match->k_ordering = $mj;
					$match->k_stage = 1;
					
					if(isset($_POST['kn_match_played_'.$mj])){
						$match->m_played = intval($_POST['kn_match_played_'.$mj]);
					}else{
						$match->m_played = 0;
					}
					
					if(!isset($_POST['res_kn_1'][$mj]) || !isset($_POST['res_kn_1_aw'][$mj]) || $_POST['res_kn_1'][$mj] == '' || $_POST['res_kn_1_aw'][$mj] == ''){
						$match->m_played = 0;
					}
					
					
					if(!$match->id){
						$query = "SELECT venue_id FROM #__bl_teams WHERE id=".$match->team1_id;
						$this->db->setQuery($query);
						$venue = $this->db->loadResult();
						if($venue){
							$match->venue_id = $venue;
						}	
					}
					
					$match->published = 1;
					if (!$match->check()) {
		
						JError::raiseError(500, $match->getError() );
		
					}
		
					if (!$match->store()) {
		
						JError::raiseError(500, $match->getError() );
		
					}
		
					$match->checkin();
					
					$prevarr[] = $match->id;
					
					$mj++;
				}
			}

            $levcount = 2;

            while (isset($_POST['teams_kn_'.$levcount])){

                $mj=0;
                foreach($_POST['teams_kn_'.$levcount] as $home_team){

                    if($levcount == 2){
                        $match_1 	= new JTableMatch($this->db);

                        $match_1->load(isset($_POST['match_id'][$mj*2])?$_POST['match_id'][$mj*2]:0);
                        $match_2 	= new JTableMatch($this->db);

                        $match_2->load(isset($_POST['match_id'][$mj*2+1])?$_POST['match_id'][$mj*2+1]:0);
                    }else{
                        if($_POST['final']){
                            $match_1 	= new JTableMatch($this->db);

                            $match_1->load(isset($_POST['matches_'.($levcount-1)][$mj*2])?$_POST['matches_'.($levcount-1)][$mj*2]:0);
                            $match_2 	= new JTableMatch($this->db);

                            $match_2->load(isset($_POST['lmatches_'.($levcount-1)][$mj*2])?$_POST['lmatches_'.($levcount-1)][$mj*2]:0);
                        }else{
                            $match_1 	= new JTableMatch($this->db);

                            $match_1->load(isset($_POST['matches_'.($levcount-1)][$mj*2])?$_POST['matches_'.($levcount-1)][$mj*2]:0);
                            $match_2 	= new JTableMatch($this->db);

                            $match_2->load(isset($_POST['matches_'.($levcount-1)][$mj*2+1])?$_POST['matches_'.($levcount-1)][$mj*2+1]:0);
                        }
                    }
                    //print_R($match_2);die;
                    //if((isset($match_1->id) && isset($match_2->id)) || ($match_1->m_played && $match_1->team1_id !=0 && $match_1->team2_id !=0) || ($match_2->m_played && $match_1->team1_id !=0 && $match_1->team2_id !=0) || ($match_1->team1_id == -1 && $match_1->team2_id != 0) || ($match_1->team2_id == -1 && $match_1->team1_id != 0) || ($match_2->team1_id == -1 && $match_2->team2_id != 0) || ($match_2->team2_id == -1 && $match_2->team1_id != 0)){

                        $match 	= new JTableMatch($this->db);

                        $match->load(isset($_POST['matches_'.$levcount][$mj])?$_POST['matches_'.$levcount][$mj]:0);

                        $match->m_id = $row->id;

                        $match->team1_id = intval($home_team);

                        if(!$match->team1_id && (($match_1->m_played && $match_1->team1_id !=0 && $match_1->team2_id !=0) || $match_1->team1_id == -1 || $match_1->team2_id == -1)){

                            if($match_1->team1_id == -1){
                                $match->team1_id = $match_1->team2_id;
                            }else
                                if($match_1->team2_id == -1){
                                    $match->team1_id = $match_1->team1_id;
                                }

                            if($match_1->score1 > $match_1->score2){
                                $match->team1_id = $match_1->team1_id;
                            }elseif($match_1->score1 < $match_1->score2){
                                $match->team1_id = $match_1->team2_id;
                            }else{
                                if($match_1->aet1 > $match_1->aet2){
                                    $match->team1_id = $match_1->team1_id;
                                }elseif($match_1->aet1 < $match_1->aet2){
                                    $match->team1_id = $match_1->team2_id;
                                }else{
                                    if($match_1->p_winner == $match_1->team1_id){
                                        $match->team1_id = $match_1->team1_id;
                                    }elseif($match_1->p_winner == $match_1->team2_id){
                                        $match->team1_id = $match_1->team2_id;
                                    }
                                }
                            }
                        }

                        $match->team2_id = intval($_POST['teams_kn_aw_'.$levcount][$mj]);

                        //$match->k_type = intval($_POST['lk_type'.$levcount][$mj]);

                        if(!$match->team2_id && (($match_2->m_played && $match_2->team1_id !=0 && $match_2->team2_id !=0) || $match_2->team1_id == -1 || $match_2->team2_id == -1)){

                            if($match_2->team1_id == -1){
                                $match->team2_id = $match_2->team2_id;
                            }else
                                if($match_2->team1_id == -1){
                                    $match->team2_id = $match_2->team1_id;
                                }

                            if($match_2->score1 > $match_2->score2){
                                $match->team2_id = $match_2->team1_id;
                            }elseif($match_2->score1 < $match_2->score2){
                                $match->team2_id = $match_2->team2_id;
                            }else{
                                if($match_2->aet1 > $match_2->aet2){
                                    $match->team2_id = $match_2->team1_id;
                                }elseif($match_2->aet1 < $match_2->aet2){
                                    $match->team2_id = $match_2->team2_id;
                                }else{
                                    if($match_2->p_winner == $match_2->team1_id){
                                        $match->team2_id = $match_2->team1_id;
                                    }elseif($match_2->p_winner == $match_2->team2_id){
                                        $match->team2_id = $match_2->team2_id;
                                    }
                                }
                            }
                        }
                        //var_dump($match);die();
                        if($_POST['res_kn_'.$levcount][$mj] != ''){
                            $match->score1 = intval($_POST['res_kn_'.$levcount][$mj]);
                        }
                        if($_POST['res_kn_'.$levcount.'_aw'][$mj] != ''){
                            $match->score2 = intval($_POST['res_kn_'.$levcount.'_aw'][$mj]);
                        }
                        $match->k_ordering = $mj;
                        $match->k_stage = $levcount;

                        if(isset($_POST['kn_match_played_'.$mj.'_'.$levcount])){
                            $match->m_played = intval($_POST['kn_match_played_'.$mj.'_'.$levcount]);
                        }else{
                            $match->m_played = 0;
                        }

                        if($_POST['res_kn_'.$levcount.'_aw'][$mj] == '' || $_POST['res_kn_'.$levcount][$mj] == ''){
                            $match->m_played = 0;
                        }


                        if(!$match->id){
                            $query = "SELECT venue_id FROM #__bl_teams WHERE id=".$match->team1_id;
                            $this->db->setQuery($query);
                            $venue = $this->db->loadResult();
                            if($venue){
                                $match->venue_id = $venue;
                            }
                        }


                        if(!$_POST['res_kn_'.$levcount][$mj] && !$_POST['res_kn_'.$levcount.'_aw'][$mj]){

                            $match->m_played = isset($match->m_played)?$match->m_played:1;
                        }else{
                            $match->m_played = isset($match->m_played)?$match->m_played:1;
                        }

                        $match->published = 1;
                        if (!$match->check()) {

                            JError::raiseError(500, $match->getError() );

                        }

                        if (!$match->store()) {

                            JError::raiseError(500, $match->getError() );

                        }

                        $match->checkin();
                        $mj++;

                        $prevarr[] = $match->id;
                   // }

                }
                $levcount++;
            }
	//////////////////

            $levcount = 1;
            while (isset($_POST['lteams_kn_'.$levcount])){

                $mj=0;
                foreach($_POST['lteams_kn_'.$levcount] as $home_team){

                    if($levcount == 1){
                        $match_1 	= new JTableMatch($this->db);

                        $match_1->load(isset($_POST['match_id'][$mj*2])?$_POST['match_id'][$mj*2]:0);
                        $match_2 	= new JTableMatch($this->db);

                        $match_2->load(isset($_POST['match_id'][$mj*2+1])?$_POST['match_id'][$mj*2+1]:0);
                    }elseif(($levcount%2)==0){
                        if ($levcount == 2 && $row->k_format > 4) {
                            $zz = $mj;
                            
                            if ($mj < $row->k_format/8) {
                                $zz = $mj + $row->k_format/8;
                            } else {
                                $zz = $mj - $row->k_format/8;
                            }
                            
                            $match_2 	= new JTableMatch($this->db);
                            $num = intval($levcount/4)?ceil($levcount/4):floor($levcount/4);
                            if($levcount==8||$levcount==10){$num += 1;}
                            $match_2->load(isset($_POST['lmatches_'.($levcount-1)][$mj])?$_POST['lmatches_'.($levcount-1)][$mj]:0);
                            $match_1 	= new JTableMatch($this->db);

                            $zz = $mj;

                           $match_1->load(isset($_POST['matches_'.($levcount- $num)][$zz])?$_POST['matches_'.($levcount - $num)][$zz]:0);   
                        } else {

                            $match_1 	= new JTableMatch($this->db);
                            $num = intval($levcount/4)?ceil($levcount/4):floor($levcount/4);
                            if($levcount==8||$levcount==10){$num += 1;}
                            $match_1->load(isset($_POST['lmatches_'.($levcount-1)][$mj])?$_POST['lmatches_'.($levcount-1)][$mj]:0);
                            $match_2 	= new JTableMatch($this->db);

                            $zz = $mj;

                           $match_2->load(isset($_POST['matches_'.($levcount- $num)][$zz])?$_POST['matches_'.($levcount - $num)][$zz]:0);                       
                        }
                    }else{
                        $match_1 	= new JTableMatch($this->db);

                        $match_1->load(isset($_POST['lmatches_'.($levcount-1)][$mj])?$_POST['lmatches_'.($levcount-1)][$mj]:0);
                        $match_2 	= new JTableMatch($this->db);

                        $match_2->load(isset($_POST['lmatches_'.($levcount-1)][$mj+1])?$_POST['lmatches_'.($levcount-1)][$mj+1]:0);
                    }

//////////////&&&&
                    //if(($match_1->id && $match_2->id) || ($match_1->m_played && $match_1->team1_id !=0 && $match_1->team2_id !=0) || ($match_2->m_played && $match_1->team1_id !=0 && $match_1->team2_id !=0) || ($match_1->team1_id == -1 && $match_1->team2_id != 0) || ($match_1->team2_id == -1 && $match_1->team1_id != 0) || ($match_2->team1_id == -1 && $match_2->team2_id != 0) || ($match_2->team2_id == -1 && $match_2->team1_id != 0)){

                        $match 	= new JTableMatch($this->db);

                        $match->load(isset($_POST['lmatches_'.$levcount][$mj])?$_POST['lmatches_'.$levcount][$mj]:0);

                        $match->m_id = $row->id;

                        $match->team1_id = intval($home_team);

                        if(!$match->team1_id && (($match_1->m_played && $match_1->team1_id !=0 && $match_1->team2_id !=0) || $match_1->team1_id == -1 || $match_1->team2_id == -1)){

                            if($match_1->team1_id == -1){
                                $match->team1_id = $match_1->team2_id;
                            }else
                                if($match_1->team2_id == -1){
                                    $match->team1_id = $match_1->team1_id;
                                }

                            if($match_1->score1 > $match_1->score2){
                                $match->team1_id = $match_1->team1_id;
                            }elseif($match_1->score1 < $match_1->score2){
                                $match->team1_id = $match_1->team2_id;
                            }else{
                                if($match_1->aet1 > $match_1->aet2){
                                    $match->team1_id = $match_1->team1_id;
                                }elseif($match_1->aet1 < $match_1->aet2){
                                    $match->team1_id = $match_1->team2_id;
                                }else{
                                    if($match_1->p_winner == $match_1->team1_id){
                                        $match->team1_id = $match_1->team1_id;
                                    }elseif($match_1->p_winner == $match_1->team2_id){
                                        $match->team1_id = $match_1->team2_id;
                                    }
                                }
                            }
                        }

                        $match->team2_id = intval($_POST['lteams_kn_aw_'.$levcount][$mj]);
                        $match->k_type = intval($_POST['lk_type_'.$levcount][$mj]);

                        if(!$match->team2_id && (($match_2->m_played && $match_2->team1_id !=0 && $match_2->team2_id !=0) || $match_2->team1_id == -1 || $match_2->team2_id == -1)){

                            if($match_2->team1_id == -1){
                                $match->team2_id = $match_2->team2_id;
                            }else
                                if($match_2->team1_id == -1){
                                    $match->team2_id = $match_2->team1_id;
                                }

                            if($match_2->score1 > $match_2->score2){
                                $match->team2_id = $match_2->team1_id;
                            }elseif($match_2->score1 < $match_2->score2){
                                $match->team2_id = $match_2->team2_id;
                            }else{
                                if($match_2->aet1 > $match_2->aet2){
                                    $match->team2_id = $match_2->team1_id;
                                }elseif($match_2->aet1 < $match_2->aet2){
                                    $match->team2_id = $match_2->team2_id;
                                }else{
                                    if($match_2->p_winner == $match_2->team1_id){
                                        $match->team2_id = $match_2->team1_id;
                                    }elseif($match_2->p_winner == $match_2->team2_id){
                                        $match->team2_id = $match_2->team2_id;
                                    }
                                }
                            }
                        }


                        if($_POST['lres_kn_'.$levcount][$mj] != ''){
                            $match->score1 = intval($_POST['lres_kn_'.$levcount][$mj]);
                        }
                        if($_POST['lres_kn_'.$levcount.'_aw'][$mj] != ''){
                            $match->score2 = intval($_POST['lres_kn_'.$levcount.'_aw'][$mj]);
                        }
                        $match->k_ordering = $mj;
                        $match->k_stage = $levcount;

                        if(isset($_POST['lkn_match_played_'.$mj.'_'.$levcount])){
                            $match->m_played = intval($_POST['lkn_match_played_'.$mj.'_'.$levcount]);
                        }else{
                            $match->m_played = 0;
                        }

                        if($_POST['lres_kn_'.$levcount.'_aw'][$mj] == '' || $_POST['lres_kn_'.$levcount][$mj] == ''){
                            $match->m_played = 0;
                        }


                        if(!$match->id){
                            $query = "SELECT venue_id FROM #__bl_teams WHERE id=".$match->team1_id;
                            $this->db->setQuery($query);
                            $venue = $this->db->loadResult();
                            if($venue){
                                $match->venue_id = $venue;
                            }
                        }


                        if(!$_POST['lres_kn_'.$levcount][$mj] && !$_POST['lres_kn_'.$levcount.'_aw'][$mj]){

                            $match->m_played = isset($match->m_played)?$match->m_played:1;
                        }else{
                            $match->m_played = isset($match->m_played)?$match->m_played:1;
                        }
                        /////////////////////////////////////////////////////////////////////////////
                        //if($match_1->m_played == 0 || $match_2->m_played == 0){          WARNING!!!!
                            //$match->m_played = 0;
                       // }////////////////////////////////////////////////////////////////////////////
                        //else{
                           // $match->m_played = 1;
                        //}
                        if(($match_1->team1_id == -1 || $match_1->team2_id == -1 || $match_2->team1_id == -1 or $match_2->team2_id == -1) && intval($_POST['lkn_match_played_'.$mj.'_'.$levcount])){
                            $match->m_played = 1;
                        }
                        $match->published = 1;
                        if (!$match->check()) {

                            JError::raiseError(500, $match->getError() );

                        }

                        if (!$match->store()) {

                            JError::raiseError(500, $match->getError() );

                        }

                        $match->checkin();
                        $mj++;

                        $prevarr[] = $match->id;
                   // }
                }
                $levcount++;
            }

	/////////////////
            $query = "SELECT id FROM #__bl_match WHERE m_id = ".$row->id." AND id NOT IN (".(count($prevarr)?implode(',',$prevarr):'-1').")";
            $this->db->setQuery($query);
            $mcids = $this->db->loadColumn();
			
				$this->db->setQuery("DELETE FROM #__bl_match WHERE m_id = ".$row->id." AND id NOT IN (".(count($prevarr)?implode(',',$prevarr):'-1').")");
				$this->db->query();


		
				if(count($mcids)){
					$cids = implode(',',$mcids);
					$this->db->setQuery("DELETE FROM #__bl_squard WHERE match_id IN (".$cids.")");
					$this->db->query();
					$this->db->setQuery("DELETE FROM #__bl_match_events WHERE match_id IN (".$cids.")");
					$this->db->query();
					$this->db->setQuery("DELETE FROM #__bl_subsin WHERE match_id IN (".$cids.")");
					$this->db->query();
					$this->db->setQuery("DELETE FROM #__bl_mapscore WHERE m_id IN (".$cids.")");
					$this->db->query();
					$this->db->setQuery("SELECT id FROM #__bl_extra_filds WHERE type='2'");
					$efcid = $this->db->loadColumn();
					if(count($efcid)){
						$efcids = implode(',',$efcid);
						$this->db->setQuery("DELETE FROM #__bl_extra_values WHERE f_id IN ('".$efcids."') AND uid IN ('".$cids."')");
						$this->db->query();
					}
				}
				
		}else{
			
		
			$arr_match = array();
		
			if(isset($_POST['home_team']) && count($_POST['home_team'])){
		
				foreach($_POST['home_team'] as $home_team){
		
					$match 	= new JTableMatch($this->db);
		
					$match->load($_POST['match_id'][$mj]);
		
					$match->m_id = $row->id;
		
					$match->team1_id = intval($home_team);
		
					$match->team2_id = intval($_POST['away_team'][$mj]);
					if($_POST['home_score'][$mj] != ''){
						$match->score1 = intval($_POST['home_score'][$mj]);
					}
					if($_POST['away_score'][$mj] != ''){
						$match->score2 = intval($_POST['away_score'][$mj]);
					}
					$match->is_extra = isset($_POST['extra_time'][$mj])?intval($_POST['extra_time'][$mj]):0;
					$match->published = 1;
		
					$match->m_played = intval($_POST['match_played'][$mj]);
		
					$match->m_date = strval($_POST['match_data'][$mj]);

		            $match->venue_id = intval($_POST['venue_id'][$mj]);
					$match->m_time = strval($_POST['match_time'][$mj]);
					$match->m_single = strval($_POST['matchtype'][$mj]);
					
					//betting
					$match->betavailable = intval($_POST['bet_available'][$mj]);
                    if ($match->betavailable){                    
                        $betfinishdate  = JRequest::getVar( 'betfinishdate', array(), 'post', 'array' );
                        $betfinishtime = JRequest::getVar( 'betfinishtime', array(), 'post', 'array' );
                        $match->betfinishdate = $betfinishdate[$mj];
                        $match->betfinishtime = $betfinishtime[$mj];
                    }
					
		

					
					if(!$match->id){
						$query = "SELECT venue_id FROM #__bl_teams WHERE id=".$match->team1_id;
						$this->db->setQuery($query);
						$venue = $this->db->loadResult();
						if($venue){
							$match->venue_id = $venue;
						}	
					}
		
					if (!$match->check()) {
		
						JError::raiseError(500, $match->getError() );
		
					}
		
					if (!$match->store()) {
		
						JError::raiseError(500, $match->getError() );
		
					}
					if ($match->betavailable){
                        $bet_coeff_old1 = JRequest::getVar( 'bet_coeff_old1', array(), 'post', 'array' );
                        $bet_coeff_old2 = JRequest::getVar( 'bet_coeff_old2', array(), 'post', 'array' );
                        if ($bet_coeff_old1[$match->id]){
                            foreach($bet_coeff_old1[$match->id] as $idevent=>$coeff1) {
                                $coeff1 = floatval($coeff1);
                                $coeff2 = floatval($bet_coeff_old2[$match->id][$idevent]);
                                $bet = new JTableBettingCoeffs($this->db);
                                $bet->idmatch = $match->id;
                                $bet->idevent = $idevent;
                                $bet->load(array('idmatch'=>$bet->idmatch, 'idevent'=>$bet->idevent));
                                if (!$bet->id) {
                                    $bet->idmatch = $match->id;
                                    $bet->idevent = $idevent;                                    
                                }
                                $bet->coeff1 = $coeff1;
                                $bet->coeff2 = $coeff2;
                                $bet->betfinishdate = $betfinishdate[$mj];
                                $bet->betfinishtime = $betfinishtime[$mj];
                                $bet->store();
                            }
                        }
					}	

		
					$match->checkin();
		
					$arr_match[] = $match->id;
		
					$mj++;
		
				}
				
				$query = "SELECT id FROM #__bl_match WHERE id NOT IN (".(count($arr_match)?implode(',',$arr_match):'-1').") AND m_id = ".$row->id;
				$this->db->setQuery($query);
				$mcids1 = $this->db->loadColumn();
                if(count($mcids1)){
                    $mcids = implode(',',$mcids1);
                    $this->db->setQuery("DELETE p,ap FROM #__bl_photos as p, #__bl_assign_photos as ap WHERE p.id = ap.photo_id AND ap.cat_type = 3 AND ap.cat_id IN (".$mcids.")");
                    $this->db->query();
                }
               // print_R($mcids);die;
				$this->db->setQuery("DELETE FROM #__bl_match WHERE id NOT IN (".(count($arr_match)?implode(',',$arr_match):'-1').") AND m_id = ".$row->id);
			    $this->db->query();
				if(count($mcids1)){
					$cids = implode(',',$mcids1);
					$this->db->setQuery("DELETE FROM #__bl_squard WHERE match_id IN (".$cids.")");
					$this->db->query();
					$this->db->setQuery("DELETE FROM #__bl_match_events WHERE match_id IN (".$cids.")");
					$this->db->query();
					$this->db->setQuery("DELETE FROM #__bl_subsin WHERE match_id IN (".$cids.")");
					$this->db->query();
					$this->db->setQuery("DELETE FROM #__bl_mapscore WHERE m_id IN (".$cids.")");
					$this->db->query();
					$this->db->setQuery("SELECT id FROM #__bl_extra_filds WHERE type='2'");
					$efcid = $this->db->loadColumn();
					if(count($efcid)){
						$efcids = implode(',',$efcid);
						$this->db->setQuery("DELETE FROM #__bl_extra_values WHERE f_id IN ('".$efcids."') AND uid IN ('".$cids."')");
						$this->db->query();
					}
				}
		
			}else{
				$query = "SELECT id FROM #__bl_match WHERE m_id = ".$row->id;
				$this->db->setQuery($query);
				$mcids = $this->db->loadColumn();

				$this->db->setQuery("DELETE FROM #__bl_match WHERE m_id = ".$row->id);
				$this->db->query();
                //
                if(count($mcids)){
                    $mcids = implode(',',$mcids);
                    $this->db->setQuery("DELETE p,ap FROM #__bl_photos as p, #__bl_assign_photos as ap WHERE p.id = ap.photo_id AND ap.cat_type = 3 AND ap.cat_id IN (".$mcids.")");
                    $this->db->query();
                }
                //print_r($mcids);die;
				$this->db->setQuery("DELETE FROM #__bl_squard WHERE match_id = ".$row->id);
				$this->db->query();
				$this->db->setQuery("DELETE FROM #__bl_match_events WHERE match_id = ".$row->id);
				$this->db->query();
				$this->db->setQuery("DELETE FROM #__bl_subsin WHERE match_id = ".$row->id);
				$this->db->query();
				$this->db->setQuery("DELETE FROM #__bl_mapscore WHERE m_id = ".$row->id);
				$this->db->query();
				$this->db->setQuery("SELECT id FROM #__bl_extra_filds WHERE type='2'");
				$efcid = $this->db->loadColumn();
		
				if(count($efcid) && count($mcids)){
					$efcids = implode(',',$efcid);
					$mcids = implode(',',$mcids);
					$this->db->setQuery("DELETE FROM #__bl_extra_values WHERE f_id IN ('".$efcids."') AND uid IN ('".$mcids."')");
					$this->db->query();
				}
		
			}
		}
	
		$this->_id = $row->id;
	}
	
	function deleteMday($cid){
		if(count($cid)){
			
		
			$cids = implode(',',$cid);
			$query = "SELECT id FROM #__bl_match WHERE m_id IN (".$cids.")";
				$this->db->setQuery($query);
				$mcids = $this->db->loadColumn();
		
			$this->db->setQuery("DELETE FROM #__bl_matchday WHERE id IN (".$cids.")");
			$this->db->query();
			$this->db->setQuery("DELETE m,p,ap FROM #__bl_match as m, #__bl_photos as p, #__bl_assign_photos as ap WHERE m.m_id IN (".$cids.") AND p.id = ap.photo_id AND ap.cat_type = 3 AND ap.cat_id = m.id"); //
			$this->db->query();
			$this->db->setQuery("DELETE FROM #__bl_squard WHERE match_id IN (".$cids.")");
			$this->db->query();
			$this->db->setQuery("DELETE FROM #__bl_match_events WHERE match_id IN (".$cids.")");
			$this->db->query();
			$this->db->setQuery("DELETE FROM #__bl_subsin WHERE match_id IN (".$cids.")");
			$this->db->query();
			$this->db->setQuery("DELETE FROM #__bl_mapscore WHERE m_id IN (".$cids.")");
			$this->db->query();
			$this->db->setQuery("SELECT id FROM #__bl_extra_filds WHERE type='2'");
			$efcid = $this->db->loadColumn();
			if(count($efcid)){
				$efcids = implode(',',$efcid);
				$mcids = implode(',',$mcids);
				//$this->db->setQuery("DELETE FROM #__bl_extra_values WHERE f_id IN (".$efcids.") AND uid IN (".$cids.")");
				$this->db->setQuery("DELETE FROM #__bl_extra_values WHERE f_id IN ('".$efcids."') AND uid IN ('".$mcids."')");
				$this->db->query();
			}
			
                        
                        //version 3.3 rounds
                         $query = " DELETE r,p"
                                ." FROM #__bl_rounds as r"
                                ." LEFT JOIN #__bl_rounds_participiants as p"
                                ." ON r.id = p.round_id"
                                ." WHERE r.md_id IN (".$cids.")";
                        $this->db->setQuery($query);
                        $this->db->query();
                        
		}
	}
	
	
}