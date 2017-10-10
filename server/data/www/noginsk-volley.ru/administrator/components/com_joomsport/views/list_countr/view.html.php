<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class JoomsportViewlist_countr extends JViewLegacy
{
	var $_model = null;
	function __construct(& $model){
		$this->_model = $model;
	}
	function display($tpl = null)
	{
		global $mainframe, $option;

		$db		= JFactory::getDBO();
		$uri	= JFactory::getURI();

		// Get data from the model
		$items		= $this->_model->_data;
		$total		= $this->_model->_total;
		$pagination =  $this->_model->_pagination;
		$lists		= $this->_model->_lists;

		$this->addToolbar();

		$this->assignRef('lists',		$lists);
		$this->assignRef('rows',		$items);
		$this->assignRef('page',	$pagination);

		require_once(dirname(__FILE__).'/tmpl/default'.($tpl?"_".$tpl:"").'.php');
	}
	
	protected function addToolbar()
	{
		//JToolBarHelper::addNew('map_add');map_apply
        JToolBarHelper::apply('apply_countr');
		//JToolBarHelper::editList('edit_countr');
		//JToolBarHelper::title( JText::_( 'BLBE_MAPSLIST' ), 'maps.png' );map_edit
		JToolBarHelper::deleteList('','del_countr',JText::_('BLBE_DELETE'));
		
		
	}
}