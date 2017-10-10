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
require(dirname(__FILE__).'/knockout.php');
require_once(JPATH_SITE.'/administrator/components/com_joomsport/classes/class-matchday-race.php');


class matchdayJSModel extends JSPRO_Models
{
	var $_lists = null;
	var $s_id = null;
	var $t_single = null;
	var $t_type = null;
	var $m_id = null;
	var $_layout = null;
	
	var $_total = null;
	var $_pagination = null;
	var $limit = null;
	var $limitstart = null;
    var $knock_type  = null;
    var $title = null;
    var $p_title = null;

	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$this->m_id = JRequest::getVar( 'id', 0, '', 'int' );
        $this->title = JFactory::getDocument()->getTitle();
		
		
		$query = "SELECT COUNT(*) FROM #__bl_seasons as s, #__bl_tournament as t, #__bl_matchday as md WHERE t.published='1' AND s.published='1' AND t.id = s.t_id AND s.s_id = md.s_id AND md.id='".$this->m_id."'";
		$this->db->setQuery($query);

		if(!$this->db->loadResult()){
			$query = "SELECT COUNT(*) FROM   #__bl_matchday as md WHERE md.s_id = '-1' AND md.id = '".$this->m_id."'";
			$this->db->setQuery($query);

		}
		if(!$this->m_id || !$this->db->loadResult()){
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return; 
		}
		
		$this->limit	= $mainframe->getUserStateFromRequest( 'com_joomsport.mdl_jslimit','jslimit', 20, 'int' );
		$this->limitstart	= JRequest::getVar( 'page', 1,'', 'int' );
		$this->limitstart = intval($this->limitstart)>1?$this->limitstart:1;
		
	}

	function getData()
	{
        $this->knock_type = new JS_Knockout();
		
		
		$query = "SELECT m_name FROM #__bl_matchday WHERE id=".$this->m_id;
		$this->db->setQuery($query);
		$this->p_title = $this->db->loadResult();

		//title
        $this->_params = $this->JS_PageTitle($this->title?$this->title:$this->p_title);

		$query = "SELECT s_id FROM #__bl_matchday WHERE id=".$this->m_id;
		$this->db->setQuery($query);
		$this->s_id = $this->db->loadResult();
		
		$query = "SELECT t_type FROM #__bl_matchday WHERE id=".$this->m_id;
        $this->db->setQuery($query);
        $t_type = $this->db->loadResult();
		
		$this->_lists["season_par"] = $this->getSParametrs($this->s_id);
		
		//get tiurnament type
		$tourn = $this->getTournOpt($this->s_id);
		if($tourn){
			$this->t_single = $tourn->t_single;
			$this->t_type = $t_type;
		}
                
                if($tourn->tournament_type == '1'){
                    $this->getRounds($this->m_id, $tourn);
                }else{
                    if($this->t_type){
                            $this->getKnockMd();
                    }else{
                            $this->getGroupMd();
                            $this->getPagination();
                    }
                }
		
		$this->_lists["enbl_extra"] = 0;
		if($this->s_id){
			$this->_lists["unable_reg"] = $this->unblSeasonReg();
		}
		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],@$this->_lists["unable_reg"],$this->s_id,1,0,1);
		
		
	}
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			$this->_pagination = new JS_Pagination($this->getTotal(),$this->limitstart,$this->limit);
		}

		return $this->_pagination;
	}
	function getTotal(){
		if($this->t_single){
			$pln = $this->getJS_Config('player_name');
			$query = "SELECT COUNT(*),"
					." ".($pln?"IF(t2.nick<>'',t2.nick,CONCAT(t2.first_name,' ',t2.last_name))":"CONCAT(t2.first_name,' ',t2.last_name)")." AS  away,t1.id as hm_id,t2.id as aw_id"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
					." WHERE m.m_id = md.id AND m.published = 1 AND m.team1_id = t1.id AND m.team2_id = t2.id AND md.id = ".$this->m_id;
		}else{
			$query = "SELECT COUNT(*)"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2"
					." WHERE m.m_id = md.id AND m.published = 1  AND m.team1_id = t1.id AND m.team2_id = t2.id AND md.id = ".$this->m_id
					." ORDER BY m.m_date,m.m_time";
		}
		
		
		
		
		$this->db->setQuery($query);
		$this->_total = $this->db->loadResult();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		//friendly
		
		if($this->s_id == -1){
			$query = "SELECT COUNT(*)"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
					." WHERE m.m_id = md.id AND m.published = 1 AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_single='1' AND md.id = ".$this->m_id;
			$this->db->setQuery($query);
			$friendly_single = $this->db->loadObjectList();		
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
			$query = "SELECT COUNT(*)"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2"
					." WHERE m.m_id = md.id AND m.published = 1  AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_single='0' AND md.id = ".$this->m_id;		
			$this->db->setQuery($query);
			$friendly_team = $this->db->loadObjectList();
			$this->_total = @array_merge($friendly_single,$friendly_team);
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
		}

		$this->_total = $this->db->loadResult();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		
		return $this->_total;
	}
	function getGroupMd(){
		$pln = $this->getJS_Config('player_name');
		if($this->t_single){
			$query = "SELECT m.*,md.m_name,md.id as mdid,md.s_id, ".($pln?"IF(t1.nick<>'',t1.nick,CONCAT(t1.first_name,' ',t1.last_name))":"CONCAT(t1.first_name,' ',t1.last_name)")." AS home,"
					." ".($pln?"IF(t2.nick<>'',t2.nick,CONCAT(t2.first_name,' ',t2.last_name))":"CONCAT(t2.first_name,' ',t2.last_name)")." AS  away,t1.id as hm_id,t2.id as aw_id"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
					." WHERE m.m_id = md.id AND m.published = 1 AND m.team1_id = t1.id AND m.team2_id = t2.id AND md.id = ".$this->m_id;
		}else{
			$query = "SELECT md.m_name,m.*, t1.t_name as home, t2.t_name as away,md.s_id,t1.id as hm_id,t2.id as aw_id,t1.t_emblem as emb1,t2.t_emblem as emb2"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2"
					." WHERE m.m_id = md.id AND m.published = 1  AND m.team1_id = t1.id AND m.team2_id = t2.id AND md.id = ".$this->m_id
					." ORDER BY m.m_date,m.m_time";
		}
		
		
		
		
		$this->db->setQuery($query,($this->limitstart-1)*$this->limit, $this->limit);
		$this->_lists["match"] = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		//friendly
		
		if($this->s_id == -1){
			$query = "SELECT m.*,md.m_name,md.id as mdid,md.s_id, CONCAT(t1.first_name,' ',t1.last_name) as home, CONCAT(t2.first_name,' ',t2.last_name) as away,t1.id as hm_id,t2.id as aw_id"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
					." WHERE m.m_id = md.id AND m.published = 1 AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_single='1' AND md.id = ".$this->m_id;
			$this->db->setQuery($query);
			$friendly_single = $this->db->loadObjectList();		
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
			$query = "SELECT md.m_name,m.*, t1.t_name as home, t2.t_name as away,md.s_id,t1.id as hm_id,t2.id as aw_id,t1.t_emblem as emb1,t2.t_emblem as emb2"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2"
					." WHERE m.m_id = md.id AND m.published = 1  AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_single='0' AND md.id = ".$this->m_id;		
			$this->db->setQuery($query);
			$friendly_team = $this->db->loadObjectList();
			$this->_lists["match"] = @array_merge($friendly_single,$friendly_team);
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
		}
		
	}
	function getKnockMd(){
		$Itemid = JRequest::getInt('Itemid');
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
					." WHERE m.m_id = md.id AND m.published = 1 AND m.k_type = '0' AND md.id=".$this->m_id
					."  ORDER BY m.k_stage,m.k_ordering";
		
			}else{
				$query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away,t1.id as hm_id,t2.id as aw_id,IF(m.score1>m.score2,t1.t_name,t2.t_name) as winner,IF(m.score1>m.score2,t1.id,t2.id) as winnerid,IF(m.score1<m.score2,t1.t_name,t2.t_name) as looser,IF(m.score1<m.score2,t1.id,t2.id) as looserid"
				." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN  #__bl_teams as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_teams as t2 ON m.team2_id = t2.id"
				." WHERE m.m_id = md.id AND m.published = 1 AND m.k_type = '0' AND md.id=".$this->m_id
				." ORDER BY m.k_stage,m.k_ordering";
			
			}
			
			$this->db->setQuery($query);
		
			$matchs = $this->db->loadObjectList();
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
            //////
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
                ." WHERE m.m_id = md.id AND m.published = 1 AND m.k_type = '1' AND md.id=".$this->m_id
                ."  ORDER BY m.k_stage,m.k_ordering";

        }else{
            $query = "SELECT md.k_format,m.*,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away,t1.id as hm_id,t2.id as aw_id,IF(m.score1>m.score2,t1.t_name,t2.t_name) as winner,IF(m.score1>m.score2,t1.id,t2.id) as winnerid,IF(m.score1<m.score2,t1.t_name,t2.t_name) as looser,IF(m.score1<m.score2,t1.id,t2.id) as looserid"
                ." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN  #__bl_teams as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_teams as t2 ON m.team2_id = t2.id"
                ." WHERE m.m_id = md.id AND m.published = 1 AND m.k_type = '1' AND md.id=".$this->m_id
                ." ORDER BY m.k_stage,m.k_ordering";

        }

        $this->db->setQuery($query);

        $matchsDE = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error)
        {
            return JError::raiseError(500, $error);
        }

            //////
			$k_format = $matchs[0]->k_format;	
			//$k_format1 = $matchsDE[0]->k_format;

			if(count($matchs) && $k_format){
				$match = $matchs;
				$matchDE = $matchsDE;

				if($this->t_single){
					$query = "SELECT MAX(LENGTH(CONCAT(t.first_name,' ',t.last_name)))"
							." FROM #__bl_season_players as st, #__bl_players as t"
							." WHERE t.id = st.player_id AND st.season_id = ".$this->s_id;
				}else{
					$query = "SELECT MAX(LENGTH(t.t_name))"
							." FROM #__bl_season_teams as st, #__bl_teams as t"
							." WHERE t.id = st.team_id AND st.season_id = ".$this->s_id;
				}
				$this->db->setQuery($query);
				$mxl = $this->db->loadResult();
				$error = $this->db->getErrorMsg();
				if ($error)
				{
					return JError::raiseError(500, $error);
				}
				if($this->getJS_Config('knock_style')){
					//$kl = $this->VertKnView($mxl,$match,$k_format, $Itemid);
				}else{
                    if($this->t_type == 1){
                        //$kl = $this->HorKnView($mxl,$match,$k_format,$Itemid); ////
                        $kl = js_mobile::isMobile()? $this->knock_type->HorKnView_mobile($mxl,$match,$k_format,$Itemid,$this->t_single,$this->s_id):$this->knock_type->HorKnView($mxl,$match,$k_format,$Itemid,$this->t_single,$this->s_id);

                    }else if($this->t_type == 2){
                        //$kl = $this->HorKnViewDE($mxl,$match,$matchDE,$k_format,$k_format1,$Itemid);
                    
                        $kl = js_mobile::isMobile()?$this->knock_type->HorKnViewDE_mobile($mxl,$match,$matchDE,$k_format,$Itemid,$this->t_single,$this->s_id): $this->knock_type->HorKnViewDE($mxl,$match,$matchDE,$k_format,$Itemid,$this->t_single,$this->s_id);
                    }
				}
				
				
				
			$this->_lists['knock_layout'] = $kl;
			
			$this->_layout = '_knock';
		}	
	}
	
	function VertKnView($mxl,$match,$k_format,$Itemid){
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
		

		$kl = '<br />';
		
		$zz = 2;
		$p=0;
		
		$wdth = $cfg->wdth;
		$height = $cfg->height;
		$step = $cfg->step; 
		$top_next = $cfg->top_next;
		

		$fid = $k_format;
		
		$kl .= '<div class="combine-box-vert" style="height:'.(($fid/2)*($height+$step)+60).'px;position:relative;overflow-x:auto;overflow-y:auto;border:1px solid #777;">';
			
		$bz = 0;
		$vz = 1;
		
		$arr_prev_pl = array();
		$vetks_null = array();
		$link = '';
		while(floor($fid/$zz) >= 1){
			
			for($i=0;$i<floor($fid/$zz);$i++){
				
				//$kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
					
				
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
					$kl .= (isset($match[$i]->id)?'<div style="position:absolute; top:'.($top_next + 40).'px; left:'.(20*($i+2) + ($i+1)*$wdth + $bz - 20).'px;"><a href="'.$match_link.'" title="'.JText::_('BL_LINK_DETAILMATCH').'"><span class="module-menu-editor"><!-- --></span></a></div>':"");
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
						if(isset($arr_prev_pl[$p-1])){
							if(in_array($i*2,$arr_prev_pl[$p-1]) OR in_array($i*2+1,$arr_prev_pl[$p-1])){
								$arr_prev_pl[$p][] = $i;
							}
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
					$kl .= '</div><div>'.((isset($match[$cur_ind]->score1) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score1:"").'</div>';
					$kl .= '</div>';
					$kl .= '<div class="knock_el_vert" style="position:absolute; top:'.($top_next).'px; left:'.(((2*$wdth+20)*(2*$vz + 1)*pow(2,$p-1) + (pow(2,$p-1)-1)*20)/2 -$wdth/2 + 20*$vz*$p + 20).'px;width:'.($reslng+50).'px;height:50px;border:1px solid #000;"><div>';
					if(isset($match[$firstchld_ind+1]->winnerid)){
						if($this->t_single){
							$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$firstchld_ind+1]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
						}else{	
							$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$firstchld_ind+1]->winnerid.'&sid='.$this->s_id.'&Itemid='.$Itemid);
						}
					}
					$kl .= (isset($match[$firstchld_ind + 1]->winner) && $match[$firstchld_ind + 1]->m_played && $has_winner2)?("<a href='".$link."' title='".$match[$firstchld_ind+1]->winner."'>".$match[$firstchld_ind+1]->winner."</a>"):"";
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
		return $kl;
	}
	

	///////////////////double


    function getMBy($match){
        if(isset($match) && $match->team1_id == -1 && $match->away_team){
            $match->winner = $match->away_team;
            $match->looser = JText::_("BLFA_BYE");
            $match->looserid = -1;
            $match->winnerid = $match->team2_id;
            $match->m_played = 1;
        }
        if(isset($match) && $match->team2_id == -1 && $match->home_team){
            $match->winner = $match->home_team;
            $match->winnerid = $match->team1_id;
            $match->looser = JText::_("BLFA_BYE");
            $match->looserid = -1;
            $match->m_played = 1;
        }
        if(isset($match) && $match->team1_id == -1 && $match->team2_id == -1){
            $match->winner = JText::_("BLFA_BYE");
            $match->winnerid = -1;
            $match->m_played = 1;
        }
        return $match;

    }
    
    
    
    
    
    ///version 3.3
    
    function getRounds($id, $tourn){
        
        $obj = new MatchdayRace($id, $tourn);
        $this->_lists['rounds'] = $obj->getRoundsById();
        $this->_lists['options'] = json_decode($tourn->season_options);
        $this->_layout = '_race';
        
        $query = " SELECT * FROM #__bl_rounds_extracol"
                ." WHERE round_id = ".$id
                ." ORDER BY ordering";
            $this->db->setQuery($query);
            $this->_lists['extracol'] = $this->db->loadObjectList();
        
    }
}	