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

$season_id     = $params->get( 'season_id' );
$team_id       = $params->get( 'team_id' );
$embl_is       = $params->get( 'embl_is' );
$match_count 	 = $params->get( 'match_count' );
$single        = modBlResHelper::getStype($params);
$list          = modBlResHelper::getList($params);
$et            = modBlResHelper::getET($params);

// New parameters added for Match Result Configuration
$emblem_width           = $params->get( 'emblem_width' );
$emblem_height          = $params->get( 'emblem_height' );
$result_layout          = $params->get( 'result_layout' );
$matchday_reference     = $params->get( 'matchday_reference' );
$align_matchday_ref     = $params->get( 'align_matchday_ref' );
$align_home_emblem      = $params->get( 'align_home_emblem' );
$align_away_emblem      = $params->get( 'align_away_emblem' );
$align_home_team        = $params->get( 'align_home_team' );
$align_away_team        = $params->get( 'align_away_team' );
$link_emblem            = $params->get( 'link_emblem' );
$tooltip_emblem         = $params->get( 'tooltip_emblem' );
$link_team              = $params->get( 'link_team' );
$link_score             = $params->get( 'link_score' );
$border_score           = $params->get( 'border_score' );

$left_margin_matchday   = $params->get( 'left_margin_matchday' );
$right_margin_matchday  = $params->get( 'right_margin_matchday' );
$left_margin_home_team  = $params->get( 'left_margin_home_team' );
$right_margin_home_team = $params->get( 'right_margin_home_team' );
$left_margin_away_team  = $params->get( 'left_margin_away_team' );
$right_margin_away_team = $params->get( 'right_margin_away_team' );
$left_margin_emblem     = $params->get( 'left_margin_emblem' );
$right_margin_emblem    = $params->get( 'right_margin_emblem' );
$left_margin_score      = $params->get( 'left_margin_score' );
$right_margin_score     = $params->get( 'right_margin_score' );
$team_name_max_length   = $params->get( 'team_name_max_length' );

require(JModuleHelper::getLayoutPath('mod_js_results'));
