<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class JoomsportViewplayer_list extends JViewLegacy
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
		$lists		= $this->_model->_lists;
		$total		= $this->_model->_total;
		$pagination =  $this->_model->_pagination;

		$this->addToolbar();
		$user = JFactory::getUser();
		$this->assignRef('user',		$user);
		$this->assignRef('lists',		$lists);
		$this->assignRef('rows',		$items);
		$this->assignRef('page',	$pagination);

		require_once(dirname(__FILE__).'/tmpl/default'.($tpl?"_".$tpl:"").'.php');
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::addNew('player_add');
		JToolBarHelper::editList('player_edit');
		JToolBarHelper::title( JText::_( 'BLBE_PLAYER_LIST' ), 'player.png' );
		JToolBarHelper::deleteList('','player_del',JText::_('BLBE_DELETE'));
		
		
	}
}