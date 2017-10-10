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
require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_joomsport'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'tabs.php');
$etabs = new esTabs();

require_once(dirname(__FILE__).'/../../../helpers'.DIRECTORY_SEPARATOR.'roundhelper.php');

?>
<script type="text/javascript">
<!--
function in_array(what, where) {
    for(var i=0, length_array=where.length; i<length_array; i++)
        if(what == where[i])
            return true;
    return false;
}
Joomla.submitbutton = function(task) {
    submitbutton(task);
}
function submitbutton(pressbutton) {
    var form = document.adminForm;
    if (pressbutton == 'matchday_save' || pressbutton == 'matchday_apply' || pressbutton == 'matchday_save_new') {
        var reg=/^\s+$/;
        if(form.m_name.value != "" && form.s_id.value != 0 && !reg.test(form.m_name.value)){


            submitform( pressbutton );
            return;

        }else{
            alert("<?php echo JText::_( 'BLBE_JSMDNOT3' ); ?>");
        }
    }else{
        submitform( pressbutton );
        return;
    }
}


//-->
</script>


<script>

var rounds_count = parseInt('<?php echo count($lists['race']['rounds'])?count($lists['race']['rounds']):1?>');

var ordering_type = '<?php echo $lists['race']['options']->ordering_type?>';
var ordering_type_advance = '<?php echo $lists['race']['options']->ordering_type_advance?>';
var participant_result = null;

function jsAddRound(){
  
  var round = '<div>'
                +'<a href="javascript:void(0);" onclick="javascript:(this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode));">'
                    +'<span class="icon-delete"></span>'
                +'</a>'
            +'</div>'
        +'<div  class="js_round_header_div">'
            +'<div class="js_round_header_subfloat">'
                +'<?php echo JText::_( 'BLBE_ROUND_TITLE' ); ?> &nbsp;'
                +'<input type="text" name="round_title['+ rounds_count +']" value="" />'
            +'</div>'
            +'<div class="js_round_header_subfloat">'
                +'<fieldset class="jsRadio">'
                    +'<div class="controls">'
                        +'<label for="round_status'+ rounds_count +'0" id="round_status'+ rounds_count +'0-lbl" class="radio btn">'
                            +'<input type="radio" name="round_status['+ rounds_count +']" id="round_status'+ rounds_count +'0" value="0" class="inputbox"><?php echo JText::_( 'JUNPUBLISHED' ); ?>'
                        +'</label>'
                        +'<label for="round_status'+ rounds_count +'1" id="round_status'+ rounds_count +'1-lbl" class="radio btn btn-success">'
                            +'<input type="radio" name="round_status['+ rounds_count +']" id="round_status'+ rounds_count +'1" value="1" class="inputbox" checked><?php echo JText::_( 'JPUBLISHED' ); ?>'
                        +'</label>'
                    +'</div>'
                +'</fieldset>'
                +'<fieldset id="round_status_'+ rounds_count + '_adv" class="jsRadio">'
                        +'<div class="controls">'
                                +'<label for="round_status_adv'+ rounds_count + '2" id="round_status'+ rounds_count + '2-lbl" class="radio btn active btn-success">'
                                    +'<input type="radio" name="round_status_adv['+ rounds_count + ']" id="round_status_adv'+ rounds_count + '2" value="2" class="inputbox" checked><?php echo JText::_( 'BLBE_ROUND_FINAL' ); ?>'
                                +'</label>'
                                +'<label for="round_status_adv'+ rounds_count + '1" id="round_status'+ rounds_count + '1-lbl" class="radio btn">'
                                    +'<input type="radio" name="round_status_adv['+ rounds_count + ']" id="round_status_adv'+ rounds_count + '1" value="1" class="inputbox" ><?php echo JText::_( 'BLBE_ROUND_QUALIFICATION' ); ?>'
                                +'</label>'
                        +'</div>'
                +'</fieldset>'
            +'</div>'
        +'</div>'
     +'<div class="js_round_main_div">'
        +'<table>'
        +'<tr>'
            +'<th>'
                 +'<?php echo JText::_( 'BLBE_LANGVIEWSOTH_PARTIC' ); ?>'
             +'</th>'
             +'<?php for($intB = 0; $intB < intval($lists['race']['options']->attempts); $intB ++){?>'
                 +'<th>'
                     +'<?php echo JText::_( 'BLBE_ROUND_ATTEMPT' ); ?>&nbsp;<?php echo ($intB + 1);?>'
                 +'</th>'
                 +'<?php } ?>'
             +'<?php if($lists['race']['options']->penalty == 1){?>'
                 +'<th>'
                     +'<?php echo JText::_( 'BLBE_ROUND_PENALTY' ); ?>'
                 +'</th>'
                 +'<?php } ?>'
             +'<th>'
                 +'<?php echo JText::_( 'BLBE_ROUND_RESULT' ); ?>'
             +'</th>'
         +'</tr>'
            +'<?php for($intA = 0; $intA < count($lists['race']['participiants']); $intA ++){?><tr>'
                +'<td>'
                    +'<?php echo $lists['race']['participiants'][$intA]->t_name?>'
                    +'<input type="hidden" name="round_partic['+ rounds_count +'][]" value="<?php echo $lists['race']['participiants'][$intA]->id;?>" />'
                +'</td>'
                +'<?php for($intB = 0; $intB < intval($lists['race']['options']->attempts); $intB ++){?><td>'
                        +'<input type="text" value="" name="round_attempts['+ rounds_count +'][<?php echo $lists['race']['participiants'][$intA]->id;?>][]" class="js_round_resultbox" />'
                    +'</td><?php } ?>'
                +'<?php  if($lists['race']['options']->penalty == 1){ ?>'
                    +'<td>'
                        +'<input type="text" value="" name="round_penalty['+ rounds_count +'][]" class="js_roundbox js_round_resultbox" />'
                    +'</td>'
                    +'<?php } ?>'
                    +'<td class="js_div_round_result">'
                        +'<input type="text" value="" name="round_result['+ rounds_count +'][]" class="js_round_result" />'
                    +'</td>'    
            +'</tr><?php } ?>'
        +'</table>'        
    +'</div>';
    
    var newDiv = document.createElement('div');

    newDiv.className = 'round_container';
    newDiv.innerHTML = round;

    var parentElem = document.getElementById('js_rounds_div');
 
    document.getElementById('js_rounds_div').insertBefore(newDiv, parentElem.firstChild);
    
    rounds_count ++;
    
}

jQuery( document ).ready(function() {
  jQuery( "#js_rounds_div" ).on( 'change', '.js_round_resultbox',function() {
    
    var attempts_fill = 0;
    var input_name = jQuery(this).attr("name");
    
    jQuery(this).parent().parent().find('.js_round_resultbox').each( function(index){
        if(!jQuery( this ).hasClass( "js_roundbox" )){
            setRoundResult(jQuery(this).val());
            if(jQuery(this).val() != ''){
                attempts_fill = attempts_fill + 1;
            }    
        }
    });
    
    var result_input = jQuery(this).parent().parent().find('.js_round_result');
    var penalty_input = jQuery(this).parent().parent().find('.js_roundbox');

    if(ordering_type_advance == '2' && attempts_fill && participant_result){
        participant_result = participant_result / attempts_fill;
        participant_result = parseFloat(participant_result).toFixed(3);
    }    

    if(penalty_input.val()){
        participant_result = sumTimeVals(penalty_input.val());
   
    } 
    //alert(participant_result);
    result_input.val(participant_result);
    participant_result = null;
  });
  
  jQuery('#js_rounds_div').on( 'keyup', '.js_round_resultbox',function (event) { 
        var tmpval = this.value;
        this.value = this.value.replace(/[^-0-9\.:]/g,'');
        //alert(event.which +'&&'+ this.value);
        if(event.which == '189' && this.value != '-'){
            //alert(this.value);
            this.value = this.value.substr(0, this.value.length -1);
        }    
    });   
});

function setRoundResult(value){
var value_not_dot = parseFloat(value.replace(/:/g,''));
var participant_result_not_dot = null;
if(participant_result){
 
    var participant_result_not_dot = parseFloat(participant_result.toString().replace(/:/g,''));
}
//alert(value_not_dot + '--' + participant_result_not_dot);
    switch(ordering_type_advance){
        case '0':
            if(ordering_type == '0'){
                if(participant_result_not_dot < value_not_dot || participant_result == null){
                    participant_result = value;

                }    
            }else{
                if((value != '' && participant_result_not_dot > value_not_dot) || participant_result == null){
                    participant_result = value;
                } 
            }    
            break;
        case '1': //sum
            if(participant_result == null){
                participant_result = 0;
            }    
            if(value != ''){
                participant_result = sumTimeVals(value);
            } 
            break;
        case '2': //avg
            if(participant_result == null){
                participant_result = 0;
            }    
            if(value != ''){
                participant_result = sumwithfloats(participant_result, value);
            } 
            break;
         
    }    
}

function sumTimeVals(penalty_input){
   
    var arr_val = participant_result.toString().split(':');
    var penalty_val = penalty_input.toString().split(':');

    if(arr_val.length > 1 || penalty_val.length > 1){

        var arrcount = (arr_val.length > penalty_val.length) ? arr_val.length : penalty_val.length;
        var max = (arrcount == arr_val.length) ? 1 : 0;
        var diff = (max == 1) ? (parseInt(arrcount) - parseInt(penalty_val.length)) : (parseInt(arrcount) - parseInt(arr_val.length));
        var sumres = '';
        if(max == 1){
            var arr_one = arr_val;
            var arr_two = penalty_val;
        }else{
            var arr_one = penalty_val;
            var arr_two = arr_val;
        }    
        var incr = 0;
        for(intA = arrcount-1; intA >= 0; intA --){
            
            if(typeof arr_two[intA - parseInt(diff)] === 'undefined'){
                sumres = arr_one[intA] + sumres;
                
            }else{
               if(((parseFloat(arr_one[0]) < 0 && parseFloat(arr_two[0]) > 0) || (parseFloat(arr_two[0]) < 0 && parseFloat(arr_one[0]) > 0 )) && intA != 0){
                   var sumtmp = sumwithfloats(sumwithfloats(arr_one[intA], -arr_two[intA - parseInt(diff)]),incr);
               }else{    
                   var sumtmp = sumwithfloats(sumwithfloats(arr_one[intA], arr_two[intA - parseInt(diff)]), incr);
               }
               
               if(intA != 0 && parseFloat(sumtmp) < 0){
                   incr = -1;
                   sumtmp = sumwithfloats(60, sumtmp);
               }else if(intA != 0 && parseFloat(sumtmp) >= 60){
                   incr = 1;
                   sumtmp = sumwithfloats(sumtmp, -60);
               }else{    
                   incr = 0;
               }    
               
               sumres =  sumtmp.toString() + sumres;
            }
            
            if(intA != 0){
                sumres = ':' + sumres;
            }    
            
        }
        
        return sumres;
    }else{
        return sumwithfloats(participant_result, penalty_input);
    }    
}    

function sumwithfloats(val1, val2){
    return Math.round((parseFloat(val1) + parseFloat(val2))*100)/100;
}    

jQuery( document ).ready(function() {
    jQuery( "#js_rounds_div" ).on( 'click', '.btn',function() {
        var radioid = jQuery(this).prop('for');
        
        jQuery(this).parent().children('label').each(function(index, element){
            //alert(jQuery(element).prop("class"));
            jQuery(element).prop("class", "radio btn");
        });    
        
        var adv_fieldset = jQuery('#'+radioid).prop("name");
        adv_fieldset = adv_fieldset.replace('[','_');
        adv_fieldset = adv_fieldset.replace(']','_');
        adv_fieldset = adv_fieldset + 'adv';

        if(jQuery('#'+radioid).val() > 0){
            jQuery(this).prop("class", "radio btn jsRactive btn-success");
            jQuery('#' + adv_fieldset).show();
        }else{
            jQuery(this).prop("class", "radio btn jsRactive btn-danger");
            jQuery('#' + adv_fieldset).hide();
        }    
    });   
});

var extracol_count = parseInt('<?php echo count($lists['race']['extracol']);?>');

function addExtraFieldTR(){
    var tbl = jQuery("#table_extra_col  tbody");
    var extra_title = jQuery('#add_extra_col').val();
    
    if(extra_title){
        var tr = document.createElement("tr");
        var html = '<td><a href="javascript:void(0);" onclick="javascript:deleteExtraValue(this, '+extracol_count+');">'
            +'<span class="icon-delete"></span><input type="hidden" name="round_extra_col_id[]" value="'+extracol_count+'" />'
            +'</a></td>'
            +'<td><input type="text" name="round_extra_col[]" value="'+extra_title+'" /></td>'
            +'<td class="td_race_moveup"></td>'
            +'<td class="td_race_movedown"></td>'
        tr.innerHTML = html;
        tbl.append(tr);
        recalcEztraTable();
        jQuery('#add_extra_col').val('');
        
        race_add_extraCol(extracol_count, extra_title);
        extracol_count ++;
    }            
}

function recalcEztraTable(){
    var size = jQuery("#table_extra_col  tbody tr").length;

    jQuery("#table_extra_col  tbody tr").each(function( index ) {
        var up_html = index ? '<a href="javascript: void(0);" onclick="javascript:race_Up_tbl_row(this); return false;" title="Move up"><img src="components/com_joomsport/img/up.gif" border="0" alt="Move up"></a>' : '';
        var down_html = (size - index > 1) ? '<a href="javascript: void(0);" onclick="javascript:race_Down_tbl_row(this); return false;" title="Move down"><img src="components/com_joomsport/img/down.gif" border="0" alt="Move down"></a>' : '';
        
        jQuery(this).children('.td_race_moveup').html(up_html);
        jQuery(this).children('.td_race_movedown').html(down_html);
    });
}

function deleteExtraValue(el, ecount){
    el.parentNode.parentNode.parentNode.removeChild(el.parentNode.parentNode);
    recalcEztraTable();
    jQuery('.js_round_main_div table').find('tr').each(function(){
        jQuery(this).find('th.extra_col_id_'+ecount).remove();
        jQuery(this).find('td.extra_col_id_'+ecount).remove();
   });
    
}

function race_Up_tbl_row(el){
    var thisRow = jQuery(el).closest('tr');
    var prevRow = thisRow.prev();
    if (prevRow.length) {
        prevRow.before(thisRow);
    }
    recalcEztraTable();
}    
function race_Down_tbl_row(el){
    var thisRow = jQuery(el).closest('tr');
    var nextRow = thisRow.next();
    if (nextRow.length) {
        nextRow.after(thisRow);
    }
    recalcEztraTable();
}   

function race_add_extraCol(id, name){
    jQuery('.js_round_main_div table').find('tr').each(function(){
        var roundid = jQuery(this).parent().parent().parent().find('input[name="round_order\[\]"]').val();
        jQuery(this).find('th.js_div_round_result_header').eq(0).before('<th class="extra_col_id_'+id+'">'+name+'</th>');
        
        jQuery(this).find('td.js_div_round_result').eq(0).before('<td class="extra_col_id_'+id+'"><input type="text" class="extra_col_input" name="extracol_input_'+id+'['+roundid+'][]" value="" /></td>');
   });
}    

</script>


<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm">

<div style="clear:both"></div>
<div id="main_conf_div" class="tabdiv">
<table>
    <tr>
        <td width="120">
			<?php echo JText::_( 'BLBE_MATCHDAYNAME' ); ?>
            <span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_MATCHDAYNAME' ); ?>::<?php echo JText::_( 'BLBE_TT_MATCHDAYNAME' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
        </td>
        <td>
            <input type="text" maxlength="255" size="60" name="m_name" value="<?php echo htmlspecialchars($row->m_name)?>" />
        </td>
    </tr>
    <tr>
        <td width="120">
            <?php echo JText::_( 'BLBE_TOURNAMENT' ); ?>
        </td>
        <td>
            <?php echo $lists['tourn'];?>
        </td>
    </tr>
    <tr>
        <td width="120">
            <?php echo JText::_( 'BLBE_FROM_DATE' ); ?>
        </td>
        <td>
            <?php
                echo JHTML::_('calendar', (intval($row->start_date) ? $row->start_date : '0000-00-00'), 'start_date', 'start_date', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'20',  'maxlength'=>'10'));
            ?>
        </td>
    </tr>
    <tr>
        <td width="120">
            <?php echo JText::_( 'BLBE_TO_DATE' ); ?>
        </td>
        <td>
            <?php
                echo JHTML::_('calendar', (intval($row->end_date) ? $row->end_date : '0000-00-00'), 'end_date', 'end_date', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'20',  'maxlength'=>'10'));
            ?>
        </td>
    </tr>
</table>
<br />
<div>
    <fieldset>
        <legend><?php echo JText::_( 'BLBE_RACE_ADDITIONAL' ); ?></legend>
        <table id="table_extra_col">
            <tbody>
                <?php
                if(count($lists['race']['extracol'])){
                    for($intQ = 0; $intQ < count($lists['race']['extracol']); $intQ ++){
                        $cur = $lists['race']['extracol'][$intQ];
                        ?>
                        <tr>
                            <td><a href="javascript:void(0);" onclick="javascript:deleteExtraValue(this, '<?php echo $intQ;?>');">
                                    <span class="icon-delete"></span><input type="hidden" name="round_extra_col_id[]" value="<?php echo $intQ;?>" />
                            </a></td>
                            <td><input type="text" name="round_extra_col[]" value="<?php echo $cur->name;?>" /></td>
                            <td class="td_race_moveup"></td>
                            <td class="td_race_movedown"></td>
                        </tr>    
                        <?php
                    }
                    echo '<script>recalcEztraTable();</script>';
                }    
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td width="20">
                    </td>    
                    <td style="padding-top:10px;">
                        <input type="text" name="add_extra_col" id="add_extra_col" value="" />
                    </td>
                    <td colspan="2">
                        <input type="button" class="btn" value="<?php echo JText::_( 'BLBE_ADD' ); ?>" onclick="addExtraFieldTR();" />
                    </td>    
                </tr>  
            </tfoot>
        </table>
    </fieldset>    
</div>    
<br />
<div style="padding-left:10px;">
    <input type="button" class="btn" value="<?php echo JText::_( 'BLBE_ADDROUND' ); ?>" onclick="jsAddRound();" />
</div>    
<div id="js_rounds_div">
    <?php 
    if(count($lists['race']['rounds'])){
        for($intQ = 0; $intQ < count($lists['race']['rounds']); $intQ ++){
            roundhelper::roundHTML($intQ, $lists);
        }
    }else{
        roundhelper::roundHTML(0, $lists);
    }
    ?>
        
</div>    

<input type="hidden" name="t_single" value="<?php echo $lists['t_single']?>" />
<input type="hidden" name="t_knock" value="0" />
<input type="hidden" name="task" value="matchday_list" />
<input type="hidden" name="id" value="<?php echo $row->id?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="s_id" value="<?php echo $lists['s_id'];?>" />
<input type="hidden" name="jscurtab" id="jscurtab" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
