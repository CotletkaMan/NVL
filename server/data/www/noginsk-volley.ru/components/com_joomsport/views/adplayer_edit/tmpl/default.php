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
JHTML::_('behavior.formvalidation');
if(isset($this->message)){

	$this->display('message');

}

$row = $this->row;

$lists = $this->lists;

$Itemid = JRequest::getInt('Itemid');

for($i=0;$i<count($this->lists["player_reg"]);$i++){
	foreach($this->lists["player_reg"][$i] as $dta){
		$tmp[$i][]='\''.addslashes($dta).'\'';
	}
}	
for($j=0;$j<count($tmp);$j++){
	$arr1[] = $tmp[$j][0];
	$arr2[] = $tmp[$j][1];
}

$fname = implode(',',$arr1);
$lname = implode(',',$arr2);

if(count($lists['ext_fields'])){
	foreach($lists['ext_fields'] as $ext){
		if($ext->reg_require == 1){
			$arr_extra[] = $ext->id;
		}
	}
	$extra_id = isset($arr_extra)?"'" . implode("','",$arr_extra) . "'":'';
}

if($this->lists['reg_lastname'] == 1){
?>
<script type="text/javascript">
	
	function confirmDelete(pressbutton){
		var fName = document.getElementsByName('first_name')[0].value; 
		var lName = document.getElementsByName('last_name')[0].value; 
		var msg = '';

		var arrFName = new Array(<?php echo $fname;?>);
		var arrLName = new Array(<?php echo $lname;?>);
				
			for(var i=0;i<arrFName.length;i++){
				if(arrFName[i] == fName && arrLName[i] == lName){
					 msg = 1;
				}
				
			}
			
			if(msg){
				if (confirm("Player with such First Name and Last Name already exist. Do you wants to continue?")) {
					submitform( pressbutton );
						return;
				} else {
					return false;
				}
			}else{
			submitform( pressbutton );
					return;
		}
	}

</script>
<?php
}
echo $lists["panel"];
?>
<!-- <module middle> -->
<div class="module-middle solid">
	
	<!-- <back box> -->
	<!-- </back box> -->
	<div class="title-box padd-bot">
			<h2>
				<?php echo $row->id?JText::_('BLFA_PLAYER_EDIT'):JText::_('BLFA_PLAYER_NEW');?>
			</h2>
			<?php if($this->acl == 2):?>
			<div class="select-wr">
				<form action='<?php echo JURI::base();?>index.php?option=com_joomsport&task=adplayer_edit&controller=moder&tid=<?php echo $lists["tid"];?>&cid[]=<?php echo $row->id?>&Itemid=<?php echo $Itemid?>' method='post' name='chg_team'>
					<?php if(isset($this->lists['seass_filtr'])){ ?>
					<div style="position:relative;text-align: right;"><?php echo $this->lists["tourn_name"];?><span class='down'><!-- --></span><?php echo $this->lists['seass_filtr'];?></div>
					<?php }?>
				</form>
			</div>
			<?php endif;?>
	</div>
	<!-- <tab box> -->
	
	
</div>
<!-- </module middle> -->
<?php 
 if($this->acl == 2 && !$lists["canmore"]){
	echo "<div>".JText::_('BLFA_PLAYERLIMITIS')."</div>"; 
 }else{
 ?>	
<!-- <control bar> -->
<div class="control-bar-wr dotted">
	<ul class="control-bar">
		<?php if($this->acl == 1){?>
			<?php if($lists["jssa_editplayer"] || !$row->id){?>
			<li><a class="save" href="javascript:return false;" title="<?php echo JText::_('BLFA_SAVE')?>" onclick="javascript:submitbutton('adplayer_save');return false;"><?php echo JText::_('BLFA_SAVE')?></a></li>
			<?php } ?>
			<li><a class="delete" href="<?php echo JRoute::_("index.php?option=com_joomsport&controller=admin&view=admin_player&sid=".$lists["s_id"]."&Itemid=".$Itemid);?>" title="<?php echo JText::_('BLFA_CLOSE')?>"><?php echo JText::_('BLFA_CLOSE')?></a></li>
		<?php }else{?>
			<li><a class="save" href="#" title="<?php echo JText::_('BLFA_SAVE')?>" onclick="javascript:submitbutton('adplayer_save');return false;"><?php echo JText::_('BLFA_SAVE')?></a></li>
			<li><a class="delete" href="#" onclick="javascript:submitbutton('admin_player');return false;" title="<?php echo JText::_('BLFA_CLOSE')?>"><?php echo JText::_('BLFA_CLOSE')?></a></li>
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
			var arrId = new Array(<?php echo $extra_id;?>)
			var form = document.adminForm;

			 if(pressbutton == 'adplayer_apply' || pressbutton == 'adplayer_save'){
			 
			 
				var del = document.formvalidator.isValid(form);
				
				if(del == false){

			 		alert('<?php echo JText::_( 'BLFA_JSMDNOT1' ); ?>');
//alert(del);
			 	
			 	}else{
					if('<?php echo isset($lists['teams_seas'])?>'=='1' && form.teams_seas.value == 0){
						alert('<?php echo JText::_('BLFA_JSMDNOT6');?>');
					}else{
					
						var player_f = "<?php echo $row->first_name;?>";
						
						if(!player_f && '<?php echo $this->lists['reg_lastname']?>' == '1'){
							confirmDelete(pressbutton);
							
						}else{
								
							submitform( pressbutton );
							return;
						}
					}

			 	}

			 }else{
				
				submitform( pressbutton );

					return;

			 }		

		}	


		//-->

		</script>

		<?php

		if(!count($row)){

			echo "<div id='system-message'>".JText::_('BLFA_NOITEMS')."</div>";

		}
		$formlink = JURI::base()."index.php?option=com_joomsport&controller=admin&sid=".$lists["s_id"]."&Itemid=".$Itemid;
		if($this->acl == 2){
			$formlink = JURI::base()."index.php?option=com_joomsport&controller=moder&tid=".$lists["tid"]."&Itemid=".$Itemid;
		}
		?>

		<form action="<?php echo $formlink;?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-validate">

		

		<table class="season-list" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="100">
					<?php echo JText::_( 'User' ); ?>
				</td>
				<td>
					<div class="selectsty"><span class='down'><!-- --></span><?php echo $lists['usrid'];?></div>
				</td>
			</tr>
			<tr>

				<td width="100">

					<?php echo JText::_( 'BLFA_FIRSTNAME' ); ?>

					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_FIRSTNAME' ); ?>::<?php echo JText::_( 'BLFA_TT_FIRST_NAME' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>

				</td>

				<td>

					<input class="feed-back inp-big required" type="text" maxlength="255" size="60" name="first_name" value="<?php echo $row->first_name?>" />

				</td>

			</tr>
			<?php if($lists['reg_lastname']){ ?>		
				<tr>

					<td width="100">

						<?php echo JText::_( 'BLFA_LASTNAME' ); ?><?php echo ($this->lists['reg_lastname_rq'] && $this->acl == 2)?" *":"";?>

						<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_LASTNAME' ); ?>::<?php echo JText::_( 'BLFA_TT_LAST_NAME' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>

					</td>

					<td>

						<input  class="feed-back inp-big <?php echo ($this->lists['reg_lastname_rq'] == 1 )?(" required"):("")?>" type="text" maxlength="255" size="60" name="last_name" value="<?php echo $row->last_name?>" />

					</td>

				</tr>
			<?php } 
				if($lists['nick_reg']){
			?>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BL_NICK' ); ?><?php echo $this->lists['nick_reg_rq']?" *":"";?>
				</td>
				<td>	
					<input class="feed-back inp-big <?php echo ($this->lists['nick_reg_rq'] )?" required":"";?>" type="text" maxlength="255" size="60" name="nick" value="<?php echo $row->nick?>" />
				</td>
			</tr>
			<?php } 
				if($lists['country_reg']){
			?>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BL_COUNTRY' ); ?><?php echo $this->lists['country_reg_rq']?" *":"";?>
				</td>
				<td>
					<div class="selectsty"><span class='down'><!-- --></span><?php echo $lists['country']?></div>
				</td>
			</tr>
			<?php } ?>
			<?php if(isset($lists['teams_seas'])): ?>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLFA_TEAM' ); ?>
				</td>
				<td>
					<div class="selectsty"><span class='down'><!-- --></span><?php echo $lists['teams_seas']?></div>
				</td>
			</tr>
			<?php endif;?>
			<?php
			//print_r($lists['ext_fields']);
			for($p=0;$p<count($lists['ext_fields']);$p++){
				if($lists['ext_fields'][$p]->field_type == '3' && !isset($lists['ext_fields'][$p]->selvals)){
				}else{
					if($lists['s_id'] && $lists['ext_fields'][$p]->season_related){
				?>
					<tr>
						<td width="100">
							<?php 						
								echo $lists['ext_fields'][$p]->name;
								
							?>
							<?php echo ($lists['ext_fields'][$p]->reg_require && $this->acl == 2)?" *":"";?>
						</td>
						<td>
							<?php
				
								switch($lists['ext_fields'][$p]->field_type){
										
									case '1':	echo $lists['ext_fields'][$p]->selvals;
												break;
									case '2':	echo $this->editor->display( 'extraf['.$lists['ext_fields'][$p]->id.']',  htmlspecialchars(isset($lists['ext_fields'][$p]->fvalue_text)?($lists['ext_fields'][$p]->fvalue_text):"", ENT_QUOTES), '550', '300', '60', '20', false ) ;
												break;
									case '3':	echo '<div class="selectsty"><span class="down"><!-- --></span>'.$lists['ext_fields'][$p]->selvals.'</div>';
												break;	
									case '0':					
									default:	echo '<input type="text"  class="feed-back inp-big '.(($lists['ext_fields'][$p]->reg_require )?' required':'').'" maxlength="255" size="60" name="extraf['.$lists['ext_fields'][$p]->id.']" value="'.(isset($lists['ext_fields'][$p]->fvalue)?htmlspecialchars($lists['ext_fields'][$p]->fvalue):'').'" />';	//&& $this->acl == 2	
												break;
										
								}
							?>
							<input type="hidden" name="extra_ftype[<?php echo $lists['ext_fields'][$p]->id;?>]" value="<?php echo $lists['ext_fields'][$p]->field_type?>" />
							<input type="hidden" name="extra_id[<?php echo $lists['ext_fields'][$p]->id;?>]" value="<?php echo $lists['ext_fields'][$p]->id?>" />
						</td>
					</tr>
				<?php	
					}else if(!$lists['ext_fields'][$p]->season_related && $lists['ext_fields'][$p]->reg_exist){ ?>
						<tr>
							<td width="100">
								<?php 						
									echo $lists['ext_fields'][$p]->name;
									
								?>
								<?php echo ($lists['ext_fields'][$p]->reg_require && $this->acl == 2)?" *":"";?>
							</td>
							<td>
								<?php
					
									switch($lists['ext_fields'][$p]->field_type){
											
										case '1':	echo $lists['ext_fields'][$p]->selvals;
													break;
										case '2':	echo $this->editor->display( 'extraf['.$lists['ext_fields'][$p]->id.']',  htmlspecialchars(isset($lists['ext_fields'][$p]->fvalue_text)?($lists['ext_fields'][$p]->fvalue_text):"", ENT_QUOTES), '550', '300', '60', '20', false ) ;
													break;
										case '3':	echo '<div class="selectsty"><span class="down"><!-- --></span>'.$lists['ext_fields'][$p]->selvals.'</div>';
													break;	
										case '0':					
										default:	echo ($lists['ext_fields'][$p]->reg_exist )?'<input type="text"  class="feed-back inp-big '.(($lists['ext_fields'][$p]->reg_require )?' required':'').'" maxlength="255" size="60" name="extraf['.$lists['ext_fields'][$p]->id.']" value="'.(isset($lists['ext_fields'][$p]->fvalue)?htmlspecialchars($lists['ext_fields'][$p]->fvalue):"").'" />':'';	//&& $this->acl == 2	
													break;
											
									}
								?>
								<input type="hidden" name="extra_ftype[<?php echo $lists['ext_fields'][$p]->id;?>]" value="<?php echo $lists['ext_fields'][$p]->field_type?>" />
								<input type="hidden" name="extra_id[<?php echo $lists['ext_fields'][$p]->id;?>]" value="<?php echo $lists['ext_fields'][$p]->id?>" />
							</td>
						</tr>
				<?php	}
				}
			}
		//}
			?>

            <tr>
                <td width="100">
                    <b><?php echo JText::_( 'BLFA_ABOUT_PLAYER' ); ?></b>
                    <!--<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLFA_ABOUT_PLAYER' ); ?>::<?php echo JText::_( 'BLFA_TT_ABOUT_PLAYER' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>-->
                </td>
                <td>
                    <?php echo $editor->display( 'about',  htmlspecialchars($row->about, ENT_QUOTES), '100%', '300', '60', '20', false ) ;  ?>
                </td>
            </tr>

		</table>
		
		<div style="margin-top:10px;border:1px solid #BBB;">
		<?php if($this->acl == 1 && !$lists["jssa_editplayer"] && $row->id){}else{?>
		<table style="padding:10px;" class="season-list">

			<tr>

				<td>

					<?php echo JText::_('BLFA_UPLFOTO');?>

					<span class="editlinktip hasTip" title="<?php echo JText::_('BLFA_UPLFOTO');?>::<?php echo JText::_( 'BLFA_TT_UPLOAD_PL_PHOTO' );?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span>

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
					<button class="send-button"  id="player_photo"><span><?php echo JText::_( 'BLFA_UPLOAD' ); ?></span></button>
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
                    }else{submitbutton('adplayer_apply');}
                };
            </script>
		<?php } ?>
		<?php

		if(count($lists['photos'])){

		?>

		<table class="season-list">

			<tr>

				<th class="title" width="30"><?php echo JText::_('BLFA_DELETE')?></th>

				<th class="title" width="30"><?php echo JText::_('BLFA_DEFAULT')?></th>

				<th class="title" ><?php echo JText::_('BLFA_TITLE')?></th>

				<th class="title" width="250"><?php echo JText::_('BLFA_IMAGE')?></th>

			</tr>

			<?php

			foreach($lists['photos'] as $photos){

			?>

			<td align="center">

				<a href="javascript:void(0);" title="<?php echo JText::_('BLFA_REMOVE')?>" onClick="javascript:Delete_tbl_row(this);"><img src="<?php echo JURI::base();?>components/com_joomsport/img/ico/close.png" title="<?php echo JText::_('BLFA_REMOVE')?>" /></a>

			</td>

			<td align="center">

				<?php

				$ph_checked = ($row->def_img == $photos->id) ? 'checked="true"' : "";

				

				?>

				<input type="radio" name="ph_default" value="<?php echo $photos->id;?>" <?php echo $ph_checked?>/>

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
		<input type="hidden" name="controller" value="<?php echo ($this->acl==1)?'admin':'moder'?>" />
		<input type="hidden" name="task" value="" />

		<input type="hidden" name="id" value="<?php echo $row->id?>" />

		<input type="hidden" name="boxchecked" value="0" />

		<input type="hidden" name="sid" value="<?php echo $lists["s_id"];?>" />
		<input type="hidden" name="tid" value="<?php echo $lists["tid"];?>" />
		
		<?php echo JHTML::_( 'form.token' ); ?>

		</form>
</div>
<?php } ?>