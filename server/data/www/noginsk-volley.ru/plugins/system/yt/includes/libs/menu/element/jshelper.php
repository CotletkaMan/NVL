<?php
// Ensure this file is being included by a parent file
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Radio List Element
 *
 */
class JFormFieldJshelper extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'YtHelper';

	function getInput( ) {
		if (!defined ('YT_JSHELPER')) {
			define('YT_JSHELPER', 1);
		$script = "
<script>
window.addEvent('domready', function(){
	try{
		var parentbyTag = function(tag){
			if(tag){
				var par = this.getParent();
				while(par && par.get('tag')!=tag){
					par = par.getParent();
				}
				return par;
			}
			return this.getParent();
		}
		var findElement = function(e){
			var _eid  = depends.control+'_params_'+e;					
			var element = $(_eid);
			if (!element){
				var eid  = depends.control+'params'+e;
				console.log('selector: ' + eid);
				element = $(eid);
			}
			return element;
		}
		var updateElements = function (){			
			if (depends.elements.length){
				console.log('numb element: ' + depends.elements.length);
				for(var i=0; i<depends.elements.length; i++) {
					var element = findElement(depends.elements[i].ename);					
					console.log('el['+i+']=' + element);
					if (element){
						var byElement =  findElement(depends.elements[i].by);
						console.log('byElement='+byElement);
						if (byElement){
							if(!depends.by[depends.elements[i].by]){
								depends.by[depends.elements[i].by] = true;//
							}							
							var byCurr = byElement.get('value');
							console.log('current value: ' + byCurr);
							if (byCurr==depends.elements[i].val){
								var item = parentbyTag.bind(element, 'li').call();
								console.log('item='+item);
								$(item).setStyle('display', '');
								var slider = parentbyTag.bind(item, 'div').call();
								slider.setStyle('height', 'auto');
							} else {
								var item = parentbyTag.bind(element, 'li').call();
								console.log('item='+item);								
								$(item).setStyle('display', 'none');
								var slider = parentbyTag.bind(item, 'div').call();
								slider.setStyle('height', 'auto');
							}
						}
					}
				}
			}
			if (depends.by){
				console.log(depends.by);
				for(var i in depends.by) {
					var element = findElement(i);
					if (element && !element._change){
						element._change = true;
						element.addEvent('change', updateElements);
					}
				}

			}
		}
		var depends ={
			elements: [
				{
					ename: 'ytext_modules',
					by: 'ytext_contenttype',
					val: 'mod'
				},
				{
					ename: 'ytext_positions',
					by: 'ytext_contenttype',
					val: 'pos'
				}
			],
			by: {},
			control: 'jform'
		};
		updateElements();
	} catch(e) {
		for(var i in e){
			console.log(i+' = ' + e[i]);
		}	
	}
});
</script>
";		
		} else {
			$script = "";
		}
		return $script;
	}
} 