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
global $yt;
if ($this->isRoot()){
	// import assets
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
	$document = JFactory::getDocument();
	$document->addStyleSheet($yt->templateurl().'menusys/class/common/css/menustyle.css','text/css');
	$document->addStyleSheet($yt->templateurl().'menusys/class/basic/assets/cssmenu.css','text/css');

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