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
?>

<div class="page-content">
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
        <div class="container-fluid">
            <?php
            echo $this->lists["panel"];
            ?>                
        </div>
    </nav>
    <!-- /.navbar -->
    <form action="" method="post" name="adminForm" id="adminForm">      
        <div class="main teamList">
            <div class="heading col-xs-12 col-lg-12">
                <h1 class="pull-left col-xs-12 col-sm-12 col-md-4 col-lg-4"><?php echo $this->escape($this->ptitle); ?></h1>
                <div class="selection col-xs-12 col-sm-12 col-md-8 col-lg-8 pull-right">
                    <div class="data">                         
                    </div>
                </div>
            </div>
            <div class="table-responsive col-xs-12 col-lg-12">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><h3><?php echo JText::_('BLFA_CLUB'); ?></h3></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        for ($i = 0; $i < count($this->row); $i++) {
                            $club = $this->row[$i];
                            $link = JRoute::_("index.php?option=com_joomsport&view=club&id=" . $club->id);
                            ?>
                            <tr class="<?php echo $i % 2 ? "" : "active"; ?>">
                                <td class="left">
                                    <?php
                                        echo JHtml::_('images.getClubEmbl', $club->id, 3, $club->c_name);
                                    ?>
                                    <a href="<?php echo $link ?>"><?php echo $club->c_name;?></a>
                                </td>
                            </tr>
                        <?php } ?>                        
                    </tbody>
                </table>
            </div>
            <div class="pages">
                <?php
                $link_page = "?Itemid=" . $Itemid . "&jslimit=" . $this->page->limit;
                echo $this->page->getLimitPage();
                echo $this->page->getPageLinks($link_page);
                echo $this->page->getLimitBox();
                ?>
            </div>
        </div>
    </form>
</div>