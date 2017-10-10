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

if ( $this->isRoot() ){
	$menucssid = $this->params->get('menustyle') . 'menu' . $this->params->get('cssidsuffix');
	$addCssRight = $this->params->get('direction', 'ltr')=='rtl' ? "rtl" : "";
	echo "<ul id=\"$menucssid\" class=\"vmenu$addCssRight\">";
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
			if ($child->haveMegaContent()){
				$child->addClass('havechild');
			}
			$child->getContent();
		}
	}
	echo "</ul>";
	
	// import assets
	JHTML::_('behavior.mootools');
	$this->addStylesheet(array('vmegamenu.css'));
	$j15 = $this->j15 ? "15" : "";
	$this->addScript(array("vmegalib$j15.js"));
	
	$duration   = $this->params->get('moofxduration', '300');
	$transition = $this->params->get('moofx', 'Fx.Transitions.linear');
	$document =& JFactory::getDocument();
	$document->addStyleDeclaration("
		#$menucssid ul.subnavi {
			position: static;
			left: auto;
			right: auto;
			margin: 0;
			padding: 0;
		}
		
		#$menucssid ul.subnavi>li {
			margin-left: 0;
		}
		
		#$menucssid ul.subnavi>li.first {
			margin-top: 0;
		}
	");
	$document->addScriptDeclaration("
		window.addEvent('load',function() {
			new YTVMega(
				$('$menucssid'),
				{
					duration: $duration,
					transition: $transition,
					slide: 1,
					wrapperClass: 'yt-main',
					debug: false
				});
		});"
	);
} else if ( $this->canAccess() ){
	$haveChild = $this->haveChild();
	$liClass = $this->haveClass() ? "class=\"{$this->getClass()}\"" : "";
	
	// get mega description
	$ytext_cols = $this->params->get('ytext_cols', 1);
	
	// get computed width of columns
	$mega_cols = $this->getMegaCols();
	
	if ($ytext_cols=='auto'){
		$ytext_cols = count($mega_cols);
	} else if (!is_numeric($ytext_cols)){
		$ytext_cols = 1;
	}
	$ytext_cols = ((int)$ytext_cols<=1) ? 1 : $ytext_cols;
	
	//p($mega_cols, $this->id==173);
	// what's type of menu item.
	$item_content_type = $this->params->get('ytext_contenttype', 'menu');	
	if ($item_content_type=='megachild' || $item_content_type=='menu') {
		$mega_template = 'mega_item';
		// get child of this item
		$listChilds = $this->getChild();
		//$listModules = array_reverse($listModules);		
		$cm = array();
		$mega_cols_render = true;
		if ($mega_cols_render){
			$r = count($listChilds) % $ytext_cols;
			$n = floor(count($listChilds)/$ytext_cols);
			$next = 1;
			$icol = 0;
			foreach ($listChilds as $myChild){
				if (!isset($cm[$next])){
					$cm[$next] = array();
					$icol = 0;
				}
				$cm[$next][$icol++] = $myChild;
				if ($next<=$r){ $max = $n+1; } else { $max = $n; }
				if ($icol>=$max){
					$next++;
				}
			}
		} else {
			$listChilds = array_reverse($listChilds);
			$next = 1;
			foreach ($listChilds as $myChild){
				if (!isset($cm[$next])){
					$cm[$next] = array();					
				}
				$icol = count($cm[$next]);
				$cm[$next][$icol] = $myChild;
				if (++$next >= $ytext_cols){
					$next = 1;
				}
			}
		}
	
	} else {
		// get modules if content type is mod or pos
		$listModules = $this->loadModules();
		//$listModules = array_reverse($listModules);		
		$cm = array();
		$mega_cols_render = true;
		if ($mega_cols_render){
			$r = count($listModules) % $ytext_cols;
			$n = floor(count($listModules)/$ytext_cols);
			$next = 1;
			$icol = 0;
			foreach ($listModules as $module){
				if (!isset($cm[$next])){
					$cm[$next] = array();
					$icol = 0;
				}
				$cm[$next][$icol++] = $module;
				if ($next<=$r){ $max = $n+1; } else { $max = $n; }
				if ($icol>=$max){
					$next++;
				}
			}
		} else {
			$listModules = array_reverse($listModules);
			$next = 1;
			foreach ($listModules as $module){
				if (!isset($cm[$next])){
					$cm[$next] = array();					
				}
				$icol = count($cm[$next]);
				$cm[$next][$icol] = $module;
				if (++$next >= $ytext_cols){
					$next = 1;
				}
			}
		}	
	}
	$mega_content_width = $this->params->get('ytext_width');	
	$mega_content_style = $mega_content_width>0? "style=\"width:" . (int)$mega_content_width . "px\"" : "";
	
	$mega_content_cssid = $this->params->get('ytext_cssid');
	$mega_content_attr  = !empty($mega_content_cssid) ? " id=\"{$mega_content_cssid}\"" : "";
	
	$sublevelClass = 'level'.( 1+$this->get('level',1) );
	
	$sys_image = $this->params->get('menu_image');
	if (isset($sys_image) && $sys_image!='-1'){
		$baseuri = JURI::base(true);
		$mega_icon = "<span class=\"menu-icon\" style=\"background-image:url({$baseuri}/images/stories/{$sys_image});\">";
	}
?>
<li <?php echo $liClass; ?>>
	<?php echo $this->getLink(); ?>
	
	<?php
		if($this->haveMegaContent()){
			
		?>
		<!-- open mega-content div -->
		<div class="<?php echo $sublevelClass; ?> mega-content" <?php echo $mega_content_attr; ?>>
			
			<div class="mega-content-inner" <?php echo $mega_content_style; ?>>
				
				<?php			
				foreach ($mega_cols as $i => $w):
					if ($w<=0 && $w!='auto'){
						continue;
					} else {						
						$styleWidth = $w!='auto' ? "style=\"width:" . $w . "px;min-width:" . $w . "px;\"" : "";
					}
					$cmi=$i+1;
					if(isset($cm[$cmi])):
						if ($cmi==1){
							$addclass = ' first';
						} else if ($cmi==count($mega_cols)) {
							$addclass = ' last';
						} else {
							$addclass = '';
						}
						$numb_element_of_col = count($cm[$cmi]);
				?>
					<div class="mega-col<?php echo $addclass;?>"<?php echo $styleWidth; ?>>
						<?php
						$submenuClass='subnavi ' . $sublevelClass;
						if($item_content_type=='menu'){
							$cidx = 0;
							echo "<ul class=\"{$submenuClass}\" $styleWidth>";			
							foreach($cm[$cmi] as $child){
								$child->addClass($sublevelClass);
								++$cidx;
								if ($cidx==1){
									$child->addClass('first');
								} else if ($cidx==$numb_element_of_col){
									$child->addClass('last');
								}
								if ($child->haveMegaContent()){
									$child->addClass('havechild');
								}
								$child->getContent();
							}
							echo "</ul>";
						} else if($item_content_type=='megachild'){
							$allchild = count($cm[$cmi]);
							$iofchild = 0;
													
							foreach($cm[$cmi] as $k => $child){								
								$child->addClass($sublevelClass);
								$child->params->set('styleofcolumn', $styleWidth);
								$child->getContent('default_group');								
							}
							
						} else {												
							foreach($cm[$cmi] as $k => $module):
								$m_params = new YtParams($module->params);
								$m_class_sfx = $m_params->get('moduleclass_sfx', '');
								$m_showtitle = $module->showtitle; //$m_params->get('moduleclass_sfx', '');
							?>
								<div class="mega-module<?php echo $m_class_sfx;?> moduletable<?php echo $m_class_sfx; ?>">
									<?php if ($m_showtitle):?>
									<div class="mega-module-title">
										<h3><?php echo $module->title; ?></h3>
									</div>
									<?php endif; ?>
									<div class="mega-module-content">
									<?php
										echo JModuleHelper::renderModule($module, array('style'=>'raw')); 
									?>
									</div>
								</div>
							<?php
							endforeach;
						}
						?>
					</div>
				<?php
					endif;
				endforeach;
				?>

			</div>
		</div>
		<?php
		}
	?>
</li>

<?php 
}
?>