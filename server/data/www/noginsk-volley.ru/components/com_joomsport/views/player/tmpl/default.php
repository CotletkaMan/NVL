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
	global $Itemid;
	$Itemid = JRequest::getInt('Itemid');
    JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
	$lists = $this->lists;
?>

<?php
echo $lists["panel"];
?>
<form action="<?php echo JRoute::_("index.php?option=com_joomsport&task=player&id=".$lists["player"]->id."&sid=0&page=1&Itemid=".$Itemid);?>" method="post" name="adminForm" id="adminForm">

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

			<h2><span itemprop="name"><?php
                //echo $this->escape($this->params->get('page_title'));
                echo $this->escape($this->ptitle);

                ?></span></h2>
			<div class="select-wr">
			<?php echo $lists["tourn_name"];?>
			<span class="down"><!-- --></span>
			<?php echo $this->lists['tourn']?>
			</div>
		</div>
		<!-- </div>title box> -->
		
		<!-- <tab box> -->
		<ul class="tab-box">
			<?php 
			 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_joomsport'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'tabs.php');
			 $etabs = new esTabs();
			  echo $etabs->newTab(JText::_('BL_TAB_PLAYER'),'etab_player','players',($this->lists['jscurtab'] == 'etab_player')?'vis':'');
			  echo $etabs->newTab(JText::_('BL_TAB_STAT'),'etab_stat','statistic',($this->lists['jscurtab'] == 'etab_stat')?'vis':'');
			  //if(count($lists["matches"])){
				 echo  $etabs->newTab(JText::_('BL_TAB_MATCHES'),'etab_match','tab_flag',($this->lists['jscurtab'] == 'etab_match')?'vis':'');
			  //}
			  if(count($lists["photos"])){
				echo $etabs->newTab(JText::_('BL_TAB_PHOTOS'),'etab_photos','photo',($this->lists['jscurtab'] == 'etab_photo')?'vis':'');
			  }
			  
			?>
		</ul>
		<!-- </tab box> -->
		
	</div>
	<!-- </module middle> -->
<!-- <content module> -->
		<div class="content-module">
<div id="etab_player_div" class="tabdiv" <?php echo $this->lists['jscurtab'] != 'etab_player'?"style='display:none;'":""?>>
	<!-- <gray box> -->

	<?php
    //print_r($lists["player"]->first_name);
        echo JHtml::_('images.getViewContent',  $Itemid, $lists["player"],$lists["ext_fields"],$lists["player"]->about,0,$lists['photos'],$lists["teams_name"],$lists["player"]);
	?>
        <?php 
        if(count($this->lists["race_res"])){
        ?>
        <div>
            <table class="season-list team-list">
                <tr>
                    <th>
                        <?php echo JText::_('BLFA_MATCHDAY');?>
                    </th>
                    <th>
                        <?php echo JText::_('BLFA_DATE');?>
                    </th>
                    <th>
                        <?php echo JText::_('BLFA_TITLE');?>
                    </th>
                    <th>
                        <?php echo JText::_('BLFA_RESULTS');?>
                    </th>
                    <th>
                        <?php echo JText::_('BL_TBL_RANK');?>
                    </th>
                </tr>
            <?php if(isset($this->lists["race_res"]) && count($this->lists["race_res"])){
                $intA = 0;
                foreach($this->lists["race_res"] as $res){
                    ?>
                    <tr <?php echo ($intA % 2)?'':'class="gray"';?>>
                        <td>
                            <?php echo $res->m_name?>
                        </td>
                        <td>
                            <?php echo $res->start_date;?>
                            <?php echo '<br />' . $res->end_date;?>
                        </td>
                        <td>
                            <?php echo $res->round_title?>
                        </td>
                        
                        <td>
                            <?php echo $res->result_string?>
                        </td>
                        <td>
                            <?php echo $res->rank ? $res->rank : '';?>
                        </td>
                    </tr>
                    <?php
                    $intA++;
                }
            }?>
            </table>
        </div> 
        <?php } ?>
</div>
<div id="etab_stat_div" class="tabdiv" <?php echo $this->lists['jscurtab'] != 'etab_stat'?"style='display:none;'":""?>>

				
				<table class="player-statistic" cellpadding="0" cellspacing="0" border="0">
					<?php
					for($i=0;$i<count($lists["stat_array"]);$i++){
						$stats = $lists["stat_array"][$i];
						echo "<tr class='dotted'>";
						echo "<td class='dotted'>";
						echo $stats[2];
						echo $stats[0];
						echo "</td>";
						echo "<td class='dotted'>";
						echo $stats[1];
						echo "</td>";
						echo "</tr>";
					}
					?>
				</table>
				
		

</div>

<div id="etab_match_div" class="tabdiv" <?php echo $this->lists['jscurtab'] != 'etab_match'?"style='display:none;'":""?>>
<?php if (count($lists["matches"])){ ?>
<table id="calendar" cellpadding="0" cellspacing="0" class="match-day">
<?php
	for($i=0;$i<count($lists["matches"]);$i++){
		$match = $lists["matches"][$i];
		?>
		<tr class="<?php echo $i%2?"gray":"";?>">
			<td class="m_name" nowrap="nowrap"><?php echo $match->m_name.":"?><br /><font style="font-size:85%;font-style:italic;">
				<?php //echo $this->formatDate($match->m_date . ' ' . $match->m_time);
					if($match->m_date){

                            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$match->m_date))
                            {
                                echo $this->formatDate($match->m_date . ' ' . $match->m_time);
                            }else{
                                echo $match->m_date;
                            }

					}
				?>
			</font></td>
			<td class="team_h"><?php echo $match->home?></td>
			<td class="score"><span class="score">
				<?php 
				if($match->m_played == 1){
					echo '<b class="score-h">'.$match->score1?></b>
					<b>:</b>
					<b class="score-a">
					<?php 
					echo $match->score2; 
					echo "</b>";
					if($match->is_extra)
					{ 
						$class_ext = ($match->score1 > $match->score2)?"extra-time-h":"extra-time-g";
						echo '<span class="'.$class_ext.'">'.JText::_('BL_RES_EXTRA').'</span>';
						
					}
					
				}else{
					echo '<b class="score-h">-</b>';?>
					<b>:</b>
					<b class="score-a">-</b>
					<?php
				}
				?>
				</span>
			</td>
			<td class="team-a" style="padding-left:25px;"><?php echo $match->away?></td>
			<?php if($lists["locven"]){?>
			<td><?php echo getJS_Location($match->mid);?></td>
			<?php } else { echo "<td>&nbsp;</td>";} ?>
			<td class="match_details"> 
				<a class="button-details" href="<?php echo JRoute::_('index.php?option=com_joomsport&task=view_match&id='.$match->mid.'&Itemid='.$Itemid).'"><span>'.JText::_('BL_LINK_DETAILMATCH').'</span>'?></a>
			</td>   
		</tr>
		
		<?php
	}
	?>
		<?php if(count($lists["matches"])):?>
		<tr>
			<td colspan="13" align="center" style="padding-top:10px;">
				<?php 
					$link_page = "index.php?option=com_joomsport&view=player&sid=".$this->s_id."&id=".$lists["player"]->id."&Itemid=".$Itemid."&jscurtab=etab_match&jslimit=".$this->page->limit;
					
					echo "<div class='page_limit'>".$this->page->getLimitPage()."</div>";
					echo $this->page->getPageLinks($link_page); 
					$limit = $this->page->limit;
					($limit==0)?($limit = 'All'):('');
					echo "<div class='selectPag'><span style='position:relative;top:7px;right:5px;'>".JText::_('BL_TAB_DISPLAY')."</span><span class='select_box'><span class='num_box'>".$limit."</span></span>".$this->page->getLimitBox()."</div>";

				?>
			</td>
		</tr>
		<?php endif;?>
</table>
<?php } ?>
</div>

<?php
if(count($lists["photos"])){
?>
<div id="etab_photos_div" class="tabdiv" <?php echo $this->lists['jscurtab'] != 'etab_photos'?"style='display:none;'":""?>>

<!-- <player gallery> -->
<ul class="player-gallery">
<?php					

    echo JHtml::_('images.getGalleryHTML',  $lists["photos"]);
echo "</div>";
}

?>
<input type="hidden" name="jscurtab" id="jscurtab" value="<?php echo $this->lists['jscurtab'];?>" />

</form>
</div>
<!-- </content module> -->