<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class JoomsportViewtour_list extends JViewLegacy
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
		$lists		= $this->_model->_lists;
		$pagination =  $this->_model->_pagination;
//		$pagination = $this->get('Pagination');
		$user       = JFactory::getUser();

		$this->addToolbar();

		$this->assignRef('user',	$user	);
		$this->assignRef('lists',		$lists);
		$this->assignRef('rows',		$items);
		$this->assignRef('page',	$pagination);

		require_once(dirname(__FILE__).'/tmpl/default'.($tpl?"_".$tpl:"").'.php');
	}
	
	protected function addToolbar()
	{
		
		JToolBarHelper::title( JText::_( 'BLBE_TOURNAMENTLIST' ), 'tourn.png' );
		JToolBarHelper::addNew('tour_add');
		JToolBarHelper::editList('tour_edit');
		JToolBarHelper::publishList('tour_publish');
		JToolBarHelper::unpublishList('tour_unpublish');
		JToolBarHelper::deleteList('','tour_del',JText::_('BLBE_DELETE') );
		
		
	}
}