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

class matchJSModel extends JSPRO_Models
{
	var $_lists = null;
	var $s_id = null;
	var $t_single = null;
	var $t_type = null;
	var $m_id = null;
    var $title = null;
    var $p_title = null;
	
	function __construct()
	{
		parent::__construct();
		
		$this->m_id = JRequest::getVar( 'id', 0, '', 'int' );
		$this->title = JFactory::getDocument()->getTitle();

		$query = "SELECT COUNT(*) FROM #__bl_seasons as s, #__bl_tournament as t, #__bl_matchday as md, #__bl_match as m WHERE t.published='1' AND s.published='1' AND t.id = s.t_id AND s.s_id = md.s_id AND md.id=m.m_id AND m.id = '".$this->m_id."'";
		$this->db->setQuery($query);

		if(!$this->db->loadResult()){
			$query = "SELECT COUNT(*) FROM   #__bl_matchday as md, #__bl_match as m WHERE md.s_id = '-1' AND md.id=m.m_id AND m.id = '".$this->m_id."'";
			$this->db->setQuery($query);
		}
	
		if(!$this->m_id or !$this->db->loadResult()){
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return; 
		}
		
	}

	function getData()
	{
		
		$query = "SELECT s_id FROM #__bl_matchday as md, #__bl_match as m  WHERE md.id=m.m_id AND m.id = '".$this->m_id."'";
		$this->db->setQuery($query);
		$this->s_id = $this->db->loadResult();
		
		$row 	= new JTableMatch($this->db);
		$row->load($this->m_id);
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		//get tiurnament type
		if($this->s_id != -1){
			$tourn = $this->getTournOpt($this->s_id );
			$this->t_single = $tourn->t_single;
			$this->t_type = 0;
			$this->_lists["s_enbl_extra"] = $tourn->s_enbl_extra;
		}else{

			$this->t_type=0;
			if($row->m_single == 1){
				$this->t_single = 1;

			}else{
				$this->t_single = 0;
			}
			
			$this->_lists["s_enbl_extra"] = 0;
		}		

		$this->_lists["match"] = $this->getMatch();
		//title
		$match = $this->_lists["match"];
		
		$this->p_title = $match->home.' '.($match->m_played?$match->score1:'-').':'.($match->m_played?$match->score2:'-').' '.$match->away;
		$this->_params = $this->JS_PageTitle($this->title?$this->title:$this->p_title);

		$this->_lists["season_par"] = $this->getSParametrs($this->s_id );
		
		$this->getMEvents($match);


		$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 3 AND cat_id = ".$this->m_id;
		$this->db->setQuery($query);
		$this->_lists["photos"] = $this->db->loadObjectList();

		///--------MAPS--------------///
		$query = "SELECT m.*,mp.m_score1,mp.m_score2 FROM #__bl_seas_maps as sm, #__bl_maps as m LEFT JOIN #__bl_mapscore as mp ON m.id=mp.map_id AND mp.m_id=".$this->m_id." WHERE m.id=sm.map_id AND sm.season_id=".$this->s_id." ORDER BY m.id";
		$this->db->setQuery($query);
		$this->_lists['maps'] = $this->db->loadObjectList();

		$this->_lists["enbl_extra"] = 0;
		if($this->s_id){
			$this->_lists["unable_reg"] = $this->unblSeasonReg();
		}
		$this->_lists["teams_season"] = $this->teamsToModer();
		$this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"],@$this->_lists["unable_reg"],$this->s_id,1,0,1);
		
		$this->_lists["mcomments"] = $this->getJS_Config("mcomments");
		if($this->_lists["mcomments"]){
			$this->getComments();
		}
		///line up
		$this->getLineUps($match);
		//betts
		if($this->isBet()){
			$this->_lists["betevents"] = $this->getMatchBetEvents($this->m_id);
		}
		
		$this->_lists['sh_name'] = getJS_Config('player_name');
		///-----EXTRAFIELDS---//
		
		$this->_lists['ext_fields'] = $this->getAddFields($this->m_id,'2','match');

		//social buttons
		$tt = $match->home.' '.($match->m_played?$match->score1:'-').':'.($match->m_played?$match->score2:'-').' '.$match->away;
		$this->_lists['socbut'] = $this->getSocialButtons('jsbp_match',$tt,'',htmlspecialchars(strip_tags($match->match_descr)));
	}
	function getLineUps($match){
		$query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,p.def_img"
				." FROM #__bl_players as p, #__bl_squard as s"
				." WHERE p.id=s.player_id AND s.match_id=".$this->m_id." AND s.team_id=".intval($match->hm_id)." AND s.mainsquard = '1'"
				." ORDER BY p.first_name,p.last_name";
		$this->db->setQuery($query);
		$this->_lists['squard1'] = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
///--->            1
		$query = "SELECT md.s_id FROM #__bl_matchday as md, #__bl_match as m WHERE m.id = '".$this->m_id."' AND md.id = m.m_id";
			$this->db->setQuery($query);
			$s_id = $this->db->loadResult();

		
		
		$query = "SELECT c.cfg_value FROM #__bl_config as c WHERE c.cfg_name = 'pllist_order_se'";//id extra
			$this->db->setQuery($query);
			$etra_id = $this->db->loadResult();

			$etra_id_exp = ($etra_id)?(explode("_",$etra_id)):('');

		

		$rel = '';
		if(isset($etra_id_exp[0])){
				$query = "SELECT ef.season_related FROM #__bl_extra_filds as ef WHERE ef.id = '".$etra_id_exp[0]."'";
					$this->db->setQuery($query);
					$rel = $this->db->loadResult();	

		}
///////////
function mySort($f1,$f2){
	if(isset($f1->eorder) || isset($f2->eorder)){	
		if($f1->eorder == ''){
			return 1;
		}
		if($f2->eorder == ''){
			return -1;
		}
		if($f1->eorder < $f2->eorder){
			return -1;
		}
		else if($f1->eorder > $f2->eorder){
			return 1;
		}else{return 0;}
		//return strcasecmp($f1->eorder, $f2->eorder);
	}
}

		if(count($this->_lists['squard1'])){
			for($i=0;$i<count($this->_lists['squard1']);$i++){
				$this->_lists['squard1'][$i]->name = $this->selectPlayerName($this->_lists['squard1'][$i]);
				$def_img2 = '';
				$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$this->_lists['squard1'][$i]->id;
				$this->db->setQuery($query);
				$photos2 = $this->db->loadObjectList();
				if($this->_lists['squard1'][$i]->def_img){
					$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$this->_lists['squard1'][$i]->def_img;
					$this->db->setQuery($query);
					$def_img2 = $this->db->loadResult();
				}else if(isset($photos2[0])){
					$def_img2 = $photos2[0]->filename;
				}
				$img = '';
                                if (js_mobile::isMobile()) {
                                    if($def_img2 && is_file('media/bearleague/'.$def_img2)){
                                            $img = "<img class='img-thumbnail' ".getImgPop($def_img2,1)." />";
                                    }else{
                                            $img = "<img class='img-thumbnail' src='".JUri::Base()."components/com_joomsport/img/ico/season-list-player-ico.gif' alt='' />";
                                    }
                                } else {
                                    if($def_img2 && is_file('media/bearleague/'.$def_img2)){
                                            $img = "<div class='team-embl' style='margin-right:8px;'><img class='team-embl  player-ico' ".getImgPop($def_img2,1)." /></div><span style='width:7px;display:table-cell'></span>";
                                    }else{
                                            $img = "<img class='player-ico' src='".JUri::Base()."components/com_joomsport/img/ico/season-list-player-ico.gif' width='30' height='30' alt='' />";
                                    }
                                }
				$this->_lists['squard1'][$i]->photo = $img;
			///get extra
				$this->_lists['squard1'][$i]->extra_val = '';//initialisation
				$this->_lists['squard1'][$i]->eorder = '';
				if($rel){
					$query = "SELECT es.sel_value, es.eordering FROM #__bl_extra_select as es, #__bl_extra_values as ev WHERE ev.f_id = '".$etra_id_exp[0]."' AND ev.uid = '".$this->_lists['squard1'][$i]->id."' AND ev.fvalue = es.id AND ev.season_id = ".$s_id.""; 
					$this->db->setQuery($query);
					$etra_val = $this->db->loadRow();
					
					$this->_lists['squard1'][$i]->extra_val = $etra_val[0];
					$this->_lists['squard1'][$i]->eorder = $etra_val[1];
					
				}else{
					if(isset($etra_id_exp[0])){
					
						$query = "SELECT es.sel_value, ev.season_id, es.eordering FROM #__bl_extra_select as es, #__bl_extra_values as ev WHERE ev.f_id = '".$etra_id_exp[0]."' AND ev.uid = '".$this->_lists['squard1'][$i]->id."' AND ev.fvalue = es.id"; 
						$this->db->setQuery($query);
						$etra_val = $this->db->loadRow();

						$this->_lists['squard1'][$i]->extra_val = $etra_val[0];
						$this->_lists['squard1'][$i]->eorder = $etra_val[2];
					}	
						
				}
			}
			
/////////

usort($this->_lists['squard1'],'mySort');
 

/////////
		}

		$query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,p.def_img"
				." FROM #__bl_players as p, #__bl_squard as s"
				." WHERE p.id=s.player_id AND s.match_id=".$this->m_id." AND s.team_id=".intval($match->aw_id)." AND s.mainsquard = '1'"
				." ORDER BY p.first_name,p.last_name";
		$this->db->setQuery($query);
		$this->_lists['squard2'] = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		if(count($this->_lists['squard2'])){
			for($i=0;$i<count($this->_lists['squard2']);$i++){
				$def_img2 = '';
				$this->_lists['squard2'][$i]->name = $this->selectPlayerName($this->_lists['squard2'][$i]);
				$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$this->_lists['squard2'][$i]->id;
				$this->db->setQuery($query);
				$photos2 = $this->db->loadObjectList();
				if($this->_lists['squard2'][$i]->def_img){
					$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$this->_lists['squard2'][$i]->def_img;
					$this->db->setQuery($query);
					$def_img2 = $this->db->loadResult();
				}else if(isset($photos2[0])){
					$def_img2 = $photos2[0]->filename;
				}
				$img = '';
                                if (js_mobile::isMobile()) {
                                    if($def_img2 && is_file('media/bearleague/'.$def_img2)){
                                            $img = "<img class='img-thumbnail' ".getImgPop($def_img2,1)." />";
                                    }else{
                                            $img = "<img class='img-thumbnail' src='".JUri::Base()."components/com_joomsport/img/ico/season-list-player-ico.gif' alt='' />";
                                    }
                                } else {
                                    if($def_img2 && is_file('media/bearleague/'.$def_img2)){
                                            $img = "<div class='team-embl' style='margin-right:8px;'><img class='team-embl  player-ico' ".getImgPop($def_img2,1)." /></div><span style='width:7px;display:table-cell'></span>";
                                    }else{
                                            $img = "<img class='player-ico' src='".JUri::Base()."components/com_joomsport/img/ico/season-list-player-ico.gif' width='30' height='30' alt='' />";
                                    }
                                }
				$this->_lists['squard2'][$i]->photo = $img;
//--->		      2		
				$this->_lists['squard2'][$i]->extra_val = '';
				if($rel){
					$query = "SELECT es.sel_value, es.eordering FROM #__bl_extra_select as es, #__bl_extra_values as ev WHERE ev.f_id = '".$etra_id_exp[0]."' AND ev.uid = '".$this->_lists['squard2'][$i]->id."' AND ev.fvalue = es.id AND ev.season_id = '".$s_id."'"; 
					$this->db->setQuery($query);
					$etra_val = $this->db->loadRow();
				
					$this->_lists['squard2'][$i]->extra_val = $etra_val[0];
					$this->_lists['squard2'][$i]->eorder = $etra_val[1];
				}else{
					if(isset($etra_id_exp[0])){
					
						$query = "SELECT es.sel_value, ev.season_id, es.eordering FROM #__bl_extra_select as es, #__bl_extra_values as ev WHERE ev.f_id = '".$etra_id_exp[0]."' AND ev.uid = '".$this->_lists['squard2'][$i]->id."' AND ev.fvalue = es.id"; 
						$this->db->setQuery($query);
						$etra_val = $this->db->loadRow();
						
						$this->_lists['squard2'][$i]->extra_val = $etra_val[0];
						$this->_lists['squard2'][$i]->eorder = $etra_val[2];
					}	
						
				}
				
			}
			usort($this->_lists['squard2'],'mySort');
			//print_r($this->_lists['squard2']);
		}
		$query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,p.def_img"
				." FROM #__bl_players as p, #__bl_squard as s"
				." WHERE p.id=s.player_id AND s.match_id=".$this->m_id." AND s.team_id=".intval($match->hm_id)." AND s.mainsquard = '0'"
				." ORDER BY p.first_name,p.last_name";
		$this->db->setQuery($query);
		$this->_lists['squard1_res'] = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		if(count($this->_lists['squard1_res'])){
			for($i=0;$i<count($this->_lists['squard1_res']);$i++){
				$def_img2 = '';
				$this->_lists['squard1_res'][$i]->name = $this->selectPlayerName($this->_lists['squard1_res'][$i]);
				$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$this->_lists['squard1_res'][$i]->id;
				$this->db->setQuery($query);
				$photos2 = $this->db->loadObjectList();
				if($this->_lists['squard1_res'][$i]->def_img){
					$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$this->_lists['squard1_res'][$i]->def_img;
					$this->db->setQuery($query);
					$def_img2 = $this->db->loadResult();
				}else if(isset($photos2[0])){
					$def_img2 = $photos2[0]->filename;
				}
				$img = '';
                                if (js_mobile::isMobile()) {
                                    if($def_img2 && is_file('media/bearleague/'.$def_img2)){
                                            $img = "<img class='img-thumbnail' ".getImgPop($def_img2,1)." />";
                                    }else{
                                            $img = "<img class='img-thumbnail' src='".JUri::Base()."components/com_joomsport/img/ico/season-list-player-ico.gif' alt='' />";
                                    }
                                } else {
                                    if($def_img2 && is_file('media/bearleague/'.$def_img2)){
                                            $img = "<img class='team-embl  player-ico' ".getImgPop($def_img2,1)." />";
                                    }else{
                                            $img = "<img class='player-ico' src='".JUri::Base()."components/com_joomsport/img/ico/season-list-player-ico.gif' width='30' height='30' alt='' />";
                                    }
                                }
				$this->_lists['squard1_res'][$i]->photo = $img;
			}
		}
		$query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,p.def_img"
				." FROM #__bl_players as p, #__bl_squard as s"
				." WHERE p.id=s.player_id AND s.match_id=".$this->m_id." AND s.team_id=".intval($match->aw_id)." AND s.mainsquard = '0'"
				." ORDER BY p.first_name,p.last_name";
		$this->db->setQuery($query);
		$this->_lists['squard2_res'] = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		if(count($this->_lists['squard2_res'])){
			for($i=0;$i<count($this->_lists['squard2_res']);$i++){
				$def_img2 = '';
				$this->_lists['squard2_res'][$i]->name = $this->selectPlayerName($this->_lists['squard2_res'][$i]);
				$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$this->_lists['squard2_res'][$i]->id;
				$this->db->setQuery($query);
				$photos2 = $this->db->loadObjectList();
				if($this->_lists['squard2_res'][$i]->def_img){
					$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$this->_lists['squard2_res'][$i]->def_img;
					$this->db->setQuery($query);
					$def_img2 = $this->db->loadResult();
				}else if(isset($photos2[0])){
					$def_img2 = $photos2[0]->filename;
				}
				$img = '';
                                if (js_mobile::isMobile()) {
                                    if($def_img2 && is_file('media/bearleague/'.$def_img2)){
                                            $img = "<img class='img-thumbnail' ".getImgPop($def_img2,1)." />";
                                    }else{
                                            $img = "<img class='img-thumbnail' src='".JUri::Base()."components/com_joomsport/img/ico/season-list-player-ico.gif' alt='' />";
                                    }
                                } else {
                                    if($def_img2 && is_file('media/bearleague/'.$def_img2)){
                                            $img = "<img class='team-embl  player-ico' ".getImgPop($def_img2,1)." />";
                                    }else{
                                            $img = "<img class='player-ico' src='".JUri::Base()."components/com_joomsport/img/ico/season-list-player-ico.gif' width='30' height='30' alt='' />";
                                    }
                                }
				$this->_lists['squard2_res'][$i]->photo = $img;
			}
		}
		//subs in
		$query = "SELECT s.*,p1.first_name as p1first,p1.last_name as p1last,p1.nick as p1nick,p2.first_name as p2first,p2.last_name as p2last,p2.nick as p2nick,CONCAT(p1.first_name,' ',p1.last_name) as plin,CONCAT(p2.first_name,' ',p2.last_name) as plout FROM #__bl_subsin as s, #__bl_players as p1, #__bl_players as p2 WHERE p1.id=s.player_in AND p2.id=s.player_out AND s.match_id=".$this->m_id." AND s.team_id=".intval($match->hm_id)." ORDER BY s.minutes";
		$this->db->setQuery($query);
		$this->_lists['subsin1'] = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		if(count($this->_lists['subsin1'])){
			for($i=0;$i<count($this->_lists['subsin1']);$i++){
				$this->_lists['subsin1'][$i]->plin = $this->selectPlayerName($this->_lists['subsin1'][$i],"p1first","p1last","p1nick");
				$this->_lists['subsin1'][$i]->plout = $this->selectPlayerName($this->_lists['subsin1'][$i],"p2first","p2last","p2nick");
			}
		}	
		$query = "SELECT s.*,p1.first_name as p1first,p1.last_name as p1last,p1.nick as p1nick,p2.first_name as p2first,p2.last_name as p2last,p2.nick as p2nick,CONCAT(p1.first_name,' ',p1.last_name) as plin,CONCAT(p2.first_name,' ',p2.last_name) as plout FROM #__bl_subsin as s, #__bl_players as p1, #__bl_players as p2 WHERE p1.id=s.player_in AND p2.id=s.player_out AND s.match_id=".$this->m_id." AND s.team_id=".intval($match->aw_id)." ORDER BY s.minutes";
		$this->db->setQuery($query);
		$this->_lists['subsin2'] = $this->db->loadObjectList();
		if(count($this->_lists['subsin2'])){
			for($i=0;$i<count($this->_lists['subsin2']);$i++){
				$this->_lists['subsin2'][$i]->plin = $this->selectPlayerName($this->_lists['subsin2'][$i],"p1first","p1last","p1nick");
				$this->_lists['subsin2'][$i]->plout = $this->selectPlayerName($this->_lists['subsin2'][$i],"p2first","p2last","p2nick");
			}
		}
		
	}
	function getComments(){
		$this->_lists["usera"]	= JFactory::getUser();
		if($this->getVer() >= '1.6'){
			$query = "SELECT DISTINCT(c.id),c.*,IF(pl.nick <> '',pl.nick,p.name) as nick, p.id as usrid,pl.id as usrid,pl.def_img,CONCAT(pl.first_name,' ',pl.last_name) as plname"
					." FROM `#__bl_comments` as c, #__users as p LEFT JOIN #__bl_players as pl ON p.id=pl.usr_id"
					." WHERE c.match_id = ".$this->m_id." AND c.user_id=p.id"
					." ORDER BY c.date_time";
		
		}else{
			$query = "SELECT DISTINCT(c.id), c.*,IF(pl.nick <> '',pl.nick,p.name) as nick, IF(p.gid <> 25,'0','1') as gid, p.id as usrid,pl.id,pl.def_img,CONCAT(pl.first_name,' ',pl.last_name) as plname"
					." FROM `#__bl_comments` as c, #__users as p LEFT JOIN #__bl_players as pl ON p.id=pl.usr_id"
					." WHERE c.match_id = ".$this->m_id." AND c.user_id=p.id"
					." ORDER BY c.date_time";
		
		}

		$this->db->setQuery($query);
		$this->_lists["comments"] = $this->db->loadObjectList();

		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
        for($j=0;$j<count($this->_lists["comments"]);$j++){
            $player_comment = $this->_lists["comments"][$j];
            $def_img = '';
            if($player_comment->id){
                if ($pl_id = $player_comment->usrid) {
                    $pl_image = $player_comment->def_img;
                    $query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$pl_id;
                    $this->db->setQuery($query);
                    $photos = $this->db->loadObjectList();

                    if($pl_image){
                        $query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$pl_image;
                        $this->db->setQuery($query);
                        $def_img = $this->db->loadResult();
                    }else if(isset($photos[0])){
                        $def_img = $photos[0]->filename;
                    }
                }
            }
            //$avatar = JURI::base()."components/com_joomsport/img/ico/season-list-player-ico.gif";

            if(is_file('media/bearleague/'.$def_img)){
                //$avatar = JURI::base()."media/bearleague/".$def_img;
                $avatar = getImgPop($def_img,1);
                $this->_lists["comments"][$j]->avatar = $avatar;
            }

        }
		
		if($this->getVer() >= '1.6'){
			$query = "SELECT IF(m.group_id <> 8,'','1') as gid"
					." FROM  #__users as p, #__user_usergroup_map as m"
					." WHERE m.user_id=p.id AND p.id=".$this->_lists["usera"]->id;
		}else{
			$query = "SELECT IF(p.gid <> 25,'0','1') as gid"
					." FROM #__users as p"
					." WHERE p.id=".$this->_lists["usera"]->id;
		}
		$this->db->setQuery($query);
		$this->_lists["comments_adm"] = $this->db->loadResult();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		$query = "SELECT COUNT(*) FROM #__users as u, #__bl_feadmins as f WHERE f.user_id = u.id AND f.season_id='".$this->s_id."' AND u.id = '".intval($this->_lists["usera"]->id)."'";

		$this->db->setQuery($query);
		if($this->db->loadResult()){
			$this->_lists["comments_adm"] = 1;
		}
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
	}
	function getMatch(){
		if($this->t_single){
			$query = "SELECT m.*,md.m_name,t1.first_name,t1.last_name,t1.nick,t2.first_name as fn2,t2.last_name as ln2,t2.nick as nick2,m.betavailable, IF(CONCAT(m.betfinishdate, ' ', m.betfinishtime)>NOW(),1,0) betfinish,"
					 ." md.s_id,t1.id as hm_id,t2.id as aw_id"
					 ." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_players as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_players as t2 ON m.team2_id = t2.id"
					 ." WHERE m.m_id = md.id AND m.published = 1 AND m.id = ".$this->m_id;
		}else{
			$query = "SELECT m.*,md.m_name,t1.t_name as home, t2.t_name as away,md.s_id,t1.id as hm_id,t2.id as aw_id,t1.t_emblem as emb1,t2.t_emblem as emb2,m.betavailable, IF(CONCAT(m.betfinishdate, ' ', m.betfinishtime)>NOW(),1,0) betfinish"
					." FROM #__bl_matchday as md, #__bl_match as m LEFT JOIN #__bl_teams as t1 ON m.team1_id = t1.id LEFT JOIN #__bl_teams as t2 ON m.team2_id = t2.id"
					." WHERE m.m_id = md.id AND m.published = 1  AND m.id = ".$this->m_id;
		}
		$this->db->setQuery($query);
		$match = $this->db->loadObject();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		if($this->t_single){
		//print_r($match);
			$query = "SELECT p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$match->team2_id;
			$this->db->setQuery($query);
			$img_players2 = $this->db->loadObject();
			
			$query = "SELECT p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$match->team1_id;
			$this->db->setQuery($query);
			$img_players1 = $this->db->loadObject();
			
			(isset($img_players1->filename))?($match->img_home = $img_players1->filename):($match->img_home = '');
			(isset($img_players2->filename))?($match->img_away = $img_players2->filename):($match->img_away = '');

			$match->home = $this->selectPlayerName($match);
			$match->away = $this->selectPlayerName($match,"fn2","ln2","nick2");
		}
		return $match;
	}
	function getMEvents($match){
		$query = "SELECT me.*,ev.*,CONCAT(p.first_name,' ',p.last_name) as p_name,p.first_name,p.last_name,p.nick"
				." FROM #__bl_match_events as me, #__bl_events as ev, #__bl_players as p"
				." WHERE me.player_id = p.id AND ev.player_event = '1' AND me.e_id = ev.id"
				." AND me.match_id = ".$this->m_id." AND ".($this->t_single?"me.player_id=".intval($match->hm_id):"me.t_id=".intval($match->hm_id))
				." ORDER BY me.eordering,CAST(me.minutes AS UNSIGNED)";
		$this->db->setQuery($query);
		$this->_lists["m_events_home"] = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
                /*
                if ($this->s_id == -1) {
                    $query = "SELECT me.*,ev.*,CONCAT(p.first_name,' ',p.last_name) as p_name,p.first_name,p.last_name,p.nick"
				." FROM #__bl_match_events as me, #__bl_events as ev, #__bl_players as p"
				." WHERE me.player_id = p.id AND ev.player_event = '1' AND me.e_id = ev.id"
				." AND me.match_id = ".$this->m_id
				." ORDER BY me.eordering,CAST(me.minutes AS UNSIGNED)";
                    $this->db->setQuery($query);
                    $this->_lists["m_events_home"] = $this->db->loadObjectList();
                
                }                */

		if(count($this->_lists['m_events_home'])){
			for($i=0;$i<count($this->_lists['m_events_home']);$i++){
				$this->_lists['m_events_home'][$i]->p_name = $this->selectPlayerName($this->_lists['m_events_home'][$i]);
			}
		}
		
		$query = "SELECT me.*,ev.*,CONCAT(p.first_name,' ',p.last_name) as p_name,p.first_name,p.last_name,p.nick"
				." FROM #__bl_match_events as me, #__bl_events as ev, #__bl_players as p"
				." WHERE me.player_id = p.id AND ev.player_event = '1' AND me.e_id = ev.id"
				." AND me.match_id = ".$this->m_id." AND ".($this->t_single?"me.player_id=".intval($match->aw_id):"me.t_id=".intval($match->aw_id))
				." ORDER BY me.eordering, CAST(me.minutes AS UNSIGNED)";
		$this->db->setQuery($query);
		
		$this->_lists["m_events_away"] = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		if(count($this->_lists['m_events_away'])){
			for($i=0;$i<count($this->_lists['m_events_away']);$i++){
				$this->_lists['m_events_away'][$i]->p_name = $this->selectPlayerName($this->_lists['m_events_away'][$i]);
			}
		}
		
		$query = "SELECT me.*,ev.*,p.t_name as p_name,p.id FROM #__bl_match_events as me, #__bl_events as ev, #__bl_teams as p"
				." WHERE me.t_id = p.id AND me.t_id = ".intval($match->hm_id)." AND ev.player_event = '0' AND me.e_id = ev.id AND me.match_id = ".$this->m_id
				." ORDER BY ev.ordering,ev.e_name";
		$this->db->setQuery($query);
		$this->_lists["h_events"] = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		
		$query = "SELECT me.*,ev.*,p.t_name as p_name,p.id FROM #__bl_match_events as me, #__bl_events as ev ,#__bl_teams as p"
				." WHERE me.t_id = p.id AND me.t_id = ".intval($match->aw_id)." AND ev.player_event = '0' AND me.e_id = ev.id AND me.match_id = ".$this->m_id
				." ORDER BY ev.ordering,ev.e_name";
		$this->db->setQuery($query);
		$this->_lists["a_events"] = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		
		$query = "SELECT DISTINCT ev.e_name as e_name, ev.* FROM #__bl_match_events as me, #__bl_events as ev ,#__bl_teams as p"
				." WHERE me.t_id = p.id AND me.t_id IN ( ".intval($match->hm_id).", ".intval($match->aw_id).") AND ev.player_event = '0' AND me.e_id = ev.id AND me.match_id = ".$this->m_id
				." ORDER BY ev.ordering,ev.e_name";
		$this->db->setQuery($query);
		$this->_lists["events"] = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		////
		
	}
	
	///betting
	function getMatchBetEvents($idmatch){
        $query = "SELECT bbc.*, bbe.*"
                ."\n FROM #__bl_betting_events bbe"
                ."\n INNER JOIN #__bl_betting_coeffs bbc ON bbc.idevent=bbe.id"
                ."\n WHERE bbc.idmatch =".$idmatch;

        $this->db->setQuery($query);
        $matchevents = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
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