<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_joomly_slider
 *
 * @copyright   Copyright (C) 2015 Artem Yegorov. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_joomly_slider
 *
 * @package     Joomla.Site
 * @subpackage  mod_joomly_slider
 * @since       1.1.0
 */
class ModSliderHelper
{
	public static function getOptions($id)
	{
		if (isset($id) && ($id > 0))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('params')
			->from('#__modules')
			->where("id={$id}");
			$db->setQuery($query);
			$array =  $db->loadAssoc();
		} else {
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('params')
			->from('#__modules')
			->where('module="mod_joomly_slider"');
			$db->setQuery($query);
			$array =  $db->loadAssoc();
		}
		$options =  json_decode($array['params']); 
			return $options;
	}
}