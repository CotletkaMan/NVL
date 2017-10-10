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
	$s_id = $lists["s_id"];
	$match = $lists["match"];
	global $Itemid;
	$Itemid = JRequest::getInt('Itemid');

?>

<?php
echo $lists["panel"];
?>
<!-- <module middle> -->
	<div class="module-middle solid">

		<!-- <back box> -->
		<!--div class="back dotted"><a href="javascript:void(0);" onclick="history.back(-1);" title="<?php echo JText::_("BL_BACK")?>">&larr; <?php echo JText::_("BL_BACK")?></a></div-->
		<!-- </back box> -->
		<div class="title-box padd-bot">
			<h2>
				<?php echo $row->id?JText::_('BLFA_MDAY_EDIT'):JText::_('BLFA_MDAY_NEW');?>
			</h2>
			<?php if($this->acl == 2):?>
			<div class="select-wr">
				<form action='<?php echo JURI::base();?>index.php?option=com_joomsport&task=edit_matchday&controller=moder&Itemid=<?php echo $Itemid?>' method='post' name='chg_team'>
					<div style="position:relative;text-align: right;"><span class='down'><!-- --></span><?php echo $this->lists['tm_filtr'];?></div>
					<?php if(isset($this->lists['seass_filtr'])):?>
					<div style="position:relative;text-align: right;"><?php echo $this->lists["tourn_name"];?><span class='down'><!-- --></span><?php echo $this->lists['seass_filtr'];?></div>
					<?php endif;?>
				</form>
				<form action="<?php echo JUri::base()."index.php?option=com_joomsport&controller=moder&view=edit_matchday&tid=".$lists["tid"]."&Itemid=".$Itemid;?>" method="post" name="filtrForm">
					<?php if($lists['md_filtr']){?>
						<div style="position:relative;text-align: right;"><span class='down'><!-- --></span><?php echo /*$lists['seas_filtr']."".*/$lists['md_filtr'];?></div>
					<?php } ?>
				</form>
			</div>
			<?php endif;?>
			<?php if($this->acl == 3):?>
			<div class="select-wr">
				<form action="<?php echo "index.php?option=com_joomsport&view=moderedit_umatchday&Itemid=".$Itemid;?>" method="post" name="filtrForm">
					<div style="position:relative;text-align: right;"><span class='down'><!-- --></span><?php echo JText::_('BLFA_FILTERS')?> :<?php echo $lists['seas_filtr'];?></div>
					<?php if($lists['md_filtr']) {?>
					<div style="position:relative;text-align:right;"><span class='down'><!-- --></span><?php echo $lists['md_filtr'];?></div>
					<?php } ?>
				</form>
			</div>
			<?php endif;?>
		</div>
		<?php if($this->acl == 2):?>
		<!-- <tab box> -->
		<ul class="tab-box-main">
			<li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=moder&view=edit_team&tid='.$lists["tid"]."&Itemid=".$Itemid);?>" title=""><span><img src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" class="star" /><?php echo JText::_('BLFA_TEAM')?></span></a></li>
			<li  class="active"><a href="<?php echo JRoute::_( 'index.php?option=com_joomsport&controller=moder&view=edit_matchday&tid='.$lists["tid"].'&Itemid='.$Itemid )?>" title=""><span><img src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" /><?php echo JText::_('BLFA_MATCHDAY')?></span></a></li>
			<?php if($lists['moder_addplayer']){ ?>
			<li><a href="<?php echo JRoute::_( 'index.php?option=com_joomsport&controller=moder&view=admin_player&tid='.$lists["tid"]."&Itemid=".$Itemid)?>" title=""><span><img class="players" src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" /><?php echo JText::_('BLFA_PLAYER')?></span></a></li>
			<?php } ?>

		</ul>
		<!-- </tab box> -->
		<?php endif;?>
		<?php if($this->acl == 3):?>
		<ul class="tab-box">
			<li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&task=regplayer&Itemid='.$Itemid);?>" title=""><span><img src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" class="players" /><?php echo JText::_('BLFA_PLAYER_RE')?></span></a></li>
			<li  class="active"><a href="<?php echo JRoute::_( 'index.php?option=com_joomsport&task=moderedit_umatchday&Itemid='.$Itemid )?>" title=""><span><img src="<?php echo JURI::base();?>components/com_joomsport/img/spacer.gif" width="16" height="16" /><?php echo JText::_('BLFA_MATCHDAY')?></span></a></li>
		</ul>
		<?php endif;?>
	</div>

	<!-- </module middle> -->
	<!-- <control bar> -->
	<div class="control-bar-wr dotted">
		<ul class="control-bar">
			<?php if($this->acl == 1):?>
			<li><a class="apply" href="javascript:void(0);" title="<?php echo JText::_('BLFA_APPLY')?>" onclick="javascript:submitbutton('matchday_apply');return false;"><?php echo JText::_('BLFA_APPLY')?></a></li>
			<li><a class="save" href="javascript:void(0);" title="<?php echo JText::_('BLFA_SAVE')?>" onclick="javascript:submitbutton('matchday_save');return false;"><?php echo JText::_('BLFA_SAVE')?></a></li>
			<li><a class="delete" href="<?php echo JRoute::_("index.php?option=com_joomsport&controller=admin&task=admin_matchday&sid=".$s_id."&Itemid=".$Itemid)?>" title="<?php echo JText::_('BLFA_CLOSE')?>"><?php echo JText::_('BLFA_CLOSE')?></a></li>
			<?php endif;?>
			<?php if(($this->acl == 2 && isset($this->lists['seass_filtr'])) || $this->acl == 3):?>
			<li><a class="save" href="javascript:void(0);" title="<?php echo JText::_('BLFA_SAVE')?>" onclick="javascript:submitbutton('matchday_save');return false;"><?php echo JText::_('BLFA_SAVE')?></a></li>
			<?php endif;?>

		</ul>
	</div>
	<!-- </control bar> -->
<!-- <content module> -->
	<div class="content-module admin-mo-co">
	<?php
		if($this->acl != 1 && $lists["t_type"] == 1){
			echo JText::_("BLFA_WARN_KNOCKMATCH");
		}else if($this->acl != 1 && $lists["t_type"] == 2){
            echo JText::_("BLFA_WARN_DBKNOCKMATCH");
        }
	?>
<?php
		JHTML::_('behavior.tooltip');

		?>
		<script type="text/javascript">
		<!--
		function in_array(what, where) {
			for(var i=0, length_array=where.length; i<length_array; i++)
				if(what == where[i])
					return true;
			return false;
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'matchday_save' || pressbutton == 'matchday_apply') {
				if('<?php echo $this->acl?>' == 1){
					if(form.m_name.value == "" || form.sid.value == 0){
						alert("<?php echo JText::_("BL_ENTRNTS");?>");
						return false;
					}

					if('<?php echo $lists['t_type']?>' == 1 || '<?php echo $lists['t_type']?>' == 2){
							var arrpl = new Array();
							var partip = eval("document.adminForm['teams_kn[]']");
							var partip_aw = eval("document.adminForm['teams_kn_aw[]']");
							if(partip){
								if(partip.options){
									if(!in_array(partip.value,arrpl) ){
												if(partip.value != '0' && partip.value != '-1'){
													arrpl.push(partip.value);
												}

											}else{
												alert(partip.options[partip.selectedIndex].text+' <?php echo JText::_("BLFA_KN_DUBL");?>');return false;
											}
								}else{
									for(i=0;i<partip.length;i++){



											if(!in_array(partip[i].value,arrpl) ){
												if(partip[i].value != '0' && partip[i].value != '-1'){
													arrpl.push(partip[i].value);
												}

											}else{
												alert(partip[i].options[partip[i].selectedIndex].text+' <?php echo JText::_("BLFA_KN_DUBL");?>');return false;
											}

									}

								}
							}
							if(partip_aw){
								if(partip_aw.options){

									if(!in_array(partip_aw.value,arrpl) ){
												if(partip_aw.value != '0' && partip_aw.value != '-1'){
													arrpl.push(partip_aw.value);
												}

											}else{
												alert(partip_aw.options[partip_aw.selectedIndex].text+' <?php echo JText::_("BLFA_KN_DUBL");?>');return false;
											}

								}else{
									for(i=0;i<partip_aw.length;i++){



											if(!in_array(partip_aw[i].value,arrpl) ){
												if(partip_aw[i].value != '0' && partip_aw[i].value != '-1'){
													arrpl.push(partip_aw[i].value);
												}

											}else{
												alert(partip_aw[i].options[partip_aw[i].selectedIndex].text+' <?php echo JText::_("BLFA_KN_DUBL");?>');return false;
											}

									}
								}
							}
						}
				}
					var extras = eval("document.adminForm['extra_time[]']");
					if(extras){
						if(extras.length){
							for(i=0;i<extras.length;i++){
								if(extras[i].checked){
									extras[i].value = 1;
								}else{
									extras[i].value = 0;
								}
								extras[i].checked = true;
							}
						}else{
							if(extras.checked){
								extras.value = 1;
							}else{
								extras.value = 0;
							}
							extras.checked = true;
						}
					}
					///if('<?php echo $this->acl?>' == 1){
						var played = eval("document.adminForm['match_played[]']");
						if(played){
							if(played.length){
								for(i=0;i<played.length;i++){
									if(played[i].checked){
										played[i].value = 1;
									}else{
										played[i].value = 0;
									}
									played[i].checked = true;
								}
							}else{
								if(played.checked){
									played.value = 1;
								}else{
									played.value = 0;
								}
								played.checked = true;
							}
						}
					///}
					if(pressbutton == 'matchday_apply'){
						form.isapply.value='1';
						pressbutton = 'matchday_save';
					}

					var errortime = '';
					var mt_time = eval("document.adminForm['match_time[]']");
					if(mt_time){
						if(mt_time.length){
							for(i=0;i<mt_time.length;i++){
								var regE = /[0-2][0-9]:[0-5][0-9]/;
								if(!mt_time[i].value.test(regE) && mt_time[i].value != ''){
									errortime = '1';
									mt_time[i].style.border = "1px solid red";
								}else{
									mt_time[i].style.border = "1px solid #C0C0C0";
								}
							}
						}else{
							var regE = /[0-2][0-9]:[0-5][0-9]/;
								if(!mt_time.value.test(regE) && mt_time.value != ''){
									errortime = '1';
									mt_time.style.border = "1px solid red";
								}else{
									mt_time.style.border = "1px solid #C0C0C0";
								}
						}
					}

					if(errortime){
						alert("<?php echo JText::_( 'BLBE_JSMDNOT7' ); ?>");return;
					}else{
						submitform( pressbutton );
						return;
					}

			}else{
				submitform( pressbutton );
					return;
			}
		}

		function bl_add_match(){
			var team1 = getObj('teams1');
			var team2 = getObj('teams2');
			var score1 = getObj('add_score1').value;
			var score2 = getObj('add_score2').value;
            var venue_id = getObj('venue_id_new');
			if('<?php echo $lists["jsmr_mark_played"]?>' == 1){
				var tm_played = getObj('tm_played').checked;
			}

			if (team1.value == 0 || team2.value == 0) {
				alert("<?php echo JText::_('BLFA_JSMDNOT1')?>");return false;
			}
			if('<?php echo $lists["jsmr_mark_played"]?>' == 1){
				if (((score1) == '' || (score2) == '') && tm_played){
					alert("<?php echo JText::_('BLFA_JSMDNOT1')?>");return;
				}
			}
			if('<?php echo $this->acl?>' == 1){
				if ( team1.value == team2.value ){
					alert("<?php echo ($lists['t_single']==1)?JText::_('BLFA_JSMDNOTPL2'):JText::_('BLFA_JSMDNOT2')?>");return false;
				}
			}else{
				if ( team1.value == team2.value || (team1.value!='<?php echo isset($lists["tid"])?$lists["tid"]:0?>' && team2.value!='<?php echo isset($lists["tid"])?$lists["tid"]:0?>')){

					alert("<?php echo ($this->acl==3)?JText::_('BLFA_JSMDNOTPART'):JText::_('BLFA_JSMDNOTPART')?>");return false;

				}
			}

			var regE = /[0-2][0-9]:[0-5][0-9]/;
			if(!getObj('match_time_new').value.test(regE) && getObj('match_time_new').value != ""){
				alert("<?php echo JText::_('BLFA_JSMDNOT7')?>");return false;
			}
			var tbl_elem = getObj('new_matches');
			var row = tbl_elem.insertRow(tbl_elem.rows.length);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			var cell3 = document.createElement("td");
			var cell4 = document.createElement("td");
			var cell5 = document.createElement("td");
			var cell6 = document.createElement("td");

			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = "match_id[]";
			input_hidden.value = 0;
			cell1.appendChild(input_hidden);
			cell1.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row_md(this); return false;" title="<?php echo JText::_('BLFA_DELETE');?>"><img src="components/com_joomsport/img/ico/close.png"  style="max-width:none;" border="0" alt="Delete"></a>';
			cell1.setAttribute("rowspan",2);
			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = "home_team[]";
			input_hidden.value = team1.value;
			cell2.innerHTML = team1.options[team1.selectedIndex].text;
			cell2.appendChild(input_hidden);

			var input_hidden = document.createElement("input");
			input_hidden.type = "text";
			input_hidden.name = "home_score[]";
			input_hidden.value = score1;
			input_hidden.size = 3;
			input_hidden.setAttribute("maxlength",5);
			cell3.align = "center";
			input_hidden.onblur = function(){extractNumber(this,0,false);};
			input_hidden.onkeyup = function(){extractNumber(this,0,false);};
			input_hidden.onkeypress = function(){return blockNonNumbers(this, event, true, false);};
			cell3.appendChild(input_hidden);
			var txtnode = document.createTextNode(" : ");
			cell3.appendChild(txtnode);
			var input_hidden = document.createElement("input");
			input_hidden.type = "text";
			input_hidden.name = "away_score[]";
			input_hidden.value = score2;
			input_hidden.size = 3;
			input_hidden.setAttribute("maxlength",5);
			input_hidden.onblur = function(){extractNumber(this,0,false);};
			input_hidden.onkeyup = function(){extractNumber(this,0,false);};
			input_hidden.onkeypress = function(){return blockNonNumbers(this, event, true, false);};
			cell3.appendChild(input_hidden);
			if('<?php echo $lists['s_enbl_extra']?>' == '1'){
				var input_hidden = document.createElement("input");
				input_hidden.type = "checkbox";
				input_hidden.name = "extra_time[]";

				if(getObj('extra_timez').checked){
					input_hidden.checked = true;
					input_hidden.value = '1';
				}else{
					input_hidden.value = '0';
				}
				cell3.appendChild(input_hidden);
				var iiinn = document.createElement("span");
				iiinn.className = "editlinktip hasTip";
				iiinn.setAttribute('title', "<?php echo JText::_( 'BLFA_ET' )?>");
				var innn = document.createElement("img");
				innn.src = "/components/com_joomsport/img/quest.jpg";
				iiinn.appendChild(innn);
				cell3.appendChild(iiinn);
			}
			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = "away_team[]";
			input_hidden.value = team2.value;
			cell4.innerHTML = team2.options[team2.selectedIndex].text;
			cell4.appendChild(input_hidden);
			cell5.innerHTML = '';
			cell5.setAttribute("rowspan",2);

			////-------------new---------------////

			var cell7 = document.createElement("td");
			var cell8 = document.createElement("td");
            var cell9 = document.createElement("td");

			var input_hidden = document.createElement("input");
			input_hidden.type = "text";
			input_hidden.name = "match_data[]";
			input_hidden.value = getObj('tm_date').value;
			input_hidden.size = 10;
			input_hidden.setAttribute("maxlength",10);

			cell6.appendChild(input_hidden);
			cell6.align = "left";


			var input_hidden = document.createElement("input");
			input_hidden.type = "text";
			input_hidden.name = "match_time[]";
			input_hidden.value = getObj('match_time_new').value;
			input_hidden.size = 5;
			input_hidden.setAttribute("maxlength",5);

			cell7.appendChild(input_hidden);


			cell7.align = "left";

            var input_hidden = document.createElement("input");
            input_hidden.type = "hidden";
            input_hidden.name = "venue_id[]";
            input_hidden.value = venue_id.value;
            cell9.innerHTML = venue_id.selectedIndex!=0?venue_id.options[venue_id.selectedIndex].text:'';
            cell9.appendChild(input_hidden);

			if('<?php echo $lists["jsmr_mark_played"]?>' == 1){
				var input_hidden = document.createElement("input");
				input_hidden.type = "checkbox";
				input_hidden.name = "match_played[]";
				if(getObj('tm_played').checked){
					input_hidden.checked = true;
					input_hidden.value = '1';
				}else{
					input_hidden.value = '0';
				}
				cell8.innerHTML = '<?php echo JText::_( 'BLFA_ISPLAYED' );?>';
				cell8.appendChild(input_hidden);
			}
			////------------/new---------------////

			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);

			row.appendChild(cell4);
			row.appendChild(cell5);
			var row2 = tbl_elem.insertRow(tbl_elem.rows.length);
			row2.appendChild(cell6);

			row2.appendChild(cell8);
			row2.appendChild(cell7);

			var row3 = tbl_elem.insertRow(tbl_elem.rows.length);
			var cell_f = document.createElement("td");
			cell_f.setAttribute("colspan",3);
			row3.appendChild(cell_f);
            row3.appendChild(cell9);
			getObj('teams1').value =  0;
			getObj('teams2').value = 0;
			getObj('add_score1').value = '';
			getObj('add_score2').value = '';
            getObj('venue_id_new').value = 0;
			if('<?php echo $lists['s_enbl_extra']?>' == '1'){
				getObj('extra_timez').checked = false;
			}
			return false;
		}
		//-->
		</script>
		<?php
		if($this->acl == 2 && !$lists['md_filtr']){
		}else{
		?>
		<form action="" method="post" name="adminForm" id="adminForm">
		<?php
		if($this->acl == 1){
		?>
		<table class="season-list" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="md-name">
					<?php echo JText::_( 'BLFA_MATCHDAYNAME' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_MATCHDAYNAME' ); ?>::<?php echo JText::_( ' ' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td class="dotted">
					<input type="text" maxlength="255" size="60" class="feed-back inp-big" name="m_name" value="<?php echo htmlspecialchars($row->m_name)?>" />
				</td>
			</tr>
			<?php if(!$lists['t_type']){ ?>
			<tr>
				<td width="100" class="dotted">
					<?php echo JText::_( 'BLFA_ISPLAYOFF' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_ISPLAYOFF' ); ?>::<?php echo JText::_( ' ' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</td>
				<td class="dotted">
					<?php echo $lists['is_playoff'];?>
				</td>
			</tr>
			<?php } ?>
		</table>
		<?php
		}
		if($lists['t_type'] && $this->acl == 1){
            if($lists['t_type'] == 1){
                $javascript = 'javascript: jQuery.post( \''.JUri::Base().'index.php?tmpl=component&option=com_joomsport&task=get_format&no_html=1&sid='.$s_id.'&t_single='.$lists['t_single'].'&fr_id=\'+jQuery(\'#format_post\').val(), function( data ) {jQuery(\'#mapformat\').html( data );});';
                
            }else if($lists['t_type'] == 2){
                $javascript = 'javascript: jQuery.post( \''.JUri::Base().'index.php?tmpl=component&option=com_joomsport&task=get_formatkn&no_html=1&sid='.$s_id.'&t_single='.$lists['t_single'].'&fr_id=\'+jQuery(\'#format_post\').val(), function( data ) {jQuery(\'#mapformat\').html( data );});';                
            }


		?>
            <div class="md-format-cont">
                <?php echo ($lists['t_type'] == 1?JText::_('BLFA_KNOCK'):JText::_('BLFA_DOUBLE'))." ".$lists['format']?>
                <span onclick="<?php echo $javascript;?>" class="jsformbutt"><b><?php echo JText::_( 'BLFA_GENERATE' ); ?></b></span>
            </div>
            <div>
			<div style="width:100%;position:relative;" id="mapformat">
				<?php
				if(count($lists["match"])){
					echo $lists['knock_layout'];
				}
				?>
			</div>
			</div>

		<?php
		}else{

		?>
		<br />
		<table class="season-list" cellpadding="0" cellspacing="0" border="0" id="new_matches">
			<tr>
				<th class="title" style="padding-left:250px;" colspan="8">
					<?php echo JText::_('BLFA_MATCHRESULTS');?>
				</th>
			</tr>
			<tr>
				<th class="title" width="20">
					#
				</th>
				<th class="title" width="160">
					<?php
						echo $lists['t_single']?JText::_( 'BLFA_HOMEPLAYER' ):JText::_( 'BLFA_HOMETEAM' );
					?>
				</th>
				<th width="160">
					<?php echo JText::_( 'BLFA_SCORE' ); ?>
				</th>

				<th class="title" width="170">
					<?php
						echo $lists['t_single']?JText::_( 'BLFA_AWAYPLAYER' ):JText::_( 'BLFA_AWAYTEAM' );
					?>
				</th>

				<th class="title">
					<?php echo JText::_( 'BLFA_MATCHDETAILS' ); ?>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_MATCHDETAILS' ); ?>::<?php echo JText::_( 'BLFA_TT_MATCHDETAILS' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</th>
			</tr>
			<?php
			$cx=0;
			if(count($match)){
				foreach($match as $curmatch){
					$moder_or_pl = ($this->acl==3)?($curmatch->team1_id == $this->lists["usr"]->id):(in_array($curmatch->team1_id,$this->lists["teams_season"]));
					$moder_or_pl2 = ($this->acl==3)?($curmatch->team2_id == $this->lists["usr"]->id):(in_array($curmatch->team2_id,$this->lists["teams_season"]));


					switch($this->acl){
						case '2':
								$match_link = 'index.php?option=com_joomsport&amp;view=edit_match&amp;cid[]='.$curmatch->id.'&amp;controller=moder&amp;sid='.$s_id.'&amp;tid='.$lists["tid"]."&Itemid=".$Itemid;
								break;
						case '3':
								$match_link = 'index.php?option=com_joomsport&amp;task=moderedit_umatch&amp;cid[]='.$curmatch->id."&Itemid=".$Itemid;
								break;
						default:
								$match_link = 'index.php?option=com_joomsport&amp;view=edit_match&amp;cid[]='.$curmatch->id.'&amp;controller=admin&amp;sid='.$s_id.'&amp;Itemid='.$Itemid;
					}
					echo "<tr class='".($cx%2?'gray':'')."'>";
					if($this->acl != 1 && ($lists["t_type"] == 1 || ($lists["jsmr_mark_played"] == 0 && $curmatch->m_played == 1) || $lists['t_type'] == 2)){
						echo '<td rowspan="2"><input type="hidden" name="match_id[]" value="'.$curmatch->id.'" /></td>';
					}else{

						echo '<td rowspan="2"><input type="hidden" name="match_id[]" value="'.$curmatch->id.'" /><a href="javascript: void(0);" onClick="javascript:Delete_tbl_row_md(this); return false;" title="'.JText::_('BLFA_DELETE').'"><img src="components/com_joomsport/img/ico/close.png" style="max-width:none;"  border="0" alt="Delete"></a></td>';

					}
					echo '<td><input type="hidden" name="home_team[]" value="'.$curmatch->team1_id.'" />'.$curmatch->home_team.'</td>';

					if($this->acl == 1){
						echo '<td align="center"><input type="text" name="home_score[]" value="'.$curmatch->score1.'" size="3" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" /> : <input type="text" name="away_score[]" value="'.$curmatch->score2.'" size="3" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
						if($lists['s_enbl_extra']){
							echo '<input type="checkbox" name="extra_time[]" value="'.(($curmatch->is_extra)?1:0).'" '.(($curmatch->is_extra)?"checked":"").' /><span class="editlinktip hasTip" title="'.JText::_( 'BLFA_ET' ).'"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>';
						}
						echo '</td>';
					}else{
						if($lists["t_type"] == 1 || $lists['t_type'] == 2){
							echo '<td align="center"><input disabled="true" type="text" readonly="true" name="home_score[]" value="'.$curmatch->score1.'" size="3" maxlength="5" /> : <input disabled="true" type="text" readonly="true" name="away_score[]" value="'.$curmatch->score2.'" size="3" maxlength="5" />';
							if($lists['s_enbl_extra']){
								echo '<input type="checkbox" name="extra_time[]" value="'.(($curmatch->is_extra)?1:0).'" '.(($curmatch->is_extra)?"checked":"").' /><span class="editlinktip hasTip" title="'.JText::_( 'BLFA_ET' ).'"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>';
							}
							echo '</td>';
						}else{
							echo '<td align="center">';
							$hidden1=false;
							$hidden2=false;
							if(($lists["jsmr_mark_played"] == 0 && $curmatch->m_played) == 1 || (($this->lists["jsmr_editresult_opposite"] == 0 && !$moder_or_pl) || ($this->lists["jsmr_editresult_yours"] == 0 && $moder_or_pl))){
								$hidden1=true;
							}
							if(($lists["jsmr_mark_played"] == 0 && $curmatch->m_played) == 1 || (($this->lists["jsmr_editresult_opposite"] == 0 && !$moder_or_pl2) || ($this->lists["jsmr_editresult_yours"] == 0 && $moder_or_pl2))){
								$hidden2=true;
							}
							echo '<input type="text" name="home_score[]" value="'.$curmatch->score1.'" size="3" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" '.($hidden1?'disabled="true"':'').' />';
							echo ' : <input type="text" name="away_score[]" value="'.$curmatch->score2.'" size="3" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" '.($hidden2?'disabled="true"':'').' />';

							if($lists['s_enbl_extra']){
								echo '<input type="checkbox" name="extra_time[]" value="'.(($curmatch->is_extra)?1:0).'" '.(($curmatch->is_extra)?"checked":"").' /><span class="editlinktip hasTip" title="'.JText::_( 'BLFA_ET' ).'::'.JText::_( '' ).'"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>';
							}
							echo '</td>';
						}
					}


					echo '<td><input type="hidden" name="away_team[]" value="'.$curmatch->team2_id.'" />'.$curmatch->away_team.'</td>';
					echo '<td rowspan=2>';
						if($lists["jsmr_mark_played"] == 0 && $curmatch->m_played == 1 && $this->acl != 1){
							echo "&nbsp;";
						}else{
							echo '<a href="'.$match_link.'">'.JText::_( 'BLFA_MATCHDETAILS' ).'</a>';
						}
					echo '</td>';

					echo '</tr>';
					echo "<tr class='".($cx%2?'gray':'')."'>";

					echo '<td class="datepicker">'.JText::_( 'BLFA_DATEE' ).'';
						echo JHTML::_('calendar', $curmatch->m_date, 'match_data[]', 'match_data_'.$curmatch->id, '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'12',  'maxlength'=>'10'));

					echo '</td>';
					if($lists["jsmr_mark_played"] == 1){
						echo '<td>';
						echo JText::_( 'BLFA_ISPLAYED' ).'<input type="checkbox" name="match_played[]" value="'.($curmatch->m_played?1:0).'" '.($curmatch->m_played?"checked":"").' />';
						echo '</td>';
					}else{
						echo "<td>&nbsp;</td>";
					}
					echo '<td>'.JText::_( 'BLFA_TIMEE' ).'<input type="text" name="match_time[]" maxlength="5" size="12" value="'.substr($curmatch->m_time,0,5).'" />';
                    echo '<div style="margin-top:10px;">'.$curmatch->venue_name.'</div></td>';
                    echo "</tr>";
					echo '<tr><td colspan="5"></td></tr>';
					$cx++;
				}
			}
			?>
		</table>
		<?php 	if(($lists["t_type"] == 1 || $lists['t_type'] == 2) && $this->acl != 1){
		}else{ ?>
		<table class="adminlist">
			<tr >
				<th  class="title" colspan="3" style=" padding-left:200px;">
					<?php echo JText::_('BLFA_ADDMATCHRESULTS');?>
				</th>
			</tr>

			<tr>
				<th style="width:195px !important;" >
					<?php echo $lists['teams1']?>
					<span class="editlinktip hasTip" title="<?php echo $lists['t_single']?JText::_( 'BLFA_HOMEPLAYER' ):JText::_( 'BLFA_HOMETEAM' ); ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</th>
				<th style="width:165px !important;" >
					<input name="add_score1" id="add_score1" style="width:47px;" type="text" maxlength="5" size="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />
					<?php echo ":"; ?>
					<input name="add_score2" id="add_score2" style="width:47px;" type="text" maxlength="5" size="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />
					<?php if($lists['s_enbl_extra']){?>
						<input type="checkbox" name="extra_timez" id="extra_timez" />&nbsp;ET
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_ET' ); ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
					<?php } ?>
				</th>
				<th>
					 <?php echo $lists['teams2']?>
					 <span class="editlinktip hasTip" title="<?php echo $lists['t_single']?JText::_( 'BLFA_AWAYPLAYER' ):JText::_( 'BLFA_AWAYTEAM' );  ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
				</th>
			</tr>
			<tr>

				<th class="datepicker">
					<?php
						echo JText::_( 'BLFA_DATEE' );
						echo JHTML::_('calendar', date("Y-m-d"), 'tm_date', 'tm_date', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'12',  'maxlength'=>'10'));
					?>
				</th>
				<th>
					<?php
					if($lists["jsmr_mark_played"] == 1){
					?>
						<input type="checkbox" name="tm_played" id="tm_played"  />
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_PLAYED' ); ?>::<?php echo JText::_( 'BLFA_TT_PLAYED' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>
					<?php } ?>
				</th>
				<th>
					<?php echo JText::_( 'BLFA_TIMEE' );?>
					<input type="text" name="match_time_new" id="match_time_new" maxlength="5" size="12" value="00:00" />

                    <?php echo $lists['venue_name'];?>
				</th>


			</tr>
		</table>
		<?php } ?>
		<?php } ?>
		<input type="hidden" name="return_sh" value="0" />
		<input type="hidden" name="task" value="admin_matchday" />
		<input type="hidden" name="id" value="<?php echo $row->id?>" />
        <input type="hidden" name="format_post" value="<?php echo $row->k_format?>" />

		<input type="hidden" name="isapply" value="0" />
		<?php if($this->acl == 1){?>
			<input type="hidden" name="sid" value="<?php echo $s_id;?>" />
			<input type="hidden" name="t_single" value="<?php echo $lists['t_single']?>" />
			<input type="hidden" name="t_knock" value="<?php echo $lists['t_type']?>" />
			<!--<input type="hidden" name="t_type" value="<?php //echo $lists['t_type']?>" />-->
		<?php }else{ ?>
			<input type="hidden" name="tid" value="<?php echo $lists["tid"]?>" />
			<input type="hidden" name="sid" value="<?php echo $s_id?>" />
			<input type="hidden" name="mid" value="<?php echo $lists["mid"]?>" />
            <input type="hidden" name="t_knock" value="<?php echo $lists['t_type']?>" />
            <input type="hidden" name="t_type" value="<?php echo $lists['t_type']?>" />
		<?php } ?>
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php if(!$lists['t_type']){?>
		<div style="float:right;"><button class="send-button" onClick="bl_add_match();return false;" /><span><b><?php echo JText::_( 'BLFA_ADD' ); ?></b></span></button></div>

		<?php } ?>

	<?php }?>
</div>