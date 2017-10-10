<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_stats
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<dl class="stats-module<?php echo $moduleclass_sfx ?>">
<?php foreach ($list as $item) : ?>
	<dt><?php echo $item->title;?></dt>
	<dd title="<?php echo $item->data;?>"><?php echo substr($item->data, 0, 8);?></dd>
<?php endforeach; ?>
</dl>
