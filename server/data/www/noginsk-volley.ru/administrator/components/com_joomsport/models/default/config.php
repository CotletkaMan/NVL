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
require_once(JPATH_ROOT.'/components/com_joomsport/includes/utils.php');

class configJSModel extends JSPRO_Models
{
	
	var $_data = null;
	var $_lists = null;
	var $_mode = 1;
	var $_id = null;
	
	function __construct()
	{
		parent::__construct();
	
		$this->getData();
	}
	
	
	function getData()
	{
		
		$this->_lists['date_format'] = $this->getJS_Config('date_format');
		
		$is_data = array();
		
		$is_data[] = JHTML::_('select.option', "%d-%m-%Y %H:%M", "d-m-Y H:M", 'id', 'name' );
		$is_data[] = JHTML::_('select.option', "%d.%m.%Y %H:%M", "d.m.Y H:M", 'id', 'name' );
		 $is_data[] = JHTML::_('select.option', "%m-%d-%Y %I:%M %p", "m-d-Y I:M p", 'id', 'name' ); 
		$is_data[] = JHTML::_('select.option', "%m %B, %Y %H:%M", "m B, Y H:M", 'id', 'name' ); 
		$is_data[] = JHTML::_('select.option', "%m %B, %Y %I:%H %p", "m B, Y I:H p", 'id', 'name' ); 
		$is_data[] = JHTML::_('select.option', "%d-%m-%Y", "d-m-Y", 'id', 'name' ); 
		$is_data[] = JHTML::_('select.option', "%A %d %B, %Y  %H:%M", "A d B, Y  H:M", 'id', 'name' ); 
		$this->_lists['data_sel'] = JHTML::_('select.genericlist',   $is_data, 'date_format', 'class="inputbox" size="1"', 'id', 'name', $this->_lists['date_format'] );

		$this->_lists['yteam_color'] = $this->getJS_Config('yteam_color');
		
		$query = "SELECT * FROM #__bl_extra_filds WHERE type='0' AND season_related='0' ORDER BY ordering";
		$this->db->setQuery($query);
		$this->_lists['adf_player'] = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		
		$query = "SELECT * FROM #__bl_extra_filds WHERE type='1' AND season_related='0' ORDER BY ordering";
		$this->db->setQuery($query);
		$this->_lists['adf_team'] = $this->db->loadObjectList();
		
		//Player Country registration
		$this->_lists['country_reg'] = $this->getJS_Config('country_reg');
		$this->_lists['country_reg_rq'] = $this->getJS_Config('country_reg_rq');
		//Nick registration
		$this->_lists['nick_reg'] = $this->getJS_Config('nick_reg');
		$this->_lists['nick_reg_rq'] = $this->getJS_Config('nick_reg_rq');
		//Match comments
		$this->_lists['mcomments'] = $this->getJS_Config('mcomments');
		//Player registration
		$this->_lists['player_reg'] = $this->getJS_Config('player_reg');
		//team registration
		$this->_lists['team_reg'] = $this->getJS_Config('team_reg');
		
		//
		$this->_lists['moder_addplayer'] = $this->getJS_Config('moder_addplayer');
		$pllist_order = $this->getJS_Config('pllist_order');
		$pllist_order_se = $this->getJS_Config('pllist_order_se');//SELECT		
		
		$query = "SELECT name, CONCAT(id,'_1') as id FROM #__bl_extra_filds WHERE type='0' AND (field_type = 0 OR field_type = 3) ORDER BY ordering";
		$this->db->setQuery($query);
		$adf = $this->db->loadObjectList();
		$alltmp[] = JHTML::_('select.option',0,JTEXT::_('Name'),'id','name');
		/*UPDATE*/
		$alltmp1 = array();		
		if(count($adf)){
			$alltmp1 = array_merge($alltmp1,$adf);
		}
		
		$query = "SELECT CONCAT(ev.id,'_2') as id,ev.e_name as name
		            FROM #__bl_events as ev WHERE ev.player_event IN (1, 2)
		            ORDER BY ev.e_name";
		$this->db->setQuery($query);
		$events_cd = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		
		if($events_cd){
			$alltmp1 = array_merge($alltmp1,$events_cd);
		}
		////my sort-------------------->
		function mySort($f1,$f2){
			return strcasecmp($f1->name, $f2->name);
		}
		usort($alltmp1,'mySort');

		$alltmp = array_merge($alltmp,$alltmp1);
//NEW SELECT
	
	
		$query = "SELECT name, CONCAT(id,'_1') as id FROM #__bl_extra_filds
		            WHERE type='0' AND field_type = 3
		            ORDER BY ordering";
		$this->db->setQuery($query);
		$adf_se = $this->db->loadObjectList();
		
		$alltmpse[] = JHTML::_('select.option',0,JTEXT::_('Name'),'id','name');	
		
		$alltmp_se = array();		
		if(count($adf_se)){
			$alltmp_se = array_merge($alltmp_se,$adf_se);
		}
		$alltmp_se = array_merge($alltmpse,$alltmp_se);

/////////	
		
		$this->_lists['pllist_order'] = JHTML::_('select.genericlist',   $alltmp, 'pllist_order', 'class="inputbox" size="1"', 'id', 'name', $pllist_order );

//SELECT!!!!!!!
		$this->_lists['pllist_order_se'] = JHTML::_('select.genericlist',   $alltmp_se, 'pllist_order_se', 'class="inputbox" size="1"', 'id', 'name', $pllist_order_se );
		//print_r($alltmp_se);
//////////////		
		//width logo
		$this->_lists['teamlogo_height'] = $this->getJS_Config('teamlogo_height');
		
		//account limits
		$this->_lists['teams_per_account'] = $this->getJS_Config('teams_per_account');
		$this->_lists['players_per_account'] = $this->getJS_Config('players_per_account');
		
		//venue
		$this->_lists['unbl_venue'] = $this->getJS_Config('unbl_venue');
		$this->_lists['cal_venue'] = $this->getJS_Config('cal_venue');
		
		//played matches
		$this->_lists['played_matches'] = $this->getJS_Config('played_matches');
		//display name - nick
		$player_name = $this->getJS_Config('player_name');
		
		$is_data = array();
		
		$is_data[] = JHTML::_('select.option', "0", JText::_("BLBE_LANGVIEWSP_FN"), 'id', 'name' ); 
		$is_data[] = JHTML::_('select.option', "1", JText::_("BLBE_NICKNAME"), 'id', 'name' ); 

		$this->_lists['player_name'] = JHTML::_('select.genericlist',   $is_data, 'player_name', 'class="inputbox" size="1"', 'id', 'name', $player_name );
		///esport invites
		$esport_invite_player = $this->getJS_Config('esport_invite_player');
		
		$is_data = array();
		
		$is_data[] = JHTML::_('select.option', "0", JText::_("BLBE_MODERADDPL"), 'id', 'name' ); 
		$is_data[] = JHTML::_('select.option', "1", JText::_("BLBE_MODERINVITEPL"), 'id', 'name' ); 

		$this->_lists['esport_invite_player'] = JHTML::_('select.genericlist',   $is_data, 'esport_invite_player', 'class="inputbox" size="1" style="width:250px;"', 'id', 'name', $esport_invite_player );
		//invite confirm
		$this->_lists["esport_invite_confirm"] = $this->getJS_Config('esport_invite_confirm');
		//invite unregistered
		$this->_lists["esport_invite_unregister"] = $this->getJS_Config('esport_invite_unregister');
		//
		$this->_lists["esport_join_team"] = $this->getJS_Config('esport_join_team');
		//invite to match
		$this->_lists["esport_invite_match"] = $this->getJS_Config('esport_invite_match');
		///admin rights
		$this->_lists["jssa_editplayer"] = $this->getJS_Config('jssa_editplayer');
		
		$this->_lists["jssa_editplayer_single"] = $this->getJS_Config('jssa_editplayer_single');
		
		$this->_lists["jssa_deleteplayers"] = $this->getJS_Config('jssa_deleteplayers');
		
		$this->_lists["jssa_deleteplayers_single"] = $this->getJS_Config('jssa_deleteplayers_single');
		
		$this->_lists["jssa_editteam"] = $this->getJS_Config('jssa_editteam');
		$this->_lists["jssa_delteam"] = $this->getJS_Config('jssa_delteam');
		
		$knock_style = $this->getJS_Config('knock_style');
		$is_data_v[] = JHTML::_('select.option', "0", JText::_("BLBE_VIEWHOR"), 'id', 'name' ); 
		$is_data_v[] = JHTML::_('select.option', "1", JText::_("BLBE_VIEWVER"), 'id', 'name' ); 

		$this->_lists['knock_style'] = JHTML::_('select.genericlist',   $is_data_v, 'knock_style', 'class="inputbox" size="1"', 'id', 'name', $knock_style );
		
		
		
		//social buttons
		$this->_lists['jsb_twitter'] = $this->getJS_Config('jsb_twitter');
		$this->_lists['jsb_gplus'] = $this->getJS_Config('jsb_gplus');
		$this->_lists['jsb_fbshare'] = $this->getJS_Config('jsb_fbshare');
		$this->_lists['jsb_fblike'] = $this->getJS_Config('jsb_fblike');
		$this->_lists['jsbp_season'] = $this->getJS_Config('jsbp_season');
		$this->_lists['jsbp_team'] = $this->getJS_Config('jsbp_team');
		$this->_lists['jsbp_player'] = $this->getJS_Config('jsbp_player');
		$this->_lists['jsbp_match'] = $this->getJS_Config('jsbp_match');
		$this->_lists['jsbp_venue'] = $this->getJS_Config('jsbp_venue');
		
		//add existing team for season admin
		$this->_lists['jssa_addexteam'] = $this->getJS_Config('jssa_addexteam');
/*UPDATE*/
		$this->_lists['jssa_addexteam_single'] = $this->getJS_Config('jssa_addexteam_single');
		
		$this->_lists['player_team_reg'] = $this->getJS_Config('player_team_reg');
		
		$this->_lists['jsmr_mark_played'] = $this->getJS_Config('jsmr_mark_played');
		$this->_lists['jsmr_editresult_yours'] = $this->getJS_Config('jsmr_editresult_yours');
		$this->_lists['jsmr_editresult_opposite'] = $this->getJS_Config('jsmr_editresult_opposite');
		$this->_lists['jsmr_edit_playerevent_yours'] = $this->getJS_Config('jsmr_edit_playerevent_yours');
		$this->_lists['jsmr_edit_playerevent_opposite'] = $this->getJS_Config('jsmr_edit_playerevent_opposite');
		$this->_lists['jsmr_edit_matchevent_yours'] = $this->getJS_Config('jsmr_edit_matchevent_yours');
		$this->_lists['jsmr_edit_matchevent_opposite'] = $this->getJS_Config('jsmr_edit_matchevent_opposite');
		$this->_lists['jsmr_edit_squad_yours'] = $this->getJS_Config('jsmr_edit_squad_yours');
		$this->_lists['jsmr_edit_squad_opposite'] = $this->getJS_Config('jsmr_edit_squad_opposite');

		
		//autoreg
		$this->_lists['autoreg_player'] = JHTML::_('select.booleanlist',  'autoreg_player', 'class="inputbox"', $this->getJS_Config('autoreg_player') );
		$this->_lists['reg_lastname'] = $this->getJS_Config('reg_lastname');
		$this->_lists['reg_lastname_rq'] = $this->getJS_Config('reg_lastname_rq');
		
		//brand
		$this->_lists['jsbrand_on'] = $this->getJS_Config('jsbrand_on');
		$this->_lists['jsbrand_epanel_image'] = $this->getJS_Config('jsbrand_epanel_image');
		
		// Custom fields: team city, etc.
		$customFields = JS_Utils::getCustomFields();
		$this->_lists['cf_team_city'] = count($customFields)
			? $customFields['team_city']
			: array('enabled' => false, 'required' => false);
	}

	public function saveConfig(){

		$date_format = JRequest::getVar( 'date_format', '', 'post', 'string' );
		$yteam_color = JRequest::getVar( 'yteam_color', '', 'post', 'string' );
		$nick_reg = JRequest::getVar( 'nick_reg', 0, 'post', 'int' );
		$nick_reg_rq = JRequest::getVar( 'nick_reg_rq', 0, 'post', 'int' );
		$country_reg = JRequest::getVar( 'country_reg', 0, 'post', 'int' );
		$country_reg_rq = JRequest::getVar( 'country_reg_rq', 0, 'post', 'int' );
		$mcomments = JRequest::getVar( 'mcomments', 0, 'post', 'int' );
		$player_reg = JRequest::getVar( 'player_reg', 0, 'post', 'int' );
		$team_reg = JRequest::getVar( 'team_reg', 0, 'post', 'int' );
		$moder_addplayer = JRequest::getVar( 'moder_addplayer', 0, 'post', 'int' );
		$pllist_order = JRequest::getVar( 'pllist_order', 0, 'post', 'string' );
		$pllist_order_se = JRequest::getVar( 'pllist_order_se', 0, 'post', 'string' );//SELECT
		$teamlogo_height = JRequest::getVar( 'teamlogo_height', 0, 'post', 'int' );
		$teams_per_account = JRequest::getVar( 'teams_per_account', 0, 'post', 'int' );
		$players_per_account = JRequest::getVar( 'players_per_account', 0, 'post', 'int' );
		$unbl_venue = JRequest::getVar( 'unbl_venue', 0, 'post', 'int' );
		$cal_venue = JRequest::getVar( 'cal_venue', 0, 'post', 'int' );
		$played_matches = JRequest::getVar( 'played_matches', 0, 'post', 'int' );
		$player_name = JRequest::getVar( 'player_name', 0, 'post', 'int' );
		$esport_invite_player = JRequest::getVar( 'esport_invite_player', 0, 'post', 'int' );
		$esport_invite_confirm = JRequest::getVar( 'esport_invite_confirm', 0, 'post', 'int' );
		$esport_invite_unregister = JRequest::getVar( 'esport_invite_unregister', 0, 'post', 'int' );
		$esport_join_team = JRequest::getVar( 'esport_join_team', 0, 'post', 'int' );
		$jssa_editplayer = JRequest::getVar( 'jssa_editplayer', 0, 'post', 'int' );
		$jssa_editplayer_single = JRequest::getVar( 'jssa_editplayer_single', 0, 'post', 'int' );
		$jssa_deleteplayers = JRequest::getVar( 'jssa_deleteplayers', 0, 'post', 'int' );
		$jssa_deleteplayers_single = JRequest::getVar( 'jssa_deleteplayers_single', 0, 'post', 'int' );
		$esport_invite_match = JRequest::getVar( 'esport_invite_match', 0, 'post', 'int' );
		$knock_style = JRequest::getVar( 'knock_style', 0, 'post', 'int' );
		
		$jsb_twitter = JRequest::getVar( 'jsb_twitter', 0, 'post', 'int' );
		$jsb_gplus = JRequest::getVar( 'jsb_gplus', 0, 'post', 'int' );
		$jsb_fbshare = JRequest::getVar( 'jsb_fbshare', 0, 'post', 'int' );
		$jsb_fblike = JRequest::getVar( 'jsb_fblike', 0, 'post', 'int' );
		$jsbp_season = JRequest::getVar( 'jsbp_season', 0, 'post', 'int' );
		$jsbp_team = JRequest::getVar( 'jsbp_team', 0, 'post', 'int' );
		$jsbp_player = JRequest::getVar( 'jsbp_player', 0, 'post', 'int' );
		$jsbp_match = JRequest::getVar( 'jsbp_match', 0, 'post', 'int' );
		$jsbp_venue = JRequest::getVar( 'jsbp_venue', 0, 'post', 'int' );
		
		$jssa_editteam = JRequest::getVar( 'jssa_editteam', 0, 'post', 'int' );
		$jssa_delteam = JRequest::getVar( 'jssa_delteam', 0, 'post', 'int' );
		
		$jssa_addexteam = JRequest::getVar( 'jssa_addexteam', 0, 'post', 'int' );
/*UPDATE*/$jssa_addexteam_single = JRequest::getVar( 'jssa_addexteam_single', 0, 'post', 'int' );
		$player_team_reg = JRequest::getVar( 'player_team_reg', 0, 'post', 'int' );
		
		$autoreg_player = JRequest::getVar( 'autoreg_player', 0, 'post', 'int' );
		$reg_lastname = JRequest::getVar( 'reg_lastname', 0, 'post', 'int' );
		$reg_lastname_rq = JRequest::getVar( 'reg_lastname_rq', 0, 'post', 'int' );
		
		$jsbrand_on = JRequest::getVar( 'jsbrand_on', 0, 'post', 'int' );
		//$jsbrand_epanel_image = JRequest::getVar( 't_logo', '', 'post', 'string' );
		$istlogo = JRequest::getVar( 'istlogo', 0, 'post', 'int' );
		
		$jsmr_mark_played = JRequest::getVar( 'jsmr_mark_played', 0, 'post', 'int' );
		$jsmr_editresult_yours = JRequest::getVar( 'jsmr_editresult_yours', 0, 'post', 'int' );
		$jsmr_editresult_opposite = JRequest::getVar( 'jsmr_editresult_opposite', 0, 'post', 'int' );
		$jsmr_edit_playerevent_yours = JRequest::getVar( 'jsmr_edit_playerevent_yours', 0, 'post', 'int' );
		$jsmr_edit_playerevent_opposite = JRequest::getVar( 'jsmr_edit_playerevent_opposite', 0, 'post', 'int' );
		$jsmr_edit_matchevent_yours = JRequest::getVar( 'jsmr_edit_matchevent_yours', 0, 'post', 'int' );
		$jsmr_edit_matchevent_opposite = JRequest::getVar( 'jsmr_edit_matchevent_opposite', 0, 'post', 'int' );
		$jsmr_edit_squad_yours = JRequest::getVar( 'jsmr_edit_squad_yours', 0, 'post', 'int' );
		$jsmr_edit_squad_opposite = JRequest::getVar( 'jsmr_edit_squad_opposite', 0, 'post', 'int' );
		
		
		$query = "UPDATE #__bl_config SET cfg_value='".$jsmr_mark_played."' WHERE cfg_name='jsmr_mark_played'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsmr_editresult_yours."' WHERE cfg_name='jsmr_editresult_yours'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsmr_editresult_opposite."' WHERE cfg_name='jsmr_editresult_opposite'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsmr_edit_playerevent_yours."' WHERE cfg_name='jsmr_edit_playerevent_yours'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsmr_edit_playerevent_opposite."' WHERE cfg_name='jsmr_edit_playerevent_opposite'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsmr_edit_matchevent_yours."' WHERE cfg_name='jsmr_edit_matchevent_yours'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsmr_edit_matchevent_opposite."' WHERE cfg_name='jsmr_edit_matchevent_opposite'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsmr_edit_squad_yours."' WHERE cfg_name='jsmr_edit_squad_yours'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsmr_edit_squad_opposite."' WHERE cfg_name='jsmr_edit_squad_opposite'";
		$this->db->setquery($query);
		$this->db->query();


		if(!$istlogo){
			$jsbrand_epanel_image = '';
			$query = "UPDATE #__bl_config SET cfg_value='".$jsbrand_epanel_image."' WHERE cfg_name='jsbrand_epanel_image'";
			$this->db->setquery($query);
			$this->db->query();
		}
		if(isset($_FILES['t_logo']['name']) && $_FILES['t_logo']['tmp_name'] != '' && isset($_FILES['t_logo']['tmp_name'])){

			$ext = pathinfo($_FILES['t_logo']['name']);
			$bl_filename = "bl".time().rand(0,3000).'.'.$ext['extension'];
			$bl_filename = str_replace(" ","",$bl_filename);
			//echo $bl_filename;
			 if($this->uploadFile($_FILES['t_logo']['tmp_name'], $bl_filename)){
				$jsbrand_epanel_image = '/media/bearleague/'.$bl_filename;
				$query = "UPDATE #__bl_config SET cfg_value='".$jsbrand_epanel_image."' WHERE cfg_name='jsbrand_epanel_image'";
				$this->db->setquery($query);
				$this->db->query();
			 }
		}
		
		
		
		$query = "UPDATE #__bl_config SET cfg_value='".$jsbrand_on."' WHERE cfg_name='jsbrand_on'";
		$this->db->setquery($query);
		$this->db->query();
		
		$query = "UPDATE #__bl_config SET cfg_value='".$jssa_editteam."' WHERE cfg_name='jssa_editteam'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jssa_delteam."' WHERE cfg_name='jssa_delteam'";
		$this->db->setquery($query);
		$this->db->query();
		
		$query = "UPDATE #__bl_config SET cfg_value='".$date_format."' WHERE cfg_name='date_format'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$yteam_color."' WHERE cfg_name='yteam_color'";
		$this->db->setquery($query);
		$this->db->query();
		
		$query = "UPDATE #__bl_config SET cfg_value='".$nick_reg."' WHERE cfg_name='nick_reg'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$nick_reg_rq."' WHERE cfg_name='nick_reg_rq'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$country_reg."' WHERE cfg_name='country_reg'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$country_reg_rq."' WHERE cfg_name='country_reg_rq'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$mcomments."' WHERE cfg_name='mcomments'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$player_reg."' WHERE cfg_name='player_reg'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$team_reg."' WHERE cfg_name='team_reg'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$moder_addplayer."' WHERE cfg_name='moder_addplayer'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$pllist_order."' WHERE cfg_name='pllist_order'";
		$this->db->setquery($query);
		$this->db->query();
//select
		$query = "UPDATE #__bl_config SET cfg_value='".$pllist_order_se."' WHERE cfg_name='pllist_order_se'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$teamlogo_height."' WHERE cfg_name='teamlogo_height'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$teams_per_account."' WHERE cfg_name='teams_per_account'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$players_per_account."' WHERE cfg_name='players_per_account'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$unbl_venue."' WHERE cfg_name='unbl_venue'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$cal_venue."' WHERE cfg_name='cal_venue'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$played_matches."' WHERE cfg_name='played_matches'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$player_name."' WHERE cfg_name='player_name'";
		$this->db->setquery($query);
		$this->db->query();
		//esport invite
		$query = "UPDATE #__bl_config SET cfg_value='".$esport_invite_player."' WHERE cfg_name='esport_invite_player'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$esport_invite_confirm."' WHERE cfg_name='esport_invite_confirm'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$esport_invite_unregister."' WHERE cfg_name='esport_invite_unregister'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$esport_join_team."' WHERE cfg_name='esport_join_team'";
		$this->db->setquery($query);
		$this->db->query();
		///admin rights
		$query = "UPDATE #__bl_config SET cfg_value='".$jssa_editplayer."' WHERE cfg_name='jssa_editplayer'";
		$this->db->setquery($query);
		$this->db->query();
		
		$query = "UPDATE #__bl_config SET cfg_value='".$jssa_editplayer_single."' WHERE cfg_name='jssa_editplayer_single'";
		$this->db->setquery($query);
		$this->db->query();
		
		$query = "UPDATE #__bl_config SET cfg_value='".$jssa_deleteplayers."' WHERE cfg_name='jssa_deleteplayers'";
		$this->db->setquery($query);
		$this->db->query();
		
		$query = "UPDATE #__bl_config SET cfg_value='".$jssa_deleteplayers_single."' WHERE cfg_name='jssa_deleteplayers_single'";
		$this->db->setquery($query);
		$this->db->query();
		
		//invite to match
		$query = "UPDATE #__bl_config SET cfg_value='".$esport_invite_match."' WHERE cfg_name='esport_invite_match'";
		$this->db->setquery($query);
		$this->db->query();
		
		//knock_style
		$query = "UPDATE #__bl_config SET cfg_value='".$knock_style."' WHERE cfg_name='knock_style'";
		$this->db->setquery($query);
		$this->db->query();
		
		//social buttons
		$query = "UPDATE #__bl_config SET cfg_value='".$jsb_twitter."' WHERE cfg_name='jsb_twitter'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsb_gplus."' WHERE cfg_name='jsb_gplus'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsb_fbshare."' WHERE cfg_name='jsb_fbshare'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsb_fblike."' WHERE cfg_name='jsb_fblike'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsbp_season."' WHERE cfg_name='jsbp_season'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsbp_team."' WHERE cfg_name='jsbp_team'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsbp_player."' WHERE cfg_name='jsbp_player'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsbp_match."' WHERE cfg_name='jsbp_match'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$jsbp_venue."' WHERE cfg_name='jsbp_venue'";
		$this->db->setquery($query);
		$this->db->query();
		
		$query = "UPDATE #__bl_config SET cfg_value='".$jssa_addexteam."' WHERE cfg_name='jssa_addexteam'";
		$this->db->setquery($query);
		$this->db->query();
/*UPDATE*/	
		$query = "UPDATE #__bl_config SET cfg_value='".$jssa_addexteam_single."' WHERE cfg_name='jssa_addexteam_single'";
		$this->db->setquery($query);
		$this->db->query();
		
		$query = "UPDATE #__bl_config SET cfg_value='".$player_team_reg."' WHERE cfg_name='player_team_reg'";
		$this->db->setquery($query);
		$this->db->query();
		
		
		//autoreg
		$query = "UPDATE #__bl_config SET cfg_value='".$autoreg_player."' WHERE cfg_name='autoreg_player'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$reg_lastname."' WHERE cfg_name='reg_lastname'";
		$this->db->setquery($query);
		$this->db->query();
		$query = "UPDATE #__bl_config SET cfg_value='".$reg_lastname_rq."' WHERE cfg_name='reg_lastname_rq'";
		$this->db->setquery($query);
		$this->db->query();
		
		
		$adf_pl 		= JRequest::getVar( 'adf_pl', array(0), '', 'array' );
		JArrayHelper::toInteger($adf_pl, array(0));
		if(count($adf_pl)){
			$counter = 0;
			foreach($adf_pl as $map){
				$query = "UPDATE #__bl_extra_filds SET reg_exist='".((isset($_POST['adfpl_reg_'.$map]) && $_POST['adfpl_reg_'.$map] == 1)?1:0)."',reg_require='".((isset($_POST['adfpl_rq_'.$map]) && $_POST['adfpl_rq_'.$map] == 1)?1:0)."' WHERE id=".$map;
				$this->db->setQuery($query);
				$this->db->query();
				$counter++;
			}
		}
		
		$adf_pl 		= JRequest::getVar( 'adf_tm', array(0), '', 'array' );
		JArrayHelper::toInteger($adf_pl, array(0));
		if(count($adf_pl)){
			$counter = 0;
			foreach($adf_pl as $map){
				$query = "UPDATE #__bl_extra_filds SET reg_exist='".((isset($_POST['adf_reg_'.$map]) && $_POST['adf_reg_'.$map] == 1)?1:0)."',reg_require='".((isset($_POST['adf_rq_'.$map]) && $_POST['adf_rq_'.$map] == 1)?1:0)."' WHERE id=".$map;
				$this->db->setQuery($query);
				$this->db->query();
				$counter++;
			}
		}
		
		// Custom fields: team city, etc.
		$customFields = JS_Utils::getCustomFields();
	    $customFields['team_city']['enabled'] = (bool)JRequest::getVar( 'cf_team_city_enabled', 0, 'post', 'int' );
	    $customFields['team_city']['required'] = (bool)JRequest::getVar( 'cf_team_city_required', 0, 'post', 'int' );
		
		$query = "UPDATE #__bl_config
	        SET cfg_value='" . serialize($customFields) . "'
            WHERE cfg_name='custom_fields'";
		
		$this->db->setquery($query);
		$this->db->query();
		JS_Utils::invalidateFieldsCache();
	}
}