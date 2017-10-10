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
$row = $this->row;
$lists = $this->lists;
JHTML::_('behavior.tooltip');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');		
		?>
		<script type="text/javascript">
		Joomla.submitbutton = function(task) {
			submitbutton(task);
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
            var reg = /^\s+$/;
			 if(pressbutton == 'club_apply' || pressbutton == 'club_save' || pressbutton == 'club_save_new'){
			 	if(form.c_name.value != ''  && !reg.test(form.c_name.value)){
					submitform( pressbutton );
					return;
				}else{
					getObj('vname').style.border = "1px solid red";
					alert("<?php echo JText::_('BLBE_JSMDNOT22');?>");
					
					
				}
			}else{
				submitform( pressbutton );
					return;
			}			
		}
		function Delete_tbl_row(element) {
			var del_index = element.parentNode.parentNode.sectionRowIndex;
			var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
			element.parentNode.parentNode.parentNode.deleteRow(del_index);
		}
		</script>
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		
		<table>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_CLUBN' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_CLUBN' ); ?>::<?php echo JText::_( 'BLBE_TT_CLUBN' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span> 
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="c_name" id="vname" value="<?php echo htmlspecialchars($row->c_name)?>" onKeyPress="return disableEnterKey(event);" />
				</td>
			</tr>
			

			
			<?php
///////extra
			for($p=0;$p<count($lists['ext_fields']);$p++){
				if($lists['ext_fields'][$p]->field_type == '3' && !isset($lists['ext_fields'][$p]->selvals)){
				}else{
					if($lists['ext_fields'][$p]->season_related == 1 && count($lists['is_seas'])){//&& $lists["bonuses"] delete!!
					
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
								case '2':	echo $editor->display( 'extraf['.$lists['ext_fields'][$p]->id.']',  htmlspecialchars(isset($lists['ext_fields'][$p]->fvalue_text)?($lists['ext_fields'][$p]->fvalue_text):"", ENT_QUOTES), '550', '300', '60', '20', array('pagebreak', 'readmore') ) ;
											break;
								case '3':	echo $lists['ext_fields'][$p]->selvals;
											break;	
								case '0':					
								default:	echo '<input type="text" maxlength="255" size="60" name="extraf['.$lists['ext_fields'][$p]->id.']" value="'.(isset($lists['ext_fields'][$p]->fvalue)?htmlspecialchars($lists['ext_fields'][$p]->fvalue):"").'" />';		
											break;
									
							}
						?>
						<input type="hidden" name="extra_ftype[<?php echo $lists['ext_fields'][$p]->id;?>]" value="<?php echo $lists['ext_fields'][$p]->field_type?>" />
						<input type="hidden" name="extra_id[<?php echo $lists['ext_fields'][$p]->id;?>]" value="<?php echo $lists['ext_fields'][$p]->id?>" />
					</td>
				</tr>
				<?php	
				}
					if($lists['ext_fields'][$p]->season_related == 0){

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
								case '2':	echo $editor->display( 'extraf['.$lists['ext_fields'][$p]->id.']',  htmlspecialchars(isset($lists['ext_fields'][$p]->fvalue_text)?($lists['ext_fields'][$p]->fvalue_text):"", ENT_QUOTES), '550', '300', '60', '20', array('pagebreak', 'readmore') ) ;
											break;
								case '3':	echo $lists['ext_fields'][$p]->selvals;
											break;	
								case '0':					
								default:	echo '<input type="text" maxlength="255" size="60" name="extraf['.$lists['ext_fields'][$p]->id.']" value="'.(isset($lists['ext_fields'][$p]->fvalue)?htmlspecialchars($lists['ext_fields'][$p]->fvalue):"").'" />';		
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
			?>
			
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_CLUBDESCR' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_CLUBDESCR' ); ?>::<?php echo JText::_( 'BLBE_TT_CLUBDESCR' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span> 
				</td>
				<td>
					<?php echo $editor->display( 'c_descr',  htmlspecialchars($row->c_descr, ENT_QUOTES), '550', '300', '60', '20', array('pagebreak', 'readmore') ) ;  ?>
				
				</td>
			</tr>
		</table>
		<div style="margin-top:10px;bOrder:1px solid #BBB;">
		<table style="padding:10px;">
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_TT_CLUBUPLPH' ); ?>
				</td>
			</tr>
			<tr>
				<td>
				<input type="file" name="player_photo_1" value="" id="player_photo_1"/><input type="button" style="cursor:pointer;" value="<?php echo JText::_( 'BLBE_UPLOAD' ); ?>" id="player_photo"/>
				</td>
			</tr>
			<!--tr>
				<td>
				<input type="file" name="player_photo_2" value="" />
				</td>
			</tr-->
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_ONEPHSEL' ); ?>
				</td>
			</tr>
		</table>
            <script type="text/javascript">
                var photo1 = document.getElementById("player_photo_1");
                var but_on = document.getElementById("player_photo");
                var serv_sett = <?php echo $lists['post_max_size'];?>;
                but_on.onclick = function() {
                    if( photo1.files[0].size > serv_sett){
                        alert("Image too big size (change settings post_max_size)");
                    }else{submitbutton('club_apply');}

                };
            </script>
		<?php
		if(count($lists['photos'])){
			echo JHtml::_('images_gal.getImageGalleryUpl',  $lists['photos'],$row->def_img);
		}
		
		
		?>
		</div>
		
		
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="club_edit" />
		<input type="hidden" name="id" value="<?php echo $row->id?>" />
		<input type="hidden" name="cid[]" value="<?php echo $row->id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>