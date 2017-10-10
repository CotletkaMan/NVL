//JS script 
var boxes=[];
var ytoverlaywrap = null;
showBox=function(box,focusobj,caller,e){
	if(!ytoverlaywrap){
		ytoverlaywrap=new Element('div',{id:"yt_overlaywrap"}).injectAfter($(box));
		ytoverlaywrap.setStyle('opacity',0.3);
		ytoverlaywrap.addEvent('click',function(e){boxes.each(function(box){
			if(box.status=='show'){
				box.status = 'hide';
				var fx = new Fx.Tween (box);
				fx.pause();
				fx.start ('opacity',box.getStyle('opacity'), 0); box.toggle();
				if (box._caller) box._caller.removeClass ('show');
			}
		},this);
		ytoverlaywrap.setStyle('display','none');
		});
	}
	caller.blur();

	box=$(box);
	if(!box)return;
	if($(caller))box._caller=$(caller);
	if(!boxes.contains(box)){
		boxes.include(box);
	}
	if(box.getStyle('display')=='none'){
		box.setStyles({display:'block',opacity:0}); box.toggle();
	}
	
	if(box.status=='show'){ 
		//hide
		box.status = 'hide';
		var fx = new Fx.Tween (box);
		fx.pause();
		fx.start ('opacity',box.getStyle('opacity'), 0); box.toggle();
		if (box._caller) box._caller.removeClass ('show');
		ytoverlaywrap.setStyle('display','none');
	}else{
		boxes.each(function(box1){
			if (box1!=box && box1.status=='show') {
				box1.status = 'hide';
				var fx = new Fx.Tween (box1);
				fx.pause();
				fx.start ('opacity',box1.getStyle('opacity'), 0); box1.toggle();
				if (box1._caller) box1._caller.removeClass ('show');
			}
		},this);
		box.status = 'show';
		var fx = new Fx.Tween (box,{onComplete:function(){if($(focusobj))$(focusobj).focus();}});
		fx.pause();
		fx.start ('opacity',box.getStyle('opacity'), 1); box.toggle();
		
		if (box._caller) box._caller.addClass ('show');
		ytoverlaywrap.setStyle('display','block');
	}	
}