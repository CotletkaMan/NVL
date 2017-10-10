<?php
/* ------------------------------------------------------------------------
  # JoomSport Professional
  # ------------------------------------------------------------------------
  # BearDev development company
  # Copyright (C) 2011 JoomSport.com. All Rights Reserved.
  # @license - http://joomsport.com/news/license.html GNU/GPL
  # Websites: http://www.JoomSport.com
  # Technical Support:  Forum - http://joomsport.com/helpdesk/
  ------------------------------------------------------------------------- */
// no direct access
defined('_JEXEC') or die;
$row = $this->row;
$lists = $this->lists;
$labels = $this->labels;
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task) {
        submitbutton(task);
    }
    function submitbutton(pressbutton) {
        if (pressbutton == 'template_save' || pressbutton == 'template_apply' || pressbutton == 'template_list') {
            document.adminForm.task.value = pressbutton;
            document.adminForm.submit();
        }
    }
    window.addEvent('domready', function(){
        jQuery('#simple_event').on('blur', function(){
            resetText(jQuery(this));
        })

        jQuery('#simple_event').on('focus', function(){
            setText(jQuery(this));
        })
        
        jQuery('#simple_add').on('click', function(){
            if (jQuery('#simple_event').val() == '' || jQuery('#simple_event').val() == 'enter text...') {
                alert('<?php echo JText::_('BLBE_BET_DOENTERTEXT')?>');
                return false;
            }
            var trelem = new Element('tr');
            trelem.innerHTML = '<td><a href="#delete_simple" title="Delete" onClick="deleteRow(jQuery(this))"><img src="components/com_joomsport/img/publish_x.png"  border="0" alt="Delete"></a></td>'+
                '<td>'+jQuery('#simple_event').val()+'</td>'+
                '<td><input type="checkbox" value="1" class="simple_events" name="simple_events_check_new[]"/></td>'+
                '<input type="hidden" name="simple_events_new[]" value="'+jQuery('#simple_event').val()+'"/>';
            trelem.inject(jQuery('#new_simple').get(0), 'before');
            jQuery('#simple_event').val('<?php echo JText::_('BLBE_BET_ENTERTEXT')?>');
        })
        
        jQuery('a[href="#delete_simple"]').each(function(item, index){
          jQuery(item).on('click', function(){
              deleteRow(jQuery(this));
          })
        })
        
        jQuery('#diff_add').on('click', function(){
            if (jQuery('#diff_event_from').val() == '' && jQuery('#diff_event_from').val() == '') {
                alert('<?php echo JText::_('BLBE_BET_DOENTERDIFF')?>');
                return false;
            }
            
            if (parseFloat(jQuery('#diff_event_to').val()) == NaN || parseFloat(jQuery('#diff_event_from').val()) == NaN) {
                alert('<?php echo JText::_('BLBE_BET_DOENTERRIGHTDIFF')?>');
                return false;
            }
            
            if (parseFloat(jQuery('#diff_event_to').val()) < parseFloat(jQuery('#diff_event_from').val())) {
                alert('<?php echo JText::_('BLBE_BET_DIFFTOMORE')?>');
                return false;
            }
            
            var trelem = new Element('tr');
            trelem.innerHTML = '<td><a href="#delete_diff" title="Delete" onClick="deleteRow(jQuery(this))"><img src="components/com_joomsport/img/publish_x.png"  border="0" alt="Delete"></a></td>'+
                '<td>'+jQuery('#diff_event_from').val()+' < DIFF < '+jQuery('#diff_event_to').val()+'</td>'+
                '<td><input type="checkbox" value="1" class="diff_events" name="diff_events_check_new[]"/></td>'+
                '<input type="hidden" name="diff_events_new_from[]" value="'+jQuery('#diff_event_from').val()+'"/>'+
                '<input type="hidden" name="diff_events_new_to[]" value="'+jQuery('#diff_event_to').val()+'"/>';
            trelem.inject(jQuery('#new_diff').get(0), 'before');
            jQuery('#diff_event_from').val('');
            jQuery('#diff_event_to').val('');
        })
        
        jQuery('a[href="#delete_diff"]').each(function(item, index){
          jQuery(item).on('click', function(){
              deleteRow(jQuery(this));
          })
        })

    })    

    function resetText(el) {
        if (el.val() == ''){
            el.val('<?php echo JText::_('BLBE_BET_ENTERTEXT')?>');
        }
    }
    
    function setText(el) {
        if (el.val() == '<?php echo JText::_('BLBE_BET_ENTERTEXT')?>'){
            el.val('');
        }        
    }
    
    function deleteRow(item){
        item.parent().parent().remove();
        return false;
    }
</script>
<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
                    
        <div id="main_team_div" class="tabdiv">
                    
            <table>
                <tr>
                    <td width="100">
                        <?php echo JText::_('BLBE_BET_TEMPLNAME'); ?>
                        <span class="editlinktip hasTip" title="<?php echo JText::_('BLBE_BET_TEMPLNAME'); ?>::<?php echo JText::_('BLBE_BET_TEMPLNAME'); ?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span> 
                    </td>
                    <td>
                        <input type="text" maxlength="255" size="60" name="name" id="tmname" value="<?php echo htmlspecialchars($row->name) ?>" />
                    </td>
                </tr>
                <tr>
                    <td width="100">
                        <?php echo JText::_('BLBE_BET_DESC'); ?>
                        <span class="editlinktip hasTip" title="<?php echo JText::_('BLBE_BET_DESC'); ?>::<?php echo JText::_('BLBE_BET_DESC'); ?>"><img src="components/com_joomsport/img/quest.jpg" border="0" /></span> 
                    </td>
                    <td>
                        <?php echo $editor->display('description', htmlspecialchars($row->description, ENT_QUOTES), '550', '300', '60', '20', array('pagebreak', 'readmore')); ?>

                    </td>
                </tr>
            </table>
            <div class="tabdiv bet_default">
                <?php echo JText::_('BLBE_BET_BETEVENTS')?>
                <div>
                    <?php for($i = 0; $i < count($lists['default']); $i++): $list = $lists['default'][$i]?>
                        <?php $checked = $list->idtemplate==$row->id?'checked="true"':""?>
                        <input type="checkbox" name="default_events[]" value="<?php echo $list->id?>" <?php echo $checked?>/>
                        <label><?php echo $labels[$list->name]?></label><br/>
                    <?php endfor;?>
                </div>
                <table width="80%">
                    <tr>
                        <td valign="top">
                            <?php echo JText::_('BLBE_BET_SIMPLEEVENTS')?>
                            <table width="100%">
                                <tr>
                                    <td style="width:16px"></td>
                                    <td style="vertical-align: top"><?php echo JText::_('BLBE_BET_EVENT')?></td>
                                    <td style="vertical-align: top"><?php echo JText::_('BLBE_BET_FORPART')?></td>
                                    <td>
                                    </td>
                                </tr>
                                <?php for($i = 0; $i < count($lists['simple']); $i++): $list = $lists['simple'][$i];?>
                                    <tr>
                                        <td><a href="#delete_simple" title="Delete"><img src="components/com_joomsport/img/publish_x.png"  border="0" alt="Delete"></a></td>
                                        <td><?php echo $list->name?></td>
                                        <td><input type="checkbox" value="1" class="simple_events" name="simple_events_check_old[<?php echo $list->id?>]"/></td>
                                        <input type="hidden" name="simple_events_old[<?php echo $list->id?>]" value="<?php echo $list->name?>"/>
                                    </tr>
                                <?php endfor;?>
                                    <tr id="new_simple">
                                        <td></td>
                                        <td><input type="text" value="<?php echo JText::_('BLBE_BET_ENTERTEXT')?>" name="simple_event" id="simple_event"/></td>
                                        <td><input type="button" value="Add event" id="simple_add"/></td>
                                    </tr>                                    
                            </table>
                        </td>
                        <td valign="top">
                            <?php echo JText::_('BLBE_BET_DIFFEVENTS')?>
                            <table width="100%">
                                <tr>
                                    <td style="width:16px"></td>
                                    <td style="vertical-align: top"><?php echo JText::_('BLBE_BET_EVENT')?></td>
                                    <td style="vertical-align: top"><?php echo JText::_('BLBE_BET_FORPART')?></td>
                                    <td></td>
                                </tr>
                                <?php for($i = 0; $i < count($lists['diff']); $i++): $list = $lists['diff'][$i];?>
                                    <tr>
                                        <td><a href="#delete_diff" title="Delete"><img src="components/com_joomsport/img/publish_x.png"  border="0" alt="Delete"></a></td>
                                        <td><?php echo $list->difffrom?> < DIFF < <?php echo $list->diffto?></td>
                                        <td><input type="checkbox" value="1" class="diff_events" name="diff_events_check_old[<?php echo $list->id?>]"/></td>
                                        <input type="hidden" name="diff_events_old_to[<?php echo $list->id?>]" value="<?php echo $list->diffto?>"/>
                                        <input type="hidden" name="diff_events_old_from[<?php echo $list->id?>]" value="<?php echo $list->difffrom?>"/>
                                        <input type="hidden" name="diff_events_old[<?php echo $list->id?>]" value="<?php echo $list->id?>"/>
                                    </tr>
                                <?php endfor;?>
                                    <tr id="new_diff">
                                        <td></td>
                                        <td><input type="text" value="" name="diff_event_from[]" id="diff_event_from"/> < DIFF < <input type="text" value="" name="diff_event_to[]" id="diff_event_to"/></td>
                                        <td><input type="button" value="Add event" id="diff_add"/></td>
                                    </tr>                                    
                            </table>
                        </td>
                    </tr>
                </table>
            </div>                
            
        </div>
        
        <input type="hidden" name="option" value="com_joomsport" />
        <input type="hidden" name="task" value="template_edit" />
        <input type="hidden" name="id" value="<?php echo $row->id ?>" />
        <input type="hidden" name="cid[]" value="<?php echo $row->id ?>" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHTML::_('form.token'); ?>
</form>