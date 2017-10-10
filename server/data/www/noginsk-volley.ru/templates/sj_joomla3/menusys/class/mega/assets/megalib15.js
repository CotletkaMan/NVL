/** 
 * YouTech menu javascript file.
 * 
 * @author The YouTech JSC
 * @package menusys
 * @filesource megalib15.js
 * @license Copyright (c) 2011 The YouTech JSC. All Rights Reserved.
 * @tutorial http://www.smartaddons.com
 * @requires Mootools 1.1
 * @requires Joomla 1.5.xx
 */
/**
 * Differents between mootools 1.1 --> and  mootools 1.2 or heigher
 * Element.getTag() -> Element.get('tag')
 * Element.remove() -> Element.dispose();
 * Fx.Styles -> Fx.Morph
 * Fx.stop() -> Fx.cancel()
 * window.ie7 -> Browser.Engine.trident5
 */

var debugmode = false;
var log = function (s) {
	if (debugmode) {
		console.log(s);
	}
}

var YTMega = new Class({
	initialize: function (context, options) {
		if (!$defined($(context))) {
			return false;
		}
		this.options = $extend({
			duration: 800,
			transition: Fx.Transitions.linear,
			maxz: 0,
			slide: 0,
			wrapperClass: 'yt-main',
			activeSlider: true,
			debug: false
		}, options);
		if ($(context).getTag() == 'ul') {
			this.root = $(context);
		} else {
			this.root = $(context).getElement('ul');
		}
		var p = this.root.getParent();
		while (p && !p.hasClass(this.options.wrapperClass)) {
			p = p.getParent();
			if (p.getTag()=='body'){
				break;
			}
		}
		this.wrapper = p;
		this.subs = [];
		this.createDropMenu(this.root);
		this.updateMaxZindex(this.root);
		log('max zindex: ' + this.options.maxz);
		this.createActiveSlide();
	},
	createDropMenu: function (r) {
		if (r._created) return;
		r._created = true;
		log('create function');
		if (r.getTag()=='ul'){
			var c = r.getChildren().filter(function (li) {
				return li.getTag() == 'li';
			});
		} else if (r.getTag()=='div' && r.hasClass('mega-content')){
			log('find subnavi:');
			var mg = r.getElements('ul.subnavi');
			log('childs: ');
			log(mg);
			var c = new Array();
			for(var i=0; i<mg.length; i++) {
				var c2 = mg[i].getChildren().filter(function (li) {
					return li.getTag() == 'li';
				});
				log('childs[' + i + ']: ');
				log(c2);
				for(var j=0; j<c2.length; j++) {
					c.push(c2[j]);
				}
			}
			log('all li childs: ');
			log(c);
		}
		//log(c);
		//log(c.length);
		if (c.length <= 0) {
			return false;
		}
		
		for (var i = 0; i < c.length; i++) {
			var li = c[i]; // li
			var ul = li.getElement('div.mega-content');
			if (ul) {
				ul.drop = r == this.root;
			}
			//log('- - - - - - - - - - - - - - ->');
			//log(li,p,ul);
			//log('<- - - - - - - - - - - - - - -');
			this.createMenuEffect(li, ul);
			if (ul) {
				this.createDropMenu(ul);
			}
		}
	},
	createMenuEffect: function (li, ul) {
		log('createMenuEffect for ' + (ul ? ul.id : 'li has not child'));
		var objectReference = this;
		var ulexists = $defined(ul);
		if (ulexists) {
			ul.li = li;
			this.installFx(ul);
		}
		li.addEvent('mouseenter', function (e) {
			//if (objectReference.hasOpenning) return;
			log('enter parent of ' + (ul ? ul.id : 'li has not child'));
			li.addClass('hover');
			if (objectReference.options.activeSlider && li._sliderFx){
				objectReference.sliderMove(li, objectReference.options.duration);
			}
			if (ulexists) {
				objectReference.showChild(ul);
			}
		});
		li.addEvent('mouseleave', function (e) {
			log('leave parent of ' + (ul ? ul.id : 'li has not child'));
			li.removeClass('hover');
			if (objectReference.options.activeSlider && objectReference.origItem && li._sliderFx){
				objectReference.sliderMove(objectReference.origItem, objectReference.options.duration);
			}
			if (ulexists) {
				if (ul.pinned) return false;
				objectReference.hideChild(ul);
			}
		});
	},
	showChild: function (ul) {
		log(ul.id + ' will be show.');
		if (ul.openning == 1) return;
		if (ul.wrapper.getParent() == ul.li) {
			if (ul.drop){
				var dh = parseFloat(ul.wrapper.getStyle('height')) - parseFloat(ul.wrapper.__height);
				var isReadyOpenned = Math.abs(dh) < 1;
			}else {
				var dw = parseFloat(ul.wrapper.getStyle('width')) - parseFloat(ul.wrapper.__width);
				var isReadyOpenned = Math.abs(dw) < 1;
			}	
			if (isReadyOpenned) {
				log('ready open.');
				return;
			}
		}
		log(ul.id + ' is not openning, prepare show it.');
		ul.status = 'show';
		ul.openning = 1;
		ul.closing = 0;
		this.subs.push(ul);
		this.updateZ();
		this.closeAnother(ul);
		this.preAnimation(ul);
		if (!ul._adjust) this.adjustPosition(ul);

		if (ul._installedFx) {
			ul.subFx.stop();
			if (ul.drop) {
				var currHeight = parseFloat(ul.wrapper.getStyle('height'));
				var newDuration = parseFloat((parseFloat(ul.wrapper.__height) - currHeight) / parseFloat(ul.wrapper.__height) * this.options.duration);
				ul.subFx.setOptions({
					duration: newDuration
				});
				ul.subFx.start({
					'height': ul.wrapper.__height
				});
			} else {
				var currWidth = parseFloat(ul.wrapper.getStyle('width'));
				var newDuration = parseFloat((parseFloat(ul.wrapper.__width) - currWidth) / parseFloat(ul.wrapper.__width) * this.options.duration);
				ul.subFx.setOptions({
					duration: newDuration
				});
				ul.subFx.start({
					'width': ul.wrapper.__width
				});
			}
		} else {
			ul.addClass('unhide');
		}
	},
	hideChild: function (ul) {
		log(ul.id + ' going to hide.');
		if (ul.closing == 1) return;
		log(ul.id + ' is not closing, prepare close it.');
		ul.status = 'hide';
		ul.openning = 0;
		ul.closing = 1;
		
		for(var z=0; z<this.subs.length; z++) {
			if (this.subs[z]==ul){
				this.subs.splice(z,1);
				z--;
			}
		}
		this.updateZ();
		
		this.closeAnother(ul);
		this.preAnimation(ul);

		if (ul._installedFx) {
			ul.subFx.stop();

			var currHeight = parseFloat(ul.wrapper.getStyle('height'));
			var newDuration = parseFloat(currHeight / parseFloat(ul.wrapper.__height) * this.options.duration);
			ul.subFx.setOptions({
				duration: newDuration
			});
			if (ul.drop) {
				ul.subFx.start({
					'height': 0
				});
			} else {
				ul.subFx.start({
					'width': 0
				});
			}
		} else {
			ul.removeClass('unhide');
		}
	},
	closeAnother: function (ul) {
		log('close another ' + ul.id);
		var root = this.root;
		var subs = this.root.getElements('div.mega-content');
		log('subs.length before=' + subs.length);
		for (var i = 0; i < subs.length; i++) {
			//log('check '+allsub[i].id);
			if (ul == subs[i]) {
				log('always remove ' + ul.id);
				subs.splice(i, 1);
				i--;
			} else {
				if (ul.status == 'show') {
					var p = ul.getParent();
					while (p != root) {
						if (p == subs[i]) {
							log(subs[i].id + ' realy is parent of ' + ul.id + ', remove!');
							subs.splice(i, 1);
							i--;
						}
						p = p.getParent();
					}
				} else if (ul.status == 'hide') {
					// remove all sub is not child of ul
					// to close sub before ul start hide fx
					var ischild = false;
					var p = subs[i];
					while (p != root) {
						if (p == ul) {
							//log(subs[i].id + ' realy is child of ' + ul.id + ',keep it!');
							ischild = true;
							break;
						} else {
							p = p.getParent();
						}
					}
					if (!ischild) {
						subs.splice(i, 1);
						i--;
					}
				}
			}
		}
		log('subs.length  after=' + subs.length);
		for (var i = 0; i < subs.length; i++) {
			var tmp = subs[i];
			if (tmp.closing == 1 || tmp.openning == 1) {
				log(tmp.id + ' is closing or openning, try close it immediate');
				tmp.subFx.stop();
				tmp.status = 'hide';
				if (tmp.drop) {
					tmp.wrapper.setStyle('height', 0);
				} else {
					tmp.wrapper.setStyle('width', 0);
				}
				this.completeCallback(tmp).call();
				// remove from open list
				for(var z=0; z<this.subs.length; z++) {
					if (this.subs[z]==ul){
						this.subs.splice(z,1);
						z--;
					}
				}
			}
		}
		this.updateZ();	
	},
	adjustPosition: function (ul) {
		log('call adjustPosition ' + ul.id);
		ul._adjust = true;
		var isrtl = this.root.hasClass('navirtl');
		var wrapper = this.wrapper.getCoordinates();
		var lihover = ul.li.getCoordinates();
		var current = ul.wrapper.getCoordinates();
		current.width  = parseFloat(ul.wrapper.__width);
		current.height = parseFloat(ul.wrapper.__height);
					
		log('--------wrapper--------');			
		log('--------top: '+wrapper.top);
		log('------right: '+wrapper.right);
		log('-----bottom: '+wrapper.bottom);
		log('-------left: '+wrapper.left);				
		log('------width: '+wrapper.width);
		log('-----height: '+wrapper.height);
		log('--------lihover--------');			
		log('--------top: '+lihover.top);
		log('------right: '+lihover.right);
		log('-----bottom: '+lihover.bottom);
		log('-------left: '+lihover.left);				
		log('------width: '+lihover.width);
		log('-----height: '+lihover.height);
		log('--------current--------');			
		log('--------top: '+current.top);
		log('------right: '+current.right);
		log('-----bottom: '+current.bottom);
		log('-------left: '+current.left);				
		log('------width: '+current.width);
		log('-----height: '+current.height);
		if (!ul.drop) {
			var nxTop = parseFloat(lihover.height * 0.2);
			ul.wrapper.setStyle('top', nxTop);
					
			if (!isrtl) {
				log('is LTR');				
				
				var relativeLeft = Math.round(lihover.width * 0.95);
				var showOnRight = lihover.left + relativeLeft + current.width <= wrapper.right;
				if (showOnRight) {
					log('show ' + ul.id + ' on right side');
					ul.wrapper.setStyle('left', relativeLeft);
					ul.wrapper.addClass('toright');
				} else {
					log('show ' + ul.id + ' on left side');
					ul.wrapper.setStyle('right', relativeLeft);
					ul.wrapper.addClass('toleft');
				}
			} else {
				log('is RTL');				
				
				var relativeRight = Math.round(lihover.width * 0.95);
				var showOnLeft = lihover.right - relativeRight - current.width >= wrapper.left;
				
				if (showOnLeft) {
					log('show ' + ul.id + ' on left side');
					ul.wrapper.setStyle('right', relativeRight);
					ul.wrapper.addClass('toleft');
				} else {
					log('show ' + ul.id + ' on right side');
					ul.wrapper.setStyle('left', relativeRight);
					ul.wrapper.addClass('toright');
				}
			}
		} else {
			// drop level			
			if (!isrtl) {
				log('is LTR');
				if (wrapper.width < current.width) {
					log('case 1: bigger wrapper -> make it center');
					var toLeft = wrapper.left - (current.width - wrapper.width) / 2;
					var nxLeft = 0 - (lihover.left - toLeft);					
					ul.wrapper.setStyle('left', nxLeft);
				} else if (lihover.left+current.width > wrapper.right) {
					log('case 2: over right -> make it same right');
					var toLeft = wrapper.right-current.width;
					var nxLeft = 0 - (lihover.left - toLeft);
					ul.wrapper.setStyle('left', nxLeft);
				} else {
					log('case 3: inside wrapper -> left=0');
					ul.wrapper.setStyle('left', 0);
				}
			} else {			
				log('is LTR');
				if (wrapper.width < current.width) {
					log('case 1: bigger wrapper -> make it center');
					var toRight = wrapper.right + (current.width - wrapper.width) / 2;
					var nxRight = 0 + (lihover.right - toRight);
					ul.wrapper.setStyle('right', nxRight);
				} else if (lihover.right-current.width < wrapper.left) {
					log('case 2: over left -> make it same left');
					var toRight = wrapper.left+current.width;
					var nxRight = 0 + (lihover.right - toRight);
					ul.wrapper.setStyle('right', nxRight);
				} else {
					log('case 3: inside wrapper -> right=0');
					ul.wrapper.setStyle('right', 0);
				}
			}
			ul.wrapper.addClass('drop');
		}
	},
	installFx: function (ul) {
		if (!$defined(ul)) {
			return false
		}
		log('init fx ' + ul.id);
		try {
			ul._width = ul.getStyle('width');
			ul._height = ul.getStyle('height');
			ul._pad_b = ul.getStyle('padding-bottom');
			ul._pad_r = ul.getStyle('padding-right');
			ul._mar_t = ul.getStyle('margin-top');
			if (!$defined(ul.wrapper)) {
				ul.wrapper = new Element('div', {
					'class': 'fxcontent'
				});
				ul.wrapper.addClass(this.options.slide ? 'slide' : 'scroll');
				ul.wrapper.__width = (parseFloat(ul._width) + parseFloat(ul._pad_r)) + 'px';
				ul.wrapper.__height = (parseFloat(ul._height) + parseFloat(ul._pad_b)) + 'px';
				ul.wrapper.setStyle('width', ul.wrapper.__width);
				ul.wrapper.setStyle('height', ul.wrapper.__height);
				ul.wrapper.setStyle('margin-top', ul._mar_t);
				if (ul.drop) {
					ul.wrapper.__bar = new Element('div', {
						'class': 'fxcontent-bar'
					}).injectInside(ul.wrapper);
					ul.wrapper.__bar.setStyle('background-color',ul.getStyle('background-color'));
					ul.wrapper.__bar.setStyle('width',ul.wrapper.__width);
					ul.wrapper.__bar.setStyle('height', ul._pad_b);
					ul.wrapper.__bar.__width = ul.wrapper.__width;
				} else {
					ul.wrapper.__bar = new Element('div', {
						'class': 'fxcontent-bar'
					}).injectInside(ul.wrapper);
					ul.wrapper.__bar.setStyle('background-color',ul.getStyle('background-color'));
					ul.wrapper.__bar.setStyle('width', ul._pad_r);
					ul.wrapper.__bar.setStyle('height', ul.wrapper.__height);
					ul.wrapper.__bar.__width = ul._pad_r;
				}
				if (this.options.debug) {
					ul.wrapper.__pinner = new Element('div', {
						'class': 'fxcontent-pinner'
					}).injectInside(ul.wrapper);
					ul.wrapper.__pinner.addEvent('click', function () {
						if (!$defined(ul.pinned) || !ul.pinned) {
							ul.pinned = true;
							this.setStyle('background-color', '#ff0');
						} else {
							ul.pinned = false;
							this.setStyle('background-color', '#f00');
						}
					});
				}
			}
			ul.subFx = new Fx.Styles(ul.wrapper, this.options);
			ul.subFx.setOptions({
				'onStart': this.startCallback(ul),
				'onComplete': this.completeCallback(ul),
				'onCancel': this.stoppedCallback(ul)
			});
			ul._installedFx = true;
		} catch (e) {
			//log('install fail: ' + e.message);
			for (var i in e) {
				log(i + ' = ' + e[i]);
			}
			ul._installedFx = false;
		}
	},
	preAnimation: function (ul) {
		log('preAnim for ' + ul.id);
		if (ul.status == 'show') {
			ul.wrapper.injectInside(ul.li);
			ul.injectTop(ul.wrapper);
			if (ul.drop) {
				ul.setStyle('margin-top', 0);
				ul.wrapper.setStyle('height', 0);
			} else {
				ul.wrapper.setStyle('width', 0);
			}
			ul.addClass('insidefx');
			ul.li.setStyle('position', 'relative');
		} else if (ul.status == 'hide') {

		}
		ul.wrapper.setStyle('overflow', 'hidden');
		ul.wrapper.__bar.setStyle('width', ul.wrapper.__bar.__width);
	},
	startCallback: function (ul) {
		return function () {
			if (ul.status == 'show') {
				ul.openning = 1;
				log('start show ' + ul.id);
			} else if (ul.status == 'hide') {
				ul.closing = 1;
				log('start hide ' + ul.id);
			}
		}
	},
	completeCallback: function (ul) {
		var objRef = this;
		return function () {
			if (ul.status == 'show') {
				log('Finish show ' + ul.id);
				ul.wrapper.setStyle('overflow', '');
				ul.wrapper.__bar.setStyle('width', 0);
				ul.openning = 0;
				objRef.hasOpenning = false;
			} else if (ul.status == 'hide') {
				log('Finish hide ' + ul.id);
				ul.li.removeClass('hover');
				ul.li.setStyle('position', '');
				ul.injectInside(ul.li);
				try {
					ul.li.getElement('div.fxcontent').remove();
				} catch (e) {}
				ul.removeClass('unhide');
				ul.removeClass('insidefx');
				ul.setStyle('margin-top', '');
				ul.closing = 0;
			}
		}
	},
	stoppedCallback: function (ul) {
		return function () {
			log('animation on ' + ul.id + ' is stopped');
		}
	},
	updateMaxZindex: function (el) {
		var thisz = el.getStyle('z-index');
		if (thisz !='auto' && thisz>this.options.maxz){
			this.options.maxz = thisz;		
		}
		var thisc = el.getChildren();
		if (thisc.length>0){
			for(var i=0; i<thisc.length; i++) {
				this.updateMaxZindex(thisc[i]);
			}
		}	
	},
	updateZ: function(){
		$$('.zfix', this.root).removeClass('zfix');
		var zfix = this.options.maxz + 1;
		log('updateZ start: ' + zfix);
		log('subs   length: ' + this.subs.length);
		for(var i=0; i<this.subs.length; i++) {
			this.subs[i].wrapper.addClass('zfix');
			this.subs[i].wrapper.setStyle('z-index', zfix+i*5);
			this.updatechildZ(this.subs[i].wrapper);
		}
	},
	updatechildZ: function(el){
		log('updatechildZ');
		var crrZ = parseInt(el.getStyle('z-index'));
		var c = el.getChildren();
		for(var i=0; i<c.length; i++) {
			if (c[i].hasClass('fxcontent-bar')){
				c[i].setStyle('z-index', (3+crrZ));
			} else if (c[i].hasClass('fxcontent-pinner')){
				c[i].setStyle('z-index', (4+crrZ));
			} else {
				c[i].setStyle('z-index', (1+crrZ));
			}
		}
	},
	createActiveSlide: function(){
		if (this.options.activeSlider && this.root){
			log('-> create lavalamp effect');
			this.origItem = this.root.getElement('li.active');
			
			this.slider = new Element('li', {
				'class': 'active level1 slider'
			}).injectTop(this.root);			
			this.slider.a = new Element('div', {
				'class': 'active level1 separator'
			}).injectTop(this.slider);
			this.sliderFx = new Fx.Styles(this.slider, this.options);
			var item1s = this.root.getChildren();
			var rootleft = this.root.getCoordinates().left;
			for(var i=0; i<item1s.length; i++) {
				if (item1s[i].hasClass('slider')){
					item1s[i].setStyle('z-index', 1+ this.options.maxz);
				} else {
					item1s[i].setStyle('z-index', 2+ this.options.maxz);
					item1s[i].setStyle('background', 'none');
					item1s[i]._sliderFx = true;
					var itemsize = item1s[i].getCoordinates();
					item1s[i]._sliderleft = itemsize.left - rootleft;
					item1s[i]._sliderwidth = itemsize.width;
				}				
			}
			
			if (this.origItem){
				this.origItem.removeClass('active').addClass('orig_active');
				this.origItem.getElement('a.active').removeClass('active').addClass('orig_active');				
				this.slider.setStyle('left',  this.origItem._sliderleft);
				this.slider.setStyle('width', this.origItem._sliderwidth);
			} else {
				this.slider.setStyle('width', '0');
				this.slider.setStyle('top', '-999em');
				this.slider.busy = true;
			}
		}
	},
	sliderMove: function(li, time){
		if (li._sliderFx){
			log('-> sliderMove:' + li + ' in ' + time + '(ms)');
			this.sliderFx.stop();
			if (time && !isNaN(time)){
				this.sliderFx.setOptions({
					duration: time
				});
			}
			if (this.slider.busy){
				var centerpos = li._sliderleft + li._sliderwidth/2;
				this.slider.setStyle('left', centerpos);
				this.slider.setStyle('top', '0');
				this.slider.busy = false;
			}
			this.sliderFx.start({
				'left': li._sliderleft,
				'width': li._sliderwidth
			});
		}
	}
});
/*
window.addEvent('load', function () {
	try {
		var root = $('moonavigator');
		var travel = function(el, i, prefix){
			var nextclass='ul.level'+i;
			var m = el.getElements(nextclass);
			for(var j=0; j<m.length; j++) {
				m[j].setProperty('id', prefix+(j+1));
				travel(m[j],i+1, prefix+(j+1));
			}
		}
		travel(root, 2, 'mg');
		var templateMenu = new YTMega($('moonavigator'),{
			duration: $duration,
			transition: $transition,
			slide: 1,
			wrapperClass: 'yt-main',
			debug: false
		});
	} catch (e) {
		for(var i in e){
			log(i+ ' = ' + e[i] );
		}
	}
	
}); */