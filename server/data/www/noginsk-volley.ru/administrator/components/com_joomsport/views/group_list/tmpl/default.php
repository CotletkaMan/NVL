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
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
		?>
		<?php
		if(!count($rows)){
			echo "<div id='system-message'><dd class='notice'><ul>".JText::_('BLBE_NOITEMS')."</ul></dd></div>";
		}
		?>
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm">
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
					<?php echo JText::_( '#' ); ?>
				</th>
				<th width="2%">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
				<th class="title">
					<?php 
						$sort_way_on = ($sort_field == 'g.group_name' && $sort_way == 'ASC')?"DESC":"ASC";
						
					?>
					<a href="#" onclick="javascript:JSPRO_order('g.group_name','<?php echo $sort_way_on;?>');" >
						<?php echo JText::_( 'BLBE_GROUP' ); ?>
                        <?php
                        if($sort_field == 'g.group_name'){
                            $sort_img = $sort_way=="ASC"?"icon-arrow-down":"icon-arrow-up";
                            echo '<i class="'.$sort_img.'"/></i>';

                        }
                        ?>
					</a>

				</th>
				<th width="13%" nowrap="nowrap" class="ordering">
					<?php 
						$sort_way_on = ($sort_field == 'g.ordering' && $sort_way == 'ASC')?"DESC":"ASC";
						
					?>
					<a href="#" onclick="javascript:JSPRO_order('g.ordering','<?php echo $sort_way_on;?>');" class="order-lbl">
						<?php echo JText::_( 'BLBE_ORDER' ); ?>
                        <?php
                        if($sort_field == 'g.ordering'){
                            $sort_img = $sort_way=="ASC"?"icon-arrow-down":"icon-arrow-up";
                            echo '<i class="'.$sort_img.'"/></i>';

                        }
                        ?>
					</a>
					<?php echo JHTML::_('jshtml.order',  $rows,'filesave.png', 'group_ordering' ); ?>
				</th>
				<th class="title">
					<?php 
						$sort_way_on = ($sort_field == 't.name' && $sort_way == 'ASC')?"DESC":"ASC";
						
					?>
					<a href="#" onclick="javascript:JSPRO_order('t.name','<?php echo $sort_way_on;?>');" >
						<?php echo JText::_( 'BLBE_TOURNAMENT' ); ?>
                        <?php
                        if($sort_field == 't.name'){
                            $sort_img = $sort_way=="ASC"?"icon-arrow-down":"icon-arrow-up";
                            echo '<i class="'.$sort_img.'"/></i>';

                        }
                        ?>
					</a>
				</th>
				<th class="title">
					<?php 
						$sort_way_on = ($sort_field == 's.s_name' && $sort_way == 'ASC')?"DESC":"ASC";
						
					?>
					<a href="#" onclick="javascript:JSPRO_order('s.s_name','<?php echo $sort_way_on;?>');" >
						<?php echo JText::_( 'BLBE_SEASON' ); ?>
                        <?php
                        if($sort_field == 's.s_name'){
                            $sort_img = $sort_way=="ASC"?"icon-arrow-down":"icon-arrow-up";
                            echo '<i class="'.$sort_img.'"/></i>';

                        }
                        ?>
					</a>
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
			$link = JRoute::_( 'index.php?option=com_joomsport&task=group_edit&cid[]='. $row->id );
			$checked 	= @JHTML::_('grid.checkedout',   $row, $i );
			//$published 	= JHTML::_('grid.published', $row, $i);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $this->page->getRowOffset( $i ); ?>
				</td>
				<td>
					<?php echo $checked; ?>
				</td>
				<td>
					<?php
						echo '<a href="'.$link.'">'.$row->group_name.'</a>';
					?>
				</td>
				<td class="Order">
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" class="text_area" style="width:30px;text-align: center" />
				</td> 
				<td>
					<?php echo $row->t_name;?>
				</td>
				<td>
					<?php echo $row->s_name;?>
				</td>
			</tr>
			<?php
		}
		} 
		?>
		</tbody>
		</table>
		
		<input type="hidden" name="option" value="com_joomsport" />
		<input type="hidden" name="task" value="group_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="sortfield" value="<?php echo $sort_field; ?>" />
		<input type="hidden" name="sortway" value="<?php echo $sort_way; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>