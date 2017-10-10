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

	
		?>
		<script type="text/javascript">
		Joomla.submitbutton = function(task) {
			submitbutton(task);
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			 if(pressbutton == 'tour_apply' || pressbutton == 'tour_save' || pressbutton == 'tour_save_new'){
                 var reg=/^\s+$/;
                 if(form.name.value && !reg.test(form.name.value)){
					submitform( pressbutton );
					return;
				}else{
					getObj('trname').style.border = "1px solid red";
					alert("<?php echo JText::_('BLBE_JSMDNOT1');?>");
					
					
				}
			}else{
				submitform( pressbutton );
					return;
			}			
		}	
		function delete_logo(){
			getObj("logoiddiv").innerHTML = '';
		}
		
		</script>
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		
		<table>
			<tr>
				<td width="130">
					<?php echo JText::_( 'BLBE_TOURNAMENTNAME' ); ?>
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="name" id="trname" value="<?php echo htmlspecialchars($this->row->name);?>" onKeyPress="return disableEnterKey(event);" />
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'JSTATUS' ); ?>
				</td>
				<td>
					<div class="controls"><fieldset class="radio btn-group"><?php echo $this->lists['published'];?></fieldset></div>
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_TOURNMODE' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_TOURNMODE' ); ?>::<?php echo JText::_( 'BLBE_TOURNMODE_DESC' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<?php echo $this->lists['t_single'];?>
				</td>
			</tr>
			<tr>
				<td width="100">
					<?php echo JText::_( 'BLBE_TOURNTYPE' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_TOURNTYPE' ); ?>::<?php echo JText::_( 'BLBE_TOURNTYPE_DESCR' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
                                <td style="padding:10px 0px;">
					<?php //echo $this->lists['tourn_type'];?>
                                    <?php
                                    $chk = $this->row->tournament_type ? false : true;
                                    ?>
                                    <div style="float:left; margin-right: 20px;">
                                        <input style="margin-top: 0px;" id="tournament_type_0" type="radio" name="tournament_type" value="0" <?php echo $chk ? 'checked' : '';?> />
                                        <label for="tournament_type_0" style="display:inline-block;">
                                            <b><?php echo JText::_('BLBE_TOURTYPEMATCH');?></b>
                                        </label>
                                        <br />
                                        <?php echo JText::_('BLBE_TOURTYPEMATCH_DESC');?>
                                    </div>
                                    <div style="float:left;">
                                        <input style="margin-top: 0px;" id="tournament_type_1" type="radio" name="tournament_type" value="1" <?php echo $chk ? '' : 'checked';?> />
                                        <label for="tournament_type_1" style="display:inline-block;">
                                            <b><?php echo JText::_('BLBE_TOURNTYPERACING');?></b>
                                        </label>
                                        <br />
                                        <?php echo JText::_('BLBE_TOURNTYPERACING_DESC');?>
                                    </div>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<?php echo JText::_( 'BLBE_TOURN_LOGO' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_TOURN_LOGO' ); ?>::<?php echo JText::_( 'BLBE_TT_TOURN_LOGO' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span> 
				</td>
				<td>
					<input type="file" name="t_logo"  id="logo"/>&nbsp;<input class="btn btn-small" type="button" style="cursor:pointer;" value="<?php echo JText::_('BLBE_TOURN_UPLOAD_PHOTO');?>" id="get_logo"/>
					<br />
					<?php
					
					if($this->row->logo && is_file('../media/bearleague/'.$this->row->logo)){
						echo '<div id="logoiddiv"><img width="120" src="'.JURI::base().'../media/bearleague/'.$this->row->logo.'">';
						echo '<input type="hidden" name="istlogo" value="1" />';
						?>
						<a href="javascript:void(0);" title="<?php echo JText::_( 'BLBE_REMOVE' ); ?>" onClick="javascript:delete_logo();"><img src="<?php echo JURI::base();?>components/com_joomsport/img/publish_x.png" title="Remove" /></a>
						</div>
					<?php
					}
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'BLBE_ABOUT_TOURN' ); ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_ABOUT_TOURN' ); ?>::<?php echo JText::_( 'BLBE_TT_ABOUT_TOURN' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
				</td>
				<td>
					<?php echo $editor->display( 'descr',  htmlspecialchars($this->row->descr, ENT_QUOTES), '550', '300', '60', '20', array('pagebreak', 'readmore') ) ;  ?>
				</td>
			</tr>

		</table>
            <script type="text/javascript">
                var logo = document.getElementById("logo");
                var but_on = document.getElementById("get_logo");
                var serv_sett = <?php echo $lists['post_max_size'];?>;
                but_on.onclick = function() {
                    if(logo.files[0]){
                        if(logo.files[0].size > serv_sett){
                            alert("Image too big (change settings post_max_size)");
                        }else{if(document.adminForm.t_logo.value){submitform('tour_apply');};}
                    }
                }
            </script>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $this->row->id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>