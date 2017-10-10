<?php
/**
 * @package Sj Carousel
 * @version 2.5
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2012 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 * 
 */
defined('_JEXEC') or die;

if(!empty($list)){?>
    <div id="myCarousel" class="carousel slide"><!-- Carousel items -->
	    <div class="carousel-inner">
	    <?php $i=0; foreach($list as $item){$i++;?>
		    <div class="<?php if($i==1){echo "active";}?> item  slide-<?php echo $i;?>">
		    	<img src="<?php echo modCarouselHelper::html_image($item->images, array('image_intro', 'image_fulltext'), false);?>" alt="<?php echo $item->title;?>" />
	    		<div class="carousel-caption">
		    		<h4><?php echo $item->title;?></h4>
		    		<p><?php echo $item->displayIntrotext;?></p>
	    		</div>
		    </div>
	    <?php }?>
	    </div><!-- Carousel nav -->
	    <a class="carousel-control left" href="#myCarousel" data-slide="prev"></a>
	    <a class="carousel-control right" href="#myCarousel" data-slide="next"></a>
    </div>
<?php }else{ echo JText::_('Has no content to show!');}?>
