"use strict";

(function ($) {
    var SLIDER_BASE64 = 'PGEgdGFyZ2V0PSJfYmxhbmsiIHRpdGxlPSJodHRwOi8vam9vbWx5LnJ1L3NsaWRlciIgaHJlZj0iaHR0cDovL2pvb21seS5ydS9zbGlkZXIiIHN0eWxlPSJvcGFjaXR5OiAwLjQ7IGRpc3BsYXk6IGJsb2NrOyB3aWR0aDogMjBweDsgaGVpZ2h0OiAyMHB4OyBwb3NpdGlvbjogYWJzb2x1dGU7IGJvdHRvbTogNXB4OyByaWdodDogNXB4OyB6LWluZGV4OiA5OTk7IGJhY2tncm91bmQ6IHVybCgnZGF0YTppbWFnZS9wbmc7YmFzZTY0LGlWQk9SdzBLR2dvQUFBQU5TVWhFVWdBQUFCQUFBQUFRQ0FZQUFBQWY4LzloQUFBQUJITkNTVlFJQ0FnSWZBaGtpQUFBQUVGSlJFRlVPSTFqWUtBUU1ESXdNREQ4NzJINGoxV3lCQ0pmZkpJSnEzeXYrVDlHSmtwZE1HckFZQUNNMkFTZHAzekFHdTk3Y3dRdzFBOThJQTREQXlnR0FKOWVDeGZVTzloUUFBQUFBRWxGVGtTdVFtQ0MnKSAycHggY2VudGVyIG5vLXJlcGVhdCByZ2JhKDI1NSwgMjU1LCAyNTUsIDAuNzAxOTYxKTsiIG9ubW91c2VvdmVyPSJ0aGlzLnN0eWxlLm9wYWNpdHk9MTsiIG9ubW91c2VvdXQ9InRoaXMuc3R5bGUub3BhY2l0eT0wLjQ7Ij48L2E+',
            defaults = {
            timer:              false,
            width:              '100%',
            height:             '100%',
            controlButtons:     false,
            bullets:            false,
            loop:               false,
            effect:             false,
            captionOnHover:     false,
            slides:             [],
            current:            1,
			aspectRatio:		false,
            afterShow:          function(index, slide) {}
        };
    
    var methods = {
        setOption: function (option, value) {
            var $this   = $(this),
                data    = $this.data('slider');
            data.options[option] = value;
            return true;     
        },
        getCurrentSlideIndex: function() {
            return $(this).find(".slider-content").index($(this).find(".slider-content.checked")) + 1;
        },
        getOptions: function() {
            return $(this).data('slider').options;
        },
        remove: function() {
            var $this = $(this),
                data = $this.data('slider');
            clearInterval(data.timer);
            $this.attr("class", "").addClass("joomly-slider");
            $this.off().removeData().empty();
            return false;
        },
        stop: function() {
            var $this = $(this),
                data = $this.data('slider');
            $this.addClass("slider-stopped");
            if (data.options.timer) {
                clearInterval(data.timer);
            }
            return false;
        },
        play: function() {
            var $this = $(this);
            $this.removeClass("slider-stopped");
            methods._setTimer($this);
            return false;
        },
        _parseTypes: function(options) {
            $.each(options, function(option, value){
                switch (option) {
                    case 'controlButtons':
                    case 'bullets':
                    case 'loop':
                    case 'captionOnHover':
					case 'aspectRatio':
                        options[option] = Boolean(value);
                        break;
                    case 'width':
                    case 'height':
                        var val;
                        value = value.toString(10);
                        if ((value.toString(10)).slice(-1) === '%') {
                            val  = parseFloat(value.slice(0, -1), 10) + "%";
                        } else if (value.slice(-2) === 'px') {
                            val  = parseFloat(value.slice(0, -2), 10) + "px";
                        } else {
                            val  = parseFloat(value, 10) || defaults[option];
                        }
                        options[option] = val;
                        break;
                    case 'timer':
                    case 'current':
                        options[option] = parseInt(value, 10) || defaults[option];
                        break;
                    case 'effect':
                        options[option] = value === "0" ? false : value;
                        break;
                }
            });
            return options;
        },
		_resize: function (elem) {
			var data    	= elem.data('slider'),
				opt_w		= data.options.width.toString(10),
                opt_h   	= data.options.height.toString(10),
				percent_w	= false,
				percent_h	= false,
				bar_h   	= 0,
				width_abs,
				height_abs,
				width,
				height;
			if (opt_w.slice(-1) === '%') {
				width_abs  	= parseFloat(opt_w.slice(0, -1), 10);
				percent_w	= true;
			} else if (opt_w.slice(-2) === 'px') {
				width_abs  	= parseFloat(opt_w.slice(0, -2), 10);
			} else {
				width_abs  	= parseFloat(opt_w, 10);
			}
			if (data.options.aspectRatio) {
				height_abs 	=  100 / data.height_aspectRatio;
				percent_h	= true;
			} else {
				if (opt_h.slice(-1) === '%') {
					height_abs  = parseFloat(opt_h.slice(0, -1), 10);
					percent_h	= true;
				} else if (opt_h.slice(-2) === 'px') {
					height_abs  = parseFloat(opt_h.slice(0, -2), 10);
				} else {
					height_abs  = parseFloat(opt_h, 10);
				}
			}
			if (percent_w) {
				width = width_abs + "%";
			} else {
				width = width_abs;
			}
			if (percent_h) {
				elem.css({width: data.options.width});
				height = height_abs / 100 * elem.width();
			} else {
				height = height_abs;
			}
			elem.css({width: width, height: height});
			elem.find(".slider-base-container").css({height: "100%"});
		},
        init: function (params) {
			return this.each(function(){
				var options = $.extend({}, defaults, params),
					$this   = $(this),
					data    = $this.data('slider'),
					bar_h   = 0;
				options = methods._parseTypes($.extend({}, options, $this.data()));
				if (!data) {
					$this.data('slider', {
						target      : $this,
						options     : options,
						timer       : null,
						direction   : 0,
						uid         : 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
							var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
							return v.toString(16);
						})
					});
					data = $this.data('slider');
					
					if (data.options.aspectRatio) {
						$this.addClass("aspect-ratio");
					}
					if ($this.html() !== base64_decode(SLIDER_BASE64)) {
						$this.html(base64_decode(SLIDER_BASE64));
					}
					$this.attr("data-uid", data.uid).append('<div class="slider-base-container"><div class="slider-container"><ul></ul></div></div>');
					var container           = $this.find(".slider-container>ul"),
						labels_container    = $('<div class="control-labels'+(!data.options.bullets ? " hide" : "")+'">'),  
						labels              = $('<div class="control-labels-circle">');
					$.each(data.options.slides, function(index, slide){
						var $li =  $('<li />');
						$li.append('<div class="slider-content" data-slider="slider-check-'+(index+1)+"_"+data.uid+'"></div>');
						if (typeof slide.caption != "undefined") {
							$li.find(".slider-content").append('<div class="slider-caption">'+slide.caption+'</div>');
						}
						$li.find(".slider-content").append(slide.html);
						if (typeof slide.link != "undefined") {
							$li.bind("click", function(){
								if (!$this.hasClass("touch-animate-reset") && !$this.hasClass("slider-animate")) {
									window.open(
										slide.link,
										'_blank'
									);
								}
							}).addClass("slide-link");
						}
						container.append($li);
						var label_bar = $('<label for="slider-check-'+(index+1)+"_"+data.uid+'">');
						labels
							.append('<input type="radio" name="slider-check_'+data.uid+'" id="slider-check-'+(index+1)+"_"+data.uid+'">')
							.append('<label for="slider-check-'+(index+1)+"_"+data.uid+'">');
					});
					labels_container.append(labels);
					$this.append(labels_container);
					if (data.options.effect) {
						$this.addClass(data.options.effect);
					}
					if (data.options.captionOnHover) {
						$this.find(".slider-caption").addClass("caption-hover");
					}
					switch (data.options.effect) {
						case "effect3":
						case "effect4":
							var prev = data.options.current > 1 ? data.options.current - 1 : (data.options.loop ? data.options.slides.length : false),
								next = data.options.current < data.options.slides.length ? data.options.current + 1 : (data.options.loop ? 1 : false);
							$this.find(".slide-prev").removeClass("slide-prev");
							$this.find(".slide-next").removeClass("slide-next");
							if (prev !== false) {
								$this.find('.slider-content[data-slider="slider-check-'+prev+"_"+data.uid+'"]').addClass("slide-prev");
							}
							if (next !== false) {
								$this.find('.slider-content[data-slider="slider-check-'+next+"_"+data.uid+'"]').addClass("slide-next");
							}
							break;
					}
					$this.addClass("slider-init");
					
					$this.find(".control-labels-circle input").bind("click", function() {
						var current = $this.find('.slider-content[data-slider="'+$(this).attr("id")+'"]'),
							checked = $this.find(".slider-content.checked:first"),
							sl1     = $this.find(".control-labels-circle input").index(this);
						if ($this.hasClass("slider-animate")) {
							return false;
						}
						
						if (!current.hasClass("checked")) {
							clearInterval(data.timer);
							$this.removeClass("slider-stopped");
						} else {
							if (!$this.hasClass("slider-stopped")) {
								if (data.options.timer) {
									$this.addClass("slider-stopped");
									clearInterval(data.timer);
								}
							} else {
								$this.removeClass("slider-stopped");
								methods._setTimer($this);
							}
							return false;
						}
						if (current.hasClass("checked")) {
							return false;
						}
						
						if (data.options.controlButtons && !data.options.loop) {
							if ($this.find("> .control-labels .control-labels-circle input").index(this) !== $this.find("> .control-labels .control-labels-circle input").size() - 1) {
								$this.find("> .slider-base-container > .button-next").removeClass("hide");
							} else {
								$this.find("> .slider-base-container > .button-next").addClass("hide");
							}
							if ($this.find("> .control-labels .control-labels-circle input").index(this) !== 0) {
								$this.find("> .slider-base-container > .button-prev").removeClass("hide");
							} else {
								$this.find("> .slider-base-container > .button-prev").addClass("hide");
							}
						}
						var isInit = $this.hasClass("slider-init");
						if (data.options.effect !== false && !isInit) {
							$this.addClass("slider-animate");
							var sl2     = $this.find(".slider-content").index($(".slider-content.checked")),
								count   = $this.find(".slider-container>ul>li").size();
							var f       = sl2 < sl1 ? -1 : 1;
							if ((sl1 === 0 && sl2 === count - 1 && data.direction === 1) || (sl2 === 0 && sl1 === count - 1 && data.direction === -1)) {
								f *= -1;
							}
							data.direction = 0;
							current.addClass("checked");
							switch (data.options.effect) {
								case "effect4":
									var type = 'Y';
									break;
								case "effect3":
									var type = 'X';
									break;
							}
							$this.find(".slider-base-container").css("zIndex", 1);
							switch (data.options.effect) {
								case "effect3":
								case "effect4":
									$this.find(".slide-prev").removeClass("slide-prev");
									$this.find(".slide-next").removeClass("slide-next");
									if (f > 0) {
										current.addClass("slide-prev");
									} else {
										current.addClass("slide-next");
									}
									
									$this.find(".slider-container").transition({
										'-moz-transform': 'translate'+type+'(' + (f * 100) + '%)',
										'-o-transform': 'translate'+type+'(' + (f * 100) + '%)',
										'-webkit-transform': 'translate'+type+'(' + (f * 100) + '%)',
										'transform': 'translate'+type+'(' + (f * 100) + '%)',
										duration: 500,
										complete: function(){
											var prev = sl1 > 0 ? sl1 : (data.options.loop ? data.options.slides.length : false),
												next = sl1 < data.options.slides.length - 1 ? sl1 + 2 : (data.options.loop ? 1 : false);
											$this.find(".slide-prev").removeClass("slide-prev");
											$this.find(".slide-next").removeClass("slide-next");
											if (prev !== false) {
												$this.find('.slider-content[data-slider="slider-check-'+prev+"_"+data.uid+'"]').addClass("slide-prev");
											}
											if (next !== false) {
												$this.find('.slider-content[data-slider="slider-check-'+next+"_"+data.uid+'"]').addClass("slide-next");
											}
											$(this).css({
												'-moz-transform': 'translate'+type+'(0%)',
												'-o-transform': 'translate'+type+'(0%)',
												'-webkit-transform': 'translate'+type+'(0%)',
												'transform': 'translate'+type+'(0%)'
											});
											$this.removeClass("slider-animate");
											if (sl1 !== sl2) {
												$this.find(".slider-base-container").css("zIndex", "");
												checked.removeClass("checked");
											}
											if (data.uid === $this.attr("data-uid")) {
												methods._setTimer($this);
											}
										}
									});
									break;
								default:
									$this.removeClass("slider-animate");
									$this.find(".slider-content.checked").removeClass("checked");
									$this.find('.slider-content[data-slider="'+$(this).attr("id")+'"]').addClass("checked");
									if (isInit) {
										$this.removeClass("slider-init");
									}
									methods._setTimer($this);
									break;
							}
						} else {
							$this.find(".slider-content.checked").removeClass("checked");
							$this.find('.slider-content[data-slider="'+$(this).attr("id")+'"]').addClass("checked");
							if (isInit) {
								$this.removeClass("slider-init");
							}
							methods._setTimer($this);
						} 
					});
					
					if (data.options.controlButtons) {
						$this.find(".slider-base-container").append('<span class="control-buttons button-prev"><span class="control-button"></span></span><span class="control-buttons button-next"><span class="control-button"></span></span>');
					}
					$this.find(".slider-base-container > .button-prev").bind("click", function(){
						$this.jSlider("prev");
					});
					$this.find(".slider-base-container > .button-next").bind("click", function(){
						$this.jSlider("next");
					});
					
					$this.find('.control-labels #slider-check-'+(data.options.current)+"_"+data.uid).click();
					
					$(window).resize(function() {
						methods._resize($this);
					});
					data.height_aspectRatio = null;
					if (data.options.aspectRatio) {
						var flag = false;
						$this.find(".grid").each(function(){
							if ($(this).css('background-image') !== "none") {
								var img = new Image;
								img.src = $(this).css('background-image').replace(/url\(\"?|\"?\)$/ig, "");
								$(img).load(function () {
									data.height_aspectRatio	= img.width / img.height;
									methods._resize($this);
								});
								flag = true;
								return false;
							}
						});
						if (!flag) {
							$this.css({width: data.options.width});
							var opt_h 		= data.options.height.toString(10),
								percent_h 	= false,
								height_abs;
							if (opt_h.slice(-1) === '%') {
								height_abs  = parseFloat(opt_h.slice(0, -1), 10);
								percent_h	= true;
							} else if (opt_h.slice(-2) === 'px') {
								height_abs  = parseFloat(opt_h.slice(0, -2), 10);
							} else {
								height_abs  = parseFloat(opt_h, 10);
							}
							data.height_aspectRatio	= $this.width() / (percent_h ? 100 / height_abs : height_abs);
							methods._resize($this);
						}
					} else {
						methods._resize($this);
					}
				}
            });
        },
        _setTimer: function(elem) {
            if (elem.hasClass("slider-stopped")) {
                return false;
            }
            var data = elem.data('slider');
            if (data.options.timer && !elem.hasClass("slider-animate") && !elem.hasClass("slider-touch")) {
                clearInterval(data.timer);
                data.timer = setInterval(function(){
                    elem.jSlider("next");
                }, data.options.timer * 1000);
            }
            data.options.afterShow(elem.find(".slider-content").index(elem.find(".slider-content.checked")) + 1, elem.find(".slider-content.checked"));
            return false;
        },
        next: function () {
            var $this   = $(this),
                data    = $this.data('slider'),
                next    = $this.find(".control-labels-circle input:checked").next().next(".control-labels-circle input");
            data.direction = 1;
            if (next.size() === 0) {
                if (data.options.loop) {
                    next    = $this.find(".control-labels-circle input:first");
                    next.click();
                }
            } else {
                next.click();
            }
            return false;
        },
        prev: function () {
            var $this   = $(this),
                data    = $this.data('slider'),
                prev    = $this.find(".control-labels-circle input:checked").prev().prev(".control-labels-circle input");
            data.direction = -1;
            if (prev.size() === 0) {
                if (data.options.loop) {
                    prev    = $this.find(".control-labels-circle input:last");
                    prev.click();
                }
            } else {
                prev.click();
            }
            return false;
        },
        goTo: function (number) {
            var $this   = $(this),
                data    = $this.data('slider');
            $this.find('.control-labels #slider-check-'+number+"_"+data.uid).click();
            return false;
        }
    };

    function polarToCartesian(centerX, centerY, radius, angleInDegrees) {
        var angleInRadians = (angleInDegrees-90) * Math.PI / 180.0;

        return {
            x: centerX + (radius * Math.cos(angleInRadians)),
            y: centerY + (radius * Math.sin(angleInRadians))
        };
    }

    function describeArc(x, y, radius, startAngle, endAngle){

        var start = polarToCartesian(x - 0.001, y, radius, endAngle);
        var end = polarToCartesian(x, y, radius, startAngle);

        var arcSweep = endAngle - startAngle <= 180 ? "0" : "1";

        var d = [
            "M", start.x, start.y, 
            "A", radius, radius, 0, arcSweep, 0, end.x, end.y
        ].join(" ");

        return d;       
    }

    $.fn.jSlider = function (method) {
		if (methods[method]) {
			return methods[ method ].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Method "' + method + '" does not exist on jQuery.joomly-slider');
		}
    };

    function base64_decode( data ) {
        var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
        var o1, o2, o3, h1, h2, h3, h4, bits, i=0, enc='';
        do {
            h1 = b64.indexOf(data.charAt(i++));
            h2 = b64.indexOf(data.charAt(i++));
            h3 = b64.indexOf(data.charAt(i++));
            h4 = b64.indexOf(data.charAt(i++));

            bits = h1<<18 | h2<<12 | h3<<6 | h4;

            o1 = bits>>16 & 0xff;
            o2 = bits>>8 & 0xff;
            o3 = bits & 0xff;

            if (h3 == 64)     enc += String.fromCharCode(o1);
            else if (h4 == 64) enc += String.fromCharCode(o1, o2);
            else               enc += String.fromCharCode(o1, o2, o3);
        } while (i < data.length);
        return enc;
    }
})(jQuery);