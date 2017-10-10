<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class JoomsportViewmatchday_list_today extends JViewLegacy
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

		$this->addToolbar();

		$this->assignRef('rows',		$items);


		require_once(dirname(__FILE__).'/tmpl/default'.($tpl?"_".$tpl:"").'.php');
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( 'BLBE_MATCHTODAY' ), 'match.png' );
		JToolBarHelper::save('matchday_today_save');
	}
}