<?php
/**
* @version		$Id: helper.php 11074 2008-10-13 04:54:12Z ian $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class modJSMODHelper
{
	public static function &getUserMenu(&$params )
	{
		$db = JFactory::getDBO();
		$user	= JFactory::getUser();
		$rows = array();
		$cItemId   = $params->get('customitemid');
		$Itemid    = JRequest::getInt('Itemid');

		if(!$cItemId){
			$cItemId = $Itemid;
		}
		if($user->id){
			$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='player_reg'";
			$db->setQuery($query);
			$player_reg = $db->loadResult();
			//print_r($player_reg);
			$query = "SELECT CONCAT(first_name,' ',last_name) as name,id FROM #__bl_players WHERE usr_id=".$user->id;
			$db->setQuery($query);
			$player = $db->loadObject();
			//print_r($player);
			if(!$player && $player_reg){
				$rows[0]["name"] = JText::_("JSMM_CREATEPROF");
				$rows[0]["link"] = JRoute::_("index.php?option=com_joomsport&task=regplayer&Itemid=".$cItemId);
			}elseif($player){ 
				$rows[0]["name"] = $player->name;
				$rows[0]["link"] = JRoute::_("index.php?option=com_joomsport&task=regplayer&Itemid=".$cItemId);
			}
		}
		return $rows;
		
	}
	public static function &getModerMenu(&$params){
		$db = JFactory::getDBO();
		$user	= JFactory::getUser();
		$rows = array();
		$cItemId   = $params->get('customitemid');
		$Itemid    = JRequest::getInt('Itemid');

		if(!$cItemId){
			$cItemId = $Itemid;
		}
		
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='team_reg'";
		$db->setQuery($query);
		$team_reg = $db->loadResult();
///UPDATE//
		$query = "SELECT CONCAT(first_name,' ',last_name) as name,id FROM #__bl_players WHERE usr_id=".$user->id;
		$db->setQuery($query);
		$player = $db->loadObject();
		
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='player_team_reg'";
		$db->setQuery($query);
		$player_team_reg = $db->loadResult();
		//print_r($team_reg);
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='player_reg'";
		$db->setQuery($query);
		$player_reg = $db->loadResult();
//	
		$query = "SELECT id,t_name FROM #__bl_teams as t, #__bl_moders as m WHERE m.tid=t.id AND m.uid=".$user->id." ORDER BY t_name";
		$db->setQuery($query);
		$m_teams = $db->loadObjectList();
		$i = 0;

		
		for($i=0;$i<count($m_teams);$i++){
			$rows[$i]["name"] = $m_teams[$i]->t_name;
			$rows[$i]["link"] = JRoute::_('index.php?option=com_joomsport&view=edit_team&tid='.$m_teams[$i]->id.'&controller=moder&Itemid='.$cItemId);
		}
		$i++;
		if($team_reg){
			if(!$player && $player_team_reg && !$player_reg){
				
			}else{
				$rows[$i]["name"] = JText::_("JSMM_CREATETEAM");
				$rows[$i]["link"] = JRoute::_("index.php?option=com_joomsport&task=regteam&Itemid=".$cItemId);
			}
			
		}
		return $rows;
		
	}
	public static function &getAdminMenu(&$params){
		$db = JFactory::getDBO();
		$user	= JFactory::getUser();
		$rows = array();
		$cItemId   = $params->get('customitemid');
		$Itemid    = JRequest::getInt('Itemid');

		if(!$cItemId){
			$cItemId = $Itemid;
		}
		
		$query = "SELECT CONCAT(t.name,' ',s.s_name) as name,s.s_id FROM #__users as u, #__bl_feadmins as f, #__bl_seasons as s, #__bl_tournament as t WHERE f.user_id = u.id AND s.s_id = f.season_id AND s.t_id = t.id AND u.id = ".intval($user->id)." ORDER BY s.ordering";
		$db->setQuery($query);
		
		$sidsss = $db->loadObjectList();
		$i = 0;
		for($i=0;$i<count($sidsss);$i++){
			$rows[$i]["name"] = $sidsss[$i]->name;
			$rows[$i]["link"] = JRoute::_('index.php?option=com_joomsport&controller=admin&view=admin_matchday&sid='.$sidsss[$i]->s_id.'&Itemid='.$cItemId);
		}
		
		return $rows;
	}
	
}
