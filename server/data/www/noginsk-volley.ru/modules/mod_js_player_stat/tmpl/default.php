<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'modules/mod_js_player_stat/css/mod_js_player_stat.css');
require_once("components/com_joomsport/includes/func.php");

$cItemId = $params->get('customitemid');
$ssss_id = $params->get('sidgid');
$ex = explode('|', $ssss_id);
$s_id = $ex[0];

$Itemid = JRequest::getInt('Itemid');
if (!$cItemId) {
    $cItemId = $Itemid;
}
?>
<div class="jsm_playerstatistic">
    <?php if (count($stats)) { ?>
        <table class="player-statistic" cellpadding="0" cellspacing="0" border="0">
            <?php
            for ($i = 0; $i < count($stats); $i++) {
                $row = $stats[$i];
                echo "<tr class='dotted'>";
                echo "<td class='dotted'>";
                echo $row[2];
                echo $row[0];
                echo "</td>";
                echo "<td class='dotted'>";
                echo $row[1];
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    <?php } ?>
</div>