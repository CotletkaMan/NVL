<?php
/*
 * ------------------------------------------------------------------------
 * Yt FrameWork for Joomla 2.5
 * ------------------------------------------------------------------------
 * Copyright (C) 2009 - 2012 The YouTech JSC. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: The YouTech JSC
 * Websites: http://www.smartaddons.com - http://www.cmsportal.net
 * ------------------------------------------------------------------------
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<script type="text/javascript" src="<?php echo $yt->templateurl()."js/masonry.min.js"; ?>"></script>
<script type="text/javascript">
//	<![CDATA[
	window.addEvent('domready', function() {
		$jsmart(function($){
			var $container = $('#position-6');
		
			var curr_layout = '';
			var colW = 0;
			
			//detect layout width
			if ($(window).width() >= 720) {
				curr_layout = 'fixed';
				colW = 120;
			} else if ($(window).width() >= 480) {
				curr_layout = 'fluid';
				colW = $container.width() / 2;
			}else { 
				curr_layout = 'fluid';
				colW = $container.width();
			}
			//init layout masonry
			$container.masonry({
				itemSelector: '.module',
				columnWidth : colW,
				isAnimated: true,
				/*isRTL: true,*/
				isResizable: true
			});
			
			var reloadMasonry = function () {
				$container.masonry( 'reload' );
			};
		
			$('.tabs-container ul.tabs li').bind('click', function(){
				$container.masonry( 'reload' );
			});
			
			divH = divW = 0;
			var masonrybrick = $("#yt_component");
			$(document).ready(function(){
				divW = masonrybrick.width();
				divH = masonrybrick.height();
			});
			function checkResize(){
				var w = masonrybrick.width();
				var h = masonrybrick.height();
				if (w != divW || h != divH) {
					$container.masonry( 'reload' );
					divH = h;
					divW = w;
				}
			}
			$(window).resize(checkResize);
			var timer = setInterval(checkResize, 500);
			//change columnWidth depend on the wrapper width, specify for this template
			$(window).bind( 'smartresize.masonry', function() { 
				//detect layout width
				if ($container.width() >= 720) {
					//fix width layout - reload one time			
					if (curr_layout != 'fixed') {
						curr_layout = 'fixed';
						$container.masonry( 'option', { columnWidth: 120, isResizable: true } );
						$container.masonry( 'reload' );
					}
				} else if ($container.width() >= 480){
					//update column width
					$container.masonry( 'option', { columnWidth: $container.width() / 2, isResizable: false } );
					//reload layout
					$container.masonry( 'reload' );
					curr_layout = 'fluid';
				}else {
					//update column width
					$container.masonry( 'option', { columnWidth: $container.width(), isResizable: false } );
					//reload layout
					$container.masonry( 'reload' );
		
					curr_layout = 'fluid';
				}
				
		  });

		}); 
	});
	window.addEvent('load', function() {
		$jsmart(function($){
			$('#content_main').masonry( 'reload' );
		});
	});
//	]]>
</script>