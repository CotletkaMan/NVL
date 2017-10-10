<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include the syndicate functions only once
require_once (dirname(__FILE__).'/helper.php');

$stats = modBlPlayerStatHelper::getStatistic($params);
require(JModuleHelper::getLayoutPath('mod_js_player_stat'));
