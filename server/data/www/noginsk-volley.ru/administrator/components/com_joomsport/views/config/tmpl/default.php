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
defined('_JEXEC') or die;
JHTML::_('behavior.tooltip');
$lists = $this->lists;
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR .'components'.DIRECTORY_SEPARATOR.'com_joomsport'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'tabs.php');
		$etabs = new esTabs();
		
		?>
		
		<script type="text/javascript" src="components/com_joomsport/color_piker/201a.js"></script>
		<script type="text/javascript">
		function delete_logo(){
			getObj("logoiddiv").innerHTML = '';
		}
		</script>
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<!-- <tab box> -->
		<ul class="tab-box">
			<?php
			echo $etabs->newTab(JText::_('BLBE_GENERAL'),'main_cfg','','vis');
			echo $etabs->newTab(JText::_('BLBE_REGISTR'),'reg_cfg','');
			echo $etabs->newTab(JText::_('BLBE_ADMRIGHTS'),'admrigh_cfg','');
			echo $etabs->newTab(JText::_('BLBE_ESPORTCONF'),'esport_cfg','');
			echo $etabs->newTab(JText::_('BLBE_SOCIALCONF'),'social_cfg','');
			?>
		</ul>
		<div style="clear:both"></div>
		<div id="main_cfg_div" class="tabdiv">
		<table class="adminlists">
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_DATECONFIG' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_DATECONFIG' ); ?>::<?php echo JText::_( 'BLBE_TT_DATECONFIG' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<?php echo $lists['data_sel'] ?>
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_YTEAMCOLOR' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_YTEAMCOLOR' ); ?>::<?php echo JText::_( 'BLBE_TT_YTEAMCOLOR' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<div id="colorpicker201" class="colorpicker201"></div>
					<input class="btn btn-small" type="button" style="cursor:pointer;" onclick="showColorGrid2('yteam_color','sample_1');" value="...">&nbsp;<input type="text" name="yteam_color" id="yteam_color" size="9" maxlength="30" value="<?php echo $lists['yteam_color'];?>" /><input type="text" id="sample_1" size="1" value="" style="background-color:<?php echo $lists['yteam_color']?>" class="color-kind" />
				</td>
			
			</tr>
			<tr>
				<td><?php echo JText::_("BLBE_UNABMATCHCOM");?></td>
				<td><input type="checkbox" name="mcomments" value="1" <?php echo ($lists['mcomments']=='1')?" checked":"";?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("BLBE_UNABLEPLREG");?></td>
				<td><input type="checkbox" name="player_reg" value="1" <?php echo ($lists['player_reg']=='1')?" checked":"";?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("BLBE_UNBTEAMREG");?></td>
				<td><input type="checkbox" name="team_reg" value="1" <?php echo ($lists['team_reg']=='1')?" checked":"";?> /></td>
			</tr>
			<!--
			<tr>
				<td><?php echo JText::_("BLBE_UNBNOTJSPL_TEAMREG");?></td>
				<td><input type="checkbox" name="player_team_reg" value="1" <?php echo ($lists['player_team_reg']=='1')?" checked":"";?> /></td>
			</tr>
			END-->
			<tr>
				<td><?php echo JText::_("BLBE_PLLISTORDER");?></td>
				<td><?php echo $lists['pllist_order'];?></td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_("BLBE_CHOOSEPE");?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_CHOOSEPE' ); ?>::<?php echo JText::_( 'BLBE_TT_CHOOSEPE' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td><?php echo $lists['pllist_order_se'];?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("BLBE_TEAMLOGOTBL");?></td>
				<td>
					
					<input type="text" maxlength="5" name="teamlogo_height" value="<?php echo $lists['teamlogo_height'];?>" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />
				</td>
			</tr>
			
			<tr>
				<td><?php echo JText::_("BLBE_UNABVENUE");?></td>
				<td><input type="checkbox" name="unbl_venue" value="1" <?php echo ($lists['unbl_venue']=='1')?" checked":"";?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("BLBE_CALVENUE");?></td>
				<td><input type="checkbox" name="cal_venue" value="1" <?php echo ($lists['cal_venue']=='1')?" checked":"";?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("BLBE_SHOWPLAYEDMATCHES");?></td>
				<td><input type="checkbox" name="played_matches" value="1" <?php echo ($lists['played_matches']=='1')?" checked":"";?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("BLBE_NAMEDONFE");?></td>
				<td><?php echo $lists['player_name'];?></td>
			</tr>
			<!--<tr>
				<td><?php echo JText::_("BLBE_KNOCKSTYLE");?></td>
				<td><?php echo $lists['knock_style'];?></td>
			</tr>-->
			
			<tr>
				<td><?php echo JText::_("BLBE_BRANDING_ON");?></td>
				<td><input type="checkbox" name="jsbrand_on" value="1" <?php echo ($lists['jsbrand_on']=='1')?" checked":"";?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("BLBE_EPANEL_IMG");?></td>
				<td>
					<input type="file" name="t_logo" /><input class="btn btn-small" type="button" style="cursor:pointer;" value="<?php echo JText::_( 'BLBE_UPLOAD' ); ?>" onclick="submitbutton('save_config');" />
					<br />
					<br />
					<div id="logoiddiv">
					<?php
					if($lists["jsbrand_epanel_image"] && is_file('..'.$lists["jsbrand_epanel_image"])){
                        $url = JURI::root().'components/com_joomsport/includes/imgres.php?src='
                            . $lists["jsbrand_epanel_image"] . '&w=53';
                        echo '<img src="'.$url.'" height="38" />';
						echo '<input type="hidden" name="istlogo" value="1" />';
						?>
						<a href="javascript:void(0);" title="<?php echo JText::_( 'BLBE_REMOVE' ); ?>" onClick="javascript:delete_logo();"><img src="<?php echo JURI::base();?>components/com_joomsport/img/publish_x.png" title="Remove" /></a>
						</div>
					<?php	
					}
					?>
					</div>
				</td>
			</tr>
            <tr>
                <td><?php echo JText::_( 'BLBE_EDITCOUNTR' ); ?>
                    <span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_EDITCOUNTR' ); ?>::<?php echo JText::_( 'BLBE_EDITCOUNTRV' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
                </td>
                <td><a href="index.php?option=com_joomsport&amp;task=list_countr">&nbsp;<?php echo JText::_( 'BLBE_EDITCOUNTRS' ); ?></a></td>
            </tr>
		</table>
		</div>
		<div id="reg_cfg_div" class="tabdiv" style="display:none;">
		<table width="100%" class="adminlists">
			<tr>
				<th width="50%"><?php echo JText::_('BLBE_REGPLFLD');?></th>
			</tr>
			<tr>
				<td width="50%" valign="top">
					<!--<div style="clear:both; overflow: hidden; width:100%;">
                        <div style="float:left;">
                            <?php echo JText::_('BLBE_AUTOREGPLAYER');?>
                            <span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_AUTOREGPLAYER' ); ?>::<?php echo JText::_( 'BLBE_TT_AUTOREGPLAYER' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
						</div>
                        <div style="float:left; margin-left: 15px; ">
                            <fieldset class="radio btn-group">
                            <?php echo $lists['autoreg_player'];?>
                            </fieldset>
                        </div>
					</div>-->
					<table>
						<tr>
							<td colspan='2'>
								<?php echo JText::_( 'BLBE_PLAYERCANJOIN' ); ?>
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_PLAYERCANJOIN' ); ?>::<?php echo JText::_( 'BLBE_TT_PLAYERCANJOIN' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
							</td>
							<td>
								<input type="checkbox" name="esport_join_team" value="1" <?php echo ($lists['esport_join_team']=='1')?" checked":"";?> />
							</td>
			
						</tr>
						<tr>
							<th width="50%"><?php echo JText::_('BLBE_FIELD');?></th>
							<th><?php echo JText::_('BLBE_ONREGPAGE');?></th>
							<th><?php echo JText::_('BLBE_REQUIRED');?></th>
						</tr>
						<tr>
							<td><?php echo JText::_("BLBE_LASTNAME");?></td>
							<td><input type="checkbox" name="reg_lastname" value="1" <?php echo ($lists['reg_lastname']=='1')?" checked":"";?> /></td>
							<td><input type="checkbox" name="reg_lastname_rq" value="1" <?php echo ($lists['reg_lastname_rq']=='1')?" checked":"";?> /></td>
						</tr>
						<tr>
							<td><?php echo JText::_("BLBE_NICKNAME");?></td>
							<td><input type="checkbox" name="nick_reg" value="1" <?php echo ($lists['nick_reg']=='1')?" checked":"";?> /></td>
							<td><input type="checkbox" name="nick_reg_rq" value="1" <?php echo ($lists['nick_reg_rq']=='1')?" checked":"";?> /></td>
						</tr>
						<tr>
							<td><?php echo JText::_("BLBE_COUNTRY");?></td>
							<td><input type="checkbox" name="country_reg" value="1" <?php echo ($lists['country_reg']=='1')?" checked":"";?> /></td>
							<td><input type="checkbox" name="country_reg_rq" value="1" <?php echo ($lists['country_reg_rq']=='1')?" checked":"";?> /></td>
						</tr>
						<?php
						for($i=0;$i<count($lists['adf_player']);$i++){
							$regpl = $lists['adf_player'][$i];
							
							echo '<tr><td><input type="hidden" name="adf_pl[]" value="'.$regpl->id.'" />'.$regpl->name.'</td>';
							echo '<td><input type="checkbox" name="adfpl_reg_'.$regpl->id.'" value="1" '.($regpl->reg_exist?" checked":"").' /></td>';
							echo '<td><input type="checkbox" name="adfpl_rq_'.$regpl->id.'" '.(($regpl->field_type == 2)?"DISABLED":"").' value="1" '.($regpl->reg_require?" checked":"").' /></td>';
                            if($regpl->published == 0){
                                echo '<td>'.JText::_('BLBE_ATTEF').'</td>';
                            }
                            echo '</tr>';
						}
						?>
					</table>
				</td>
				
			</tr>
		</table>
		</div>
		<div id="admrigh_cfg_div" class="tabdiv" style="display:none;overflow:hidden;">
			<div style="width:100%;">
				<table class="adminlists">
					<tr>
						<th colspan="3">
							<?php echo JText::_( 'BLBE_MODERRIGHTS' ); ?>
						</th>
					</tr>
					<tr>
						<td><?php echo JText::_("BLBE_MODEREDITPLAYER");?></td>
						<td colspan="2"><input type="checkbox" name="moder_addplayer" value="1" <?php echo ($lists['moder_addplayer']=='1')?" checked":"";?> /></td>
					</tr>
					<tr>
						<td><?php echo JText::_("BLBE_TEAMPERACCOUNT");?></td>
						<td colspan="2"><input type="text" name="teams_per_account" value="<?php echo $lists['teams_per_account'];?>" /></td>
					</tr>
					<tr>
						<td><?php echo JText::_("BLBE_PLAYERSRPEACCOUNT");?></td>
						<td colspan="2"><input type="text" name="players_per_account" value="<?php echo $lists['players_per_account'];?>" /></td>
					</tr>


					<!--UPDATE-->
					<tr>
						<td><?php echo JText::_("BLBE_UNBNOTJSPL_TEAMREG");?></td>
						<td colspan="2"><input type="checkbox" name="player_team_reg" value="1" <?php echo ($lists['player_team_reg']=='1')?" checked":"";?> /></td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_( 'BLBE_INVITEPL' ); ?>
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_INVITEPL' ); ?>::<?php echo JText::_( 'BLBE_TT_INVITEPL' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
						</td>
						<td colspan="2">
							<?php echo $lists['esport_invite_player']; ?>
						</td>
					
					</tr>
					<tr>
						<td>
							<?php echo JText::_( 'BLBE_INVITEPLUNREG' ); ?>
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_INVITEPLUNREG' ); ?>::<?php echo JText::_( 'BLBE_TT_INVITEPLUNREG' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
						</td>
						<td colspan="2">
							<input type="checkbox" name="esport_invite_unregister" value="1" <?php echo ($lists['esport_invite_unregister']=='1')?" checked":"";?> />
						</td>
					
					</tr>
					<!--<tr>
						<td>
							<?php echo JText::_( 'BLBE_PLAYERCANJOIN' ); ?>
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_PLAYERCANJOIN' ); ?>::<?php echo JText::_( 'BLBE_TT_PLAYERCANJOIN' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
						</td>
						<td>
							<input type="checkbox" name="esport_join_team" value="1" <?php echo ($lists['esport_join_team']=='1')?" checked":"";?> />
						</td>
					
					</tr>-->
					<tr>
						<td>
							<?php echo JText::_( 'BLBE_INVITEMATCH' ); ?>
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_INVITEMATCH' ); ?>::<?php echo JText::_( 'BLBE_TT_INVITEMATCH' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
						</td>
						<td colspan="2">
							<input type="checkbox" name="esport_invite_match" value="1" <?php echo ($lists['esport_invite_match']=='1')?" checked":"";?> />
						</td>
					
					</tr>
					<tr>
						<td>
							<?php echo JText::_( 'BLBE_JSMR_MARK_PLAYED' ); ?>
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_JSMR_MARK_PLAYED' ); ?>::<?php echo JText::_( 'BLBE_TT_JSMR_MARK_PLAYED' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
						</td>
						<td colspan="2">
							<input type="checkbox" name="jsmr_mark_played" value="1" <?php echo ($lists['jsmr_mark_played']=='1')?" checked":"";?> />
						</td>
					
					</tr>
					<tr>
						<td>
							&nbsp;
						</td>
						<td align="center">
							<?php echo JText::_( 'BLBE_JSMR_OWNTEAM' ); ?>
						</td>
						<td align="center">
							<?php echo JText::_( 'BLBE_JSMR_OPPOSITETEAM' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_( 'BLBE_JSMR_EDIT_MATCHRES' ); ?>
						</td>
						<td align="center">
							<input type="checkbox" name="jsmr_editresult_yours" value="1" <?php echo ($lists['jsmr_editresult_yours']=='1')?" checked":"";?> />
						</td>
						<td align="center">
							<input type="checkbox" name="jsmr_editresult_opposite" value="1" <?php echo ($lists['jsmr_editresult_opposite']=='1')?" checked":"";?> />
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_( 'BLBE_JSMR_EDIT_PLEVENTS' ); ?>
						</td>
						<td align="center">
							<input type="checkbox" name="jsmr_edit_playerevent_yours" value="1" <?php echo ($lists['jsmr_edit_playerevent_yours']=='1')?" checked":"";?> />
						</td>
						<td align="center">
							<input type="checkbox" name="jsmr_edit_playerevent_opposite" value="1" <?php echo ($lists['jsmr_edit_playerevent_opposite']=='1')?" checked":"";?> />
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_( 'BLBE_JSMR_EDIT_MATCHEVENTS' ); ?>
						</td>
						<td align="center">
							<input type="checkbox" name="jsmr_edit_matchevent_yours" value="1" <?php echo ($lists['jsmr_edit_matchevent_yours']=='1')?" checked":"";?> />
						</td>
						<td align="center">
							<input type="checkbox" name="jsmr_edit_matchevent_opposite" value="1" <?php echo ($lists['jsmr_edit_matchevent_opposite']=='1')?" checked":"";?> />
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_( 'BLBE_JSMR_EDIT_SQUAD' ); ?>
						</td>
						<td align="center">
							<input type="checkbox" name="jsmr_edit_squad_yours" value="1" <?php echo ($lists['jsmr_edit_squad_yours']=='1')?" checked":"";?> />
						</td>
						<td align="center">
							<input type="checkbox" name="jsmr_edit_squad_opposite" value="1" <?php echo ($lists['jsmr_edit_squad_opposite']=='1')?" checked":"";?> />
						</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<th width="100%" colspan='3'><?php echo JText::_('BLBE_REGTEAMFLD');?></th>
					</tr>
					<tr>
						<td colspan='3'>
							<table>
								<tr>
									<th width="50%"><?php echo JText::_('BLBE_FIELD');?></th>
									<th><?php echo JText::_('BLBE_ONREGPAGE');?></th>
									<th><?php echo JText::_('BLBE_REQUIRED');?></th>
								</tr>
								<tr>
									<td><?php echo JText::_('BLBE_CITY'); ?></td>
									<td><input type="checkbox" name="cf_team_city_enabled" value="1" <?php echo $lists['cf_team_city']['enabled'] ? 'checked="checked"' : ''; ?> /></td>
									<td><input type="checkbox" name="cf_team_city_required" value="1" <?php echo $lists['cf_team_city']['required'] ? 'checked="checked"' : ''; ?> /></td>
								</tr>
								<?php
								for($i=0;$i<count($lists['adf_team']);$i++){
									$regpl = $lists['adf_team'][$i];
									
									echo '<tr><td><input type="hidden" name="adf_tm[]" value="'.$regpl->id.'" />'.$regpl->name.'</td>';
									echo '<td><input type="checkbox" name="adf_reg_'.$regpl->id.'" value="1" '.($regpl->reg_exist?" checked":"").' /></td>';
									echo '<td><input type="checkbox" '.(($regpl->field_type == 2)?"DISABLED":"").' name="adf_rq_'.$regpl->id.'" value="1" '.($regpl->reg_require?" checked":"").' /></td>';
									if($regpl->published == 0){
										echo '<td>'.JText::_('BLBE_ATTEF').'</td>';
									}
									echo '</tr>';
								}
								if(!count($lists['adf_team'])){
									echo '<tr><td colspan="3">'.JText::_('BLBE_NOTEAMEXTRA').'</td></tr>';
								}
								?>
							</table>
						</td>
					</tr>
					<!--qwerqr-->
				</table>
			</div>
		</div>
		<div id="esport_cfg_div" class="tabdiv" style="display:none;">
		
			<div style="width:50%;float:left;">
				<table class="adminlists">
					<tr>
						<th colspan="2">
							<?php echo JText::_( 'BLBE_SEASADMRIGHTS' ); ?>
						</th>
					</tr>
					<tr>
						<td><?php echo JText::_("BLBE_ADMADDEXPAR");?></td>
						<td><input type="checkbox" name="jssa_addexteam_single" value="1" <?php echo ($lists['jssa_addexteam_single']=='1')?" checked":"";?> /></td>
					</tr>
					<tr>
						<td><?php echo JText::_("BLBE_ADMEDITPLSINGLE");?></td>
						<td><input type="checkbox" name="jssa_editplayer_single" value="1" <?php echo ($lists['jssa_editplayer_single']=='1')?" checked":"";?> /></td>
					</tr>
					<tr>
						<td><?php echo JText::_("BLBE_ADMDELPLS");?></td>
						<td><input type="checkbox" name="jssa_deleteplayers_single" value="1" <?php echo ($lists['jssa_deleteplayers_single']=='1')?" checked":"";?> /></td>
					</tr>
				</table>
			</div>
			<div style="width:50%;float:right;">
				<table class="adminlists">
					<tr>
						<th colspan="2">
							<?php echo JText::_( 'BLBE_SEASADMLEFT' ); ?>
						</th>
					</tr>
					<tr>
						<td><?php echo JText::_("BLBE_ADMEDITPL");?></td>
						<td><input type="checkbox" name="jssa_editplayer" value="1" <?php echo ($lists['jssa_editplayer']=='1')?" checked":"";?> /></td>
					</tr>
					<tr>
						<td><?php echo JText::_("BLBE_ADMEDITTM");?></td>
						<td><input type="checkbox" name="jssa_editteam" value="1" <?php echo ($lists['jssa_editteam']=='1')?" checked":"";?> /></td>
					</tr>
					<tr>
						<td><?php echo JText::_("BLBE_ADMDELPL");?></td>
						<td><input type="checkbox" name="jssa_deleteplayers" value="1" <?php echo ($lists['jssa_deleteplayers']=='1')?" checked":"";?> /></td>
					</tr>
					<tr>
						<td><?php echo JText::_("BLBE_ADMDELTM");?></td>
						<td><input type="checkbox" name="jssa_delteam" value="1" <?php echo ($lists['jssa_delteam']=='1')?" checked":"";?> /></td>
					</tr>
					<tr>
						<td><?php echo JText::_("BLBE_ADMADDEXTEAM");?></td>
						<td><input type="checkbox" name="jssa_addexteam" value="1" <?php echo ($lists['jssa_addexteam']=='1')?" checked":"";?> /></td>
					</tr>
				</table>
			</div>	
			<div style="clear:both;"></div>
		</div>
		<div id="social_cfg_div" class="tabdiv" style="display:none;">
		<table class="adminlists">
			<tr>
				<th colspan="2">
					<?php echo JText::_( 'BLBE_UNBLBUTTONS' ); ?>
				</th>
			</tr>
			<tr>
				<td width="200">
					<?php echo JText::_( 'BLBE_TWITBUTTON' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsb_twitter" value="1" <?php echo ($lists['jsb_twitter']=='1')?" checked":"";?> />
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_GPLUSBUTTON' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsb_gplus" value="1" <?php echo ($lists['jsb_gplus']=='1')?" checked":"";?> />
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_FBSHAREBUTTON' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsb_fbshare" value="1" <?php echo ($lists['jsb_fbshare']=='1')?" checked":"";?> />
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_FBLIKEBUTTON' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsb_fblike" value="1" <?php echo ($lists['jsb_fblike']=='1')?" checked":"";?> />
				</td>
			
			</tr>
		</table>
		<br />
		<table class="adminlists">
			<tr>
				<th colspan="2">
					<?php echo JText::_( 'BLBE_CHECKPAGES' ); ?>
				</th>
			</tr>
			<tr>
				<td width="200">
					<?php echo JText::_( 'BLBE_TABLE_LAYOUT' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsbp_season" value="1" <?php echo ($lists['jsbp_season']=='1')?" checked":"";?> />
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_TEAM_LAYOUT' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsbp_team" value="1" <?php echo ($lists['jsbp_team']=='1')?" checked":"";?> />
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_PLAYER_LAYOUT' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsbp_player" value="1" <?php echo ($lists['jsbp_player']=='1')?" checked":"";?> />
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_MATCH_LAYOUT' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsbp_match" value="1" <?php echo ($lists['jsbp_match']=='1')?" checked":"";?> />
				</td>
			
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_VENUE_LAYOUT' ); ?>
				</td>
				<td>
					<input type="checkbox" name="jsbp_venue" value="1" <?php echo ($lists['jsbp_venue']=='1')?" checked":"";?> />
				</td>
			
			</tr>
		</table>
		</div>
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="config" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="jscurtab" id="jscurtab" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>