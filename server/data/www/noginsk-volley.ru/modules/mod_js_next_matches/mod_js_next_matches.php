<?php
/**
* @version		$Id: mod_stats.php 10381 2008-06-01 03:35:53Z pasamio $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include the syndicate functions only once
require_once (dirname(__FILE__).'/helper.php');

$season_id = $params->get( 'season_id' );
$match_count 	= $params->get( 'match_count' );
$team_id = $params->get( 'team_id' );
$embl_is = $params->get( 'embl_is' );
$list = modBlNextHelper::getList($params);
$single = modBlNextHelper::getStype($params);

$result_layout_next          = $params->get( 'result_layout_next' );
$emblem_width_next           = $params->get( 'emblem_width_next' );
$emblem_height_next          = $params->get( 'emblem_height_next' );
$matchday_reference_next     = $params->get( 'matchday_reference_next' );
$align_matchday_ref_next     = $params->get( 'align_matchday_ref_next' );
$align_home_emblem_next     = $params->get( 'align_home_emblem_next' );
$align_away_emblem_next      = $params->get( 'align_away_emblem_next' );
$align_home_team_next        = $params->get( 'align_home_team_next' );
$align_away_team_next        = $params->get( 'align_away_team_next' );
$link_emblem_next            = $params->get( 'link_emblem_next' );
$tooltip_emblem_next         = $params->get( 'tooltip_emblem_next' );
$link_team_next              = $params->get( 'link_team_next' );
$link_score_next             = $params->get( 'link_score_next' );
$border_score_next           = $params->get( 'border_score_next' );

$left_margin_matchday_next   = $params->get( 'left_margin_matchday_next' );
$right_margin_matchday_next  = $params->get( 'right_margin_matchday_next' );
$left_margin_home_team_next  = $params->get( 'left_margin_home_team_next' );
$right_margin_home_team_next = $params->get( 'right_margin_home_team_next' );
$left_margin_away_team_next  = $params->get( 'left_margin_away_team_next' );
$right_margin_away_team_next = $params->get( 'right_margin_away_team_next' );
$left_margin_emblem_next     = $params->get( 'left_margin_emblem_next' );
$right_margin_emblem_next    = $params->get( 'right_margin_emblem_next' );
$left_margin_score_next      = $params->get( 'left_margin_score_next' );
$right_margin_score_next     = $params->get( 'right_margin_score_next' );
$team_name_max_length_next   = $params->get( 'team_name_max_length_next' );

require(JModuleHelper::getLayoutPath('mod_js_next_matches'));
