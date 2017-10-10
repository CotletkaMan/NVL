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
$sort_way = $this->lists["sortway"];
$sort_field = $this->lists["sortfield"];
	JHTML::_('behavior.tooltip');
		?>
		<?php
		if(!count($rows)){
			echo "<div id='system-message'><dd class='notice'><ul>".JText::_('BLBE_NOITEMS')."</ul></dd></div>";
		}
		?>
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm">
		<div class="pull-left">
                <div class="filter-search btn-group pull-left" style="float:left !important;">
                    <label for="js_filter_search" class="element-invisible"><?php echo JText::_('BLBE_SEARCH'); ?></label>
                    <input type="text" name="js_filter_search" id="js_filter_search" value="<?php echo $this->lists["js_filter_search"];?>" title="<?php echo JText::_('BLBE_SEARCH'); ?>" />
                    <input type="hidden" name="is_search" value="0" />
                </div>
                <div class="btn-group pull-left" style="float:left !important;">
                    <button type="submit" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                    <button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('js_filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
                </div>
            </div>
		<div style="clear:both;"></div>
		
		
		<table class="table table-striped">
		<thead>
			<tr>
				<th width="2%" align="left">
					<?php echo JText::_( '#' ); ?>
				</th>
				<!--th width="2%" align="left">
					<?php //echo JText::_( 'ID' ); ?>
				</th-->
				<th width="2%">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" />
				</th>
				<th class="title" width="50%">
					<?php 
						$sort_way_on = ($sort_field == 'c_name' && $sort_way == 'ASC')?"DESC":"ASC";
						
					?>
					<a href="#" onclick="javascript:JSPRO_order('c_name','<?php echo $sort_way_on;?>');" >
						<?php echo JText::_( 'BLBE_CLUB' ); ?>
						<?php
							/*if($sort_field == 'first_name'){
								$sort_img = $sort_way=="DESC"?"components/com_joomsport/img/sort_desc.png":"components/com_joomsport/img/sort_asc.png";
								echo '<img src="'.$sort_img.'" alt="" />';
							}*/
						?>
					</a>
				</th>
				<!--UPDATE-->
				
				<!--<th class="title">
					<?php 
						//$sort_way_on = ($sort_field == 'c.country' && $sort_way == 'ASC')?"DESC":"ASC";
						
					?>
					<a href="#" onclick="javascript:JSPRO_order('c.country','<?php echo $sort_way_on;?>');" >
						<?php echo JText::_( 'BLBE_COUNTRY' ); ?>
						<?php
							if($sort_field == 'c.country'){
								$sort_img = $sort_way=="DESC"?"components/com_joomsport/img/sort_desc.png":"components/com_joomsport/img/sort_asc.png";
								echo '<img src="'.$sort_img.'" alt="" />';
							}
						?>
					</a>
				</th>-->
				
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
		//print_r($rows);
		if( count( $rows ) ) {
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	= $rows[$i];
			JFilterOutput::objectHtmlSafe($row);
			$link = JRoute::_( 'index.php?option=com_joomsport&task=club_edit&cid[]='. $row->id );
			$checked 	= @JHTML::_('grid.checkedout',   $row, $i );
			//$published 	= JHTML::_('grid.published', $row, $i);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $this->page->getRowOffset( $i ); ?>
				</td>
				<!--td>
					<?php //echo $row->id; ?>
				</td-->
				<td>
					<?php echo $checked; ?>
				</td>
				<td>
					<?php
					
						echo '<a href="'.$link.'">'.$row->c_name.'</a>';
					?>
				</td>
				
			</tr>
			<?php
		}
		} 
		?>
		</tbody>
		</table>
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="club_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="sortfield" value="<?php echo $sort_field; ?>" />
		<input type="hidden" name="sortway" value="<?php echo $sort_way; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>