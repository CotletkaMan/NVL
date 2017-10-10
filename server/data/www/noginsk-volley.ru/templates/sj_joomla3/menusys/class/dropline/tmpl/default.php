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
	$js = $this->params->get('jsdropline', false) ? "js" : "";
	$this->addStylesheet(array($js."droplinemenu.css"));
	$abspath = $this->params->get('basepath') . DS . 'class';
	$abspath = realpath($abspath);
	!empty($abspath) or die($this->params->get('basepath') . ' does not exits. Please kindly set basepath for menusys');
	$relpath = array_pop( explode(JPATH_BASE, realpath($abspath), 2) );
	$relpath = str_replace("\\", "/", $relpath);
	
	if(!empty($js)){
		JHTML::_('behavior.mootools');
		$j15 = $this->j15 ? "15" : "";
		$this->addScript(array("jsdroplinemenu$j15.js"));
		
		$duration   = $this->params->get('moofxduration', '300');
		$transition = $this->params->get('moofx', 'Fx.Transitions.linear');
		$document =& JFactory::getDocument();
		$document->addStyleDeclaration("
			/* style for drop */
			ul#$menucssid.navi li.level1{
				position: static;
			}
			
			ul#$menucssid.navi ul.level2{
				margin: 0;
				width: " . intval($this->params->get('droplinewidth', 980)) . "px;
				height: " . intval($this->params->get('droplineheight', 35)) . "px;
			}
			
			ul#$menucssid.navi li.level2{
				float: left;
			}
			
			ul#$menucssid.navi div.separator.level2.havechild,
			ul#$menucssid.navi a.level2.havechild
			{	
				padding: 0 20px 0 20px;
				background-image: url($relpath/common/images/arrow.png);
				background-position: 95% -106px;
				background-repeat: no-repeat;
			}
			
			ul#$menucssid.navi li.level1.active ul.level2{
				left: 0px;	
			}
			ul#$menucssid.navi li.hover{
				position: static;
			}
			
			/* for rtl */
			ul#$menucssid.navirtl li.level1{
				position: static;
			}
			ul#$menucssid.navirtl ul.level2{
				margin: 0;
				width: " . intval($this->params->get('droplinewidth', 980)) . "px;
				height: " . intval($this->params->get('droplineheight', 35)) . "px;
			}
			
			ul#$menucssid.navirtl li.level2{
				float: right;
			}
			
			ul#$menucssid.navirtl div.separator.level2.havechild,
			ul#$menucssid.navirtl a.level2.havechild
			{	
				padding: 0 20px 0 20px;
				background-image: url($relpath/common/images/arrow.png);
				background-position: 5% -106px;
				background-repeat: no-repeat;
			}
			
			ul#$menucssid.navirtl li.level1.active ul.level2{
				right: 0px;
			}
			ul#$menucssid.navirtl li.hover{
				position: static;
			}
			ul#$menucssid.navirtl ul.level2 li.hover{
				position: relative;
			}
		");
		$document->addScriptDeclaration("
			window.addEvent('load',function() {
				new YTDroplineMenu(
					$('$menucssid'),
					{
						duration: $duration,
						transition: $transition,
						slide: 1,
						wrapperClass: 'yt-main',
						debug: false
					}
				);
			});"
		);
	} else {
		$document =& JFactory::getDocument();
		$document->addStyleDeclaration("
			/* style for drop */
			ul#$menucssid.navi{
				height: 79px;
			}
			
			ul#$menucssid.navi ul.level2{
				margin: 0;
				width: " . intval($this->params->get('droplinewidth', 980)) . "px;
				height: " . intval($this->params->get('droplineheight', 35)) . "px;
			}
			
			ul#$menucssid.navi li.level2{
				float: left;
			}
			
			ul#$menucssid.navi div.separator.level2.havechild,
			ul#$menucssid.navi a.level2.havechild
			{	
				padding: 0 20px 0 20px;
				background-image: url($relpath/common/images/arrow.png);
				background-position: 95% -106px;
				background-repeat: no-repeat;
			}
			
			ul#$menucssid.navi li.level1.active ul.level2{
				left: 0px;	
			}
			
			ul#$menucssid.navi ul.level2 li:hover{
				position: relative;
			}
			
			ul#$menucssid.navi ul.level2 li:hover ul.level3{
				left: -1px;
				top: 99%;
			}
			
			/* right to left */
			ul#$menucssid.navirtl{
				height: 79px;
			}
			
			ul#$menucssid.navirtl ul.level2{
				margin: 0;
				width: " . intval($this->params->get('droplinewidth', 980)) . "px;
				height: " . intval($this->params->get('droplineheight', 35)) . "px;
			}
			
			ul#$menucssid.navirtl li.level2{
				float: right;
			}
			
			ul#$menucssid.navirtl div.separator.level2.havechild,
			ul#$menucssid.navirtl a.level2.havechild
			{	
				padding: 0 20px 0 20px;
				background-image: url($relpath/common/images/arrow.png);
				background-position: 5% -106px;
				background-repeat: no-repeat;
			}
			
			ul#$menucssid.navirtl li.level1.active ul.level2{
				right: 0px;
			}
			
			ul#$menucssid.navirtl ul.level2 li:hover{
				position: relative;
			}
			ul#$menucssid.navirtl ul.level2 li:hover ul.level3{
				right: -1px;
				top: 99%;
			}
			
			/* zIndex */
			
			ul#$menucssid li.level1.active ul.level2{
				z-index: 1;
			}
			ul#$menucssid li.level1:hover ul.level2{
				z-index: 2;
			}
		");
	}
} else if ( $this->canAccess() ){
	$haveChild = $this->haveChild();
	$liClass = $this->haveClass() ? "class=\"{$this->getClass()}\"" : "";
?>

<li <?php echo $liClass; ?>>
	<?php echo $this->getLink(); ?>	
	<?php
		if($haveChild){
			$levelClassName = 'level'.($this->get('level',1)+1);
			if ($this->level>1){
				$subStyleWidth = $this->getSubmenuWidth();
			} else {
				// dropline doesnt set style for this level
				$subStyleWidth = "";
			}		
			
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