<?php
/*------------------------------------------------------------------------
# JoomSport Professional 
# ------------------------------------------------------------------------
# BearDev development company 
# Copyright (C) 2011 JoomSport.com. All Rights Reserved.
# @license - http://joomsport.com/news/license.html GNU/GPL
# Websites: http://www.JoomSport.com 
# Technical Support:  Forum - http://joomsport.com/helpdesk/
-------------------------------------------------------------------------*/
// no direct access
defined('_JEXEC') or die;
$rows = $this->rows;

	$jsf = JRequest::getVar('function','jSelectArticle','','string');
		?>
		<script type="text/javascript">
		function setId(e){
			var s_id = parent.document.getElementById('nowID').href; //="index.php?option=com_joomsport&task=player_menu&tmpl=component&seas_id="+e.id
			parent.document.getElementById('nowID').href=s_id+"&seas_id="+e.id;
		}
		</script>
		<table class="table table-striped">
		<thead>
			<tr>
				<th width="2%" align="left">
					<?php echo JText::_( 'BLBE_NUM' ); ?>
				</th>
				
				<th class="title">
					<?php echo JText::_( 'BLBE_MENSEASON' ); ?>
				</th>
			</tr>
		</thead>
		
		<tbody>
		<?php
		if(isset($_GET['all']) && $_GET['all']==1){
			?>
			<tr >
				<td>
					#
				</td>
				
				<td>
					<a href="javascript:window.parent.<?php echo $jsf;?>('0', '<?php echo htmlspecialchars(JText::_('BLBE_ALL'), ENT_QUOTES, 'UTF-8')?>', 'sid');" id="0" onclick="setId(this)"><?php echo JText::_('BLBE_ALL'); ?></a>
				</td>
				
				
			</tr>
			<?php
		}
		$k = 0;
		if( count( $rows ) ) {
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	= $rows[$i];
			JFilterOutput::objectHtmlSafe($row);
			//$published 	= JHTML::_('grid.published', $row, $i);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo  $i+1; ?>
				</td>
				
				<td>
					<a href="javascript:window.parent.<?php echo $jsf;?>('<?php echo $row->id?>', '<?php echo htmlspecialchars(addslashes($row->name), ENT_QUOTES, 'UTF-8')?>', 'sid');" id="<?php echo $row->id;?>" onclick="setId(this)"><?php echo $row->name; ?></a>
				</td>
				
				
			</tr>
			<?php
		}
		} 
		?>
		</tbody>
	</table>