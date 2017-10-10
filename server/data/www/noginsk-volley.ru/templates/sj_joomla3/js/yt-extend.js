
$jsmart(document).ready(function($){ //$('#yt_menuposition ul.navi').css('width', $('#yt_menuposition ul.navi').width());
	var currentdevice = '';
	var bootstrap_elements = $('[class*="span"]');
	// Build data
	bootstrap_elements.each ( function(){
		var $this = $(this);
		// With attr data-*
		$this.data();
		// Make the source better view in inspector
    	$this.removeAttr ('data-default data-wide data-normal data-tablet data-stablet data-mobile');
		// For element no attr data-default
		if (!$this.data('default')) 
			$this.data('default', $this.attr('class'));
	
	});
	function updateBootstrapElementClass(newdevice){
  		if (newdevice == currentdevice) return ;
		bootstrap_elements.each(function(){
			var $this = $(this);
			// Default
			if ( !$this.data('default') || (!$this.data(newdevice) && (!currentdevice || !$this.data(currentdevice))) )
				return;
			// Remove current
			if ($this.data(currentdevice)) $this.removeClass($this.data(currentdevice));
			else $this.removeClass ($this.data('default'));
			// Add new
			if ($this.data(newdevice)) $this.addClass ($this.data(newdevice));
			else $this.addClass ($this.data('default'));
		});
    	currentdevice = newdevice;
		setHeight(new Array("position-10","position-11","position-12", "position-13", "position-14"));
	};
	
	function detectDevice () {
		var width = $(window).width();
		if( width > 1200 ){
			return 'wide';
		}else if( width > 980 ){
			return 'normal';
		}else if( width > 600 && width < 800 ){
			return 'stablet';
		}else if(  width > 768 ){
			return 'tablet';
		}else if(  width > 0 ){
			return 'mobile';
		}
		/*
		Mobile portrait (320x480)
		Mobile landscape (480x320)
		Small tablet portrait (600x800)
		Small tablet landscape (800x600)
		Tablet portrait (768x1024)
		Tablet landscape (1024x768)
		*/
	}
	function setHeight (array){
		var h=0;
		for($i=0;$i<array.length; $i++){
			$('#'+array[$i]).css('height', "");
		}
		for($i=0;$i<array.length; $i++){
			if( (h < $('#'+array[$i]).height()) && 
				($('#'+array[$i]).css('display')!="none")
			  ){
				h = $('#'+array[$i]).height();
			}
		}
		//alert(h);
		for($i=0;$i<array.length; $i++){
			$('#'+array[$i]).css('height', h);
		}
	}
  	
  	updateBootstrapElementClass (detectDevice());
  
  	// With window resize 
  	$(window).resize(function(){ 
    	if ($.data(window, 'detect-device-time'))
      		clearTimeout($.data(window, 'detect-device-time'));
			
    	$.data(window, 'detect-device-time', 
      		setTimeout(function(){
        		updateBootstrapElementClass (detectDevice());
      		}, 200)
    	)
  	})
});	

