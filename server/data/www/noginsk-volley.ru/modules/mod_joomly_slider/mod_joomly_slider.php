<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_joomly_slider
 *
 * @copyright   Copyright (C) 2015 Artem Yegorov. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/helper.php';

$options = ModSliderHelper::getOptions($module->id);
$doc = JFactory::getDocument();
$doc->addStyleSheet( 'modules/mod_joomly_slider/css/base_slider.css' );

require JModuleHelper::getLayoutPath('mod_joomly_slider',  $params->get('layout', 'default'));
