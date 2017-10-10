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
$lists = $this->lists;
$Itemid = JRequest::getInt('Itemid');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
?>
<?php
if($this->tmpl != 'component'){
	echo $lists["panel"];

	$lnk = "window.open('".JURI::base()."index.php?tmpl=component&option=com_joomsport&amp;view=calendar&amp;sid=".$lists["s_id"]."','jsmywindow','width=750,height=700,scrollbars=1,resizable=1');";
}else{
	$lnk = "window.print();";
}

?>
<script type="text/javascript">
    function resetPoints(el){
        if (jQuery(el).is(':checked')){
            jQuery('input', jQuery(el).parent()).removeAttr('disabled');
        } else {
            jQuery('input', jQuery(el).parent()).attr('disabled', 'disabled');
        }
    }
	function js_showfil(){
		if(getObj('js_tblfilt_id').style.display=="block"){
			getObj('js_tblfilt_id').style.display="none";
		}else{
			getObj('js_tblfilt_id').style.display="block";
		}
	}
</script>
<!-- <module middle> -->
<form name="adminForm" id="adminForm" action="<?php echo JRoute::_("index.php?option=com_joomsport&view=calendar&sid=".$lists["s_id"]."&Itemid=".$Itemid."&jslimit=".$this->page->limit."&page=1".($this->tmpl == 'component'?"&tmpl=component":""));?>" method="post">
<div class="module-middle">
	
	<!-- <back box> -->
	<?php if($this->tmpl != 'component'){?>
			<div class="back dotted"><a href="javascript:void(0);" onclick="history.back(-1);" title="<?php echo JText::_("BL_BACK")?>">&larr; <?php echo JText::_("BL_BACK")?></a></div>
	<?php } ?>
	<!-- </back box> -->
	
	<!-- <title box> -->
	<div class="title-box">
		<h2><?php //echo $this->escape($this->params->get('page_title'));
		 echo $this->escape($this->ptitle); ?></h2>
		<a class="print" href="#" onClick="<?php echo $lnk;?>" title="<?php echo JText::_("BLFA_PRINT");?>"><?php echo JText::_("BLFA_PRINT");?></a>
		
	</div>

	<div style="padding-bottom:20px;<?php if($this->tmpl == 'component'){ echo "display:none;";}?>" >
		<table id="js_tblfilt_id" border="0" cellspacing="2" cellpadding="0" class="adf-fields-table" style="display:none;">
			<tr>
				<td class="js_filtername"><?php echo JText::_("BLFA_TEAM");?></td>
				<td>		
					<div style="position:relative;float:left;top:4px;">
					<span class="down"><!-- --></span>
					<?php echo $this->lists['teams'];?>
					</div>
					<div style="position:relative;float:left;top:4px;" class="js_minth">
					<span class="down"><!-- --></span>
					<?php echo $this->lists['teamhm'];?>
					</div>
				</td>	
			
				
			
				<td class="js_filtername"><?php echo JText::_("BLFA_MATCHDAY");?></td>
				<td>
					<div style="position:relative;top:4px;">
						<span class="down"><!-- --></span>
						<?php echo $this->lists['mdays'];?>
					</div>
				</td>
				<td class="js_filtername"><?php echo JText::_("BLFA_DATE");?></td>
				<td class="date-list">
					<?php echo $this->lists['fromdate'];?>
					<?php echo $this->lists['todate'];?>
				</td>
			</tr>
			<tr>
				<td colspan="5">&nbsp;</td>
				<td align="right">
					<button class="send-button" onclick="document.adminForm.submit();">
						<span>
							<b><?php echo JText::_("BLFA_SEARCH");?></b>
						</span>
					</button>
				</td>
			</tr>
		</table>
			<div style="text-align:right;margin-top:10px;margin-bottom:-20px;"><a href="javascript:void(0);" onclick="js_showfil();"><?php echo JText::_("BLFA_SEARCH_MATCHES");?></a></div>
		</div>

	<!-- </div>title box> -->
	
</div>
<!-- </module middle> -->
<!-- <content module> -->
<?php
if($this->tmpl == 'component'){
echo '<div id="wr-module">';
}
?>
			<div class="content-module padd-off" style="overflow:visible !important;">
				<?php
				$old_md = 0;
				echo '<table class="match-day" cellpadding="0" cellspacing="0" border="0">';
				for($i=0;$i<count($lists["matchs"]);$i++){
				
					$match = $lists["matchs"][$i];
					//print_r($match);
					if( $old_md != $match->mdid){
						
						echo "<tr><td colspan='13'><h3 class='solid'>".$match->m_name."</h3></td></tr>";
						
					}
					$old_md = $match->mdid;
				?>
				<tr class="<?php echo $i % 2?"":"gray";?>">
					
					<td class="match-day-date">
					<?php //echo $this->formatDate(strtotime($match->m_date . ' ' . $match->m_time));
						if($match->m_date){

                            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$match->m_date))
                            {
                                echo $this->formatDate($match->m_date . ' ' . $match->m_time);
                            }else{
                                echo $match->m_date;
                            }

						}					?>
					</td>
					<td class="team-ico-h"><!-- -->
					<?php
						if(!$this->lists['t_single'] && isset($match->home)){
							/*if($match->emb1 && is_file('media/bearleague/'.$match->emb1)){
								echo '<div class="team-embl"><img '.getImgPop($match->emb1,1).'  alt="'.$match->home.'" /></div>';
							}else{
								echo '<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30" height="30" alt="">';
							}*/
                            echo JHtml::_('images.getTeamEmbl',  $match->team1_id,1,$match->home);
						}
					?>
					</td>
					<td class="team-h">
						
					<?php
                        if(isset($match->hm_id)){
                             if($lists["t_single"]){
                                $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match->hm_id.'&sid='.$lists["s_id"].'&Itemid='.$Itemid);
                             }else{
                                $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match->hm_id.'&sid='.$lists["s_id"].'&Itemid='.$Itemid);
                             }

                            echo '<a href="'. $link.'">'.$match->home.'</a>';
                        }

                        ?>

					</td>
					<td class="score" nowrap="nowrap">
						<span class="score">
						<?php $lnks =  JRoute::_('index.php?option=com_joomsport&task=view_match&id='.$match->id.'&Itemid='.$Itemid);?>
						
						<?php
							$thismat2_extra = '';
							if($match->m_played == 1){  
								$thismat =  '<b class="score-h">'.$match->score1; 
								
								$thismat .=  '</b>';
								$thismat .= "<b>:</b>";
								$thismat .= '<b class="score-a">'.$match->score2;
								
								$thismat .=  '</b>';
								$thismat2 = '';
								$tmpmat = '';
								
								if(@$lists["enbl_extra"] && $match->is_extra)
								{ //UPDATE
									
									$class_ext = ($match->score1 > $match->score2)?"extra-time-h":"extra-time-g";
										if($match->score1 == $match->score2){
											$class_ext = ($match->aet1 > $match->aet2)?"extra-time-h":"extra-time-g";
											if($match->aet1 == $match->aet2 && $match->p_winner){
												$class_ext = ($match->p_winner == $match->team1_id)?"extra-time-h":"extra-time-g";
											}
										}
										
									if($match->p_winner || $match->aet1 != $match->aet2 || $match->score1 != $match->score2){
										$thismat2_extra = '<div class="'.$class_ext.'" title="'.JText::_('BLFA_TEAM_WON_ET').'">'.JText::_('BL_RES_EXTRA').'</div>';
									}else{
									$tmpmat = '&nbsp;<font style="font-size:80%;">('.JText::_('BL_RES_EXTRA').')</font>';}
								}
								
								//match info tooltip
								
								$ev_count = (count($match->m_events_home) > count($match->m_events_away)) ? (count($match->m_events_home)) : (count($match->m_events_away));
								$tbl_info = "<div class='tooltip-wr'><table class='tooltptbl' border='0' cellspacing='0' cellpadding='0'>";
								$tbl_info .= "<tr class='gray' style='border:0px;'><td class='tool-home-td' align='right'>".$match->home."</td><td colspan='4' align='center' width='80' nowrap='nowrap'>".$match->score1." : ".$match->score2."</td><td class='tool-home-td' align='left'>".$match->away."</td></tr>";
								if($lists["t_type"] && $match->is_extra){
									///UPDATE unlocked
									//print_r($match->p_winner);
								
										
									$etclass="extra-time-g-re";
									//echo $match->p_winner;
									if($match->p_winner == $match->hm_id){
										$etclass="extra-time-h-re";
										
									}
									
								
									if($match->p_winner){
										$tbl_info .= "<tr class='gray' style='border:0px;'><td colspan='6' align='center' title='".JText::_("BLFA_TT_AET")."'>".JText::_("BLFA_TT_AET")."</br>".$match->aet1." : ".$match->aet2."<div class='".$etclass."'>".JText::_('W')."</div></td></tr>";
									}else{
										$tbl_info .= "<tr class='gray' style='border:0px;'><td colspan='6' align='center' title='".JText::_("BLFA_TT_AET")."'>".JText::_("BLFA_TT_AET")."</br>".$match->aet1." : ".$match->aet2."</td></tr>";
									}
								}
								$thismat .= $thismat2;

								for($j=0;$j<$ev_count;$j++){
									$tbl_info .= "<tr class='gray'>";
									if(isset($match->m_events_home[$j])){
									
										$tbl_info .= "<td class='home_event' width='42%'>";
										if($match->m_events_home[$j]->e_img && is_file('media/bearleague/events/'.$match->m_events_home[$j]->e_img)){
											// $tbl_info .= '<img height="15" src="'.JURI::base().'media/bearleague/events/'.$match->m_events_home[$j]->e_img.'" title="'.$match->m_events_home[$j]->e_name.'" alt="'.$match->m_events_home[$j]->e_name.'" />';
											$tbl_info .= '<img '.getImgPop($match->m_events_home[$j]->e_img,6).'  title="'.$match->m_events_home[$j]->e_name.'" alt="'.$match->m_events_home[$j]->e_name.'" />';
										
										}else{ 
											$tbl_info .= $match->m_events_home[$j]->e_name;
										}
										if(!$this->lists['t_single']){
											$tbl_info .= '&nbsp;'.$match->m_events_home[$j]->p_name;
										}
										$tbl_info .= '</td>';
										
										$tbl_info .= '<td class="home_event_count" width="20">';
										
										if($match->m_events_home[$j]->ecount){
											$tbl_info .= $match->m_events_home[$j]->ecount;
										}else {$tbl_info .= "0";}
										
										$tbl_info .= '</td>';
										$tbl_info .= '<td class="home_event_minute" width="20">';
									
										if($match->m_events_home[$j]->minutes){
											$tbl_info .= $match->m_events_home[$j]->minutes."&rsquo;";
										}else{ $tbl_info .= "&nbsp;";}
										
										$tbl_info .= '</td>';
										
									}else{
										$tbl_info .= '<td style="padding:0px" width="10">&nbsp;</td>';
										$tbl_info .= '<td style="padding:0px" width="10">&nbsp;</td>';
										$tbl_info .= '<td style="padding:0px">&nbsp;</td>';
									}
									if(isset($match->m_events_away[$j])){
										
										$tbl_info .= '<td class="away_event_minute" width="20">';
										
										if($match->m_events_away[$j]->minutes){
											$tbl_info .= $match->m_events_away[$j]->minutes."&rsquo;";
										}else{ $tbl_info .= "&nbsp;";}
										
										$tbl_info .= "</td>";
										$tbl_info .= '<td class="away_event_count" width="20">';
									
										if($match->m_events_away[$j]->ecount){
											$tbl_info .= $match->m_events_away[$j]->ecount;
										}else{ $tbl_info .= "0";}
										
										$tbl_info .= "</td>";
										
										$tbl_info .= '<td class="away_event" width="42%">';
										if($match->m_events_away[$j]->e_img && is_file('media/bearleague/events/'.$match->m_events_away[$j]->e_img)){
											// $tbl_info .= '<img height="15" src="'.JURI::base().'media/bearleague/events/'.$match->m_events_away[$j]->e_img.'" title="'.$match->m_events_away[$j]->e_name.'" alt="'.$match->m_events_away[$j]->e_name.'" />';
											$tbl_info .= '<img '.getImgPop($match->m_events_away[$j]->e_img,6).'  title="'.$match->m_events_away[$j]->e_name.'" alt="'.$match->m_events_away[$j]->e_name.'" />';
										}else{ 
											$tbl_info .= $match->m_events_away[$j]->e_name;
										}
										if(!$this->lists['t_single']){
											$tbl_info .= "&nbsp;".$match->m_events_away[$j]->p_name;
										}	
										$tbl_info .= "</td>";
									}else{
										$tbl_info .= '<td style="padding:0px" width="10">&nbsp;</td>';
										$tbl_info .= '<td style="padding:0px" width="10">&nbsp;</td>';
										$tbl_info .= "<td style='padding:0px'>&nbsp;</td>";
									}
									$tbl_info .= '</tr>';
								}
								
								$tbl_info .= '</table></div>';
								$tbl_title = $tbl_info;
								//echo JHTML::tooltip($tbl_info,$tbl_title,"",$thismat,$lnks);
								if($this->tmpl != 'component'){
									echo '<a href="'.$lnks.'" class="bdtooltip"><span>'.$tbl_title.'</span>'.$thismat.'</a>'.$thismat2_extra; 
								}else{
									echo $thismat;
								}
							}else{
								echo "<a href='".$lnks."' class='so_not_played'>";
								echo '<b class="score-h">-</b>';?>
								<b>:</b>
								<b class="score-a">-</b>
								<?php
								echo "</a>";
								if (isset($match->betavailable) && isset($match->betfinish) && $match->betavailable && $match->betfinish){
									?>
									<table style="display:none" class="bettable">
										<tr>
											<th><?php echo JText::_('BLFA_BET_COEFF')?>/<?php echo JText::_('BLFA_BET_PT')?></th>
											<th></th>
											<th><?php echo JText::_('BLFA_BET_COEFF')?>/<?php echo JText::_('BLFA_BET_PT')?></th>
										</tr>
										<?php foreach($match->betevents as $event):?>
										<?php if ($event->coeff1 || $event->coeff2):?>
										<tr>
											<td>
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
											<td>
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
											<td colspan="3">
												<input type="hidden" name="bet_match[]" value="<?php echo $match->id?>"/>
												<input type="button" value="<?php echo JText::_("BLFA_BET_SUBMIT_BET");?>" onClick="document.adminForm.task.value = 'bet_calendar_save';document.adminForm.submit();"/>
											</td>
										</tr>
									</table>
									<?php
								}
							}
							
							?>
						</span>	
						<!--update-->
						<div style="text-align:center;" title="<?php echo JText::_('BLFA_BONUS');?>">
							<?php
							if($match->bonus1 != 0 || $match->bonus2 != 0){
								echo ($match->bonus1)?"<font style='font-size:75%;'>".floatval($match->bonus1).":</font>":"";
								echo ($match->bonus2)?"<font style='font-size:75%;'>".floatval($match->bonus2)."</font>":"";
							}
							?>
						</div>
					</td>
					<?php
                    if(isset($match->aw_id)){
                         if($lists["t_single"]){
                            $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$match->aw_id.'&sid='.$lists["s_id"].'&Itemid='.$Itemid);
                         }else{
                            $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$match->aw_id.'&sid='.$lists["s_id"].'&Itemid='.$Itemid);
                         }
                    }
					?>
					<td class="team-ico-a"><!-- -->
					<?php
						if(!$this->lists['t_single'] && isset($match->away)){
							/*if($match->emb2 && is_file('media/bearleague/'.$match->emb2)){
								
								echo '<div class="team-embl"><img '.getImgPop($match->emb2,1).'  alt="'.$match->away.'" /></div>';
							}else{
								echo '<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30" height="30" alt="">';
							}*/
                            echo JHtml::_('images.getTeamEmbl',  $match->team2_id,1,$match->away);
						}
					?>
					</td>

					<td class="team-a"><?php echo (isset($match->aw_id))?'<a href="'.$link.'">'.$match->away.'</a>':'';?></td>
					<?php if($lists["locven"]){?>
					<td><?php echo getJS_Location($match->id);?></td>
					<?php } ?>
					<?php if ($match->betavailable && $match->betfinish):?>
						<td>
							<a href="#bet" onClick="jQuery('.bettable', jQuery(this).parent().parent()).css({'display': ''})"><?php echo JText::_('BLFA_BET_BETME');?></a>
						</td>
					<?php else:?>
						<td></td>
					<?php endif;?>
				</tr>		
				<?php
				}
			?>

			<tr>
				<td colspan="13" align="center" style="padding-top:10px;">
					<?php 
						$link_page = "index.php?option=com_joomsport&view=calendar&sid=".$lists["s_id"]."&Itemid=".$Itemid."&jslimit=".$this->page->limit.($this->tmpl == 'component'?"&tmpl=component":"");
						echo "<div class='page_limit'>".$this->page->getLimitPage()."</div>";
						echo $this->page->getPageLinks($link_page); 
						$limit = $this->page->limit;
						($limit==0)?($limit = 'All'):('');
						echo "<div class='selectPag'><span style='position:relative;top:7px;right:5px;'>".JText::_('BL_TAB_DISPLAY')."</span><span class='select_box'><span class='num_box'>".$limit."</span></span>".$this->page->getLimitBox()."</div>";

					?>
				</td>
			</tr>
			
	</table>
	</div>
<?php
if($this->tmpl == 'component'){
echo '</div>';
}
?>	
	<!-- </content module> -->
</form>