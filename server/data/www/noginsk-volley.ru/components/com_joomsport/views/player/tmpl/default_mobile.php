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
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
$lists = $this->lists;
?>
<form action="<?php echo JRoute::_("index.php?option=com_joomsport&task=player&id=" . $lists["player"]->id . "&sid=0&page=1&Itemid=" . $Itemid); ?>" method="post" name="adminForm" id="adminForm">
    <div class="page-content">
        <nav class="navbar navbar-default navbar-static-top" role="navigation">
            <?php echo $lists["panel"]; ?>
        </nav>
        <!-- /.navbar -->

        <div class="main playerLayout">
            <div class="history col-xs-12 col-lg-12">
                <ol class="breadcrumb pull-left">
                    <li><a href="#"  onclick="history.back(-1);
                            return false;"><i class="fa fa-long-arrow-left"></i><?php echo JText::_("BL_BACK") ?></a></li>
                </ol>
                <div class="socialMediaCont pull-right">                     
                    <div class="addthis_native_toolbox"><?php echo $this->lists['socbut']; ?></div>
                </div>
            </div>
            <div class="heading col-xs-12 col-lg-12">
                <h1 class="pull-left col-xs-12 col-sm-12 col-md-4 col-lg-4"><?php echo $this->escape($this->ptitle); ?></h1>
                <div class="selection col-xs-12 col-sm-12 col-md-8 col-lg-8 pull-right">
                    <label class="selected"><?php echo $this->lists["tourn_name"]; ?></label>
                    <div class="data">
                        <?php echo $this->lists['tourn']; ?>
                    </div>
                </div>                
            </div>
            <div class="playerDetails"> 
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#player" role="tab" data-toggle="tab"><i class="hidden-xs users"></i></i><?php echo JText::_('BL_TAB_PLAYER'); ?></a></li>
                    <li role="presentation"><a href="#statistic" role="tab" data-toggle="tab"><i class="hidden-xs chart"></i><?php echo JText::_('BL_TAB_STAT'); ?></a></li>
                    <li role="presentation"><a href="#matches" role="tab" data-toggle="tab"><i class="hidden-xs flag"></i></i><?php echo JText::_('BL_TAB_MATCHES'); ?></a></li>
                    <?php if(count($lists["photos"])){ ?>
                    <li role="presentation"><a href="#gallery" role="tab" data-toggle="tab"><i class="hidden-xs photos"></i><?php echo JText::_('BL_TAB_PHOTOS'); ?></a></li>
                    <?php } ?>
                </ul>

                <!-- Tab panels -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="player">
                        <div class="row">
                            <div class="col-xs-12">                                
                                <?php
                                echo JHtml::_('images.getViewContent', $Itemid, $lists["player"], $lists["ext_fields"], $lists["player"]->about, 0, $lists['photos'], $lists["teams_name"], $lists["player"]);
                                ?>                                
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="statistic">
                        <div class="">
                            <div class="table-responsive col-xs-12 col-lg-12">
                                <table class="table table-striped">
                                    <tbody>
                                        <?php
                                        for ($i = 0; $i < count($lists["stat_array"]); $i++) {
                                            $stats = $lists["stat_array"][$i];
                                            echo "<tr class='" . ($i % 2 ? "" : "active") . "'>";
                                            echo "<td><strong>";
                                            echo $stats[2];
                                            echo $stats[0];
                                            echo "</strong></td>";
                                            echo "<td>";
                                            echo $stats[1];
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                        ?>                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="matches">
                        <?php if (count($lists["matches"])) { ?>
                            <div class="">
                                <div class="table-responsive col-xs-12 col-lg-12">
                                    <table class="table table-striped">
                                        <tbody>
                                            <?php
                                            for ($i = 0; $i < count($lists["matches"]); $i++) {
                                                $match = $lists["matches"][$i];
                                                ?>
                                                <tr class="<?php echo $i % 2 ? "active" : ""; ?>">
                                                    <td><strong><?php echo $match->m_name . ":" ?></strong> <span class="date">
                                                            <?php
                                                            if ($match->m_date) {
                                                                if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $match->m_date)) {
                                                                    echo $this->formatDate($match->m_date . ' ' . $match->m_time);
                                                                } else {
                                                                    echo $match->m_date;
                                                                }
                                                            }
                                                            ?></span>
                                                    </td>
                                                    <td class="text-right"><?php echo $match->home ?></td>

                                                    <td style="width:6%"><button type="button" class="btn btn-default" 
                                                                                 data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Tooltip on bottom">                                                            
                                                                                     <?php
                                                                                     if ($match->m_played == 1) {
                                                                                         echo $match->score1
                                                                                         ?> : <?php
                                                                echo $match->score2;
                                                                if ($match->is_extra) {
                                                                    echo '<span class="ET" title="'.JText::_('BLFA_TEAM_WON_ET').'">'.JText::_('BL_RES_EXTRA').'</span>';
                                                                }
                                                            } else {
                                                                ?>
                                                                - : -                                                                    
                                                            <?php } ?>
                                                        </button>
                                                    </td>
                                                    <td class="text-left"><?php echo $match->away ?></td>
                                                    <?php if ($lists["locven"]) { ?>
                                                        <td><?php echo getJS_Location($match->mid); ?></td>
                                                        <?php
                                                    } else {
                                                        echo "<td>&nbsp;</td>";
                                                    }
                                                    ?>
                                                    <td>
                                                        <a class="btn btn-default matchDet" href="<?php echo JRoute::_('index.php?option=com_joomsport&task=view_match&id=' . $match->mid . '&Itemid=' . $Itemid) ?>"><i class="fa fa-search-plus"></i><?php echo JText::_('BL_LINK_DETAILMATCH'); ?></a>

                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="pages">
                                    <?php
                                    $link_page = "index.php?option=com_joomsport&view=player&sid=".$this->s_id."&id=".$lists["player"]->id."&Itemid=".$Itemid."&jscurtab=etab_match&jslimit=".$this->page->limit;
                                    echo $this->page->getLimitPage();
                                    echo $this->page->getPageLinks($link_page);
                                    echo $this->page->getLimitBox();
                                    ?>
                                </div>                                
                            </div>
                        <?php } ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="gallery">
                        <?php if(count($lists["photos"])){ echo JHtml::_('images.getGalleryHTML',  $lists["photos"]); } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="jscurtab" id="jscurtab" value="<?php echo $this->lists['jscurtab'];?>" />
</form>
<script>
jQuery( 'a' ).imageLightbox(
{
    selector:       'id="imagelightbox"',   // string;
    allowedTypes:   'png|jpg|jpeg|gif',     // string;
    animationSpeed: 250,                    // integer;
    preloadNext:    true,                   // bool;            silently preload the next image
    enableKeyboard: true,                   // bool;            enable keyboard shortcuts (arrows Left/Right and Esc)
    quitOnEnd:      false,                  // bool;            quit after viewing the last image
    quitOnImgClick: false,                  // bool;            quit when the viewed image is clicked
    quitOnDocClick: true,                   // bool;            quit when anything but the viewed image is clicked
    onStart:        false,                  // function/bool;   calls function when the lightbox starts
    onEnd:          false,                  // function/bool;   calls function when the lightbox quits
    onLoadStart:    false,                  // function/bool;   calls function when the image load begins
    onLoadEnd:      false                   // function/bool;   calls function when the image finishes loading
});
</script>
