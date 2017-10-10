<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$document	 = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'modules/mod_js_results/css/mod_js_results.css'); 
$cItemId   = $params->get('customitemid');
$Itemid    = JRequest::getInt('Itemid');

if(!$cItemId){
	$cItemId = $Itemid;
}

$old_date = '';
$old_md   = '' ;

$ssss_id  = $params->get( 'sidgid' );
$ex       = explode('|',$ssss_id );
$s_id     = $ex[0];
require_once('components/com_joomsport/includes/func.php');

// ----------------------------------------------------------------
// Common configuration for all matches - all kind of layout
// ----------------------------------------------------------------

// Configuration of team and emblem 'width' depending of the layout
switch ($result_layout) {
  case 0:  
  case 1:        
  default:
    $width_team   = "45%" ; 
    $width_emblem = "8%" ;
    $colspan      = 3 ;        
    break ;
  case 2:  
    $width_team   = "45%" ; 
    $width_emblem = "45%" ;  // Increase Emblems width when they are alone with score
    $colspan      = 3 ;        
    break ;
  case 3:
  case 4:
    if (!$embl_is) $width_team = "38%" ;   // Increase Teams width when Emblems are not selected
    else $width_team = "30%" ; 
    $width_emblem = "8%" ;
    $colspan      = 6 ;
    break ;
  case 5:
  case 6:
    if (!$embl_is) $width_team = "45%" ;   // Increase Teams width when Emblems are not selected
    else $width_team   = "35%" ;
    $width_emblem = "10%" ;
    $colspan      = 5 ; 
    break ;
  }

// Preparation of CSS style
$style_margin_matchday   = 'style="white-space:nowrap;' ;
$style_margin_matchday  .= ($left_margin_matchday  ? 'padding-left:'.$left_margin_matchday.'px;' : "") ;
$style_margin_matchday  .= ($right_margin_matchday ? 'padding-right:'.$right_margin_matchday.'px;' : "") ;
$style_margin_matchday  .= '"' ;

$style_margin_home_team  = 'style="width:'.$width_team.';' ;
$style_margin_home_team .= ($left_margin_home_team  ? 'padding-left:'.$left_margin_home_team.'px;' : "") ;
$style_margin_home_team .= ($right_margin_home_team ? 'padding-right:'.$right_margin_home_team.'px;' : "") ;
$style_margin_home_team .= '"' ;

$style_margin_away_team  = 'style="width:'.$width_team.';' ;
$style_margin_away_team .= ($left_margin_away_team  ? 'padding-left:'.$left_margin_away_team.'px;' : "") ;
$style_margin_away_team .= ($right_margin_away_team ? 'padding-right:'.$right_margin_away_team.'px;' : "") ;
$style_margin_away_team .= '"' ;

$style_margin_emblem     = 'style="width:'.$width_emblem.';' ;
$style_margin_emblem    .= ($left_margin_emblem  ? 'padding-left:'.$left_margin_emblem.'px;' : "") ;
$style_margin_emblem    .= ($right_margin_emblem ? 'padding-right:'.$right_margin_emblem.'px;' : "") ;
$style_margin_emblem    .= '"' ;

$style_margin_score      = 'style="white-space:nowrap;' ;
$style_margin_score     .= ($left_margin_score  ? 'padding-left:'.$left_margin_score.'px;' : "") ;
$style_margin_score     .= ($right_margin_score ? 'padding-right:'.$right_margin_score.'px;' : "") ;
$style_margin_score     .= '"' ;

$tooltip_home            = "" ;
$tooltip_away            = "" ;

if ( ( ($result_layout == 1) || ($result_layout == 2) ) && (!$embl_is) ) print_r ('Warning : with this layout, the emblems must be selected otherwise the results are not significant') ;

?>
<table align="center" cellpadding="3" border="0" class="jsm_restable">
<?php 
if(count($list)){
  foreach ($list as $match) : 
 
    // ----------------------------------------------------------------
    // Common configuration for one match - all kind of layout
    // ----------------------------------------------------------------
     if ( $tooltip_emblem ) {
       // Generate CSS Emblem tooltip 
       $tooltip_home = 'title="'.htmlspecialchars($match->home).'"' ;
       $tooltip_away = 'title="'.htmlspecialchars($match->away).'"' ;
    }
    
    // Check if teams names are not too long : if YES, insert a 'blank' in team name to generate a carriage return in the layout result
    if ( $team_name_max_length ) {
       // check Home and Away Team names and split if necessary 
	     $match->home = modBlResHelper::checkTEAMLength($match->home, $team_name_max_length);
	     $match->away = modBlResHelper::checkTEAMLength($match->away, $team_name_max_length);
    }
    
    // Generate the hyperlink on teams ?
    if ( $link_team )
    	if ( $single || (isset($match->ssingle) && $match->ssingle) ) {
         $link_team_home_a = '<a href="'.JRoute::_('index.php?option=com_joomsport&task=player&id='.$match->hm_id.'&sid='.($s_id).'&Itemid='.$cItemId).'">'.$match->home.'</a>';
         $link_team_away_a = '<a href="'.JRoute::_('index.php?option=com_joomsport&task=player&id='.$match->aw_id.'&sid='.($s_id).'&Itemid='.$cItemId).'">'.$match->away.'</a>';
			}
			else {
         $link_team_home_a = '<a href="'.JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match->hm_id.'&sid='.($s_id).'&Itemid='.$cItemId).'">'.$match->home.'</a>';					
         $link_team_away_a = '<a href="'.JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match->aw_id.'&sid='.($s_id).'&Itemid='.$cItemId).'">'.$match->away.'</a>';
			}
    else {
      $link_team_home_a = $match->home ;
      $link_team_away_a = $match->away ;
    }

    // Generate the hyperlink on emblems ?
    if ( $link_emblem )
    	if ( $single || (isset($match->ssingle) && $match->ssingle) ) {
         $link_emblem_home_a = 'href="'.JRoute::_('index.php?option=com_joomsport&task=player&id='.$match->hm_id.'&sid='.($s_id).'&Itemid='.$cItemId).'"';         
         $link_emblem_away_a = 'href="'.JRoute::_('index.php?option=com_joomsport&task=player&id='.$match->aw_id.'&sid='.($s_id).'&Itemid='.$cItemId).'"';
			}
			else {
         $link_emblem_home_a = 'href="'.JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match->hm_id.'&sid='.($s_id).'&Itemid='.$cItemId).'"';
         $link_emblem_away_a = 'href="'.JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match->aw_id.'&sid='.($s_id).'&Itemid='.$cItemId).'"';
			}
    else {
      $link_emblem_home_a = "" ;
      $link_emblem_away_a = "" ;
    }

    // Generate the hyperlink on score ?
    if ( $link_score )
     //$link_score_a = "href='index.php?option=com_joomsport&task=view_match&id=$match->id&Itemid=$cItemId'" ;
	  $link_score_a = 'href="'.JRoute::_('index.php?option=com_joomsport&task=view_match&id='.$match->id.'&Itemid='.$cItemId).'"';
    else
      $link_score_a = "" ;

    // Generation of MatchDay reference in the results : Date, MatchDay, Date-Matchday or MatchDay-Date
		$date_matchday_ouput = "" ;
    switch ($matchday_reference) {
      case 0:  // Date alone
      default:
        if ( $old_date != $match->m_date.' '.$match->m_time ) $date_matchday_ouput = date_bl($match->m_date,$match->m_time) ; 
        break;    
      case 1:  // MatchDay name alone
        if ( $old_md != $match->mdid ) $date_matchday_ouput = $match->m_name ; 
        break;
      case 2:  // Date - Matchday output
        if ( ($old_md != $match->mdid) || ($old_date != $match->m_date.' '.$match->m_time) ) {
            $date_matchday_ouput = date_bl($match->m_date,$match->m_time).' - '.$match->m_name ;
        }
        break;
      case 3:  // Matchday - Date output
        if ( ($old_md != $match->mdid) || ($old_date != $match->m_date.' '.$match->m_time) ) {
           $date_matchday_ouput = $match->m_name.' - '.date_bl($match->m_date,$match->m_time) ;
        }
        break;
    }

    $old_date = $match->m_date.' '.$match->m_time;
    $old_md   = $match->mdid ;

    if($single || (isset($match->ssingle) && $match->ssingle)){
	    $match->emb1 = modBlResHelper::getPhoto($match->hm_id);
	    $match->emb2 = modBlResHelper::getPhoto($match->aw_id);
    }

    // --------------------------------------------------------------------------
    // Presentation of match results depending of configuration parameters
    // --------------------------------------------------------------------------
     switch ($result_layout) {
      case 0:  
      case 1:
      case 2:        
      default:
        // ------------------------------------------------------------------------------------
        // Joomsport presentation on several lines
        // Print results layout:  
        // Case 0 : Matchday separation             Case 1 : Matchday separation             
        //          Home_Emblem        Away_Emblem           Home_Emblem  Score  Away_Emblem           
        //          Home_Team   Score  Away_Team             Home_Team           Away_Team
        //
        // Case 2 : Matchday separation
        //          Home_Emblem  Score  Away_Emblem
        // ------------------------------------------------------------------------------------
        
        // Print Matchday separation on first line
          ?>
          <tr>
	          <td colspan="<?php echo $colspan ;?>" class="match_date_<?php echo $align_matchday_ref ;?>" <?php echo $style_margin_matchday ;?>>
		          <?php echo $date_matchday_ouput; ?>
	          </td>
          </tr>
          <?php
    
        // Print Emblems with or without Score on the second line
        if($embl_is){
        ?>
          <tr>    	
			      <td  class="emblem_thome_<?php echo $align_home_emblem ;?>" <?php echo $style_margin_emblem ;?>><!---->
				      <a <?php echo $link_emblem_home_a ;?> >	        
			        <?php 
	               if($match->emb1 && is_file('media/bearleague/'.$match->emb1) && $embl_is){
		               // echo '<img src="'.JURI::base().'media/bearleague/'.$match->emb1.'" '.$tooltip_home.' width="'.$emblem_width.'" height="'.$emblem_height.'">';
					   echo '<img '.getImgPop($match->emb1,5,$emblem_height,$emblem_width).' '.$tooltip_home.'>'; //<div class='team-embl'>
                   }else{echo ($single?'<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/season-list-player-ico.gif" width="30" height="30" alt="">':'<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30px" height="30px" style="max-width: none;" alt="">');}
	            ?>         
		          </a>
            </td>

        <?php
          if ($result_layout != 0){  // Score is on 2nd line only for Case 0
          ?>            
            <td class="score_<?php echo $border_score ;?>" <?php echo $style_margin_score ;?>>
				      <span class="score_<?php echo $border_score ;?>">
 				        <a <?php echo $link_score_a ;?> >
					        <?php 
						        echo "<b class='score-h'>".$match->score1."</b><b> : </b><b class='score-a'>".$match->score2."</b>";
					        ?>
				        </a>
			        </span>	
			      </td>
          <?php
          }
          else {
          	echo "<td>&nbsp;</td>";
          }
          ?>         
			      <td class="emblem_taway_<?php echo $align_away_emblem ;?>" <?php echo $style_margin_emblem ;?> ><!---->
				      <a <?php echo $link_emblem_away_a ;?> >	        
			        <?php 
	               if($match->emb2 && is_file('media/bearleague/'.$match->emb2) && $embl_is){
		               // echo '<img src="'.JURI::base().'media/bearleague/'.$match->emb2.'" '.$tooltip_away.' width="'.$emblem_width.'" height="'.$emblem_height.'">';
					   echo '<img '.getImgPop($match->emb2,5,$emblem_height,$emblem_width).' '.$tooltip_away.'>';
                   }else{echo ($single?'<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/season-list-player-ico.gif" width="30" height="30" alt="">':'<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30px" height="30px" style="max-width: none;" alt="">');}
	            ?>         
		          </a>
            </td>
          </tr>
    
        <?php 
        }
        
        // Print Teams Names with or without Score on the third line 
        if ( $result_layout != 2 ) {  // Print Team names only for Cases 0 and 1
        ?>
        <tr>
			    <td class="team_thome_<?php echo $align_home_team ;?>" <?php echo $style_margin_home_team ;?>>
			      <?php echo $link_team_home_a ; ?>
			    </td>
			    
        <?php
          if ($result_layout == 1){
          	echo "<td>&nbsp;</td>";          	
          }
          else {
          ?>         
            <td class="score_<?php echo $border_score ;?>" <?php echo $style_margin_score ;?>>
				      <span class="score_<?php echo $border_score ;?>">
 				        <a <?php echo $link_score_a ;?> >
					        <?php 
						        echo "<b class='score-h'>".$match->score1."</b><b> : </b><b class='score-a'>".$match->score2."</b>";
					        ?>
				        </a>
			        </span>	
			      </td>
          <?php
          }
          ?>      
			    
			    <td class="team_taway_<?php echo $align_away_team ;?>" <?php echo $style_margin_away_team ;?>>			
			      <?php echo $link_team_away_a ; ?>
			    </td>
		    </tr>
      <?php
        }
        break;
        
      case 3:  
      case 5:  
        // -------------------------------------------------------------------------------------------------
        // Case : 3 -> Results on one line  and emblems around the score
        // Print results layout:  
        //             Matchday_Separation  Home_Team  Home_Emblems  Score  Aways_Emblem  Away_Team        		    
        //
        // Case : 5 -> Results on two lines and emblems around the score 
        // Print results layout:          
        //             Matchday_Separation         		    
        //             Home_Team  Home_Emblems  Score  Aways_Emblem  Away_Team        		    
        // -------------------------------------------------------------------------------------------------

        // Print Matchday separation in first        
		    ?>
		    <tr>
	        <td colspan="<?php echo $colspan ;?>" class="match_date_<?php echo $align_matchday_ref ;?>" <?php echo $style_margin_matchday ;?>>
		        <?php echo $date_matchday_ouput; ?>
	        </td>		    
        <?php
  		    
		    if ( $result_layout == 5 ) {  // results on two lines -> skip to next line
           echo "</tr><tr>";    		    
        }

        // Print results : Home_Team  Home_Emblems  Score  Aways_Emblem  Away_Team        		    
        ?>		
			    <td class="team_thome_<?php echo $align_home_team ;?>" <?php echo $style_margin_home_team ;?>>
			    <?php echo $link_team_home_a ; ?>
			    </td>

        <?php
          if ($embl_is){
          ?>
			    <td  class="emblem_thome_<?php echo $align_home_emblem ;?>" <?php echo $style_margin_emblem ;?> ><!---->
				    <a <?php echo $link_emblem_home_a ;?> >	        
			      <?php 
	             if($match->emb1 && is_file('media/bearleague/'.$match->emb1) && $embl_is){
		            // echo '<img src="'.JURI::base().'media/bearleague/'.$match->emb1.'" '.$tooltip_home.' height="'.$emblem_height.'" width="'.$emblem_width.'" />';
					echo '<img '.getImgPop($match->emb1,5,$emblem_height,$emblem_width).' '.$tooltip_home.'>';
                 }else{echo ($single?'<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/season-list-player-ico.gif" width="30" height="30" alt="">':'<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30px" height="30px" style="max-width: none;" alt="">');}
	          ?>         
		        </a>
          </td>
          <?php
          }
          ?>
			    
			    <td class="score_<?php echo $border_score ;?>" <?php echo $style_margin_score ;?>  >
				    <span class="score_<?php echo $border_score ;?>">
 				      <a <?php echo $link_score_a ;?> >
					      <?php 
						       echo "<b class='score-h'>".$match->score1."</b><b> : </b><b class='score-a'>".$match->score2."</b>";
					      ?>
				      </a>
			      </span>	
			    </td>

        <?php
          if ($embl_is){
          ?>
			    <td class="emblem_taway_<?php echo $align_away_emblem ;?>" <?php echo $style_margin_emblem ;?>> 
				    <a <?php echo $link_emblem_away_a ;?> >	        
			      <?php 
	             if($match->emb2 && is_file('media/bearleague/'.$match->emb2) && $embl_is){
		            // echo '<img src="'.JURI::base().'media/bearleague/'.$match->emb2.'" '.$tooltip_away.' width="'.$emblem_width.'" height="'.$emblem_height.'">';
					echo '<img '.getImgPop($match->emb2,5,$emblem_height,$emblem_width).' '.$tooltip_away.'>';
                 }else{echo ($single?'<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/season-list-player-ico.gif" width="30" height="30" alt="">':'<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30px" height="30px" style="max-width: none;" alt="">');}
	          ?>         
		        </a>
          </td>
          <?php
          }
          ?>

			    <td class="team_taway_<?php echo $align_away_team ;?>" <?php echo $style_margin_away_team ;?>>			
			      <?php echo $link_team_away_a ; ?>
			    </td>
		    </tr>
        <?php      
        break;

      case 4:  
      case 6:  
        // -------------------------------------------------------------------------------------------------
        // Case : 4 -> Results on one line  and emblems around the teams
        // Print results layout:          
        //             Matchday_Separation  Home_Emblem  Home_Team  Score  Away_Team  Aways_Emblem
        // Case : 6 -> Results on two lines and emblems around the teams
        // Print results layout:          
        //             Matchday_Separation         		    
        //             Home_Emblem  Home_Team  Score  Away_Team  Aways_Emblem
        // -------------------------------------------------------------------------------------------------

       // Print Matchday separation in first         
		    ?>
		    <tr>
	        <td colspan="<?php echo $colspan ;?>" class="match_date_<?php echo $align_matchday_ref ;?>" <?php echo $style_margin_matchday ;?> >
		          <?php echo $date_matchday_ouput; ?>
	        </td>		    
        <?php

		    if ( $result_layout == 6 ) { // results on two lines -> skip to next line
           echo "</tr><tr>";   		    	 
        }

        // Print results : Home_Emblems  Home_Team  Score  Away_Team  Aways_Emblem
        if ($embl_is){
        ?>		      
			    <td class="emblem_thome_<?php echo $align_home_emblem ;?>" <?php echo $style_margin_emblem ;?>> <!---->
				    <a <?php echo $link_emblem_home_a ;?> >	        
			      <?php 
	             if($match->emb1 && is_file('media/bearleague/'.$match->emb1) && $embl_is){
		             //echo '<img src="'.JURI::base().'media/bearleague/'.$match->emb1.'" '.$tooltip_home.' width="'.$emblem_width.'" height="'.$emblem_height.'">';
					 echo '<img '.getImgPop($match->emb1,5,$emblem_height,$emblem_width).' '.$tooltip_home.'>';
                 }else{echo ($single?'<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/season-list-player-ico.gif" width="30" height="30" alt="">':'<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30px" height="30px" style="max-width: none;" alt="">');}
	          ?>         
		        </a>
          </td>
        <?php
        }
        ?>
 			    <td class="team_thome_<?php echo $align_home_team ;?>" <?php echo $style_margin_home_team ;?>>
			      <?php echo $link_team_home_a ; ?>
			    </td>

			    <td class="score_<?php echo $border_score ;?>" <?php echo $style_margin_score ;?>>
				    <span class="score_<?php echo $border_score ;?>">
 				      <a <?php echo $link_score_a ;?> >
					      <?php 
						       echo "<b class='score-h'>".$match->score1."</b><b> : </b><b class='score-a'>".$match->score2."</b>";
					      ?>
				      </a>
			      </span>	
			    </td>

			    <td class="team_taway_<?php echo $align_away_team ;?>" <?php echo $style_margin_away_team ;?>>			
			      <?php echo $link_team_away_a ; ?>
			    </td>

        <?php      
        if ($embl_is){
          ?>		      
			    <td class="emblem_taway_<?php echo $align_away_emblem ;?>" <?php echo $style_margin_emblem ;?>> <!-- -->
				    <a <?php echo $link_emblem_away_a ;?> >	        
			      <?php 
	             if($match->emb2 && is_file('media/bearleague/'.$match->emb2) && $embl_is){
		              //echo '<img src="'.JURI::base().'media/bearleague/'.$match->emb2.'" '.$tooltip_away.' width="'.$emblem_width.'" height="'.$emblem_height.'">';
					 echo '<img '.getImgPop($match->emb2,5,$emblem_height,$emblem_width).' '.$tooltip_away.'>';
                 }else{echo ($single?'<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/season-list-player-ico.gif" width="30" height="30" alt="">':'<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30px" height="30px" style="max-width: none;" alt="">');}
	          ?>         
		        </a>
          </td>
          <?php
        }
        ?>          
		    </tr>
        <?php      
        break;
     }      
  endforeach; 
}
?>
</table>
