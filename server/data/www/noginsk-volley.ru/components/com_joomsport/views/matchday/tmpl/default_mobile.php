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
$Itemid = JRequest::getInt('Itemid');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
?>
<form action="<?php echo JRoute::_("index.php?option=com_joomsport&view=matchday&id=" . $this->m_id . "&Itemid=" . $Itemid . "&jslimit=" . $this->page->limit . "&page=0"); ?>" method="post" name="adminForm" id="adminForm">
    <div class="page-content">
        <nav class="navbar navbar-default navbar-static-top" role="navigation">
            <?php echo $lists["panel"]; ?>
        </nav>
        <!-- /.navbar -->
        <div class="main matchLayout">
            <div class="history col-xs-12 col-lg-12 mb20">
                <ol class="breadcrumb">
                    <li><a href="#" onclick="history.back(-1);return false;"><i class="fa fa-long-arrow-left"></i><?php echo JText::_("BL_BACK") ?></a></li>
                </ol>
            </div>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#matchday" role="tab" data-toggle="tab"><i class="tableS"></i><?php echo $this->escape($this->ptitle); ?></a></li>
            </ul>
            <!-- Tab panels -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="matchday">
                    <div class="table-responsive col-xs-12 col-lg-12">
                        <table class="table table-striped">
                            <tbody>
                                <?php
                                for ($i = 0; $i < count($this->lists["match"]); $i++) {
                                    $match = $this->lists["match"][$i];
                                    ?>
                                    <tr class="<?php echo $i % 2 ? "" : "active"; ?>">
                                        <td><span class="date"><?php echo $this->formatDate($match->m_date . ' ' . $match->m_time); ?></span></td>
                                        <td class="text-right"><a class="mr10" href="#"><?php echo $match->home ?></a> 
                                            <?php
                                            if (!$this->lists['t_single'] && $match->m_single != '1') {                        
                                                echo JHtml::_('images.getTeamEmbl', $match->team1_id, 1, $match->home);
                                            }
                                            ?>                                            
                                        </td>
                                        <td style="width:6%;"><button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Tooltip on bottom">
                                                <?php if ($match->m_played == 1) {
                                                    echo $match->score1.' : '.$match->score2;
                                                } else { 
                                                    echo '- : -';
                                                }?>
                                            </button>
                                        </td>
                                        <td class="text-left">
                                            <?php
                                            if (!$this->lists['t_single'] && $match->m_single != '1') {
                                                echo JHtml::_('images.getTeamEmbl', $match->team2_id, 1, $match->away);
                                            }
                                            ?>
                                            <a class="ml10" href="#"><?php echo $match->away ?></a>
                                        </td>      
                                        <?php if(true||$lists["locven"]){?>
					<td><?php echo getJS_Location($match->id);?></td>
					<?php } ?>
                                        <td>                                           
                                            <a class="btn btn-default matchDet" href="<?php echo JRoute::_('index.php?option=com_joomsport&view=match&id=' . $match->id) ?>"><i class="fa fa-search-plus"></i><?php echo JText::_('BL_LINK_DETAILMATCH'); ?></a>
                                        </td>                                          
                                    </tr>		
                                    <?php
                                }
                                ?>                                
                            </tbody>
                        </table>
                    </div>
                    <div class="pages">
                        <?php
                        $link_page = "index.php?option=com_joomsport&view=matchday&id=" . $this->m_id . "&Itemid=" . $Itemid . "&jslimit=" . $this->page->limit;
                        echo $this->page->getLimitPage();
                        echo $this->page->getPageLinks($link_page);
                        echo $this->page->getLimitBox();
                        ?>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</form>