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

class getformatknJSModel extends JSPRO_Models
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
		/////new_double_knock
        $zzz = 4;

		$p=0;
        //$z = 0;

		echo '<div style="height:'.(($fid/2)*($height+$step+50)+360).'px;position:relative;overflow-x:auto;overflow-y:hidden;border:1px solid #777;">';
		$played_tt = '<span class="editlinktip hasTip" style="margin-right:15px;" title="'.JText::_( 'BLBE_PLAYED' ).'::'.JText::_( 'BLBE_TT_PLAYED' ).'"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>';
			
		while(floor($fid/$zz) >= 1){
			
			for($i=0;$i<floor($fid/$zz);$i++){
				echo '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
				if($p==0){
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next - 20).'px; left:'.(20 + ($p)*$wdth).'px;">';
					echo $lists['teams_kn'];
					echo '</div>';
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next + 5).'px; left:'.(20 + ($p)*$wdth).'px;">';
					echo $lists['teams_kn_aw'];
					echo '</div>';
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 5).'px; left:'.(20 + ($p)*$wdth).'px;">';
					echo '<input type="text" name="res_kn_1[]" value="" size="5" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
					echo '</div>';
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20).'px; left:'.(20 + ($p)*$wdth).'px;">';
					echo '<input type="text" name="res_kn_1_aw[]" value="" size="5" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
					echo '</div>';
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10).'px; left:'.(-5 + ($p+1)*$wdth - 50).'px;"><input type="hidden" name="match_id[]" value=""><input type="checkbox" name="kn_match_played_'.$i.'" value="1" />'.$played_tt.'&nbsp;</div>';

				}else{
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 5).'px; left:'.(60 + ($p)*$wdth).'px;">';
					echo '<input type="text" name="res_kn_'.($p+1).'[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
					echo '</div>';
					echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20).'px; left:'.(60 + ($p)*$wdth).'px;">';
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

            $w_st = $p!=2?200*($p-2):0;
            $h_st = 60*($height/240);
            //echo ($p-1)."<br>".$h_st;
			echo '<div style="position:absolute;width:'.($wdth+30+$w_st).'px;height:'.($height-$h_st).'px; border-top:1px solid #aaa;border-right:1px solid #aaa; top:'.( $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
            echo '<div style="position:absolute; top:'.($top_next+5).'px; left:'.(90 + ($p)*$wdth+$w_st).'px;">';
            echo '<input type="text" name="res_kn_'.($p+1).'[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
            echo '</div>';
        }


       // echo '</div>';

        //////////////////////////////////////////////////////////////////////aaa
        ///////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////
        $cfg = $this->get_kn_cfg();

        $wdth = $cfg->wdth;
        $height = $cfg->height;
        $step = $cfg->step;
        $top_next = $cfg->top_next;
        $zz = 4;


        $p=0;
        $top = 0;
        $p_step = 0;
        $top_step = 60;
        $all_step = 120*($fid/2);
        $step_block = ($fid/$zz>2)?200:0;

        //echo '<div style="height:'.(($fid/2)*($height+$step)+60).'px;position:relative;overflow-x:auto;overflow-y:hidden;border:1px solid #777;">';
        while(floor($fid/$zz) >= 1){

            $p_step =($p>=2)? $p_step+1:$p_step+0;
            if($p==3||$p==4){
                $top =($p==3)? $top+60:$top+210;
            }
            $top_step =($p==0||$p==1)? $top_step + 0: $top_step+60;
            for($i=0;$i<floor($fid/$zz);$i++){

                if($p==0){

                    echo '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) +$top_next+$all_step).'px; left:'.(20 + ($p)*$wdth).'px; "></div>';

                    echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next +5+$all_step).'px; left:'.(20 + ($p)*$wdth).'px;">';
                    echo '<input type="text" name="lres_kn_'.($p+1).'[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    echo '</div>';

                    echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20+$all_step).'px; left:'.(20 + ($p)*$wdth).'px;">';
                    echo '<input type="text" name="lres_kn_'.($p+1).'_aw[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    echo '</div>';
                  //////
                    echo '<div style="position:absolute;width:'.($wdth-120).'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.($i*($height+$step) +$top_next+25+$all_step).'px; left:'.( ($p)*$wdth+170).'px;"></div>';
                    //SECOND
                       echo '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) +$top_next+$all_step).'px; left:'.(200 + ($p)*$wdth).'px;"></div>';

                       echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next +5+$all_step).'px; left:'.(240 + ($p)*$wdth).'px;">';
                           echo '<input type="text" name="lres_kn_'.($p+2).'[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                       echo '</div>';

                       echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20+$all_step).'px; left:'.(240 + ($p)*$wdth).'px;">';
                            echo '<input type="text" name="lres_kn_'.($p+2).'_aw[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                       echo '</div>';
                   //}
                }else{

                    $step_block1 = ($p==3||$p==4)?$step_block+($p==4?400:200):0;

                    echo '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next+$all_step).'px; left:'.(200+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;"></div>';

                    echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 5+$all_step).'px; left:'.(240+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;">';
                    echo '<input type="text" name="lres_kn_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    echo '</div>';
                    echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20+$all_step).'px; left:'.(240+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;">';
                    echo '<input type="text" name="lres_kn_'.(($p>=2)?$p+2+$p_step:$p+2).'_aw[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    echo '</div>';
                    ////
                    echo '<div style="position:absolute;width:'.($wdth-120).'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.($i*($height+$step) + $top_next+$top_step+$top+$all_step).'px; left:'.( ($p)*$wdth+350+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1)).'px;"></div>';
                    //}
                    if($p!=0){
                        echo '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next+$all_step).'px; left:'.(400+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1)+ ($p)*$wdth).'px;"></div>';

                        echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 5+$all_step).'px; left:'.(440+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;">';
                        echo '<input type="text" name="lres_kn_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                        echo '</div>';
                        echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20+$all_step).'px; left:'.(440+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;">';
                        echo '<input type="text" name="lres_kn_'.(($p>=2)?$p+3+$p_step:$p+3).'_aw[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                        echo '</div>';

                    }
                    //$step_block1 +=200;
                }
            }
            $top_next += $height/2;
            $height = $height + $step;
            $step = $height;
            $zz *= 2;
            $p++;



        }
//echo $step."step | ".$top_next."top_next | ".$height."height | ".$zz."zz  | ".$p."p";
        if($fid){
            $step_line = 100*($zz/$fid)*$p;//$fid;
            $var = 2*$p;

            echo '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.( $top_next+$all_step).'px; left:'.($step_line + ($p)*$wdth).'px;"></div>';
            echo '<div style="position:absolute; top:'.( $top_next+$all_step-20).'px; left:'.($step_line + ($p)*$wdth+40).'px;">';
            echo '<input type="text" name="lres_kn_'.($var+1).'_aw[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
            echo '</div>';

        ////////line of winner
            echo '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-bottom:1px solid #aaa; top:'.( ($top_next+$all_step)/3).'px; left:'.($step_line + ($p)*$wdth+$wdth).'px;"></div>';
        }
        echo '</div>';
	}

	
	
}