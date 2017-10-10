<?php
// Ensure this file is being included by a parent file
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Radio List Element
 *
 */
if (class_exists('JFormField')){
	class JFormFieldPositions extends JFormField
	{
		
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var	$_name = 'YtPositions';
		function getInput( ) { 
			$db =& JFactory::getDBO();
			$query = "SELECT DISTINCT position FROM #__modules ORDER BY position ASC";
			$db->setQuery($query);
			$groups = $db->loadObjectList();
			
			$groupHTML = array();	
			if ($groups && count ($groups)) {
				foreach ($groups as $v=>$t){
					$groupHTML[] = JHTML::_('select.option', $t->position, $t->position);
				}
			}
			$lists = JHTML::_('select.genericlist', $groupHTML, $this->name.'[]', ' multiple="multiple"  size="10" ', 'value', 'text', $this->value);
			
			return $lists;
		}
	}
}
if (class_exists('JElement')){
	class JElementPositions extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var	$_name = 'YtPositions';
	
		function fetchElement( $name, $value, &$node, $control_name ) {
			$showon = $node->attributes('showon');
			$script = "";
			if (gettype($showon)=='string'){
				list($ref, $val)=explode('==', $showon, 2);
				if (!empty($ref) || !empty($val)){				
					$script ="
					<script>
						window.addEvent('domready', function(){
							var ref = $('params$ref');
							var thisElement = $('params$name');						
							var TR$name = thisElement.parentNode.parentNode;
							if (ref.value=='$val'){
								TR$name.style.display='';
							} else {								
								TR$name.parentNode.parentNode.parentNode.style.height='auto';
								TR$name.style.display='none';
							}
							ref.addEvent('change', function(){
								if (this.value=='$val'){
									TR$name.style.display='';
								} else {								
									TR$name.parentNode.parentNode.parentNode.style.height='auto';
									TR$name.style.display='none';
								}
							});
						});
					</script>
					";
				}			
			}
			$db =& JFactory::getDBO();
			$query = "SELECT DISTINCT position FROM #__modules ORDER BY position ASC";
			$db->setQuery($query);
			$groups = $db->loadObjectList();
			$groupHTML = array();
			if ($groups && count ($groups)) {
				foreach ($groups as $tvalue=>$item){
					$groupHTML[] = JHTML::_('select.option', $item->position, $item->position);
				}
			}
			if( !empty($value) && !is_array($value) )
				$value = explode("|", $value);
			$lists = JHTML::_('select.genericlist', $groupHTML, "params[".$name."][]", ' multiple="multiple"  size="10" style="width:200px;"', 'value', 'text', $value);
			return $lists.$script;
		}
	}
}