<?php
/** 
 * YouTech menu template file.
 * 
 * @author The YouTech JSC
 * @package menusys
 * @filesource default.php
 * @license Copyright (c) 2011 The YouTech JSC. All Rights Reserved.
 * @tutorial http://www.smartaddons.com
 */

if ($this->isRoot()){
	$menucssid = $this->params->get('menustyle') . 'navigator' . $this->params->get('cssidsuffix');
	$addCssRight = $this->params->get('direction', 'ltr')=='rtl' ? "rtl" : "";
	echo "<ul id=\"$menucssid\" class=\"navi$addCssRight\">";
	if($this->haveChild()){
		$idx = 0;
		foreach($this->getChild() as $child){
			$child->addClass('level'.$child->get('level',1));
			++$idx;
			if ($idx==1){
				$child->addClass('first');
			} else if ($idx==$this->countChild()){
				$child->addClass('last');
			}
			if ($child->haveChild()){
				$child->addClass('havechild');
			}
			$child->getContent();
		}
	}
	echo "</ul>";
	
	// import assets
	$this->addStylesheet(array('moomenu.css'));
	$j15 = $this->j15 ? "15" : "";
	$this->addScript(array("menulib$j15.js"));
	
	$duration   = $this->params->get('moofxduration', '300');
	$transition = $this->params->get('moofx', 'Fx.Transitions.linear');		
	$document =& JFactory::getDocument();
	$activeSlider = intval($this->params->get('activeslider', '0')) ? 1 : 0;
	$document->addScriptDeclaration("
		// <![CDATA[
		window.addEvent('load',function() {
			new YTMenu(
						$('$menucssid'),
						{
							duration: $duration,
							transition: $transition,
							slide: 1,
							wrapperClass: 'yt-main',
							activeSlider: $activeSlider,
							debug: false
						});
		});
		// ]]>
		"
	);

} else if ( $this->canAccess() ){
	$haveChild = $this->haveChild();
	$liClass = $this->haveClass() ? "class=\"{$this->getClass()}\"" : "";
?>

<li <?php echo $liClass; ?>>
	<?php echo $this->getLink(); ?>
	<?php
		if($haveChild){
			$levelClassName = 'level'.($this->get('level',1)+1);
			$subStyleWidth = $this->getSubmenuWidth();
			
			echo "<ul class=\"{$levelClassName} subnavi\" $subStyleWidth>";
			$cidx = 0;
			foreach($this->getChild() as $child){
				$child->addClass($levelClassName);
				++$cidx;
				if ($cidx==1){
					$child->addClass('first');
				} else if ($cidx==$this->countChild()){
					$child->addClass('last');
				}
				if ($child->haveChild()){
					$child->addClass('havechild');
				}
				$child->getContent();
			}
			echo "</ul>";
		}
	?>
</li>

<?php 
}
?>