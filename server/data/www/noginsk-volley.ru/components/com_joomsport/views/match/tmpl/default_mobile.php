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

$Itemid = JRequest::getInt('Itemid');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
$lists = $this->lists;
$match = $this->lists["match"];
?>
<script type="text/javascript">
    function delCom(num) {
        jQuery.post(
                'index.php?tmpl=component&option=com_joomsport&task=del_comment&no_html=1&cid=' + num,
                function(result) {
                    if (result) {
                        alert(result);
                    } else {
                        var d = document.getElementById('divcomb_' + num).parentNode;
                        d.removeChild(jQuery('#divcomb_' + num).get(0));
                    }
                });

    }
    function resetPoints(el) {
        if (jQuery(el).is(':checked')) {
            jQuery('input', jQuery(el).parent()).removeAttr('disabled');
        } else {
            jQuery('input', jQuery(el).parent()).attr('disabled', 'disabled');
        }
    }
</script>

<div class="page-content">
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
        <?php echo $lists["panel"]; ?>
    </nav>
    <!-- /.navbar -->
    <div class="main viewMatch matchDay">
        <div class="history col-xs-12 col-lg-12">
            <ol class="breadcrumb pull-left">
                <li><a href="#" onclick="history.back(-1);"><i class="fa fa-long-arrow-left"></i><?php echo JText::_("BL_BACK") ?></a></li>
            </ol>
            <div class="socialMediaCont pull-right"> 
                <!-- Go to www.addthis.com/dashboard to customize your tools -->
                <div class="addthis_native_toolbox"><?php echo $this->lists['socbut']; ?></div>
            </div>
        </div>
        <div class="heading col-xs-12 col-lg-12">
            <h1 class="text-center"> <span itemprop="name"><?php
                    if ($match->m_date) {
                        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $match->m_date)) {
                            echo $this->formatDate($match->m_date . ' ' . $match->m_time);
                        } else {
                            echo $match->m_date;
                        }
                    }
                    ?></span></h1>

            <?php
            if ($match->m_location || $match->venue_id) {
                echo '<h1 class="text-center"> <span itemprop="name">';
                echo getJS_Location($match->id);
                echo '</span></h1>';
            }
            ?>
        </div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#match" role="tab" data-toggle="tab"><i class="flag"></i><?php echo JText::_('BL_TAB_MATCH') ?></a></li>
            <?php
            $how_rowst_k = (count($this->lists['squard1']) > count($this->lists['squard2'])) ? count($this->lists['squard1']) : count($this->lists['squard2']);
            if ($how_rowst_k) {
                ?>
                <li role="presentation"><a href="#squad" role="tab" data-toggle="tab"><i class="squad"></i><?php echo JText::_('BL_TAB_SQUAD') ?></a></li>						
            <?php } if ($match->match_descr) { ?>
                <li role="presentation"><a href="#about" role="tab" data-toggle="tab"><i class="star"></i><?php echo JText::_('BL_TAB_ABOUT') ?></a></li>
            <?php } if (count($this->lists["photos"])) { ?>
                <li role="presentation"><a href="#gallery" role="tab" data-toggle="tab"><i class="photos"></i><?php echo JText::_('BL_TAB_PHOTOS') ?></a></li>
            <?php } ?>
        </ul>
        <!-- Tab panels -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="match">
                <div class="table-responsive col-xs-12 col-lg-12 results">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="right"><h3><?php echo $match->team1_id > 0 ? $match->home : ($match->team1_id == -1 ? 'BYE' : '&nbsp;'); ?></h3></td>
                                <td class="text-right">
                                    <?php
                                    if (!$this->lists['t_single']) {
                                        echo JHtml::_('images.getTeamEmbl', $match->team1_id, 1, $match->home, 0, 29, 'ml20');
                                    } else { //player
                                        echo JHtml::_('images.getPlayerThumb', $match->team1_id, 0, $match->home, 0, 29, 'ml20');
                                    }
                                    ?>
                                </td>
                                <td class="scoreBig"><span class="label label-default"><?php echo ($match->m_played ? $match->score1 : '-') ?> : <?php echo ($match->m_played ? $match->score2 : '-'); ?></span></td>
                                <td class="text-left">
                                    <?php
                                    if (!$this->lists['t_single']) {
                                        echo JHtml::_('images.getTeamEmbl', $match->team2_id, 1, $match->away, 0, 29, 'mr20');
                                    } else {
                                        echo JHtml::_('images.getPlayerThumb', $match->team2_id, 0, $match->away, 0, 29, 'mr20');
                                    }
                                    ?>
                                </td>
                                <td class="left"><h3><?php echo $match->team2_id > 0 ? $match->away : ($match->team2_id == -1 ? 'BYE' : '&nbsp;'); ?></h3></td>
                            </tr>
                            <?php
                            if ($lists["t_type"] == 1 && $match->is_extra && ($match->p_winner || $match->aet1 == $match->aet2 || $match->aet1 != $match->aet2) && $match->m_played) {
                                ?>
                                <tr>
                                    <td class="text-right"><strong><?php echo JText::_('BLFA_ET') ?></strong></td>
                                    <td></td>
                                    <td class="score"><span class="label label-default"><?php echo $match->aet1; ?> : <?php echo $match->aet2; ?></span></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php
                $prev_id = 0;
                $ev_count = (count($this->lists["m_events_home"]) > count($this->lists["m_events_away"])) ? (count($this->lists["m_events_home"])) : (count($this->lists["m_events_away"]));
                if ($ev_count) {
                    ?>
                    <div class="table-responsive col-xs-12 col-lg-12 stats">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th colspan="4"><i><?php echo JText::_('BL_PBL_STAT') ?></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < $ev_count; $i++) { ?>
                                    <tr>
                                        <?php
                                        if (isset($this->lists["m_events_home"][$i])) {
                                            echo '<td class="home_event" width="40%">';
                                            if ($this->lists["m_events_home"][$i]->e_img && is_file('media/bearleague/events/' . $this->lists["m_events_home"][$i]->e_img)) {
                                                echo '<img ' . getImgPop($this->lists["m_events_home"][$i]->e_img, 6) . '  title="' . $this->lists["m_events_home"][$i]->e_name . '" alt="' . $this->lists["m_events_home"][$i]->e_name . '" />';
                                            } else {
                                                echo "<span class='js_event_name'>" . $this->lists["m_events_home"][$i]->e_name . "</span>";
                                            }
                                            if (!$this->lists['t_single']) {
                                                echo "&nbsp;&nbsp;" . $this->lists["m_events_home"][$i]->p_name;
                                            }
                                            echo '</td>';
                                            ?>
                                            <td class="home_event_count" width="5%">
                                                <?php
                                                if ($this->lists["m_events_home"][$i]->ecount) {
                                                    echo $this->lists["m_events_home"][$i]->ecount;
                                                }
                                                else
                                                    echo "0";
                                                ?>
                                            </td>
                                            <td class="home_event_minute" width="3%" style="padding-right:35px;">
                                                <?php
                                                if ($this->lists["m_events_home"][$i]->minutes) {
                                                    echo $this->lists["m_events_home"][$i]->minutes . "'";
                                                }
                                                else
                                                    echo "&nbsp;";
                                                ?>
                                            </td>
                                            <?php
                                        }else {
                                            echo '<td style="padding:0px" colspan="3">&nbsp;</td>';
                                        }
                                        if (isset($this->lists["m_events_away"][$i])) {
                                            echo '<td class="away_event" width="40%" style="padding-left:35px;">';
                                            if (isset($this->lists["m_events_away"][$i]->e_img) && $this->lists["m_events_away"][$i]->e_img && is_file('media/bearleague/events/' . $this->lists["m_events_away"][$i]->e_img)) {
                                                echo '<img ' . getImgPop($this->lists["m_events_away"][$i]->e_img, 6) . '  title="' . $this->lists["m_events_away"][$i]->e_name . '" alt="' . $this->lists["m_events_away"][$i]->e_name . '" />';
                                            } else {
                                                echo "<span class='js_event_name'>" . $this->lists["m_events_away"][$i]->e_name . "</span>";
                                            }
                                            if (!$this->lists['t_single']) {
                                                echo "&nbsp;&nbsp;" . $this->lists["m_events_away"][$i]->p_name;
                                            }
                                            echo '</td>';
                                            ?>
                                            <td class="away_event_count" width="5%">
                                                <?php
                                                if ($this->lists["m_events_away"][$i]->ecount) {
                                                    echo $this->lists["m_events_away"][$i]->ecount;
                                                }
                                                else
                                                    echo "0";
                                                ?>
                                            </td>
                                            <td class="away_event_minute" width="3%">
                                                <?php
                                                if ($this->lists["m_events_away"][$i]->minutes) {
                                                    echo $this->lists["m_events_away"][$i]->minutes . "'";
                                                }
                                                else
                                                    echo "&nbsp;";
                                                ?>
                                            </td>

                                            <?php
                                        }else {
                                            echo '<td style="padding:0px" colspan="3">&nbsp;</td>';
                                        }
                                        ?>
                                    </tr>
                                <?php } ?>                           
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="squad">
                <div class="table-responsive col-xs-12 col-lg-12" style="border:none;">
                    <table class="table table-striped teams">
                        <thead>
                            <tr>
                                <th class="text-center"><h4><?php echo $match->home; ?></h4></th>
                        <th class="text-center"><h4><?php echo $match->away; ?></h4></th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="table-responsive col-xs-12 col-lg-12" style="border:none;">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th colspan="2"><i><?php echo JText::_('BLFA_LINEUP'); ?></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $how_rows = (count($lists['squard1']) > count($lists['squard2'])) ? count($lists['squard1']) : count($lists['squard2']);
                            if ($how_rows) {

                                for ($p = 0; $p < $how_rows; $p++) {
                                    echo "<tr>";
                                    echo "<td>" . (isset($this->lists['squard1'][$p]->name) ? $this->lists['squard1'][$p]->photo . "" . $this->lists['squard1'][$p]->name : "") . "</td>";
                                    echo "<td>" . (isset($this->lists['squard2'][$p]->name) ? $this->lists['squard2'][$p]->photo . "" . $this->lists['squard2'][$p]->name : "") . "</td>";
                                    echo "</tr>";
                                }
                            }
                            ?>                            
                        </tbody>
                    </table>
                </div>
                <?php
                $how_rows = (count($this->lists['squard1_res']) > count($this->lists['squard2_res'])) ? count($this->lists['squard1_res']) : count($this->lists['squard2_res']);
                if ($how_rows) {
                    ?>
                    <div class="table-responsive col-xs-12 col-lg-12">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th colspan="2"><i><?php echo JText::_('BLFA_SUBSTITUTES') ?></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                for ($p = 0; $p < $how_rows; $p++) {
                                    echo "<tr>";
                                    echo "<td>" . (( isset($this->lists['squard1_res'][$p]->name) && $this->lists['squard1_res'][$p]->name) ? $this->lists['squard1_res'][$p]->photo . "" . $this->lists['squard1_res'][$p]->name . "" : "&nbsp;") . "</td>";
                                    echo "<td>" . ((isset($this->lists['squard2_res'][$p]->name) && $this->lists['squard2_res'][$p]->name) ? $this->lists['squard2_res'][$p]->photo . "" . $this->lists['squard2_res'][$p]->name . "" : "&nbsp;") . "</td>";
                                    echo "</tr>";
                                }
                                ?>                             
                            </tbody>
                        </table>
                    </div>
                    <?php
                }
                $how_rows = (count($this->lists['subsin1']) > count($this->lists['subsin2'])) ? count($this->lists['subsin1']) : count($this->lists['subsin2']);
                if ($how_rows) {
                    ?>
                    <div class="table-responsive col-xs-12 col-lg-12">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th colspan="2"><i><?php echo JText::_('BLFA_SUBSIN') ?></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($p = 0; $p < $how_rows; $p++) { ?>
                                    <tr>
                                        <td> 
                                            <table class="table table-striped">
                                                <tbody>                                            
                                                    <?php
                                                    echo '<tr><td>';
                                                    echo (isset($this->lists['subsin1'][$p]->plin) && $this->lists['subsin1'][$p]->plin) ? '<i class="redo"></i>' . "" . $this->lists['subsin1'][$p]->plin . "" : "&nbsp;";
                                                    echo '</td></tr>';
                                                    echo '<tr><td>';
                                                    echo (isset($this->lists['subsin1'][$p]->plout) && $this->lists['subsin1'][$p]->plout) ? '<i class="undo"></i>' . "" . $this->lists['subsin1'][$p]->plout . "" : "&nbsp;";
                                                    echo '</td></tr>';
                                                    ?>                                            
                                                </tbody>                                        
                                            </table>
                                        </td>
                                        <td> 
                                            <table class="table table-striped">
                                                <tbody>                                            
                                                    <?php
                                                    echo '<tr><td>';
                                                    echo (isset($this->lists['subsin2'][$p]->plin) && $this->lists['subsin2'][$p]->plin) ? '<i class="redo"></i>' . "" . $this->lists['subsin2'][$p]->plin . "" : "&nbsp;";
                                                    echo '</td></tr>';
                                                    echo '<tr><td>';
                                                    echo (isset($this->lists['subsin2'][$p]->plout) && $this->lists['subsin2'][$p]->plout) ? '<i class="undo"></i>' . "" . $this->lists['subsin2'][$p]->plout . "" : "&nbsp;";
                                                    echo '</td></tr>';
                                                    ?>                                            
                                                </tbody>                                        
                                            </table>
                                        </td>
                                    </tr>
                                <?php } ?>                            
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="gallery">
                <div class="col-xs-12 col-lg-12">
                    <?php
                    if (count($this->lists["photos"])) {
                        echo JHtml::_('images.getGalleryHTML', $this->lists["photos"]);
                    }
                    ?>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="about">
                <div class="col-xs-12 col-lg-12">
                    <?php
                    if ($match->match_descr) {
                        JPluginHelper::importPlugin('content');
                        $dispatcher = JDispatcher::getInstance();
                        $results = @$dispatcher->trigger('onContentPrepare', array('content'));
                        $match->match_descr = JHTML::_('content.prepare', $match->match_descr);
                        echo $match->match_descr;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>