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
                            alert(partip.options[partip.selectedIndex].text+' <?php echo JText::_("BLBE_KN_DUBL");?>');return false;
                        }
                    }else{
                        for(i=0;i<partip.length;i++){



                            if(!in_array(partip[i].value,arrpl) ){
                                if(partip[i].value != '0' && partip[i].value != '-1'){
                                    arrpl.push(partip[i].value);
                                }

                            }else{
                                alert(partip[i].options[partip[i].selectedIndex].text+' <?php echo JText::_("BLBE_KN_DUBL");?>');return false;
                            }

                        }
                    } //if partip == 1


                }
                if(partip_aw){
                    if(partip_aw.options){
                        if(!in_array(partip_aw.value,arrpl) ){
                            if(partip_aw.value != '0' && partip_aw.value != '-1'){
                                arrpl.push(partip_aw.value);
                            }

                        }else{
                            alert(partip_aw.options[partip_aw.selectedIndex].text+' <?php echo JText::_("BLBE_KN_DUBL");?>');
							return false;
                        }

                    }else{

                        for(i=0;i<partip_aw.length;i++){



                            if(!in_array(partip_aw[i].value,arrpl) ){
                                if(partip_aw[i].value != '0' && partip_aw[i].value != '-1'){
                                    arrpl.push(partip_aw[i].value);
                                }

                            }else{
                                alert(partip_aw[i].options[partip_aw[i].selectedIndex].text+' <?php echo JText::_("BLBE_KN_DUBL");?>');return false;
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
            var errortime = '';
            var mt_time = eval("document.adminForm['match_time[]']");
            if(mt_time){
                if(mt_time.length){
                    for(i=0;i<mt_time.length;i++){
                        var regE = /[0-2][0-9]:[0-5][0-9]/;
                        if(!regE.test(mt_time[i].value) && mt_time[i].value != ''){
                            errortime = '1';
                            mt_time[i].style.border = "1px solid red";
                        }else{
                            mt_time[i].style.border = "1px solid #C0C0C0";
                        }
                    }
                }else{
                    var regE = /[0-2][0-9]:[0-5][0-9]/;
                    if(!regE.test(mt_time.value) && mt_time.value != ''){
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
            alert("<?php echo JText::_( 'BLBE_JSMDNOT3' ); ?>");
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
    var tm_played = getObj('tm_played').checked;
    var venue_id = getObj('venue_id_new');
    var pl = 0;
    if('<?php echo $lists['s_id']?>' == '-1'){
        if(getObj('fr_choose_2').checked){
            pl = 1;
            team1 = getObj('plmd');
            team2 = getObj('plmd_away');
            score1 = getObj('add_score1_sg').value;
            score2 = getObj('add_score2_sg').value;
        }
    }
    ///UPDATE
    if (team1.value == 0 || team2.value == 0) {
	<?php if($lists['t_single']){?>
        alert("<?php echo JText::_( 'BLBE_JSMDNOT5' ); ?>");return;
		<?php }else{?>
        alert("<?php echo JText::_( 'BLBE_JSMDNOT6' ); ?>");return;
		<?php }?>
    }
    if (((score1) == '' || (score2) == '') && tm_played){
        alert("<?php echo JText::_( 'BLBE_JSMDNOT21' ); ?>");return;
    }
    if ( team1.value == team2.value ){
        alert("<?php echo JText::_( 'BLBE_JSMDNOT2' ); ?>");return;
    }

    var regE = /[0-2][0-9]:[0-5][0-9]/;
    //console.log(getObj('match_time_new').value);
    if(!regE.test(getObj('match_time_new').value) && getObj('match_time_new').value != ''){
        alert("<?php echo JText::_( 'BLBE_JSMDNOT7' ); ?>");return;
    }
    var tbl_elem = getObj('new_matches');
    var row = tbl_elem.insertRow(tbl_elem.rows.length);
    var cell1 = document.createElement("td");
    var cell2 = document.createElement("td");
    var cell3 = document.createElement("td");
    var cell4 = document.createElement("td");
    var cell5 = document.createElement("td");
    var cell6 = document.createElement("td");
    var cell9 = document.createElement("td");
    var cell10 = document.createElement("td");
    var cell11 = document.createElement("td");

    var input_hidden = document.createElement("input");
    input_hidden.type = "hidden";
    input_hidden.name = "match_id[]";
    input_hidden.value = 0;
    cell1.appendChild(input_hidden);
    cell1.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('BLBE_DELETE');?>"><img src="components/com_joomsport/img/publish_x.png"  bOrder="0" alt="Delete"></a>';

    var input_hidden = document.createElement("input");
    input_hidden.type = "hidden";
    input_hidden.name = "matchtype[]";
    input_hidden.value = pl;
    cell1.appendChild(input_hidden);

    var input_hidden = document.createElement("input");
    input_hidden.type = "hidden";
    input_hidden.name = "home_team[]";
    input_hidden.value = team1.value;
    var team1bet = team1.options[team1.selectedIndex].text;

    cell2.innerHTML = team1.options[team1.selectedIndex].text;
    cell2.appendChild(input_hidden);

    var input_hidden = document.createElement("input");
    input_hidden.type = "text";
    input_hidden.name = "home_score[]";
    input_hidden.value = score1;
    input_hidden.size = 3;
    input_hidden.style.width = '30px';
    input_hidden.setAttribute("maxlength",5);
    cell3.className = "col-score";
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
    input_hidden.style.width = '30px';
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
        cell9.appendChild(input_hidden);
    }
    var input_hidden = document.createElement("input");
    input_hidden.type = "hidden";
    input_hidden.name = "away_team[]";
    input_hidden.value = team2.value;
    var team2bet = team2.options[team2.selectedIndex].text;

    cell4.innerHTML = team2.options[team2.selectedIndex].text;
    cell4.appendChild(input_hidden);
    cell10.innerHTML = '';

    ////-------------new---------------////

    var cell7 = document.createElement("td");
    var cell8 = document.createElement("td");

    var input_hidden = document.createElement("input");
    input_hidden.type = "text";
    input_hidden.name = "match_data[]";
    input_hidden.value = getObj('tm_date').value;
    input_hidden.size = 12;
    input_hidden.style.width = '70px';
    input_hidden.setAttribute("maxlength",10);

    cell6.appendChild(input_hidden);
    cell6.align = "left";


    var input_hidden = document.createElement("input");
    input_hidden.type = "text";
    input_hidden.name = "match_time[]";
    input_hidden.value = getObj('match_time_new').value;
    input_hidden.size = 12;
    input_hidden.style.width = '50px';
    input_hidden.setAttribute("maxlength",5);

    cell7.appendChild(input_hidden);


    cell7.align = "left";


    var input_hidden = document.createElement("input");
    input_hidden.type = "checkbox";
    input_hidden.name = "match_played[]";
    if(getObj('tm_played').checked){
        input_hidden.checked = true;
        input_hidden.value = '1';
    }else{
        input_hidden.value = '0';
    }
    cell8.appendChild(input_hidden);

	
	var input_hidden = document.createElement("input");
    input_hidden.type = "hidden";
    input_hidden.name = "venue_id[]";
    input_hidden.value = venue_id.value;
    //var team1bet = venue_id.options[venue_id.selectedIndex].text;

    cell5.innerHTML = venue_id.selectedIndex!=0?venue_id.options[venue_id.selectedIndex].text:'';
    cell5.appendChild(input_hidden);
    ////------------/new---------------////

    row.appendChild(cell1);
    row.appendChild(cell2);
    row.appendChild(cell3);
    if('<?php echo $lists['s_enbl_extra']?>' == '1'){
        row.appendChild(cell9);
    }
    row.appendChild(cell4);
    row.appendChild(cell8);

    row.appendChild(cell6);

    row.appendChild(cell7);
    row.appendChild(cell5);
    row.appendChild(cell10);
    //row.appendChild(cell11);
    getObj('teams1').value =  0;
    getObj('teams2').value = 0;
    <?php if($lists['s_id'] == -1){?>
	getObj('plmd').value =  0;
	getObj('plmd_away').value = 0;
    <?php }?>
    getObj('add_score1').value = '';
    getObj('add_score2').value = '';
    getObj('venue_id_new').value = 0;
    //addNewBetMatch(team1bet, team2bet);
    if('<?php echo $lists['s_enbl_extra']?>' == '1'){
    getObj('extra_timez').checked = false;
    }
}

function addNewBetMatch(team1, team2){
    var betDate = jQuery('#bet_opt_div .adminlist input[name^="betfinishdate"]').get(0);
    var betTime = jQuery('#bet_opt_div .adminlist input[name^="betfinishtime"]').get(0);
    var el = new Element('tr');
    el.set('html', '<td style="vertical-align: top">'+
            '<input type="checkbox" name="bet_available[]" value="1"/>'+
            '</td>'+
            '<td>'+
            '<table width="100%">'+
            '<tr>'+
            '<td style="text-align:center">'+team1+'</td>'+
            '<td></td>'+
            '<td style="text-align:center">'+team2+'</td>'+
            '</tr>'+
            '</table>'+
            '</td>'+
            '<td width="10%" style="vertical-align: top">'+
            '<input size="12" type="text" name="betfinishdate[]" value="'+betDate.get('value')+'"/>'+
            '</td>'+
            '<td style="vertical-align: top">'+
            '<input size="12" type="text" name="betfinishtime[]" value="'+betTime.get('value')+'"/>'+
            '</td>');
    el.inject(jQuery('#bet_opt_div .adminlist > tbody').get(0));
}

function Delete_tbl_row(element) {
    var del_index = element.parentNode.parentNode.sectionRowIndex;
    var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
    element.parentNode.parentNode.parentNode.deleteRow(del_index);
    jQuery('#bet_opt_div .adminlist > tbody tr:nth-child(2)').remove()
}

function toggleBetEvents(element){
    var el = element.getParent('td').getParent('tr').getElement('.betevents');
    if (el.getStyle('display') != 'none') {
        element.getParent('td').getParent('tr').getElements('.betevents').setStyle('display', 'none');
    } else {
        element.getParent('td').getParent('tr').getElements('.betevents').setStyle('display', '');
    }

}

function unbl_frmd(cur){
    if(cur == 2){
        getObj('teams1').disabled = 'true';
        getObj('teams2').disabled = 'true';
        getObj('plmd').disabled = '';
        getObj('plmd_away').disabled = '';

    }
    if(cur == 1){
        getObj('teams1').disabled = '';
        getObj('teams2').disabled = '';
        getObj('plmd').disabled = 'true';
        getObj('plmd_away').disabled = 'true';
       // var container = jQuery('#teams1_chzn');
       // container.addClass("chzn-disabled");
    }
}

//-->
</script>

<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm">
<ul class="tab-box">
	<?php
	if($lists['avail_betting']){
		echo $etabs->newTab(JText::_( 'BLBE_MAIN' ),'main_conf','','vis');
		echo $etabs->newTab(JText::_( 'BLBE_BET_OPTIONS' ),'bet_opt','');
	}
	?>
</ul>
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
	<?php if(!$lists['t_type']){
	if($lists['s_id'] != -1) {?>
        <tr>
            <td width="100">
				<?php echo JText::_( 'BLBE_ISPLAYOFF' ); ?>
                <span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_ISPLAYOFF' ); ?>::<?php echo JText::_( 'BLBE_TT_ISPLAYOFF' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
            </td>
            <td>
                <div class="controls"><fieldset class="radio btn-group"><?php echo $this->lists['is_playoff'];?></fieldset></div>

            </td>
        </tr>
		<?php } } ?>
</table>
<br />
<?php

if($lists['t_type']){
    $version = new JVersion;
    $joomla_v = $version->getShortVersion();
    if($lists['t_type'] == 1){
        if (substr($joomla_v,0,3) > '3.0') {
            $javascript = 'javascript: jQuery.post( \'index.php?tmpl=component&option=com_joomsport&task=knockout&sid='.$lists['s_id'].'&t_single='.$lists['t_single'].'&fr_id=\'+jQuery(\'#format_post\').val(), function( data ) {jQuery(\'#mapformat\').html( data );});';
        } elseif(substr($joomla_v,0,3) >= '1.6'){
            $javascript = 'javascript:  var myRequest = new Request({url:\'index.php?tmpl=component&option=com_joomsport&task=knockout&sid='.$lists['s_id'].'&t_single='.$lists['t_single'].'&fr_id=\'+$(\'format_post\').value, method: \'post\', onSuccess: function(responseText){$(\'mapformat\').innerHTML = responseText;}}).send()';
        }else{
            $javascript = 'javascript:  var myRequest = new Ajax(\'index3.php?option=com_joomsport&task=knockout&sid='.$lists['s_id'].'&t_single='.$lists['t_single'].'&fr_id=\'+$(\'format_post\').value, { method: \'post\', update: $(\'mapformat\')}).request();"';
        }
    }else{
        if (substr($joomla_v,0,3) > '3.0') {
            $javascript = 'javascript: jQuery.post( \'index.php?tmpl=component&option=com_joomsport&task=knockoutkn&sid='.$lists['s_id'].'&t_single='.$lists['t_single'].'&fr_id=\'+jQuery(\'#format_post\').val(), function( data ) {jQuery(\'#mapformat\').html( data );});';
        } elseif(substr($joomla_v,0,3) >= '1.6'){
            $javascript = 'javascript:  var myRequest = new Request({url:\'index.php?tmpl=component&option=com_joomsport&task=knockoutkn&sid='.$lists['s_id'].'&t_single='.$lists['t_single'].'&fr_id=\'+$(\'format_post\').value, method: \'post\', onSuccess: function(responseText){$(\'mapformat\').innerHTML = responseText;}}).send()';
        }else{
            $javascript = 'javascript:  var myRequest = new Ajax(\'index3.php?option=com_joomsport&task=knockoutkn&sid='.$lists['s_id'].'&t_single='.$lists['t_single'].'&fr_id=\'+$(\'format_post\').value, { method: \'post\', update: $(\'mapformat\')}).request();"';
        }
    }

	?>
	<?php echo ($lists['t_type'] == 1?JText::_('BLBE_KNOCK'):JText::_('BLBE_DOUBLE'))." ".$lists['format']?> <input class="btn btn-small" type="button" id="btn_format" style="cursor:pointer;" onClick="<?php echo $javascript?>" value="<?php echo JText::_('BLBE_GENERATE');?>" />

<div class="adminlist_kn">
    <div style="width:100%;position:relative;" id="mapformat">
		<?php
		if(count($lists["match"])){
			echo $lists['knock_layout'];
		} ///UPD
		?>
    </div>
</div>

	<?php
}else{

	?>
<br />
<table class="table table-striped" id="new_matches">
    <tr>
        <th class="title" style="padding-left:250px;" colspan="9">
			<?php echo JText::_( 'BLBE_MATCHRESULTS' ); ?>
        </th>
    </tr>
    <tr>
        <th class="title" width="20">
            #
        </th>
        <th class="title" width="170">
			<?php
			echo $lists['t_single']?JText::_( 'BLBE_HOMEPLAYER' ):JText::_( 'BLBE_HOMETEAM' );
			?>
        </th>
        <th class="col-score">
			<?php echo JText::_( 'BLBE_SCORE' ); ?>
        </th>
		<?php if($lists['s_enbl_extra']){?>
        <th class="col-extra-time">
			<?php echo JText::_( 'BLBE_ET' ); ?>
        </th>
		<?php } ?>
        <th class="title" width="170">
			<?php
			echo $lists['t_single']?JText::_( 'BLBE_AWAYPLAYER' ):JText::_( 'BLBE_AWAYTEAM' );
			?>
        </th>
        <th class="col-played">
			<?php echo JText::_( 'BLBE_PLAYED' ); ?>
        </th>

        <th class="col-date">
			<?php echo JText::_( 'BLBE_DATE' ); ?>
        </th>
        <th class="col-time">
			<?php echo JText::_( 'BLBE_TIME' ); ?>
        </th>
        <th class="col-time">
            <?php echo JText::_('BLBE_VENUE'); ?>
        </th>
        <th class="title">
			<?php echo JText::_( 'BLBE_MATCHDETAILS' ); ?>
            <span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_MATCHDETAILS' ); ?>::<?php echo JText::_( 'BLBE_TT_MATCHDETAILS' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
        </th>
    </tr>
	<?php

	if(count($lists["match"])){
		foreach($lists["match"] as $curmatch){
			$match_link = 'index.php?option=com_joomsport&amp;task=match_edit&amp;cid='.$curmatch->id;
			echo "<tr>";
			echo '<td><input type="hidden" name="matchtype[]" value="'.$curmatch->m_single.'" /><input type="hidden" name="match_id[]" value="'.$curmatch->id.'" /><a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="'.JText::_('BLBE_DELETE').'"><img src="components/com_joomsport/img/publish_x.png"  bOrder="0" alt="Delete"></a></td>';
			echo '<td><input type="hidden" name="home_team[]" value="'.$curmatch->team1_id.'" />'.$curmatch->home_team.'</td>';
			echo '<td class="col-score"><input type="text" name="home_score[]" value="'.$curmatch->score1.'" size="3" maxlength="5" style="width:30px;" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" /> : <input type="text" style="width:30px;" name="away_score[]" value="'.$curmatch->score2.'" size="3" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" /></td>';
			if($lists['s_enbl_extra']){
				echo '<td class="col-extra-time"><input type="checkbox" name="extra_time[]" value="'.(($curmatch->is_extra)?1:0).'" '.(($curmatch->is_extra)?"checked":"").' /></td>';
			}
			echo '<td><input type="hidden" name="away_team[]" value="'.$curmatch->team2_id.'" />'.$curmatch->away_team.'</td>';
			echo '<td><input type="checkbox" name="match_played[]" value="'.($curmatch->m_played?1:0).'" '.($curmatch->m_played?"checked":"").' /></td>';
			echo '<td>';
			echo JHTML::_('calendar', $curmatch->m_date, 'match_data[]', 'match_data_'.$curmatch->id, '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'12',  'maxlength'=>'10', 'style'=>'width:70px;'));

			echo '</td>';
			echo '<td><input type="text" style="width:50px;" name="match_time[]" maxlength="5" size="12" value="'.substr($curmatch->m_time,0,5).'" />';
            echo '<td>'.$curmatch->venue_name.'</td>';
			echo '<td><a href="'.$match_link.'">'.JText::_( 'BLBE_MATCHDETAILS' ).'</a></td>';
			echo "</tr>";
		}
	}
	?>
</table>
<table class="table table-striped md-edit-new-match">
    <tr >
        <th  class="title" colspan="9" style=" padding-left:200px;">
			<?php echo JText::_( 'BLBE_ADDMATCHRESULTS' ); ?>
        </th>
    </tr>
	<?php
	$rowsp = "";
	if($lists['s_id'] == -1){
		$rowsp = "rowspan='2'";
	}
	?>
    <tr>
		<?php

		if($lists['s_id'] == -1){
			echo '<th class="col-choose"><input type="radio" name="fr_choose" id="fr_choose_1" onclick="javascript:unbl_frmd(1);" checked /></th>';
		}
		?>
        <th width="205">
			<?php echo $lists['teams1']?>
            <span class="editlinktip hasTip" title="<?php echo $lists['t_single']?JText::_( 'BLBE_HOMEPLAYER' ):JText::_( 'BLBE_HOMETEAM' ); ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>

        </th>
        <th class="col-score" width="100">
            <input name="add_score1" style="width:30px;" id="add_score1" type="text" maxlength="5" size="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />&nbsp;:
            <input name="add_score2" style="width:30px;" id="add_score2" type="text" maxlength="5" size="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />
        </th>
		<?php if($lists['s_enbl_extra']){?>
        <th class="col-extra-time" <?php echo $rowsp;?>>
            <input type="checkbox" name="extra_timez" id="extra_timez" />
            <span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_ET' ); ?>::<?php echo JText::_( 'BLBE_TT_ET' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>

        </th>
		<?php } ?>
        <th width="196">
			<?php echo $lists['teams2']?>
            <span class="editlinktip hasTip" title="<?php echo $lists['t_single']?JText::_( 'BLBE_AWAYPLAYER' ):JText::_( 'BLBE_AWAYTEAM' );  ?>::<?php echo JText::_( '' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
        </th>
        <th class="col-played" <?php echo $rowsp;?> width="60">
            <input type="checkbox" name="tm_played" id="tm_played" />
            <span class="editlinktip hasTip" title="<?php echo JText::_( 'BLBE_PLAYED' ); ?>::<?php echo JText::_( 'BLBE_TT_PLAYED' );?>"><img src="components/com_joomsport/img/quest.jpg" bOrder="0" /></span>
        </th>
        <th width="155" <?php echo $rowsp;?>>
			<?php
			echo JHTML::_('calendar', date("Y-m-d"), 'tm_date', 'tm_date', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'12',  'maxlength'=>'10', 'style'=> 'width:70px;'));
			?>
        </th>
        <th class="col-time" <?php echo $rowsp;?>>
            <input style="width:50px;" type="text" name="match_time_new" id="match_time_new" maxlength="5" size="12" value="00:00" />
        </th>
		<th class="col-time" <?php echo $rowsp;?>>
			<?php echo $lists['venue_name'];?>
		</th>
        <th <?php echo $rowsp;?>>
            <input class="btn btn-small" type="button" style="cursor:pointer;" value="<?php echo JText::_( 'BLBE_ADD' ); ?>" onClick="bl_add_match();" />
        </th>
    </tr>
	<?php
	if($lists['s_id'] == -1){
		echo "<tr>";
		echo '<th class="col-choose"><input type="radio" name="fr_choose" id="fr_choose_2" onclick="unbl_frmd(2);" /></th>';
		echo '<td>'.$lists['plmd'].'</td>';
		?>
        <th class="col-score" colspan="2">
            <input name="add_score1_sg"  style="width:30px;" id="add_score1_sg" type="text" maxlength="5" size="5" />&nbsp;:
            <input name="add_score2_sg"  style="width:30px;" id="add_score2_sg" type="text" maxlength="5" size="5" />
        </th>

		<?php
		echo '<td colspan="5">'.$lists['plmd_away'].'</td>';
		echo "</tr>";

	/*	echo "<script>
						//getObj('plmd').disabled = 'true';
						//getObj('plmd_away').disabled = 'true';
					</script>";*/
	}
	?>
</table>
	<?php } ?>
</div>
<div id="bet_opt_div" class="tabdiv" style="display:none">
    <table class="adminlist">
        <tr>
            <th class="title" width="5%"><?php echo JText::_('BLBE_BET_AVAIL')?></th>
            <th class="title">
                <div style="float:left"><?php echo JText::_('BLBE_HOMETEAM')?></div>
                <div style="float:right"><?php echo JText::_( 'BLBE_AWAYTEAM' ); ?></div>
            </th>
            <th class="title" width="10%">
				<?php echo JText::_( 'BLBE_DATE' ); ?>
            </th>
            <th class="title">
				<?php echo JText::_( 'BLBE_TIME' ); ?>
            </th>
        </tr>
		<?php if(count($lists["match"])):?>
		<?php foreach($lists["match"] as $curmatch):
			$events = empty($curmatch->events) ? array() : $curmatch->events;
			$eventdate = $eventtime = '';
			if (count($events)) {
				$eventdate = $events[0]->betfinishdate;
				$eventtime = $events[0]->betfinishtime;
			}
			$closeCoeffs = $curmatch->m_played?'disabled="true"':'';
			?>
            <tr>
                <td style="vertical-align: top">
                    <input type="checkbox" name="bet_available[]" value="1" onClick="toggleBetEvents(this)" <?php echo $curmatch->betavailable?'checked="true"':''?>/>
                </td>
                <td>
                    <table width="100%">
                        <tr>
                            <td style="text-align:center"><?php echo $curmatch->home_team?></td>
                            <td></td>
                            <td style="text-align:center"><?php echo $curmatch->away_team?></td>
                        </tr>
						<?php $class = (!$curmatch->betavailable)?'style="display:none"':''?>
						<?php if(count($lists["betevents"])):?>
						<?php foreach ($lists['betevents'] as $event):
							$matchevent = $lists['matchbetevents'][$curmatch->id][$event->id];
							?>
                            <tr <?php echo $class?> class="betevents">
                                <td></td>
                                <td style="text-align:left">
									<?php if ($event->type == 'default'):?>
                                    <input <?php echo $closeCoeffs?> type="text" name="bet_coeff_old1[<?php echo $curmatch->id?>][<?php echo $event->id?>]" size="6" value="<?php echo $matchevent->coeff1?$matchevent->coeff1:''?>"/>
                                    <span style="padding-left:10px; padding-right:10px;"><?php echo JText::_($event->name)?></span>
                                    <input <?php echo $closeCoeffs?> type="text"  name="bet_coeff_old2[<?php echo $curmatch->id?>][<?php echo $event->id?>]" size="6" value="<?php echo $matchevent->coeff2?$matchevent->coeff2:''?>"/>
									<?php elseif($event->type == 'simple'):?>
                                    <input <?php echo $closeCoeffs?> type="text" name="bet_coeff_old1[<?php echo $curmatch->id?>][<?php echo $event->id?>]" size="6" value="<?php echo $matchevent->coeff1?$matchevent->coeff1:''?>"/>
									<?php echo JText::_($event->name)?>
									<?php else:?>
                                    <input <?php echo $closeCoeffs?> type="text" name="bet_coeff_old1[<?php echo $curmatch->id?>][<?php echo $event->id?>]" size="6" value="<?php echo $matchevent->coeff1?$matchevent->coeff1:''?>"/>
									<?php echo $event->difffrom?> < DIFF < <?php echo $event->diffto?>
									<?php endif;?>
                                </td>
                                <td></td>
                            </tr>
							<?php endforeach;?>
						<?php endif;?>
                    </table>
                </td>
                <td width="10%" style="vertical-align: top">
					<?php
					echo JHTML::_('calendar', $eventdate, 'betfinishdate[]', 'betfinishdate_'.$curmatch->id, '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'12',  'maxlength'=>'10'));
					?>
                </td>
                <td style="vertical-align: top">
                    <input type="text" name="betfinishtime[]" maxlength="5" size="12" value="<?php echo substr($eventtime,0,5)?>" />
                </td>
            </tr>
			<?php endforeach;?>
		<?php endif;?>

    </table>
</div>


<input type="hidden" name="t_single" value="<?php echo $lists['t_single']?>" />
<input type="hidden" name="t_knock" value="<?php echo $lists['t_type']?>" />
<input type="hidden" name="task" value="matchday_list" />
<input type="hidden" name="id" value="<?php echo $row->id?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="s_id" value="<?php echo $lists['s_id'];?>" />
<input type="hidden" name="jscurtab" id="jscurtab" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>