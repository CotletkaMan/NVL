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
	$rows = $this->rows;
	$page = $this->page;
	
	global $Itemid;
	$Itemid = JRequest::getInt('Itemid');
	
?>
<script type="text/javascript">
function bl_submit(task,chk){
	if(chk == 1 && document.adminForm.boxchecked.value == 0){
		alert('<?php echo JText::_('BLFA_SELECTITEM')?>');
	}else{
		document.adminForm.task.value = task;
		document.adminForm.submit();	
	}
}
</script>
<?php
echo $this->lists["panel"];
?>

<!-- <module middle> -->
<div class="module-middle solid">
	
	<!-- <back box> -->
	<!-- <div class="back dotted"><a href="#" title="Back">&larr; Back</a></div> -->
	<!-- </back box> -->
	<!-- <title box> -->
		<div class="title-box padd-bot">
			<?php if($this->acl == 2){?>
				<h2><?php echo JText::_('BLFA_PLAYERSLIST')?>
				</h2>
				<div class="select-wr">
					<form action='<?php echo JURI::base();?>index.php?option=com_joomsport&task=team_edit&controller=moder&Itemid=<?php echo $Itemid?>' method='post' name='chg_team'>
						<div style="position:relative;"><span class='down'><!-- --></span><?php echo $this->lists['tm_filtr'];?></div>
						<!--div style="position:relative;"><span class='down'><!-- -- ></span><?php //echo $this->lists['seass_filtr'];?></div-->
					</form>
				</div>
			<?php }else{ ?>
				<h4><?php echo $this->lists['tournname'];?></h4>
				<h2><?php echo JText::_('BLFA_PLAYERSLIST')?></h2>
			<?php } ?>
		</div>
		<!-- </div>title box> -->
		<!-- <tab box> -->
		<ul class="tab-box-main">
			<?php if($this->acl == 2){?>
				<li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=moder&view=edit_team&tid='.$this->tid.'&Itemid='.$Itemid);?>" title=""><span><img src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" class="star" /><?php echo JText::_('BLFA_TEAM')?></span></a></li>
				<?php if($this->lists["enmd"]):?>
				<li><a href="<?php echo JRoute::_( 'index.php?option=com_joomsport&controller=moder&task=edit_matchday&tid='.$this->tid.'&Itemid='.$Itemid )?>" title=""><span><img src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" /><?php echo JText::_('BLFA_MATCHDAY')?></span></a></li>
				<?php endif;?>
				<li class="active"><a href="<?php echo JRoute::_( 'index.php?option=com_joomsport&controller=moder&view=admin_player&tid='.$this->tid.'&Itemid='.$Itemid)?>" title=""><span><img class="players" src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" /><?php echo JText::_('BLFA_PLAYER')?></span></a></li>
			<?php }else{ ?>
				<li><a href="<?php echo JRoute::_( 'index.php?option=com_joomsport&controller=admin&view=admin_matchday&sid='.$this->s_id.'&Itemid='.$Itemid )?>" title=""><span><img src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" /><?php echo JText::_('BLFA_MATCHDAY')?></span></a></li>
				<?php if(!$this->lists['t_single']){?>
				<li><a href="<?php echo JRoute::_( 'index.php?option=com_joomsport&controller=admin&task=admin_team&sid='.$this->s_id.'&Itemid='.$Itemid)?>" title=""><span><img class="star" src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" /><?php echo JText::_('BLFA_ADMIN_TEAM')?></span></a></li>
				<?php } ?>
				<li class="active"><a href="<?php echo JRoute::_( 'index.php?option=com_joomsport&controller=admin&task=admin_player&sid='.$this->s_id.'&Itemid='.$Itemid)?>" title=""><span><img class="players" src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" /><?php echo JText::_('BLFA_PLAYER')?></span></a></li>
		
			<?php } ?>
			
		</ul>
		<!-- </tab box> -->
	
</div>
<!-- </module middle> -->
<!-- <control bar> -->
<div class="control-bar-wr dotted">
	<ul class="control-bar">
		<?php if($this->acl == 2){?>
			<li><a class="add" href="javascript:void(0);" title="<?php echo JText::_('BLFA_NEW')?>" onclick="javascript:bl_submit('adplayer_edit',0);return false;"><?php echo JText::_('BLFA_NEW')?></a></li>
			<li><a class="edit" href="javascript:void(0);" title="<?php echo JText::_('BLFA_EDIT')?>" onclick="javascript:bl_submit('adplayer_edit',1);return false;"><?php echo JText::_('BLFA_EDIT')?></a></li>
			<li><a class="delete" href="javascript:void(0);" title="<?php echo JText::_('BLFA_DELETE')?>" onclick="javascript:bl_submit('mdplayer_del',1);return false;"><?php echo JText::_('BLFA_DELETE')?></a></li>
		<?php }else{ ?>
			<?php if($this->lists["jssa_addexteam_single"] == 1 && $this->lists['t_single'] == 1):?>
			<li><a class="add" href="javascript:void(0);" onclick="javascript:getObj('div_addexpl').style.display='block';return false;" title="<?php echo JText::_('BLFA_ADDEXPL')?>"><?php echo JText::_('BLFA_ADDEXPL')?></a></li>
			<?php endif;?>
			<li><a class="add" href="javascript:void(0);" title="<?php echo JText::_('BLFA_NEW')?>" onclick="javascript:bl_submit('adplayer_edit',0);return false;"><?php echo JText::_('BLFA_NEW')?></a></li>
			<?php if($lists["jssa_editplayer"]){?>
			<li><a class="edit" href="javascript:void(0);" title="<?php echo JText::_('BLFA_EDIT')?>" onclick="javascript:bl_submit('adplayer_edit',1);return false;"><?php echo JText::_('BLFA_EDIT')?></a></li>
			<?php }?>
			<?php if($lists["jssa_deleteplayers"]){?>
			<li><a class="delete" href="javascript:void(0);" title="<?php echo JText::_('BLFA_REMOVE')?>" onclick="javascript:bl_submit('adplayer_del',1);return false;"><?php echo JText::_('BLFA_REMOVE')?></a></li>
			<?php }?>
		<?php } ?>
	</ul>
</div>
<!-- </control bar> -->

<!-- <content module> -->
	<div class="content-module admin-mo-co">


<?php
		if(!count($rows)){
			echo "<div id='system-message'><dd class='notice'><ul>".JText::_('BLFA_NOITEMS')."</ul></dd></div>";
		}
		$newad = '';
		?>
		<?php if($this->acl == 1){
			$link = JURI::base()."index.php?option=com_joomsport&controller=admin&sid=".$this->s_id."&Itemid=".$Itemid;
		    if($this->lists["jssa_addexteam_single"] == 1 && $this->lists['t_single'] == 1){
				$newad .= '<div style="display:none;padding:10px;" id="div_addexpl">';
					$newad .= $this->lists['players_ex'];
					$newad .= '<button class="send-button" onclick="javascript:if(document.adminForm.players_ex.value != 0){bl_submit(\'add_ex_player\',0);};return false;" />';
						$newad .='<span>'.JText::_("BLFA_ADD").'</span>';
					$newad .='</button>';
				$newad .='</div>';
			}
		}else{ 
			$link = JURI::base()."index.php?option=com_joomsport&controller=moder&tid=".$this->tid."&Itemid=".$Itemid;
		} 
		?>
		<form action="<?php echo $link;?>" method="post" name="adminForm" id="adminForm">
		<?php echo $newad;?>
		<table class="season-list" cellpadding="0" cellspacing="0" border="0">
		<thead>
			<tr>
				<th width="2%" align="left">
					<?php echo JText::_( 'BLFA_NUM' ); ?>
				</th>
				<th width="2%" style="text-align:center;">
					<?php if($this->acl == 1 && ($lists["jssa_editplayer"] == 1 OR $lists["jssa_deleteplayers"])){?>
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);;" />
					<?php }elseif($this->acl == 2){ ?>
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);;" />
					<?php } ?>
				</th>
				<th class="title" style="padding-left:11px;">
					<?php echo JText::_( 'BLFA_PLAYERR' ); ?>
				</th>
				
			</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="13" style="text-align:center;">
				<?php 
					if($this->acl == 1){
						$link_page = "index.php?option=com_joomsport&view=admin_player&controller=admin&sid=".$this->s_id."&Itemid=".$Itemid."&jslimit=".$this->page->limit;
					
					}elseif($this->acl == 2){
						$link_page = "index.php?option=com_joomsport&view=admin_player&controller=moder&tid=".$this->tid."&Itemid=".$Itemid."&jslimit=".$this->page->limit;
					
					}
					echo $this->page->getPageLinks($link_page); 
					echo $this->page->getLimitBox();

				?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		if( count( $rows ) ) {
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	= $rows[$i];
			JFilterOutput::objectHtmlSafe($row);
			if($this->acl == 2){
				$link = JRoute::_( 'index.php?option=com_joomsport&controller=moder&task=adplayer_edit&tid='.$this->tid.'&cid[]='. $row->id.'&Itemid='.$Itemid );
			}else{
				if($lists["jssa_editplayer"]){
					$link = JRoute::_( 'index.php?option=com_joomsport&task=adplayer_edit&controller=admin&sid='.$this->s_id.'&cid[]='. $row->id.'&Itemid='.$Itemid );
				}else{
					$link = JRoute::_( 'index.php?option=com_joomsport&task=player&sid='.$this->s_id.'&id='. $row->id.'&Itemid='.$Itemid );
				}
			}
			$checked 	= @JHTML::_('grid.checkedout',   $row, $i );
			//$published 	= JHTML::_('grid.published', $row, $i);
			?>
			<tr class="<?php echo $i % 2?"gray":""; ?>">
				<td>
					<?php echo ($i+1 + (($this->page->page-1)*$this->page->limit));?>
				</td>
				<td>
					<?php 
						if($this->acl == 1 && ($lists["jssa_editplayer"] == 1 OR $lists["jssa_deleteplayers"])){
							echo $checked;
						}elseif($this->acl == 2){
							echo $checked;
						}
					?>	
				</td>
				<td>
                    <?php
                    if($row->photo && is_file('media/bearleague/'.$row->photo)){
                        echo '<div class="team-embl" style="margin-right:8px;"><img class="team-embl  player-ico" '.getImgPop($row->photo,1).' alt="" /></div><p class="player-name" style="display: table-cell;padding-left:7px;">';
                    }else{
                        echo '<img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/season-list-player-ico.gif" width="30" height="30" alt=""><p class="player-name">';
                    }
                    ?>
					<?php
						echo '<a href="'.$link.'">'.$row->first_name.' '.$row->last_name.'</a>';
					?>
					</p>
				</td>
				
				
				
			</tr>
			<?php
		}
		} 
		?>
		</tbody>
		</table>
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="admin_player" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
</div>