<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$document		= JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'modules/mod_js_moder/css/mod_js_moder.css'); 

?>
<div class="div_moder_module">
	<?php if(count($usr)):?>
		<h4><?php echo JText::_("JSMM_USERPROFILE");?></h4>
		<ul>
			<?php foreach($usr as $res){?>
				<li><a href="<?php echo $res["link"];?>"><?php echo $res["name"];?></a></li>
			<?php } ?>
		</ul>
	<?php endif;?>
	<?php if(count($moder)):?>
		<h4><?php echo JText::_("JSMM_TEAMMODER");?></h4>
		<ul>
			<?php foreach($moder as $res){?>
				<li><a href="<?php echo $res["link"];?>"><?php echo $res["name"];?></a></li>
			<?php } ?>
		</ul>
	<?php endif;?>
	<?php if(count($admin)):?>
		<h4><?php echo JText::_("JSM_SEASADM");?></h4>
		<ul>
			<?php foreach($admin as $res){?>
				<li><a href="<?php echo $res["link"];?>"><?php echo $res["name"];?></a></li>
			<?php } ?>
		</ul>
	<?php endif;?>
</div>
