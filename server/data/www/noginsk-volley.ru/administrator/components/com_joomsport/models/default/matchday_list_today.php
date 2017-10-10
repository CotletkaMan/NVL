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

class matchday_list_todayJSModel extends JSPRO_Models
{
	
	var $_data = null;
	var $_lists = null;

	function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();

		
		$this->getData();

	}

	function getData()
	{
		date_default_timezone_set("Europe/Athens");
		$today = getdate();
		$date1= date("o-m-d");


		$query = "SELECT m.*,IF(m.m_single <> 1,t.t_name,CONCAT(pl.first_name,' ',pl.last_name)) as home_team, IF(m.m_single <> 1,t2.t_name,CONCAT(pl2.first_name,' ',pl2.last_name)) as away_team, 
			 IF(m.score1>m.score2,t.t_name,t2.t_name) as winner, 
			 IF(m.score1>m.score2,t.id,t2.id) as winnerid
			 FROM #__bl_matchday as md, #__bl_match as m 
			 LEFT JOIN #__bl_teams as t ON t.id = m.team1_id 
			 LEFT JOIN #__bl_teams as t2 ON t2.id = m.team2_id 
			 LEFT JOIN #__bl_players as pl ON pl.id = m.team1_id 
			 LEFT JOIN #__bl_players as pl2 ON pl2.id = m.team2_id 
			 WHERE m.m_date='".$date1."' AND md.s_id = '-1' AND m.m_id=md.id AND m.m_played = '0'
			 ORDER BY m.id";

			 $this->db->setQuery($query);
			 $data_fr = $this->db->loadObjectList();
			/* $error = $this->db->getErrorMsg();
				if ($error)
				{
					return JError::raiseError(500, $error);
				}*/
			if(isset($data_fr)){
				for($i=0;$i<count($data_fr);$i++){
					$data_fr[$i]->fullname = 'Friendly Match';
				}
			}


		$query = "SELECT m.*,IF(tour.t_single <> 1,t.t_name,CONCAT(pl.first_name,' ',pl.last_name)) as home_team, IF(tour.t_single <> 1,t2.t_name,CONCAT(pl2.first_name,' ',pl2.last_name)) as away_team,
			 IF(m.score1>m.score2,t.t_name,t2.t_name) as winner, 
			 IF(m.score1>m.score2,t.id,t2.id) as winnerid,
			 seas.s_id as seasid, seas.s_name,
			 tour.id as tourid, tour.name as tourname ,
			 concat(tour.name,'-',seas.s_name) as fullname
			 FROM #__bl_matchday as md, #__bl_seasons as seas, #__bl_tournament as tour, #__bl_match as m
			 LEFT JOIN #__bl_teams as t ON t.id = m.team1_id 
			 LEFT JOIN #__bl_teams as t2 ON t2.id = m.team2_id 
			  LEFT JOIN #__bl_players as pl ON pl.id = m.team1_id 
			 LEFT JOIN #__bl_players as pl2 ON pl2.id = m.team2_id 
			 WHERE m.m_date='".$date1."' 
			   AND m.m_id=md.id
			   AND md.s_id=seas.s_id
			   AND seas.t_id=tour.id
			   AND m_played=0
			   ORDER BY  tour.id, seas.s_id, m.id";
					 
		$this->db->setQuery($query);
		$data = $this->db->loadObjectList();
		$this->_data = (isset($data_fr))?(array_merge($data_fr,$data)):($data);

	}

	function saveMdayToday(){

		$mainframe = JFactory::getApplication();;
		$this->db			=& JFactory::getDBO();
		
		$post		= JRequest::get( 'post' );

		// save match
		$mj = 0;
		$prevarr = array();
		
			$arr_match = array();
		
			if(isset($_POST['home_team']) && count($_POST['home_team'])){
		
				foreach($_POST['home_team'] as $home_team){
		
					$match 	= new JTableMatch($this->db);
		
					$match->load($_POST['match_id'][$mj]);
		
					$match->m_id = $row->id;
		
					$match->team1_id = intval($home_team);
		
					$match->team2_id = intval($_POST['away_team'][$mj]);
		
					$match->score1 = intval($_POST['home_score'][$mj]);
		
					$match->score2 = intval($_POST['away_score'][$mj]);
		
					$match->is_extra = isset($_POST['extra_time'][$mj])?intval($_POST['extra_time'][$mj]):0;
					$match->published = 1;
		
					$match->m_played = intval($_POST['match_played'][$mj]);
				$match->m_date = strval($_POST['match_data'][$mj]);
		
					$match->m_time = strval($_POST['match_time'][$mj]);
		   
			if (!$match->check()) {
		
						JError::raiseError(500, $match->getError() );
		
					}
		
					if (!$match->store()) {
		
						JError::raiseError(500, $match->getError() );
		
					}
		
					$match->checkin();
		
					$arr_match[] = $match->id;
		
					$mj++;
		
				}
			}
		
		
		
		
		
		
	}


	
}