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
		JHTML::_('behavior.tooltip');
		?>
		
		<table class="table table-striped">
		<thead>
			<tr>
				<th width="2%" align="left">
					<?php echo JText::_( 'BLBE_NUM' ); ?>
				</th>
				
				<th class="title">
					<?php echo JText::_( 'BLBE_GROUP' ); ?>
				</th>
				<th class="title">
					<?php echo JText::_( 'BLBE_SEASON' ); ?>
				</th>
			</tr>
		</thead>
		
		<tbody>
		<tr class="<?php echo "row1"; ?>" style="display:none;" id="row1">
				<td>
					<?php echo  '0'; ?>
				</td>
				
				<td>
					<a href="javascript:window.parent.<?php echo $jsf;?>('0', '<?php echo JText::_('BLBE_ALL')?>', 'gr_id');"><?php echo JText::_('BLBE_ALL'); ?></a>
				</td>
				<td>
					<?php echo JText::_('BLBE_ALL'); ?>
				</td>
				
			</tr>
		<?php
		$k = 0;
		if( count( $rows ) ) {
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	= $rows[$i];
			JFilterOutput::objectHtmlSafe($row);
			//$published 	= JHTML::_('grid.published', $row, $i);
			?>
			<tr class="<?php echo "row$k"; ?>" style="display:none;" id="<?php echo $row->s_id; ?>">
				<td>
					<?php echo  $i+1; ?>
				</td>
				
				<td>
					<a href="javascript:window.parent.<?php echo $jsf;?>('<?php echo $row->id?>', '<?php echo htmlspecialchars(addslashes($row->group_name), ENT_QUOTES, 'UTF-8')?>', 'gr_id');"><?php echo $row->group_name; ?></a>
				</td>
				<td>
					<?php echo $row->name; ?>
				</td>
				
			</tr>
			<?php
		}
		} 
		?>
		</tbody>
		<script type="text/javascript">
			var s_id = parent.document.getElementById('jform_request_sid_id').value;
			
			if(s_id){
				var el = document.getElementsByClassName("row0");
				var el2 = document.getElementById("row1");
					
					for(i=0; i<el.length; i++){
						if(el[i].id == s_id){
							el[i].style.display="table-row";
							el2.style.display="table-row";
						}
					}
			}
			/*else{
				var el = document.getElementsByClassName("row0");
				for(i=0; i<el.length; i++){
					el[i].style.display="table-row";
				}
			}*/
		</script>
	</table>