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
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
$Itemid = JRequest::getInt('Itemid');

echo $this->lists["panel"];
?>

<form action="" method="post" name="adminForm" id="adminForm">

    <!-- <module middle> -->
    <div class="module-middle solid">

        <!-- <back box> -->
        <!-- <div class="back dotted"><a href="#" title="Back">&larr; Back</a></div> -->
        <!-- </back box> -->

        <!-- <title box> -->
        <div class="title-box">
            <h2><?php echo $this->escape($this->ptitle); ?></h2>            
        </div>
        <!-- </div>title box> -->
    </div>
    <!-- </module middle> -->

    <div class="content-module">

        <table class="season-list" cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr>                
                <th><?php echo JText::_('BLFA_CLUB'); ?></th>

            </tr>
            <?php
            for ($i = 0; $i < count($this->row); $i++) {
                $club = $this->row[$i];
                $link = JRoute::_("index.php?option=com_joomsport&view=club&id=" . $club->id);
                ?>
                <tr class="<?php echo $i % 2 ? "" : "gray"; ?>">
                    <td style="text-align:left;">
                        <?php
                            echo JHtml::_('images.getClubEmbl',  $club->id,3,$club->c_name);
                        ?>
	    		
                        <p class="player-name" style="display: table-cell;padding-left:7px;"><a href="<?php echo $link?>"><?php echo $club->c_name;?></a></p>
                    </td>                    

                </tr>
            <?php } ?>
            <tr>
                <td colspan="8" style="text-align:center;">
                    <?php
                    //echo $this->page->getListFooter(); 
                    $link_page = "?Itemid=" . $Itemid . "&jslimit=" . $this->page->limit;
                    echo "<div class='page_limit'>" . $this->page->getLimitPage() . "</div>";
                    echo $this->page->getPageLinks($link_page);
                    $limit = $this->page->limit;
                    ($limit == 0) ? ($limit = 'All') : ('');
                    echo "<div class='selectPag'><span style='position:relative;top:7px;right:5px;'>" . JText::_('BL_TAB_DISPLAY') . "</span><span class='select_box'><span class='num_box'>" . $limit . "</span></span>" . $this->page->getLimitBox() . "</div>";
                    ?>
                </td>
            </tr>
        </table>
    </div>
</form>