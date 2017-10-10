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
$country = isset($this->lists["country"])?$this->lists["country"]:'';
	JHTML::_('behavior.tooltip');
		?>
		<?php
		if(!count($rows)){
			echo "<div id='system-message'><dd class='notice'><ul>".JText::_('BLBE_NOITEMS')."</ul></dd></div>";
		}
		?>
		<form action="index.php?option=com_joomsport" method="post" name="adminForm" id="adminForm">

		<div id="filter-bar" class="btn-toolbar">
            <div style="float:left;">
                <?php //echo JText::_( 'BLBE_MAPNAME' ); ?>
                <input type="text" maxlength="255" size="60" name="country" id="country" value="<?php echo !empty($country->country)?htmlspecialchars($country->country):''?>"  />
                <input type="text" maxlength="2" size="20" name="ccode" id="ccode" value="<?php echo !empty($country->ccode)?htmlspecialchars($country->ccode):''?>" style="width: 30px;" />
                <input type="hidden" name="countryid" value="<?php echo !empty($country->id)?intval($country->id):0?>"/>
            </div>
            <div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $pagination->getLimitBox(); ?>
			</div>

		</div>

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
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
				<th class="title">
					<?php 
						$sort_way_on = ($sort_field == 'country' && $sort_way == 'ASC')?"DESC":"ASC";
						
					?>
					<a href="#" onclick="javascript:JSPRO_order('country','<?php echo $sort_way_on;?>');" >
						<?php echo JText::_( 'BLBE_MAPNAME' ); ?>
                        <?php
                        if($sort_field == 'country'){
                            $sort_img = $sort_way=="ASC"?"icon-arrow-down":"icon-arrow-up";
                            echo '<i class="'.$sort_img.'"/></i>';

                        }
                        ?>
					</a>
				</th>
				<th class="title">
					<?php echo JText::_( 'CCODE' ); ?>
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
			$link = JRoute::_( 'index.php?option=com_joomsport&task=list_countr&countryid='. $row->id );
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
						echo '<a href="'.$link.'">'.$row->country.'</a>';
					?>
				</td>
				<td>
					<?php
						echo $row->ccode;
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
		<input type="hidden" name="task" value="list_countr" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="sortfield" value="<?php echo $sort_field; ?>" />
		<input type="hidden" name="sortway" value="<?php echo $sort_way; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>