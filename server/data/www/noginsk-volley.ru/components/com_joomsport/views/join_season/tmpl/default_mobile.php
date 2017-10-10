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
global $Itemid;
$Itemid = JRequest::getInt('Itemid');
$options = $this->options;
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
?>
<div class="page-content">
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
        <?php
        echo $lists["panel"];
        ?>
    </nav>
    <!-- /.navbar -->

    <div class="main clubLayout">
        <div class="history col-xs-12 col-lg-12">
            <ol class="breadcrumb">
                <li><a href="#" onclick="history.back(-1);"><i class="fa fa-long-arrow-left"></i><?php echo JText::_("BL_BACK") ?></a></li>
            </ol>
        </div>
        <div class="heading col-xs-12 col-lg-12">
            <h1 class="pull-left col-xs-12 col-sm-12 col-md-12 col-lg-12"><?php echo $this->escape($this->params->get('page_title')); ?></h1>
        </div>
        <div class="joinSeason"> 
            <div class="well well-sm col-xs-12 col-sm-6 col-md-3 col-lg-3">                   
                <div class="line"> <span class="pull-left"><strong><?php echo JText::_('BL_STARTDATE') ?>:</strong></span> <span class="pull-right"><?php echo $this->lists["season_par"]->reg_start ?></span> </div>
                <div class="line"> <span class="pull-left"><strong><?php echo JText::_('BL_ENDDATE') ?>:</strong></span> <span class="pull-right"><?php echo $this->lists["season_par"]->reg_end ?></span> </div>
                <div class="line"> <span class="pull-left"><strong><?php echo JText::_('BL_PARTIC') ?>:</strong></span> <span class="pull-right"><?php echo $this->lists["season_par"]->s_participant . " (" . JText::_('BL_NOW') . " " . $this->lists["part_count"] . ")"; ?></span> </div>

                <?php
                if ($this->lists["unable_reg"]) {
                    if ($options->paypal_on) {
                        //
                    } else {
                        ?>
                        <form method="POST" action="">
                            <?php
                            if ($this->lists["t_single"]) {
                                echo "<input type='hidden' name='reg_team' value='" . $this->user->id . "' />";
                                echo "<input type='hidden' name='is_team' value='0' />";
                            } else if (!$this->lists['no_team']) {
                                echo '<div class="line">';
                                echo $this->lists['cap'];
                                echo "<input type='hidden' name='is_team' value='1' />";
                                echo '</div>';
                            }
                            ?>
                            <?php
                            if ($this->lists['no_team']) {
                                echo '<div class="line">' . JText::_('BL_NOCAP') . '</div>';
                            } else {
                                ?>
                                <input type="hidden" name="task" value="joinme" />
                                <input type="hidden" name="sid" value="<?php echo $this->lists["s_id"]; ?>" />

                                <div class="line">
                                    <button type="submit" class="btn"><?php echo JText::_('BL_JOINSEAS'); ?></button>
                                </div>
                            <?php } ?>
                        </form>
                        <?php
                    }
                }
                ?>                
            </div>
        </div>
    </div>
</div>