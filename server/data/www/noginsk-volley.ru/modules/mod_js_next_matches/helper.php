<?php
/**
Beardev
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class modBlNextHelper
{
	public static function &getList( &$params )
	{
		global $mainframe;

		$db = JFactory::getDBO();
		$rows = array();


		$match_count 	= $params->get( 'match_count' );
		$ssss_id = $params->get( 'sidgid' );
        $match_v = $params->get( 'match_v' );

        $is_venue = $match_v?" AND m.venue_id != 0 ":"";

		if($ssss_id == '0'){
			$s_id=0;
			$gr_id=0;
		}else{
			$ex = explode('|',$ssss_id );
			$s_id = $ex[0];
			$gr_id = $ex[1];
		}
		$t_id = $params->get( 'team_id' );
		
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='player_name'";
		$db->setQuery($query);
		$pln = $db->loadResult();
		
		$gr_sql = '';
		if($gr_id){
			$query = "SELECT t_id FROM  #__bl_grteams WHERE g_id=".$gr_id;
			$db->setQuery($query);
			$grtm = $db->loadColumn();
			if(count($grtm) == 1){
				//$gr_sql = " AND (t1.id = {$grtm[0]} OR t2.id = {$grtm[0]}) ";
			}else if(count($grtm) > 1){
				$gr_sql = " AND (t1.id IN (".implode(',',$grtm).") AND t2.id IN (".implode(',',$grtm).")) ";
			}
		}
		if($s_id){

			$query = "SELECT t.t_single FROM #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id WHERE s.s_id=".$s_id;
			$db->setQuery($query);
			if($db->loadResult()){
				$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='player_name'";
				$db->setQuery($query);
				$pln = $db->loadResult();
				if(!$pln){
					$nname = "CONCAT(t1.first_name,' ',t1.last_name) as home";
					$nname2 = "CONCAT(t2.first_name,' ',t2.last_name) as away";
				}else{
					$nname = "IF(t1.nick<>'',t1.nick,CONCAT(t1.first_name,' ',t1.last_name)) as home";
					$nname2 = "IF(t2.nick<>'',t2.nick,CONCAT(t2.first_name,' ',t2.last_name)) as away";
				}
				
				$query = "SELECT m.id,m.m_date,m.m_time,md.m_name,md.id as mdid, ".$nname.", ".$nname2.", score1,score2,m.is_extra,t1.id as hm_id,t2.id as aw_id,'' as emb1, '' as emb2"
						." FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
						." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_date >= '".date('Y-m-d')."' AND m.m_played = 0 ".($t_id?(" AND (t1.id=".$t_id." OR t2.id=".$t_id.")"):"").$gr_sql."".$is_venue
						." ORDER BY m.m_date,m.m_time,md.id LIMIT ".$match_count;
		
			}else{
			
				$query = "SELECT m.id,m.m_date,m.m_time,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away, score1,score2,m.is_extra,t1.id as hm_id,t2.id as aw_id,t1.t_emblem as emb1, t2.t_emblem as emb2"
						." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2"
						." WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=".$s_id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_date >= '".date('Y-m-d')."' AND m.m_played = 0 ".($t_id?(" AND (t1.id=".$t_id." OR t2.id=".$t_id.")"):"").$gr_sql."".$is_venue
						." ORDER BY m.m_date,m.m_time,md.id LIMIT ".$match_count;
			}
		}else{
				$query = "SELECT DISTINCT(m.id),m.m_date,m.m_time,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away, score1,score2,m.is_extra,t1.id as hm_id,t2.id as aw_id,t1.t_emblem as emb1, t2.t_emblem as emb2"
					." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2, #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id"
					." WHERE md.s_id=s.s_id AND t.t_single='0' AND t.published=1 AND s.published=1 AND m.m_id = md.id AND m.published = 1 AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_date >= '".date('Y-m-d')."' AND m.m_played = 0 ".($t_id?(" AND (t1.id=".$t_id." OR t2.id=".$t_id.")"):"")."".$is_venue
					." ORDER BY m.m_date,m.m_time,md.id LIMIT ".$match_count;
					
				$query_fr = "SELECT DISTINCT(m.id), m.m_date,m.m_time,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away, score1,score2,m.is_extra,t1.id as hm_id,t2.id as aw_id,t1.t_emblem as emb1, t2.t_emblem as emb2
					FROM #__bl_match as m, #__bl_matchday as md, #__bl_teams as t1, #__bl_teams as t2
					WHERE md.s_id = '-1' AND m.m_single='0' AND md.id = m.m_id AND m.m_date >= '".date('Y-m-d')."' AND m.team1_id = t1.id AND m.team2_id = t2.id AND m_single = '0' AND m.published = 1 AND m.m_played = 0 ".($t_id?(" AND (t1.id=".$t_id." OR t2.id=".$t_id.")"):"")."".$is_venue;
	
	
				if(!$t_id){
					$query = "SELECT DISTINCT(m.id),t.t_single"
						." FROM #__bl_matchday as md, #__bl_match as m, #__bl_seasons as s,#__bl_tournament as t"
						." WHERE t.id = s.t_id AND m.m_id = md.id AND md.s_id=s.s_id AND m.published = '1' AND m.m_date >= '".date('Y-m-d')."' AND m.m_played = 0  AND t.published='1' AND s.published='1'".$is_venue
						." ORDER BY m.m_date,m.m_time,md.id LIMIT ".$match_count;
					//$query = "SELECT DISTINCT(m.id),t.t_single"
						//." FROM #__bl_matchday as md,#__bl_match as m, #__bl_seasons as s, #__bl_tournament as t "
						//." WHERE t.id=s.t_id AND t.published=1 AND s.published=1 AND md.s_id=s.s_id "
						//." AND m.m_played = 0 AND m.m_date >= '".date('Y-m-d')."' AND m.published=1 "
						//." ORDER BY m.m_date,m.m_time,md.id LIMIT ".$match_count;
					$query_fr = "SELECT DISTINCT(m.id), m.m_single
								FROM #__bl_matchday as md, #__bl_match as m
								WHERE md.id = m.m_id AND md.s_id = '-1'  AND (m.m_single='0' OR m.m_single='1') AND m.m_date >= '".date('Y-m-d')."' AND m.published = '1'".$is_venue;
					


					
				}
			
				$db->setQuery($query_fr);
				$rows_fr = $db->loadObjectList();
				
		}	
		$db->setQuery($query);

		$rows1 = $db->loadObjectList(); //print_r($rows_fr);
		if(isset($rows_fr)){
			$rows = array_merge($rows1,$rows_fr);
		}else{
			$rows = $rows1;
		}


		if(!$s_id && !$t_id){
			$new_row = array();
			for($i=0;$i<count($rows);$i++){
				$row = $rows[$i];
				if(!empty($row->t_single) || !empty($row->m_single)){
					if(!$pln){
						$nname = "CONCAT(t1.first_name,' ',t1.last_name) as home";
						$nname2 = "CONCAT(t2.first_name,' ',t2.last_name) as away";
					}else{
						$nname = "IF(t1.nick<>'',t1.nick,CONCAT(t1.first_name,' ',t1.last_name)) as home";
						$nname2 = "IF(t2.nick<>'',t2.nick,CONCAT(t2.first_name,' ',t2.last_name)) as away";
					}
					$query = "SELECT 1 as ssingle,m.id,m.m_date,m.m_time,md.m_name,md.id as mdid, ".$nname.", ".$nname2.", score1,score2,m.is_extra,t1.id as hm_id,t2.id as aw_id,'' as emb1, '' as emb2"
						." FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
						." WHERE m.m_id = md.id AND m.published = 1 AND m.id=".$row->id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_played = 0 ";
					
				}else{
					$query = "SELECT 0 as ssingle,m.id,m.m_date,m.m_time,md.m_name,md.id as mdid, t1.t_name as home, t2.t_name as away, score1,score2,m.is_extra,t1.id as hm_id,t2.id as aw_id,t1.t_emblem as emb1, t2.t_emblem as emb2"
						." FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2"
						." WHERE  m.m_id = md.id AND m.published = 1 AND m.id=".$row->id."  AND m.team1_id = t1.id AND m.team2_id = t2.id AND m.m_played = 0 ";

				}
				$db->setQuery($query);

				$new_row = @array_merge($new_row,$db->loadObjectList());
			}
			return $new_row;
		}
		
		return $rows;
	}
	public static function &getStype(&$params){
		$db = JFactory::getDBO();
		$ssss_id = $params->get( 'sidgid' );
		$ex = explode('|',$ssss_id );
		$s_id = $ex[0];
		$query = "SELECT t.t_single FROM #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id WHERE s.s_id=".$s_id;
		$db->setQuery($query);
		$res = $db->loadResult();
		return $res;
	}
	public static function &getPhoto($plid){
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM #__bl_players WHERE id=".$plid;
		$db->setQuery($query);
		$player = $db->loadObject();
		
		$query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$player->id;
		$db->setQuery($query);
		$photos = $db->loadObjectList();
		
		$def_img = '';
		if($player->def_img){
			$query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$player->def_img;
			$db->setQuery($query);
			$def_img = $db->loadResult();
		}else if(isset($photos[0])){
			$def_img = $photos[0]->filename;
		}
		return $def_img;
	}
	///new
	public static function &checkTEAMLength($team_name, $team_max_length){
		$team_name_max_check = 1 ;
    
    while ( $team_name_max_check ) {
     	 $i = 0 ; 
     	 $name_corrected = 0 ;
       $chars = preg_split('/ /', $team_name, -1, PREG_SPLIT_OFFSET_CAPTURE);
         
       while ( (isset($chars[$i][0])) && ( !$name_corrected) ) {
          if ( strlen($chars[$i][0]) > $team_max_length ) {
           	 $team_name = substr($team_name, 0, $team_max_length+$chars[$i][1] ). " " .substr($team_name, $team_max_length+$chars[$i][1]);
           	 $name_corrected = 1 ;
           }
           $i++ ;
         }
         $team_name_max_check = $name_corrected ;
       } 
		return $team_name ;
	}
}
