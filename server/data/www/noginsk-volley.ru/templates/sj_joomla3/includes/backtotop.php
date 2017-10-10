<?php
/*
 * ------------------------------------------------------------------------
 * Yt FrameWork for Joomla 3.0
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
<a id="yt-totop" href="#">Scroll to top</a>
<script type="text/javascript">
	$jsmart(function($){
		// back to top
		$("#yt-totop").hide();
		$(function () {
			var wh = $(window).height();
			var whtml =  $(document).height();
			$(window).scroll(function () {
				if ($(this).scrollTop() > whtml/10) {
					$('#yt-totop').fadeIn();
				} else {
					$('#yt-totop').fadeOut();
				}
			});
			$('#yt-totop').click(function () {
				$('body,html').animate({
					scrollTop: 0
				}, 800);
				return false;
			});
		});
		// end back to top
	});
</script>