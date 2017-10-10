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
defined( '_JEXEC' ) or die( 'Restricted access' );?>
<?php
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
	$lnk = "
		var arr = document.getElementsByClassName('joomsporttab');
		var tabName = '';
		for(var i =0; i<arr.length;i++){
			if(arr[i].className.indexOf('active')!= '-1'){
				tabName=arr[i].id;
				document.getElementById(tabName+'_div').style.display='block';break;
			}
		}
	
	 window.open('".JURI::base()."index.php?tmpl=component&option=com_joomsport&amp;view=table&amp;sid=".$lists["s_id"]."&amp;tab_n='+tabName,'jsmywindow','width=700,height=700');
	";
}else{
	$lnk = "window.print();";
}
?>


<!-- <module middle> -->
			<div class="module-middle solid">
				
				<!-- <back box> -->
				<?php if($this->lists['socbut']){?>
				<div class="back dotted">
					<div class="div_for_socbut">
						<?php echo $this->lists['socbut'];?>
					</div>
					<div class="clear"></div>
				</div>
				<?php } ?>
				<!-- </back box> -->
				
				<!-- <title box> -->
				<div class="title-box">
					<h2>
						
						<span itemprop="name"><?php echo $this->escape($this->ptitle); ?></span>
					</h2>
                    <a class="print" href="#" onClick="<?php echo $lnk;?>" title="<?php echo JText::_("BLFA_PRINT");?>"><?php echo JText::_("BLFA_PRINT");?></a>
				</div>
				<!-- </div>title box> -->
				<?php
				$tLogo = '';
				if($lists["curseas"]->logo && is_file('media/bearleague/'.$lists["curseas"]->logo)){
					$tLogo = "<img itemprop='image' ".getImgPop($lists["curseas"]->logo,4)." width='100' alt='".$this->params->get('page_title')."' style='margin-bottom:10px;' />";
					
				}
				if($lists['ext_fields'] || $tLogo){
				?>
				<div class="gray-box">
					<?php echo $tLogo;?>
					<table cellpadding="0" cellspacing="0" border="0" class="adf-fields-table">
						<?php echo $lists['ext_fields']?>		
					</table>
					<div class="gray-box-cr tl"><!-- --></div>
					<div class="gray-box-cr tr"><!-- --></div>
					<div class="gray-box-cr bl"><!-- --></div>
					<div class="gray-box-cr br"><!-- --></div>
				</div>
				<?php }?>
				<div class='jscontent'><span itemprop="description"><?php echo $lists["curseas"]->descr;?></span></div>
				<div style="clear:both;"></div>
				<!-- <tab box> -->
				<ul class="tab-box">
					<?php 
					if($this->tmpl!= 'component'){
						require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_joomsport'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'tabs.php');
						$etabs = new esTabs();
                                          if ($lists['is_group_matches'] || !$lists['is_matches']) {
                                            echo $etabs->newTab(JText::_('BL_TAB_TBL'),'etab_main','table',(($lists["unable_reg"] && $lists['season_par']->s_rules)?'hide':'vis'));
                                          }
					  if($lists['season_par']->s_rules){
						echo $etabs->newTab(JText::_('BL_TAB_RULES'),'etab_rules','tab_flag',($lists["unable_reg"]?'vis':'hide'));
					  }
					  if($lists['season_par']->s_descr){
						echo $etabs->newTab(JText::_('BL_TAB_ABOUTSEAS'),'etab_aboutm','tab_flag');
					  }
					}
					?>
				</ul>
				<!-- </tab box> -->
				
			</div>
			<!-- </module middle> -->
		<div class="content-module">
<?php 
if(($this->tmpl != 'component' || $lists["table_n"]=='etab_main')){?>
<div id="etab_main_div" class="tabdiv" <?php echo ($lists["unable_reg"] && $lists['season_par']->s_rules)?(($lists["table_n"]=='etab_main')?"style='display:block;'":"style='display:none;'"):"";?>>
<?php
if ($lists['is_group_matches'] || !$lists['is_matches']) { 
for($zzz=0;$zzz<count($lists["groups"]);$zzz++){
$show = false;
(isset($lists["gr_id"]))?(''):($lists["gr_id"] = '');//UPDATE
if(!$lists["gr_id"] || $lists["gr_id"] == $lists["groups"][$zzz]){
	$show = true;
}

if(!$lists["enbl_gr"]){
	$show = true;
}
if($show){
	if(isset($lists["groups_name"][$zzz])){
		echo '<h2 class="dotted">'.$lists["groups_name"][$zzz]."</h2>";
	}
?>
<?php //if(count($lists['matches'])):?>
<div style='overflow-x:auto;'>
<table class="season-list team-list" id="s_table_<?php echo $zzz?>" cellpadding="0" cellspacing="0" border="0">

	<thead>

	<tr>

		<th width="50" class="sort asc down" axis="int">

			<span><?php echo JText::_('BL_TBL_RANK');?></span>

		</th>
		<th class="sort asc" axis="string" style="text-align:left;">

			<span><?php echo $lists["t_single"]?JText::_('BL_PARTICS'):JText::_('BL_TBL_TEAMS');?></span>

		</th>
		
		<?php
		if(count($lists["soptions"])){
			foreach($lists["soptions"] as $key=>$value){
				if($key != 'emblem_chk'){
					if($key == 'otwin_chk' || $key == 'otlost_chk'){
						if($lists["enbl_extra"] == 1){
						?>
							<th class="sort asc" axis="int"><span><?php echo $lists["available_options"][$key];?></span></th>
						<?php
						}
					}elseif($key == 'grwinpr_chk' ){ //|| $key == 'percent_chk'
						//if($lists["enbl_gr"] == 1){
							?>
							<th class="sort asc" axis="int"><span><?php echo $lists["available_options"][$key];?></span></th>
						<?php
							//}
					}else{ //updt
						if($key == 'grwin_chk' || $key == 'grlost_chk'){
							if($lists["enbl_gr"] == 1){ ?>
								<th class="sort asc" axis="int"><span><?php echo $lists["available_options"][$key];?></span></th>
							<?php }
						}else{
							?>
								<th class="sort asc" axis="int"><span><?php echo $lists["available_options"][$key];?></span></th>
							<?php
						}

					}
				}
			}
		}
		?>
		
		<?php

		$num_ext = array();
			for($i=0;$i<count($lists["ext_fields_name"]);$i++){
				$var_ex = 0;

				for($j=0;$j<count($lists['v_table']);$j++){					
					if(!empty($lists['v_table'][$j]['ext_fields'][$i])){

						$var_ex = 1;
						$num_ext[$i] = $i;
					}
				}
				if($var_ex){
			?>

			<th class="sort asc" axis="string"><span><?php echo $lists["ext_fields_name"][$i]->name;?></span></th>	

			<?php	
				}else{
					echo "<th></th>";
				}
			}
		//}
	

		?>

	</tr>

	</thead>

	<tbody>

	<?php

	$ranks = 1;

	for($i=0;$i<count($lists["v_table"]);$i++){

		$team = $lists["v_table"][$i];

		

		$color = '';

		if(isset($lists["colors"][$ranks])){

			$color = 'style="background-color:'.$lists["colors"][$ranks].'"';

		}

		if($team['yteam']){

			$color = 'style="background-color:'.$team['yteam'].'"';

		}

		

		if($team['g_id'] == $lists["groups"][$zzz]){

		?>

		<tr class="<?php echo $ranks % 2?"gray":"";?>" <?php echo $color?>>

			<td><?php echo $ranks?></td>
			<?php 
				$teamembl = '';
				$st = '';
				if(isset($lists["soptions"]['emblem_chk']) && $lists["soptions"]['emblem_chk'] == 1) 
				{
                    if($lists["t_single"]){
                        //echo $team['tid']."</br>";
                        $teamembl = JHtml::_('images.getPlayerThumb',  $team['tid'],0, $team['name'],0,($lists['teamlogo_height']?$lists['teamlogo_height']:29));
                        $st = 'display: table-cell;padding-left:8px;vertical-align:middle;';
                       //print_r($team);
                    }else{
                        $teamembl = JHtml::_('images.getTeamEmbl',  $team['tid'],1, $team['name'],0,($lists['teamlogo_height']?$lists['teamlogo_height']:29));
                        $st = 'display: table-cell;padding-left:8px;vertical-align:middle;';
                    }
					/*if($team['t_emblem'] && is_file('media/bearleague/'.$team['t_emblem'])){
						$teamembl = '<div class="team-embl" style=" width:'.($lists['teamlogo_height']?$lists['teamlogo_height']:29).'px; height:'.($lists['teamlogo_height']?$lists['teamlogo_height']:29).'px;"><img player-ico" '.getImgPop($team['t_emblem'],5,$lists['teamlogo_height'],$lists['teamlogo_height']).' title="'.$team['name'].'" alt="'.$team['name'].'" style="max-height:'.($lists['teamlogo_height']?$lists['teamlogo_height']:29).'px;max-width:'.($lists['teamlogo_height']?$lists['teamlogo_height']:29).'px;" /></div>';
						$st = 'display: table-cell;padding-left:8px;vertical-align:middle;';
					}else{
						$teamembl = '<div class="team-embl" style=" width:'.($lists['teamlogo_height']?$lists['teamlogo_height']:29).'px; height:'.($lists['teamlogo_height']?$lists['teamlogo_height']:29).'px;"><img src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" title="'.$team['name'].'" alt="'.$team['name'].' width="100%" height="100%"" /></div>';
						$st = 'display: table-cell;padding-left:8px;vertical-align:middle;';
					}*/
				} 
			
			if($lists["t_single"]){
				$link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$team['tid'].'&sid='.$lists["s_id"].'&Itemid='.$Itemid); 
			}else{
				$link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$team['tid'].'&sid='.$lists["s_id"].'&Itemid='.$Itemid); 
			}
			?>
			
			<td class="teams" nowrap><?php echo $teamembl;?><p style="<?php echo $st;?>"><a href="<?php echo $link;?>"><?php echo $team['name']?></a></p></td>
			
			<?php
			if(count($lists["soptions"])){
				foreach($lists["soptions"] as $key=>$value){
					if($key != 'emblem_chk'){
						if($key == 'otwin_chk' || $key == 'otlost_chk'){
							if($lists["enbl_extra"] == 1){
							?>
								<td><?php echo $team[$key];?></td>
							<?php
							}
						}elseif( $key == 'percent_chk'){ ///$key == 'grwinpr_chk' ||
							//if($lists["enbl_gr"] == 1){
							?>
								<td><?php echo ($team[$key] == 1)?1.000:substr(sprintf("%.3f",round($team[$key],3)),1);?></td>
							<?php
							//}
						}else{
							if($key == 'grwin_chk' || $key == 'grlost_chk'){
								if($lists["enbl_gr"] == 1){ ?>
									<td><?php echo $team[$key];?></td>
								<?php }
							}else{
							?>
								<td><?php echo $team[$key];?></td>
							<?php
							}
						}	
					}
				}
			}
			?>
			
				
			<?php    
				for($j=0;$j<count($this->lists["ext_fields_name"]);$j++){
					$value = isset($team['ext_fields'][$j])?($team['ext_fields'][$j]):("&nbsp;");
					echo "<td >".$value."</td>";
				}
			
			?>

			

		</tr>

		<?php

		$ranks++;

		}

		

	}

	?>

	</tbody>

</table>
</div>
<?php //endif;?>
<?php if($this->tmpl != 'component'){?>
<script type="text/javascript">

new Grid(jQuery('#s_table_<?php echo $zzz?>').get(0)); 

</script>
<?php } ?>
<?php
}

if($lists["enbl_gr"]){
	echo isset($lists["bonus_not"][$lists["groups"][$zzz]])?('<div class="js_botbonusp">'.JText::_('BLFA_SEASBONUSTABLE').'</div>'.$lists["bonus_not"][$lists["groups"][$zzz]]):'';
}else{
	if(count($lists["bonus_not"])){
		echo '<div class="js_botbonusp">'.JText::_('BLFA_SEASBONUSTABLE').'</div>';
		foreach($lists["bonus_not"] as $bons){
			echo $bons;
		}
	}
}
}
if(!count($lists["groups"])){
	echo JText::_('BLFA_SGROUPSNOTABLE');
}
//echo $this->bonus_not[0];
//var_dump($this->bonus_not);
?>

<?php
if(count($lists["playoffs"])){

$prev_mday = 0;

for ($i=0;$i<count($lists["playoffs"]);$i++){

	$playoff_match = $lists["playoffs"][$i];

	if($playoff_match->m_id != $prev_mday){

		
		if($i){
			echo "</table>";
		}
		echo '<h2 class="solid">'.$playoff_match->m_name.'</h2>';
                echo "<div style='overflow-x:auto;'>";
		echo '<table class="match-day" cellpadding="0" cellspacing="0" border="0">';
		$prev_mday = $playoff_match->m_id;

	}

	?>

	<tr class="dotted">

			<td width="25"><!-- --></td>
			
			<td class="team-h"><span><?php echo $playoff_match->home?></span></td>
			<td class="team-ico-h"><!-- -->
			<?php
				if(!$this->lists['t_single']){
					if($playoff_match->emb1 && is_file('media/bearleague/'.$playoff_match->emb1)){
						echo '<div class="team-embl"><img '.getImgPop($playoff_match->emb1,1).'  alt="'.$playoff_match->home.'" /></div>';
					}else{
						echo '<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30" height="30" alt="">';
					}
				}
			?>
			</td>
			<td class="score"><span class="score">

				<a class="plyoff-matches" href="<?php echo JRoute::_('index.php?option=com_joomsport&view=match&id='.$playoff_match->mid.'&Itemid='.$Itemid)?>">
					<b class="score-h">
						<?php echo $playoff_match->m_played?$playoff_match->score1:'-'?>
					</b>
					<b>:</b>
					<b class="score-a">
						<?php echo $playoff_match->m_played?$playoff_match->score2:'-'; ?>
					</b>
					<?php
					if(@$lists["enbl_extra"] && $playoff_match->is_extra)
					{ 
						$class_ext = ($playoff_match->score1 > $playoff_match->score2)?"extra-time-h":"extra-time-g";
						echo '<span class="'.$class_ext.'" title="'.JText::_('BLFA_TEAM_WON_ET').'">'.JText::_('BL_RES_EXTRA').'</span>';
						
					}
					?>
				</a>

			</td>
			<td class="team-ico-a"><!-- -->
					<?php
						if(!$this->lists['t_single']){
							if($playoff_match->emb2 && is_file('media/bearleague/'.$playoff_match->emb2)){
								echo '<div class="team-embl"><img '.getImgPop($playoff_match->emb2,1).'  alt="'.$playoff_match->away.'" /></div>';
							}else{
								echo '<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/players-ico.png" width="30" height="30" alt="">';
							}
						}
					?>
					</td>
			<td class="team_a"><span><?php echo $playoff_match->away?></span></td>

			

	</tr>
<?php } ?>
</table>
</div>

	<?php

}
}

if($this->tmpl!= 'component'){
	echo $this->lists['knock_layout'];
}
?>
</div>
<?php }?>
<?php if($lists["season_par"]->s_rules):?>
<?php if($this->tmpl != 'component' || $lists["table_n"]=='etab_rules'){?>
<div id="etab_rules_div" class="tabdiv" <?php echo ($lists["unable_reg"] || $lists["table_n"]=='etab_rules')?"style='display:block;'":"style='display:none;'";?>>
	<?php echo $lists["season_par"]->s_rules;?>
</div>
<?php }?>
<?php endif;?>
<?php if($lists['season_par']->s_descr):
        JPluginHelper::importPlugin('content'); 
        $dispatcher = JDispatcher::getInstance(); 
        $results = @$dispatcher->trigger('onContentPrepare', array ('content'));
        $lists['season_par']->s_descr = JHTML::_('content.prepare', $lists['season_par']->s_descr);
?> 
<?php if($this->tmpl != 'component' || $lists["table_n"]=='etab_aboutm'){?>
<div id="etab_aboutm_div" class="tabdiv" <?php echo ($lists["table_n"]=='etab_aboutm')?"style='display:block;'":"style='display:none;'";?>>
	<?php echo $lists['season_par']->s_descr;?>
</div>
<?php }?>
<?php endif;?>
<br />

</div>
<input type="hidden" name="jscurtab" id="jscurtab" value="" />
<!-- Attention! To remove the branding link you must pay the branding removal license here http://www.joomsport.com/web-shop/custom-fees/branding-free-license.html . Please support us by doing so using the free version of JoomSport!  Thank you!-->


<!-----KNOCK ----->
<div class="content-module">
   <!-- <div id="etab_main_div" class="tabdiv" <?php echo ($this->lists["unable_reg"] && $this->lists['season_par']->s_rules)?"style='display:none;'":"style='display:block;'";?>>
        <?php

        ?>
    </div>-->
    <?php if($this->lists['season_par']->s_rules):?>
    <!--<div id="etab_rules_div" class="tabdiv" <?php echo $this->lists["unable_reg"]?"style='display:block;'":"style='display:none;'";?>>
        <?php echo $this->lists['season_par']->s_rules;?>
    </div>-->
    <?php endif;?>
    <?php if($lists["curseas"]->s_descr):
    JPluginHelper::importPlugin('content');
    $dispatcher = JDispatcher::getInstance();
    $results = @$dispatcher->trigger('onContentPrepare', array ('content'));
    $lists["curseas"]->s_descr = JHTML::_('content.prepare', $lists["curseas"]->s_descr);
    ?>
    <div id="etab_aboutm_div" class="tabdiv" style='display:none;'>
        <?php echo $this->lists["curseas"]->s_descr;?>
    </div>
    <?php endif;?>
    <input type="hidden" name="jscurtab" id="jscurtab" value="" />
    
    <?php if($this->lists['jsbrand_on'] == 1):?>
    <div id="copy" class="copyright"><a href="http://joomsport.com">JoomSport - sport Joomla league</a></div>
    <?php endif;?>
</div>
