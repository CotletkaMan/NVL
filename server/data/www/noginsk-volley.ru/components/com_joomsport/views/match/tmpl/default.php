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
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

	if(isset($this->message)){
		$this->display('message');
	}
	$Itemid = JRequest::getInt('Itemid');
    JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
	$lists = $this->lists;
?>
<script type="text/javascript">

function delCom(num){
    
        jQuery.post(
            'index.php?tmpl=component&option=com_joomsport&task=del_comment&no_html=1&cid='+num,
            function( result ) { 
                if(result){
                    alert(result);
                } else {
                    var d = document.getElementById('divcomb_'+num).parentNode;
                    d.removeChild(jQuery('#divcomb_'+num).get(0));
                }
        });

}
function resetPoints(el){
    if (jQuery(el).is(':checked')){
        jQuery('input', jQuery(el).parent()).removeAttr('disabled');
    } else {
        jQuery('input', jQuery(el).parent()).attr('disabled', 'disabled');
    }
}
</script>

<?php
echo $lists["panel"];
$match = $this->lists["match"];
?>
<!-- <module middle> -->
			<div class="module-middle solid">
				
				<!-- <back box> -->
				<div class="back dotted">
					<a href="javascript:void(0);" onclick="history.back(-1);" title="<?php echo JText::_("BL_BACK")?>">&larr; <?php echo JText::_("BL_BACK")?></a>
					<div class="div_for_socbut">
						<?php echo $this->lists['socbut'];?>
					</div>
					<div class="clear"></div>
				</div>
				<!-- </back box> -->
				
				<!-- <title box> -->
				<div class="title-box">
					
					
					<h2 class="result-box-date">
						<span itemprop="name">
						<?php
						if($match->m_date){

                            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$match->m_date))
                            {
                                echo $this->formatDate($match->m_date . ' ' . $match->m_time);
                            }else{
                                echo $match->m_date;
                            }
						}
						?>
						</span>
					</h2>
					<?php
					if($match->m_location || $match->venue_id){
						echo '<h3 class="result-box-stadium">';
						echo getJS_Location($match->id);
						echo '</h3>';
					}	
					?>
					
				</div>
				<!-- </div>title box> -->
				
				<!-- <tab box> -->
				<ul class="tab-box">
					<?php 
					
					 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_joomsport'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'tabs.php');
					 $etabs = new esTabs();
					  echo $etabs->newTab(JText::_('BL_TAB_MATCH'),'etab_match','star','vis');
					  $how_rowst_k = (count($this->lists['squard1']) > count($this->lists['squard2']))?count($this->lists['squard1']):count($this->lists['squard2']);
					  if($how_rowst_k){
						echo $etabs->newTab(JText::_('BL_TAB_SQUAD'),'etab_squad','players');
					  }
					  if($match->match_descr){
						echo $etabs->newTab(JText::_('BL_TAB_ABOUT'),'etab_descr');
					  }
					  if(count($this->lists["photos"])){
						echo $etabs->newTab(JText::_('BL_TAB_PHOTOS'),'etab_photos','photo');
					  }
					  
					?>
				</ul>
				<!-- </tab box> -->
				
			</div>
			<!-- </module middle> -->

<!-- <content module> -->
<div class="content-module padd-off">
	<form name="adminForm" id="adminForm" action="" method="post">
	<div id="etab_match_div" class="tabdiv">
			
				
				<!-- <Result box> -->
				<div class="result-box">
					<table class="match-day" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td class="team-h-l"><span><?php echo $match->team1_id>0?$match->home:($match->team1_id==-1?'BYE':'&nbsp;');?></span></td>
							<td class="team-ico-h-l">
								<div class="team-embl" style="display: table;">
								<?php
							
								if(!$this->lists['t_single']){
									
                                    echo JHtml::_('images.getTeamEmbl',  $match->team1_id,1,$match->home);
                                }else{ //player
									
                                    echo JHtml::_('images.getPlayerThumb',  $match->team1_id,0,$match->home);
								}
								
								?>
								</div>
							</td>
							<td class="score">
								<span class="score">
									<b class="score-h">
									<?php echo ($match->m_played?$match->score1:'-')?>
									</b>
									<b>:</b>
									<b class="score-a">
									<?php echo ($match->m_played?$match->score2:'-');?>
									</b>
									
									<?php

									if($match->m_played){
										$etclass = ($match->score1 > $match->score2)?"extra-time-h":"extra-time-g";
											if($match->score1 == $match->score2){
												$etclass = ($match->aet1 > $match->aet2)?"extra-time-h":"extra-time-g";
												if($match->aet1 == $match->aet2 && $match->p_winner){
													$etclass = ($match->p_winner == $match->team1_id)?"extra-time-h":"extra-time-g";
												}
											}

										if((@$lists["enbl_extra"] || $match->s_id == -1) && $match->is_extra){

												if($match->p_winner || $match->aet1 != $match->aet2 || $match->score1 != $match->score2){
													echo $match->p_winner?'':'<div class="'.$etclass.'" title="'.JText::_('BLFA_TEAM_WON_ET').'">'.JText::_('BL_RES_EXTRA').'</div>';
												}
											
										}
										if($match->p_winner){
											echo "<div class='".$etclass."' title='".JText::_('W_TT')."'>".JText::_('W')."</div>";
										}
										
									}
									?>
								</span>
                                <?php if($match->m_played){ ?>
								<div style="text-align:center;" title="<?php echo JText::_('BLFA_BONUS');?>">
									<?php
										echo (($match->bonus1!= '0.00' || $match->bonus2 != '0.00')?"<font style='font-size:75%;'>".floatval($match->bonus1).":</font>":"");
										echo (($match->bonus1!= '0.00' || $match->bonus2 != '0.00')?"<font style='font-size:75%;'>".floatval($match->bonus2)."</font>":"");
									?>
								</div>
                                <?php } ?>
								<?php //$match->is_extra
								if($lists["t_type"] == 1 && $match->is_extra && ($match->p_winner || $match->aet1 == $match->aet2 || $match->aet1 != $match->aet2) && $match->m_played){
								?>
								<span class="score" style="margin-top:15px;">
									<b class="score-h">
									<?php echo $match->aet1;?>
									</b>
									<b>:</b>
									<b class="score-a">
									<?php echo $match->aet2;?>
									</b>
									<?php
									$etclass="extra-time-g";
									
									if($match->p_winner == $match->hm_id){
										$etclass="extra-time-h";
									}
									echo "<div class='extra-time-aet' title='".JText::_('BLFA_TT_AET')."'>".JText::_('BLFA_ET')."</div>";
									if($match->p_winner){
										//echo "<div class='".$etclass."' title='".JText::_('W_TT')."'>".JText::_('W')."</div>";
									}?>
									
								</span>
								<?php } ?>
							</td>
							<td class="team-ico-a">
								<div class="team-embl" style="display: table;">
								<?php
								if(!$this->lists['t_single']){
									
                                    echo JHtml::_('images.getTeamEmbl',  $match->team2_id,1,$match->away);
								}else{
									
                                    echo JHtml::_('images.getPlayerThumb',  $match->team2_id,0,$match->away);
								}
								
								?>
								</div>
							</td>
							<td class="team-a"><span><?php echo $match->team2_id>0?$match->away:($match->team2_id==-1?'BYE':'&nbsp;');?></span></td>
							
						</tr>
						<tr>
							<td colspan="4">
								<?php
									if (isset($match->betavailable) && isset($match->betfinish) && $match->betavailable && $match->betfinish && !$match->m_played){
									?>
									<table class="bettable" width="100%">
										<tr>
											<th align="right"><?php echo JText::_('BLFA_BET_COEFF')?>/<?php echo JText::_('BLFA_BET_PT')?></th>
											<th></th>
											<th><?php echo JText::_('BLFA_BET_COEFF')?>/<?php echo JText::_('BLFA_BET_PT')?></th>
										</tr>
										<?php foreach($this->lists["betevents"] as $event):?>
										<?php if ($event->coeff1 || $event->coeff2):?>
										<tr>
											<td align="right">
											<?php 
											if ($event->coeff1){
												?>
												<input type="radio" name="betevents_radio[<?php echo $match->id?>][<?php echo $event->id?>]" onChange="resetPoints(this)"/>
												<?php echo $event->coeff1?>
												<input type="text" disabled="true" size="3" name="betevents_points1[<?php echo $match->id?>][<?php echo $event->id?>]"/>
												<?php
											}
											?>
											</td>
											<td align="center">
												<?php if ($event->type=='simple' || $event->type=='default'):?>
													<?php echo $event->name?>
												<?php else:?>
													<?php echo $event->difffrom?$event->difffrom.' < ':'' ?>
													DIFF
													<?php echo $event->diffto?' < '.$event->diffto:'' ?>
												<?php endif;?>
											</td>
											<td>
											<?php 
											if ($event->coeff2){
												?>
												<input type="radio" name="betevents_radio[<?php echo $match->id?>][<?php echo $event->id?>]" onChange="resetPoints(this)"/>
												<?php echo $event->coeff2?>
												<input type="text" disabled="true" name="betevents_points2[<?php echo $match->id?>][<?php echo $event->id?>]"/>
												<?php
											}
											?>                                    
											</td>
										</tr>
										<?php endif;?>
										<?php endforeach;?>
										<tr>
											<td colspan="3" align="center">
												<input type="hidden" name="bet_match[]" value="<?php echo $match->id?>"/>
												<input type="button" value="<?php echo JText::_("BLFA_BET_SUBMIT_BET");?>" onClick="document.adminForm.task.value = 'bet_match_save';document.adminForm.submit();"/>
											</td>
										</tr>
									</table>
									<?php
									}
									?>
							</td>
						</tr>
						
						<!-- MAPS -->
						<?php

						if(count($this->lists['maps'])){
							for($i=0;$i<count($this->lists['maps']);$i++){
								if(isset($this->lists['maps'][$i]->m_score1) && isset($this->lists['maps'][$i]->m_score2)){
									$mpz = '<b title="'.htmlspecialchars($this->lists['maps'][$i]->map_descr).'">'.$this->lists['maps'][$i]->m_name.'</b>';
									if($this->lists['maps'][$i]->map_img && is_file('media/bearleague/'.$this->lists['maps'][$i]->map_img)){
										$mpz = '<a rel="lightbox-mapsport" title="'.htmlspecialchars($this->lists['maps'][$i]->map_descr).'" href="'.getImgPop($this->lists['maps'][$i]->map_img).'" class="team-images"><b>'.$this->lists['maps'][$i]->m_name.'</b></a>';
									}
									echo "<tr>";
									echo "<td align='right'>".$mpz."</td>";
									?>
										<td class="team-ico-h-l"><!-- --></td>
										<td class="score">
											<span class="score-small" style="cursor:default;">
												<b class="score-h">
												<?php echo (isset($this->lists['maps'][$i]->m_score1)?$this->lists['maps'][$i]->m_score1."":"")?>
												</b>
												<b>:</b>
												<b class="score-a">
												<?php echo (isset($this->lists['maps'][$i]->m_score2)?$this->lists['maps'][$i]->m_score2:"");?>
												</b>
											</span>
											
										</td>
										
									</tr>
								<?php
								}
							}
						}
						?>
						
					</table>
					
				</div>
				<!-- </Result box> -->
				
				
				
						
					<table border="0" cellpadding="5" cellspacing="0" width="100%" class="season-list">

					<?php
					$prev_id = 0;
					$ev_count = (count($this->lists["m_events_home"]) > count($this->lists["m_events_away"])) ? (count($this->lists["m_events_home"])) : (count($this->lists["m_events_away"]));
                    if($ev_count){
                        echo '<tr><th colspan="6" class="teams_stats"><h3>'.JText::_('BL_PBL_STAT').'</h3></th></tr>';
                    }
                     for($i=0;$i<$ev_count;$i++){
					?>
                    
					<tr class="<?php echo $i%2?"gray":"yellow";?>">
						<?php
						if(isset($this->lists["m_events_home"][$i])){
							echo '<td class="home_event" width="40%">';
							if($this->lists["m_events_home"][$i]->e_img && is_file('media/bearleague/events/'.$this->lists["m_events_home"][$i]->e_img)){
								// echo '<img height="15" src="'.JURI::base().'media/bearleague/events/'.$this->lists["m_events_home"][$i]->e_img.'" title="'.$this->lists["m_events_home"][$i]->e_name.'" alt="'.$this->lists["m_events_home"][$i]->e_name.'" />';
								echo '<img '.getImgPop($this->lists["m_events_home"][$i]->e_img,6).'  title="'.$this->lists["m_events_home"][$i]->e_name.'" alt="'.$this->lists["m_events_home"][$i]->e_name.'" />';
							}else{ 
								echo "<span class='js_event_name'>".$this->lists["m_events_home"][$i]->e_name."</span>";
							}
							if(!$this->lists['t_single']){
								echo "&nbsp;&nbsp;".$this->lists["m_events_home"][$i]->p_name;
							}
							echo '</td>';
							?>
							<td class="home_event_count" width="5%">
							<?php
							if($this->lists["m_events_home"][$i]->ecount){
								echo $this->lists["m_events_home"][$i]->ecount;
							}else echo "0";
							?>
							</td>
							<td class="home_event_minute" width="3%" style="padding-right:35px;">
							<?php
							if($this->lists["m_events_home"][$i]->minutes){
								echo $this->lists["m_events_home"][$i]->minutes."'";
								//$time = explode(".", $this->lists["m_events_home"][$i]->minutes);
								//echo $time[0]."'&nbsp;";
								//echo isset($time[1])?$time[1]."''":'';
							}else echo "&nbsp;";
							?>
							</td>
							<?php
						}else{
							echo '<td style="padding:0px" colspan="3">&nbsp;</td>';
						}
						if(isset($this->lists["m_events_away"][$i])){
							echo '<td class="away_event" width="40%" style="padding-left:35px;">';
							if(isset($this->lists["m_events_away"][$i]->e_img) && $this->lists["m_events_away"][$i]->e_img && is_file('media/bearleague/events/'.$this->lists["m_events_away"][$i]->e_img)){
								// echo '<img height="15" src="'.JURI::base().'media/bearleague/events/'.$this->lists["m_events_away"][$i]->e_img.'" title="'.$this->lists["m_events_away"][$i]->e_name.'" alt="'.$this->lists["m_events_away"][$i]->e_name.'" />';
								echo '<img '.getImgPop($this->lists["m_events_away"][$i]->e_img,6).'  title="'.$this->lists["m_events_away"][$i]->e_name.'" alt="'.$this->lists["m_events_away"][$i]->e_name.'" />';
							}else{ 
								echo "<span class='js_event_name'>".$this->lists["m_events_away"][$i]->e_name."</span>";
							}
							if(!$this->lists['t_single']){
								echo "&nbsp;&nbsp;".$this->lists["m_events_away"][$i]->p_name;
							}	
							echo '</td>';
							?>
							<td class="away_event_count" width="5%">
							<?php
							if($this->lists["m_events_away"][$i]->ecount){
								echo $this->lists["m_events_away"][$i]->ecount;
							}else echo "0";
							?>
							</td>
							<td class="away_event_minute" width="3%">
							<?php
							if($this->lists["m_events_away"][$i]->minutes){
								echo $this->lists["m_events_away"][$i]->minutes."'";
								//$time = explode(".", $this->lists["m_events_away"][$i]->minutes);
								//echo $time[0]."'&nbsp;";
								//echo isset($time[1])?$time[1]."''":'';
							}else echo "&nbsp;";
							?>
							</td>
							
							<?php
							
						}else{
							echo '<td style="padding:0px" colspan="3">&nbsp;</td>';
						}
						?>
					</tr>
					<?php
					}
					//$how_rows = (count($this->lists["h_events"]) > count($this->lists["a_events"]))?count($this->lists["h_events"]):count($this->lists["a_events"]);
					$how_rows = count($this->lists["events"]);
					
					for($i=0;$i<count($this->lists["events"]);$i++){
						$this->lists["events"][$i]->a_ecount = "&nbsp;";
						$this->lists["events"][$i]->h_ecount = "&nbsp;";
						
						for($j=0;$j<count($this->lists["a_events"]);$j++){
							if($this->lists["events"][$i]->e_name == $this->lists["a_events"][$j]->e_name){
								$this->lists["events"][$i]->a_ecount = $this->lists["a_events"][$j]->ecount;
							}
						}
						for($j=0;$j<count($this->lists["h_events"]);$j++){
							if($this->lists["events"][$i]->e_name == $this->lists["h_events"][$j]->e_name){
								$this->lists["events"][$i]->h_ecount = $this->lists["h_events"][$j]->ecount;
							}
						}
					}
				
					for($p=0;$p<$how_rows;$p++){
						if($p==0){
							echo '</table><table class="season-list" style="margin-top:40px;" border="0" cellpadding="5" cellspacing="0" width="100%"><tr><th colspan="4" class="teams_stats"><h3>'.JText::_('BL_TBL_STAT').'</h3></th></tr>';
						}

						echo '<tr class="'.($p%2?"gray":"yellow").'">';
						echo "<td width='40%'>";
						if(isset($this->lists["events"][$p])){
							if($this->lists["events"][$p]->e_img && is_file('media/bearleague/events/'.$this->lists["events"][$p]->e_img)){
								// echo '<div style="float:left"><img height="20" src="'.JURI::base().'media/bearleague/events/'.$this->lists["events"][$p]->e_img.'" title="'.$this->lists["events"][$p]->e_name.'" alt="'.$this->lists["events"][$p]->e_name.'" /></div>';
								echo '<div style="float:left"><img '.getImgPop($this->lists["events"][$p]->e_img,6).'  title="'.$this->lists["events"][$p]->e_name.'" alt="'.$this->lists["events"][$p]->e_name.'" /></div>';
							}else{ 
							}
						echo '<div style="float:left;padding:5px;">'.$this->lists["events"][$p]->e_name."</div>";	
						}else echo "&nbsp;";
						echo "</td>";
						echo "<td class='home_stats_minute' width='100'>";	
							echo $this->lists["events"][$p]->h_ecount;
						echo "</td>";
						
						echo "<td width='40%' style='padding-left:30px;'>";
						echo "</td>";
						echo "<td class='away_stats_minute' width='50'>";	
							echo $this->lists["events"][$p]->a_ecount;
						echo "</td>";
						echo "</tr>";
					}
					?>
				</table>
				<table border="0" cellpadding="5" cellspacing="0" class="adf-fields-table first-bold">

				<?php
				    if($this->lists["ext_fields"]){
                        echo '<tr><th colspan="6" class="teams_stats"><h3>'.JText::_('BL_EBL_VAL').'</h3></th></tr>';
                    }
					echo $this->lists["ext_fields"];
					?>
				</table>
				
				
			
			
</div>
<input type="hidden" name="m_id" value="<?php echo $match->id?>"/>
<input type="hidden" name="task" value="" />
<input type="hidden" name="jscurtab" id="jscurtab" value="" />
</form>
<?php

if($match->match_descr){
echo '<div id="etab_descr_div" class="tabdiv" style="display:none;">';
?>
<div>
	<span itemprop="description">
	<?php 
        JPluginHelper::importPlugin('content'); 
        $dispatcher = JDispatcher::getInstance(); 
        $results = @$dispatcher->trigger('onContentPrepare', array ('content')); 
        $match->match_descr = JHTML::_('content.prepare', $match->match_descr); 
	?> 
	<?php 
		
		echo $match->match_descr;
	?>
	</span>
</div>
<?php
echo '</div>';
}


if($how_rowst_k){
echo '<div id="etab_squad_div" class="tabdiv" style="display:none;">';
?>
<!-- <content module> -->
			<div class="content-module">
			<table class="season-list">
				<tr>
					<td width="50%" style="text-align:center;font-size:18px;">
						<?php echo $match->home;?>
					</td>
					<td width="50%" style="text-align:center;font-size:18px;">
						<?php echo $match->away;?>
					</td>
				</tr>
			</table>
				<h3 class="solid"><?php echo JText::_('BLFA_LINEUP');?></h3>
<?php 
$how_rows = (count($lists['squard1']) > count($lists['squard2']))?count($lists['squard1']):count($lists['squard2']);
if($how_rows){
	echo '<table class="season-list" cellpadding="0" cellspacing="0" border="0">';
	for($p=0;$p<$how_rows;$p++){
	echo "<tr class='".($p % 2?"":"gray")."'>";
//SELECT
	echo "<td width='25%'>".(isset($this->lists['squard1'][$p]->name)?$this->lists['squard1'][$p]->photo."<p class='player-name' style='display: table-cell;padding-left:7px;'>".$this->lists['squard1'][$p]->name."</p><td>".$this->lists['squard1'][$p]->extra_val."</td>":"<td>&nbsp;</td>")."</td>";
	echo "<td width='25%'>".(isset($this->lists['squard2'][$p]->name)?$this->lists['squard2'][$p]->photo."<p class='player-name' style='display: table-cell;padding-left:7px;'>".$this->lists['squard2'][$p]->name."</p><td>".$this->lists['squard2'][$p]->extra_val."</td>":"<td>&nbsp;</td>")."</td>";
	echo "</tr>";
	}
	echo '</table>';
}	
?>
<?php 

$how_rows = (count($this->lists['squard1_res']) > count($this->lists['squard2_res']))?count($this->lists['squard1_res']):count($this->lists['squard2_res']);
if($how_rows){
	echo "<h3 class='solid'>".JText::_('BLFA_SUBSTITUTES')."</h3>";
	echo '<table class="season-list" cellpadding="0" cellspacing="0" border="0">';
	for($p=0;$p<$how_rows;$p++){
		echo "<tr class='".($p % 2?"":"gray")."'>";
		echo "<td width='50%'>".(( isset($this->lists['squard1_res'][$p]->name) && $this->lists['squard1_res'][$p]->name)?$this->lists['squard1_res'][$p]->photo."<p class='player-name'>".$this->lists['squard1_res'][$p]->name."</p>":"&nbsp;")."</td>";
		echo "<td width='50%'>".((isset($this->lists['squard2_res'][$p]->name) && $this->lists['squard2_res'][$p]->name)?$this->lists['squard2_res'][$p]->photo."<p class='player-name'>".$this->lists['squard2_res'][$p]->name."</p>":"&nbsp;")."</td>";
		echo "</tr>";
	}
	echo '</table>';
}	

//subs in
$how_rows = (count($this->lists['subsin1']) > count($this->lists['subsin2']))?count($this->lists['subsin1']):count($this->lists['subsin2']);
$arrow_in = '<img src="'.JUri::Base().'components/com_joomsport/img/ico/old-edit-redo.png" class="sub-player-ico" title="" alt="" />';
$arrow_out = '<img src="'.JUri::Base().'components/com_joomsport/img/ico/old-edit-undo.png" class="sub-player-ico" title="" alt="" />';
if($how_rows){
	echo "<h3 class='solid'>".JText::_('BLFA_SUBSIN')."</h3>";
	echo '<table class="season-list" cellpadding="0" cellspacing="0" border="0">';
	for($p=0;$p<$how_rows;$p++){
		echo "<tr class='".($p % 2?"":"gray")."'>";
		echo "<td width='50%' align='right' style='padding-right:20px;'>";
			echo "<table width='100%' cellpadding='2' border='0' class='season-list'>";
				echo '<tr>';
					echo '<td>';
						echo (isset($this->lists['subsin1'][$p]->plin) && $this->lists['subsin1'][$p]->plin)?$arrow_in."<p class='sub-player-name'>".$this->lists['subsin1'][$p]->plin."</p><br />":"&nbsp;";
						
						echo (isset($this->lists['subsin1'][$p]->plout) && $this->lists['subsin1'][$p]->plout)?$arrow_out."<p class='sub-player-name'>".$this->lists['subsin1'][$p]->plout."</p>":"&nbsp;";
					echo '</td>';
					echo '<td width="50" valign="middle">';
						echo (isset($this->lists['subsin1'][$p]->minutes) && $this->lists['subsin1'][$p]->minutes)?$this->lists['subsin1'][$p]->minutes."'":"&nbsp;";
					echo '</td>';
				echo '</tr>';
			echo '</table>';
		echo "</td>";
		echo "<td>";
			echo "<table width='100%' cellpadding='2' border='0' class='season-list'>";
				echo '<tr>';
					echo '<td>';
						echo (isset($this->lists['subsin2'][$p]->plin) && $this->lists['subsin2'][$p]->plin)?$arrow_in."<p class='sub-player-name'>".$this->lists['subsin2'][$p]->plin."</p><br />":"&nbsp;";
					
						echo (isset($this->lists['subsin2'][$p]->plout) && $this->lists['subsin2'][$p]->plout)?$arrow_out."<p class='sub-player-name'>".$this->lists['subsin2'][$p]->plout."</p>":"&nbsp;";
					echo '</td>';
					echo '<td width="50" valign="middle">';
						echo (isset($this->lists['subsin2'][$p]->minutes) && $this->lists['subsin2'][$p]->minutes)?$this->lists['subsin2'][$p]->minutes."'":"&nbsp;";
					echo '</td>';
				echo '</tr>';
			echo '</table>';
		echo "</td>";
		echo "</tr>";
	}
	echo '</table>';
}	
echo "</div>";
echo "</div>";
}

if(count($this->lists["photos"])){
echo '<div id="etab_photos_div" class="tabdiv" style="display:none;">';
echo "<table class='jsnoborders'><tr><td>";
	 echo JHtml::_('images.getGalleryHTML',  $this->lists["photos"]);
echo "</td></tr></table>";	
echo '</div>';
}
?>
				<?php
				if($this->lists['mcomments']){
				?>
				<!-- <Comments box> -->
				<div class="dv_comments"><h3><?php echo JText::_("BLFA_COMMENTS");?></h3></div>
				<ul class="comments-box" id="all_comments">
				
				
					
				<?php


				for($i=0;$i<count($this->lists["comments"]);$i++){	
					?>
					<li id="divcomb_<?php echo $this->lists["comments"][$i]->id?>">
						<?php if(isset($this->lists["comments"][$i]->avatar)){?>
						<span style="float:left;margin-right:10px;">
						<div class="team-embl"> 
							<img <?php echo $this->lists["comments"][$i]->avatar;?>"  alt="" style="padding-right:0;"/>
						
						</div>
						</span>
						<?php }else{
							echo "<img src='".JURI::base()."components/com_joomsport/img/ico/season-list-player-ico.gif' width='30' height='30' alt='' />";
						}?>
						<div class="comments-box-inner" style="">
							<span class="date" nowrap="nowrap">
								<?php 
								if(($this->lists["comments_adm"]) || ($this->lists["usera"]->id == $this->lists["comments"][$i]->user_id)){
									echo "<img src='".JURI::base()."components/com_joomsport/img/ico/close.png' width='15' border=0 style='cursor:pointer;' onClick='javascript:delCom(".$this->lists["comments"][$i]->id.");' />";
								}
								?>
								<?php 
									jimport('joomla.utilities.date');
										if(getVer() > '1.6'){
											$tz	= new DateTimeZone(JFactory::getApplication()->getCfg('offset'));
											$jdate = new JDate($this->lists["comments"][$i]->date_time);
										
											$jdate->setTimezone($tz);
										}else{
											
											$jdate = new JDate($this->lists["comments"][$i]->date_time,JFactory::getApplication()->getCfg('offset'));
										
										
										}
									//$jdate = new JDate($this->lists["comments"][$i]->date_time,JFactory::getApplication()->getCfg('offset'));
									
									//$jdate->setTimezone($tz);
									
									echo $jdate->format('Y-m-d H:i:s', true, false);
									//echo $this->lists["comments"][$i]->date_time;
								?>
								
							</span>
							<h4 class="nickname">
								<?php 
									//echo ($this->lists["sh_name"]?$this->lists["comments"][$i]->nick:$this->lists["comments"][$i]->plname);
									if($this->lists["sh_name"]){
										echo $this->lists["comments"][$i]->nick;
									}else{
										echo ($this->lists["comments"][$i]->plname?$this->lists["comments"][$i]->plname:$this->lists["comments"][$i]->nick);
									}
								?>
							</h4>
							<p><?php echo str_replace("\n",'<br />',htmlspecialchars($this->lists["comments"][$i]->comment));?></p>
						</div>
					</li>
				<?php	
				}
				?>
				</ul>
				<?php 
				if($this->jver >= '1.6'){
					$link = JUri::base().'index.php?option=com_joomsport&task=add_comment&no_html=1&tmpl=component';
				}else{
					$link = 'index2.php?option=com_joomsport&task=add_comment&no_html=1';
				}
				?>
				<form action="<?php echo $link;?>" method="POST" id="comForm" name="comForm">
				<!-- <Post comment> -->
				<div class="post-comment">
					<textarea name="addcomm" id="addcomm"></textarea>
					<button type="submit" class="send-button" id="submcom"><span><b><?php echo JText::_("BLFA_POSTCOMMENT");?></b></span></button>
					<input type="hidden" name="mid" value="<?php echo $match->id;?>" />
				</div>
				</form>
				<!-- </Post comment> -->
				

				<?php if($this->jver  >= '1.6') {?>
				<script type="text/javascript">
				//<![CDATA[ 
				window.addEvent('domready', function(){
				jQuery('#comForm').on('submit', function(e) {
                                        e.preventDefault();
//					new Event(e).stop();
						if(jQuery('#addcomm').val()){
							var submcom = jQuery('#submcom').get(0);
							//submcom.disabled = true;
							jQuery.ajax({
                                                            url: '<?php echo $link;?>',                                                           
                                                            type: "post",
                                                            data: jQuery('#comForm').serialize(),
                                                            success: function(result){
                                                                if(result){										
                                                                        var allc = jQuery('#all_comments').get(0);
                                                                        allc.innerHTML = allc.innerHTML + result;
                                                                        submcom.disabled = false;
                                                                        jQuery('#addcomm').val('');

                                                                }else{
                                                                        alert('<?php echo JText::_('BLFA_NOTREGISTRED');?>');
                                                                }
                                                                jQuery('#comForm').get(0).reset();
                                                            }                                                            
                                                         });
						}
					});
				});
				//]]> 
				</script>
				<?php } ?>
				<?php }?>
				
				<!-- </Comments box> -->
				
</div>
<!-- </content module> -->
