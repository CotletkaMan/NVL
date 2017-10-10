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
JHTML::_('behavior.tooltip');
?>	
<?php
	
		if(!count($rows)){
			echo "<div id='system-message'><dd class='notice'><ul>".JText::_('BLBE_NOITEMS')."</ul></dd></div>";
		}
		?>
		<form action="index.php?option=com_joomsport&tmpl=component" method="post" name="adminForm" id="adminForm">
		<div id="filter-bar" class="btn-toolbar">
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right"><?php echo $this->lists['tourn'];?></div>
		</div>
		<table class="table table-striped">
		<thead>
			<tr>
				<th width="2%" align="left">
					<?php echo JText::_( 'BLBE_NUM' ); ?>
				</th>
				<th class="title">
					<?php echo JText::_( 'BLBE_MATCHDAY' ); ?>
				</th>
				<th class="title">
					<?php echo JText::_( 'BLBE_TOURNAMENT' ); ?>
				</th>
				
			</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="13">
				<?php echo $this->page->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		if( count( $rows ) ) {
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	= $rows[$i];
			JFilterOutput::objectHtmlSafe($row);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $this->page->getRowOffset( $i ); ?>
				</td>
				<td>
				
					<?php echo '<a href="javascript:window.parent.jSelectArticle('.$row->id.', \''.htmlspecialchars(addslashes($row->m_name), ENT_QUOTES, 'UTF-8').'\', \'id\');">'.$row->m_name.'</a>';?>				
			
				</td>
				<td>
					<?php echo $row->tourn;?>
				</td>
				
			</tr>
			<?php
		}
		} 
		?>
		</tbody>
		</table>
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="matchday_menu" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="tmpl" value="component" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>