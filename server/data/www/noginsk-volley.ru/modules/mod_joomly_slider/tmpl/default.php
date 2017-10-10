<div class="joomly-content">
    <ul class="joomly-slider joomly-slider<?php echo $module->id;?>"><a target="_blank" title="http://joomly.net/slider" href="http://joomly.net/slider" style="opacity: 0.4; display: block; width: 20px; height: 20px; position: absolute; bottom: 5px; right: 5px; z-index: 999; background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAEFJREFUOI1jYKAQMDIwMDD872H4j1WyBCJffJIJq3yv+T9GJkpdMGrAYACM2ASdp3zAGu97cwQw1A98IA4DAygGAJ9eCxfUO9hQAAAAAElFTkSuQmCC') 2px center no-repeat rgba(255, 255, 255, 0.701961);" onmouseover="this.style.opacity=1;" onmouseout="this.style.opacity=0.4;"></a></ul>
</div> 
<script type="text/javascript" src="modules/mod_joomly_slider/js/base_slider.min.js"></script>
<script type="text/javascript" src="modules/mod_joomly_slider/js/jquery.transit.min.js"></script>
<script type="text/javascript">
var jQ = jQuery.noConflict();
var options= <?php echo $options->option->options;?>;
jQ(".joomly-slider<?php echo $module->id;?>").jSlider(options);
</script>