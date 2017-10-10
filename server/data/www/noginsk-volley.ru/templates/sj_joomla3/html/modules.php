<?php
/*
 * ------------------------------------------------------------------------
 * Yt FrameWork for Joomla 3.0
 * ------------------------------------------------------------------------
 * Copyright (C) 2009 - 2012 The YouTech JSC. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: The YouTech JSC
 * Websites: http://www.smartaddons.com - http://www.cmsportal.net
 * ------------------------------------------------------------------------
*/

defined('_JEXEC') or die;
/*
 * Module chrome that allows for ytmod corners by wrapping in nested div tags
 */
function modChrome_ytmod($module, &$params, &$attribs){ ?>
    <?php
	$badge = preg_match ('/badge/', $params->get('moduleclass_sfx')) ? "<span class=\"badge\"></span>\n" : "";
	$icons = preg_match ('/icon/', $params->get('moduleclass_sfx'))?"<span class=\"icon\">&nbsp;</span>\n":"";	
	
	?>
	<div class="module<?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?>">
    <div class="module-inner">
		<?php if ((bool) $module->showtitle) : ?>
			<h3 class="title"><?php echo $badge; ?><?php echo $module->title; ?><?php echo $icons; ?></h3>
		<?php endif; ?>
		<div class="module-content clearfix">
			<div class="custom clearfix">
				<?php echo $module->content; ?>
			</div>
		</div>
		<div class="module-bottom"></div>
    </div>
    </div>
<?php
}

function modChrome_ytmod2($module, &$params, &$attribs){ ?>
    <div class="module<?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?>">
    <div class="module-inner">
        <?php if ((bool) $module->showtitle) : ?>
            <h3 class="title"><?php echo $module->title; ?></h3>
        <?php endif; ?>
        <div class="module-content clearfix">
        	<?php echo $module->content; ?>
        </div>
    </div>
    </div>
<?php
}


?>


