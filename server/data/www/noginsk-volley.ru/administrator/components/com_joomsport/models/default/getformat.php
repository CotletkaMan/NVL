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

class getformatJSModel extends JSPRO_Models
{
	
	var $_data = null;

	function __construct()
	{
		parent::__construct();
	
		$this->getData();
	}

	function getData()
	{
		$fid = JRequest::getVar( 'fr_id', 0, 'GET', 'int' );
		$t_single = JRequest::getVar( 't_single', 0, 'GET', 'int' );
		$s_id = JRequest::getVar( 'sid', 0, 'GET', 'int' );
		
		if($t_single){
			$query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.first_name";
		}else{
			$query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.t_name";
		}
		$this->db->setQuery($query);
		$team = $this->db->loadObjectList();
		$is_team[] = JHTML::_('select.option',  0, ($t_single?JText::_('BLBE_SELPLAYER'):JText::_('BLBE_SELTEAM')), 'id', 't_name' ); 
		$is_team[] = JHTML::_('select.option',  -1, JText::_('BLBE_BYE'), 'id', 't_name' ); 
		$teamis = array_merge($is_team,$team);
		$lists['teams_kn'] = JHTML::_('select.genericlist',   $teamis, 'teams_kn[]', 'class="inputbox" size="1"', 'id', 't_name', 0 );
		$lists['teams_kn_aw'] = JHTML::_('select.genericlist',   $teamis, 'teams_kn_aw[]', 'class="inputbox" size="1"', 'id', 't_name', 0 );
		
		$cfg = $this->get_kn_cfg();
		
		$wdth = $cfg->wdth;
		$height = $cfg->height;
		$step = $cfg->step; 
		$top_next = $cfg->top_next;
		$zz = 2;
		
		$p=0;
		
		echo '<div style="height:'.(($fid/2)*($height+$step)+60).'px;position:relative;overflow-x:auto;overflow-y:hidden;border:1px solid #777;">';
		$played_tt = '<span class="editlinktip hasTip" style="margin-right:15px;" title="'.JText::_( 'BLBE_PLAYED' ).'::'.JText::_( 'BLBE_TT_PLAYED' ).'"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>';
			
		while(floor($fid/$zz) >= 1){
			
			for($i=0;$i<floor($fid/$zz);$i++){
				echo '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
				if($p==0){
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next - 24).'px; left:'.(20 + ($p)*$wdth).'px;">';
					echo $lists['teams_kn'];
					echo '</div>';
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next + 4).'px; left:'.(20 + ($p)*$wdth).'px;">';
					echo $lists['teams_kn_aw'];
					echo '</div>';
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 3).'px; left:'.(20 + ($p)*$wdth).'px;">';
					echo '<input type="text" name="res_kn_1[]" value="" size="5" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
					echo '</div>';
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 23).'px; left:'.(20 + ($p)*$wdth).'px;">';
					echo '<input type="text" name="res_kn_1_aw[]" value="" size="5" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
					echo '</div>';
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10).'px; left:'.(-5 + ($p+1)*$wdth - 50).'px;"><input type="hidden" name="match_id[]" value=""><input type="checkbox" name="kn_match_played_'.$i.'" value="1" />'.$played_tt.'&nbsp;</div>';
						
				}else{
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 5).'px; left:'.(60 + ($p)*$wdth).'px;">';
					echo '<input type="text" name="res_kn_'.($p+1).'[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
					echo '</div>';
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 25).'px; left:'.(60 + ($p)*$wdth).'px;">';
					echo '<input type="text" name="res_kn_'.($p+1).'_aw[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
					echo '</div>';
				}
			}
			$top_next += $height/2;
			$height = $height + $step;
			$step = $height;
			$zz *= 2;
			$p++;
			
		}
		
		if($fid){
			echo '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.( $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
		}	
		echo '</div>';
		
		
	}

	
	
}