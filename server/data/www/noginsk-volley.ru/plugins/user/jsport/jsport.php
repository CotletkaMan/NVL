<?php
/**
 *
 * Contact Creator
 * A tool to automatically create and synchronise contacts with a user
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Class for Contact Creator
 * @package		Joomla.Plugin
 * @subpackage	User.contactcreator
 * @version		1.6
 */
class plgUserJSport extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onUserAfterSave($user, $isnew, $success, $msg)
	{
		if(!$success) {
			return false; // if the user wasn't stored we don't resync
		}

		if(!$isnew) {
			return false; // if the user isn't new we don't sync
		}

		// ensure the user id is really an int
		$user_id = (int)$user['id'];

		if (empty($user_id)) {
			die('invalid userid');
			return false; // if the user id appears invalid then bail out just in case
		}
		
		$db = JFactory::getDBO();
		
		$query = "INSERT INTO #__bl_players(`first_name`,`usr_id`,`nick`) VALUES('".$user['name']."',{$user_id},'".$user['username']."')";
		$db->setQuery($query);
		$db->query();
		$error = $db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
	}
}
