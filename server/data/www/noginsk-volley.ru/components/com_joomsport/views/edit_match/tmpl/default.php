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
	$row = $this->row;
	$lists = $this->lists;
	$s_id = $this->lists["s_id"];
	$Itemid = JRequest::getInt('Itemid');
	$user	= JFactory::getUser();


?>
<?php
echo $lists["panel"];
?>
<!-- <module middle> -->
<div class="module-middle solid">
	
	<!-- <back box> -->
	<!-- <div class="back dotted"><a href="#" title="Back">&larr; Back</a></div> -->
	<!-- </back box> -->
	<!-- <tab box> -->
	<div class="title-box padd-bot">
			<h2>
				<?php echo JText::_("BLFA_MATCH_EDIT");?>
			</h2>
			<?php if($this->acl == 2):?>
			<div class="select-wr">
				<form action='<?php echo JURI::base();?>index.php?option=com_joomsport&task=team_edit&controller=moder&Itemid=<?php echo $Itemid?>' method='post' name='chg_team'>
					<div style="position:relative;text-align: right;"><span class='down'><!-- --></span><?php echo $this->lists['tm_filtr'];?></div>
					<div style="position:relative;text-align: right;"><?php echo $this->lists["tourn_name"];?><span class='down'><!-- --></span><?php echo $this->lists['seass_filtr'];?></div>
				</form>
			</div>
			<?php endif;?>
	</div>		
		<ul class="tab-box">
			<?php
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_joomsport'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'tabs.php');
			$etabs = new esTabs();
			echo $etabs->newTab(JText::_('BLFA_MAIN'),'match_conf','star','vis');
			if(!$lists['t_single']) {
				echo $etabs->newTab(JText::_('BLFA_SQUARD'),'squard_conf','');
			}
			if($this->acl == 2){
				if($lists["esport_invite_match"]){
					echo $etabs->newTab(JText::_('BLFA_INVMATCH'),'invmatch_conf','');
				}
			}
			?>
		</ul>
		<!-- </tab box> -->
	
</div>
<!-- </module middle> -->
<!-- <control bar> -->
<div class="control-bar-wr dotted">
	<ul class="control-bar">
		<li><a class="save" href="javascript:void(0);" title="<?php echo JText::_('BLFA_SAVE')?>" onclick="javascript:submitbutton('match_save');return false;"><?php echo JText::_('BLFA_SAVE')?></a></li>
		<li><a class="apply" href="javascript:void(0);" title="<?php echo JText::_('BLFA_APPLY')?>" onclick="javascript:submitbutton('match_apply');return false;"><?php echo JText::_('BLFA_APPLY')?></a></li>
		<?php if($this->acl == 1){?>
		<li><a class="delete" href="<?php echo JRoute::_('index.php?option=com_joomsport&view=edit_matchday&controller=admin&sid='.$s_id.'&cid[]='.$row->m_id.'&Itemid='.$Itemid);?>" title="<?php echo JText::_('BLFA_CLOSE')?>"><?php echo JText::_('BLFA_CLOSE')?></a></li>
		<?php }elseif($this->acl == 2){?>
		<li><a class="delete" href="<?php echo JRoute::_("index.php?option=com_joomsport&controller=moder&view=edit_matchday&tid=".$lists["tid"]."&mid=".$row->m_id."&sid=".$s_id."&Itemid=".$Itemid)?>" title="<?php echo JText::_('BLFA_CLOSE')?>"><?php echo JText::_('BLFA_CLOSE')?></a></li>
		<?php } ?>
	</ul>
</div>
<!-- </control bar> -->
<!-- <content module> -->
	<div class="content-module admin-mo-co">
<?php
		$editor = JFactory::getEditor();
		JHTML::_('behavior.tooltip');
		
		?>

		<script type="text/javascript">
		<!--
		Joomla.submitbutton = function(task) {
			submitbutton(task);
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			
				if(pressbutton == 'match_apply'){
						form.isapply.value='1';
						pressbutton = 'match_save';
					}
					if( '<?php echo $this->acl;?>' == 1 && '<?php echo $lists['t_type']?>'==0 && document.adminForm.m_id.value == 0){
						alert("<?php echo JText::_('BLFA_PLSELMD');?>");return false;
					}
					
					var regE = /[0-2][0-9]:[0-5][0-9]/;
					if(!document.adminForm.m_time.value.test(regE) && document.adminForm.m_time.value != ''){
						alert("<?php echo JText::_( 'BLBE_JSMDNOT7' ); ?>");return;
					}else{
						if(pressbutton == 'match_invite' && !document.adminForm.inv_mtitle.value){
							alert("<?php echo JText::_( 'BLFA_JSMFILLTITLE' ); ?>");return;
						}else{
							submitform( pressbutton );
							return;
						}
					}
			
		}	
		
		function bl_add_event(){
			var cur_event = getObj('event_id');
			
			//var e_count = getObj('e_count').value;
			var e_minutes = getObj('e_minutes').value;
			var e_player = getObj('playerz_id');
			var re_count = getObj('re_count').value;
			if (cur_event.value == 0) {
				alert("<?php echo JText::_('BLFA_JSMDNOT4');?>");return;
			}
			if (e_player.value == 0) {
				alert("<?php echo JText::_('BLFA_JSMDNOT5');?>");return;
			}
			
			var tbl_elem = getObj('new_events');
			var row = tbl_elem.insertRow(tbl_elem.rows.length);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			var cell3 = document.createElement("td");
			var cell4 = document.createElement("td");
			var cell5 = document.createElement("td");
			var cell6 = document.createElement("td");
			var cell7 = document.createElement("td");
			var cell8 = document.createElement("td");
			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = "em_id[]";
			input_hidden.value = 0;
			cell1.appendChild(input_hidden);
			cell1.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('BLFA_DELETE');?>"><img src="components/com_joomsport/img/ico/close.png"  border="0" alt="Delete"></a>';
			
			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = "new_eventid[]";
			input_hidden.value = cur_event.value;
			cell2.innerHTML = cur_event.options[cur_event.selectedIndex].text;
			cell2.appendChild(input_hidden);
			
			
			var input_hidden = document.createElement("input");
			input_hidden.type = "text";
			input_hidden.name = "e_minuteval[]";
			input_hidden.value = e_minutes;
			//cell4.innerHTML = e_minutes;
			input_hidden.setAttribute("maxlength",5);
			input_hidden.setAttribute("size",5);
			input_hidden.onblur = function(){extractNumber(this,0,false);}; 
			input_hidden.onkeyup = function(){extractNumber(this,0,false);};
			input_hidden.onkeypress = function(){return blockNonNumbers(this, event, true, false);};
			cell4.appendChild(input_hidden);
			
			var input_player = document.createElement("input");
			input_player.type = "hidden";
			input_player.name = "new_player[]";
			input_player.value = e_player.value;
			if(e_player.value != 0){
				cell5.innerHTML = e_player.options[e_player.selectedIndex].text;
			}	
			cell5.appendChild(input_player);
			var input_hidden = document.createElement("input");
			input_hidden.type = "text";
			input_hidden.name = "e_countval[]";
			input_hidden.value = re_count;
			//cell4.innerHTML = e_minutes;
			input_hidden.setAttribute("maxlength",5);
			input_hidden.setAttribute("size",5);
			input_hidden.onblur = function(){extractNumber(this,0,false);}; 
			input_hidden.onkeyup = function(){extractNumber(this,0,false);};
			input_hidden.onkeypress = function(){return blockNonNumbers(this, event, true, false);};
			cell6.appendChild(input_hidden);
			
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell5);
			row.appendChild(cell4);
			row.appendChild(cell6);
			row.appendChild(cell7);
			row.appendChild(cell8);
			getObj('event_id').value =  0;
			getObj('playerz_id').value =  0;
			getObj('e_minutes').value = '';
			
			ReAnalize_tbl_Rows('new_events');
			
		}
		function bl_add_tevent(){
			var cur_event = getObj('tevent_id');
			
			var e_count = getObj('et_count').value;
			var e_player = getObj('teamz_id');
			
			if (cur_event.value == 0) {
				alert("<?php echo JText::_('BLFA_JSMDNOT4');?>");return;
			}
			if (e_player.value == 0) {
				alert("<?php echo JText::_('BLFA_JSMDNOT6');?>");return;
			}
			
			var exevs = eval( 'document.adminForm["new_teventid\[\]"]');
			var exiev = eval( 'document.adminForm["new_tplayer\[\]"]');
			if(exevs && exiev){
				var ransw2 = exevs.length;
				if(ransw2){
					for (var i=0; i < ransw2; i++) {
						if(exiev[i].value == e_player.value && exevs[i].value == cur_event.value){
							alert("<?php echo JText::_( 'BLFA_JSMDNOT66' ); ?>");return;
						}
					} 
				}else{
					if(exiev.value == e_player.value && exevs.value == cur_event.value){
						alert("<?php echo JText::_( 'BLFA_JSMDNOT66' ); ?>");return;
					}
				}
				
			}
			
			var tbl_elem = getObj('new_tevents');
			var row = tbl_elem.insertRow(tbl_elem.rows.length);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			var cell3 = document.createElement("td");
			var cell4 = document.createElement("td");
			var cell5 = document.createElement("td");
			var cell6 = document.createElement("td");
			var cell7 = document.createElement("td");
			var cell8 = document.createElement("td");
			
			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = "et_id[]";
			input_hidden.value = 0;
			cell1.appendChild(input_hidden);
			cell1.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('BLFA_DELETE');?>"><img src="components/com_joomsport/img/ico/close.png"  border="0" alt="Delete"></a>';
			
			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = "new_teventid[]";
			input_hidden.value = cur_event.value;
			cell2.innerHTML = cur_event.options[cur_event.selectedIndex].text;
			cell2.appendChild(input_hidden);
			
			var input_hidden = document.createElement("input");
			input_hidden.type = "text";
			input_hidden.name = "et_countval[]";
			input_hidden.value = e_count;
			input_hidden.setAttribute("maxlength",5);
			input_hidden.setAttribute("size",5);
			input_hidden.onblur = function(){extractNumber(this,0,false);}; 
			input_hidden.onkeyup = function(){extractNumber(this,0,false);};
			input_hidden.onkeypress = function(){return blockNonNumbers(this, event, true, false);};
			cell3.align = "center";
			//cell3.innerHTML = e_count;
			cell3.appendChild(input_hidden);
			
			
			var input_player = document.createElement("input");
			input_player.type = "hidden";
			input_player.name = "new_tplayer[]";
			input_player.value = e_player.value;
			if(e_player.value != 0){
				cell5.innerHTML = e_player.options[e_player.selectedIndex].text;
			}	
			cell5.appendChild(input_player);
			
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell5);			
			row.appendChild(cell6);
			row.appendChild(cell3);
			row.appendChild(cell7);
			row.appendChild(cell8);
			
		
			getObj('tevent_id').value =  0;
			getObj('teamz_id').value =  0;
			getObj('et_count').value = 1;
			
			//ReAnalize_tbl_Rows('new_tevents');
		}

		function bl_add_squard(tblid,selid,elname){
			var cur_event = getObj(selid);
			

			if (cur_event.value == 0) {
				alert("<?php echo JText::_( 'BLFA_JSMDNOT5' ); ?>");return;
				}
			
			
			var tbl_elem = getObj(tblid);
			var row = tbl_elem.insertRow(tbl_elem.rows.length);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			
			
			
			cell1.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('BLFA_DELETE');?>"><img src="components/com_joomsport/img/ico/close.png"  border="0" alt="Delete"></a>';
			
			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = elname;
			input_hidden.value = cur_event.value;
			cell2.innerHTML = cur_event.options[cur_event.selectedIndex].text;
			cell2.appendChild(input_hidden);
			
			
			
			row.appendChild(cell1);
			row.appendChild(cell2);
			
		
			getObj(selid).value =  0;
			
		}
		function enblnp(){
			if(document.adminForm.new_points1.checked){
				getObj("newp1").removeAttribute('readonly');
				getObj("newp2").removeAttribute('readonly');
			}else{
				getObj("newp1").setAttribute('readonly','readonly');
				getObj("newp2").setAttribute('readonly','readonly');
			}
		}
        function chng_disbl_aet(){

            if(getObj('is_extra1').checked){

                getObj('aet1').disabled = '';
                getObj('aet2').disabled = '';
            }				else{

                getObj('aet1').disabled = 'true';
                getObj('aet2').disabled = 'true';
            }
        }
		function sqchng(nid,nid2){
			if(getObj(nid).checked){
		
				getObj(nid2).checked = false;
			}
		}
		function js_add_subs(tblid,pl1,pl2,minutes){
			var tbl_elem = getObj(tblid);
			if(getObj(pl1).value == getObj(pl2).value || getObj(pl1).value == 0 || getObj(pl2).value == 0){
				return false;
			}
			var row = tbl_elem.insertRow(tbl_elem.rows.length);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			var cell3 = document.createElement("td");
			var cell4 = document.createElement("td");
			
			
			cell1.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('BLFA_DELETE');?>"><img src="components/com_joomsport/img/ico/close.png"  border="0" alt="Delete"></a>';
			
			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = pl1+"_arr[]";
			input_hidden.value = getObj(pl1).value;
			cell2.innerHTML = getObj(pl1).options[getObj(pl1).selectedIndex].text;
			cell2.appendChild(input_hidden);
			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = pl2+"_arr[]";
			input_hidden.value = getObj(pl2).value;
			cell3.innerHTML = getObj(pl2).options[getObj(pl2).selectedIndex].text;
			cell3.appendChild(input_hidden);
			
			var input_hidden = document.createElement("input");
			input_hidden.type = "text";
			input_hidden.name = minutes+"_arr[]";
			input_hidden.value = getObj(minutes).value;
			input_hidden.setAttribute("maxlength",5);
			input_hidden.setAttribute("size",5);
			cell4.appendChild(input_hidden);
			
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
			row.appendChild(cell4);
			
			getObj(minutes).value =  0;
		}
		
		function ReAnalize_tbl_Rows( tbl_id ) {
			start_index = 2;
			var tbl_elem = getObj(tbl_id);
			if (tbl_elem.rows[start_index]) {
				for (var i=start_index; i<tbl_elem.rows.length; i++) {
					
					
					
					if (i > 2) { 
						tbl_elem.rows[i].cells[5].innerHTML = '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_("BLFA_MOVEUP");?>"><img src="administrator/components/com_joomsport/img/up.gif"  border="0" alt="<?php echo JText::_("BLFA_MOVEUP");?>"></a>';
					} else { tbl_elem.rows[i].cells[5].innerHTML = ''; }
					if (i < (tbl_elem.rows.length - 1)) {
						tbl_elem.rows[i].cells[6].innerHTML = '<a href="javascript: void(0);" onClick="javascript:Down_tbl_row(this); return false;" title="<?php echo JText::_("BLFA_MOVEDOWN");?>"><img src="administrator/components/com_joomsport/img/down.gif"  border="0" alt="<?php echo JText::_("BLFA_MOVEDOWN");?>"></a>';
					} else { tbl_elem.rows[i].cells[6].innerHTML = ''; }

				}
			}
		}
		
		

		
		function Up_tbl_row(element) {
			if (element.parentNode.parentNode.sectionRowIndex > 1) {
				var sec_indx = element.parentNode.parentNode.sectionRowIndex;
				var table = element.parentNode.parentNode.parentNode;
				var tbl_id = table.parentNode.id;
				
				var row = table.insertRow(sec_indx - 1);

				row.appendChild(element.parentNode.parentNode.cells[0]);
				row.appendChild(element.parentNode.parentNode.cells[0]);
				row.appendChild(element.parentNode.parentNode.cells[0]);
				row.appendChild(element.parentNode.parentNode.cells[0]);
				row.appendChild(element.parentNode.parentNode.cells[0]);
				//row.appendChild(element.parentNode.parentNode.cells[0]);
				
				var cell5 = document.createElement("td");
				var cell6 = document.createElement("td");
				row.appendChild(cell5);
				row.appendChild(cell6);
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				
				ReAnalize_tbl_Rows(tbl_id);
			}
		}

		function Down_tbl_row(element) {
			if (element.parentNode.parentNode.sectionRowIndex < element.parentNode.parentNode.parentNode.rows.length - 1) {
				var sec_indx = element.parentNode.parentNode.sectionRowIndex;
				var table = element.parentNode.parentNode.parentNode;
				var tbl_id = table.parentNode.id;
				
				var row = table.insertRow(sec_indx + 2);

				row.appendChild(element.parentNode.parentNode.cells[0]);
				row.appendChild(element.parentNode.parentNode.cells[0]);
				row.appendChild(element.parentNode.parentNode.cells[0]);
				row.appendChild(element.parentNode.parentNode.cells[0]);
				row.appendChild(element.parentNode.parentNode.cells[0]);
				//row.appendChild(element.parentNode.parentNode.cells[0]);
				
				var cell5 = document.createElement("td");
				var cell6 = document.createElement("td");
				row.appendChild(cell5);
				row.appendChild(cell6);
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				
				ReAnalize_tbl_Rows(tbl_id);
			}	
		}
		
		//-->
		</script>
		<form action="" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		
		<div id="match_conf_div" class="tabdiv">
		<table class="season-list" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLFA_MATCHDAYNAME' ); ?>
				</td>
				<td>
					<?php echo $lists['mday'];?>
				</td>
			</tr>
			<?php
			if(count($lists['maps'])){
			?>
			
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLFA_MAPS' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_ABOUTMATCH' ); ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td>
					<table border="1" cellpadding="5"><tr>
						<th><?php echo JText::_( 'BLFA_MAPS' ); ?></th>
						<th><?php echo $lists['teams1'];?></th>
						<th><?php echo $lists['teams2'];?></th>
						</tr>
					<?php 
					for($i=0;$i<count($lists['maps']);$i++){
						echo "<tr>";
						echo "<td>".$lists['maps'][$i]->m_name."</td>";
						echo "<td><input type='text' name='t1map[]' size='5' value='".(isset($lists['maps'][$i]->m_score1)?$lists['maps'][$i]->m_score1:"")."' /></td>";
						echo "<td><input type='text' name='t2map[]' size='5' value='".(isset($lists['maps'][$i]->m_score2)?$lists['maps'][$i]->m_score2:"")."' /></td>";
						echo "<input type='hidden' name='mapid[]' value='".$lists['maps'][$i]->id."'/>";
						echo "</tr>";
					}
					?>
					</table>
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLFA_RESULTS' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_RESULTS' ); ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td>
					<?php 
					
					$moder_or_pl = ($this->acl==3)?($row->team1_id == $this->lists["usr"]->id):(in_array($row->team1_id,$this->lists["teams_season"]));
					$moder_or_pl2 = ($this->acl==3)?($row->team2_id == $this->lists["usr"]->id):(in_array($row->team2_id,$this->lists["teams_season"]));
					if($lists['t_type'] == 1 || ($this->acl != 1 && (($this->lists["jsmr_editresult_opposite"] == 0 && !$moder_or_pl) || ($this->lists["jsmr_editresult_yours"] == 0 && $moder_or_pl)))) {
						echo $lists['teams1'].' <input type="hidden" name="score1" value="'.$row->score1.'" size="5" maxlength="5"  />'.$row->score1;
					}else{
						echo $lists['teams1'].' <input type="text" name="score1" value="'.$row->score1.'" size="5" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
					
					}
					if($lists['s_enbl_extra'] && $lists['t_type']){
						echo '&nbsp;'.JText::_("AET").'<input type="text" id="aet1" name="aet1" value="'.$row->aet1.'" size="5" maxlength="5" '.($row->is_extra?"":"disabled").' onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />&nbsp;';
						
					}
					if($lists['t_type']){
						echo JText::_("W").'<input type="checkbox" id="spenwin_1" '.(( $row->p_winner && $row->p_winner == $row->team1_id)?"checked":"").' name="penwin[]" value="'.$row->team1_id.'" onchange="sqchng(\'spenwin_1\',\'spenwin_2\');" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
					}
					if($lists['t_type'] == 1 || ($this->acl != 1 && (($this->lists["jsmr_editresult_opposite"] == 0 && !$moder_or_pl2) || ($this->lists["jsmr_editresult_yours"] == 0 && $moder_or_pl2)))) {	echo '&nbsp;:&nbsp;'.$lists['teams2'].' <input type="hidden" name="score2" value="'.$row->score2.'" size="5" maxlength="5"  />'.$row->score2;
					}else{
						echo '&nbsp;:&nbsp;'.$lists['teams2'].'&nbsp;<input type="text" name="score2" value="'.$row->score2.'" size="5" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
					}
                    //'.($lists['s_enbl_extra']?"":"disabled").'

					if($lists['s_enbl_extra'] && $lists['t_type']){
						echo '&nbsp'.JText::_("AET").'<input type="text" id="aet2" name="aet2" value="'.$row->aet2.'" size="5" maxlength="5" '.($row->is_extra?"":"disabled").' onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />&nbsp;';
						
					}
					if($lists['t_type']){
						echo JText::_("W").'<input type="checkbox" id="spenwin_2" '.(( $row->p_winner && $row->p_winner == $row->team2_id)?"checked":"").' onchange="sqchng(\'spenwin_2\',\'spenwin_1\');" name="penwin[]" value="'.$row->team2_id.'" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
					}
					?>
				</td>
			</tr>
			<?php if(!$lists['t_type']) {?>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BL_TBL_POINTS' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BL_TBL_POINTS' ); ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td>
					<?php echo '<div style="float:left;margin-right:10px;">'.$lists['teams1'].' <input type="text" name="points1" value="'.floatval($row->points1).'" size="5" maxlength="5" id="newp1" '.(!$row->new_points?"readonly='readonly'":"").' onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />&nbsp;:&nbsp;<input type="text" name="points2" value="'.floatval($row->points2).'" size="5" maxlength="5" id="newp2" '.(!$row->new_points?"readonly='readonly'":"").' onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);"/> '.$lists['teams2'].'&nbsp;'.JText::_( 'BLFE_MANUAL_POINT' )." ".$lists['new_points'];?>
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLFA_BONUS' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_BONUS' ); ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td>
					<?php echo $lists['teams1'].' <input type="text" name="bonus1" value="'.floatval($row->bonus1).'" size="5" maxlength="5" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />&nbsp;:&nbsp;<input type="text" name="bonus2" value="'.floatval($row->bonus2).'" size="5" maxlength="5" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" /> '.$lists['teams2'];?>
				</td>
			</tr>
			<?php } ?>
			<?php if($lists['s_enbl_extra'] == 1) {?>
			<tr>
				<td>
					<?php echo JText::_( 'BLFA_ET' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_ET' ); ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<Td>
					<?php echo $lists['extra'];?>
				</Td>
			</tr>
			<?php } ?>
			<?php if($this->acl != 1 && $lists['jsmr_mark_played'] == 1){?>
			<tr>
				<td>
					<?php echo JText::_( 'BLFA_PLAYED' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_PLAYED' ); ?>::<?php echo JText::_( 'BLFA_TT_PLAYED' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>					
				</td>
				<td>
					<?php echo $lists['m_played'];?>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td>
					<?php echo JText::_('BLFA_DATE');?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_DATE' ); ?>::<?php echo JText::_( 'BLFA_TT_DATE' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>					
					
				</td>
				<td>
					<?php
						echo JHTML::_('calendar', $row->m_date ? $row->m_date : date("Y-m-d"), 'm_date', 'm_date', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'20',  'maxlength'=>'10')); 
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('BLFA_TIME');?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_TIME' ); ?>::<?php echo JText::_( 'BLFA_TT_TIME' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
					
				</td>
				<td>
					<input type="text" maxlength="5" class="feed-back inp-small" size="10" name="m_time" value="<?php echo substr($row->m_time,0,5);?>" />
					
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('BLFA_LOCATION');?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_LOCATION' ); ?>::<?php echo JText::_( 'BLFA_TT_LOCATION' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
					
				</td>
				<td>
					<input type="text" class="feed-back inp-big" maxlength="255" size="20" name="m_location" value="<?php echo htmlspecialchars($row->m_location);?>" />
					
				</td>
			</tr>

			<tr>
				<td>
					<?php echo JText::_('BLFA_VENUE');?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_VENUE' ); ?>::<?php echo JText::_( 'BLFA_TT_VENUE' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
					
				</td>
				<td>
					<div class="selectsty"><span class='down'><!-- --></span><?php echo $lists["venue"];?></div>
					
				</td>
			</tr>
			
			<?php
			
			for($p=0;$p<count($lists['ext_fields']);$p++){
			if($lists['ext_fields'][$p]->field_type == '3' && !isset($lists['ext_fields'][$p]->selvals)){
			}else{
			?>
			<tr>
				<td width="100">
					<?php echo $lists['ext_fields'][$p]->name;?>
				</td>
				<td>
					<?php
					
						switch($lists['ext_fields'][$p]->field_type){
								
							case '1':	echo $lists['ext_fields'][$p]->selvals;
										break;
							case '2':	echo $editor->display( 'extraf['.$lists['ext_fields'][$p]->id.']',  htmlspecialchars(isset($lists['ext_fields'][$p]->fvalue_text)?($lists['ext_fields'][$p]->fvalue_text):"", ENT_QUOTES), '550', '300', '60', '20', false ) ;
										break;
							case '3':	echo '<div class="selectsty"><span class="down"><!-- --></span>'.$lists['ext_fields'][$p]->selvals.'</div>';
										break;	
							case '0':					
							default:	echo '<input type="text" maxlength="255" size="60" class="feed-back inp-big" name="extraf['.$lists['ext_fields'][$p]->id.']" value="'.(isset($lists['ext_fields'][$p]->fvalue)?htmlspecialchars($lists['ext_fields'][$p]->fvalue):"").'" />';		
										break;
								
						}
					?>
					<input type="hidden" name="extra_ftype[<?php echo $lists['ext_fields'][$p]->id;?>]" value="<?php echo $lists['ext_fields'][$p]->field_type?>" />
					<input type="hidden" name="extra_id[<?php echo $lists['ext_fields'][$p]->id;?>]" value="<?php echo $lists['ext_fields'][$p]->id?>" />
				</td>
			</tr>
			<?php	
			}
			}
			?>
			
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLFA_ABOUTMATCH' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_ABOUTMATCH' ); ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td>
					<?php echo $editor->display( 'match_descr',  htmlspecialchars($row->match_descr, ENT_QUOTES), '550', '300', '60', '20', false ) ;  ?>
				</td>
			</tr>
			
			
		</table>
		<br />
<table width="100%">
<tr>
	<td>
	<div>		
		<table class="adminlist" id="new_events">
			<tr>
				<th align="center" colspan="7" class="title">
					<?php echo JText::_('BLFA_PLAYEREVENTS');?>
				</th>
			</tr>
			<tr>
				<th class="title" width="2%">
					#
				</th>
				<th class="title" width="170">
					<?php echo JText::_( 'BLFA_PLAYEREVENT' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'BLFA_PLAYERR' ); ?>
				</th>
				
				<th class="title" width="60">
					<?php echo JText::_( 'BLFA_MINUTES' ); ?>
				</th>
				<th width="60">
					<?php echo JText::_( 'BLFA_COUNT' ); ?>
				</th>
				<th width="20"></th>
				<th width="20"></th>
			</tr>
			<?php
			$ps = 0;
			if(count($lists['m_events'])){
				foreach($lists['m_events'] as $m_events){
					echo "<tr>";
					$moder_or_pl = ($this->acl==3)?($m_events->player_id != $this->lists["usr"]->id):(!in_array($m_events->t_id,$this->lists["teams_season"]));
					$moder_or_pl2 = ($this->acl==3)?($m_events->player_id == $this->lists["usr"]->id):(in_array($m_events->t_id,$this->lists["teams_season"]));
					if($this->acl == 1 || (($this->lists["jsmr_edit_playerevent_opposite"] == 1 && $moder_or_pl) || ($this->lists["jsmr_edit_playerevent_yours"] == 1 && $moder_or_pl2))){
						echo '<td><input type="hidden" name="em_id[]" value="'.$m_events->id.'" /><a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="'.JText::_('BLFA_DELETE').'"><img src="components/com_joomsport/img/ico/close.png"  border="0" alt="Delete"></a></td>';

						echo '<td><input type="hidden" name="new_eventid[]" value="'.$m_events->e_id.'" />'.$m_events->e_name.'</td>';

						echo '<td><input type="hidden" name="new_player[]" value="'.$m_events->player_id.'" />'.$m_events->p_name.'</td>';

						echo '<td><input type="text" size="5" maxlenght="5" name="e_minuteval[]" value="'.$m_events->minutes.'" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);"  /></td>';
						echo '<td><input type="text" size="5" maxlength="5" name="e_countval[]" value="'.$m_events->ecount.'" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" /></td>';
						echo '<td>';
						if($ps > 0){
							echo '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="'.JText::_("BLFA_MOVEUP").'"><img src="administrator/components/com_joomsport/img/up.gif"  border="0" alt="'.JText::_("BLFA_MOVEUP").'"></a>';
						}
						echo '</td>';
						echo '<td>';
						if($ps < count($lists['m_events']) - 1){
							echo '<a href="javascript: void(0);" onClick="javascript:Down_tbl_row(this); return false;" title="'.JText::_("BLFA_MOVEDOWN").'"><img src="administrator/components/com_joomsport/img/down.gif"  border="0" alt="'.JText::_("BLFA_MOVEDOWN").'"></a>';
						}
						echo '</td>';
					}else{
						echo '<td>&nbsp;<input type="hidden" name="em_id_n[]" value="'.$m_events->id.'" /></td>';

						echo '<td>'.$m_events->e_name.'</td>';

						echo '<td>'.$m_events->p_name.'</td>';

						echo '<td>'.$m_events->minutes.'</td>';
						echo '<td>'.$m_events->ecount.'</td>';
						echo '<td>';
						if($ps > 0){
							echo '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="administrator/components/com_joomsport/img/up.gif"  border="0" alt="Move Up"></a>';
						}
						echo '</td>';
						echo '<td>';
						if($ps < count($lists['m_events']) - 1){
							echo '<a href="javascript: void(0);" onClick="javascript:Down_tbl_row(this); return false;" title="Move Down"><img src="administrator/components/com_joomsport/img/down.gif"  border="0" alt="Move Down"></a>';
						}
						echo '</td>';
					}
					
					echo "</tr>";
					$ps++;
				}
			}
			?>
		</table>
		<?php 
		//print_r($lists['jsmr_edit_playerevent_yours']);
		//print_r($lists['jsmr_edit_playerevent_opposite']);

		if(!isset($lists['jsmr_edit_playerevent_yours']) && !isset($lists['jsmr_edit_playerevent_opposite'])){ //update
			$lists['jsmr_edit_playerevent_yours'] = '';
			$lists['jsmr_edit_playerevent_opposite'] = '';
		}
		if(!$lists['jsmr_edit_playerevent_yours'] && !$lists['jsmr_edit_playerevent_opposite'] && ($this->acl == 2 || $this->acl == 3)){
		}else{
		?>
		<table class="adminlist">
			<tr>
				<th class="title" colspan="4" align="center">
					<?php echo JText::_('BLFA_ADDPLEVENTS');?>
				</th>
			</tr>
			
			<tr>
				<th class="title" width="260">
					<?php echo $lists['events'];?>
				</th>
				<th>
					<?php echo $lists['players'];?>
				</th>
				
				<th class="title" width="110">
					<input name="e_minutes" id="e_minutes" type="text" maxlength="5" size="5"  onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />
					
				</th>
				<th>
					<input name="re_count" id="re_count" type="text" value="1" maxlength="5" size="5"  onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />
					<input type="button" value="<?php echo JText::_('BLFA_ADD');?>" onClick="bl_add_event();" />
				</th>
				
			</tr>
		</table>
		<?php } ?>
		<br />
	</div>
	<?php if(!$lists['t_single']) {?>
	<div>
		<table class="adminlist" id="new_tevents">
			<tr>
				<th class="title" align="center" colspan="7">
					<?php echo JText::_('BLFA_MATCHSTATS');?>
				</th>
			</tr>
			<tr>
				<th class="title" width="2%">
					#
				</th>
				<th class="title" width="170">
					<?php echo JText::_( 'BLFA_MATCHSTATSEV' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'BLFA_TT_TEAM' ); ?>
				</th>
				
				<th width="20"></th>
				<th width="60">
					<?php echo JText::_( 'BLFA_COUNT' ); ?>
				</th>
				<th width="20"></th>
				<th width="20"></th>
				
			</tr>
			<?php
			$ps1 = 0;
	
			if(count($lists['t_events'])){
				foreach($lists['t_events'] as $m_events){
					echo "<tr>";
					if($this->acl != 1 && (($this->lists["jsmr_edit_matchevent_opposite"] == 0 && !in_array($m_events->pid,$this->lists["teams_season"])) || ($this->lists["jsmr_edit_matchevent_yours"] == 0 && in_array($m_events->pid,$this->lists["teams_season"])))){
						echo '<td>&nbsp;</td>';

						echo '<td>'.$m_events->e_name.'</td>';

						echo '<td>'.$m_events->p_name.'</td>';
						echo '<td></td>';
						echo '<td>'.$m_events->ecount.'</td>';
	
						echo '<td>';
						///
						echo '</td>';
						echo '<td>';
						///
						echo '</td>';
					}else{
						echo '<td><input type="hidden" name="et_id[]" value="'.$m_events->id.'" /><a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="'.JText::_('BLFA_DELETE').'"><img src="components/com_joomsport/img/ico/close.png"  border="0" alt="Delete"></a></td>';

						echo '<td><input type="hidden" name="new_teventid[]" value="'.$m_events->e_id.'" />'.$m_events->e_name.'</td>';

						echo '<td><input type="hidden" name="new_tplayer[]" value="'.$m_events->pid.'" />'.$m_events->p_name.'</td>';
						echo '<td></td>';
						echo '<td><input type="text" size="5" maxlenght="5" name="et_countval[]" value="'.$m_events->ecount.'" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" /></td>';

						echo '<td>';
						///
						echo '</td>';
						echo '<td>';
						///
						echo '</td>';
						
					}
					echo "</tr>";
					$ps1++;
				}
			}
			?>
		</table>
		<?php 
//print_r($lists['jsmr_edit_matchevent_yours']); // 1
//print_r($lists['jsmr_edit_matchevent_opposite']);// 2	
			//$this->acl = 1;
	// if(empty)
		// echo $this->acl."<b>-----------------------</b>";

		if(empty($lists['jsmr_edit_matchevent_yours']) && empty($lists['jsmr_edit_matchevent_opposite']) && $this->acl == 2){
		}else{	
		?>
		<table class="adminlist">
			<tr>
				<th class="title" colspan="3" align="center">
					<?php echo JText::_('BLFA_ADDSTATTOMATCH')?>
				</th>
			</tr>
			<tr>
				<th class="title" width="260">
					<?php echo $lists['team_events'];?>
				</th>
				<th>
					<?php echo $lists['sel_team'];?>
				</th>
				<th width="110">
					<input name="e_count" id="et_count" type="text" maxlength="5" size="5" value="1" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />
					<input type="button" value="<?php echo JText::_( 'BLFA_ADD' ); ?>" onClick="bl_add_tevent();" />
				</th>
			</tr>
		</table>
		<?php } ?>
		<br />
	</div>
	<?php } ?>
</td>
</tr>
</table>
	
	<div style="margin-top:10px;border:1px solid #BBB;">
		<table style="padding:10px;" class="season-list">
			<tr>
				<td>
					<?php echo JText::_('BLFA_UPLPHTOMTCH');?>
				</td>
			</tr>
			<tr>
				<td>&nbsp;
				
				</td>
			</tr>
			<tr>
				<td>
				<input type="file" name="player_photo_1" value="" class="feed-back inp-small" id="player_photo_1"/>
				</td>
			</tr>
			<tr>
				<td>
				<input type="file" name="player_photo_2" value="" class="feed-back inp-small" id="player_photo_2"/>
				</td>
			</tr>
			<tr>
				<td>
					<button class="send-button" id="player_photo"><span><?php echo JText::_( 'BLFA_UPLOAD' ); ?></span></button>
				</td>

			</tr>

			<tr>
				<td>
					<?php echo JText::_('BLFA_ONEPHSEL');?>
				</td>
			</tr>
		</table>
        <script type="text/javascript">
            var photo1 = document.getElementById("player_photo_1");
            var photo2 = document.getElementById("player_photo_2");
            var but_on = document.getElementById("player_photo");
            var serv_sett = <?php echo $lists['post_max_size'];?>;
            but_on.onclick = function() {
                if(photo1.files[0]){
                    var size_img =  photo1.files[0].size;
                }else if(photo2.files[0]){
                    var size_img =  photo2.files[0].size;
                }

                if(size_img > serv_sett){
                    alert("Image too big (change settings post_max_size)"); return false;
                }else{submitbutton('match_apply');}
            };
        </script>
		<?php
		if(count($lists['photos'])){
		?>
		<table class="adminlist">
			<tr>
				<th class="title" width="30"><?php echo JText::_('BLFA_DELETE');?></th>
				
				<th class="title" ><?php echo JText::_('BLFA_TITLE');?></th>
				<th class="title" width="250"><?php echo JText::_('BLFA_IMAGE');?></th>
			</tr>
			<?php
			foreach($lists['photos'] as $photos){
			?>
			<td align="center">
				<a href="javascript:void(0);" title="<?php echo JText::_('BLFA_DELETE');?>" onClick="javascript:Delete_tbl_row(this);"><img src="<?php echo JURI::base();?>components/com_joomsport/img/ico/close.png" title="<?php echo JText::_('BLFA_DELETE');?>" /></a>
				<input type="hidden" name="photos_id[]" value="<?php echo $photos->id;?>"/>
			</td>
			
			<td>
				<input type="text" maxlength="255" size="60" name="ph_names[]" value="<?php echo htmlspecialchars($photos->name)?>" />
			</td>
			<td align="center">
				<?php
				$imgsize = getimagesize('media/bearleague/'.$photos->filename);
				if($imgsize[0] > 200){
					$width = 200;
				}else{
					$width  = $imgsize[0];
				}
				?>
				<a rel="lightbox-imgsport" href="<?php echo getImgPop($photos->filename)?>" title="Image"><img src="<?php echo JURI::base();?>media/bearleague/<?php echo $photos->filename?>" width="<?php echo $width;?>" /></a>
			</td>
			</tr>
			<?php
			}
			?>
		</table>
		<?php
		}
		?>
	</div>	
	</div>
	
	<?php
		
		if(!$lists['t_single']) {
		
		?>	
		<div id="squard_conf_div" class="tabdiv" style="display:none;">
		<?php echo JText::_( 'BLFA_LINEUP' ); ?>
		<div style="width:100%;overflow:hidden;">
		<div style="float:left; width:50%">
			<table class="adminlist" id="new_squard1">
				<tr>
					<th class="title" align="center" colspan="3">
						<?php echo $lists['teams1'];?>
					</th>
				</tr>
				<tr>
					<th>
						<?php echo JText::_( 'BLFA_PLAYER' ); ?>
					</th>
					<th>
						<?php echo JText::_( 'BLFA_LINEUP' ); ?>
					</th>
					<th>
						<?php echo JText::_( 'BLFA_SUBSTITUTES' ); ?>
					</th>
					
					
				</tr>
				<?php //var_dump($lists['squard1']);
				if(count($lists['pl1'])){
					foreach($lists['pl1'] as $m_events){
						echo "<tr>";
						
						echo '<td>'.$m_events->p_name.'</td>';
						$main_chk = false;
						$main_chk_r = false;
						if(count($lists['squard1']) && in_array($m_events->pid,$lists['squard1'])){
							$main_chk = true;
						}
						if(count($lists['squard1_res']) && in_array($m_events->pid,$lists['squard1_res'])){
							$main_chk_r = true;
						}

						if($this->acl != 1 && (($this->lists["jsmr_edit_squad_opposite"] == 0 && !in_array($row->team1_id,$this->lists["teams_season"])) || ($this->lists["jsmr_edit_squad_yours"] == 0 && in_array($row->team1_id,$this->lists["teams_season"])))){
							echo '<td align="center">'.($main_chk?"<img src='components/com_joomsport/img/ico/active.png' />":"&nbsp;").'</td>';
							echo '<td align="center">'.($main_chk_r?"<img src='components/com_joomsport/img/ico/active.png' />":"&nbsp;").'</td>';

						}else{
						
							echo '<td><input type="checkbox" name="t1_squard[]" id="t1sq_'.$m_events->pid.'" value="'.$m_events->pid.'" '.($main_chk?"checked='true'":"").' onclick="sqchng(\'t1sq_'.$m_events->pid.'\',\'t1sqr_'.$m_events->pid.'\');" /></td>';
							echo '<td><input type="checkbox" name="t1_squard_res[]" id="t1sqr_'.$m_events->pid.'" value="'.$m_events->pid.'" '.($main_chk_r?"checked='true'":"").' onclick="sqchng(\'t1sqr_'.$m_events->pid.'\',\'t1sq_'.$m_events->pid.'\');"  /></td>';
						
						}
						echo "</tr>";
					}
				}
				?>
			</table>
			
			<br />
		</div>
		<div style="float:left; width:50%">
			<table class="adminlist" id="new_squard2">
				<tr>
					<th class="title" align="center" colspan="3">
						<?php echo $lists['teams2'];?>
					</th>
				</tr>
				<tr>
					<th>
						<?php echo JText::_( 'BLFA_PLAYER' ); ?>
					</th>
					<th>
						<?php echo JText::_( 'BLFA_LINEUP' ); ?>
					</th>
					<th>
						<?php echo JText::_( 'BLFA_SUBSTITUTES' ); ?>
					</th>
					
				</tr>
				<?php
				if(count($lists['pl2'])){
					foreach($lists['pl2'] as $m_events){
						echo "<tr>";
						
						echo '<td>'.$m_events->p_name.'</td>';
						$main_chk = false;
						$main_chk_r = false;
						if(count($lists['squard2']) && in_array($m_events->pid,$lists['squard2'])){
							$main_chk = true;
						}
						if(count($lists['squard2_res']) && in_array($m_events->pid,$lists['squard2_res'])){
							$main_chk_r = true;
						}
						if($this->acl !=1 && (($this->lists["jsmr_edit_squad_opposite"] == 0 && !in_array($row->team2_id,$this->lists["teams_season"])) || ($this->lists["jsmr_edit_squad_yours"] == 0 && in_array($row->team2_id,$this->lists["teams_season"])))){
							echo '<td align="center">'.($main_chk?"<img src='components/com_joomsport/img/ico/active.png' />":"&nbsp;").'</td>';
							echo '<td align="center">'.($main_chk_r?"<img src='components/com_joomsport/img/ico/active.png' />":"&nbsp;").'</td>';
						
						}else{
							echo '<td><input type="checkbox" name="t2_squard[]" id="t2sq_'.$m_events->pid.'" value="'.$m_events->pid.'" '.($main_chk?"checked='true'":"").' onclick="sqchng(\'t2sq_'.$m_events->pid.'\',\'t2sqr_'.$m_events->pid.'\');" /></td>';
							echo '<td><input type="checkbox" name="t2_squard_res[]" id="t2sqr_'.$m_events->pid.'" value="'.$m_events->pid.'" '.($main_chk_r?"checked='true'":"").' onclick="sqchng(\'t2sqr_'.$m_events->pid.'\',\'t2sq_'.$m_events->pid.'\');"  /></td>';
						}
						echo "</tr>";
					}
				}
				?>
			</table>
			
			<br />
		</div>
		
		<div style="clear:both;">
			<div style="float:left; width:50%">
				<table class="adminlist" id="subsid_1">
					<tr>
						<th width="5%">
						#
						</th>
						<th>
							<?php echo JText::_( 'BLFA_PLAYERIN' ); ?>
						</th>
						<th>
							<?php echo JText::_( 'BLFA_PLAYEROUT' ); ?>
						</th>
						<th>
							<?php echo JText::_( 'BLFA_MINUTES' ); ?>
						</th>
					</tr>
					<?php
					if($this->acl != 1 && (($this->lists["jsmr_edit_squad_opposite"] == 0 && !in_array($row->team1_id,$this->lists["teams_season"])) || ($this->lists["jsmr_edit_squad_yours"] == 0 && in_array($row->team1_id,$this->lists["teams_season"])))){
					}else{
					?>
					<tr>
						<td>
						</td>
						<td colspan="2">
							<?php echo $lists['players_team1']?>
						
							<div style="text-align:right"><?php echo $lists['players_team1_out']?></div>
						</td>
						<td>
							<input type="text" name="minutes1" id="minutes1" value="" maxlength="5" size="5" />
							<input type="button" value="<?php echo JText::_( 'BLFA_ADD' ); ?>" style="cursor:pointer;" onclick="js_add_subs('subsid_1','playersq1_id','playersq1_out_id','minutes1');" />
						</td>
					</tr>
					<?php } ?>
					<?php
					if(count($lists["subsin1"])){
						for($i=0;$i<count($lists["subsin1"]);$i++){
							$subs = $lists["subsin1"][$i];
							echo "<tr>";
								echo "<td>";
									if($this->acl != 1 && (($this->lists["jsmr_edit_squad_opposite"] == 0 && !in_array($row->team1_id,$this->lists["teams_season"])) || ($this->lists["jsmr_edit_squad_yours"] == 0 && in_array($row->team1_id,$this->lists["teams_season"])))){
										echo "&nbsp;";
									}else{
										echo '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="'.JText::_('BLFA_DELETE').'"><img src="components/com_joomsport/img/ico/close.png"  border="0" alt="'.JText::_('BLFA_DELETE').'"></a>';
									}
								echo "</td>";
								echo "<td>";
									echo '<input type="hidden" value="'.$subs->player_in.'" name="playersq1_id_arr[]" />'.$subs->plin;
								echo "</td>";
								echo "<td>";
									echo '<input type="hidden" value="'.$subs->player_out.'" name="playersq1_out_id_arr[]" />'.$subs->plout;
								echo "</td>";
								echo "<td>";
									if($this->acl != 1 && (($this->lists["jsmr_edit_squad_opposite"] == 0 && !in_array($row->team1_id,$this->lists["teams_season"])) || ($this->lists["jsmr_edit_squad_yours"] == 0 && in_array($row->team1_id,$this->lists["teams_season"])))){
										echo $subs->minutes;
									}else{
										echo '<input type="text" value="'.$subs->minutes.'" name="minutes1_arr[]" maxlength="5" size="5" />';
									}
								echo "</td>";
							echo "</tr>";
						}
					}
					?>
				</table>	
				
			</div>	
			<div style="float:left; width:50%">	
				<table class="adminlist" id="subsid_2">
					<tr>
						<th width="5%">
						#
						</th>
						<th>
							<?php echo JText::_( 'BLFA_PLAYERIN' ); ?>
						</th>
						<th>
							<?php echo JText::_( 'BLFA_PLAYEROUT' ); ?>
						</th>
						<th>
							<?php echo JText::_( 'BLFA_MINUTES' ); ?>
						</th>
					</tr>
					<?php
					if($this->acl != 1 && (($this->lists["jsmr_edit_squad_opposite"] == 0 && !in_array($row->team2_id,$this->lists["teams_season"])) || ($this->lists["jsmr_edit_squad_yours"] == 0 && in_array($row->team2_id,$this->lists["teams_season"])))){
					}else{
					?>
					<tr>
						<td>
						</td>
						<td colspan="2">
							<?php echo $lists['players_team2']?>
						
							<div style="text-align:right"><?php echo $lists['players_team2_out']?></div>
						</td>
						<td>
							<input type="text" name="minutes2" id="minutes2"  value="" maxlength="5" size="5" />
							<input type="button" value="<?php echo JText::_( 'BLFA_ADD' ); ?>" style="cursor:pointer;" onclick="js_add_subs('subsid_2','playersq2_id','playersq2_out_id','minutes2');" />
						</td>
					</tr>
					<?php
					}
					if(count($lists["subsin2"])){
						for($i=0;$i<count($lists["subsin2"]);$i++){
							$subs = $lists["subsin2"][$i];
							echo "<tr>";
								echo "<td>";
									if($this->acl !=1 && (($this->lists["jsmr_edit_squad_opposite"] == 0 && !in_array($row->team2_id,$this->lists["teams_season"])) || ($this->lists["jsmr_edit_squad_yours"] == 0 && in_array($row->team2_id,$this->lists["teams_season"])))){
										echo "&nbsp;";
									}else{
										echo '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="'.JText::_('BLFA_DELETE').'"><img src="components/com_joomsport/img/ico/close.png"  border="0" alt="'.JText::_('BLFA_DELETE').'"></a>';
									}
								echo "</td>";
								echo "<td>";
									echo '<input type="hidden" value="'.$subs->player_in.'" name="playersq2_id_arr[]" />'.$subs->plin;
								echo "</td>";
								echo "<td>";
									echo '<input type="hidden" value="'.$subs->player_out.'" name="playersq2_out_id_arr[]" />'.$subs->plout;
								echo "</td>";
								echo "<td>";
									if($this->acl !=1 && (($this->lists["jsmr_edit_squad_opposite"] == 0 && !in_array($row->team2_id,$this->lists["teams_season"])) || ($this->lists["jsmr_edit_squad_yours"] == 0 && in_array($row->team2_id,$this->lists["teams_season"])))){
										echo $subs->minutes;
									}else{
										echo '<input type="text" value="'.$subs->minutes.'" name="minutes2_arr[]" maxlength="5" size="5" />';
									}
								echo "</td>";
							echo "</tr>";
						}
					}
					?>
				</table>	
			</div>	
		</div>
		
		</div>
		
		</div>
		<?php
		
		}
		
		?>	
		<?php if ($this->acl == 2 && $lists["esport_invite_match"]){?>
		<div id="invmatch_conf_div" class="tabdiv" style="display:none;">
		
			<table class="season-list">
				<tr>
					<td>
						<?php echo JText::_( 'BLFA_INVMATCHUSRS' ); ?>
					</td>
					<td>
						<div class="selectsty"><span class='down'><!-- --></span><?php echo $lists['is_minv'];?></div>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_( 'BLFA_INVMAILTITLE' ); ?>
					</td>
					<td>
						<input type="text" class="feed-back inp-big" value="" name="inv_mtitle" size="100" />
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_( 'BLFA_INVMAILTEXT' ); ?>
					</td>
					<td>
						<?php echo $editor->display( 'inv_mtext',  JText::_('BLFA_INVMAILTEXTTEMPL'), '550', '300', '60', '20', false ) ;  ?>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><button class="send-button" onclick="javascript:submitbutton('match_invite');return false;" /><span><b><?php echo JText::_( 'BL_SEND' );?></b></span></td>
				</tr>
			</table>
		</div>
		<?php } ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $row->id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="sid" value="<?php echo $s_id?>" />
		<input type="hidden" name="cid[]" value="<?php echo $row->m_id?>" />
		<input type="hidden" name="isapply" value="0" />
		<input type="hidden" name="jscurtab" id="jscurtab" value="" />
		<?php if($this->acl == 2):?>
		<input type="hidden" name="tid" value="<?php echo $lists["tid"]?>" />
		<?php endif;?>
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
</div>