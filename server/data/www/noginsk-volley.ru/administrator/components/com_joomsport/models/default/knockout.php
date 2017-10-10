<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class JS_Knockout{
    

    function getKnockFormat(& $row,$t_type){
        //$format_kn = '';
        $format[] = JHTML::_('select.option',  0, JText::_('BLBE_SELFORM'), 'id', 'name' );
        if($t_type == 1){
            $format[] = JHTML::_('select.option',  2, 2, 'id', 'name' );
        }
        $format[] = JHTML::_('select.option',  4, 4, 'id', 'name' );
        $format[] = JHTML::_('select.option',  8, 8, 'id', 'name' );
        $format[] = JHTML::_('select.option',  16, 16, 'id', 'name' );
        $format[] = JHTML::_('select.option',  32, 32, 'id', 'name' );
        $format[] = JHTML::_('select.option',  64, 64, 'id', 'name' );
        /*$format[] = JHTML::_('select.option',  128, 128, 'id', 'name' );
        $format[] = JHTML::_('select.option',  256, 256, 'id', 'name' );
        $format[] = JHTML::_('select.option',  512, 512, 'id', 'name' );*/
        $format_kn = JHTML::_('select.genericlist',   $format, 'format_post', 'class="inputbox" size="1" id="format_post"', 'id', 'name', $row->k_format );

        return $format_kn;
    }

    function getKnock($row,$tourn,$match,$s_id,$get_kn_cfg,$type){
        $is_team = array();
                                                          ///$type: 0 - FE knock, 1 - BE knock
        $db = JFactory::getDBO();

        $cfg = $get_kn_cfg;
        $wdth = $cfg->wdth;
        $height = $cfg->height;
        $step = $cfg->step;
        $top_next = $cfg->top_next;
        $zz = 2;

        $Itemid = JRequest::getInt('Itemid');

        $p=0;

        $played_tt = '<span class="editlinktip hasTip" style="margin-right:15px;" title="'.JText::_( 'BLBE_PLAYED' ).'::'.JText::_( 'BLBE_TT_PLAYED' ).'"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>';

        if($tourn->t_single){
            $query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.first_name";
        }else{
            $query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.t_name";
        }
        $db->setQuery($query);

        $team = $db->loadObjectList();

        $is_team[] = JHTML::_('select.option',  0, ($tourn->t_single?JText::_('BLBE_SELPLAYER'):JText::_('BLBE_SELTEAM')), 'id', 't_name' );
        $is_team[] = JHTML::_('select.option',  -1, JText::_('BLBE_BYE'), 'id', 't_name' );
        if(count($team)){
            $teamis = array_merge($is_team,$team);
        }else{
            $teamis = $is_team;
        }

        $fid = $row->k_format;


        $kl = '';

        $kl .= '<div style="height:'.(($fid/2)*($height+$step)+60).'px;position:relative;overflow-x:auto;overflow-y:hidden;border:1px solid #777;">';
        $mm = 0;
        while(floor($fid/$zz) >= 1){

            for($i=0;$i<floor($fid/$zz);$i++){
                $mm++;
                $kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';

                if($p==0){
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next - 24).'px; left:'.(20 + ($p)*$wdth).'px;">';
                    $kl .= JHTML::_('select.genericlist',   $teamis, 'teams_kn[]', 'class="chzn-done" data-chosen="kn" size="1"', 'id', 't_name', isset($match[$i]->team1_id)?$match[$i]->team1_id:0,  ($type?'teams_kn':'teams_kn_'.$mm) ); 
                    $kl .= '</div>';
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next + 4).'px; left:'.(20 + ($p)*$wdth).'px;">';
                    $kl .= JHTML::_('select.genericlist',   $teamis, 'teams_kn_aw[]', 'class="chzn-done" data-chosen="kn" size="1"', 'id', 't_name', isset($match[$i]->team1_id)?$match[$i]->team2_id:0,  ($type?'teams_kn_aw':'teams_kn_aw'.$mm) );
                    $kl .= '</div>';
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 3).'px; left:'.(20 + ($p)*$wdth).'px;">';
                    $kl .= '<input type="text" name="res_kn_1[]" value="'.(isset($match[$i]->id)?$match[$i]->score1:'').'" size="5" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    $kl .= '</div>';
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 23).'px; left:'.(20 + ($p)*$wdth).'px;">';
                    $kl .= '<input type="text" name="res_kn_1_aw[]" value="'.(isset($match[$i]->id)?$match[$i]->score2:'').'" size="5" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    $kl .= '</div>';
                    if(!$type){
                        $match_link = 'index.php?option=com_joomsport&amp;task=match_edit&amp;cid='.(isset($match[$i]->id)?($match[$i]->id):'');
                    }else{
                        $match_link = 'index.php?option=com_joomsport&amp;view=edit_match&amp;cid[]='.(isset($match[$i]->id)?($match[$i]->id):'').'&amp;controller=admin&amp;sid='.$s_id.'&amp;Itemid='.$Itemid;
                    }
                    $kl .= (isset($match[$i]->id)?'<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10).'px; left:'.(-5 + ($p+1)*$wdth - 50).'px;"><input type="hidden" name="match_id[]" value="'.$match[$i]->id.'"><input type="checkbox" name="kn_match_played_'.$i.'" value="1" '.($match[$i]->m_played?" checked":"").' />'.$played_tt.'&nbsp;
                    <a href="'.$match_link.'"><img src="../components/com_joomsport/img/edit.png" width="20" /></a></div>':"");

                    // $cur_ind = $i + ($fid/2)*((pow(2,$p)-1)/pow(2,$p-1));

                }else{
                    $firstchld_ind = $i*2 + ($fid/2)*((pow(2,$p-1)-1)/pow(2,$p-2));
                    $cur_ind = $i + ($fid/2)*((pow(2,$p)-1)/pow(2,$p-1));
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next - 20).'px; left:'.(40 + ($p)*$wdth).'px;">';


                    if( isset($match[$firstchld_ind]) && ($match[$firstchld_ind]->score1 == $match[$firstchld_ind]->score2) && isset($match[$firstchld_ind]->winner)){

                        if($match[$firstchld_ind]->aet1 > $match[$firstchld_ind]->aet2){
                            $match[$firstchld_ind]->winner = $match[$firstchld_ind]->home_team;
                            $match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
                        }elseif($match[$firstchld_ind]->aet1 < $match[$firstchld_ind]->aet2){
                            $match[$firstchld_ind]->winner = $match[$firstchld_ind]->away_team;
                            $match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
                        }else{
                            if($match[$firstchld_ind]->p_winner && $match[$firstchld_ind]->p_winner == $match[$firstchld_ind]->team1_id){
                                $match[$firstchld_ind]->winner = $match[$firstchld_ind]->home_team;
                                $match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
                            }elseif($match[$firstchld_ind]->p_winner && $match[$firstchld_ind]->p_winner == $match[$firstchld_ind]->team2_id){
                                $match[$firstchld_ind]->winner = $match[$firstchld_ind]->away_team;
                                $match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
                            }else{
                                $match[$firstchld_ind]->m_played = 0;
                            }
                        }
                    }
                    if( isset($match[$firstchld_ind+1]) && ($match[$firstchld_ind+1]->score1 == $match[$firstchld_ind+1]->score2) && isset($match[$firstchld_ind+1]->winner)){
                        if($match[$firstchld_ind+1]->aet1 > $match[$firstchld_ind+1]->aet2){
                            $match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home_team;
                            $match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
                        }elseif($match[$firstchld_ind+1]->aet1 < $match[$firstchld_ind+1]->aet2){
                            $match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away_team;
                            $match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
                        }else{
                            if($match[$firstchld_ind+1]->p_winner && $match[$firstchld_ind+1]->p_winner == $match[$firstchld_ind+1]->team1_id){
                                $match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home_team;
                                $match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
                            }elseif($match[$firstchld_ind+1]->p_winner && $match[$firstchld_ind+1]->p_winner == $match[$firstchld_ind+1]->team2_id){
                                $match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away_team;
                                $match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
                            }else{
                                $match[$firstchld_ind+1]->m_played = 0;
                            }
                        }
                    }

                    if(isset($match[$firstchld_ind]) && $match[$firstchld_ind]->team1_id == -1 && $match[$firstchld_ind]->away_team){
                        $match[$firstchld_ind]->winner = $match[$firstchld_ind]->away_team;
                        $match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
                        $match[$firstchld_ind]->m_played = 1;
                    }
                    if(isset($match[$firstchld_ind]) && $match[$firstchld_ind]->team2_id == -1 && $match[$firstchld_ind]->home_team){
                        $match[$firstchld_ind]->winner = $match[$firstchld_ind]->home_team;
                        $match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
                        $match[$firstchld_ind]->m_played = 1;
                    }

                    if(isset($match[$firstchld_ind+1]) && $match[$firstchld_ind+1]->team1_id == -1 && $match[$firstchld_ind+1]->away_team){
                        $match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away_team;
                        $match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
                        $match[$firstchld_ind+1]->m_played = 1;
                    }
                    if(isset($match[$firstchld_ind+1]) && $match[$firstchld_ind+1]->team2_id == -1 && $match[$firstchld_ind+1]->home_team){
                        $match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home_team;

                        $match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
                        $match[$firstchld_ind+1]->m_played = 1;
                    }

                    if(isset($match[$firstchld_ind]) && $match[$firstchld_ind]->team1_id == -1 && $match[$firstchld_ind]->team2_id == -1){
                        $match[$firstchld_ind]->winner = JText::_("BLBE_BYE");
                        $match[$firstchld_ind]->winnerid = -1;
                        $match[$firstchld_ind]->m_played = 1;
                    }
                    if(isset($match[$firstchld_ind+1]) && $match[$firstchld_ind+1]->team1_id == -1 && $match[$firstchld_ind+1]->team2_id == -1){
                        $match[$firstchld_ind+1]->winner = JText::_("BLBE_BYE");
                        $match[$firstchld_ind+1]->winnerid = -1;
                        $match[$firstchld_ind+1]->m_played = 1;
                    }




                    //echo $has_winner1.'--'.$has_winner2."<br />";

                    $kl .= (isset($match[$firstchld_ind]->winner)  && $match[$firstchld_ind]->m_played )?$match[$firstchld_ind]->winner:"";
                    $kl .= (isset($match[$firstchld_ind]->winnerid) && $match[$firstchld_ind]->m_played )?('<input type="hidden" name="teams_kn_'.($p+1).'[]" value="'.$match[$firstchld_ind]->winnerid.'" />'):('<input type="hidden" name="teams_kn_'.($p+1).'[]" value="0" />');
                    $kl .= '</div>';
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next + 5).'px; left:'.(40 + ($p)*$wdth).'px;">';
                    $kl .= (isset($match[$firstchld_ind + 1]->winner)  && $match[$firstchld_ind + 1]->m_played)?$match[$firstchld_ind + 1]->winner:"";
                    $kl .= (isset($match[$firstchld_ind + 1]->winnerid) && $match[$firstchld_ind + 1]->m_played )?('<input type="hidden" name="teams_kn_aw_'.($p+1).'[]" value="'.$match[$firstchld_ind + 1]->winnerid.'" />'):('<input type="hidden" name="teams_kn_aw_'.($p+1).'[]" value="0" />');
                    $kl .= '</div>';
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 5).'px; left:'.(60 + ($p)*$wdth).'px;">';
                    $kl .= '<input type="text" name="res_kn_'.($p+1).'[]" value="'.((isset($match[$cur_ind]->score1) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score1:"").'" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    $kl .= '</div>';
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 25).'px; left:'.(60 + ($p)*$wdth).'px;">';
                    $kl .= '<input type="text" name="res_kn_'.($p+1).'_aw[]" value="'.((isset($match[$cur_ind]->score2) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score2:"").'" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    $kl .= '</div>';

                    if(!$type){
                        $match_link = 'index.php?option=com_joomsport&amp;task=match_edit&amp;cid='.(isset($match[$cur_ind]->id)?($match[$cur_ind]->id):'');
                    }else{
                        $match_link = 'index.php?option=com_joomsport&amp;view=edit_match&amp;cid[]='.(isset($match[$cur_ind]->id)?($match[$cur_ind]->id):'').'&amp;controller=admin&amp;sid='.$s_id.'&amp;Itemid='.$Itemid;
                    }
                    // if(isset($match[$cur_ind]->id) && isset($match[$firstchld_ind]->winnerid) && isset($match[$firstchld_ind + 1]->winnerid)){
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10).'px; left:'.(-5 + ($p+1)*$wdth - 50).'px;"><input type="hidden" name="matches_'.($p+1).'[]" value="'.(isset($match[$cur_ind]->id)?$match[$cur_ind]->id:0).'"><input type="checkbox" name="kn_match_played_'.$i.'_'.($p+1).'" value="1" '.(!empty($match[$cur_ind]->m_played)?" checked":"").' />'.$played_tt.'&nbsp;';
                    //if(isset($match[$cur_ind]->id) && isset($match[$firstchld_ind]->winnerid) && isset($match[$firstchld_ind + 1]->winnerid)){
                        $kl .='<a href="'.$match_link.'"><img src="../components/com_joomsport/img/edit.png" width="20" /></a>';
                    //}
                    $kl .= '</div>';
                    // }
                }
            }
            $top_next += $height/2;
            $height = $height + $step;
            $step = $height;
            $zz *= 2;
            $p++;

        }
        //var_dump($arr_prev_pl);
        //echo "<br/>";
        //var_dump($vetks_null);
        //print_r($match);
        $winmd_id = $fid - 2;
        $wiinn = '';
        if(isset($match[$winmd_id]->winner) && $match[$winmd_id]->winner && $match[$winmd_id]->score1 != $match[$winmd_id]->score2 && $match[$winmd_id]->m_played)
        {
            $wiinn = "<div style='margin-left:15px; margin-top:-20px;'>".$match[$winmd_id]->winner."</div>";

        }else if(isset($match[$winmd_id]->score1) && $match[$winmd_id]->score1 == $match[$winmd_id]->score2 && $match[$winmd_id]->aet1 != $match[$winmd_id]->aet2)
        {
            $wiinn = "<div style='margin-left:15px; margin-top:-20px;'>".($match[$winmd_id]->aet1 > $match[$winmd_id]->aet2?$match[$winmd_id]->home_team:$match[$winmd_id]->away_team)."</div>";
        }
        if(!empty($match[$winmd_id]->p_winner)){


            $wiinn = "<div style='margin-left:15px; margin-top:-20px;'>".($match[$winmd_id]->p_winner == $match[$winmd_id]->team1_id?$match[$winmd_id]->home_team:$match[$winmd_id]->away_team)."</div>";
        }
        if($fid){
            $kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.( $top_next).'px; left:'.(20 + ($p)*$wdth).'px;">'.$wiinn.'</div>';
        }
        $kl .=  '</div>';
        return $kl;
    }


    function getKnockDE($row,$tourn,$match,$s_id,$matchDE,$get_kn_cfg,$type){
        $is_team = array();
        $cfg = $get_kn_cfg;
        $db		= JFactory::getDBO();
        //print_r($match);
        $wdth = $cfg->wdth;
        $height = $cfg->height;
        $step = $cfg->step;
        $top_next = $cfg->top_next;
        $zz = 2;
        $Itemid = JRequest::getInt('Itemid');
        $p=0;

        $played_tt = '<span class="editlinktip hasTip" style="margin-right:15px;" title="'.JText::_( 'BLBE_PLAYED' ).'::'.JText::_( 'BLBE_TT_PLAYED' ).'"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>';

        if($tourn->t_single){
            $query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.first_name";
        }else{
            $query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.t_name";
        }
        $db->setQuery($query);

        $team = $db->loadObjectList();

        $is_team[] = JHTML::_('select.option',  0, ($tourn->t_single?JText::_('BLBE_SELPLAYER'):JText::_('BLBE_SELTEAM')), 'id', 't_name' );
        $is_team[] = JHTML::_('select.option',  -1, JText::_('BLBE_BYE'), 'id', 't_name' );
        if(count($team)){
            $teamis = array_merge($is_team,$team);
        }else{
            $teamis = $is_team;
        }

        $fid = $row->k_format;


        $kl = '';

        $kl .= '<div style="height:'.(($fid/2)*($height+$step+50)+360).'px;position:relative;overflow-x:auto;overflow-y:hidden;border:1px solid #777;">';
        $mm = 0;
        while(floor($fid/$zz) >= 1){

            for($i=0;$i<floor($fid/$zz);$i++){
                $mm++;
                $kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';

                if($p==0){
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next - 24).'px; left:'.(20 + ($p)*$wdth).'px;">';
                    $kl .= JHTML::_('select.genericlist',   $teamis, 'teams_kn[]', 'class="chzn-done" data-chosen="kn" size="1"', 'id', 't_name', isset($match[$i]->team1_id)?$match[$i]->team1_id:0,  ($type?'teams_kn':'teams_kn_'.$mm) );
                    $kl .= '</div>';
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next + 4).'px; left:'.(20 + ($p)*$wdth).'px;">';
                    $kl .= JHTML::_('select.genericlist',   $teamis, 'teams_kn_aw[]', 'class="chzn-done" data-chosen="kn" size="1"', 'id', 't_name', isset($match[$i]->team1_id)?$match[$i]->team2_id:0, ($type?'teams_kn_aw':'teams_kn_aw'.$mm) );
                    $kl .= '</div>';
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 3).'px; left:'.(20 + ($p)*$wdth).'px;">';
                    $kl .= '<input type="text" name="res_kn_1[]" value="'.(isset($match[$i]->id)?$match[$i]->score1:'').'" size="5" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    $kl .= '</div>';
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 23).'px; left:'.(20 + ($p)*$wdth).'px;">';
                    $kl .= '<input type="text" name="res_kn_1_aw[]" value="'.(isset($match[$i]->id)?$match[$i]->score2:'').'" size="5" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    $kl .= '</div>';
                    if(!$type){
                        $match_link = 'index.php?option=com_joomsport&amp;task=match_edit&amp;cid='.(isset($match[$i]->id)?($match[$i]->id):'');
                    }else{
                        $match_link = 'index.php?option=com_joomsport&amp;view=edit_match&amp;cid[]='.(isset($match[$i]->id)?($match[$i]->id):'').'&amp;controller=admin&amp;sid='.$s_id.'&amp;Itemid='.$Itemid;
                    }

                    $kl .= (isset($match[$i]->id)?'<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10).'px; left:'.(-5 + ($p+1)*$wdth - 50).'px;"><input type="hidden" name="match_id[]" value="'.$match[$i]->id.'"><input type="checkbox" name="kn_match_played_'.$i.'" value="1" '.($match[$i]->m_played?" checked":"").' />'.$played_tt.'&nbsp;
                        <a href="'.$match_link.'"><img src="../components/com_joomsport/img/edit.png" width="20" /></a></div>':"");



                }else{
                    $firstchld_ind = $i*2 + ($fid/2)*((pow(2,$p-1)-1)/pow(2,$p-2));
                    $cur_ind = $i + ($fid/2)*((pow(2,$p)-1)/pow(2,$p-1));
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next - 20).'px; left:'.(40 + ($p)*$wdth).'px;">';


                    if( isset($match[$firstchld_ind]) && ($match[$firstchld_ind]->score1 == $match[$firstchld_ind]->score2) && isset($match[$firstchld_ind]->winner)){

                        if($match[$firstchld_ind]->aet1 > $match[$firstchld_ind]->aet2){
                            $match[$firstchld_ind]->winner = $match[$firstchld_ind]->home_team;
                            $match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
                        }elseif($match[$firstchld_ind]->aet1 < $match[$firstchld_ind]->aet2){
                            $match[$firstchld_ind]->winner = $match[$firstchld_ind]->away_team;
                            $match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
                        }else{
                            if($match[$firstchld_ind]->p_winner && $match[$firstchld_ind]->p_winner == $match[$firstchld_ind]->team1_id){
                                $match[$firstchld_ind]->winner = $match[$firstchld_ind]->home_team;
                                $match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
                            }elseif($match[$firstchld_ind]->p_winner && $match[$firstchld_ind]->p_winner == $match[$firstchld_ind]->team2_id){
                                $match[$firstchld_ind]->winner = $match[$firstchld_ind]->away_team;
                                $match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
                            }else{
                                $match[$firstchld_ind]->m_played = 0;
                            }
                        }
                    }
                    if( isset($match[$firstchld_ind+1]) && ($match[$firstchld_ind+1]->score1 == $match[$firstchld_ind+1]->score2) && isset($match[$firstchld_ind+1]->winner)){
                        if($match[$firstchld_ind+1]->aet1 > $match[$firstchld_ind+1]->aet2){
                            $match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home_team;
                            $match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
                        }elseif($match[$firstchld_ind+1]->aet1 < $match[$firstchld_ind+1]->aet2){
                            $match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away_team;
                            $match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
                        }else{
                            if($match[$firstchld_ind+1]->p_winner && $match[$firstchld_ind+1]->p_winner == $match[$firstchld_ind+1]->team1_id){
                                $match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home_team;
                                $match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
                            }elseif($match[$firstchld_ind+1]->p_winner && $match[$firstchld_ind+1]->p_winner == $match[$firstchld_ind+1]->team2_id){
                                $match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away_team;
                                $match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
                            }else{
                                $match[$firstchld_ind+1]->m_played = 0;
                            }
                        }
                    }
/////////////////
                    if(isset($match[$firstchld_ind]) && $match[$firstchld_ind]->team1_id == -1 && $match[$firstchld_ind]->away_team){
                        $match[$firstchld_ind]->winner = $match[$firstchld_ind]->away_team;
                        $match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team2_id;
                        $match[$firstchld_ind]->m_played = 1;
                    }
                    if(isset($match[$firstchld_ind]) && $match[$firstchld_ind]->team2_id == -1 && $match[$firstchld_ind]->home_team){
                        $match[$firstchld_ind]->winner = $match[$firstchld_ind]->home_team;
                        $match[$firstchld_ind]->winnerid = $match[$firstchld_ind]->team1_id;
                        $match[$firstchld_ind]->m_played = 1;
                    }

                    if(isset($match[$firstchld_ind+1]) && $match[$firstchld_ind+1]->team1_id == -1 && $match[$firstchld_ind+1]->away_team){
                        $match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->away_team;
                        $match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team2_id;
                        $match[$firstchld_ind+1]->m_played = 1;
                    }
                    if(isset($match[$firstchld_ind+1]) && $match[$firstchld_ind+1]->team2_id == -1 && $match[$firstchld_ind+1]->home_team){
                        $match[$firstchld_ind+1]->winner = $match[$firstchld_ind+1]->home_team;

                        $match[$firstchld_ind+1]->winnerid = $match[$firstchld_ind+1]->team1_id;
                        $match[$firstchld_ind+1]->m_played = 1;
                    }

                    if(isset($match[$firstchld_ind]) && $match[$firstchld_ind]->team1_id == -1 && $match[$firstchld_ind]->team2_id == -1){
                        $match[$firstchld_ind]->winner = JText::_("BLBE_BYE");
                        $match[$firstchld_ind]->winnerid = -1;
                        $match[$firstchld_ind]->m_played = 1;
                    }
                    if(isset($match[$firstchld_ind+1]) && $match[$firstchld_ind+1]->team1_id == -1 && $match[$firstchld_ind+1]->team2_id == -1){
                        $match[$firstchld_ind+1]->winner = JText::_("BLBE_BYE");
                        $match[$firstchld_ind+1]->winnerid = -1;
                        $match[$firstchld_ind+1]->m_played = 1;
                    }




                    //echo $has_winner1.'--'.$has_winner2."<br />";

                    $kl .= (isset($match[$firstchld_ind]->winner)  && $match[$firstchld_ind]->m_played )?$match[$firstchld_ind]->winner:"";
                    $kl .= (isset($match[$firstchld_ind]->winnerid) && $match[$firstchld_ind]->m_played )?('<input type="hidden" name="teams_kn_'.($p+1).'[]" value="'.$match[$firstchld_ind]->winnerid.'" />'):('<input type="hidden" name="teams_kn_'.($p+1).'[]" value="0" />');
                    $kl .= '</div>';
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next + 5).'px; left:'.(40 + ($p)*$wdth).'px;">';
                    $kl .= (isset($match[$firstchld_ind + 1]->winner)  && $match[$firstchld_ind + 1]->m_played)?$match[$firstchld_ind + 1]->winner:"";
                    $kl .= (isset($match[$firstchld_ind + 1]->winnerid) && $match[$firstchld_ind + 1]->m_played )?('<input type="hidden" name="teams_kn_aw_'.($p+1).'[]" value="'.$match[$firstchld_ind + 1]->winnerid.'" />'):('<input type="hidden" name="teams_kn_aw_'.($p+1).'[]" value="0" />');
                    $kl .= '</div>';
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 5).'px; left:'.(60 + ($p)*$wdth).'px;">';
                    $kl .= '<input type="text" name="res_kn_'.($p+1).'[]" value="'.((isset($match[$cur_ind]->score1) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score1:"").'" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    $kl .= '</div>';
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 25).'px; left:'.(60 + ($p)*$wdth).'px;">';
                    $kl .= '<input type="text" name="res_kn_'.($p+1).'_aw[]" value="'.((isset($match[$cur_ind]->score2) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score2:"").'" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    $kl .= '</div>';
                    if(!$type){
                        $match_link = 'index.php?option=com_joomsport&amp;task=match_edit&amp;cid='.(isset($match[$cur_ind]->id)?($match[$cur_ind]->id):'');
                    }else{
                        $match_link = 'index.php?option=com_joomsport&amp;view=edit_match&amp;cid[]='.(isset($match[$cur_ind]->id)?($match[$cur_ind]->id):'').'&amp;controller=admin&amp;sid='.$s_id.'&amp;Itemid='.$Itemid;
                    }
                    // if(isset($match[$cur_ind]->id) && isset($match[$firstchld_ind]->winnerid) && isset($match[$firstchld_ind + 1]->winnerid)){
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10).'px; left:'.(-5 + ($p+1)*$wdth - 50).'px;"><input type="checkbox" name="kn_match_played_'.$i.'_'.($p+1).'" value="1" '.(!empty($match[$cur_ind]->m_played)?" checked":"").' />'.$played_tt.'&nbsp;';
                    //if(isset($match[$cur_ind]->id) && isset($match[$firstchld_ind]->winnerid) && isset($match[$firstchld_ind + 1]->winnerid)){
                        $kl .='<input type="hidden" name="matches_'.($p+1).'[]" value="'.(isset($match[$cur_ind]->id)?$match[$cur_ind]->id:0).'">
                                <a href="'.$match_link.'"><img src="../components/com_joomsport/img/edit.png" width="20" /></a>';
                    //}
                    $kl .= '</div>';
                    // }
                }
            }
            $top_next += $height/2;
            $height = $height + $step;
            $step = $height;
            $zz *= 2;
            $p++;

        }
        //var_dump($arr_prev_pl);
        //echo "<br/>";
        //var_dump($vetks_null);
        $winmd_id = $fid - 2;
        $wiinn = '';
        $wiinnid = '';
        if(isset($match[$winmd_id])){
            $match[$winmd_id] = $this->getMBy($match[$winmd_id]);
        }
		
        if(isset($match[$winmd_id]->winner) && $match[$winmd_id]->winner && $match[$winmd_id]->score1 != $match[$winmd_id]->score2 && $match[$winmd_id]->m_played)
        {

            $wiinn = "<div style='margin-left:45px; margin-top:-20px;'>".$match[$winmd_id]->winner."</div>";
			$wiinnid = '<input type="hidden" name=teams_kn_'.($p+1).'[]" value="'.(isset($match[$winmd_id]->winnerid)?$match[$winmd_id]->winnerid:'').'" />';
        }
		if(isset($match[$winmd_id]->score1) && isset($match[$winmd_id]->score2)){
			if($match[$winmd_id]->score1 == $match[$winmd_id]->score2 && $match[$winmd_id]->aet1 != $match[$winmd_id]->aet2)
			{
				$wiinn = "<div style='margin-left:45px; margin-top:-20px; '>".($match[$winmd_id]->aet1 > $match[$winmd_id]->aet2?$match[$winmd_id]->home_team:$match[$winmd_id]->away_team)."</div>";
				$wiinnid = '<input type="hidden" name=teams_kn_'.($p+1).'[]" value="'.($match[$winmd_id]->aet1 > $match[$winmd_id]->aet2?$match[$winmd_id]->team1_id:$match[$winmd_id]->team2_id).'" />';
			}
		}
        if(!empty($match[$winmd_id]->p_winner)){


            $wiinn = "<div style='margin-left:45px; margin-top:-20px; '>".($match[$winmd_id]->p_winner == $match[$winmd_id]->team1_id?$match[$winmd_id]->home_team:$match[$winmd_id]->away_team)."</div>";
			$wiinnid = '<input type="hidden" name=teams_kn_'.($p+1).'[]" value="'.(isset($match[$winmd_id]->p_winner)?$match[$winmd_id]->p_winner:'').'" />';
        }
        if(!$wiinnid){$wiinnid = '<input type="hidden" name=teams_kn_'.($p+1).'[]" value="0" />';}
        if($fid){
            $res = ($p==4)?15:($p==5?115:($p==6?335:0));
            $w_st = $p!=2?200*($p-2):0;
            $h_st = 60*($height/240);
            if(isset($match[$winmd_id+1])){
                $match[$winmd_id+1] = $this->getMBy($match[$winmd_id+1]);
            }
            //$kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.( $top_next).'px; left:'.(20 + ($p)*$wdth).'px;">'.$wiinn.'</div>';
            $kl .= '<div style="position:absolute;width:'.($wdth+30+$w_st).'px;height:'.($height-$h_st-(($fid/4)==1?15:15*($fid/2))-$res).'px; border-top:1px solid #aaa;border-right:1px solid #aaa; top:'.( $top_next).'px; left:'.(20 + ($p)*$wdth).'px;">'.$wiinn.'</div>'.$wiinnid;
            $kl .= '<div style="position:absolute; top:'.($top_next+5).'px; left:'.(90 + ($p)*$wdth+$w_st).'px;">';
            $kl .= '<input type="text" name="res_kn_'.($p+1).'[]" value="'.((isset($match[$winmd_id+1]->score1) && $match[$winmd_id+1]->m_played)?$match[$winmd_id+1]->score1:"").'" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';

            $kl .= '<input type="hidden" name="final" value="'.((isset($match[$winmd_id+1]->winnerid))?1:0).'"></div>';
            //echo "<hr>".$w_st;
            /// echo "</br>".$top_step;
            if(!$type){
                $match_link = 'index.php?option=com_joomsport&amp;task=match_edit&amp;cid='.(isset($match[$winmd_id+1]->id)?($match[$winmd_id+1]->id):'');
            }else{
                $match_link = 'index.php?option=com_joomsport&amp;view=edit_match&amp;cid[]='.(isset($match[$winmd_id+1]->id)?($match[$winmd_id+1]->id):'').'&amp;controller=admin&amp;sid='.$s_id.'&amp;Itemid='.$Itemid;
            }
            $kl .= '<div style="width:70px;position:absolute; top:'.($i*($height+$step) + $height/2 - (($fid/4 > 1)?380*($fid/4):390)-(($fid/4)==1?15:10*($fid/2))-$res).'px; left:'.(-5 + ($p+1)*$wdth - 20 + ($fid/4 > 1?$w_st:0)).'px;">';
            if(isset($match[$winmd_id]) && isset($matchDE[$winmd_id-1])){
                if($match[$winmd_id]->winnerid && $matchDE[$winmd_id-1]->winnerid && $matchDE[$winmd_id-1]->m_played && $match[$winmd_id]->m_played){
                    $kl .= '<input type="checkbox" name="kn_match_played_'.($i-1).'_'.($p+1).'" value="1" '.($match[$winmd_id+1]->m_played?" checked":"").' />'.$played_tt.'&nbsp;';

                }
            }
            $kl .= '<input type="hidden" name="matches_'.($p+1).'[]" value="'.(isset($match[$winmd_id+1]->id)?$match[$winmd_id+1]->id:0).'">
                    <a href="'.$match_link.'"><img src="../components/com_joomsport/img/edit.png" width="20" /></a></div>';
        }
        //$kl .=  '</div>';
        //////////////////////
        //////////////////////////////
        ///////////////////////////////
        //$is_team = array();
        $cfg = $get_kn_cfg;

        $wdth = $cfg->wdth;
        $height = $cfg->height;
        $step = $cfg->step;
        $top_next = $cfg->top_next;
        $zz = 4;


        $p_last = $p+1;

        $p=0;
        $top = 0;
        $p_step = 0;
        $top_step = 60;
        $all_step = 120*($fid/2);
        $step_block = ($fid/$zz>2)?200:0;
        $strafe = 0;
        $first = 0;
        $marg = 15;
        $marg1 = 60;
        $m = -1;

///////////////////////////////////////////////////////////////////////////////////

        while(floor($fid/$zz) >= 1){
            $p_step =($p>=2)? $p_step+1:$p_step+0;
            $strafe++;

            if($p==3||$p==4){
                $top =($p==3)? $top+60:$top+210;
            }
            $top_step =($p==0||$p==1)? $top_step + 0: $top_step+60;
            for($i=0;$i<floor($fid/$zz);$i++){

//////////////////////////////////////////////////////

			if(isset($match[$firstchld_ind]) && isset($match[$firstchld_ind + 1])){
                $match[$firstchld_ind] = $this->getMBy($match[$firstchld_ind]);
                $match[$firstchld_ind + 1] = $this->getMBy($match[$firstchld_ind + 1]);

			}
////////////////////////////////////////////////////////

                if($p==0){
                    $firstchld_ind = $i*2 + ($fid/2)*((pow(2,$p)-1)/pow(2,$p-1));
                    $cur_ind = $i + ($fid/2)*((pow(2,$p)-1)/pow(2,$p-1));
//////////////////////////////////			
			if(isset($match[$firstchld_ind]) && isset($match[$firstchld_ind + 1])){
                $match[$firstchld_ind] = $this->getMBy($match[$firstchld_ind]);
                $match[$firstchld_ind + 1] = $this->getMBy($match[$firstchld_ind + 1]);
			}
                    $kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) +$top_next+$all_step).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';

                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next - 20+$all_step).'px; left:'.(40 + ($p)*$wdth).'px;">';
               
				if(!$match[$firstchld_ind]->is_extra && !$match[$firstchld_ind]->p_winner){
					$kl .= (isset($match[$firstchld_ind]->looser)  && $match[$firstchld_ind]->m_played )?$match[$firstchld_ind]->looser:"";
                    $kl .= (isset($match[$firstchld_ind]->looserid) && $match[$firstchld_ind]->m_played )?('<input type="hidden" name="lteams_kn_'.($p+1).'[]" value="'.$match[$firstchld_ind]->looserid.'" />'):('<input type="hidden" name="lteams_kn_'.($p+1).'[]" value="0" />');
                }
				if($match[$firstchld_ind]->score1 == $match[$firstchld_ind]->score2 && $match[$firstchld_ind]->aet1 != $match[$firstchld_ind]->aet2){
					$kl .= $match[$firstchld_ind]->aet1 > $match[$firstchld_ind]->aet2?$match[$firstchld_ind]->away_team.'<input type="hidden" name="lteams_kn_'.($p+1).'[]" value="'.$match[$firstchld_ind]->team2_id.'" />':$match[$firstchld_ind]->home_team.'<input type="hidden" name="lteams_kn_'.($p+1).'[]" value="'.$match[$firstchld_ind]->team1_id.'" />';      
				}
				if($match[$firstchld_ind]->p_winner){
                    $kl .= $match[$firstchld_ind]->p_winner == $match[$firstchld_ind]->team1_id?$match[$firstchld_ind]->away_team.'<input type="hidden" name="lteams_kn_'.($p+1).'[]" value="'.$match[$firstchld_ind]->team2_id.'" />':$match[$firstchld_ind]->home_team.'<input type="hidden" name="lteams_kn_'.($p+1).'[]" value="'.$match[$firstchld_ind]->team1_id.'" />';
                }
					$kl .= '</div>';
				///
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next + 5+$all_step).'px; left:'.(40 + ($p)*$wdth).'px;">';
                if(!$match[$firstchld_ind + 1]->is_extra && !$match[$firstchld_ind + 1]->p_winner){    
					$kl .= (isset($match[$firstchld_ind + 1]->looser)  && $match[$firstchld_ind + 1]->m_played)?$match[$firstchld_ind + 1]->looser:"";
                    $kl .= (isset($match[$firstchld_ind + 1]->looserid) && $match[$firstchld_ind + 1]->m_played )?('<input type="hidden" name="lteams_kn_aw_'.($p+1).'[]" value="'.$match[$firstchld_ind + 1]->looserid.'" />'):('<input type="hidden" name="lteams_kn_aw_'.($p+1).'[]" value="0" />');
                }
				if($match[$firstchld_ind + 1]->score1 == $match[$firstchld_ind + 1]->score2 && $match[$firstchld_ind + 1]->aet1 != $match[$firstchld_ind + 1]->aet2){
                    $kl .= $match[$firstchld_ind + 1]->aet1 > $match[$firstchld_ind + 1]->aet2?$match[$firstchld_ind + 1]->away_team.'<input type="hidden" name="lteams_kn_aw_'.($p+1).'[]" value="'.$match[$firstchld_ind + 1]->team2_id.'" />':$match[$firstchld_ind + 1]->home_team.'<input type="hidden" name="lteams_kn_aw_'.($p+1).'[]" value="'.$match[$firstchld_ind + 1]->team1_id.'" />';
                }
				if($match[$firstchld_ind + 1]->p_winner){
                    $kl .= $match[$firstchld_ind + 1]->p_winner == $match[$firstchld_ind + 1]->team1_id?$match[$firstchld_ind + 1]->away_team.'<input type="hidden" name="lteams_kn_aw_'.($p+1).'[]" value="'.$match[$firstchld_ind + 1]->team2_id.'" />':$match[$firstchld_ind + 1]->home_team.'<input type="hidden" name="lteams_kn_aw_'.($p+1).'[]" value="'.$match[$firstchld_ind + 1]->team1_id.'" />';
                }
					$kl .= '</div>';
					if(isset($matchDE[$cur_ind])){
						$matchDE[$cur_ind] = $this->getMBy($matchDE[$cur_ind]);
					}
                    //print_r($matchDE[$cur_ind]);
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 5+$all_step).'px; left:'.(20 + ($p)*$wdth).'px;">';
                    $kl .= '<input type="text" name="lres_kn_'.($p+1).'[]" value="'.((isset($matchDE[$cur_ind]->score1) && $matchDE[$cur_ind]->m_played)?$matchDE[$cur_ind]->score1:"").'" size="10" maxlength="5" style="width:61px;" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    $kl .= '</div>';

                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20+$all_step).'px; left:'.(20 + ($p)*$wdth).'px;">';
                    $kl .= '<input type="text" name="lres_kn_'.($p+1).'_aw[]" value="'.((isset($matchDE[$cur_ind]->score2) && $matchDE[$cur_ind]->m_played)?$matchDE[$cur_ind]->score2:"").'" size="10" maxlength="5" style="width:61px;" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    $kl .= '<input type="hidden" name="lk_type_'.($p+1).'[]" value="1"></div>';
                    if(!$type){
                    /*???*/         $match_link = 'index.php?option=com_joomsport&amp;task=match_edit&amp;cid='.(isset($matchDE[$cur_ind]->id)?($matchDE[$cur_ind]->id):'');
                    }else{
                    /*???*/         $match_link = 'index.php?option=com_joomsport&amp;view=edit_match&amp;cid[]='.(isset($match[$cur_ind]->id)?($match[$cur_ind]->id):'').'&amp;controller=admin&amp;sid='.$s_id.'&amp;Itemid='.$Itemid;
                    }
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10+$all_step).'px; left:'.(-5 + ($p+1)*$wdth - 50).'px;">';
                    if(isset($match[$cur_ind]->id) && isset($match[$firstchld_ind]->looserid) && isset($match[$firstchld_ind + 1]->looserid)){
                        $kl .= '<input type="checkbox" name="lkn_match_played_'.$i.'_'.($p+1).'" value="1" '.(isset($matchDE[$cur_ind])?($matchDE[$cur_ind]->m_played?" checked":""):'').' />'.$played_tt.'&nbsp;
                        ';
                    }
                     $kl .= '<input type="hidden" name="lmatches_'.($p+1).'[]" value="'.(isset($matchDE[$cur_ind]->id)?$matchDE[$cur_ind]->id:0).'">
                            <a href="'.$match_link.'"><img src="../components/com_joomsport/img/edit.png" width="20" /></a></div>';
                    ///////////////
                    $kl .= '<div style="position:absolute;width:'.($wdth-120).'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.($i*($height+$step) +$top_next+25+$all_step).'px; left:'.( ($p)*$wdth+170).'px;"></div>';



                    if(($fid/4)!=1){
                        $firstchld_indDE = ($fid/4) + $i+ (floor(($fid/8)) == 1?0:floor(($fid/8)));
                        $first = (($fid%16)==0 && ($fid/16)!=1)?($fid/16):0;
                        if($first == 4){$first = 6;}
                        $tm_step = ceil($fid/16);

                        $cur_indDE = $cur_ind + floor(($fid/8))+floor($fid/16);
                    }else{
                        $cur_indDE = $cur_ind;
                        $firstchld_indDE = $firstchld_ind;
                        $tm_step = 1;
                    }

                    if(isset($matchDE[$cur_ind])){
                        $matchDE[$cur_ind] = $this->getMBy($matchDE[$cur_ind]);
                    }
                    if(isset($matchDE[$firstchld_indDE + 2+$first])){
                        $match[$firstchld_indDE + 2+$first] = $this->getMBy($match[$firstchld_indDE + 2+$first]);
                    }
//////////////////////////////////////////////////////////////////////////////////////////
                    $kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) +$top_next+$all_step-15).'px; left:'.(200 + ($p)*$wdth).'px;"></div>';

                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next - 20+$all_step-15).'px; left:'.(240 + ($p)*$wdth).'px;">';
//print_r($match[$firstchld_indDE + 2+$first]);
				    $lt_ln = '';
                    if(isset($match[$firstchld_indDE + 2+$first]->p_winner) && !$match[$firstchld_indDE + 2+$first]->is_extra &&  !$match[$firstchld_indDE + 2+$first]->p_winner && $match[$firstchld_indDE + 2+$first]->score1 != $match[$firstchld_indDE + 2+$first]->score2){
                        $kl .= (isset($match[$firstchld_indDE + 2+$first]->looser)  && $match[$firstchld_indDE + 2+$first]->m_played)?$match[$firstchld_indDE + 2+$first]->looser:"";
                        $lt_ln = (isset($match[$firstchld_indDE + 2+$first]->looserid) && $match[$firstchld_indDE + 2+$first]->m_played )?('<input type="hidden" name="lteams_kn_'.($p+2).'[]" value="'.$match[$firstchld_indDE + 2+$first]->looserid.'" />'):('<input type="hidden" name="lteams_kn_'.($p+2).'[]" value="0" />');
                    }
				
				
                    if(isset($match[$firstchld_indDE + 2+$first]->score1) && $match[$firstchld_indDE + 2+$first]->score1 == $match[$firstchld_indDE + 2+$first]->score2 && $match[$firstchld_indDE + 2+$first]->aet1 != $match[$firstchld_indDE + 2+$first]->aet2)
                    {
                        $lt_ln = $match[$firstchld_indDE + 2+$first]->aet1 > $match[$firstchld_indDE + 2+$first]->aet2?$match[$firstchld_indDE + 2+$first]->away_team.'<input type="hidden" name="lteams_kn_'.($p+2).'[]" value="'.$match[$firstchld_indDE + 2+$first]->team2_id.'" />':$match[$firstchld_indDE + 2+$first]->home_team.'<input type="hidden" name="lteams_kn_'.($p+2).'[]" value="'.$match[$firstchld_indDE + 2+$first]->team1_id.'" />';
                    }
				
                    if(!empty($match[$firstchld_indDE + 2+$first]->p_winner)){
                        $lt_ln = $match[$firstchld_indDE + 2+$first]->p_winner == $match[$firstchld_indDE + 2+$first]->team1_id?$match[$firstchld_indDE + 2+$first]->away_team.'<input type="hidden" name="lteams_kn_'.($p+2).'[]" value="'.$match[$firstchld_indDE + 2+$first]->team2_id.'" />':$match[$firstchld_indDE + 2+$first]->home_team.'<input type="hidden" name="lteams_kn_'.($p+2).'[]" value="'.$match[$firstchld_indDE + 2+$first]->team1_id.'" />';
                    }
                    if(!$lt_ln){$lt_ln = '<input type="hidden" name="lteams_kn_'.($p+2).'[]" value="0" />';}
                    $kl .= $lt_ln;

                    $kl .= '</div>';
/////////////////////////////////////////////////////////
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next + 5+$all_step-15).'px; left:'.(240 + ($p)*$wdth).'px;">';
                    $lt_ln_aw = '';
                    if(isset($matchDE[$cur_ind]->is_extra)){
                        if(!$matchDE[$cur_ind]->is_extra && !$matchDE[$cur_ind]->p_winner && $matchDE[$cur_ind]->score1 != $matchDE[$cur_ind]->score2 || ($matchDE[$cur_ind]->team1_id==-1 || $matchDE[$cur_ind]->team2_id==-1)){
                            $kl .= (isset($matchDE[$cur_ind]->winner)  && $matchDE[$cur_ind]->m_played)?$matchDE[$cur_ind]->winner:""; ///
                            $lt_ln_aw = (isset($matchDE[$cur_ind]->winnerid) && $matchDE[$cur_ind]->m_played)?('<input type="hidden" name="lteams_kn_aw_'.($p+2).'[]" value="'.$matchDE[$cur_ind]->winnerid.'" />'):('<input type="hidden" name="lteams_kn_aw_'.($p+2).'[]" value="0" />');
                        }
                    }
				//} ---------------------------
                    if(isset($matchDE[$cur_ind]->score1) && $matchDE[$cur_ind]->score1 == $matchDE[$cur_ind]->score2 && $matchDE[$cur_ind]->aet1 != $matchDE[$cur_ind]->aet2)
                    {
                        $lt_ln_aw = $matchDE[$cur_ind]->aet1 > $matchDE[$cur_ind]->aet2?$matchDE[$cur_ind]->home_team.'<input type="hidden" name="lteams_kn_aw_'.($p+2).'[]" value="'.$matchDE[$cur_ind]->team1_id.'" />':$matchDE[$cur_ind]->away_team.'<input type="hidden" name="lteams_kn_aw_'.($p+2).'[]" value="'.$matchDE[$cur_ind]->team2_id.'" />';
                    }
                    if(!empty($matchDE[$cur_ind]->p_winner)){


                        $lt_ln_aw = $matchDE[$cur_ind]->p_winner == $matchDE[$cur_ind]->team1_id?$matchDE[$cur_ind]->home_team.'<input type="hidden" name="lteams_kn_aw_'.($p+2).'[]" value="'.$matchDE[$cur_ind]->team1_id.'" />':$matchDE[$cur_ind]->away_team.'<input type="hidden" name="lteams_kn_aw_'.($p+2).'[]" value="'.$matchDE[$cur_ind]->team2_id.'" />';
                    }
                    if(!$lt_ln_aw){$lt_ln_aw = '<input type="hidden" name="lteams_kn_aw_'.($p+2).'[]" value="0" />';}
                    $kl .= $lt_ln_aw;
     /////////////////
                    //$kl .= (isset($matchDE[$cur_ind]->winnerid) && $matchDE[$cur_ind]->m_played )?('<input type="hidden" name="lteams_kn_aw_'.($p+2).'[]" value="'.$matchDE[$cur_ind]->winnerid.'" />'):('<input type="hidden" name="lteams_kn_aw_'.($p+2).'[]" value="0" />');
                   ///(isset($matchDE[$cur_ind]->winnerid) && $matchDE[$cur_ind]->m_played )   $matchDE[$cur_ind]->winnerid
                    $kl .= '</div>';

                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next +5+$all_step-15).'px; left:'.(200 + ($p)*$wdth).'px;">';
                    $kl .= '<input type="text" name="lres_kn_'.($p+2).'[]" value="'.((isset($matchDE[$cur_indDE+$tm_step]->score1) && $matchDE[$cur_indDE+$tm_step]->m_played)?$matchDE[$cur_indDE+$tm_step]->score1:"").'" size="10" maxlength="5" style="width:61px;" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    $kl .= '</div>';

                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20+$all_step-15).'px; left:'.(200 + ($p)*$wdth).'px;">';
                    $kl .= '<input type="text" name="lres_kn_'.($p+2).'_aw[]" value="'.((isset($matchDE[$cur_indDE+$tm_step]->score2) && $matchDE[$cur_indDE+$tm_step]->m_played)?$matchDE[$cur_indDE+$tm_step]->score2:"").'" size="10" maxlength="5" style="width:61px;" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    $kl .= '<input type="hidden" name="lk_type_'.($p+2).'[]" value="1"></div>';
                    if(!$type){
                        $match_link = 'index.php?option=com_joomsport&amp;task=match_edit&amp;cid='.(isset($matchDE[$cur_indDE+$tm_step]->id)?($matchDE[$cur_indDE+$tm_step]->id):'');
                    }else{
                        $match_link = 'index.php?option=com_joomsport&amp;view=edit_match&amp;cid[]='.(isset($match[$cur_indDE+$tm_step]->id)?($match[$cur_indDE+$tm_step]->id):'').'&amp;controller=admin&amp;sid='.$s_id.'&amp;Itemid='.$Itemid;
                    }
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10+$all_step-15).'px; left:'.(-5 + ($p+1)*$wdth - 50+180).'px;">';
                    if(isset($matchDE[$cur_ind]->id) && isset($matchDE[$cur_ind]->winnerid) && $match[$firstchld_indDE + 2+$first]->looserid && $matchDE[$cur_ind]->m_played && $match[$firstchld_indDE + 2+$first]->m_played){
                        $kl .= '<input type="checkbox" name="lkn_match_played_'.$i.'_'.($p+2).'" value="1" '.(!empty($matchDE[$cur_indDE+$tm_step]->m_played)?" checked":"").' />'.$played_tt.'&nbsp;
                        ';

                    }
                    $kl .= '<input type="hidden" name="lmatches_'.($p+2).'[]" value="'.(isset($matchDE[$cur_indDE+$tm_step]->id)?$matchDE[$cur_indDE+$tm_step]->id:0).'">
                            <a href="'.$match_link.'"><img src="../components/com_joomsport/img/edit.png" width="20" /></a></div>';
                }else{

                    $firstchld_ind = $i*2 + ($fid/4)*((pow(2,$p-1)-1)/pow(2,$p-2));
                    $cur_ind = $i + ($fid/2)*((pow(2,$p)-1)/pow(2,$p-1));

                    $firstchld_ind += (floor(($fid/8)) == 1?0:floor(($fid/8)));

                    $step_block1 = ($p==3||$p==4)?$step_block+($p==4?400:200):0;

                    $f_num = (($fid/8)>2)?($fid/8):2;
                    if($fid == 64&&$strafe==4){$f_num += 4;}

                    if($m!=$p){
                        $marg *=$p;
                        $marg1 *=$p;
                        if($p>1){

                            $marg = $marg+($height/8)-($p==3?30*$p:($p==4?90*$p:0));
                            $marg1 = $marg1+($height/8)-($p==3?50*$p:($p==4?200*$p:0));

                            //$p==3?$marg1 = $marg1-(75):"";
                            //$p==4?$marg1 = $marg1-(375):"";
                        }
                    }
                    $m = $p;

                    ///
                    $kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next+$all_step-$marg).'px; left:'.(200+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;"></div>';

//////////////////////////////////////////////////////
                   if(isset($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]) && isset($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))])){
                        $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))] = $this->getMBy($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]);
                        $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))] = $this->getMBy($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]);
                   }
///////////////////////////////////////////////////////
                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next - 20+$all_step-$marg).'px; left:'.(240+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;">';
                $lt_ln = '';
                if(isset($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner) && !$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->is_extra && !$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner && $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score1 != $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score2){
					$kl .= (isset($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->winner)  && $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==5?14:0))]->m_played )?$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==5?14:0))]->winner:""; ///+4 для 64.
                    $lt_ln = (isset($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->winnerid) && $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==5?14:0))]->m_played )?('<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="'.$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==5?14:0))]->winnerid.'" />'):('<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="0" />');
                }
				if(isset($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score1) && $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score1 == $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score2 && $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet1 != $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet2){
                    $lt_ln = $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet1 > $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet2?$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->home_team.'<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="'.$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team1_id.'" />':$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->away_team.'<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="'.$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team2_id.'" />';
				}
				if(!empty($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner)){
                    $lt_ln = $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner == $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team1_id?$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->home_team.'<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="'.$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team1_id.'" />':$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->away_team.'<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="'.$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team2_id.'" />';
                }
                    if(!$lt_ln){$lt_ln = '<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="0" />';}
                    $kl .= $lt_ln;
					$kl .= '</div>';

                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next + 5+$all_step-$marg).'px; left:'.(240+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;">';
                $lt_ln_aw = '';
                if(isset($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner) && !$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->is_extra && !$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner && $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score1 != $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score2){
					$kl .= (isset($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->winner)  && $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==5?14:0))]->m_played)?$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==5?14:0))]->winner:"";
                    $lt_ln_aw = (isset($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->winnerid) && $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==5?14:0))]->m_played )?('<input type="hidden" name="lteams_kn_aw_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="'.$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==5?14:0))]->winnerid.'" />'):('<input type="hidden" name="lteams_kn_aw_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="0" />');
                }   
				if(isset($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score1) && $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score1 == $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score2 && $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet1 != $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet2){
                    $lt_ln_aw = $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet1 > $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet2?$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->home_team.'<input type="hidden" name="lteams_kn_aw_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="'.$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team1_id.'" />':$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->away_team.'<input type="hidden" name="lteams_kn_aw_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="'.$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team2_id.'" />';
				}
				if(!empty($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner)){
                    $lt_ln_aw = $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner == $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team1_id?$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->home_team.'<input type="hidden" name="lteams_kn_aw_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="'.$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team1_id.'" />':$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->away_team.'<input type="hidden" name="lteams_kn_aw_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="'.$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team2_id.'" />';
                }
                    if(!$lt_ln_aw){$lt_ln_aw = '<input type="hidden" name="lteams_kn_aw_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="0" />';}
                    $kl .= $lt_ln_aw;
					$kl .= '</div>';

                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 5+$all_step-$marg).'px; left:'.(240+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;">';
                    $kl .= '<input type="text" name="lres_kn_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="'.((isset($matchDE[$cur_ind]->score1) && $matchDE[$cur_ind]->m_played)?$matchDE[$cur_ind]->score1:"").'" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    $kl .= '</div>';

                    $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20+$all_step-$marg).'px; left:'.(240+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;">';
                    $kl .= '<input type="text" name="lres_kn_'.(($p>=2)?$p+2+$p_step:$p+2).'_aw[]" value="'.((isset($matchDE[$cur_ind]->score2) && $matchDE[$cur_ind]->m_played)?$matchDE[$cur_ind]->score2:"").'" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    $kl .= '<input type="hidden" name="lk_type_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="1"></div>';
                    if(!$type){
                        $match_link = 'index.php?option=com_joomsport&amp;task=match_edit&amp;cid='.(isset($matchDE[$cur_ind]->id)?($matchDE[$cur_ind]->id):'');
                    }else{
                        $match_link = 'index.php?option=com_joomsport&amp;view=edit_match&amp;cid[]='.(isset($match[$cur_ind]->id)?($match[$cur_ind]->id):'').'&amp;controller=admin&amp;sid='.$s_id.'&amp;Itemid='.$Itemid;
                    }
                     $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10+$all_step-$marg).'px; left:'.(-5 + ($p+1)*$wdth - 50+180+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1)).'px;">';
                    if(isset($matchDE[$firstchld_ind + 2+($strafe%3==0?($fid/8):0)]->winnerid) && isset($matchDE[$firstchld_ind + 3+($strafe%3==0?($fid/8):0)]->winnerid) && $matchDE[$firstchld_ind + 2+($strafe%3==0?($fid/8):0)]->m_played && $matchDE[$firstchld_ind + 3+($strafe%3==0?($fid/8):0)]->m_played){
                        $kl .= '<input type="checkbox" name="lkn_match_played_'.$i.'_'.(($p>=2)?$p+2+$p_step:$p+2).'" value="1" '.($matchDE[$cur_ind]->m_played?" checked":"").' />'.$played_tt.'&nbsp;';

                    }
                    $kl .= '<input type="hidden" name="lmatches_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="'.(isset($matchDE[$cur_ind]->id)?$matchDE[$cur_ind]->id:0).'"><a href="'.$match_link.'"><img src="../components/com_joomsport/img/edit.png" width="20" /></a></div>';
                    ////////////////////////two_clon////
                    $kl .= '<div style="position:absolute;width:'.($wdth-103).'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.($i*($height+$step) + $top_next+$top_step+$top+$all_step-$marg).'px; left:'.( ($p)*$wdth+350+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1)).'px;"></div>';
                    ////////////////////////////////
                    //////след шаг!!!
                    $firstchld_ind_end = $i + ($fid/4)*((pow(2,$p)-1)/pow(2,$p-1));
                    if($p!=0){
//echo "<hr>";print_r($matchDE);
                        //$first = $i*2 + ($fid/2)*((pow(2,$p)-1)/pow(2,$p-1));
                        $win_pl = 0;
                        $tab_b = 0;
                        if($fid == 32&&$strafe==4){$win_pl = 6;$tab_b = 3;}
                        else if($fid == 64&&$strafe==4){$win_pl = 12;$tab_b=6;}
                        else if($fid == 64&&$strafe==5){$win_pl = 14;$tab_b=7;}
                        ////////////////////////////////////////////////////
                        if(isset($match[$firstchld_ind_end+($fid/2)]) && isset($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl])){
                            $match[$firstchld_ind_end+($fid/2)] = $this->getMBy($match[$firstchld_ind_end+($fid/2)]);
                            $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl] = $this->getMBy($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]);
                        }
                        ////////////////////////////////////////////////////////
                        $kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next+$all_step-$marg1).'px; left:'.(400+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1)+ ($p)*$wdth).'px;"></div>';
                        $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next - 20+$all_step-$marg1).'px; left:'.(440+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;">';
                    $lt_ln = '';
                    if(isset($match[$firstchld_ind_end+($fid/2)]->p_winner) && !$match[$firstchld_ind_end+($fid/2)]->is_extra && !$match[$firstchld_ind_end+($fid/2)]->p_winner && $match[$firstchld_ind_end+($fid/2)]->score1 != $match[$firstchld_ind_end+($fid/2)]->score2){
						$kl .= (isset($match[$firstchld_ind_end+($fid/2)]->looser)  && $match[$firstchld_ind_end+($fid/2)]->m_played)?$match[$firstchld_ind_end+($fid/2)]->looser:"";
                        $lt_ln = (isset($match[$firstchld_ind_end+($fid/2)]->looserid) && $match[$firstchld_ind_end+($fid/2)]->m_played )?('<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="'.$match[$firstchld_ind_end+($fid/2)]->looserid.'" />'):('<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="0" />');
                    }
					if(isset($match[$firstchld_ind_end+($fid/2)]->score1) && $match[$firstchld_ind_end+($fid/2)]->score1 == $match[$firstchld_ind_end+($fid/2)]->score2 && $match[$firstchld_ind_end+($fid/2)]->aet1 != $match[$firstchld_ind_end+($fid/2)]->aet2){
                        $lt_ln = $match[$firstchld_ind_end+($fid/2)]->aet1 > $match[$firstchld_ind_end+($fid/2)]->aet2?$match[$firstchld_ind_end+($fid/2)]->away_team.'<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="'.$match[$firstchld_ind_end+($fid/2)]->team2_id.'" />':$match[$firstchld_ind_end+($fid/2)]->home_team.'<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="'.$match[$firstchld_ind_end+($fid/2)]->team1_id.'" />';
					}	
					if(!empty($match[$firstchld_ind_end+($fid/2)]->p_winner)){
                        $lt_ln = $match[$firstchld_ind_end+($fid/2)]->p_winner == $match[$firstchld_ind_end+($fid/2)]->team1_id?$match[$firstchld_ind_end+($fid/2)]->away_team.'<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="'.$match[$firstchld_ind_end+($fid/2)]->team2_id.'" />':$match[$firstchld_ind_end+($fid/2)]->home_team.'<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="'.$match[$firstchld_ind_end+($fid/2)]->team1_id.'" />';
					}
                        if(!$lt_ln){$lt_ln = '<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="0" />';}
                        $kl .= $lt_ln;
						$kl .= '</div>';

                        $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next + 5+$all_step-$marg1).'px; left:'.(440+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;">';
                    $lt_ln_aw = '';
                    if(isset($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->p_winner) && !$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->is_extra && !$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->p_winner && $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->score1 != $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->score2){
						$kl .= (isset($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->winner)  && $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->m_played)?$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->winner:""; ///+6 для 32\ 64 - 12
                        $kl .= (isset($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->winnerid) && $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->m_played )?('<input type="hidden" name="lteams_kn_aw_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="'.$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->winnerid.'" />'):('<input type="hidden" name="lteams_kn_aw_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="0" />');
                    }  
					if(isset($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->score1) && $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->score1 == $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->score2 && $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->aet1 != $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->aet2){
						$kl .= $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->aet1 > $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->aet2?$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->home_team.'<input type="hidden" name="lteams_kn_aw_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="'.$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->team1_id.'" />':$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->away_team.'<input type="hidden" name="lteams_kn_aw_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="'.$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->team2_id.'" />';      
					}
					if(!empty($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->p_winner)){
						$kl .= $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->p_winner == $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->team1_id?$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->home_team.'<input type="hidden" name="lteams_kn_aw_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="'.$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->team1_id.'" />':$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->away_team.'<input type="hidden" name="lteams_kn_aw_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="'.$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->team2_id.'" />';
					}
                        if(!$lt_ln_aw){$lt_ln_aw = '<input type="hidden" name="lteams_kn_aw_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="0" />';}
                        $kl .= $lt_ln_aw;
						$kl .= '</div>';

                        $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 5+$all_step-$marg1).'px; left:'.(440+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;">';
                        $kl .= '<input type="text" name="lres_kn_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="'.((isset($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->score1) && $matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->m_played)?$matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->score1:"").'" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                        $kl .= '</div>';

                        $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20+$all_step-$marg1).'px; left:'.(440+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;">';
                        $kl .= '<input type="text" name="lres_kn_'.(($p>=2)?$p+3+$p_step:$p+3).'_aw[]" value="'.((isset($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->score2) && $matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->m_played)?$matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->score2:"").'" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                        $kl .= '<input type="hidden" name="lk_type_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="1"></div>';
//print_r($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)-7]->id);
                        if(!$type){
                            $match_link = 'index.php?option=com_joomsport&amp;task=match_edit&amp;cid='.(isset($matchDE[$cur_ind+1]->id)?($matchDE[$cur_ind+1]->id):'');
                        }else{
                            $match_link = 'index.php?option=com_joomsport&amp;view=edit_match&amp;cid[]='.(isset($match[$cur_ind+1]->id)?($match[$cur_ind+1]->id):'').'&amp;controller=admin&amp;sid='.$s_id.'&amp;Itemid='.$Itemid;
                        }
                        //if(isset($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->id) && isset($match[$firstchld_ind_end+($fid/2)]->looserid) && isset($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->winnerid)){ //or $firstchld_ind_end-4  and $firstchld_ind_end-3
                        $kl .= '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10+$all_step-$marg1).'px; left:'.(-5 + ($p+1)*$wdth - 50+380 + (($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1)).'px;">';
                        if(isset($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->winnerid) && isset($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->winnerid) && $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->m_played && $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->m_played){
							$kl .= '<input type="checkbox" name="lkn_match_played_'.$i.'_'.(($p>=2)?$p+3+$p_step:$p+3).'" value="1" '.($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->m_played?" checked":"").' />'.$played_tt.'&nbsp;';

                        }
                        $kl .= '<input type="hidden" name="lmatches_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="'.(isset($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->id)?$matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->id:0).'">
                                <a href="'.$match_link.'"><img src="../components/com_joomsport/img/edit.png" width="20" /></a></div>';
                    }

                }
            }
            $top_next += $height/2;
            $height = $height + $step;
            $step = $height;
            $zz *= 2;
            $p++;

        }


        $winmd_id = $fid - 3;
        $wiinn = '';
        $winnid = '';
        if(isset($matchDE[$winmd_id])){
            $matchDE[$winmd_id] = $this->getMBy($matchDE[$winmd_id]);
        }
        if(isset($matchDE[$winmd_id]->winner) && $matchDE[$winmd_id]->winner && $matchDE[$winmd_id]->score1 != $matchDE[$winmd_id]->score2 && $matchDE[$winmd_id]->m_played)
        {

            $wiinn = "<div style='margin-left:15px;'>".$matchDE[$winmd_id]->winner."</div>";
			$winnid = '<input type="hidden" name=teams_kn_aw_'.($p_last).'[]" value="'.(isset($matchDE[$winmd_id]->winnerid)?$matchDE[$winmd_id]->winnerid:'').'" />';
        } 
	
		if(isset($matchDE[$winmd_id]->score1) && $matchDE[$winmd_id]->score1 == $matchDE[$winmd_id]->score2 && $matchDE[$winmd_id]->aet1 != $matchDE[$winmd_id]->aet2){
            $wiinn = "<div style='margin-left:15px; '>".($matchDE[$winmd_id]->aet1 > $matchDE[$winmd_id]->aet2?$matchDE[$winmd_id]->home_team:$matchDE[$winmd_id]->away_team)."</div>";
			$winnid = '<input type="hidden" name=teams_kn_aw_'.($p_last).'[]" value="'.($matchDE[$winmd_id]->aet1 > $matchDE[$winmd_id]->aet2?$matchDE[$winmd_id]->team1_id:$matchDE[$winmd_id]->team2_id).'" />';
        }
        if(!empty($matchDE[$winmd_id]->p_winner)){
            $wiinn = "<div style='margin-left:15px; '>".($matchDE[$winmd_id]->p_winner == $matchDE[$winmd_id]->team1_id?$matchDE[$winmd_id]->home_team:$matchDE[$winmd_id]->away_team)."</div>";
			$winnid = '<input type="hidden" name=teams_kn_aw_'.($p_last).'[]" value="'.(isset($matchDE[$winmd_id]->p_winner)?$matchDE[$winmd_id]->p_winner:'').'" />';
        }
        if(!$winnid){$winnid = '<input type="hidden" name=teams_kn_aw_'.($p_last).'[]" value="0" />';}
        if($fid){
            $res = ($p==3)?15:($p==4?115:($p==5?335:0));
            $step_line = 100*($zz/$fid)*$p;
            if(isset($match[$winmd_id+2])){
                $match[$winmd_id+2] = $this->getMBy($match[$winmd_id+2]);
            }
            $kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.( $top_next+$all_step-(($fid/4)==1?15:15*($fid/2))-$res).'px; left:'.($step_line + ($p)*$wdth).'px;">'.$wiinn.'</div>'.$winnid;
            $kl .= '<div style="position:absolute; top:'.( $top_next+$all_step-20-(($fid/4)==1?15:15*($fid/2))-$res).'px; left:'.($step_line + ($p)*$wdth+40).'px;">';
            $kl .= '<input type="text" name="res_kn_'.($p_last).'_aw[]" value="'.((isset($match[$winmd_id+2]->score2) && $match[$winmd_id+2]->m_played)?$match[$winmd_id+2]->score2:"").'" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
            $kl .= '</div>';

            ////////line of winner
		$winner = '';
		if(isset($match[$winmd_id+2]->score1) && $match[$winmd_id+2]->score1 == $match[$winmd_id+2]->score2 && $match[$winmd_id+2]->aet1 != $match[$winmd_id+2]->aet2){
			$winner = '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-bottom:1px solid #aaa; top:'.( (($top_next+$all_step)/3)-(($fid/4)==2?25:15*($fid/4))-$res-(10*$p)).'px; left:'.($step_line + ($p)*$wdth+$wdth).'px;"><div style="margin-left:45px; margin-top:'.((($top_next+$all_step+($fid/4>1?(($fid/8>1)?60*(($fid/4)-1):60):0))/3)).'px;">'.($match[$winmd_id+2]->aet1 > $match[$winmd_id+2]->aet2?$match[$winmd_id+2]->home_team:$match[$winmd_id+2]->away_team).'</div></div>';
			
		}
		if(!empty($match[$winmd_id+2]->p_winner)){
			$winner = '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-bottom:1px solid #aaa; top:'.( (($top_next+$all_step)/3)-(($fid/4)==2?25:15*($fid/4))-$res-(10*$p)).'px; left:'.($step_line + ($p)*$wdth+$wdth).'px;"><div style="margin-left:45px; margin-top:'.((($top_next+$all_step+($fid/4>1?(($fid/8>1)?60*(($fid/4)-1):60):0))/3)).'px;">'.($match[$winmd_id+2]->p_winner == $match[$winmd_id+2]->team1_id?$match[$winmd_id+2]->home_team:$match[$winmd_id+2]->away_team).'</div></div>';
		}
		if(isset($match[$winmd_id+2]->winner) && $match[$winmd_id+2]->winner && $match[$winmd_id+2]->score1 != $match[$winmd_id+2]->score2 && $match[$winmd_id+2]->m_played){
			$winner = '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-bottom:1px solid #aaa; top:'.( (($top_next+$all_step)/3)-(($fid/4)==2?25:15*($fid/4))-$res-(10*$p)).'px; left:'.($step_line + ($p)*$wdth+$wdth).'px;"><div style="margin-left:45px; margin-top:'.((($top_next+$all_step+($fid/4>1?(($fid/8>1)?60*(($fid/4)-1):60):0))/3)).'px;">'.(isset($match[$winmd_id+2])?(($match[$winmd_id+2]->m_played)?$match[$winmd_id+2]->winner:''):'').'</div></div>';
		}
            //$kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-bottom:1px solid #aaa; top:'.( (($top_next+$all_step)/3)-(($fid/4)==2?25:15*($fid/4))-$res-(10*$p)).'px; left:'.($step_line + ($p)*$wdth+$wdth).'px;"><div style="margin-left:45px; margin-top:'.((($top_next+$all_step+($fid/4>1?(($fid/8>1)?60*(($fid/4)-1):60):0))/3)).'px;">'.(isset($match[$winmd_id+2])?(($match[$winmd_id+2]->m_played)?$match[$winmd_id+2]->winner:''):'').'</div></div>';
			$kl .= $winner;
		}
//////////////////////////////////////////////////////////////
        $kl .=  '</div>';
        return $kl;
    }


    public function getMBy($match){
        //print_r($match);
        if(isset($match) && $match->team1_id == -1 && (!empty($match->away_team) || !empty($match->away))){
            $match->winner = (isset($match->away_team))?$match->away_team:$match->away;
            $match->looser = JText::_("BLBE_BYE");
            $match->looserid = -1;
            $match->winnerid = $match->team2_id;
            $match->m_played = 1;

        }
        if(isset($match) && $match->team2_id == -1 && (!empty($match->home_team) || !empty($match->home))){
            $match->winner = isset($match->home_team)?$match->home_team:$match->home;
            $match->winnerid = $match->team1_id;
            $match->looser = JText::_("BLBE_BYE");
            $match->looserid = -1;
            $match->m_played = 1;

        }
        if(isset($match) && $match->team1_id == -1 && $match->team2_id == -1){
            $match->winner = JText::_("BLBE_BYE");
            $match->winnerid = -1;
            $match->m_played = 1;
        }
        return $match;

    }
    //////
    //View
    ////
    function HorKnView($mxl,$match,$k_format,$Itemid,$t_single,$s_id){

        $models = new JSPRO_Models();
        //$models->selectPlayerName($match[$i]);
        if($mxl){
            $reslng = ($mxl)*7+20;
        }else{
            $reslng = 120;
        }
        if($reslng<200) $reslng=120;
        $cfg = new stdClass();
        $cfg->wdth = $reslng+70;
        $cfg->height = 60;
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

        $kl .= '<div class="combine-box-new" style="height:'.(($fid/2)*($height+$step)+60).'px;position:relative;overflow-x:auto;overflow-y:hidden;border:1px solid #ccc;">';


        $link = '';

        while(floor($fid/$zz) >= 1){

            for($i=0;$i<floor($fid/$zz);$i++){

                $kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
                if($t_single && isset($match[$i])){
                    $match[$i]->home = $models->selectPlayerName($match[$i]);
                    $match[$i]->away = $models->selectPlayerName($match[$i],"fn2","ln2","nick2");
                }
                if($p==0){
                    if(isset($match[$i]->hm_id)){
                        if($t_single){
                            $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$i]->hm_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }else{
                            $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$i]->hm_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }
                    }
                    $winclass = "";
                    if(($match[$i]->team2_id == -1 && $match[$i]->team1_id != -1) || ($match[$i]->m_played && (($match[$i]->score1 > $match[$i]->score2) || (($match[$i]->score1 == $match[$i]->score2)&&($match[$i]->aet1 > $match[$i]->aet2)) || (($match[$i]->score1 == $match[$i]->score2)&&($match[$i]->aet1 == $match[$i]->aet2) && ($match[$i]->p_winner == $match[$i]->hm_id))))){
                        $winclass = " knwinner";
                    }
                    $kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $top_next - 14).'px; left:'.(20 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($match[$i]->score1) && $match[$i]->m_played)?$match[$i]->score1.($match[$i]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$match[$i]->aet1."</abbr>)":""):'').'</span>';
                    ////////////////////
                    //if(isset($match[$i]->home)){
                    $kl .= ($match[$i]->team1_id != -1)?("<a href='".$link."' title='".$match[$i]->home."'>".$match[$i]->home."</a>"):(JText::_('BLFA_BYE'));
                    //}else{
                    //$kl .="&nbsp;";
                    //}
                    $kl .= '</div>';
                    if(isset($match[$i]->aw_id) && $match[$i]->team2_id != -1){
                        if($t_single){
                            $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$i]->aw_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }else{
                            $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$i]->aw_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }
                    }
                    if(($match[$i]->m_played || ($match[$i]->team1_id == -1 && $match[$i]->team2_id != -1))  && $winclass == ''){
                        $winclass = " knwinner";
                    }else{
                        $winclass = "";
                    }
                    $kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 13).'px; left:'.(20 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($match[$i]->score2) && $match[$i]->m_played)?$match[$i]->score2.($match[$i]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$match[$i]->aet2."</abbr>)":""):'').'</span>';!
                        //if(isset($match[$i]->away)){
                    $kl .= ($match[$i]->team2_id != -1)?("<a href='".$link."' title='".$match[$i]->away."'>".$match[$i]->away."</a>"):(JText::_('BLFA_BYE'));
                    //}
                    //else{
                    //$kl .="&nbsp;";
                    //}
                    $kl .= '</div>';
                    $match_link = 'index.php?option=com_joomsport&amp;task=view_match&amp;id='.(isset($match[$i]->id)?($match[$i]->id):'').'&amp;Itemid='.$Itemid;
                    $kl .= (isset($match[$i]->id)?'<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10).'px; left:'.(-20 + ($p+1)*$wdth).'px;"><a href="'.$match_link.'" title="'.JText::_('BL_LINK_DETAILMATCH').'"><span class="module-menu-editor"><!-- --></span></a></div>':"");
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
                    if($t_single){
                        if(isset($match[$firstchld_ind])){
                            $match[$firstchld_ind]->home = $models->selectPlayerName($match[$firstchld_ind]);
                            $match[$firstchld_ind]->winner = $models->selectPlayerName($match[$firstchld_ind],"winner","","winner_nick");
                            $match[$firstchld_ind]->away = $models->selectPlayerName($match[$firstchld_ind],"fn2","ln2","nick2");
                        }
                        if(isset($match[$firstchld_ind+1])){
                            $match[$firstchld_ind+1]->home = $models->selectPlayerName($match[$firstchld_ind+1]);
                            $match[$firstchld_ind+1]->away = $models->selectPlayerName($match[$firstchld_ind+1],"fn2","ln2","nick2");
                            $match[$firstchld_ind+1]->winner = $models->selectPlayerName($match[$firstchld_ind+1],"winner","","winner_nick");
                        }
                    }
                    if(isset($match[$firstchld_ind])){
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
                    }
                    if(isset($match[$firstchld_ind+1])){
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

                    $kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $top_next - 15).'px; left:'.(25 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($match[$cur_ind]->score1) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score1.($match[$cur_ind]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$match[$cur_ind]->aet1."</abbr>)":""):"").'</span>';
                    if(isset($match[$firstchld_ind]->winnerid)){
                        if($t_single){
                            $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$firstchld_ind]->winnerid.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }else{
                            $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$firstchld_ind]->winnerid.'&sid='.$s_id.'&Itemid='.$Itemid);
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
                    $kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 15).'px; left:'.(25 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($match[$cur_ind]->score2) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score2.($match[$cur_ind]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$match[$cur_ind]->aet2."</abbr>)":""):"").'</span>';
                    if(isset($match[$firstchld_ind+1]->winnerid)){
                        if($t_single){
                            $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$firstchld_ind+1]->winnerid.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }else{
                            $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$firstchld_ind+1]->winnerid.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }
                    }

                    if(isset($match[$firstchld_ind + 1]->winner) && $match[$firstchld_ind + 1]->m_played){
                        $kl .= ($match[$firstchld_ind + 1]->winner != JText::_("BLFA_BYE"))?("<a href='".$link."' title='".$match[$firstchld_ind+1]->winner."'>".$match[$firstchld_ind+1]->winner."</a>"):($match[$firstchld_ind+1]->winner);
                    }

                    //$kl .= (isset($match[$firstchld_ind + 1]->winner) && $match[$firstchld_ind + 1]->m_played)?("<a href='".$link."' title='".$match[$firstchld_ind+1]->winner."'>".$match[$firstchld_ind+1]->winner."</a>"):"";
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
        if($t_single){
            if(isset($match[$winmd_id])){

                $match[$winmd_id]->winner = $models->selectPlayerName($match[$winmd_id],"winner","","winner_nick");
            }

        }
        //if(isset($match[$winmd_id]->winner) && $match[$winmd_id]->winner && $match[$winmd_id]->score1 != $match[$winmd_id]->score2 && $match[$winmd_id]->m_played)
        //{
            if($t_single){
                $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.(isset($match[$winmd_id]->winnerid)?$match[$winmd_id]->winnerid:'').'&sid='.$s_id.'&Itemid='.$Itemid);
            }else{
                $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.(isset($match[$winmd_id]->winnerid)?$match[$winmd_id]->winnerid:'').'&sid='.$s_id.'&Itemid='.$Itemid);
            }
			
			if(isset($match[$winmd_id]->winner) && $match[$winmd_id]->winner && $match[$winmd_id]->score1 != $match[$winmd_id]->score2 && $match[$winmd_id]->m_played){
				$wiinn = "<div class='field-comb' style='width:".($reslng+40)."px;margin-left:5px !important;margin-top:-17px !important;'><div><div><div class='knres'></div><div class='knlink' style='width:".$reslng."px;'><div><div><a href='".$link."' title='".$match[$winmd_id]->winner."'>".$match[$winmd_id]->winner."</a></div></div></div></div></div></div>";
			}else if(isset($match[$winmd_id]->score1) && $match[$winmd_id]->score1 == $match[$winmd_id]->score2 && $match[$winmd_id]->aet1 != $match[$winmd_id]->aet2){	
				$wiinnid = $match[$winmd_id]->aet1 > $match[$winmd_id]->aet2?$match[$winmd_id]->home:$match[$winmd_id]->away;
				$wiinn = "<div class='field-comb' style='width:".($reslng+40)."px;margin-left:5px !important;margin-top:-17px !important;'><div><div><div class='knres'></div><div class='knlink' style='width:".$reslng."px;'><div><div><a href='".$link."' title='".$wiinnid."'>".$wiinnid."</a></div></div></div></div></div></div>";
			}
			if(!empty($match[$winmd_id]->p_winner)){	
				$wiinnid = $match[$winmd_id]->p_winner == $match[$winmd_id]->team1_id?$match[$winmd_id]->home:$match[$winmd_id]->away;
				$wiinn = "<div class='field-comb' style='width:".($reslng+40)."px;margin-left:5px !important;margin-top:-17px !important;'><div><div><div class='knres'></div><div class='knlink' style='width:".$reslng."px;'><div><div><a href='".$link."' title='".$wiinnid."'>".$wiinnid."</a></div></div></div></div></div></div>";
			}
		//}

        if($fid){
            $kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.( $top_next).'px; left:'.(20 + ($p)*$wdth).'px;">'.$wiinn.'</div>';
        }
        $kl .=  '</div>';
        return $kl;
    }

    function HorKnViewDE($mxl,$match,$matchDE,$k_format,$Itemid,$t_single,$s_id){
        if($mxl){
            $reslng = ($mxl)*7+20;
        }else{
            $reslng = 120;
        }
        if($reslng<200) $reslng=120;

        $models = new JSPRO_Models();

        $cfg = new stdClass();
        $cfg->wdth = $reslng+70;
        $cfg->height = 60;
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

        $kl .= '<div class="combine-box-new" style="height:'.(($fid/2)*($height+$step+50)+360+((($fid/32)>=1)?200:0)).'px;position:relative;overflow-x:auto;overflow-y:hidden;border:1px solid #ccc;">';


        $link = '';

        while(floor($fid/$zz) >= 1){

            for($i=0;$i<floor($fid/$zz);$i++){

                $kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
                if($t_single && isset($match[$i])){
                    $match[$i]->home = $models->selectPlayerName($match[$i]);
                    $match[$i]->away = $models->selectPlayerName($match[$i],"fn2","ln2","nick2");
                }
                if($p==0){
                    if(isset($match[$i]->hm_id)){
                        if($t_single){
                            $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$i]->hm_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }else{
                            $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$i]->hm_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }
                    }
                    $winclass = "";
                    if(($match[$i]->team2_id == -1 && $match[$i]->team1_id != -1) || ($match[$i]->m_played && (($match[$i]->score1 > $match[$i]->score2) || (($match[$i]->score1 == $match[$i]->score2)&&($match[$i]->aet1 > $match[$i]->aet2)) || (($match[$i]->score1 == $match[$i]->score2)&&($match[$i]->aet1 == $match[$i]->aet2) && ($match[$i]->p_winner == $match[$i]->hm_id))))){
                        $winclass = " knwinner";
                    }

                    $kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $top_next - 14).'px; left:'.(20 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($match[$i]->score1) && $match[$i]->m_played)?$match[$i]->score1.($match[$i]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$match[$i]->aet1."</abbr>)":""):'').'</span>';
                    //if(isset($match[$i]->home)){
                    $kl .= ($match[$i]->team1_id != -1)?("<a href='".$link."' title='".$match[$i]->home."'>".$match[$i]->home."</a>"):(JText::_('BLFA_BYE'));
                    //}else{
                    //$kl .="&nbsp;";
                    //}
                    $kl .= '</div>';

                    if(isset($match[$i]->aw_id) && $match[$i]->team2_id != -1){
                        if($t_single){
                            $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$i]->aw_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }else{
                            $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$i]->aw_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }
                    }
                    if(($match[$i]->m_played || ($match[$i]->team1_id == -1 && $match[$i]->team2_id != -1))  && $winclass == ''){
                        $winclass = " knwinner";
                    }else{
                        $winclass = "";
                    }
                    $kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 13).'px; left:'.(20 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($match[$i]->score2) && $match[$i]->m_played)?$match[$i]->score2.($match[$i]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$match[$i]->aet2."</abbr>)":""):'').'</span>';!
                        //if(isset($match[$i]->away)){
                    $kl .= ($match[$i]->team2_id != -1)?("<a href='".$link."' title='".$match[$i]->away."'>".$match[$i]->away."</a>"):(JText::_('BLFA_BYE'));
                    //}
                    //else{
                    //$kl .="&nbsp;";
                    //}
                    $kl .= '</div>';
                    /////////////////
                    $match_link = 'index.php?option=com_joomsport&amp;task=view_match&amp;id='.(isset($match[$i]->id)?($match[$i]->id):'').'&amp;Itemid='.$Itemid;
                    $kl .= (isset($match[$i]->id)?'<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10).'px; left:'.(-20 + ($p+1)*$wdth).'px;"><a href="'.$match_link.'" title="'.JText::_('BL_LINK_DETAILMATCH').'"><span class="module-menu-editor"><!-- --></span></a></div>':"");
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
                    if($t_single){
                        if(isset($match[$firstchld_ind])){
                            $match[$firstchld_ind]->home = $models->selectPlayerName($match[$firstchld_ind]);
                            $match[$firstchld_ind]->winner = $models->selectPlayerName($match[$firstchld_ind],"winner","","winner_nick");
                            $match[$firstchld_ind]->away = $models->selectPlayerName($match[$firstchld_ind],"fn2","ln2","nick2");
                        }
                        if(isset($match[$firstchld_ind+1])){
                            $match[$firstchld_ind+1]->home = $models->selectPlayerName($match[$firstchld_ind+1]);
                            $match[$firstchld_ind+1]->away = $models->selectPlayerName($match[$firstchld_ind+1],"fn2","ln2","nick2");
                            $match[$firstchld_ind+1]->winner = $models->selectPlayerName($match[$firstchld_ind+1],"winner","","winner_nick");
                        }
                    }
                    if(isset($match[$firstchld_ind])){
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
                    }
                    if(isset($match[$firstchld_ind+1])){
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

                    $kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $top_next - 15).'px; left:'.(25 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($match[$cur_ind]->score1) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score1.($match[$cur_ind]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$match[$cur_ind]->aet1."</abbr>)":""):"").'</span>';
                    if(isset($match[$firstchld_ind]->winnerid)){
                        if($t_single){
                            $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$firstchld_ind]->winnerid.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }else{
                            $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$firstchld_ind]->winnerid.'&sid='.$s_id.'&Itemid='.$Itemid);
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
                    $kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 15).'px; left:'.(25 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($match[$cur_ind]->score2) && $match[$cur_ind]->m_played)?$match[$cur_ind]->score2.($match[$cur_ind]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$match[$cur_ind]->aet2."</abbr>)":""):"").'</span>';
                    if(isset($match[$firstchld_ind+1]->winnerid)){
                        if($t_single){
                            $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match[$firstchld_ind+1]->winnerid.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }else{
                            $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match[$firstchld_ind+1]->winnerid.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }
                    }

                    if(isset($match[$firstchld_ind + 1]->winner) && $match[$firstchld_ind + 1]->m_played){
                        $kl .= ($match[$firstchld_ind + 1]->winner != JText::_("BLFA_BYE"))?("<a href='".$link."' title='".$match[$firstchld_ind+1]->winner."'>".$match[$firstchld_ind+1]->winner."</a>"):($match[$firstchld_ind+1]->winner);
                    }

                    //$kl .= (isset($match[$firstchld_ind + 1]->winner) && $match[$firstchld_ind + 1]->m_played)?("<a href='".$link."' title='".$match[$firstchld_ind+1]->winner."'>".$match[$firstchld_ind+1]->winner."</a>"):"";
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
        $w_st = $p!=2?200*($p-2):0;
        $h_st = 60*($height/240);

        $res = ($p==4)?15:($p==5?315:($p==6?735:0));
        if($t_single){
            if(isset($match[$winmd_id])){

                $match[$winmd_id]->winner = $models->selectPlayerName($match[$winmd_id],"winner","","winner_nick");
            }

        }
        //if(isset($match[$winmd_id]->winner) && $match[$winmd_id]->winner && $match[$winmd_id]->score1 != $match[$winmd_id]->score2 && $match[$winmd_id]->m_played)
        //{
		$link_id ='';
		if(isset($match[$winmd_id]->p_winner) && !$match[$winmd_id]->is_extra && !$match[$winmd_id]->p_winner){
				$link_id = $match[$winmd_id]->winnerid;
			}
			if(isset($match[$winmd_id]->score1) && $match[$winmd_id]->score1 == $match[$winmd_id]->score2 && $match[$winmd_id]->aet1 != $match[$winmd_id]->aet2){
				$link_id = ($match[$winmd_id]->aet1 > $match[$winmd_id]->aet2)?$match[$winmd_id]->team1_id:$match[$winmd_id]->team2_id;
			}
			if(!empty($match[$winmd_id]->p_winner)){
				$link_id = $match[$winmd_id]->p_winner == $match[$winmd_id]->team1_id?$match[$winmd_id]->team1_id:$match[$winmd_id]->team2_id;
			}
            if($t_single){
                $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
            }else{
                $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
            }
//print_r($match[$winmd_id+1]); +1 -- delete
        if(isset($match[$winmd_id]->winner) && $match[$winmd_id]->winner && $match[$winmd_id]->score1 != $match[$winmd_id]->score2 && $match[$winmd_id]->m_played)
        {    
			$wiinn = "<div class='field-comb' style='width:".($reslng+40)."px;margin-left:5px !important;margin-top:-17px !important;'>
               <span>".((isset($match[$winmd_id]->score1) && $match[$winmd_id]->m_played && isset($match[$winmd_id+1]->score1))?$match[$winmd_id+1]->score1.($match[$winmd_id]->is_extra?' (<abbr title="'.JText::_("BLFA_TT_AET").'">'.$match[$winmd_id]->aet1.'</abbr>)':''):"")."</span>
                <div><div><div class='knres'></div><div class='knlink' style='width:".$reslng."px;'><div><div><a href='".$link."' title='".$match[$winmd_id]->winner."'>".$match[$winmd_id]->winner."</a></div></div></div></div></div>
            </div>";
			
		}
	
		if(isset($match[$winmd_id]->score1) && $match[$winmd_id]->score1 == $match[$winmd_id]->score2 && $match[$winmd_id]->aet1 != $match[$winmd_id]->aet2)
		{
			$win_player = ($match[$winmd_id]->aet1 > $match[$winmd_id]->aet2?$match[$winmd_id]->home:$match[$winmd_id]->away);
			$wiinn = "<div class='field-comb' style='width:".($reslng+40)."px;margin-left:5px !important;margin-top:-17px !important;'>
               <span>".((isset($match[$winmd_id]->score1) && $match[$winmd_id]->m_played)?$match[$winmd_id]->score1.($match[$winmd_id]->is_extra?' (<abbr title="'.JText::_("BLFA_TT_AET").'">'.$match[$winmd_id]->aet1.'</abbr>)':''):"")."</span>
                <div><div><div class='knres'></div><div class='knlink' style='width:".$reslng."px;'><div><div><a href='".$link."' title='".$win_player."'>".$win_player."</a></div></div></div></div></div>
            </div>";
			
		}
		if(!empty($match[$winmd_id]->p_winner)){
			$wiinn = "<div class='field-comb' style='width:".($reslng+40)."px;margin-left:5px !important;margin-top:-17px !important;'>
               <span>".((isset($match[$winmd_id]->score1) && $match[$winmd_id]->m_played)?$match[$winmd_id]->score1.($match[$winmd_id]->is_extra?' (<abbr title="'.JText::_("BLFA_TT_AET").'">'.$match[$winmd_id]->aet1.'</abbr>)':''):"")."</span>
                <div><div><div class='knres'></div><div class='knlink' style='width:".$reslng."px;'><div><div><a href='".$link."' title='".$match[$winmd_id]->winner."'>".$match[$winmd_id]->winner."</a></div></div></div></div></div>
            </div>";
		}
        //}

        if($fid){

            $kl .= '<div style="position:absolute;width:'.($wdth+30+$w_st).'px;height:'.(($height-$h_st+(30*($fid/4)))-(($fid/4)==1?15:25*($fid/2))-$res).'px; border-top:1px solid #aaa;border-right:1px solid #aaa; top:'.( $top_next).'px; left:'.(20 + ($p)*$wdth).'px;">'.$wiinn.'</div>';




        }
        //$kl .=  '</div>';

        //if($mxl){
        //    $reslng = ($mxl)*7+20;
        //}else{
         //   $reslng = 120;
        //}
        //if($reslng<120) $reslng=120;
        $cfg = new stdClass();
        $cfg->wdth = $reslng+70;
        $cfg->height = 60;
        $cfg->step = 70;
        $cfg->top_next = 50;

        $wdth = $cfg->wdth;
        $height = $cfg->height;
        $step = $cfg->step;
        $top_next = $cfg->top_next;
        $zz = 4;
        $fid = $k_format;

        $p_last = $p+1;

        $p=0;
        $top = 0;
        $p_step = 0;
        $top_step = 60;
        $all_step = (120*($fid/2))+(50*(($fid/4)>4?($fid/8):($fid/4)));
        $step_block = ($fid/$zz>2)?200:0;
        $strafe = 0;
        $first = 0;
        $marg = 30;
        $marg1 = 100;
        $m = -1;

        while(floor($fid/$zz) >= 1){
            $p_step =($p>=2)? $p_step+1:$p_step+0;
            $strafe++;

            if($p==3||$p==4){
                $top =($p==3)? $top+60:$top+210;
            }
            $top_step =($p==0||$p==1)? $top_step + 0: $top_step+60;
            for($i=0;$i<floor($fid/$zz);$i++){



                ////
                if($t_single && isset($match[$i])){
                    $match[$i]->home = $models->selectPlayerName($match[$i]);
                    $match[$i]->away = $models->selectPlayerName($match[$i],"fn2","ln2","nick2");
                }
                ////
                if($p==0){
                    $firstchld_ind = $i*2 + ($fid/2)*((pow(2,$p)-1)/pow(2,$p-1));
                    $cur_ind = $i + ($fid/2)*((pow(2,$p)-1)/pow(2,$p-1));
					if(isset($matchDE[$cur_ind])){
						$matchDE[$cur_ind] = $this->getMBy($matchDE[$cur_ind]);
					}
					if(isset($match[$firstchld_ind])){
						$match[$firstchld_ind] = $this->getMBy($match[$firstchld_ind]);
					}

                    $kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) +$top_next+$all_step).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
                    ///1
                    if(isset($match[$firstchld_ind]->hm_id)){
						if(!$match[$firstchld_ind]->is_extra && !$match[$firstchld_ind]->p_winner && $match[$firstchld_ind]->score1 != $match[$firstchld_ind]->score2){
							$link_id = $match[$firstchld_ind]->looserid;
						}
						if($match[$firstchld_ind]->score1 == $match[$firstchld_ind]->score2 && $match[$firstchld_ind]->aet1 != $match[$firstchld_ind]->aet2){
							$link_id = ($match[$firstchld_ind]->aet1 > $match[$firstchld_ind]->aet2)?$match[$firstchld_ind]->team2_id:$match[$firstchld_ind]->team1_id;
						}
						if($match[$firstchld_ind]->p_winner){
							$link_id = $match[$firstchld_ind]->p_winner == $match[$firstchld_ind]->team1_id?$match[$firstchld_ind]->team2_id:$match[$firstchld_ind]->team1_id;
						}
						
                        if($t_single){
                            $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }else{
                            $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }
                    }
                    $winclass = "";////----

                    if(isset($matchDE[$cur_ind])){
                        if(($matchDE[$cur_ind]->team2_id == -1 && $matchDE[$cur_ind]->team1_id != -1) || ($matchDE[$cur_ind]->m_played && (($matchDE[$cur_ind]->score1 > $matchDE[$cur_ind]->score2) || (($matchDE[$cur_ind]->score1 == $matchDE[$cur_ind]->score2)&&($matchDE[$cur_ind]->aet1 > $matchDE[$cur_ind]->aet2)) || (($matchDE[$cur_ind]->score1 == $matchDE[$cur_ind]->score2)&&($matchDE[$cur_ind]->aet1 == $matchDE[$cur_ind]->aet2) && ($matchDE[$cur_ind]->p_winner == $matchDE[$cur_ind]->hm_id))))){
                            $winclass = " knwinner";
                        }
                    }
                    $kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $top_next - 20+$all_step).'px; left:'.(20 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($matchDE[$cur_ind]->score1) && $matchDE[$cur_ind]->m_played)?$matchDE[$cur_ind]->score1.($matchDE[$cur_ind]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$matchDE[$cur_ind]->aet1."</abbr>)":""):'').'</span>';
                    
					//if($match[$firstchld_ind]->m_played){
                       // $kl .= ($match[$firstchld_ind]->team1_id != -1 && $match[$firstchld_ind]->looser != JText::_('BLFA_BYE'))?("<a href='".$link."' title='".$match[$firstchld_ind]->looser."'>".$match[$firstchld_ind]->looser."</a>"):(JText::_('BLFA_BYE'));
                    //}

					if(!$match[$firstchld_ind]->is_extra && $match[$firstchld_ind]->m_played && !$match[$firstchld_ind]->p_winner && $match[$firstchld_ind]->score1 != $match[$firstchld_ind]->score2 || ($match[$firstchld_ind]->team1_id==-1 || $match[$firstchld_ind]->team2_id==-1)){
						$kl .= ($match[$firstchld_ind]->team1_id != -1 && $match[$firstchld_ind]->looser != JText::_('BLFA_BYE'))?("<a href='".$link."' title='".$match[$firstchld_ind]->looser."'>".$match[$firstchld_ind]->looser."</a>"):(JText::_('BLFA_BYE'));
					}
					if($match[$firstchld_ind]->score1 == $match[$firstchld_ind]->score2 && $match[$firstchld_ind]->aet1 != $match[$firstchld_ind]->aet2){
						$kl .= ($match[$firstchld_ind]->aet1 > $match[$firstchld_ind]->aet2)?("<a href='".$link."' title='".$match[$firstchld_ind]->away."'>".$match[$firstchld_ind]->away."</a>"):("<a href='".$link."' title='".$match[$firstchld_ind]->home."'>".$match[$firstchld_ind]->home."</a>");
					}
					if($match[$firstchld_ind]->p_winner){
						$kl .= ($match[$firstchld_ind]->p_winner == $match[$firstchld_ind]->team1_id)?("<a href='".$link."' title='".$match[$firstchld_ind]->away."'>".$match[$firstchld_ind]->away."</a>"):("<a href='".$link."' title='".$match[$firstchld_ind]->home."'>".$match[$firstchld_ind]->home."</a>");
					}
                    $kl .= '</div>';
                    ////2
                    if(isset($match[$firstchld_ind+1]->aw_id) && $match[$firstchld_ind+1]->team2_id != -1){
                        if(!$match[$firstchld_ind+1]->is_extra && !$match[$firstchld_ind+1]->p_winner && $match[$firstchld_ind+1]->score1 != $match[$firstchld_ind+1]->score2){
							$link_id = $match[$firstchld_ind+1]->looserid;
						}
						if($match[$firstchld_ind+1]->score1 == $match[$firstchld_ind+1]->score2 && $match[$firstchld_ind+1]->aet1 != $match[$firstchld_ind+1]->aet2){
							$link_id = ($match[$firstchld_ind+1]->aet1 > $match[$firstchld_ind+1]->aet2)?$match[$firstchld_ind+1]->team2_id:$match[$firstchld_ind+1]->team1_id;
						}
						if($match[$firstchld_ind+1]->p_winner){
							$link_id = $match[$firstchld_ind+1]->p_winner == $match[$firstchld_ind+1]->team1_id?$match[$firstchld_ind+1]->team2_id:$match[$firstchld_ind+1]->team1_id;
						}
						
						if($t_single){
                            $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }else{
                            $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }
                    }
                    if(isset($matchDE[$cur_ind])){
                        if(($matchDE[$cur_ind]->m_played || ($matchDE[$cur_ind]->team1_id == -1 && $matchDE[$cur_ind]->team2_id != -1))  && $winclass == ''){
                            $winclass = " knwinner";
                        }else{
                            $winclass = "";
                        }
                    }
                    $kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $height + $top_next -7+$all_step).'px; left:'.(20 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($matchDE[$cur_ind]->score2) && $matchDE[$cur_ind]->m_played)?$matchDE[$cur_ind]->score2.($matchDE[$cur_ind]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$matchDE[$cur_ind]->aet2."</abbr>)":""):'').'</span>';
                    
					if(!$match[$firstchld_ind+1]->is_extra && !$match[$firstchld_ind+1]->p_winner && $match[$firstchld_ind+1]->score1 != $match[$firstchld_ind+1]->score2 || ($match[$firstchld_ind+1]->team1_id==-1 || $match[$firstchld_ind+1]->team2_id==-1)){
						$kl .= ($match[$firstchld_ind+1]->looser != JText::_('BLFA_BYE') && ($match[$firstchld_ind+1]->team1_id != -1 && $match[$firstchld_ind+1]->team2_id != -1))?("<a href='".$link."' title='".$match[$firstchld_ind+1]->looser."'>".$match[$firstchld_ind+1]->looser."</a>"):(JText::_('BLFA_BYE'));
					
					}
				
					if($match[$firstchld_ind+1]->score1 == $match[$firstchld_ind+1]->score2 && $match[$firstchld_ind+1]->aet1 != $match[$firstchld_ind+1]->aet2){
						$kl .= ($match[$firstchld_ind+1]->aet1 > $match[$firstchld_ind+1]->aet2)?("<a href='".$link."' title='".$match[$firstchld_ind+1]->away."'>".$match[$firstchld_ind+1]->away."</a>"):("<a href='".$link."' title='".$match[$firstchld_ind+1]->home."'>".$match[$firstchld_ind+1]->home."</a>");
					}
					if($match[$firstchld_ind+1]->p_winner){
						$kl .= ($match[$firstchld_ind+1]->p_winner == $match[$firstchld_ind+1]->team1_id)?("<a href='".$link."' title='".$match[$firstchld_ind+1]->away."'>".$match[$firstchld_ind+1]->away."</a>"):("<a href='".$link."' title='".$match[$firstchld_ind+1]->home."'>".$match[$firstchld_ind+1]->home."</a>");
					}
					
                    $kl .= '</div>';

                    $match_link = 'index.php?option=com_joomsport&amp;task=view_match&amp;id='.(isset($matchDE[$cur_ind]->id)?($matchDE[$cur_ind]->id):'').'&amp;Itemid='.$Itemid;
                    $kl .= (isset($matchDE[$cur_ind]->id)?'<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10+$all_step).'px; left:'.(30 + ($p+1)*$wdth - 50).'px;"><a href="'.$match_link.'" title="'.JText::_('BL_LINK_DETAILMATCH').'"><span class="module-menu-editor"><!-- --></span></a></div>':"");


                    ////////second
                    if(($fid/4)!=1){
                        $firstchld_indDE = ($fid/4) + $i+ (floor(($fid/8)) == 1?0:floor(($fid/8)));
                        $first = (($fid%16)==0 && ($fid/16)!=1)?($fid/16):0;
                        if($first == 4){$first = 6;}
                        $tm_step = ceil($fid/16);

                        $cur_indDE = $cur_ind + floor(($fid/8))+floor($fid/16);
                    }else{
                        $cur_indDE = $cur_ind;
                        $firstchld_indDE = $firstchld_ind;
                        $tm_step = 1;
                    }

                    $kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) +$top_next+$all_step-30).'px; left:'.(220 + ($p)*$wdth).'px;"></div>';
///1
                    if(isset($match[$firstchld_indDE + 2+$first]->aw_id) && $match[$firstchld_indDE + 2+$first]->team2_id != -1){
                        if(!$match[$firstchld_indDE + 2+$first]->is_extra && !$match[$firstchld_indDE + 2+$first]->p_winner && $match[$firstchld_indDE + 2+$first]->score1 != $match[$firstchld_indDE + 2+$first]->score2){
							$link_id = $match[$firstchld_indDE + 2+$first]->looserid;
						}
						if($match[$firstchld_indDE + 2+$first]->score1 == $match[$firstchld_indDE + 2+$first]->score2 && $match[$firstchld_indDE + 2+$first]->aet1 != $match[$firstchld_indDE + 2+$first]->aet2){
							$link_id = ($match[$firstchld_indDE + 2+$first]->aet1 > $match[$firstchld_indDE + 2+$first]->aet2)?$match[$firstchld_indDE + 2+$first]->team2_id:$match[$firstchld_indDE + 2+$first]->team1_id;
						}
						if($match[$firstchld_indDE + 2+$first]->p_winner){
							$link_id = $match[$firstchld_indDE + 2+$first]->p_winner == $match[$firstchld_indDE + 2+$first]->team1_id?$match[$firstchld_indDE + 2+$first]->team2_id:$match[$firstchld_indDE + 2+$first]->team1_id;
						}
						
						if($t_single){
                            $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }else{
                            $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }
                    }
                    if(isset($matchDE[$cur_indDE+$tm_step])){
                        if(($matchDE[$cur_indDE+$tm_step]->score1 > $matchDE[$cur_indDE+$tm_step]->score2) || ($matchDE[$cur_indDE+$tm_step]->m_played || ($matchDE[$cur_indDE+$tm_step]->team1_id == -1 && $matchDE[$cur_indDE+$tm_step]->team2_id != -1))  && $winclass == ''){
                            $winclass = " knwinner";
                        }else{
                            $winclass = "";
                        }
                    }
                    $kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $top_next - 20+$all_step-30).'px; left:'.(220 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($matchDE[$cur_indDE+$tm_step]->score2) && $matchDE[$cur_indDE+$tm_step]->m_played)?$matchDE[$cur_indDE+$tm_step]->score1.($matchDE[$cur_indDE+$tm_step]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$matchDE[$cur_indDE+$tm_step]->aet2."</abbr>)":""):'').'</span>';
                    
					//if(isset($match[$firstchld_indDE + 2+$first])){
                        //$kl .= ($match[$firstchld_indDE + 2+$first]->team2_id != -1)?("<a href='".$link."' title='".$match[$firstchld_indDE + 2+$first]->looser."'>".(($match[$firstchld_indDE + 2+$first]->m_played)?$match[$firstchld_indDE + 2+$first]->looser:'')."</a>"):(JText::_('BLFA_BYE'));
                    //}
						if(isset($match[$firstchld_indDE + 2+$first]->p_winner) && !$match[$firstchld_indDE + 2+$first]->is_extra && !$match[$firstchld_indDE + 2+$first]->p_winner && $match[$firstchld_indDE + 2+$first]->score1 != $match[$firstchld_indDE + 2+$first]->score2){
							$kl .= ($match[$firstchld_indDE + 2+$first]->team1_id != -1 && $match[$firstchld_indDE + 2+$first]->looser != JText::_('BLFA_BYE'))?("<a href='".$link."' title='".$match[$firstchld_indDE + 2+$first]->looser."'>".$match[$firstchld_indDE + 2+$first]->looser."</a>"):(JText::_('BLFA_BYE'));
						}
						if(isset($match[$firstchld_indDE + 2+$first]->score1) && $match[$firstchld_indDE + 2+$first]->score1 == $match[$firstchld_indDE + 2+$first]->score2 && $match[$firstchld_indDE + 2+$first]->aet1 != $match[$firstchld_indDE + 2+$first]->aet2){
							$kl .= ($match[$firstchld_indDE + 2+$first]->aet1 > $match[$firstchld_indDE + 2+$first]->aet2)?("<a href='".$link."' title='".$match[$firstchld_indDE + 2+$first]->away."'>".$match[$firstchld_indDE + 2+$first]->away."</a>"):("<a href='".$link."' title='".$match[$firstchld_indDE + 2+$first]->home."'>".$match[$firstchld_indDE + 2+$first]->home."</a>");
						}
						if(!empty($match[$firstchld_indDE + 2+$first]->p_winner)){
							$kl .= ($match[$firstchld_indDE + 2+$first]->p_winner == $match[$firstchld_indDE + 2+$first]->team1_id)?("<a href='".$link."' title='".$match[$firstchld_indDE + 2+$first]->away."'>".$match[$firstchld_indDE + 2+$first]->away."</a>"):("<a href='".$link."' title='".$match[$firstchld_indDE + 2+$first]->home."'>".$match[$firstchld_indDE + 2+$first]->home."</a>");
						}
                    $kl .= '</div>';
                   ///2
				   
                    if(isset($matchDE[$cur_ind]->aw_id) && $matchDE[$cur_ind]->team2_id != -1){
						//$link_id = '';
                        if(!$matchDE[$cur_ind]->is_extra && !$matchDE[$cur_ind]->p_winner && $matchDE[$cur_ind]->score1 != $matchDE[$cur_ind]->score2 || ($matchDE[$cur_ind]->team1_id==-1||$matchDE[$cur_ind]->team2_id==-1)){
							$link_id = ($match[$firstchld_ind]->team1_id != -1 || $match[$firstchld_ind]->team2_id != -1)?$matchDE[$cur_ind]->winnerid:'-1';
						}
						if($matchDE[$cur_ind]->score1 == $matchDE[$cur_ind]->score2 && $matchDE[$cur_ind]->aet1 != $matchDE[$cur_ind]->aet2){
							$link_id = ($matchDE[$cur_ind]->aet1 > $matchDE[$cur_ind]->aet2)?$matchDE[$cur_ind]->team1_id:$matchDE[$cur_ind]->team2_id;
						}
						if($matchDE[$cur_ind]->p_winner){
							$link_id = $matchDE[$cur_ind]->p_winner == $matchDE[$cur_ind]->team1_id?$matchDE[$cur_ind]->team1_id:$matchDE[$cur_ind]->team2_id;
						}
						
						
						if($t_single){
                            $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }else{
                            $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }
                    }
                    $winclass = "";
                    if(isset($matchDE[$cur_indDE+$tm_step])){
                        if(($matchDE[$cur_indDE+$tm_step]->score1 < $matchDE[$cur_indDE+$tm_step]->score2)||($matchDE[$cur_indDE+$tm_step]->team2_id == -1 && $matchDE[$cur_indDE+$tm_step]->team1_id != -1) || ($matchDE[$cur_indDE+$tm_step]->m_played && (  (($matchDE[$cur_indDE+$tm_step]->score1 == $matchDE[$cur_indDE+$tm_step]->score2)&&($matchDE[$cur_indDE+$tm_step]->aet1 > $matchDE[$cur_indDE+$tm_step]->aet2)) || (($matchDE[$cur_indDE+$tm_step]->score1 == $matchDE[$cur_indDE+$tm_step]->score2)&&($matchDE[$cur_indDE+$tm_step]->aet1 == $matchDE[$cur_indDE+$tm_step]->aet2) && ($matchDE[$cur_indDE+$tm_step]->p_winner == $matchDE[$cur_indDE+$tm_step]->hm_id))))){
                            $winclass = " knwinner";
                        }
                    }

                    $kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $height + $top_next -10+$all_step-30).'px; left:'.(220 + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($matchDE[$cur_indDE+$tm_step]->score1) && $matchDE[$cur_indDE+$tm_step]->m_played)?$matchDE[$cur_indDE+$tm_step]->score2.($matchDE[$cur_indDE+$tm_step]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$matchDE[$cur_indDE+$tm_step]->aet1."</abbr>)":""):'').'</span>';
                    
					//if(isset($match[$firstchld_ind]) && isset($matchDE[$cur_ind])){ //////----
                        //$kl .= ($match[$firstchld_ind]->team1_id != -1)?("<a href='".$link."' title='".$matchDE[$cur_ind]->winner."'>".(($matchDE[$cur_ind]->m_played)?$matchDE[$cur_ind]->winner:'')."</a>"):(JText::_('BLFA_BYE'));
                    //}
					//print_r($match[$cur_ind]);
					if(isset($matchDE[$cur_ind]->is_extra)){
						if(!$matchDE[$cur_ind]->is_extra && !$matchDE[$cur_ind]->p_winner && $matchDE[$cur_ind]->score1 != $matchDE[$cur_ind]->score2 || ($matchDE[$cur_ind]->team1_id==-1||$matchDE[$cur_ind]->team2_id==-1)){
							$kl .= ($match[$firstchld_ind]->team1_id != -1 || $match[$firstchld_ind]->team2_id != -1)?("<a href='".$link."' title='".$matchDE[$cur_ind]->winner."'>".(($matchDE[$cur_ind]->m_played)?$matchDE[$cur_ind]->winner:'')."</a>"):(JText::_('BLFA_BYE'));
						}
					}
						if(isset($matchDE[$cur_ind]->score1) && $matchDE[$cur_ind]->score1 == $matchDE[$cur_ind]->score2 && $matchDE[$cur_ind]->aet1 != $matchDE[$cur_ind]->aet2){
							$kl .= ($matchDE[$cur_ind]->aet1 > $matchDE[$cur_ind]->aet2)?("<a href='".$link."' title='".$matchDE[$cur_ind]->home."'>".$matchDE[$cur_ind]->home."</a>"):("<a href='".$link."' title='".$matchDE[$cur_ind]->away."'>".$matchDE[$cur_ind]->away."</a>");
						}
						if(!empty($matchDE[$cur_ind]->p_winner)){
							$kl .= ($matchDE[$cur_ind]->p_winner == $matchDE[$cur_ind]->team1_id)?("<a href='".$link."' title='".$matchDE[$cur_ind]->home."'>".$matchDE[$cur_ind]->home."</a>"):("<a href='".$link."' title='".$matchDE[$cur_ind]->away."'>".$matchDE[$cur_ind]->away."</a>");
						}
                    $kl .= '</div>';




                    $match_link = 'index.php?option=com_joomsport&amp;task=view_match&amp;id='.(isset($matchDE[$cur_indDE+$tm_step]->id)?($matchDE[$cur_indDE+$tm_step]->id):'').'&amp;Itemid='.$Itemid;
                    $kl .= (isset($matchDE[$cur_ind]->id)?'<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10+$all_step-30).'px; left:'.(-5 + ($p+1)*$wdth +180).'px;"><a href="'.$match_link.'" title="'.JText::_('BL_LINK_DETAILMATCH').'"><span class="module-menu-editor"><!-- --></span></a></div>':"");

                }else{

                    $firstchld_ind = $i*2 + ($fid/4)*((pow(2,$p-1)-1)/pow(2,$p-2));
                    $cur_ind = $i + ($fid/2)*((pow(2,$p)-1)/pow(2,$p-1));

                    $firstchld_ind += (floor(($fid/8)) == 1?0:floor(($fid/8)));

                    $step_block1 = ($p==3||$p==4)?$step_block+($p==4?400:200):0;

                    if($m!=$p){
                        $marg *=$p;
                        $marg1 *=$p;
                        if($p>1){

                            $marg = $marg+($height/8)-($p==3?(30*$p):($p==4?(90*$p)+265:0));
                            $marg1 = $marg1+($height/8)-($p==3?(50*$p)+98:($p==4?(200*$p)+350:0));

                            //$p==3?$marg1 = $marg1-(75):"";
                            //$p==4?$marg1 = $marg1-(375):"";
                        }
                    }
                    $m = $p;

                    $f_num = (($fid/8)>2)?($fid/8):2;
                    if($fid == 64&&$strafe==4){$f_num += 4;}

                    $kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next+$all_step-$marg).'px; left:'.(220+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;"></div>';

                    if(isset($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->hm_id)){
                        if(!$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->is_extra && !$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner && $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score1 != $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score2){
							$link_id = $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->winnerid;
						}
						if($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score1 == $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score2 && $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet1 != $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet2){
							$link_id = ($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet1 > $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet2)?$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team1_id:$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team2_id;
						}
						if($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner){
							$link_id = $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner == $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team1_id?$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team1_id:$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team2_id;
						}
						
						if($t_single){
                            $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }else{
                            $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }
                    }
                    $winclass = "";////----
                    if(isset($matchDE[$cur_ind])){
                        if(($matchDE[$cur_ind]->team2_id == -1 && $matchDE[$cur_ind]->team1_id != -1) || ($matchDE[$cur_ind]->m_played && (($matchDE[$cur_ind]->score1 > $matchDE[$cur_ind]->score2) || (($matchDE[$cur_ind]->score1 == $matchDE[$cur_ind]->score2)&&($matchDE[$cur_ind]->aet1 > $matchDE[$cur_ind]->aet2)) || (($matchDE[$cur_ind]->score1 == $matchDE[$cur_ind]->score2)&&($matchDE[$cur_ind]->aet1 == $matchDE[$cur_ind]->aet2) && ($matchDE[$cur_ind]->p_winner == $matchDE[$cur_ind]->hm_id))))){
                            $winclass = " knwinner";
                        }
                    }
                    $kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $top_next - 20+$all_step-$marg).'px; left:'.(225+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($matchDE[$cur_ind]->score1) && $matchDE[$cur_ind]->m_played)?$matchDE[$cur_ind]->score1.($matchDE[$cur_ind]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$matchDE[$cur_ind]->aet1."</abbr>)":""):'').'</span>';
                    //if(isset($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))])){
                        //$kl .= ($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team1_id != -1)?("<a href='".$link."' title='".$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->winner."'>".(($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->m_played)?$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->winner:' ')."</a>"):(JText::_('BLFA_BYE'));
                    //}
					    if(isset($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner) && !$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->is_extra && !$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner){ //&& $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score1 == $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score2
							$kl .= ($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team1_id != -1)?("<a href='".$link."' title='".$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->winner."'>".(($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->m_played)?$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->winner:' ')."</a>"):(JText::_('BLFA_BYE'));
						}
						if(isset($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score1) && $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score1 == $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score2 && $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet1 != $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet2){
							$kl .= ($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet1 > $matchDE[$$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet2)?("<a href='".$link."' title='".$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->home."'>".$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->home."</a>"):("<a href='".$link."' title='".$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->away."'>".$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->away."</a>");
						}
						if(!empty($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner)){
							$kl .= ($matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner == $matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team1_id)?("<a href='".$link."' title='".$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->home."'>".$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->home."</a>"):("<a href='".$link."' title='".$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->away."'>".$matchDE[$firstchld_ind+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->away."</a>");
						}
					
                    $kl .= '</div>';
                    //2

                    if(isset($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aw_id) && $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team2_id != -1){
                        if(!$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->is_extra && !$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner && $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score1 == $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score2){
							$link_id = $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->winnerid;
						}
						if($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score1 == $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score2 && $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet1 != $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet2){
							$link_id = ($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet1 > $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet2)?$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team1_id:$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team2_id;
						}
						if($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner){
							$link_id = $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner == $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team1_id?$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team1_id:$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team2_id;
						}
						
						if($t_single){
                            $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }else{
                            $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                        }
                    }
                    if(isset($matchDE[$cur_ind])){
                        if(($matchDE[$cur_ind]->m_played || ($matchDE[$cur_ind]->team1_id == -1 && $matchDE[$cur_ind]->team2_id != -1))  && $winclass == ''){
                            $winclass = " knwinner";
                        }else{
                            $winclass = "";
                        }
                    }
                    $kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $height + $top_next -20+$all_step-$marg).'px; left:'.(225+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($matchDE[$cur_ind]->score2) && $matchDE[$cur_ind]->m_played)?$matchDE[$cur_ind]->score2.($matchDE[$cur_ind]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$matchDE[$cur_ind]->aet2."</abbr>)":""):'').'</span>';
                    //if(isset($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))])){
                        //$kl .= ($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team2_id != -1)?("<a href='".$link."' title='".$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->winner."'>".(($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->m_played)?$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->winner:' ')."</a>"):(JText::_('BLFA_BYE'));
                    //}
						if(isset($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner) && !$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->is_extra && !$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner){// && $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score1 == $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score2
							$kl .= ($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team2_id != -1)?("<a href='".$link."' title='".$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->winner."'>".(($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->m_played)?$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->winner:' ')."</a>"):(JText::_('BLFA_BYE'));
						}
						if(isset($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score1) && $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score1 == $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->score2 && $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet1 != $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet2){
							$kl .= ($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet1 > $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->aet2)?("<a href='".$link."' title='".$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->home."'>".$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->home."</a>"):("<a href='".$link."' title='".$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->away."'>".$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->away."</a>");
						}
						if(!empty($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner)){
							$kl .= ($matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->p_winner == $matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->team1_id)?("<a href='".$link."' title='".$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->home."'>".$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->home."</a>"):("<a href='".$link."' title='".$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->away."'>".$matchDE[$firstchld_ind + 1+$f_num+($strafe%3==0?($fid/8):0)+($strafe==4?($strafe+($fid/16)):($strafe==4?14:0))]->away."</a>");
						}
					
                    $kl .= '</div>';

                    $match_link = 'index.php?option=com_joomsport&amp;task=view_match&amp;id='.(isset($matchDE[$cur_ind]->id)?($matchDE[$cur_ind]->id):'').'&amp;Itemid='.$Itemid;
                    $kl .= (isset($matchDE[$cur_ind]->id)?'<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10+$all_step-$marg).'px; left:'.(50 + ($p+1)*$wdth - 50+180+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1)).'px;"><a href="'.$match_link.'" title="'.JText::_('BL_LINK_DETAILMATCH').'"><span class="module-menu-editor"><!-- --></span></a></div>':"");
                    /////---------------------
                    /////////////
                    ///////////////////////////////////
                    //////////////////////////////////////////////
                    $firstchld_ind_end = $i + ($fid/4)*((pow(2,$p)-1)/pow(2,$p-1));
                    if($p!=0){
                        $win_pl = 0;
                        $tab_b = 0;
                        if($fid == 32&&$strafe==4){$win_pl = 6;$tab_b = 3;}
                        else if($fid == 64&&$strafe==4){$win_pl = 12;$tab_b=6;}
                        else if($fid == 64&&$strafe==5){$win_pl = 14;$tab_b=7;}

                        $kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next+$all_step-$marg1).'px; left:'.(420+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;"></div>';
                        //1
                        if(isset($match[$firstchld_ind_end+($fid/2)]->hm_id)){
                            if(!$match[$firstchld_ind_end+($fid/2)]->is_extra && !$match[$firstchld_ind_end+($fid/2)]->p_winner && $match[$firstchld_ind_end+($fid/2)]->score1 != $match[$firstchld_ind_end+($fid/2)]->score2){
								$link_id = $match[$firstchld_ind_end+($fid/2)]->looserid;
							}
							if($match[$firstchld_ind_end+($fid/2)]->score1 == $match[$firstchld_ind_end+($fid/2)]->score2 && $match[$firstchld_ind_end+($fid/2)]->aet1 != $match[$firstchld_ind_end+($fid/2)]->aet2){
								$link_id = ($match[$firstchld_ind_end+($fid/2)]->aet1 > $match[$firstchld_ind_end+($fid/2)]->aet2)?$match[$firstchld_ind_end+($fid/2)]->team2_id:$match[$firstchld_ind_end+($fid/2)]->team1_id;
							}
							if($match[$firstchld_ind_end+($fid/2)]->p_winner){
								$link_id = $match[$firstchld_ind_end+($fid/2)]->p_winner == $match[$firstchld_ind_end+($fid/2)]->team1_id?$match[$firstchld_ind_end+($fid/2)]->team2_id:$match[$firstchld_ind_end+($fid/2)]->team1_id;
							}
							
							if($t_single){
                                $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                            }else{
                                $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
                            }
                        }
                        $winclass = "";////----
                        if(isset($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)])){
                            if(($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->team2_id == -1 && $matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->team1_id != -1) || ($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->m_played && (($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->score1 > $matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->score2) || (($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->score1 == $matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->score2)&&($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->aet1 > $matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->aet2)) || (($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->score1 == $matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->score2)&&($matchDE[$cur_ind]->aet1 == $matchDE[$cur_ind]->aet2) && ($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->p_winner == $matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->hm_id))))){
                                $winclass = " knwinner";
                            }
                        }
                        $kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $top_next - 20+$all_step-$marg1).'px; left:'.(425+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1)+ ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->score1) && $matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->m_played)?$matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->score1.($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->aet1."</abbr>)":""):'').'</span>';
                        //if(isset($match[$firstchld_ind_end+($fid/2)])){
                           // $kl .= ($match[$firstchld_ind_end+($fid/2)]->team1_id != -1)?("<a href='".$link."' title='".$match[$firstchld_ind_end+($fid/2)]->looser."'>".(($match[$firstchld_ind_end+($fid/2)]->m_played)?$match[$firstchld_ind_end+($fid/2)]->looser:' ')."</a>"):(JText::_('BLFA_BYE'));
                        //}
						if(isset($match[$firstchld_ind_end+($fid/2)]->p_winner) && !$match[$firstchld_ind_end+($fid/2)]->is_extra && !$match[$firstchld_ind_end+($fid/2)]->p_winner && $match[$firstchld_ind_end+($fid/2)]->score1 != $match[$firstchld_ind_end+($fid/2)]->score2){
							$kl .= ($match[$firstchld_ind_end+($fid/2)]->team1_id != -1)?("<a href='".$link."' title='".$match[$firstchld_ind_end+($fid/2)]->looser."'>".(($match[$firstchld_ind_end+($fid/2)]->m_played)?$match[$firstchld_ind_end+($fid/2)]->looser:' ')."</a>"):(JText::_('BLFA_BYE'));
						}
						if(isset($match[$firstchld_ind_end+($fid/2)]->score1) && $match[$firstchld_ind_end+($fid/2)]->score1 == $match[$firstchld_ind_end+($fid/2)]->score2 && $match[$firstchld_ind_end+($fid/2)]->aet1 != $match[$firstchld_ind_end+($fid/2)]->aet2){
							$kl .= ($match[$firstchld_ind_end+($fid/2)]->aet1 > $match[$firstchld_ind_end+($fid/2)]->aet2)?("<a href='".$link."' title='".$match[$firstchld_ind_end+($fid/2)]->away."'>".$match[$firstchld_ind_end+($fid/2)]->away."</a>"):("<a href='".$link."' title='".$match[$firstchld_ind_end+($fid/2)]->home."'>".$match[$firstchld_ind_end+($fid/2)]->home."</a>");
						}
						if(!empty($match[$firstchld_ind_end+($fid/2)]->p_winner)){
							$kl .= ($match[$firstchld_ind_end+($fid/2)]->p_winner == $match[$firstchld_ind_end+($fid/2)]->team1_id)?("<a href='".$link."' title='".$match[$firstchld_ind_end+($fid/2)]->away."'>".$match[$firstchld_ind_end+($fid/2)]->away."</a>"):("<a href='".$link."' title='".$match[$firstchld_ind_end+($fid/2)]->home."'>".$match[$firstchld_ind_end+($fid/2)]->home."</a>");
						}
                        $kl .= '</div>';

                        //2
                        if(isset($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)])){
                            if(isset($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->aw_id) && $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->team2_id != -1){
                                if(!$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->is_extra && !$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->p_winner && $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->score1 != $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->score2){
									$link_id = $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->winnerid;
								}
								if($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->score1 == $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->score2 && $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->aet1 != $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->aet2){
									$link_id = ($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->aet1 > $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->aet2)?$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->team2_id:$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->team1_id;
								}
								if($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->p_winner){
									$link_id = $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->p_winner == $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->team1_id?$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->team2_id:$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->team1_id;
								}
								
								if($t_single){
                                    $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->winnerid.'&sid='.$s_id.'&Itemid='.$Itemid);
                                }else{
                                    $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->winnerid.'&sid='.$s_id.'&Itemid='.$Itemid);
                                }
                            }
                            if(($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->m_played || ($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->team1_id == -1 && $matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->team2_id != -1))  && $winclass == ''){
                                $winclass = " knwinner";
                            }else{
                                $winclass = "";
                            }
                        }
                        $kl .= '<div class="field-comb'.$winclass.'" style="position:absolute; top:'.($i*($height+$step) + $height + $top_next -20+$all_step-$marg1).'px; left:'.(425+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;width:'.($reslng+40).'px;"><span>'.((isset($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->score2) && $matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->m_played)?$matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->score2.($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->is_extra?" (<abbr title='".JText::_("BLFA_TT_AET")."'>".$matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->aet2."</abbr>)":""):'').'</span>';
                        //if(isset($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl])){
                            //$kl .= ($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->team2_id != -1)?("<a href='".$link."' title='".$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->winner."'>".(($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->m_played)?$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->winner:' ')."</a>"):(JText::_('BLFA_BYE'));
                        //}
						if(isset($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->p_winner) && !$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->is_extra && !$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->p_winner && $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->score1 != $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->score2){
							$kl .= ($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->team2_id != -1)?("<a href='".$link."' title='".$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->winner."'>".(($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->m_played)?$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->winner:' ')."</a>"):(JText::_('BLFA_BYE'));
						}
						if(isset($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->score1) && $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->score1 == $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->score2 && $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->aet1 != $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->aet2){
							$kl .= ($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->aet1 > $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->aet2)?("<a href='".$link."' title='".$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->home."'>".$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->home."</a>"):("<a href='".$link."' title='".$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->away."'>".$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->away."</a>");
						}
						if(!empty($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->p_winner)){
							$kl .= ($matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->p_winner == $matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->team1_id)?("<a href='".$link."' title='".$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->home."'>".$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->home."</a>"):("<a href='".$link."' title='".$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->away."'>".$matchDE[$firstchld_ind_end+($fid/4)+($strafe%3==0?($fid/8):0)+$win_pl]->away."</a>");
						}
                        $kl .= '</div>';







                        $match_link = 'index.php?option=com_joomsport&amp;task=view_match&amp;id='.(isset($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->id)?($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->id):'').'&amp;Itemid='.$Itemid;
                        $kl .= (isset($matchDE[$cur_ind+($fid/8)-($strafe%3==0?($fid/16):$tab_b)]->id)?'<div style="position:absolute; top:'.($i*($height+$step) + $top_next + $height/2 - 10+$all_step-$marg1).'px; left:'.(50 + ($p+1)*$wdth - 50+380 + (($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1)).'px;"><a href="'.$match_link.'" title="'.JText::_('BL_LINK_DETAILMATCH').'"><span class="module-menu-editor"><!-- --></span></a></div>':"");
                    }

                }

            }
            $top_next += $height/2;
            $height = $height + $step;
            $step = $height;
            $zz *= 2;
            $p++;

        }

//echo $fid;
        $winmd_id = $fid - 3;
        $wiinn = '';

        $res = ($p==3)?15:($p==4?115:($p==5?335:0));
        if($fid){
            $step_line = 100*($zz/$fid)*$p;
        }

        if($t_single){
            if(isset($matchDE[$winmd_id])){

                $matchDE[$winmd_id]->winner = $models->selectPlayerName($matchDE[$winmd_id],"winner","","winner_nick");
            }

        }
       // if(isset($matchDE[$winmd_id]->winner) && $matchDE[$winmd_id]->winner && $matchDE[$winmd_id]->score1 != $matchDE[$winmd_id]->score2 && $matchDE[$winmd_id]->m_played)
        //{
		///////////////////////////////
////////////////////
////////////////
//////////
			if(isset($matchDE[$winmd_id]->p_winner) && !$matchDE[$winmd_id]->is_extra && !$matchDE[$winmd_id]->p_winner){
				$link_id = $matchDE[$winmd_id]->winnerid;
			}
			if(isset($matchDE[$winmd_id]->score1) && $matchDE[$winmd_id]->score1 == $matchDE[$winmd_id]->score2 && $matchDE[$winmd_id]->aet1 != $matchDE[$winmd_id]->aet2){
				$link_id = ($matchDE[$winmd_id]->aet1 > $matchDE[$winmd_id]->aet2)?$matchDE[$winmd_id]->team1_id:$matchDE[$winmd_id]->team2_id;
			}
			if(!empty($matchDE[$winmd_id]->p_winner)){
				$link_id = $matchDE[$winmd_id]->p_winner == $matchDE[$winmd_id]->team1_id?$matchDE[$winmd_id]->team1_id:$matchDE[$winmd_id]->team2_id;
			}
            if($t_single){
                $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
            }else{
                $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
            }
            // $wiinn .="<span>".((isset($match[$winmd_id+2]->score2) && $match[$winmd_id+2]->m_played)?$match[$winmd_id+2]->score2.($match[$winmd_id+2]->is_extra?' (<abbr title="'.JText::_("BLFA_TT_AET").'">'.$match[$winmd_id+2]->aet2.'</abbr>)':''):"")."</span>";
   //print_R($matchDE[$winmd_id]);
		if(isset($match[$winmd_id]->winner) && $match[$winmd_id]->winner &&  !empty($matchDE[$winmd_id]->m_played))//$match[$winmd_id]->score1 != $match[$winmd_id]->score2 &&
        {      
			$wiinn = "<div class='field-comb' style='width:".($reslng+40)."px;margin-left:5px !important;margin-top:-17px !important;'>
               <span>".((isset($match[$winmd_id]->score2) && $match[$winmd_id]->m_played)?$match[$winmd_id+2]->score2.($match[$winmd_id]->is_extra?' (<abbr title="'.JText::_("BLFA_TT_AET").'">'.$match[$winmd_id]->aet2.'</abbr>)':''):"")."</span>
                <div><div><div class='knres'></div><div class='knlink' style='width:".$reslng."px;'><div><div><a href='".$link."' title='".$matchDE[$winmd_id]->winner."'>".$matchDE[$winmd_id]->winner."</a></div></div></div></div></div>
            </div>";
		}
	
		if(isset($match[$winmd_id]->score1) && $match[$winmd_id]->score1 == $match[$winmd_id]->score2 && $match[$winmd_id]->aet1 != $match[$winmd_id]->aet2)
		{
			$win_player = ($match[$winmd_id]->aet1 > $match[$winmd_id]->aet2?$match[$winmd_id]->away:$match[$winmd_id]->home);
			$wiinn = "<div class='field-comb' style='width:".($reslng+40)."px;margin-left:5px !important;margin-top:-17px !important;'>
               <span>".((isset($match[$winmd_id]->score2) && $match[$winmd_id]->m_played)?$match[$winmd_id]->score2.($match[$winmd_id]->is_extra?' (<abbr title="'.JText::_("BLFA_TT_AET").'">'.$match[$winmd_id]->aet2.'</abbr>)':''):"")."</span>
                <div><div><div class='knres'></div><div class='knlink' style='width:".$reslng."px;'><div><div><a href='".$link."' title='".$win_player."'>".$win_player."</a></div></div></div></div></div>
            </div>";
		}
		if(!empty($match[$winmd_id]->p_winner)){
			$wiinn = "<div class='field-comb' style='width:".($reslng+40)."px;margin-left:5px !important;margin-top:-17px !important;'>
               <span>".((isset($match[$winmd_id]->score2) && $match[$winmd_id]->m_played)?$match[$winmd_id]->score2.($match[$winmd_id]->is_extra?' (<abbr title="'.JText::_("BLFA_TT_AET").'">'.$match[$winmd_id]->aet2.'</abbr>)':''):"")."</span>
                <div><div><div class='knres'></div><div class='knlink' style='width:".$reslng."px;'><div><div><a href='".$link."' title='".$matchDE[$winmd_id]->winner."'>".$matchDE[$winmd_id]->winner."</a></div></div></div></div></div>
            </div>";
		}
        //}
        if($fid){
            $match_link = 'index.php?option=com_joomsport&amp;task=view_match&amp;id='.(isset($match[$winmd_id+2]->id)?($match[$winmd_id+2]->id):'').'&amp;Itemid='.$Itemid;
            $kl .= '<div style="position:absolute; top:'.(( ($top_next+$all_step)/2)+($height/2)-($height/4)+10*$p).'px; left:'.($step_line + ($p)*$wdth+$wdth).'px;"><a href="'.$match_link.'" title="'.JText::_('BL_LINK_DETAILMATCH').'"><span class="module-menu-editor"><!-- --></span></a></div>';

            $kl .= '<div style="position:absolute;width:'.($wdth+20).'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.( ($top_next+$all_step)-(($fid/4)==1?15:25*($fid/2))-$res).'px; left:'.(20+$step_line + ($p)*$wdth).'px;">'.$wiinn.'</div>';

        }

        //$kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-bottom:1px solid #aaa; top:'.( ($top_next+$all_step)/3).'px; left:'.($step_line + ($p)*$wdth+$wdth).'px;"><div style="margin-left:45px; margin-top:'.((($top_next+$all_step+($fid/4>1?(($fid/8>1)?60*(($fid/4)-1):60):0))/3)).'px;">'.(($match[$winmd_id+2]->m_played)?$match[$winmd_id+2]->winner:'').'</div></div>';
        //if(isset($match[$winmd_id+2]->winner)){
           // $wiiinnn = "<div class='field-comb' style='width:".($reslng+40)."px;margin-left:-35px !important;margin-top:-17px !important;'><div><div><div class='knres'></div><div class='knlink' style='width:".$reslng."px;'><div><div><a href='".$link."' title='".$match[$winmd_id+2]->winner."'>".$match[$winmd_id+2]->winner."</a></div></div></div></div></div></div>";
        //}
////////////////////WINER!!!!
		
			if(isset($match[$winmd_id+2]->p_winner) && !$match[$winmd_id+2]->is_extra && !$match[$winmd_id+2]->p_winner && $match[$winmd_id+2]->score1 != $match[$winmd_id+2]->score2){
				$link_id = $match[$winmd_id+2]->winnerid;
			}
			if(isset($match[$winmd_id+2]->score1) && $match[$winmd_id+2]->score1 == $match[$winmd_id+2]->score2 && $match[$winmd_id+2]->aet1 != $match[$winmd_id+2]->aet2){
				$link_id = ($match[$winmd_id+2]->aet1 > $match[$winmd_id+2]->aet2)?$match[$winmd_id+2]->team1_id:$match[$winmd_id+2]->team2_id;
			}
			if(!empty($match[$winmd_id+2]->p_winner)){
				$link_id = $match[$winmd_id+2]->p_winner == $match[$winmd_id+2]->team1_id?$match[$winmd_id+2]->team1_id:$match[$winmd_id+2]->team2_id;
			}
            if($t_single){
                $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
            }else{
                $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$link_id.'&sid='.$s_id.'&Itemid='.$Itemid);
            }
		//////
		if(isset($match[$winmd_id+2]->winner) && $match[$winmd_id+2]->winner && $match[$winmd_id+2]->score1 != $match[$winmd_id+2]->score2 && $match[$winmd_id+2]->m_played){
			$wiiinnn = "<div class='field-comb' style='width:".($reslng+40)."px;margin-left:-35px !important;margin-top:-17px !important;'><div><div><div class='knres'></div><div class='knlink' style='width:".$reslng."px;'><div><div><a href='".$link."' title='".$match[$winmd_id+2]->winner."'>".$match[$winmd_id+2]->winner."</a></div></div></div></div></div></div>";
		}
		if(isset($match[$winmd_id+2]->score1) && $match[$winmd_id+2]->score1 == $match[$winmd_id+2]->score2 && $match[$winmd_id+2]->aet1 != $match[$winmd_id+2]->aet2){	
			$win_player = ($match[$winmd_id+2]->aet1 > $match[$winmd_id+2]->aet2?$match[$winmd_id+2]->home:$match[$winmd_id+2]->away);
			$wiiinnn = "<div class='field-comb' style='width:".($reslng+40)."px;margin-left:-35px !important;margin-top:-17px !important;'><div><div><div class='knres'></div><div class='knlink' style='width:".$reslng."px;'><div><div><a href='".$link."' title='".$win_player."'>".$win_player."</a></div></div></div></div></div></div>";
		}
		if(!empty($match[$winmd_id+2]->p_winner)){
			$win_player = ($match[$winmd_id+2]->p_winner==$match[$winmd_id+2]->team1_id?$match[$winmd_id+2]->home:$match[$winmd_id+2]->away);
			$wiiinnn = "<div class='field-comb' style='width:".($reslng+40)."px;margin-left:-35px !important;margin-top:-17px !important;'><div><div><div class='knres'></div><div class='knlink' style='width:".$reslng."px;'><div><div><a href='".$link."' title='".$win_player."'>".$win_player."</a></div></div></div></div></div></div>";
		}
	
	
		
        //$kl .= '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.( ($top_next+$all_step)/2).'px; left:'.($step_line + ($p)*$wdth+$wdth).'px;">'.$wiiinnn.'</div>';
        $kl .= '<div style="position:absolute;width:'.($wdth).'px;height:'.($height).'px; border-bottom:1px solid #aaa; top:'.(( ($top_next+$all_step)/3)-(($fid/4)==2?25:15*($fid/4))-$res-(10*$p)).'px; left:'.(40+$step_line + ($p)*$wdth+$wdth).'px;"><div style="margin-left:45px; margin-top:'.((($top_next+$all_step+($fid/4>1?(($fid/8>1)?60*(($fid/4)-1):60):0))/3)).'px;">'.(isset($match[$winmd_id+2]->m_played)?(($match[$winmd_id+2]->m_played)?$wiiinnn:''):'').'</div></div>';
        $kl .=  '</div>';

        return $kl;
    }


    ///////
    function getFormatkn()
    {
        $fid = JRequest::getVar( 'fr_id', 0, 'GET', 'int' );
        $t_single = JRequest::getVar( 't_single', 0, 'GET', 'int' );
        $s_id = JRequest::getVar( 'sid', 0, 'GET', 'int' );

        $db = JFactory::getDBO();
        if($t_single){
            $query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.first_name";
        }else{
            $query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.t_name";
        }
        $db->setQuery($query);
        $team = $db->loadObjectList();
        $is_team[] = JHTML::_('select.option',  0, ($t_single?JText::_('BLBE_SELPLAYER'):JText::_('BLBE_SELTEAM')), 'id', 't_name' );
        $is_team[] = JHTML::_('select.option',  -1, JText::_('BLBE_BYE'), 'id', 't_name' );
        $teamis = array_merge($is_team,$team);
        $lists['teams_kn'] = JHTML::_('select.genericlist',   $teamis, 'teams_kn[]', 'class="chzn-done" data-chosen="kn" size="1"', 'id', 't_name', 0 );
        $lists['teams_kn_aw'] = JHTML::_('select.genericlist',   $teamis, 'teams_kn_aw[]', 'class="chzn-done" data-chosen="kn" size="1"', 'id', 't_name', 0 );

        $cfg = new stdClass();
        $cfg->wdth = 150;
        $cfg->height = 50;
        $cfg->step = 70;
        $cfg->top_next = 50;


        //print_r($cfg);
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
                    echo '<input type="hidden" name="teams_kn_'.($p+1).'[]" value="0">';
                    echo '<input type="hidden" name="teams_kn_aw_'.($p+1).'[]" value="0">';
                    echo '<input type="text" name="res_kn_'.($p+1).'[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    echo '</div>';
                    echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20).'px; left:'.(60 + ($p)*$wdth).'px;">';
                    echo '<input type="text" name="res_kn_'.($p+1).'_aw[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    echo '<input type="hidden" name="matches_'.($p+1).'[]" value="0">';
                    echo '</div>';
                }
            }
            $top_next += $height/2;
            $height = $height + $step;
            $step = $height;
            $zz *= 2;
            $p++;

            //$z = $z+1;

        }
        //echo $step."step | ".$top_next."top_next | ".$height."height | ".$zz."zz  | ".$p."p";
        if($fid){

            $res = ($p==4)?15:($p==5?115:($p==6?335:0));
            $w_st = $p!=2?200*($p-2):0;
            $h_st = 60*($height/240);
            //echo ($p-1)."<br>".$h_st;
            echo '<div style="position:absolute;width:'.($wdth+30+$w_st).'px;height:'.($height-$h_st-(($fid/4)==1?15:15*($fid/2))-$res).'px; border-top:1px solid #aaa;border-right:1px solid #aaa; top:'.( $top_next).'px; left:'.(20 + ($p)*$wdth).'px;"></div>';
            echo '<div style="position:absolute; top:'.($top_next+5).'px; left:'.(90 + ($p)*$wdth+$w_st).'px;">';
            echo '<input type="text" name="res_kn_'.($p+1).'[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
            echo '<input type="hidden" name="teams_kn_'.($p+1).'[]" value="0">';
            echo '</div>';
        }


        // echo '</div>';

        //////////////////////////////////////////////////////////////////////aaa
        ///////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////
        $cfg = new stdClass();
        $cfg->wdth = 150;
        $cfg->height = 50;
        $cfg->step = 70;
        $cfg->top_next = 50;

        $wdth = $cfg->wdth;
        $height = $cfg->height;
        $step = $cfg->step;
        $top_next = $cfg->top_next;
        $zz = 4;

        $marg = 15;
        $marg1 = 60;
        $m = -1;
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
//echo $p;
                if($p==0){

                    echo '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) +$top_next+$all_step).'px; left:'.(20 + ($p)*$wdth).'px; "></div>';
                    echo '<input type="hidden" name="lmatches_'.($p+1).'[]" value="0">';
                    echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next +5+$all_step).'px; left:'.(20 + ($p)*$wdth).'px;">';
                    echo '<input type="text" name="lres_kn_'.($p+1).'[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    echo '<input type="hidden" name="lteams_kn_'.($p+1).'[]" value="0" />';
                    echo '</div>';

                    echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20+$all_step).'px; left:'.(20 + ($p)*$wdth).'px;">';
                    echo '<input type="text" name="lres_kn_'.($p+1).'_aw[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    echo '<input type="hidden" name="lteams_kn_'.($p+1).'_aw[]" value="0" />';
                    echo '<input type="hidden" name="lk_type_'.($p+1).'[]" value="1">';
                    echo '</div>';
                    //////
                    echo '<div style="position:absolute;width:'.($wdth-120).'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.($i*($height+$step) +$top_next+25+$all_step).'px; left:'.( ($p)*$wdth+170).'px;"></div>';
                    //SECOND
                    echo '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) +$top_next+$all_step-15).'px; left:'.(200 + ($p)*$wdth).'px;"></div>';
                    echo '<input type="hidden" name="lmatches_'.($p+2).'[]" value="0">';
                    echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next +5+$all_step-15).'px; left:'.(240 + ($p)*$wdth).'px;">';
                    echo '<input type="text" name="lres_kn_'.($p+2).'[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    echo '<input type="hidden" name="lteams_kn_'.($p+2).'[]" value="0" />';
                    echo '</div>';

                    echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20+$all_step-15).'px; left:'.(240 + ($p)*$wdth).'px;">';
                    echo '<input type="text" name="lres_kn_'.($p+2).'_aw[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    echo '<input type="hidden" name="lteams_kn_'.($p+2).'_aw[]" value="0" />';
                    echo '<input type="hidden" name="lk_type_'.($p+2).'[]" value="1">';
                    echo '</div>';
                    //}
                }else{
                    //$marg *=2;

                    if($m!=$p){
                        $marg *=$p;
                        $marg1 *=$p;
                        if($p>1){

                            $marg = $marg+($height/8)-($p==3?30*$p:($p==4?90*$p:0));
                            $marg1 = $marg1+($height/8)-($p==3?50*$p:($p==4?200*$p:0));

                            //$p==3?$marg1 = $marg1-(75):"";
                            //$p==4?$marg1 = $marg1-(375):"";
                        }
                    }
                    $m = $p;

                    $step_block1 = ($p==3||$p==4)?$step_block+($p==4?400:200):0;

                    echo '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next+$all_step-$marg).'px; left:'.(200+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;"></div>';

                    echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 5+$all_step-$marg).'px; left:'.(240+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;">';
                    echo '<input type="hidden" name="lmatches_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="0">';
                    echo '<input type="text" name="lres_kn_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    echo '<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="0" />';
                    echo '</div>';
                    echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20+$all_step-$marg).'px; left:'.(240+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;">';
                    echo '<input type="text" name="lres_kn_'.(($p>=2)?$p+2+$p_step:$p+2).'_aw[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    echo '<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+2+$p_step:$p+2).'_aw[]" value="0" />';
                    echo '<input type="hidden" name="lk_type_'.(($p>=2)?$p+2+$p_step:$p+2).'[]" value="1">';
                    echo '</div>';
                    ////
                    echo '<div style="position:absolute;width:'.($wdth-120).'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.($i*($height+$step) + $top_next+$top_step+$top+$all_step-$marg).'px; left:'.( ($p)*$wdth+350+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1)).'px;"></div>';
                    //}
                    if($p!=0){
                        echo '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border:1px solid #aaa; border-left:0px; top:'.($i*($height+$step) + $top_next+$all_step-$marg1).'px; left:'.(400+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1)+ ($p)*$wdth).'px;"></div>';


                        echo '<div style="position:absolute; top:'.($i*($height+$step) + $top_next + 5+$all_step-$marg1).'px; left:'.(440+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;">';
                        echo '<input type="hidden" name="lmatches_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="0">';
                        echo '<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="0" />';
                        echo '<input type="text" name="lres_kn_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';

                        echo '</div>';
                        echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20+$all_step-$marg1).'px; left:'.(440+(($p!=1&&$p!=3&&$p!=4)?$step_block: $step_block1) + ($p)*$wdth).'px;">';
                        echo '<input type="text" name="lres_kn_'.(($p>=2)?$p+3+$p_step:$p+3).'_aw[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                        echo '<input type="hidden" name="lteams_kn_'.(($p>=2)?$p+3+$p_step:$p+3).'_aw[]" value="0" />';
                        echo '<input type="hidden" name="lk_type_'.(($p>=2)?$p+3+$p_step:$p+3).'[]" value="1">';
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
            //echo $p;echo "|";
            $res = ($p==3)?15:($p==4?115:($p==5?335:0));
//echo $marg."  ---   ".$marg1;
            echo '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-top:1px solid #aaa; top:'.( $top_next+$all_step-(($fid/4)==1?15:15*($fid/2))-$res).'px; left:'.($step_line + ($p)*$wdth).'px;"></div>';
            echo '<div style="position:absolute; top:'.( $top_next+$all_step-20-(($fid/4)==1?15:15*($fid/2))-$res).'px; left:'.($step_line + ($p)*$wdth+40).'px;">';
            echo '<input type="text" name="res_kn_'.($var+1).'_aw[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
            echo '<input type="hidden" name="teams_kn_'.($var+1).'_aw[]" value="0" />';
            echo '<input type="hidden" name="matches_'.($var+1).'[]" value="0" />';
            echo '</div>';

            ////////line of winner
            echo '<div style="position:absolute;width:'.$wdth.'px;height:'.($height).'px; border-bottom:1px solid #aaa; top:'.( (($top_next+$all_step)/3)-(($fid/4)==2?15:15*($fid/4))-$res).'px; left:'.($step_line + ($p)*$wdth+$wdth).'px;"></div>';
        }
        echo '</div>';
    }


    function getFormat()
    {
        $fid = JRequest::getVar( 'fr_id', 0, 'GET', 'int' );
        $t_single = JRequest::getVar( 't_single', 0, 'GET', 'int' );
        $s_id = JRequest::getVar( 'sid', 0, 'GET', 'int' );

        $db = JFactory::getDBO();

        if($t_single){
            $query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.first_name";
        }else{
            $query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".($s_id)." ORDER BY t.t_name";
        }
        $db->setQuery($query);
        $team = $db->loadObjectList();
        $is_team[] = JHTML::_('select.option',  0, ($t_single?JText::_('BLBE_SELPLAYER'):JText::_('BLBE_SELTEAM')), 'id', 't_name' );
        $is_team[] = JHTML::_('select.option',  -1, JText::_('BLBE_BYE'), 'id', 't_name' );
        $teamis = array_merge($is_team,$team);
        $lists['teams_kn'] = JHTML::_('select.genericlist',   $teamis, 'teams_kn[]', 'class="chzn-done" data-chosen="kn" size="1"', 'id', 't_name', 0 );
        $lists['teams_kn_aw'] = JHTML::_('select.genericlist',   $teamis, 'teams_kn_aw[]', 'class="chzn-done" data-chosen="kn" size="1"', 'id', 't_name', 0 );

        $cfg = new stdClass();
        $cfg->wdth = 150;
        $cfg->height = 50;
        $cfg->step = 70;
        $cfg->top_next = 50;

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
                    echo '<input type="hidden" name="teams_kn_'.($p+1).'[]" value="0">';
                    echo '<input type="hidden" name="teams_kn_aw_'.($p+1).'[]" value="0">';
                    echo '<input type="text" name="res_kn_'.($p+1).'[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    echo '</div>';
                    echo '<div style="position:absolute; top:'.($i*($height+$step) + $height + $top_next - 20).'px; left:'.(60 + ($p)*$wdth).'px;">';
                    echo '<input type="text" name="res_kn_'.($p+1).'_aw[]" value="" size="10" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                    //if($i>=2){
                        echo '<input type="hidden" name="matches_'.($p+1).'[]" value="0">';
                   // }
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

?>