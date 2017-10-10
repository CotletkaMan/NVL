<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class JoomsportViewvenue_list extends JViewLegacy
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
		JToolBarHelper::addNew('venue_add');
		JToolBarHelper::editList('venue_edit');
		JToolBarHelper::title( JText::_( 'BLBE_VENUELIST' ), 'team.png' );
		JToolBarHelper::deleteList('','venue_del',JText::_('BLBE_DELETE') );
		
		
	}
}