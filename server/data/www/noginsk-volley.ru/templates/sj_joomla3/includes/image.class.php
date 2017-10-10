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


jimport("joomla.filesystem.folder");
jimport("joomla.filesystem.file");

if (!class_exists('YtImageJoomlaContent')){
	class YtImageJoomlaContent{ 
		var $thumbnail_mode = 'stretch';
		var $thumbnail_background='';
		var $url_to_resize = '';
		var $width = 200;
		var $height = 200;	
		var $resizePath = '';
		
		function YtImageJoomlaContent() { 
			
		}
		
		function processImage(&$item, $lazyload=0) {		
			$image_arr = array();
			// set $image_arr
			if (!preg_match_all ("/\<img[^\>]*>/", $item->introtext, $image_arr)) return;			
			foreach ($image_arr[0] as $image) {
				// $i_info
				if (!preg_match ('#(<img.*)src\s*=\s*(["\'])(.*?)\2(.*\/?>)#im', $image, $i_info)) continue; 
				// replace all image
				$str = preg_replace ( "/\<img[^\>]*>/", '', $item->introtext );
				$str = preg_replace ( "/<div class=\"mosimage\".*<\/div>/", '', $str );
				//replace all <p></p>
				$str = preg_replace("/<p><\/p>/", '' , $str);
				// trim introtext
				$item->introtext = trim ( $str );			
				$imagSource = JPATH_SITE.DS. str_replace( '/', DS, $i_info[3]);
				if( file_exists($imagSource) && is_file($imagSource) || !strpos($i_info[3], 'http:') ){	
					$imgResizeConfig = array(
						'background' => $this->thumbnail_background,
						'thumbnail_mode' => $this->thumbnail_mode
					);					
					YtUtils::getImageResizerHelper($imgResizeConfig);
					$i_src = YtUtils::resize($i_info[3], $this->width, $this->height, $this->thumbnail_mode);
					if($lazyload==0){
						return $i_info[1]."src=".$i_info[2].$i_src.$i_info[2].$i_info[4];
					}else{
						return $i_info[1]."src=".$i_info[2].JURI::base().'templates/'.JFactory::getApplication()->getTemplate()."/images/white.gif".$i_info[2]." data-original=".$i_info[2].$i_src.$i_info[2].$i_info[4];
					}

				} else{
					return '';
				}		
			}	
		}
	}
}
?>
