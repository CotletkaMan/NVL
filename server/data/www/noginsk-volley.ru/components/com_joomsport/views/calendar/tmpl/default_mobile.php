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
if (isset($this->message)) {
    $this->display('message');
}
$lists = $this->lists;
$Itemid = JRequest::getInt('Itemid');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
?>
<?php
if ($this->tmpl != 'component') {
    $lnk = "window.open('" . JURI::base() . "index.php?tmpl=component&option=com_joomsport&amp;view=calendar&amp;sid=" . $lists["s_id"] . "','jsmywindow','width=750,height=700,scrollbars=1,resizable=1');";
} else {
    $lnk = "window.print();";
}
?>
<div class="page-content">
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
        <?php
        if ($this->tmpl != 'component') {
            echo $this->lists["panel"];
        }
        ?>
    </nav>
    <!-- /.navbar -->
    <form name="adminForm" id="adminForm" action="<?php echo JRoute::_("index.php?option=com_joomsport&view=calendar&sid=" . $lists["s_id"] . "&Itemid=" . $Itemid . "&jslimit=" . $this->page->limit . "&page=1" . ($this->tmpl == 'component' ? "&tmpl=component" : "")); ?>" method="post">
        <div class="main">
            <div class="history col-xs-12 col-lg-12">
                <ol class="breadcrumb">
                    <li><a href="#" onclick="history.back(-1);
                            return false;" title="<?php echo JText::_("BL_BACK") ?>"><i class="fa fa-long-arrow-left"></i><?php echo JText::_("BL_BACK") ?></a></li>
                </ol>
            </div>
            <div class="seasoncalendar col-xs-12 col-lg-12">
                <div class="heading col-xs-12 col-lg-12"> <a href="#" class="print pull-right"><i class="fa fa-print"></i><?php echo JText::_("BLFA_PRINT"); ?></a>
                    <h1><?php echo $this->escape($this->ptitle); ?></h1>
                </div>
                <div class="searchBar col-xs-12 col-lg-12">
                    <form role="form">
                        <div class="form-group srcTeam col-xs-12 col-sm-6 col-md-6 col-lg-4">
                            <label for="team"><?php echo JText::_("BLFA_TEAM"); ?></label>
                            <?php echo $this->lists['teams']; ?>
                            <?php echo $this->lists['teamhm']; ?>
                        </div>
                        <div class="form-group srcDay col-xs-12 col-sm-6 col-md-6 col-lg-2">
                            <label for="matchDay"><?php echo JText::_("BLFA_MATCHDAY"); ?></label>
                            <?php echo $this->lists['mdays']; ?>
                        </div>
                        <div class="form-group srcDate col-xs-12 col-sm-12 col-md-6 col-lg-5">
                            <label for="date"><?php echo JText::_("BLFA_DATE"); ?></label>
                            <input class="form-control  pull-right" id="date" placeholder="" type="date">
                            <input class="form-control  pull-right" id="date" placeholder="" type="date">
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-1">
                            <button type="submit" class="btn btn-primary pull-right"><?php echo JText::_("BLFA_SEARCH"); ?></button>
                        </div>
                    </form>
                </div>

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <?php
                    $old_md = 0;
                    for ($i = 0; $i < count($lists["matchs"]); $i++) {
                        $match = $lists["matchs"][$i];

                        if ($old_md != $match->mdid) {
                            echo '<li role="presentation" class="' . (!$old_md ? 'active' : '') . '"><a href="#matchday' . $match->mdid . '" role="tab" data-toggle="tab"><i class="tableS"></i>' . $match->m_name . '</a></li>';
                        }
                        $old_md = $match->mdid;
                    }
                    ?>
                </ul>
                <!-- Tab panels -->
                <div class="tab-content">
                    <?php
                    $old_md = 0;
                    for ($i = 0; $i < count($lists["matchs"]); $i++) {
                        $match = $lists["matchs"][$i];

                        if ($old_md != $match->mdid) {
                            if ($old_md) {
                                echo '</tbody></table></div></div>';
                            }
                            echo '<div role="tabpanel" class="tab-pane ' . (!$old_md ? 'active' : '') . '" id="matchday' . $match->mdid . '">
                                    <div class="table-responsive col-xs-12 col-lg-12">
                                        <table class="table table-striped">
                                            <tbody>';
                        }
                        $old_md = $match->mdid;
                        ?>
                        <tr class="active">
                            <td>
                                <?php
                                if ($match->m_date) {
                                    if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $match->m_date)) {
                                        echo $this->formatDate($match->m_date . ' ' . $match->m_time);
                                    } else {
                                        echo $match->m_date;
                                    }
                                }
                                ?>
                            </td>
                            <td class="text-right">
                                <?php
                                if (isset($match->hm_id)) {
                                    if ($lists["t_single"]) {
                                        $link = JRoute::_('index.php?option=com_joomsport&task=player&id=' . $match->hm_id . '&sid=' . $lists["s_id"] . '&Itemid=' . $Itemid);
                                    } else {
                                        $link = JRoute::_('index.php?option=com_joomsport&task=team&tid=' . $match->hm_id . '&sid=' . $lists["s_id"] . '&Itemid=' . $Itemid);
                                    }
                                    ?>
                                    <a class="mr10" href="<?php echo $link; ?>"><?php echo $match->home; ?></a>
                                <?php } ?>
                                <?php
                                if (!$this->lists['t_single'] && isset($match->home)) {
                                    echo JHtml::_('images.getTeamEmbl', $match->team1_id, 1, $match->home);
                                }
                                ?>                                
                            </td>
                            <td style="width:80px;">
                                <div class="score">
                                    <?php if ($match->m_played == 1) { 
                                        $lnks =  JRoute::_('index.php?option=com_joomsport&task=view_match&id='.$match->id.'&Itemid='.$Itemid);
                                        ?>
                                        <a href="<?php echo $lnks?>" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="">
                                            <?php echo $match->score1; ?> : <?php echo $match->score2; ?>
                                        </a>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="">
                                            - : -
                                        </button>
                                    <?php } ?>
                                    <?php if(@$lists["enbl_extra"] && $match->is_extra){?>
                                    <span class="ET" title="<?php echo JText::_('BLFA_TEAM_WON_ET');?>"><?php echo JText::_('BL_RES_EXTRA');?></span> 
                                    <span class="label label-default"><?php echo $match->aet1." : ".$match->aet2?></span>
                                    <?php }?>
                                </div>
                            </td>
                            <td class="text-left">
                                <?php
                                if (!$this->lists['t_single'] && isset($match->away)) {
                                    echo JHtml::_('images.getTeamEmbl', $match->team2_id, 1, $match->away);
                                }
                                ?> 
                                <?php
                                if (isset($match->aw_id)) {
                                    if ($lists["t_single"]) {
                                        $link = JRoute::_('index.php?option=com_joomsport&task=player&id=' . $match->aw_id . '&sid=' . $lists["s_id"] . '&Itemid=' . $Itemid);
                                    } else {
                                        $link = JRoute::_('index.php?option=com_joomsport&task=team&tid=' . $match->aw_id . '&sid=' . $lists["s_id"] . '&Itemid=' . $Itemid);
                                    }
                                    ?>
                                    <a class="mr10" href="<?php echo $link; ?>"><?php echo $match->away; ?></a>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($lists["locven"]) { ?>
                                    <?php echo getJS_Location($match->id); ?>
                                <?php } ?>                                
                            </td>
                        </tr>
                        <?php
                    }
                    if ($old_md)
                        echo '</tbody></table></div></div>';
                    ?>                                  
                </div>
            </div>
        </div>
    </form>
</div>