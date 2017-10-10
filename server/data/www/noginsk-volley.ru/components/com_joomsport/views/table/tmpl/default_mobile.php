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
defined('_JEXEC') or die('Restricted access');
?>
<?php
if (isset($this->message)) {
    $this->display('message');
}
$lists = $this->lists;
$Itemid = JRequest::getInt('Itemid');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
?>
<?php
if ($this->tmpl != 'component') {
    $lnk = "
        var arr = document.getElementsByClassName('joomsporttab');
        var tabName = '';
        for(var i =0; i<arr.length;i++){
                if(arr[i].className.indexOf('active')!= '-1'){
                        tabName=arr[i].id;
                        document.getElementById(tabName+'_div').style.display='block';break;
                }
        }	
	window.open('" . JURI::base() . "index.php?tmpl=component&option=com_joomsport&amp;view=table&amp;sid=" . $lists["s_id"] . "&amp;tab_n='+tabName,'jsmywindow','width=700,height=700');
    ";
} else {
    $lnk = "window.print();";
}
?>

<div class="page-content">
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
        <?php echo $lists["panel"]; ?>
    </nav>
    <!-- /.navbar -->
    <div class="main seasonTable">
        <div class="history col-xs-12 col-lg-12">
            <div class="socialMediaCont pull-right">                 
                <div class="addthis_native_toolbox"><?php echo $this->lists['socbut']; ?></div>
            </div>
        </div>
        <div class="itemHeading col-xs-12 col-lg-12"> 
            <a class="print pull-right" href="#" onclick="<?php echo $lnk; ?>" title="<?php echo JText::_("BLFA_PRINT"); ?>"><i class="fa fa-print"></i><?php echo JText::_("BLFA_PRINT"); ?></a>            
            <h1><?php echo $this->escape($this->ptitle); ?></h1>
        </div>
        <div class="itemDetails col-xs-12 col-lg-12">
            <div class="well well-sm col-xs-12 col-sm-6 col-md-3 col-lg-2">
                <?php
                $tLogo = '';
                if ($lists["curseas"]->logo && is_file('media/bearleague/' . $lists["curseas"]->logo)) {
                    $tLogo = "<img class='img-responsive img-thumbnail' " . getImgPop($lists["curseas"]->logo, 4) . " alt='" . $this->params->get('page_title') . "' />";
                }
                if ($lists['ext_fields'] || $tLogo) {
                    echo $tLogo;
                    echo $lists['ext_fields'];
                }
                ?>            
            </div>
            <p class="txt-cnt"></p>            
        </div>
        <div class="itemTable col-xs-12 col-lg-12"> 
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <?php if ($lists['is_group_matches'] || !$lists['is_matches']) { ?>
                    <li role="presentation" class="active"><a href="#sTable" role="tab" data-toggle="tab"><i class="hidden-xs tableS"></i><?php echo JText::_('BL_TAB_TBL') ?></a></li>
                <?php } if ($lists['season_par']->s_rules) { ?>
                    <li role="presentation"><a href="#seasonsRules" role="tab" data-toggle="tab"><i class="hidden-xs flag"></i><?php echo JText::_('BL_TAB_RULES') ?></a></li>
                <?php } if ($lists['season_par']->s_descr) { ?>
                    <li role="presentation"><a href="#seasonsProfile" role="tab" data-toggle="tab"><i class="hidden-xs flag"></i><?php echo JText::_('BL_TAB_ABOUTSEAS') ?></a></li>
                <?php } ?>
            </ul>
            <!-- Tab panels -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="sTable">
                    <?php
                    if ($lists['is_group_matches'] || !$lists['is_matches']) {
                        for ($zzz = 0; $zzz < count($lists["groups"]); $zzz++) {
                            $show = false;
                            (isset($lists["gr_id"])) ? ('') : ($lists["gr_id"] = ''); //UPDATE
                            if (!$lists["gr_id"] || $lists["gr_id"] == $lists["groups"][$zzz]) {
                                $show = true;
                            }

                            if (!$lists["enbl_gr"]) {
                                $show = true;
                            }
                            if ($show) {
                                if (isset($lists["groups_name"][$zzz])) {
                                    echo '<h2 class="dotted">' . $lists["groups_name"][$zzz] . "</h2>";
                                }
                                ?>
                                <div class="table-responsive col-xs-12 col-lg-12">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th><a href="#"><?php echo JText::_('BL_TBL_RANK'); ?> <i class="fa fa-caret-up"></i></a></th>
                                                <th class="left"><a href="#"><?php echo $lists["t_single"] ? JText::_('BL_PARTICS') : JText::_('BL_TBL_TEAMS'); ?> <i class="fa fa-caret-up"></i></a></th>                      
                                                <?php
                                                if (count($lists["soptions"])) {
                                                    foreach ($lists["soptions"] as $key => $value) {
                                                        if ($key != 'emblem_chk') {
                                                            if ($key == 'otwin_chk' || $key == 'otlost_chk') {
                                                                if ($lists["enbl_extra"] == 1) {
                                                                    ?>
                                                                    <th><a href="#"><?php echo $lists["available_options"][$key]; ?> <i class="fa fa-caret-up"></i></a></th>                                                        
                                                                    <?php
                                                                }
                                                            } elseif ($key == 'grwinpr_chk') {
                                                                ?>
                                                                <th><a href="#"><?php echo $lists["available_options"][$key]; ?><i class="fa fa-caret-up"></i></a></th>                                                   
                                                                <?php
                                                            } else {
                                                                if ($key == 'grwin_chk' || $key == 'grlost_chk') {
                                                                    if ($lists["enbl_gr"] == 1) {
                                                                        ?>
                                                                        <th><a href="#"><?php echo $lists["available_options"][$key]; ?><i class="fa fa-caret-up"></i></a></th>                                                            
                                                                        <?php
                                                                    }
                                                                } else {
                                                                    ?>
                                                                    <th><a href="#"><?php echo $lists["available_options"][$key]; ?><i class="fa fa-caret-up"></i></a></th>                                                        
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                $num_ext = array();
                                                for ($i = 0; $i < count($lists["ext_fields_name"]); $i++) {
                                                    $var_ex = 0;

                                                    for ($j = 0; $j < count($lists['v_table']); $j++) {
                                                        if (!empty($lists['v_table'][$j]['ext_fields'][$i])) {

                                                            $var_ex = 1;
                                                            $num_ext[$i] = $i;
                                                        }
                                                    }
                                                    if ($var_ex) {
                                                        ?>
                                                        <th><a href="#"><?php echo $lists["ext_fields_name"][$i]->name; ?><i class="fa fa-caret-up"></i></a></th>                                          
                                                        <?php
                                                    } else {
                                                        echo "<th></th>";
                                                    }
                                                }
                                                ?>
                                            </tr>
                                        </thead>                            
                                        <tbody>
                                            <?php
                                            $ranks = 1;
                                            for ($i = 0; $i < count($lists["v_table"]); $i++) {
                                                $team = $lists["v_table"][$i];
                                                $color = '';
                                                if (isset($lists["colors"][$ranks])) {
                                                    $color = 'style="background-color:' . $lists["colors"][$ranks] . '"';
                                                }
                                                if ($team['yteam']) {
                                                    $color = 'style="background-color:' . $team['yteam'] . '"';
                                                }

                                                if ($team['g_id'] == $lists["groups"][$zzz]) {
                                                    ?>
                                                    <tr class="<?php echo $ranks % 2 ? "active" : ""; ?>" <?php echo $color ?>>
                                                        <td><?php echo $ranks ?></td>
                                                        <?php
                                                        $teamembl = '';
                                                        $st = '';
                                                        if (isset($lists["soptions"]['emblem_chk']) && $lists["soptions"]['emblem_chk'] == 1) {
                                                            if ($lists["t_single"]) {
                                                                $teamembl = JHtml::_('images.getPlayerThumb', $team['tid'], 0, $team['name'], 0, ($lists['teamlogo_height'] ? $lists['teamlogo_height'] : 29));
                                                                $st = 'display: table-cell;padding-left:8px;vertical-align:middle;';
                                                            } else {
                                                                $teamembl = JHtml::_('images.getTeamEmbl', $team['tid'], 1, $team['name'], 0, ($lists['teamlogo_height'] ? $lists['teamlogo_height'] : 29));
                                                                $st = 'display: table-cell;padding-left:8px;vertical-align:middle;';
                                                            }
                                                        }
                                                        if ($lists["t_single"]) {
                                                            $link = JRoute::_('index.php?option=com_joomsport&task=player&id=' . $team['tid'] . '&sid=' . $lists["s_id"] . '&Itemid=' . $Itemid);
                                                        } else {
                                                            $link = JRoute::_('index.php?option=com_joomsport&task=team&tid=' . $team['tid'] . '&sid=' . $lists["s_id"] . '&Itemid=' . $Itemid);
                                                        }
                                                        ?>
                                                        <td class="left">
                                                            <?php echo $teamembl; ?>                                                
                                                            <a href="<?php echo $link; ?>"><?php echo $team['name'] ?></a>
                                                        </td>

                                                        <?php
                                                        if (count($lists["soptions"])) {
                                                            foreach ($lists["soptions"] as $key => $value) {
                                                                if ($key != 'emblem_chk') {
                                                                    if ($key == 'otwin_chk' || $key == 'otlost_chk') {
                                                                        if ($lists["enbl_extra"] == 1) {
                                                                            ?>
                                                                            <td><?php echo $team[$key]; ?></td>
                                                                            <?php
                                                                        }
                                                                    } elseif ($key == 'percent_chk') {
                                                                        ?>
                                                                        <td><?php echo ($team[$key] == 1) ? 1.000 : substr(sprintf("%.3f", round($team[$key], 3)), 1); ?></td>
                                                                        <?php
                                                                    } else {
                                                                        if ($key == 'grwin_chk' || $key == 'grlost_chk') {
                                                                            if ($lists["enbl_gr"] == 1) {
                                                                                ?>
                                                                                <td><?php echo $team[$key]; ?></td>
                                                                                <?php
                                                                            }
                                                                        } else {
                                                                            ?>
                                                                            <td><?php echo $team[$key]; ?></td>
                                                                            <?php
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        for ($j = 0; $j < count($this->lists["ext_fields_name"]); $j++) {
                                                            $value = isset($team['ext_fields'][$j]) ? ($team['ext_fields'][$j]) : ("&nbsp;");
                                                            echo "<td >" . $value . "</td>";
                                                        }
                                                        ?>
                                                    </tr>
                                                    <?php
                                                    $ranks++;
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php
                            }
                            if ($lists["enbl_gr"]) {
                                echo isset($lists["bonus_not"][$lists["groups"][$zzz]]) ? ('<div class="js_botbonusp">' . JText::_('BLFA_SEASBONUSTABLE') . '</div>' . $lists["bonus_not"][$lists["groups"][$zzz]]) : '';
                            } else {
                                if (count($lists["bonus_not"])) {
                                    echo '<div class="js_botbonusp">' . JText::_('BLFA_SEASBONUSTABLE') . '</div>';
                                    foreach ($lists["bonus_not"] as $bons) {
                                        echo $bons;
                                    }
                                }
                            }
                        }
                        if (!count($lists["groups"])) {
                            echo JText::_('BLFA_SGROUPSNOTABLE');
                        }

                        if (count($lists["playoffs"])) {
                            $prev_mday = 0;
                            for ($i = 0; $i < count($lists["playoffs"]); $i++) {
                                $playoff_match = $lists["playoffs"][$i];
                                if ($playoff_match->m_id != $prev_mday) {
                                    if ($i) {
                                        echo "</tbody></table>";
                                    }?>                                    
                                    <h2 class="dotted"><?php echo $playoff_match->m_name; ?></h2>
                                    <table class="table table-striped">
                                        <tbody>
                                    <?php
                                    $prev_mday = $playoff_match->m_id;
                                }
                                ?>
                                <tr class="active">
                                    <td class="text-right">
                                        <?php echo $playoff_match->home; ?>                                        
                                        <?php
                                        if (!$this->lists['t_single'] && isset($playoff_match->home)) {
                                            echo JHtml::_('images.getTeamEmbl', $playoff_match->team1_id, 1, $playoff_match->home);
                                        }
                                        ?>                                
                                    </td>
                                    <td style="width:80px;">
                                        <div class="score">
                                            <?php if ($playoff_match->m_played == 1) { 
                                                $lnks =  JRoute::_('index.php?option=com_joomsport&task=view_match&id='.$playoff_match->id.'&Itemid='.$Itemid);
                                                ?>
                                                <a href="<?php echo $lnks?>" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="">
                                                    <?php echo $playoff_match->score1; ?> : <?php echo $playoff_match->score2; ?>
                                                </a>
                                            <?php } else { ?>
                                                <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="">
                                                    - : -
                                                </button>
                                            <?php } ?>
                                            <?php if(@$lists["enbl_extra"] && $playoff_match->is_extra){?>
                                            <span class="ET" title="<?php echo JText::_('BLFA_TEAM_WON_ET');?>"><?php echo JText::_('BL_RES_EXTRA');?></span> 
                                            <span class="label label-default"><?php echo $playoff_match->aet1." : ".$playoff_match->aet2?></span>
                                            <?php }?>
                                        </div>
                                    </td>
                                    <td class="text-left">
                                        <?php
                                        if (!$this->lists['t_single'] && isset($playoff_match->away)) {
                                            echo JHtml::_('images.getTeamEmbl', $playoff_match->team2_id, 1, $playoff_match->away);
                                        }
                                        ?> 
                                        <?php echo $playoff_match->away; ?>
                                    </td>
                                    <td>
                                        <?php if ($lists["locven"]) { ?>
                                            <?php echo getJS_Location($playoff_match->id); ?>
                                        <?php } ?>                                
                                    </td>
                                </tr>                               
                            <?php } ?>
                            </tbody></table>
                            <?php
                        }
                    }
                    if($this->lists['knock_layout']){ 
                        $doc = JFactory::getDocument();
                        $doc->addCustomTag('<link rel="stylesheet" type="text/css"  href="components/com_joomsport/css/mobile/drawBracket.css" />');
                    ?>
                    <div class="drawBracket">
                        <div class="drawBracketContainer">
                            <div class="table-responsive col-xs-12 col-lg-12">
                            <?php
                            echo $this->lists['knock_layout'];
                            ?>
                            </div>
                        </div>
                    </div>
                    <?php }
                    ?>                    
                </div>
                <div role="tabpanel" class="tab-pane" id="seasonsRules">
                    <p class="txt-cnt"><?php echo $lists["season_par"]->s_rules; ?></p>
                </div>
                <div role="tabpanel" class="tab-pane" id="seasonsProfile">
                    <?php
                    if ($lists['season_par']->s_descr):
                        JPluginHelper::importPlugin('content');
                        $dispatcher = JDispatcher::getInstance();
                        $results = @$dispatcher->trigger('onContentPrepare', array('content'));
                        $lists['season_par']->s_descr = JHTML::_('content.prepare', $lists['season_par']->s_descr);
                        ?> 
                        <p class="txt-cnt"><?php echo $lists['season_par']->s_descr; ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
