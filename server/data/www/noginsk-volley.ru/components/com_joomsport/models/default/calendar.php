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

class calendarJSModel extends JSPRO_Models
{
	var $_lists = null;
	var $s_id = null;
	var $t_single = null;
	var $t_type = null;
	var $tid = null;
	var $mid = null;
	var $fromdate = null;
	var $todate   = null;
	var $teamhm	  = null;
	var $limit	  = null;
	var $limitstart = null;
	var $_total		= null;
	var $_pagination = null;
	var $title = null;
	var $p_title = null;
        var $_layout    = null;
        

	function __construct()
	{
		parent::__construct();
		
		$this->s_id = JRequest::getVar( 'sid', 0, '', 'int' );
        $this->title = JFactory::getDocument()->title;

		$this->limit	= JRequest::getVar( 'jslimit', 20,'', 'int' );
		$this->limitstart	= JRequest::getVar( 'page', 1,'', 'int' );
		$this->limitstart = intval($this->limitstart)>1?$this->limitstart:1;
		$this->tid = $this->mainframe->getUserStateFromRequest( 'com_joomsport.ftid'.$this->s_id, 'ftid', 0, 'int' );
		$this->mid = $this->mainframe->getUserStateFromRequest( 'com_joomsport.fmid'.$this->s_id,'fmid', 0, 'int' );
		$this->teamhm = $this->mainframe->getUserStateFromRequest( 'com_joomsport.fteamhm'.$this->s_id,'fteamhm', 0, '', 'int' );
		$this->fromdate = $this->mainframe->getUserStateFromRequest( 'com_joomsport.ffromdate'.$this->s_id,'ffromdate', '', 'string' );
		$this->todate = $this->mainframe->getUserStateFromRequest( 'com_joomsport.ftodate'.$this->s_id,'ftodate', '', 'string' );
		/*if(!$this->s_id){
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return; 
		}*/
		$query = "SELECT COUNT(*) FROM #__bl_seasons as s, #__bl_tournament as t WHERE t.published='1' AND s.published='1' AND t.id = s.t_id AND s.s_id = ".$this->s_id;
		$this->db->setQuery($query);
		if(!$this->s_id || !$this->db->loadResult()){
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return; 
		}

		//get tiurnament type
		$tourn = $this->getTournOpt($this->s_id);
		$this->t_single = $tourn->t_single;
		$this->t_type = 0; //$tourn->t_type;
		$this->_lists["t_type"] = $this->t_type;
                $this->_lists["tourn"] = $tourn;
                
                if($tourn->tournament_type != '1'){
                    $this->getPagination();
                }
		
		
	}

	function getData()
	{
        $not_m = array();
		
		$query = "SELECT CONCAT(t.name,' ',s.s_name) FROM #__bl_seasons as s, #__bl_tournament as t WHERE t.id = s.t_id AND s.s_id = ".$this->s_id;
		$this->db->setQuery($query);
		$this->p_title = $this->db->loadResult();

		//title  $this->title
		$this->_params = $this->JS_PageTitle($this->title?$this->title:$this->p_title);
		
		
	if($this->_lists["tourn"]->tournament_type != '1'){
		
		$this->_lists["matchs"] = $this->getMatchs();

		if(count($this->_lists["matchs"])){
			for($z=0;$z<count($this->_lists["matchs"]);$z++){
				if($this->t_single){
					$this->_lists["matchs"][$z]->home = $this->selectPlayerName($this->_lists["matchs"][$z]);
					$this->_lists["matchs"][$z]->away = $this->selectPlayerName($this->_lists["matchs"][$z],"fn2","ln2","nick2");
				}
				$query = "SELECT me.*,ev.*,CONCAT(p.first_name,' ',p.last_name) as p_name,p.first_name,p.last_name,p.nick"
						." FROM #__bl_match_events as me, #__bl_events as ev, #__bl_players as p"
						." WHERE me.player_id = p.id AND ev.player_event = '1' AND me.e_id = ev.id"
						." AND me.match_id = ".$this->_lists["matchs"][$z]->id." AND ".($this->t_single?"me.player_id=".$this->_lists["matchs"][$z]->hm_id:"me.t_id=".$this->_lists["matchs"][$z]->hm_id)
						." ORDER BY me.eordering,CAST(me.minutes AS UNSIGNED)";
				$this->db->setQuery($query);
				$this->_lists["matchs"][$z]->m_events_home = $this->db->loadObjectList();
				$error = $this->db->getErrorMsg();
				if ($error)
				{
					return JError::raiseError(500, $error);
				}
				if(count($this->_lists["matchs"][$z]->m_events_home)){
					for($i=0;$i<count($this->_lists["matchs"][$z]->m_events_home);$i++){
						$this->_lists["matchs"][$z]->m_events_home[$i]->p_name = $this->selectPlayerName($this->_lists["matchs"][$z]->m_events_home[$i]);
					}
				}
				$query = "SELECT me.*,ev.*,CONCAT(p.first_name,' ',p.last_name) as p_name,p.first_name,p.last_name,p.nick"
						." FROM #__bl_match_events as me, #__bl_events as ev, #__bl_players as p"
						." WHERE me.player_id = p.id AND ev.player_event = '1' AND me.e_id = ev.id"
						." AND me.match_id = ".$this->_lists["matchs"][$z]->id." AND ".($this->t_single?"me.player_id=".$this->_lists["matchs"][$z]->aw_id:"me.t_id=".$this->_lists["matchs"][$z]->aw_id)
						." ORDER BY me.eordering,CAST(me.minutes AS UNSIGNED)";
				$this->db->setQuery($query);
				
				$this->_lists["matchs"][$z]->m_events_away = $this->db->loadObjectList();
				$error = $this->db->getErrorMsg();
				if ($error)
				{
					return JError::raiseError(500, $error);
				}
				if(count($this->_lists["matchs"][$z]->m_events_away)){
					for($i=0;$i<count($this->_lists["matchs"][$z]->m_events_away);$i++){
						$this->_lists["matchs"][$z]->m_events_away[$i]->p_name = $this->selectPlayerName($this->_lists["matchs"][$z]->m_events_away[$i]);
					}
				}
				if($this->isBet()){
					$this->_lists["matchs"][$z]->betevents = $this->getMatchBetEvents($this->_lists["matchs"][$z]->id);
				}

                $not_m[] = $this->_lists["matchs"][$z]->id;
				
			}
		}

       // if($this->t_single){

            $query = "SELECT m.*,md.m_name,md.id as mdid, m.betavailable, IF(CONCAT(m.betfinishdate, ' ', m.betfinishtime)>NOW(),1,0) betfinish"
                ." FROM #__bl_matchday as md, #__bl_match as m "
                ." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id." AND (md.t_type = 1 OR md.t_type = 2) AND (m.m_date != '0000-00-00' OR ((m.team2_id != 0 AND m.team1_id = 0) OR (m.team2_id = 0 AND m.team1_id != 0))) ".(count($not_m)?"AND m.id NOT IN(".implode(',',$not_m).")":"")
                //.$date_sql
                .($this->mid?" AND md.id=".$this->mid:"")
                ." ORDER BY m.m_date,m.m_time,md.ordering,md.id";
            $this->db->setQuery($query);
            $mtch1 =  $this->db->loadObjectList();



        //if($this->t_single){
            for($k=0;$k<count($mtch1);$k++){
                if($this->t_single){
                     $query = "SELECT t1.first_name,
                                      t1.last_name,
                                      t1.nick
                                      FROM #__bl_players as t1
                                      WHERE t1.id = ".($mtch1[$k]->team1_id==0?$mtch1[$k]->team2_id:$mtch1[$k]->team1_id)." ";
                }else{
                    $query = "SELECT t1.t_name
                                      FROM #__bl_teams as t1
                                      WHERE t1.id = ".($mtch1[$k]->team1_id==0?$mtch1[$k]->team2_id:$mtch1[$k]->team1_id)." ";
                }
                    $this->db->setQuery($query);
                    $pl =  $this->db->loadObjectList();
//print_R($pl);echo "<hr/>";
                    if($mtch1[$k]->team1_id){
                        $mtch1[$k]->home = ($this->t_single)?$this->selectPlayerName($pl[0]):$pl[0]->t_name;
                        $mtch1[$k]->hm_id = $mtch1[$k]->team1_id;
                    }
                    if($mtch1[$k]->team2_id){
                        $mtch1[$k]->away = ($this->t_single)?$this->selectPlayerName($pl[0],"fn2","ln2","nick2"):$pl[0]->t_name;
                         $mtch1[$k]->aw_id = $mtch1[$k]->team2_id;
                    }


            }
        //}
        //print_r($mtch1);
        function mySort($f1,$f2){
            if(isset($f1->m_date) || isset($f2->m_date)){
                if($f1->m_date == '0000-00-00'){
                    return -1;
                }
                if($f2->m_date == '0000-00-00'){
                    return 1;
                }
                if($f1->m_date < $f2->m_date){
                    return -1;
                }
                else if($f1->m_date > $f2->m_date){
                    return 1;
                }else{
                    ///time
                    if($f1->m_time == ''){
                        return -1;
                    }
                    if($f2->m_time == ''){
                        return 1;
                    }
                    if($f1->m_time < $f2->m_time){
                        return -1;
                    }
                    else if($f1->m_time > $f2->m_time){
                        return 1;
                    }else{return 0;}
                }
            }
        }
        //}

                $this->_lists["matchs"] = array_merge($this->_lists["matchs"],(array)$mtch1);
                usort( $this->_lists["matchs"],'mySort');
		$this->_lists["enbl_extra"] = 0;
		if($this->s_id){
			$this->_lists["unable_reg"] = $this->unblSeasonReg();
		}
                $this->getCTFilter();
        }else{
            $query = "SELECT * FROM #__bl_matchday"
                    ." WHERE s_id = ".$this->s_id
                    ." ORDER BY start_date desc, id";
            $this->db->setQuery($query);
            $md1 =  $this->db->loadObjectList();
            $this->_lists["mdays_list"] = $md1;
            
            $this->_layout = 'race';
        }        
		$this->_lists["teams_season"] = $this->teamsToModer();;
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],@$this->_lists["unable_reg"],$this->s_id,0,0,1);
		
		$this->_lists['locven'] = $this->getJS_Config("cal_venue");		
		
	}
	function getMatchs(){
		
		$team_sql = '';
		if($this->tid){
			$team_sql .= $this->teamhm?(($this->teamhm == 1)?(" AND t1.id=".$this->tid):(" AND t2.id=".$this->tid)):(" AND (t1.id=".$this->tid." OR t2.id=".$this->tid.")");
		}
		
		$date_sql = '';
		if($this->fromdate){
			$date_sql .= " AND m.m_date >= '{$this->fromdate}'";
		}
		if($this->todate){
			$date_sql .= " AND m.m_date <= '{$this->todate}'";
		}
		
		if($this->t_single){
			$query = "SELECT m.*,md.m_name,md.id as mdid,t1.first_name, t1.last_name,t1.nick,t2.first_name as fn2,t2.last_name as ln2,t2.nick as nick2,t1.id as hm_id,t2.id as aw_id,m.betavailable, IF(CONCAT(m.betfinishdate, ' ', m.betfinishtime)>NOW(),1,0) betfinish"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
					." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id "
					.$team_sql
					.$date_sql
					.($this->mid?" AND md.id=".$this->mid:"")
					." ORDER BY m.m_date,m.m_time,md.ordering,md.id";
		}else{
			$query = "SELECT m.*,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away, t1.id as hm_id,t2.id as aw_id,t1.t_emblem as emb1,t2.t_emblem as emb2,m.betavailable, IF(CONCAT(m.betfinishdate, ' ', m.betfinishtime)>NOW(),1,0) betfinish"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2"
					." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id "
					.$team_sql
					.$date_sql
					.($this->mid?" AND md.id=".$this->mid:"")
					." ORDER BY m.m_date,m.m_time,md.ordering,md.id";
		}
		$this->db->setQuery($query,($this->limitstart-1)*$this->limit, $this->limit);
    
		$mtch =  $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		return $mtch;
	}
	function getCTFilter(){
		if($this->t_single){
			$query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id"
					." FROM #__bl_players as t, #__bl_season_players as st"
					." WHERE st.player_id = t.id AND st.season_id = ".$this->s_id
					." ORDER BY t.first_name";		
		}else{
			$query = "SELECT t.* FROM #__bl_teams as t, #__bl_season_teams as st"
					." WHERE st.team_id = t.id AND st.season_id = ".$this->s_id
					." ORDER BY t.t_name";	
		}
		
		$this->db->setQuery($query);
		$parti = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		$arr = array();
		$arr[] = JHTML::_("select.option",0,JText::_('BLFA_ALL'),'id','t_name');
		
		if(count($parti)){
			$arr = array_merge($arr,$parti);
		}
		$javascript = "";
		$this->_lists['teams'] = JHTML::_('select.genericlist',   $arr, 'ftid', (js_mobile::isMobile()?'class="form-control pull-right"':'class="styled"').' size="1" '.$javascript, 'id', 't_name', $this->tid);
		
		$arr = array();
		$arr[] = JHTML::_("select.option",0,JText::_('BLFA_ALL'),'id','m_name');
		
		$query = "SELECT id,m_name FROM #__bl_matchday WHERE s_id=".$this->s_id." ORDER BY ordering,id";
		$this->db->setQuery($query);
		$mdays = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		if(count($mdays)){
			$arr = array_merge($arr,$mdays);
		}

		$this->_lists['mdays'] = JHTML::_('select.genericlist',   $arr, 'fmid', (js_mobile::isMobile()?'class="form-control pull-right"':'class="styled"').' size="1" '.$javascript, 'id', 'm_name', $this->mid);
		if (!js_mobile::isMobile()) {
                    $this->_lists['fromdate'] = JHTML::_('calendar', $this->fromdate, 'ffromdate', 'ffromdate', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'12',  'maxlength'=>'10')); 
                    $this->_lists['todate'] = JHTML::_('calendar', $this->todate, 'ftodate', 'ftodate', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'12',  'maxlength'=>'10')); 
                }
		$arr = array();
		$arr[] = JHTML::_("select.option",0,JText::_('BLFA_ALL'),'id','name');
		$arr[] = JHTML::_("select.option",1,JText::_('BLFA_HOMETEAM'),'id','name');
		$arr[] = JHTML::_("select.option",2,JText::_('BLFA_AWAYTEAM'),'id','name');
		$this->_lists['teamhm'] = JHTML::_('select.genericlist',   $arr, 'fteamhm', (js_mobile::isMobile()?'class="form-control pull-right"':'class="styled-short secst"').' size="1" '.$javascript, 'id', 'name', $this->teamhm);
	}
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			$this->_pagination = new JS_Pagination($this->getTotal(),$this->limitstart,$this->limit);
		}
		
		return $this->_pagination;
	}
	function getTotal()
	{
			if (empty($this->_total))
			{
				$team_sql = '';
				if($this->tid){
					$team_sql .= $this->teamhm?(($this->teamhm == 1)?(" AND t1.id=".$this->tid):(" AND t2.id=".$this->tid)):(" AND (t1.id=".$this->tid." OR t2.id=".$this->tid.")");
				}
				
				$date_sql = '';
				if($this->fromdate){
					$date_sql .= " AND m.m_date >= '{$this->fromdate}'";
				}
				if($this->todate){
					$date_sql .= " AND m.m_date <= '{$this->todate}'";
				}
				
				if($this->t_single){
				$query = "SELECT COUNT(m.id)"
						." FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
						." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id "
						.$team_sql
						.$date_sql;
				}else{
					$query = "SELECT COUNT(m.id)"
							." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2"
							." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id "
							.$team_sql
							.$date_sql
							.($this->mid?" AND md.id=".$this->mid:"");
				}
				$this->db->setQuery($query);
				$this->_total = $this->db->loadResult();
				$error = $this->db->getErrorMsg();
				if ($error)
				{
					return JError::raiseError(500, $error);
				}
			}

		return $this->_total;
	}
	
	//betting
	
	function getMatchBetEvents($idmatch){
        $query = "SELECT bbc.*, bbe.*"
                ."\n FROM #__bl_betting_events bbe"
                ."\n INNER JOIN #__bl_betting_coeffs bbc ON bbc.idevent=bbe.id"
                ."\n WHERE bbc.idmatch =".$idmatch;

        $this->db->setQuery($query);
        $matchevents = $this->db->loadObjectList();

        return $matchevents;
    }
	
	function saveBets(){
        $betmatches = JRequest::getVar('bet_match');
        $bet_events_radio = JRequest::getVar('betevents_radio');
        $bet_events_points1 = JRequest::getVar('betevents_points1');
        $bet_events_points2 = JRequest::getVar('betevents_points2');
        if ($betmatches) {
            $userpoints = $this->getUserPoints(JFactory::getUser()->get('id'));
            $points = 0;
            $matches = array();
            foreach($betmatches as $idmatch){
                $match = new JTableMatch($this->db);
                $match->load($idmatch);
                if($match->betfinishdate.' '.$match->betfinishtime > date('Y-m-d H:i') && $match->betavailable){
                    $matches[] = $match;
                    if ($bet_events_radio[$idmatch]){
                        foreach($bet_events_radio[$idmatch] as $idevent=>$value){
                            $points += (float)$bet_events_points1[$idmatch][$idevent] + (float)$bet_events_points2[$idmatch][$idevent];
                        }
                    }
                }
            }
            if ($userpoints < $points) {
                return BLFA_BET_NOT_ENOUGH_POINTS;
            }
            
            if ($matches) {
                foreach($matches as $match){
                    $idmatch = $match->id;
                    if ($bet_events_radio[$idmatch]){
                        foreach($bet_events_radio[$idmatch] as $idevent=>$value){
                            $who=0;
                            if ((float)$bet_events_points1[$idmatch][$idevent]){
                                $currentbetpoints = (float)$bet_events_points1[$idmatch][$idevent];
                                $who=1;
                            } elseif ((float)$bet_events_points2[$idmatch][$idevent]){
                                $currentbetpoints = (float)$bet_events_points2[$idmatch][$idevent];
                                $who=2;
                            }
                            $this->saveBet($currentbetpoints, $idmatch, $idevent, $who);
                        }
                    }
                }
            }
        }
        return 1;
    }
	
}	