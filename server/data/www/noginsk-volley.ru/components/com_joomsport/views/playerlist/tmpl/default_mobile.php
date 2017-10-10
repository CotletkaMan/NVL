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
    <form action="<?php echo JRoute::_('index.php')?>" method="post" name="adminForm" id="adminForm">      
        <div class="main playerList">
            <div class="history col-xs-12 col-lg-12">
                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-long-arrow-left"></i>[Back]</a></li>
                </ol>
            </div>
            <div class="heading col-xs-12 col-lg-12">
                <h1 class="pull-left col-xs-12 col-sm-12 col-md-4 col-lg-4"><?php echo $this->escape($this->ptitle); ?></h1>
                <div class="selection col-xs-12 col-sm-12 col-md-8 col-lg-8 pull-right">
                    <label class="selected"><?php echo $this->lists["tourn_name"];?></label>
                    <div class="data">
                        <?php echo $this->lists['tourn'];?>
                    </div>
                </div>
            </div>
            <div class="table-responsive col-xs-12 col-lg-12">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="left"><a href="#" onclick="js_newsort('name',this); return false;"><?php echo JText::_('BL_TAB_PLAYER');?> <i class="fa <?php echo ($this->lists["field"] == "name")?($this->lists["dest"]?"fa-caret-down":"fa-caret-up"):("");?>"></i></a></th>
                            <?php if(!$this->lists["t_single"] && $this->lists["s_id"] || !$this->lists["s_id"]){?>
                            <th><a href="#" onclick="js_newsort('sortteams',this); return false;"><?php echo JText::_('BL_TAB_TEAM');?> <i class="fa <?php echo ($this->lists["field"] == "sortteams")?($this->lists["dest"]?"fa-caret-down":"fa-caret-up"):("");?>"></i></a></th>
                            <?php } ?>
                            <?php
                            if(isset($this->lists["events"])){
                                    for($i=0;$i<count($this->lists["events"]);$i++){
                                            echo '<th><a href="#" onclick="js_newsort(\''.($this->lists["events"][$i]->id).'\',this); return false;">'.(isset($this->lists["events"][$i]->e_imgth)?$this->lists["events"][$i]->e_imgth:$this->lists["events"][$i]->e_name).' <i class="fa '.($this->lists["field"] == $this->lists["events"][$i]->id?($this->lists["dest"]? "fa-caret-down": "fa-caret-up"): "").'"></i></a></th>';
                                    }
                            }
                            ?>                 
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $lim = $this->page->limit;
                        $this->lists["limitstart"] = ($this->lists["limitstart"] == 1) ? (0) : (($this->lists["limitstart"] - 1) * $lim);
                        if ($this->page->limit == 0) {
                            $this->lists["limitstart"] = 0;
                            $lim = count($this->lists["players"]) - $this->lists["limitstart"];
                        }
                        for ($i = $this->lists["limitstart"]; $i < $lim + $this->lists["limitstart"]; $i++) {
                            if (isset($this->lists["players"][$i])) {
                                $player = $this->lists["players"][$i];
                                $link = JRoute::_("index.php?option=com_joomsport&view=player&id=" . $player["id"] . "&sid=" . $this->lists["s_id"] . "&Itemid=" . $Itemid);
                                $link2 = '#';
                                ?>
                                <tr class="<?php echo $i % 2 ? "" : "active"; ?>">
                                    <td  class="left">
                                        <?php echo JHtml::_('images.getPlayerThumb', $player['id'], 0, null, 0);?>
                                        <a href="<?php echo $link ?>"><?php echo $player["name"] ?></a>               
                                    </td>
                                    <?php if (!$this->lists["t_single"] && $this->lists["s_id"] || !$this->lists["s_id"]) { ?>
                                        <td><a href="<?php echo $link2 ?>"><?php echo $player["teams"]; ?></a></td>
                                    <?php } ?>
                                    <?php
                                    if (isset($this->lists["events"])) {
                                        for ($j = 0; $j < count($this->lists["events"]); $j++) {
                                            echo "<td>" . $player[$this->lists["events"][$j]->id] . "</td>";
                                        }
                                    }
                                    ?>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="pages">
                <?php
                $link_page = "index.php?option=com_joomsport&view=playerlist&sid=".$this->lists["s_id"]."&sortfield=".$this->lists["field"]."&sortdest=".$this->lists["dest"]."&Itemid=".$Itemid."&jslimit=".$this->page->limit;
                echo $this->page->getLimitPage();
                echo $this->page->getPageLinks($link_page);
                echo $this->page->getLimitBox();
                ?>
            </div>
        </div>
        <input type="hidden" name="option" value="com_joomsport" />
        <input type="hidden" name="view" value="playerlist" />
        <input type="hidden" name="Itemid" value="<?php echo $Itemid?>" />
        <input type="hidden" name="sortfield" value="<?php echo $this->lists["field"]?>" />
        <input type="hidden" name="sortdest" value="<?php echo $this->lists["dest"]?>" />
        <input type="hidden" name="limitstart" value="0" />
    </form>
</div>