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
//require(JPATH_SITE.'/administrator/components/com_joomsport/models/default/knockout.php');
require(dirname(__FILE__).'/knockout.php');
require_once(JPATH_SITE.'/administrator/components/com_joomsport/classes/class-matchday-race.php');

class tableJSModel extends JSPRO_Models
{
	
	var $_data = null;
	var $_lists = null;
	var $s_id = null;
	var $gr_id = null;
	var $t_single = null;
	var $t_type = null;
	var $_layout = null;
	var $_params = null;
    var $knock_type  = null;
    var $title = null;
    var $p_title = null;
	
	function __construct()
	{
		parent::__construct();
		
		$this->gr_id = JRequest::getVar( 'gr_id', 0, '', 'int' );
		$this->s_id = JRequest::getVar( 'sid', 0, '', 'int' );
		$this->_lists["table_n"] = JRequest::getVar( 'tab_n', '', '', 'string' );
		//print_r($this->_lists["table_n"]);
        $this->title = JFactory::getDocument()->getTitle();
		
		$query = "SELECT COUNT(*) FROM #__bl_seasons as s, #__bl_tournament as t WHERE t.published='1' AND s.published='1' AND t.id = s.t_id AND s.s_id = ".$this->s_id;
		$this->db->setQuery($query);
		if(!$this->s_id || !$this->db->loadResult()){
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return; 
		}
		
	}

	function getData()
	{
        $this->knock_type = new JS_Knockout();

		$query = "SELECT CONCAT(t.name,' ',s.s_name) FROM #__bl_seasons as s, #__bl_tournament as t WHERE t.id = s.t_id AND s.s_id = ".$this->s_id;
		$this->db->setQuery($query);
		$this->p_title = $this->db->loadResult();
        $this->_params = $this->JS_PageTitle($this->title?$this->title:$this->p_title);
		//get season options
		$this->SeasOptsAll();
		//get tiurnament type
		$tourn = $this->getTournOpt($this->s_id);
		$this->t_single = $tourn->t_single;
		$this->t_type = 0;
                $this->_lists['tournament_type'] = $tourn->tournament_type;
		//season admin links
		$this->_lists['adm_links'] = $this->getAdmLinks();
		$this->_lists['teamlogo_height'] = $this->getJS_Config('teamlogo_height');
		//get matches
		$this->_lists['matches'] = $this->getmatches();
        $this->_lists['knmatches'] = $this->getKnMatches();
    //print_r($this->_lists['knmatches']);
		//unable registration?
		$this->_lists["unable_reg"] = $this->unblSeasonReg();
                
                $round_options = json_decode($tourn->season_options);
                
                if($this->_lists['tournament_type'] == '1' && $round_options->race_points == '0'){
                    $this->getRounds($tourn);
                }else{
		//
		//if($this->t_type){
			$this->KnockTable();
			//$this->_layout = "knock";
		//}else{
			$this->TournTable();
			$this->_layout = null;
		//}
		//
                }

		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],$this->_lists["unable_reg"],$this->s_id,1);
		$query = "SELECT * FROM #__bl_seasons as s, #__bl_tournament as t WHERE t.id = s.t_id AND s.s_id = ".$this->s_id;
		$this->db->setQuery($query);
		$this->_lists['curseas'] = $this->db->loadObject();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		//tabs
		//$this->getTabsLT();
		
		///-----EXTRAFIELDS---//
		
		$this->_lists['ext_fields'] = $this->getAddFields($this->s_id,'3','season');

		//social buttons
		$tLogo = '';
		if($this->_lists["curseas"]->logo && is_file('media/bearleague/'.$this->_lists["curseas"]->logo)){
			$tLogo = JURI::base()."media/bearleague/".$this->_lists["curseas"]->logo;
		}
		$this->_lists['socbut'] = $this->getSocialButtons('jsbp_season',$this->p_title,$tLogo,htmlspecialchars(strip_tags($this->_lists['season_par']->s_descr)));
		$this->_lists['jsbrand_on'] = $this->getJS_Config('jsbrand_on');
                
                $query = "SELECT 1 FROM #__bl_matchday AS md JOIN #__bl_match AS m ON m.m_id = md.id WHERE md.s_id = ".$this->s_id." AND md.t_type = 0";
		$this->db->setQuery($query);                
		$this->_lists['is_group_matches'] = (int)$this->db->loadResult();
                
                $query = "SELECT 1 FROM #__bl_matchday AS md JOIN #__bl_match AS m ON m.m_id = md.id WHERE md.s_id = ".$this->s_id." ";
		$this->db->setQuery($query);
		$this->_lists['is_matches'] = (int)$this->db->loadResult();
                $this->_lists['locven'] = $this->getJS_Config("cal_venue");

	}
	function getTabsLT(){
		$this->_lists['jstabs'] = '';
		$this->set_JS_tabs();
		$str = '';
		$divs = array();
		$str .= $this->jstab->newTab(JText::_('BL_TAB_TBL'),'etab_main','tab_star',(($this->_lists["unable_reg"] && $this->_lists['season_par']->s_rules)?'hide':'vis'));
	    $divs[] = "etab_main_div";
		if($this->_lists['season_par']->s_rules){
		  $str .= $this->jstab->newTab(JText::_('BL_TAB_RULES'),'etab_rules','tab_flag',($this->_lists["unable_reg"]?'vis':'hide'));
		  $divs[] = "etab_rules_div";
	    }
	    if($this->_lists['season_par']->s_descr){
		  $str .= $this->jstab->newTab(JText::_('BL_TAB_ABOUTSEAS'),'etab_aboutm','tab_flag');
		  $divs[] = "etab_aboutm_div";
	    }
		
		if($this->jstab->count > 1){
			$this->_lists['jstabs'] = $str;
			$this->_lists['jstabs_divs'] = $divs;
		}
	}
	function SeasOptsAll(){
		$this->_lists["available_options"] = array("played_chk"=>JText::_("BL_TBL_PLAYED"),"emblem_chk"=>'',"win_chk"=>JText::_("BL_TBL_WINS"),"lost_chk"=>JText::_("BL_TBL_LOST"),"draw_chk"=>JText::_("BL_TBL_DRAW"),
												   "otwin_chk"=>JText::_("BL_TBL_EXTRAWIN"),"otlost_chk"=>JText::_("BL_TBL_EXTRALOST"),"diff_chk"=>JText::_("BL_TBL_DIFF"),"gd_chk"=>JText::_("BL_TBL_GD"),
												   "point_chk"=>JText::_("BL_TBL_POINTS"),"percent_chk"=>JText::_("BL_TBL_WINPERCENT"),"goalscore_chk"=>JText::_("BL_TBL_TTGSC"),"goalconc_chk"=>JText::_("BL_TBL_TTGCC"),"winhome_chk"=>JText::_("BL_TBL_TTWHC"),
												   "winaway_chk"=>JText::_("BL_TBL_TTWAC"),"drawhome_chk"=>JText::_("BL_TBL_TTDHC"),"drawaway_chk"=>JText::_("BL_TBL_TTDAC"),"losthome_chk"=>JText::_("BL_TBL_TTLHC"),"lostaway_chk"=>JText::_("BL_TBL_TTLAC"),
												   "pointshome_chk"=>JText::_("BL_TBL_TTPHC"),"pointsaway_chk"=>JText::_("BL_TBL_TTPAC"),"grwin_chk"=>JText::_("BL_WINGROUP"),"grlost_chk"=>JText::_("BL_LOSTGROUP"),"grwinpr_chk"=>JText::_("BL_PRCGROUP"));
		
		$this->_lists["soptions"] = array();	
		$query = "SELECT * FROM #__bl_season_option WHERE opt_value='1' AND s_id = ".$this->s_id." AND opt_name != 'equalpts_chk' ORDER BY ordering";
		$this->db->setQuery($query);
		$listsss = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		for($i=0;$i<count($listsss);$i++){
			$vars = get_object_vars( $listsss[$i]  );
			$this->_lists["soptions"][$vars["opt_name"]] = $vars["opt_value"];
		}
	}
	function getmatches(){
		//$orderby = $this->t_type?"md.id,m.k_stage,m.k_ordering":"m.m_date,m.m_time,md.id";
		if($this->t_single){
			$query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid,t1.nick as nick1,t2.nick as nick2, CONCAT(t1.first_name,' ',t1.last_name) as home, CONCAT(t2.first_name,' ',t2.last_name) as away,t1.id as hm_id,t2.id as aw_id,IF(m.score1>m.score2,CONCAT(t1.first_name,' ',t1.last_name),CONCAT(t2.first_name,' ',t2.last_name)) as winner, IF(m.score1>m.score2,t1.id,t2.id) as winnerid "
					." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_players as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_players as t2 ON m.team2_id = t2.id"
					." WHERE m.m_id = md.id AND m.published = 1 AND md.t_type = 0 AND md.s_id=".$this->s_id
					." ORDER BY md.id,m.k_stage,m.k_ordering";
		
		}else{
			$query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away,t1.id as hm_id,t2.id as aw_id,IF(m.score1>m.score2,t1.t_name,t2.t_name) as winner,IF(m.score1>m.score2,t1.id,t2.id) as winnerid"
					." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN  #__bl_teams as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_teams as t2 ON m.team2_id = t2.id"
					." WHERE m.m_id = md.id AND m.published = 1 AND md.t_type = 0 AND md.s_id=".$this->s_id
					." ORDER BY m.m_date,m.m_time,md.id";
		
		}
		
		$this->db->setQuery($query);
    
		$mtch =  $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		return $mtch;
	}
    function getKnMatches(){
       // $orderby = $this->t_type?"md.id,m.k_stage,m.k_ordering":"m.m_date,m.m_time,md.id";
        if($this->t_single){
            $query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid,t1.nick as nick1,t2.nick as nick2, CONCAT(t1.first_name,' ',t1.last_name) as home, CONCAT(t2.first_name,' ',t2.last_name) as away,t1.id as hm_id,t2.id as aw_id,IF(m.score1>m.score2,CONCAT(t1.first_name,' ',t1.last_name),CONCAT(t2.first_name,' ',t2.last_name)) as winner, IF(m.score1>m.score2,t1.id,t2.id) as winnerid "
                ." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_players as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_players as t2 ON m.team2_id = t2.id"
                ." WHERE m.m_id = md.id AND m.published = 1 AND (md.t_type = 1 OR md.t_type = 2) AND md.s_id=".$this->s_id
                ." ORDER BY md.id,m.k_stage,m.k_ordering";

        }else{
            $query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away,t1.id as hm_id,t2.id as aw_id,IF(m.score1>m.score2,t1.t_name,t2.t_name) as winner,IF(m.score1>m.score2,t1.id,t2.id) as winnerid"
                ." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN  #__bl_teams as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_teams as t2 ON m.team2_id = t2.id"
                ." WHERE m.m_id = md.id AND m.published = 1 AND (md.t_type = 1 OR md.t_type = 2) AND md.s_id=".$this->s_id
                ." ORDER BY md.id,m.k_stage,m.k_ordering";

        }

        $this->db->setQuery($query);

        $knmtch =  $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error)
        {
            return JError::raiseError(500, $error);
        }
        return $knmtch;
    }
	function TournTable(){
		$groups_exists = array();	
		$table_view = array();
		$user	= JFactory::getUser();
		
		$teams_your_color = $this->getJS_Config('yteam_color');
		$season_par = $this->_lists['season_par'];
		if($this->t_single){
			$query = "SELECT t.id,bonus_point,t.first_name,t.last_name,'' as t_yteam,t.nick"
					." FROM #__bl_season_players as st, #__bl_players as t"
					." WHERE t.id = st.player_id AND st.season_id = ".$this->s_id;
		}else{
			$query = "SELECT t.id,bonus_point,t.t_yteam,t.t_name,t.t_emblem"
					." FROM #__bl_season_teams as st, #__bl_teams as t"
					." WHERE t.id = st.team_id AND st.season_id = ".$this->s_id;
		}
		$this->db->setQuery($query);
		$teams = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
///////////////ext fld name
		if(!$this->t_single){
			$query = "SELECT ef.name, ef.id FROM #__bl_extra_filds as ef  WHERE ef.published=1 AND ef.type = '1' AND ef.e_table_view = '1' AND ef.fdisplay = '1' ORDER BY ef.ordering";
		}else{
			$query = "SELECT ef.name, ef.id FROM #__bl_extra_filds as ef  WHERE ef.published=1 AND ef.type = '0' AND ef.e_table_view = '1' AND ef.fdisplay = '1' ORDER BY ef.ordering";
		}
		$this->db->setQuery($query);
		
		
		//$this->_lists["ext_fields_name"] = $this->db->loadObjectList();
		$ext_fields_name = $this->db->loadObjectList();
		$this->_lists["ext_fields_name"] = $ext_fields_name;
// print_r($this->_lists["ext_fields_name"]);
//exp
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		
		
		$bonus_not = array();
		
		for ($i=0;$i<count($teams);$i++){
			$tid = $teams[$i]->id;
			if($this->t_single){
				$teams_name = $this->selectPlayerName($teams[$i]);
			}else{
				$teams_name = $teams[$i]->t_name;
			}
			$teams_your = $teams[$i]->t_yteam;
			
			
			if($this->t_single){
				$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$tid;
				$this->db->setQuery($query);
				$photos = $this->db->loadObjectList();
				$error = $this->db->getErrorMsg();
				if ($error)
				{
					return JError::raiseError(500, $error);
				}
				
				$query = "SELECT p.*,c.country,c.ccode FROM #__bl_players as p LEFT JOIN #__bl_countries as c ON c.id=p.country_id  WHERE p.id = ".$tid;
				$this->db->setQuery($query);
				$players = $this->db->loadObjectList();
				$player = $players[0];
				$error = $this->db->getErrorMsg();
				if ($error)
				{
					return JError::raiseError(500, $error);
				}
				
				$emblems = '';
				if($player->def_img){
					$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$player->def_img;
					$this->db->setQuery($query);
					$emblems = $this->db->loadResult();
				}else if(isset($photos[0])){
					$emblems = $photos[0]->filename;
				}
			}else{
				$emblems = $teams[$i]->t_emblem;
			}
			
			if($this->t_single){
				$query = "SELECT gr.g_id FROM  #__bl_season_players as st, #__bl_grteams as gr, #__bl_groups as g WHERE g.s_id = ".$this->s_id." AND g.id = gr.g_id AND gr.t_id = st.player_id AND st.season_id = ".$this->s_id." AND st.player_id = ".$tid." LIMIT 1";
			
			}else{
				$query = "SELECT gr.g_id FROM  #__bl_season_teams as st, #__bl_grteams as gr, #__bl_groups as g WHERE g.s_id = ".$this->s_id." AND g.id = gr.g_id AND gr.t_id = st.team_id AND st.season_id = ".$this->s_id." AND st.team_id = ".$tid." LIMIT 1";
			}
			$this->db->setQuery($query);
			$group_id = (int) $this->db->loadResult();
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
			if(!in_array($group_id,$groups_exists) && $group_id){
				if($this->gr_id && $season_par->s_groups){	
					if($this->gr_id==$group_id){
						$groups_exists[] = $group_id;
					}
				}else{
					$groups_exists[] = $group_id;
				}
				
			}
			$this->_lists["gr_id"] = $this->gr_id;
			$teams[$i]->bonus_point;
			if($teams[$i]->bonus_point && $teams[$i]->bonus_point != '0.00'){
				if(!isset($bonus_not[$group_id]) || !$bonus_not[$group_id]){
					
					$bonus_not[$group_id] = $teams_name." - ".$teams[$i]->bonus_point."<br />";
				}else{
					$bonus_not[$group_id] .= $teams_name." - ".$teams[$i]->bonus_point."<br />";
				}
			}
			
			
			//var_dump($this->_lists['bonus_not']);
			// in groups
			$query = "SELECT t_id FROM #__bl_grteams WHERE t_id != ".$tid;
			$query .= ($group_id)?(" AND g_id = ".$group_id):"";

			$this->db->setQuery($query);
			$gtid = $this->db->loadColumn();
			//var_dump($gtid);
			//print_r($season_par->s_groups);
		if($season_par->s_groups == 1){	//updt
			if(count($gtid)){
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND ((".$tid." = m.team1_id AND m.score1 > m.score2 AND m.team2_id IN (".implode(",",$gtid).")) OR (".$tid." = m.team2_id AND m.score1 < m.score2 AND m.team1_id IN (".implode(",",$gtid).")) ) ".($season_par->s_enbl_extra?"":" AND m.is_extra = 0")."  AND m.m_played = 1 AND md.is_playoff = 0 AND md.t_type = 0";
				$this->db->setQuery($query);
				$wins_gr = $this->db->loadResult();
				$error = $this->db->getErrorMsg();
				if ($error)
				{
					return JError::raiseError(500, $error);
				}
				
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND ((".$tid." = m.team1_id AND m.score1 = m.score2 AND m.team2_id IN (".implode(",",$gtid).")) OR (".$tid." = m.team2_id AND m.score1 = m.score2 AND m.team1_id IN (".implode(",",$gtid).")) ) ".($season_par->s_enbl_extra?"":" AND m.is_extra = 0")." AND m.m_played = 1 AND md.is_playoff = 0 AND md.t_type = 0";
				$this->db->setQuery($query);
				$draw_gr = $this->db->loadResult();
				$error = $this->db->getErrorMsg();
				if ($error)
				{
					return JError::raiseError(500, $error);
				}
				
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND ((".$tid." = m.team1_id AND m.score1 < m.score2 AND m.team2_id IN (".implode(",",$gtid).")) OR (".$tid." = m.team2_id AND m.score1 > m.score2 AND m.team1_id IN (".implode(",",$gtid).")) ) ".($season_par->s_enbl_extra?"":" AND m.is_extra = 0")." AND md.is_playoff = 0 AND m.m_played = 1 AND md.t_type = 0";
				$this->db->setQuery($query);
				$loose_gr = $this->db->loadResult();
				$error = $this->db->getErrorMsg();
				if ($error)
				{
					return JError::raiseError(500, $error);
				}
				if(($wins_gr+$loose_gr+$draw_gr) > 0){
					$table_view[$i]['grwinpr_chk'] = ($wins_gr + $draw_gr/2)/($wins_gr+$loose_gr+$draw_gr);
				}else{
					$table_view[$i]['grwinpr_chk'] = 0;
				}
			}
			else{
				$wins_gr = 0;
				$loose_gr = 0;
			}
		}	
                
                if($this->_lists['tournament_type'] != 1){
			$query = "SELECT SUM(score1) as sc,SUM(score2) as rc FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND md.is_playoff = 0 AND m.m_played = 1 AND md.t_type = 0 AND m.team1_id = ".$tid;
			$this->db->setQuery($query);
			$home = $this->db->loadObjectList();
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
			$query = "SELECT SUM(score1) as rc,SUM(score2) as sc FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND md.is_playoff = 0 AND m.m_played = 1 AND md.t_type = 0 AND m.team2_id = ".$tid;
			$this->db->setQuery($query);
			$away = $this->db->loadObjectList();
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
			
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team1_id AND m.score1 > m.score2) AND m.is_extra = 0 AND m.m_played = 1 AND md.is_playoff = 0 AND md.t_type = 0";
			$this->db->setQuery($query);
			$wins = $this->db->loadResult();
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team1_id) AND m.score1 = m.score2  AND m.m_played = 1 AND md.is_playoff = 0 AND md.t_type = 0";
			$this->db->setQuery($query);
			$drows = $this->db->loadResult();
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team1_id AND m.score1 < m.score2) AND m.is_extra = 0 AND md.is_playoff = 0 AND m.m_played = 1 AND md.t_type = 0";
			$this->db->setQuery($query);
			$loose = $this->db->loadResult();
			
			$query = "SELECT SUM(bonus1) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND ".$tid." = m.team1_id AND md.is_playoff = 0 AND m.m_played = 1 AND md.t_type = 0";
			$this->db->setQuery($query);
			$bonus1 = $this->db->loadResult();
			$query = "SELECT SUM(bonus2) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND ".$tid." = m.team2_id AND md.is_playoff = 0 AND m.m_played = 1 AND md.t_type = 0";
			$this->db->setQuery($query);
			$bonus2 = $this->db->loadResult();
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (m.team2_id = ".$tid." AND m.score2 > m.score1) AND m.is_extra = 0 AND m.m_played = 1 AND md.is_playoff = 0 AND md.t_type = 0";
			$this->db->setQuery($query);
			$wins_away = $this->db->loadResult();
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team2_id) AND m.score1 = m.score2  AND m.m_played = 1 AND md.is_playoff = 0 AND md.t_type = 0";
			$this->db->setQuery($query);
			$drows_away = $this->db->loadResult();
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team2_id AND m.score2 < m.score1)  AND m.is_extra = 0 AND md.is_playoff = 0 AND m.m_played = 1 AND md.t_type = 0";
			$this->db->setQuery($query);
			$loose_away = $this->db->loadResult();
			
			$query = "SELECT SUM(points1) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND ".$tid." = m.team1_id AND md.is_playoff = 0 AND m.m_played = 1 AND m.new_points = '1' AND md.t_type = 0";
			$this->db->setQuery($query);
			$homebonus = $this->db->loadResult();
			$query = "SELECT SUM(points2) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND ".$tid." = m.team2_id AND md.is_playoff = 0 AND m.m_played = 1 AND m.new_points = '1' AND md.t_type = 0";
			$this->db->setQuery($query);
			$awabonus = $this->db->loadResult();
			//--// 
			
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team1_id AND m.score1 > m.score2) AND m.m_played = 1 AND m.is_extra = 0 AND md.is_playoff = 0 AND m.new_points = '0' AND md.t_type = 0";
				$this->db->setQuery($query);
				$wins2 = $this->db->loadResult();
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team1_id) AND m.score1 = m.score2  AND m.m_played = 1   AND md.is_playoff = 0 AND m.new_points = '0' AND md.t_type = 0";
				$this->db->setQuery($query);
				$drows2 = $this->db->loadResult();
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team1_id AND m.score1 < m.score2) AND md.is_playoff = 0 AND m.is_extra = 0 AND m.m_played = 1 AND m.new_points = '0' AND md.t_type = 0";
				$this->db->setQuery($query);
				$loose2 = $this->db->loadResult();
				
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (m.team2_id = ".$tid." AND m.score2 > m.score1)  AND m.m_played = 1 AND md.is_playoff = 0 AND m.new_points = '0' AND m.is_extra = 0 AND md.t_type = 0";
				$this->db->setQuery($query);
				$wins_away2 = $this->db->loadResult();
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team2_id) AND m.score1 = m.score2  AND m.m_played = 1 AND md.is_playoff = 0 AND m.new_points = '0' AND md.t_type = 0";
				$this->db->setQuery($query);
				$drows_away2 = $this->db->loadResult();
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND (".$tid." = m.team2_id AND m.score2 < m.score1)   AND md.is_playoff = 0 AND m.m_played = 1 AND m.new_points = '0' AND m.is_extra = 0 AND md.t_type = 0";
				$this->db->setQuery($query);
				$loose_away2 = $this->db->loadResult();
			//--//
			
			if($season_par->s_enbl_extra){
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND ((m.team2_id = ".$tid." AND m.score2 > m.score1) OR (".$tid." = m.team1_id AND m.score1 > m.score2)) AND m.is_extra = 1 AND md.is_playoff = 0 AND m.m_played = 1 AND md.t_type = 0";
				$this->db->setQuery($query);
				$wins_ext = $this->db->loadResult();
				
				$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND ((".$tid." = m.team2_id AND m.score2 < m.score1) OR (".$tid." = m.team1_id AND m.score1 < m.score2)) AND m.is_extra = 1 AND md.is_playoff = 0 AND m.m_played = 1 AND md.t_type = 0";
				$this->db->setQuery($query);
				$loose_ext = $this->db->loadResult();
			}
			
			
			$table_view[$i]['g_id'] = $season_par->s_groups ? $group_id : 0;
			$table_view[$i]['tid'] = $tid;
			$table_view[$i]['name'] = $teams_name;
			$table_view[$i]['played_chk'] = $wins + $drows + $loose +$wins_away+$drows_away+$loose_away + (($season_par->s_enbl_extra)?($wins_ext + $loose_ext):0);
			$table_view[$i]['win_chk'] = $wins +$wins_away;
			$table_view[$i]['draw_chk'] = $drows+$drows_away;
			$table_view[$i]['lost_chk'] = $loose+$loose_away;
			if($season_par->s_enbl_extra){
				$table_view[$i]['otwin_chk'] = $wins_ext;
				$table_view[$i]['otlost_chk'] = $loose_ext;
			}
			$table_view[$i]['diff_chk'] = ($home[0]->sc + $away[0]->sc).' - '.($home[0]->rc + $away[0]->rc);
			
			$table_view[$i]['gd_chk'] = ($home[0]->sc + $away[0]->sc) - ($home[0]->rc + $away[0]->rc);
			$table_view[$i]['point_chk'] = ($wins2 * $season_par->s_win_point + $wins_away2 * $season_par->s_win_away) + ($drows2 * $season_par->s_draw_point + $drows_away2 * $season_par->s_draw_away) + ($loose2 * $season_par->s_lost_point + $loose_away2 * $season_par->s_lost_away) + $homebonus + $awabonus + $bonus1 + $bonus2 +$teams[$i]->bonus_point + (($season_par->s_enbl_extra)?($wins_ext*$season_par->s_extra_win + $loose_ext*$season_par->s_extra_lost):0);
			
			$table_view[$i]['goalscore_chk'] = $home[0]->sc + $away[0]->sc;	
			
			$table_view[$i]['yteam'] = $teams_your?$teams_your_color:'';
			
			if($table_view[$i]['played_chk']){
				$table_view[$i]['percent_chk'] = ($wins + $wins_away + (( $season_par->s_enbl_extra && isset($wins_ext))?$wins_ext:0) + ($table_view[$i]['draw_chk']/2))/($table_view[$i]['played_chk']);
			}else{
				$table_view[$i]['percent_chk'] = 0;
			}
                }else{
                    
                    $query = "SELECT SUM(points) as rc "
                            ." FROM #__bl_matchday as md "
                            ." JOIN #__bl_rounds as r "
                            ." ON r.md_id = md.id AND md.s_id = ".$this->s_id
                            ." JOIN #__bl_rounds_participiants as p"
                            ." ON p.round_id = r.id"
                            ." WHERE r.round_status = '2' AND p.participiant_id = ".$tid;
			$this->db->setQuery($query);
			$pts = $this->db->loadResult();
                    
                    $table_view[$i]['g_id'] = 0;
                    $table_view[$i]['tid'] = $tid;
		    $table_view[$i]['name'] = $teams_name;
                    $table_view[$i]['yteam'] = $teams_your?$teams_your_color:'';
                    $table_view[$i]['point_chk'] = $pts +$teams[$i]->bonus_point;
                    
                    $this->_lists["soptions"]["point_chk"] = JText::_("BL_TBL_POINTS");
                    
			
                }
			if(!$this->t_single){ 
				$query = "SELECT * FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid=".$tid." WHERE ef.published=1 AND ef.type = '1' AND ef.e_table_view = '1' AND ef.fdisplay = '1' ".($user->get('guest')?" AND ef.faccess='0'":"")." AND ((ef.season_related = 0 AND ev.season_id=0) OR (ef.season_related = 1 AND ev.season_id='".$this->s_id."' AND ev.season_id != 0)) ORDER BY ef.ordering";
			}else{
				$query = "SELECT * FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid=".$tid." WHERE ef.published=1 AND ef.type = '0' AND ef.e_table_view = '1' AND ef.fdisplay = '1' ".($user->get('guest')?" AND ef.faccess='0'":"")." AND ((ef.season_related = 0 AND ev.season_id=0) OR (ef.season_related = 1 AND ev.season_id='".$this->s_id."' AND ev.season_id != 0)) ORDER BY ef.ordering";
			}
	// print_r($query); echo "<hr>";
			$this->db->setQuery($query);
			$lists['ext_fields'] = $this->db->loadObjectList();
	//print_r($lists['ext_fields']); echo "<hr>";
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
			$mj=0;
			if(isset($lists['ext_fields'])){
				for($j=0;$j<count($ext_fields_name);$j++){
					foreach ($lists['ext_fields'] as $extr){

						if($ext_fields_name[$j]->id == $extr->id){
							if($extr->field_type == '3'){
								$query = "SELECT sel_value FROM #__bl_extra_select WHERE id='".$extr->fvalue."'";
								$this->db->setQuery($query);
								$selvals = $this->db->loadResult();

								if(isset($selvals) && $selvals){
									$ext_pl[$mj] = $selvals;
								}else{
									$ext_pl[$mj] = '&nbsp;';
								}
							}else
							if($extr->field_type == '1'){
								$ext_pl[$mj]	= $extr->fvalue?JText::_("Yes"):JText::_("No");
							}else if($extr->field_type == '2'){
								$ext_pl[$mj] = $extr->fvalue_text?$extr->fvalue_text:'&nbsp;';
							
							
							}else{
								$ext_pl[$mj] = $extr->fvalue?$extr->fvalue:"&nbsp;";
							}
                            if($extr->field_type == '4' && $extr->fvalue){
                                $ext_pl[$mj]	= "<a target='_blank' href='".(substr($extr->fvalue,0,7)=='http://'?$extr->fvalue:"http://".$extr->fvalue)."'>".$extr->fvalue."</a>";
                            }
							if($ext_pl[$mj] == NULL){
								$ext_pl[$mj] = '&nbsp;';
							}
						
							$table_view[$i]['ext_fields'][$j] = $ext_pl[$mj];
						}	
						$mj++;
					}
				}
			}
			$table_view[$i]['t_emblem'] = $emblems;
                        
		if($this->_lists['tournament_type'] != 1){
			$table_view[$i]['avulka_v'] = '';
			$table_view[$i]['avulka_cf'] = '';
			$table_view[$i]['avulka_cs'] = '';
			$table_view[$i]['avulka_qc'] = '';
			
			
			///2.0.7
			$table_view[$i]['goalscore_chk'] = $home[0]->sc + $away[0]->sc;
			$table_view[$i]['goalconc_chk'] = $home[0]->rc + $away[0]->rc;
			$table_view[$i]['winhome_chk'] = $wins;
			$table_view[$i]['drawhome_chk'] = $drows;
			$table_view[$i]['losthome_chk'] = $loose;
			$table_view[$i]['winaway_chk'] = $wins_away;
			$table_view[$i]['drawaway_chk'] = $drows_away;
			$table_view[$i]['lostaway_chk'] = $loose_away;
			$table_view[$i]['pointshome_chk'] = ($wins2) * $season_par->s_win_point + ($drows2) * $season_par->s_draw_point + ($loose2) * $season_par->s_lost_point + $homebonus +$bonus1;
			$table_view[$i]['pointsaway_chk'] = ($wins_away2) * $season_par->s_win_away + ($drows_away2) * $season_par->s_draw_away + ($loose_away2) * $season_par->s_lost_away + $awabonus + $bonus2;
			///in groups
                        if($season_par->s_groups){
                                $table_view[$i]['grwin_chk'] = $wins_gr;
                                $table_view[$i]['grlost_chk'] = $loose_gr;
                        }
                }    
//updt  
		//$this->_lists['value_extra'] .= $table_view[$i]['ext_fields'];
		}
			
			//---playeachother---///
		$query = "SELECT opt_value FROM #__bl_season_option WHERE s_id = ".$this->s_id." AND opt_name='equalpts_chk'";
		$this->db->setQuery($query);
		$equalpts_chk = $this->db->loadResult();

		
		if($equalpts_chk){
			$pts_arr = array();
			$pts_equal = array();
			foreach($table_view as $tv){
				if(!in_array($tv['point_chk'],$pts_arr)){
					$pts_arr[] = $tv['point_chk'];
				}else{
					if(!in_array($tv['point_chk'],$pts_equal)){
						$pts_equal[] = $tv['point_chk'];
					}
				}
			}
			$k = 0;
			$team_arr = array();
			foreach ($pts_equal as $pts){
				foreach($table_view as $tv){
					if($tv['point_chk'] == $pts){
						$team_arr[$k][] = $tv['tid'];
						
					}
				}
				$k++;
			}
			
			foreach ($team_arr as $tm){
				
				foreach ($tm as $tm_one){
					
					$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2 WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_played=1 AND md.t_type = 0 AND ((t1.id = ".$tm_one." AND m.score1>m.score2 AND t2.id IN (".implode(',',$tm).")) OR (t2.id=".$tm_one." AND m.score1<m.score2 AND t1.id IN (".implode(',',$tm).")))";
		
					$this->db->setQuery($query);
					
					$matchs_avulsa_win = $this->db->loadResult();
			$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2 WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id." AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_played=1 AND md.t_type = 0 AND ((t1.id = ".$tm_one." AND m.score1=m.score2 AND t2.id IN (".implode(',',$tm).")) OR (t2.id=".$tm_one." AND m.score1=m.score2 AND t1.id IN (".implode(',',$tm).")))";
			$this->db->setQuery($query);
			$matchs_avulsa_win_c = 3*$matchs_avulsa_win + $this->db->loadResult();			
					$tm_equal_win = array();
					
					foreach ($tm as $tm_other){
						$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2 WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$this->s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_played=1 AND md.t_type = 0 AND ((t1.id = ".$tm_other." AND m.score1>m.score2 AND t2.id IN (".implode(',',$tm).")) OR (t2.id=".$tm_other." AND m.score1<m.score2 AND t1.id IN (".implode(',',$tm).")))";
			
						$this->db->setQuery($query);
						
						$matchs_avulsa_win_other = $this->db->loadResult();
						
						if($matchs_avulsa_win_other == $matchs_avulsa_win){
							$tm_equal_win[] = $tm_other;
						}
					}
					
					$query = "SELECT SUM(score1) as sh,SUM(score2) as sw FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2 WHERE m.m_id = md.id AND m.published = 1 AND m.m_played=1 AND md.s_id=".$this->s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND md.t_type = 0 AND ((t1.id = ".$tm_one." AND t2.id IN (".implode(',',$tm_equal_win).")))";
		
					$this->db->setQuery($query);
				
					$matchs_avulsa_score = $this->db->loadRow();
					//var_dump($matchs_avulsa_score);
					
					$query = "SELECT SUM(score2) as sh,SUM(score1) as sw FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2 WHERE m.m_id = md.id AND m.published = 1 AND m.m_played=1 AND md.s_id=".$this->s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND md.t_type = 0 AND ((t2.id=".$tm_one." AND t1.id IN (".implode(',',$tm_equal_win).")))";
		
					$this->db->setQuery($query);
				
					$matchs_avulsa_rec = $this->db->loadRow();
					
					 $matchs_avulsa_res = intval($matchs_avulsa_score[0]) + intval($matchs_avulsa_rec[0]);
					 $matchs_avulsa_res2 = intval($matchs_avulsa_score[1]) + intval($matchs_avulsa_rec[1]);
					

					for ($b=0;$b<count($table_view);$b++){
						if($table_view[$b]['tid']==$tm_one){
							$table_view[$b]['avulka_v'] = $matchs_avulsa_win_c;
							$table_view[$b]['avulka_cf'] = $matchs_avulsa_res;
							$table_view[$b]['avulka_cs'] = $matchs_avulsa_res2;
							$table_view[$b]['avulka_qc'] = $matchs_avulsa_res-$matchs_avulsa_res2;
						}
					}
				}	
			}
		}	
		//--/playeachother---///
			
			$sort_arr = array();
		 foreach($table_view AS $uniqid => $row){
	        foreach($row AS $key=>$value){
	            $sort_arr[$key][$uniqid] = $value;
	        }
	    }
	   if(count($groups_exists)){
			$query = "SELECT id FROM #__bl_groups WHERE id IN (".implode(',',$groups_exists).") ORDER BY ordering";
			$this->db->setQuery($query);
			$groups_exists = $this->db->loadColumn();
			
			//sort($groups_exists, SORT_NUMERIC);
	   }
	  	if(!$season_par->s_groups){
	  		$groups_exists = array(0);
	  	}
		$this->_lists["groups"] = $groups_exists;
		if(count($sort_arr)){
			// sort fields 1-points, 2-wins percent, /*3-if equal between teams*/, 4-goal difference, 5-goal score
			$query = "SELECT * FROM #__bl_ranksort WHERE seasonid=".$this->s_id." ORDER BY ordering";
			$this->db->setQuery($query);
			$savedsort = $this->db->loadObjectList();
			$argsort = array();
			$argsort_way = array();
			if(count($savedsort)){
				foreach($savedsort as $sortop){
					switch($sortop->sort_field){
						case '1': $argsort[][0] = $sort_arr['point_chk'];		break;
						case '2': $argsort[][0] = $sort_arr['percent_chk'];		break;
						case '3': $argsort[][0] = $sort_arr['point_chk'];		break; /* not used */
						case '4': $argsort[][0] = $sort_arr['gd_chk'];			break;
						case '5': $argsort[][0] = $sort_arr['goalscore_chk'];	break;
						case '6': $argsort[][0] = $sort_arr['played_chk'];		break;
						case '7': $argsort[][0] = $sort_arr['win_chk'];		break;
					}
					
					$argsort_way[] = $sortop->sort_way;
				}
				
			}
			//var_dump($argsort);
			if($equalpts_chk){
				//var_dump($sort_arr['avulka_v']);
				array_multisort($sort_arr['g_id'], SORT_ASC,$sort_arr['point_chk'], SORT_DESC,$sort_arr['avulka_v'], SORT_DESC,$sort_arr['avulka_qc'],SORT_DESC,$sort_arr['avulka_cf'],SORT_DESC,$sort_arr['gd_chk'], SORT_DESC,$sort_arr['goalscore_chk'], SORT_DESC, $table_view);
			
			}else{
			
				if($this->_lists['tournament_type'] == 1){
                                    array_multisort($sort_arr['g_id'], SORT_ASC, $sort_arr['point_chk'], SORT_DESC, $table_view);
                                }else{
                                    array_multisort($sort_arr['g_id'], SORT_ASC,(isset($argsort[0][0])?$argsort[0][0]:$sort_arr['point_chk']), (isset($argsort_way[0])?($argsort_way[0]?SORT_ASC:SORT_DESC):SORT_DESC),(isset($argsort[1][0])?$argsort[1][0]:$sort_arr['gd_chk']), (isset($argsort_way[1])?($argsort_way[1]?SORT_ASC:SORT_DESC):SORT_DESC),(isset($argsort[2][0])?$argsort[2][0]:$sort_arr['goalscore_chk']), (isset($argsort_way[2])?($argsort_way[2]?SORT_ASC:SORT_DESC):SORT_DESC),(isset($argsort[3][0])?$argsort[3][0]:$sort_arr['win_chk']), (isset($argsort_way[3])?($argsort_way[3]?SORT_ASC:SORT_DESC):SORT_DESC), $table_view);
                                }
                                    
                        }
		}
                
		$this->_lists["v_table"] = $table_view;
		
		/////playoffs
		$pln = getJS_Config('player_name');
		if($this->t_single){
			$query = "SELECT m.*,m.id as mid,".($pln?"IF(t.nick<>'',t.nick,CONCAT(t.first_name,' ',t.last_name))":"CONCAT(t.first_name,' ',t.last_name)")." AS home,"
					." ".($pln?"IF(t2.nick<>'',t2.nick,CONCAT(t2.first_name,' ',t2.last_name))":"CONCAT(t2.first_name,' ',t2.last_name)")." AS away, md.m_name, m.is_extra "
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t, #__bl_players as t2"
					." WHERE  m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND md.is_playoff = 1 AND t.id = m.team1_id AND t2.id = m.team2_id AND md.t_type = 0"
					." ORDER BY md.id,m.id";
		}else{
			$query = "SELECT m.*,m.id as mid,t.t_name as home, t2.t_name as away, md.m_name, m.is_extra, t.t_emblem as emb1,t2.t_emblem as emb2  FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t, #__bl_teams as t2  WHERE  m.m_id = md.id AND md.s_id = ".$this->s_id." AND m.published = 1 AND md.is_playoff = 1 AND t.id = m.team1_id AND t2.id = m.team2_id AND md.t_type = 0  ORDER BY md.id,m.id";
		}
		$this->db->setQuery($query);
		$this->_lists["playoffs"] = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		//var_dump($groups_exists);die;
		$query = "SELECT group_name FROM #__bl_groups WHERE id IN(".(count($groups_exists)?implode(',',$groups_exists):"''").") ORDER BY ordering";
		$this->db->setQuery($query);
		$this->_lists["groups_name"] = $this->db->loadColumn();
		
		$this->_lists["enbl_gr"] = $season_par->s_groups;
		$this->_lists["enbl_extra"] = $season_par->s_enbl_extra;
		/*if(!$this->t_single){
			$query = "SELECT ef.name FROM #__bl_extra_filds as ef  WHERE ef.published=1 AND ef.type = '1' AND ef.e_table_view = '1' AND ef.fdisplay = '1' ORDER BY ef.ordering";
		}else{
			$query = "SELECT ef.name FROM #__bl_extra_filds as ef  WHERE ef.published=1 AND ef.type = '0' AND ef.e_table_view = '1' AND ef.fdisplay = '1' ORDER BY ef.ordering";
		}
		$this->db->setQuery($query);
		
		$this->_lists["ext_fields_name"] = $this->db->loadColumn();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}*/
		
		$this->_lists['bonus_not'] = (isset($bonus_not)?$bonus_not:'');
		//----colors----//
		$query = "SELECT * FROM #__bl_tblcolors WHERE s_id=".$this->s_id." ORDER BY place";
		$this->db->setQuery($query);
		$colors = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		$color_mass = array();
		for($j=0;$j<count($colors);$j++){
			
			$tmp_pl = $colors[$j]->place;
			$color_mass[intval($colors[$j]->place)] = $colors[$j]->color;
			$tmp_arr = explode(',',$tmp_pl);
			$tmp_arr2 = explode('-',$tmp_pl);
			if(count($tmp_arr)>1){
				foreach ($tmp_arr as $arr){
					if(intval($arr)){
						$color_mass[intval($arr)] = $colors[$j]->color;
					}
				}
			}
			if(count($tmp_arr2)>1){
				for($zzz=$tmp_arr2[0];$zzz<$tmp_arr2[1]+1;$zzz++){
					$color_mass[$zzz] = $colors[$j]->color;
				}
			}
		}
		$this->_lists["colors"] = $color_mass;
		
	}
	function KnockTable(){
		$Itemid = JRequest::getInt('Itemid');
		$matchs = $this->_lists['knmatches'];
		$k_format = 0;	
		if(count($matchs))	{
			$k_format = $matchs[0]->k_format;	
		}
		$pln = $this->getJS_Config('player_name');
        $kl = '';
		if(count($matchs) && $k_format){
			$match = $matchs;
			$orderby = "md.id,m.k_stage,m.k_ordering";
			if($this->t_single){
				$query = "SELECT MAX(LENGTH(CONCAT(t.first_name,' ',t.last_name))) FROM #__bl_season_players as st, #__bl_players as t WHERE t.id = st.player_id AND st.season_id = ".$this->s_id;
			}else{
				$query = "SELECT MAX(LENGTH(t.t_name)) FROM #__bl_season_teams as st, #__bl_teams as t WHERE t.id = st.team_id AND st.season_id = ".$this->s_id;
			}
			$this->db->setQuery($query);
			$mxl = $this->db->loadResult();
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
////////////////////////////////////////
            $query = "SELECT * FROM #__bl_matchday WHERE s_id=".$this->s_id." AND (t_type = 1 OR t_type = 2) ORDER BY id";
            $this->db->setQuery($query);
            $mdays = $this->db->loadObjectList();
            //$kl = '<br />';

            if(count($mdays)){
                foreach ($mdays as $mday){
                    $k_format = $mday->k_format;
                    $t_type = $mday->t_type;


                    if($this->t_single){
                        $query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid,t1.first_name,t1.last_name,t1.nick,t2.first_name as fn2,t2.last_name as ln2,t2.nick as nick2,"
                            ." CONCAT(t1.first_name,' ',t1.last_name) as home, CONCAT(t2.first_name,' ',t2.last_name) as away,t1.id as hm_id,t2.id as aw_id,"
                            ." IF(m.score1>m.score2,CONCAT(t1.first_name,' ',t1.last_name), CONCAT(t2.first_name,' ',t2.last_name)) as winner,"
                            ." IF(m.score1>m.score2,t1.nick, t2.nick) as winner_nick,"
                            ." IF(m.score1>m.score2,t1.id,t2.id) as winnerid,"
                            ." IF(m.score1<m.score2,CONCAT(t1.first_name,' ',t1.last_name), CONCAT(t2.first_name,' ',t2.last_name)) as looser,"
                            ." IF(m.score1<m.score2,t1.nick, t2.nick) as looser_nick,"
                            ." IF(m.score1<m.score2,t1.id,t2.id) as looserid"
                            ." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_players as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_players as t2 ON m.team2_id = t2.id"
                            ." WHERE m.m_id = md.id AND m.published = 1 AND (md.t_type = 1 OR md.t_type = 2) AND md.s_id=".$this->s_id." AND m.k_type = '0' AND md.id=".$mday->id
                            ." ORDER BY ".$orderby;

                    }else{
                        $query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away, t1.id as hm_id,t2.id as aw_id, IF(m.score1>m.score2,t1.t_name,t2.t_name) as winner,IF(m.score1>m.score2,t1.id,t2.id) as winnerid, IF(m.score1<m.score2,t1.t_name,t2.t_name) as looser,IF(m.score1<m.score2,t1.id,t2.id) as looserid"
                            ." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN  #__bl_teams as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_teams as t2 ON m.team2_id = t2.id"
                            ." WHERE m.m_id = md.id AND m.published = 1 AND (md.t_type = 1 OR md.t_type = 2) AND md.s_id=".$this->s_id." AND m.k_type = '0' AND md.id=".$mday->id
                            ." ORDER BY ".$orderby;

                    }
                    $this->db->setQuery($query);

                    $match = $this->db->loadObjectList();
                    $error = $this->db->getErrorMsg();
                    if ($error)
                    {
                        return JError::raiseError(500, $error);
                    }

                    if($t_type == 2){
                        if($this->t_single){
                            $query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid,t1.first_name,t1.last_name,t1.nick,t2.first_name as fn2,t2.last_name as ln2,t2.nick as nick2,"
                                ." CONCAT(t1.first_name,' ',t1.last_name) as home, CONCAT(t2.first_name,' ',t2.last_name) as away,t1.id as hm_id,t2.id as aw_id,"
                                ." IF(m.score1>m.score2,CONCAT(t1.first_name,' ',t1.last_name), CONCAT(t2.first_name,' ',t2.last_name)) as winner,"
                                ." IF(m.score1>m.score2,t1.nick, t2.nick) as winner_nick,"
                                ." IF(m.score1>m.score2,t1.id,t2.id) as winnerid,"
                                ." IF(m.score1<m.score2,CONCAT(t1.first_name,' ',t1.last_name), CONCAT(t2.first_name,' ',t2.last_name)) as looser,"
                                ." IF(m.score1<m.score2,t1.nick, t2.nick) as looser_nick,"
                                ." IF(m.score1<m.score2,t1.id,t2.id) as looserid"
                                ." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_players as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_players as t2 ON m.team2_id = t2.id"
                                ." WHERE m.m_id = md.id AND m.published = 1 AND (md.t_type = 1 OR md.t_type = 2) AND md.s_id=".$this->s_id."  AND m.k_type = '1' AND md.id=".$mday->id
                                ." ORDER BY ".$orderby;

                        }else{
                            $query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away, t1.id as hm_id,t2.id as aw_id, IF(m.score1>m.score2,t1.t_name,t2.t_name) as winner,IF(m.score1>m.score2,t1.id,t2.id) as winnerid, IF(m.score1<m.score2,t1.t_name,t2.t_name) as looser,IF(m.score1<m.score2,t1.id,t2.id) as looserid"
                                ." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN  #__bl_teams as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_teams as t2 ON m.team2_id = t2.id"
                                ." WHERE m.m_id = md.id AND m.published = 1 AND (md.t_type = 1 OR md.t_type = 2) AND md.s_id=".$this->s_id." AND m.k_type = '1' AND md.id=".$mday->id
                                ." ORDER BY ".$orderby;

                        }
                        $this->db->setQuery($query);

                        $matchDE = $this->db->loadObjectList();
                        $error = $this->db->getErrorMsg();
                        if ($error)
                        {
                            return JError::raiseError(500, $error);
                        }
                }

               // print_r($matchDE);echo "<hr>";


                    if($this->getJS_Config('knock_style')){
                        $kl = $this->KnockTableVert($mxl,$match,$k_format, $Itemid, $orderby);

                    }else{
                        //$kl = $this->KnockTableHor($mxl,$match,$k_format,$Itemid, $orderby);
                        if($t_type == 1){
                            //$kl = $this->HorKnView($mxl,$match,$k_format,$Itemid); ////
                            $kl = js_mobile::isMobile()? $this->knock_type->HorKnView_mobile($mxl,$match,$k_format,$Itemid,$this->t_single,$this->s_id):$this->knock_type->HorKnView($mxl,$match,$k_format,$Itemid,$this->t_single,$this->s_id);
                        }else if($t_type == 2){
                            //$kl = $this->HorKnViewDE($mxl,$match,$matchDE,$k_format,$k_format1,$Itemid);
                            $kl .= js_mobile::isMobile()?$this->knock_type->HorKnViewDE_mobile($mxl,$match,$matchDE,$k_format,$Itemid,$this->t_single,$this->s_id): $this->knock_type->HorKnViewDE($mxl,$match,$matchDE,$k_format,$Itemid,$this->t_single,$this->s_id);
                           // echo "2";
                        }
                        //$kl = $this->KnockTableHor($mxl,$match,$k_format,$Itemid, $orderby);
                    }
                }
            }

		}else{	
				if($this->t_single){
					$query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($this->s_id)." ORDER BY t.first_name";
				}else{
					$query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".($this->s_id)." ORDER BY t.t_name";
				}
				$this->db->setQuery($query);
			
				$team = $this->db->loadObjectList();
				$error = $this->db->getErrorMsg();
				if ($error)
				{
					return JError::raiseError(500, $error);
				}
				//$kl = '';
				for($y=0;$y<count($team);$y++){
					//$kl = "<div>".$team[$y]->t_name."</div>"; ////??
				}

		}
	
		
		$this->_lists['knock_layout'] = $kl;
	
	}
	
	function KnockTableVert($mxl,$match,$k_format, $Itemid, $orderby){
	
		if($mxl){
			$reslng = ($mxl)*7+20;
		}else{
			$reslng = 120;
		}
		if($reslng<120) $reslng=120;
		$cfg = new stdClass();
		$cfg->wdth = $reslng+50;
		$cfg->height = 20;
		$cfg->step = 70; 
		$cfg->top_next = 50;
		
			
			
			$query = "SELECT * FROM #__bl_matchday WHERE s_id=".$this->s_id." AND (t_type = 1 OR t_type = 2) ORDER BY id";
			$this->db->setQuery($query);
			$mdays = $this->db->loadObjectList();
			$kl = '<br />';
			if(count($mdays)){
				foreach ($mdays as $mday){
				$k_format = $mday->k_format;
			
					if($this->t_single){
						$query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid,t1.first_name,t1.last_name,t1.nick,t2.first_name as fn2,t2.last_name as ln2,t2.nick as nick2,"
								." CONCAT(t1.first_name,' ',t1.last_name) as home, CONCAT(t2.first_name,' ',t2.last_name) as away,t1.id as hm_id,t2.id as aw_id,"
								." IF(m.score1>m.score2,CONCAT(t1.first_name,' ',t1.last_name), CONCAT(t2.first_name,' ',t2.last_name)) as winner,"
								." IF(m.score1>m.score2,t1.nick, t2.nick) as winner_nick,"
								." IF(m.score1>m.score2,t1.id,t2.id) as winnerid"
								." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_players as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_players as t2 ON m.team2_id = t2.id"
								." WHERE m.m_id = md.id AND m.published = 1 AND (md.t_type = 1 OR md.t_type = 2) AND md.s_id=".$this->s_id." AND md.id=".$mday->id
								." ORDER BY ".$orderby;
					
					}else{
						$query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away, t1.id as hm_id,t2.id as aw_id, IF(m.score1>m.score2,t1.t_name,t2.t_name) as winner,IF(m.score1>m.score2,t1.id,t2.id) as winnerid"
								." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN  #__bl_teams as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_teams as t2 ON m.team2_id = t2.id"
								." WHERE m.m_id = md.id AND m.published = 1 AND (md.t_type = 1 OR md.t_type = 2) AND md.s_id=".$this->s_id." AND md.id=".$mday->id
								." ORDER BY ".$orderby;
					
					}
					$this->db->setQuery($query);
		    
					$match = $this->db->loadObjectList();
					$error = $this->db->getErrorMsg();
					if ($error)
					{
						return JError::raiseError(500, $error);
					}
					$zz = 2;
					$p=0;
					
					$wdth = $cfg->wdth;
					$height = $cfg->height;
					$step = $cfg->step; 
					$top_next = $cfg->top_next;
					
					
					
					if($this->t_single){
						$query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($this->s_id)." ORDER BY t.first_name";
					}else{
						$query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".($this->s_id)." ORDER BY t.t_name";
					}
					$this->db->setQuery($query);
				
					$team = $this->db->loadObjectList();
				
					$is_team[] = JHTML::_('select.option',  0, ($this->t_single?JText::_('BLFA_SELPLAYER'):JText::_('BLFA_SELTEAM')), 'id', 't_name' ); 
				
					$teamis = array_merge($is_team,$team);
				
					$fid = $k_format;
			$kl .= '<div class="combine-box-vert" style="height:'.(($fid/2)*($height+$step)+60).'px;position:relative;overflow-x:auto;overflow-y:auto;border:1px solid #777;">';
			
			$bz = 0;
			$vz = 1;
			$arr_prev_pl = array();
			$vetks_null = array();
			
			while(floor($fid/$zz) >= 1){
				
				for($i=0;$i<floor($fid/$zz);$i++){
					
					//$kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
					if($this->t_single){
							$match[$i]->home = $this->selectPlayerName($match[$i]);
							$match[$i]->away = $this->selectPlayerName($match[$i],"fn2","ln2","nick2");
						}	
					if($p==0){
						if(isset($match[$i]->hm_id)){
							if($this->t_single){
								$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$i]->hm_id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
							}else{	
								$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$i]->hm_id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
							}
						}	
						$kl .= '<div class="knock_el_vert" style="position:absolute; top:'.($top_next - 14).'px; left:'.(20*($i+1) + ($i)*$wdth + $bz).'px;width:'.($reslng+50).'px;height:50px;border:1px solid #000;"><div>';
						$kl .= isset($match[$i]->home)?("<a href='".$link."' title='".$match[$i]->home."'>".$match[$i]->home."</a>"):"&nbsp;";
						$kl .= '</div><div>'.((isset($match[$i]->score1) && $match[$i]->m_played)?$match[$i]->score1:'').'</div>';
						$kl .= '</div>';
						if(isset($match[$i]->aw_id)){
							if($this->t_single){
								$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$i]->aw_id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
							}else{	
								$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$i]->aw_id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
							}
						}	
						$kl .= '<div class="knock_el_vert" style="position:absolute; top:'.($top_next - 14).'px; left:'.(20*($i+2) + ($i+1)*$wdth + $bz).'px;width:'.($reslng+50).'px;height:50px;border:1px solid #000;"><div>';
						$kl .= isset($match[$i]->away)?("<a href='".$link."' title='".$match[$i]->away."'>".$match[$i]->away."</a>"):"&nbsp;";
						$kl .= '</div><div>'.((isset($match[$i]->score2) && $match[$i]->m_played)?$match[$i]->score2:'').'</div><div class="knlink" style="width:'.$reslng.'px;"></div>';
						
						$kl .= '</div>';
						$match_link = 'index.php?option=com_joomsport&amp;task=view_match&amp;id='.(isset($match[$i]->id)?($match[$i]->id):'').'&amp;Itemid='.$Itemid;
						$kl .= (isset($match[$i]->id)?'<div class="field-vert" style="position:absolute; top:'.($top_next + 40).'px; left:'.(20*($i+2) + ($i+1)*$wdth + $bz - 20).'px;"><a href="'.$match_link.'" title="'.JText::_('BL_LINK_DETAILMATCH').'"><span class="module-menu-editor"><!-- --></span></a></div>':"");
						$bz += $wdth +20;
						
						if($match[$i]->m_played == 0 && $match[$i]->team1_id && $match[$i]->team2_id){
							$arr_prev_pl[$p][] = $i;
						}
						if(!$match[$i]->team1_id || !$match[$i]->team2_id){
							$vetks_null[$p][] = $i;
						}
						
					}else{
						
						$firstchld_ind = $i*2 + ($fid/2)*((pow(2,$p-1)-1)/pow(2,$p-2));
						//$match[$firstchld_ind]->winner = ($pln && $match[$firstchld_ind]->winner_nick)?($match[$firstchld_ind]->winner_nick):($match[$firstchld_ind]->winner);
						//$match[$firstchld_ind+1]->winner = ($pln && $match[$firstchld_ind+1]->winner_nick)?$match[$firstchld_ind+1]->winner_nick:$match[$firstchld_ind+1]->winner;
						$cur_ind = $i + ($fid/2)*((pow(2,$p)-1)/pow(2,$p-1));
						if($this->t_single){
							$match[$firstchld_ind]->home = $this->selectPlayerName($match[$firstchld_ind]);
							$match[$firstchld_ind]->winner = $this->selectPlayerName($match[$firstchld_ind],"winner","","winner_nick");
							$match[$firstchld_ind]->away = $this->selectPlayerName($match[$firstchld_ind],"fn2","ln2","nick2");
							$match[$firstchld_ind+1]->home = $this->selectPlayerName($match[$firstchld_ind+1]);
							$match[$firstchld_ind+1]->away = $this->selectPlayerName($match[$firstchld_ind+1],"fn2","ln2","nick2");
							$match[$firstchld_ind+1]->winner = $this->selectPlayerName($match[$firstchld_ind+1],"winner","","winner_nick");
						}
						
						if(($match[$firstchld_ind]->score1 == $match[$firstchld_ind]->score2) && isset($match[$firstchld_ind]->winner)){
							
							if($match[$firstchld_ind]->aet1 > $match[$firstchld_ind]->aet2){
								$match[$firstchld_ind]->winner = $match[$firstchld_ind]->home;
								$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
								
							}elseif($match[$firstchld_ind]->aet1 < $match[$firstchld_ind]->aet2){
								$match[$firstchld_ind]->winner = $match[$firstchld_ind]->away;
								$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
							}else{
								if($match[$firstchld_ind]->p_winner && $match[$firstchld_ind]->p_winner == $match[$firstchld_ind]->team1_id){
									$match[$firstchld_ind]->winner = $match[$firstchld_ind]->home;
									$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
							
								}elseif($match[$firstchld_ind]->p_winner && $match[$firstchld_ind]->p_winner == $match[$firstchld_ind]->team2_id){
									$match[$firstchld_ind]->winner = $match[$firstchld_ind]->away;
									$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
								
								}else{
									$match[$firstchld_ind]->m_played = 0;
								}
							}
						}
						if(($match[$firstchld_ind+1]->score1 == $match[$firstchld_ind+1]->score2) && isset($match[$firstchld_ind+1]->winner)){
							if($match[$firstchld_ind+1]->aet1 > $match[$firstchld_ind+1]->aet2){
								$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home;
								$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
							}elseif($match[$firstchld_ind+1]->aet1 < $match[$firstchld_ind+1]->aet2){
								$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away;
								$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
							}else{
								if($match[$firstchld_ind+1]->p_winner && $match[$firstchld_ind+1]->p_winner == $match[$firstchld_ind+1]->team1_id){
									$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home;
									$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
								}elseif( $match[$firstchld_ind+1]->p_winner && $match[$firstchld_ind+1]->p_winner == $match[$firstchld_ind+1]->team2_id){
									$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away;
									$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
								}else{
									$match[$firstchld_ind+1]->m_played = 0;
								}
							}
						}
						
						if(!$match[$firstchld_ind]->home && $match[$firstchld_ind]->away){
							$match[$firstchld_ind]->winner = $match[$firstchld_ind]->away;
							$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
							$match[$firstchld_ind]->m_played = 1;
						}
						if(!$match[$firstchld_ind]->away && $match[$firstchld_ind]->home){
							$match[$firstchld_ind]->winner = $match[$firstchld_ind]->home;
							$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
							$match[$firstchld_ind]->m_played = 1;
						}
					
						if(!$match[$firstchld_ind+1]->home && $match[$firstchld_ind+1]->away){
							$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away;
							$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
							$match[$firstchld_ind+1]->m_played = 1;
						}
						if(!$match[$firstchld_ind+1]->away && $match[$firstchld_ind+1]->home){
							$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home;
							$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
							$match[$firstchld_ind+1]->m_played = 1;
						}
						
						$has_winner1 = 1;
						$has_winner2 = 1;
						if(isset($match[$firstchld_ind]) && isset($match[$firstchld_ind+1])){
							if(!$match[$firstchld_ind]->winner || !$match[$firstchld_ind+1]->winner){
								if(in_array($i*2,$vetks_null[$p-1]) || in_array($i*2+1,$vetks_null[$p-1])){
									$vetks_null[$p][] = $i;
								}								
							}
						}
						
						if(isset($arr_prev_pl[$p-1]) && count($arr_prev_pl[$p-1])){
							if(in_array($i*2,$arr_prev_pl[$p-1])){
								$has_winner1 = 0;
							}						
							if(in_array($i*2+1,$arr_prev_pl[$p-1])){
								$has_winner2 = 0;
							}
						}
						
						if(isset($match[$cur_ind]->m_played) && $match[$cur_ind]->m_played == 0){
								if(isset($vetks_null[$p-1])){
									if(in_array($i*2,$vetks_null[$p-1]) OR in_array($i*2+1,$vetks_null[$p-1])){
										
									}else{
										$arr_prev_pl[$p][] = $i;
									}
								}
								if(in_array($i*2,$arr_prev_pl[$p-1]) OR in_array($i*2+1,$arr_prev_pl[$p-1])){
									$arr_prev_pl[$p][] = $i;
								}
							}
						
						$kl .= '<div class="knock_el_vert" style="position:absolute; top:'.($top_next).'px; left:'.(((2*$wdth+20)*(2*$vz -1)*pow(2,$p-1) + (pow(2,$p-1)-1)*20)/2 -$wdth/2 + 20*$vz*$p - 20).'px;width:'.($reslng+50).'px;height:50px;border:1px solid #000;"><div>';
						
						if(isset($match[$firstchld_ind]->winnerid)){
							if($this->t_single){
								$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$firstchld_ind]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
							}else{	
								$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$firstchld_ind]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
							}
						}	
						$kl .= (isset($match[$firstchld_ind]->winner) && $match[$firstchld_ind]->m_played && $has_winner1)?("<a href='".$link."' title='".$match[$firstchld_ind]->winner."'>".$match[$firstchld_ind]->winner."</a>"):"";
						$kl .= '</div><div>'.((isset($match[$cur_ind]->score1) && $match[$cur_ind]->m_played && $has_winner2)?$match[$cur_ind]->score1:"").'</div>';
						$kl .= '</div>';
						$kl .= '<div class="knock_el_vert" style="position:absolute; top:'.($top_next).'px; left:'.(((2*$wdth+20)*(2*$vz + 1)*pow(2,$p-1) + (pow(2,$p-1)-1)*20)/2 -$wdth/2 + 20*$vz*$p + 20).'px;width:'.($reslng+50).'px;height:50px;border:1px solid #000;"><div>';
						if(isset($match[$firstchld_ind+1]->winnerid)){
							if($this->t_single){
								$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$firstchld_ind+1]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
							}else{	
								$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$firstchld_ind+1]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
							}
						}
						$kl .= (isset($match[$firstchld_ind + 1]->winner) && $match[$firstchld_ind + 1]->m_played)?("<a href='".$link."' title='".$match[$firstchld_ind+1]->winner."'>".$match[$firstchld_ind+1]->winner."</a>"):"";
						$kl .= '</div><div>'.((isset($match[$cur_ind]->score2) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score2:"").'</div>';
						
						$kl .= '</div>';
						$match_link = 'index.php?option=com_joomsport&amp;task=view_match&amp;id='.(isset($match[$cur_ind]->id)?($match[$cur_ind]->id):'');
						$kl .= (isset($match[$cur_ind]->id)?'<div style="position:absolute; top:'.($top_next+20).'px; left:'.((((2*$wdth+20)*(2*$vz)*pow(2,$p-1) + (pow(2,$p-1)-1)*20) - $wdth + 40*$vz*$p)/2 + $wdth/2).'px;"><a href="'.$match_link.'" title="'.JText::_('BL_LINK_DETAILMATCH').'"><span class="module-menu-editor"><!-- --></span></a></div>':"");

					}
					$vz+=2;
				}
				
				$top_next += $height + $step;
				//$height = $height + $step;
				//$step = $height;
				$zz *= 2;
				$p++;
				
				$vz = 1;
				
			}
			$winmd_id = $fid - 2;
			$wiinn = '';
			if(isset($match[$winmd_id]->winner) && $match[$winmd_id]->winner && $match[$winmd_id]->score1 != $match[$winmd_id]->score2 && $match[$winmd_id]->m_played) 
			{ 
				if($this->t_single){
					$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$winmd_id]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
				}else{	
					$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$winmd_id]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
				}
				$wiinn = "<div class='knock_el' style='width:".($reslng+50)."px;margin-left:5px;margin-top:-17px;'><div><div><div class='knres'></div><div class='knlink' style='width:".$reslng."px;'><div><div><a href='".$link."' title='".$match[$winmd_id]->winner."'>".$match[$winmd_id]->winner."</a></div></div></div></div></div></div>";
			}
			
			if($fid){
				$kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.( $top_next).'px; left:'.((((2*$wdth+20)*(2 + 1)*pow(2,$p-2) + (pow(2,$p-2)-1)*20)/2 -$wdth/2 + 20*($p-1) + 20)*2/3).'px;">'.$wiinn.'</div>';
			}	
				$kl .=  '</div>';
				}
			}
		return $kl;	
		
	}
	function KnockTableHor($mxl,$match,$k_format, $Itemid, $orderby){
	
		if($mxl){
				$reslng = ($mxl)*7+20;
			}else{
				$reslng = 120;
			}
			if($reslng<120) $reslng=120;
			$cfg = new stdClass();
			$cfg->wdth = $reslng+70;
			$cfg->height = 60;
			$cfg->step = 70; 
			$cfg->top_next = 50;
		
			
			
			$query = "SELECT * FROM #__bl_matchday WHERE s_id=".$this->s_id." AND t_type = 1 ORDER BY id";
			$this->db->setQuery($query);
			$mdays = $this->db->loadObjectList();
			$kl = '<br />';
			if(count($mdays)){
				foreach ($mdays as $mday){
				$k_format = $mday->k_format;
			
					if($this->t_single){
						$query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid,t1.first_name,t1.last_name,t1.nick,t2.first_name as fn2,t2.last_name as ln2,t2.nick as nick2,"
								." CONCAT(t1.first_name,' ',t1.last_name) as home, CONCAT(t2.first_name,' ',t2.last_name) as away,t1.id as hm_id,t2.id as aw_id,"
								." IF(m.score1>m.score2,CONCAT(t1.first_name,' ',t1.last_name), CONCAT(t2.first_name,' ',t2.last_name)) as winner,"
								." IF(m.score1>m.score2,t1.nick, t2.nick) as winner_nick,"
								." IF(m.score1>m.score2,t1.id,t2.id) as winnerid"
								." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_players as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_players as t2 ON m.team2_id = t2.id"
								." WHERE m.m_id = md.id AND m.published = 1 AND md.t_type = 1 AND md.s_id=".$this->s_id." AND md.id=".$mday->id
								." ORDER BY ".$orderby;
					
					}else{
						$query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away, t1.id as hm_id,t2.id as aw_id, IF(m.score1>m.score2,t1.t_name,t2.t_name) as winner,IF(m.score1>m.score2,t1.id,t2.id) as winnerid"
								." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN  #__bl_teams as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_teams as t2 ON m.team2_id = t2.id"
								." WHERE m.m_id = md.id AND m.published = 1 AND md.t_type = 1 AND md.s_id=".$this->s_id." AND md.id=".$mday->id
								." ORDER BY ".$orderby;
					
					}
					$this->db->setQuery($query);
		    
					$match = $this->db->loadObjectList();
					$error = $this->db->getErrorMsg();
					if ($error)
					{
						return JError::raiseError(500, $error);
					}
					
					$zz = 2;
					$p=0;
					
					$wdth = $cfg->wdth;
					$height = $cfg->height;
					$step = $cfg->step; 
					$top_next = $cfg->top_next;
					
					
					
					if($this->t_single){
						$query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($this->s_id)." ORDER BY t.first_name";
					}else{
						$query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".($this->s_id)." ORDER BY t.t_name";
					}
					$this->db->setQuery($query);
				
					$team = $this->db->loadObjectList();
				
					$is_team[] = JHTML::_('select.option',  0, ($this->t_single?JText::_('BLFA_SELPLAYER'):JText::_('BLFA_SELTEAM')), 'id', 't_name' ); 
				
					$teamis = array_merge($is_team,$team);
				
					$fid = $k_format;
			//	echo count($match);die();
					$kl .= $mday->m_name;
					$kl .= '<div class="combine-box-new" style="height:'.(($fid/2)*($height+$step)+60).'px;position:relative;overflow-x:auto;overflow-y:hidden;border:1px solid #ccc;">';
	
				
				while(floor($fid/$zz) >= 1){
					
					for($i=0;$i<floor($fid/$zz);$i++){
						
						if($this->t_single && isset($match[$i])){
							$match[$i]->home = $this->selectPlayerName($match[$i]);
							$match[$i]->away = $this->selectPlayerName($match[$i],"fn2","ln2","nick2");
						}

						$kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
							
						if($p==0){
						$link = '';//update
							if(isset($match[$i]->hm_id)){
								if($this->t_single){
									$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$i]->hm_id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
								}else{	
									$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$i]->hm_id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
								}
							}
							$winclass = "";
							if(($match[$i]->team2_id == -1 && $match[$i]->team1_id != -1) || ($match[$i]->m_played && (($match[$i]->score1 > $match[$i]->score2) || (($match[$i]->score1 == $match[$i]->score2)&&($match[$i]->aet1 > $match[$i]->aet2)) || (($match[$i]->score1 == $match[$i]->score2)&&($match[$i]->aet1 == $match[$i]->aet2) && ($match[$i]->p_winner == $match[$i]->hm_id))))){
								$winclass = " knwinner";
							}
							$kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $top_next - 14).'px; left:'.(20 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($match[$i]->score1) && $match[$i]->m_played)?$match[$i]->score1.($match[$i]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$match[$i]->aet1."</abbr>)":""):'').'</span>';
							// $kl .= isset($match[$i]->home)?("<a href='".$link."' title='".$match[$i]->home."'>".$match[$i]->home."</a>"):"&nbsp;";
						$kl .= ($match[$i]->team1_id != -1)?("<a href='".$link."' title='".$match[$i]->home."'>".$match[$i]->home."</a>"):(JText::_('BLFA_BYE'));	
							$kl .= '</div>';
							if(isset($match[$i]->aw_id)){
								if($this->t_single){
									$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$i]->aw_id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
								}else{	
									$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$i]->aw_id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
								}
							}	
							
							if(($match[$i]->m_played || ($match[$i]->team1_id == -1 && $match[$i]->team2_id != -1))  && $winclass == ''){
								$winclass = " knwinner";
							}else{
								$winclass = "";
							}
							$kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 13).'px; left:'.(20 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($match[$i]->score2) && $match[$i]->m_played)?$match[$i]->score2.($match[$i]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$match[$i]->aet2."</abbr>)":""):'').'</span>';
							// $kl .= isset($match[$i]->away)?("<a href='".$link."' title='".$match[$i]->away."'>".$match[$i]->away."</a>"):"&nbsp;";
						$kl .= ($match[$i]->team2_id != -1)?("<a href='".$link."' title='".$match[$i]->away."'>".$match[$i]->away."</a>"):(JText::_('BLFA_BYE'));		
							$kl .= '</div>';
							$match_link = 'index.php?option=com_joomsport&amp;task=view_match&amp;id='.(isset($match[$i]->id)?($match[$i]->id):'').'&amp;Itemid='.$Itemid;
							$kl .= (isset($match[$i]->id)?'<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10).'px; left:'.(-20 + ($p+1)*$wdth).'px;"><a href="'.$match_link.'" title="'.JText::_('BL_LINK_DETAILMATCH').'"><span class="module-menu-editor"><!-- --></span></a></div>':"");
							
							
						}else{
							
							$firstchld_ind = $i*2 + ($fid/2)*((pow(2,$p-1)-1)/pow(2,$p-2));
							$cur_ind = $i + ($fid/2)*((pow(2,$p)-1)/pow(2,$p-1));
							
							if($this->t_single){
								if(isset($match[$firstchld_ind])){
									$match[$firstchld_ind]->home = $this->selectPlayerName($match[$firstchld_ind]);
									$match[$firstchld_ind]->winner = $this->selectPlayerName($match[$firstchld_ind],"winner","","winner_nick");
									$match[$firstchld_ind]->away = $this->selectPlayerName($match[$firstchld_ind],"fn2","ln2","nick2");
								}
								if(isset($match[$firstchld_ind+1])){
									$match[$firstchld_ind+1]->home = $this->selectPlayerName($match[$firstchld_ind+1]);
									$match[$firstchld_ind+1]->away = $this->selectPlayerName($match[$firstchld_ind+1],"fn2","ln2","nick2");
									$match[$firstchld_ind+1]->winner = $this->selectPlayerName($match[$firstchld_ind+1],"winner","","winner_nick");
								}
							}
							if(isset($match[$firstchld_ind]) &&($match[$firstchld_ind]->score1 == $match[$firstchld_ind]->score2) && isset($match[$firstchld_ind]->winner)){
								
								if($match[$firstchld_ind]->aet1 > $match[$firstchld_ind]->aet2){
									$match[$firstchld_ind]->winner = $match[$firstchld_ind]->home;
									$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
									
								}elseif($match[$firstchld_ind]->aet1 < $match[$firstchld_ind]->aet2){
									$match[$firstchld_ind]->winner = $match[$firstchld_ind]->away;
									$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
								}else{
									if($match[$firstchld_ind]->p_winner && $match[$firstchld_ind]->p_winner == $match[$firstchld_ind]->team1_id){
										$match[$firstchld_ind]->winner = $match[$firstchld_ind]->home;
										$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
								
									}elseif($match[$firstchld_ind]->p_winner && $match[$firstchld_ind]->p_winner == $match[$firstchld_ind]->team2_id){
										$match[$firstchld_ind]->winner = $match[$firstchld_ind]->away;
										$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
									
									}else{
										$match[$firstchld_ind]->m_played = 0;
									}
								}
							}
							if(isset($match[$firstchld_ind +1]) && ($match[$firstchld_ind+1]->score1 == $match[$firstchld_ind+1]->score2) && isset($match[$firstchld_ind+1]->winner)){
								if($match[$firstchld_ind+1]->aet1 > $match[$firstchld_ind+1]->aet2){
									$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home;
									$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
								}elseif($match[$firstchld_ind+1]->aet1 < $match[$firstchld_ind+1]->aet2){
									$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away;
									$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
								}else{
									if($match[$firstchld_ind+1]->p_winner && $match[$firstchld_ind+1]->p_winner == $match[$firstchld_ind+1]->team1_id){
										$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home;
										$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
									}elseif( $match[$firstchld_ind+1]->p_winner && $match[$firstchld_ind+1]->p_winner == $match[$firstchld_ind+1]->team2_id){
										$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away;
										$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
									}else{
										$match[$firstchld_ind+1]->m_played = 0;
									}
								}
							}
							if(isset($match[$firstchld_ind]) && $match[$firstchld_ind]->team1_id == -1 && $match[$firstchld_ind]->away){
								$match[$firstchld_ind]->winner = $match[$firstchld_ind]->away;
								$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
								$match[$firstchld_ind]->m_played = 1;
							}
							if(isset($match[$firstchld_ind]) && $match[$firstchld_ind]->team2_id == -1 && $match[$firstchld_ind]->home){
								$match[$firstchld_ind]->winner = $match[$firstchld_ind]->home;
								$match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
								$match[$firstchld_ind]->m_played = 1;
							}
							
							if(isset($match[$firstchld_ind+1]) && $match[$firstchld_ind+1]->team1_id == -1 && $match[$firstchld_ind+1]->away){
								$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away;
								$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
								$match[$firstchld_ind+1]->m_played = 1;
							}
							if(isset($match[$firstchld_ind+1]) && $match[$firstchld_ind+1]->team2_id == -1 && $match[$firstchld_ind+1]->home){
								$match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home;
								
								$match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
								$match[$firstchld_ind+1]->m_played = 1;
							}
							
							if(isset($match[$firstchld_ind]) && $match[$firstchld_ind]->team1_id == -1 && $match[$firstchld_ind]->team2_id == -1){
								$match[$firstchld_ind]->winner = JText::_("BLFA_BYE");
								$match[$firstchld_ind]->winnerid = -1;
								$match[$firstchld_ind]->m_played = 1;
							}
							if(isset($match[$firstchld_ind+1]) && $match[$firstchld_ind+1]->team1_id == -1 && $match[$firstchld_ind+1]->team2_id == -1){
								$match[$firstchld_ind+1]->winner = JText::_("BLFA_BYE");
								$match[$firstchld_ind+1]->winnerid = -1;
								$match[$firstchld_ind+1]->m_played = 1;
							}
							
							
							$winclass = "";
							if(isset($match[$cur_ind]) && (($match[$cur_ind]->team2_id == -1 && $match[$cur_ind]->team1_id != -1) || ($match[$cur_ind]->m_played && (($match[$cur_ind]->score1 > $match[$cur_ind]->score2) || (($match[$cur_ind]->score1 == $match[$cur_ind]->score2)&&($match[$cur_ind]->aet1 > $match[$cur_ind]->aet2)) || (($match[$cur_ind]->score1 == $match[$cur_ind]->score2)&&($match[$cur_ind]->aet1 == $match[$cur_ind]->aet2) && ($match[$cur_ind]->p_winner == $match[$cur_ind]->hm_id)))))){
								$winclass = " knwinner";
							}
							$kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $top_next - 15).'px; left:'.(25 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($match[$cur_ind]->score1) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score1.($match[$cur_ind]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$match[$cur_ind]->aet1."</abbr>)":""):'').'</span>';
							if(isset($match[$firstchld_ind]->winnerid)){
								if($this->t_single){
									$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$firstchld_ind]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
								}else{	
									$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$firstchld_ind]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
								}
							}
							if(isset($match[$firstchld_ind]->winner) && $match[$firstchld_ind]->m_played){
								$kl .= ($match[$firstchld_ind]->winner != JText::_("BLFA_BYE"))?("<a href='".$link."' title='".$match[$firstchld_ind]->winner."'>".$match[$firstchld_ind]->winner."</a>"):($match[$firstchld_ind]->winner);
							}		
						//$kl .= (isset($match[$firstchld_ind]->winner) && $match[$firstchld_ind]->m_played)?("<a href='".$link."' title='".$match[$firstchld_ind]->winner."'>".$match[$firstchld_ind]->winner."</a>"):"";
							$kl .= '</div>';
							if(isset($match[$cur_ind]) && ($match[$cur_ind]->m_played || ($match[$cur_ind]->team1_id == -1 && $match[$cur_ind]->team2_id != -1))  && $winclass == ''){
								$winclass = " knwinner";
							}else{
								$winclass = "";
							}
							$kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 15).'px; left:'.(25 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($match[$cur_ind]->score2) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score2.($match[$cur_ind]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$match[$cur_ind]->aet2."</abbr>)":""):'').'</span>';
							if(isset($match[$firstchld_ind+1]->winnerid)){
								if($this->t_single){
									$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$firstchld_ind+1]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
								}else{	
									$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$firstchld_ind+1]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
								}
							}
							if(isset($match[$firstchld_ind+1]->winner) && $match[$firstchld_ind]->m_played){
								$kl .= ($match[$firstchld_ind+1]->winner != JText::_("BLFA_BYE"))?("<a href='".$link."' title='".$match[$firstchld_ind+1]->winner."'>".$match[$firstchld_ind+1]->winner."</a>"):($match[$firstchld_ind+1]->winner);
							}	
						//$kl .= (isset($match[$firstchld_ind + 1]->winner) && $match[$firstchld_ind + 1]->m_played )?("<a href='".$link."' title='".$match[$firstchld_ind+1]->winner."'>".$match[$firstchld_ind+1]->winner."</a>"):"";
							$kl .= '</div>';
							$match_link = 'index.php?option=com_joomsport&amp;task=view_match&amp;id='.(isset($match[$cur_ind]->id)?($match[$cur_ind]->id):'');
							$kl .= (isset($match[$cur_ind]->id)?'<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10).'px; left:'.(-20 + ($p+1)*$wdth).'px;"><a href="'.$match_link.'" title="'.JText::_('BL_LINK_DETAILMATCH').'"><span class="module-menu-editor"><!-- --></span></a></div>':"");
						}
					
					}
					$top_next += $height/2;
					$height = $height + $step;
					$step = $height;
					$zz *= 2;
					$p++;
					
				}

				$winmd_id = $fid - 2;
				$wiinn = '';
				if($this->t_single){
					if(isset($match[$winmd_id])){

						$match[$winmd_id]->winner = $this->selectPlayerName($match[$winmd_id],"winner","","winner_nick");
					}
					
				}
				if(isset($match[$winmd_id]->winner) && $match[$winmd_id]->winner && $match[$winmd_id]->score1 != $match[$winmd_id]->score2 && $match[$winmd_id]->m_played) 
				{ 
					if($this->t_single){
						$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$winmd_id]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
					}else{	
						$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$winmd_id]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
					}
					$wiinn = "<div class='field-comb' style='width:".($reslng+40)."px;margin-left:5px !important;margin-top:-17px !important;'><div><div><div class='knres'></div><div class='knlink' style='width:".$reslng."px;'><div><div><a href='".$link."' title='".$match[$winmd_id]->winner."'>".$match[$winmd_id]->winner."</a></div></div></div></div></div></div>";
				}
				
				if($fid){
					$kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.( $top_next).'px; left:'.(20 + ($p)*$wdth).'px;">'.$wiinn.'</div>';
				}	
				$kl .=  '</div>';
				}
			}
			return $kl;
		
	}
        
        ///version 3.3
    
    function getRounds($tourn){
        
        $query = "SELECT *"
                ." FROM #__bl_matchday "
                ." WHERE s_id = ".$tourn->id
                ." ORDER BY ordering, id";
        $this->db->setQuery($query);
	$mdays = $this->db->loadObjectList();
        for($i=0; $i < count($mdays); $i++){
            $obj = new MatchdayRace($mdays[$i]->id, $tourn);
            $this->_lists['rounds_name'][$i] = $mdays[$i]->m_name;
            $this->_lists['rounds'][$i] = $obj->getRoundsById();
            
            $query = " SELECT * FROM #__bl_rounds_extracol"
                ." WHERE round_id = ".$mdays[$i]->id
                ." ORDER BY ordering";
            $this->db->setQuery($query);
            $this->_lists['extracol'][$i] = $this->db->loadObjectList();
        }
        $this->_lists['options'] = json_decode($tourn->season_options);
        $this->_layout = 'race';
    }
	
}	