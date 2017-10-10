"use strict";

var jQ = jQuery.noConflict();
    jQ(document).ready(function(){

    var tmpl                = [],
        slides              = [],
        json_opt            = document.getElementById("opt"),
        opt                 = {},
        edit_slide          = document.getElementById("edit_slide").value,
        create_slide        = document.getElementById("create_slide").value,
        delete_slide        = document.getElementById("delete_slide").value,
        delete_last_slide   = document.getElementById("delete_last_slide").value,
        init_text           = document.getElementById("init_text").value;
   
    if (json_opt.value.length !== 0) {
        var opt= JSON.parse(json_opt.value);
        
        slides = opt.slides;

        jQ("#timer").val(opt.timer);
        jQ("#width").val(opt.width);
        jQ("#height").val(opt.height);
        jQ("#controlButtons").prop("checked", opt.controlButtons);
        jQ("#bullets").prop("checked", opt.bullets);
        jQ("#loop").prop("checked", opt.loop);
        jQ("#effect").val(opt.effect ? opt.effect : false);
        jQ("#captionOnHover").prop("checked", opt.captionOnHover);
		jQ("#aspectRatio").prop("checked", opt.aspectRatio);
    } else 
    {
        jQ("#bullets").prop("checked", true);
        jQ("#height").val(400);
        jQ("#countInBar").val(3);
		jQ("#aspectRatio").prop("checked", true);
        opt.height          = 400;
        opt.countInBar      = 3;
        opt.bullets         = true;
        opt.loop            = true;
		opt.aspectRatio     = false;
        opt.controlButtons  = true;
        slides[0]           = new Object();
        slides[0].html      = '<div class="grid" style="font-size: 30px; text-align: left; color: rgb(255, 255, 255); background-color: rgb(0, 150, 136);"><div><br><br>' + init_text + '</div></div>';
        opt.slides          = slides;   
    }
    setTimeout(function(){
		jQ("#slider1").jSlider(opt);
	}, 100);

    var uid = '';
    jQ.contextMenu({
        selector: '.joomly-slider',
        items: {
            "edit": {
                name: edit_slide,
                callback: function(key, options) {
                    var options = jQ("#slider1").jSlider('getOptions'),
                        current = jQ("#slider1").jSlider('getCurrentSlideIndex'),
						grid 	= jQ("#slider1 .slider-content.checked .grid");
                    jQ("#slide-modal #add").addClass("hide");
                    jQ("#slide-modal #save").removeClass("hide");
					jQ("#caption").val(options.slides[current - 1].caption || "");
					jQ("#link").val(options.slides[current - 1].link || "");
					document.getElementById("text").value = grid.find("div").html().replace(/<br\s*[\/]?>/gi, "\n");
					jQ("#fontSize").val(parseInt(grid.css("fontSize"), 10));
                    jQ("#textAlign").val(grid.css("textAlign"));
                    jQ("#textColor").val(rgb2hex(grid.css("color")));
                    jQ("#textColor").parent().children(".minicolors-swatch").children("span").css("background-color",rgb2hex(grid.css("color")));
                    jQ("#bgColor").val(rgb2hex(grid.css("backgroundColor")));
                    jQ("#bgColor").parent().children(".minicolors-swatch").children("span").css("background-color",rgb2hex(grid.css("backgroundColor")));
                    var bg = grid.css("background-image");
                    if (bg === "none") {
                        document.getElementById("bgImagePreview").setAttribute('src', '');
                    } else {
                        bg = bg.replace('url("','').replace('")','');
                        document.getElementById("bgImagePreview").setAttribute('src', bg);
                        document.getElementById("bgImage").value = bg;
                    }
                    jQ("body").append('<div class="modal-backdrop fade in"></div>');  
                    jQ('#slide-modal').addClass("slider-in").removeClass("hide");
                }
            },
            "add": {
                name: create_slide,
                callback: function(key, options) {
                    jQ("#slide-modal #save").addClass("hide");
                    jQ("#slide-modal #add").removeClass("hide");
                    jQ("#caption").val("");
					jQ("#link").val("");
                    document.getElementById("text").value = "";
                    jQ("#fontSize").val(13);
                    jQ("#textAlign").val("left");
                    jQ("#textColor").val("#000");
                    jQ("#textColor").parent().children(".minicolors-swatch").children("span").css("background-color","#000");
                    jQ("#bgColor").val("#fff");
                    jQ("#bgColor").parent().children(".minicolors-swatch").children("span").css("background-color","#fff");
                    document.getElementById("bgImagePreview").setAttribute('src', '');
                    jQ("body").append('<div class="modal-backdrop fade in"></div>');             
                    jQ('#slide-modal').addClass("slider-in").removeClass("hide");
                }
            },
            "delete": {
                    name: delete_slide,
                    callback: function(key, options) {
                        var options = jQ("#slider1").jSlider('getOptions'),
                            current = jQ("#slider1").jSlider('getCurrentSlideIndex');
                        if (options.slides.length === 1) {
                            alert(delete_last_slide);
                            return false;
                        }
                        slides.splice(current - 1, 1);
                        options.slides  = slides;
                        options.current = current > slides.length ? current - 1 : current;
                        jQ("#slider1").jSlider('remove');
                        jQ("#slider1").jSlider(options);
                        options.current = 1;
                        jQ("#opt").val(JSON.stringify(options));
                        console.log(options);
                    }
                }
        },
        events: {
            show : function(options){
                uid = jQ("#slider1").attr("data-uid");
                if (jQ("#slider1").hasClass("slider-animate")) {
                    return false;
                }
                jQ("#slider1").jSlider('stop');
                
            },
            hide: function(options){
                if (!jQ("#slide-modal").hasClass("hide") || uid !== jQ("#slider1").attr("data-uid")) {
                    return true;
                }
                jQ("#slider1").jSlider('play');
            }
        }
    });

    jQ(function () {
       jQ("#add").bind("click", function(){
            var current = jQ("#slider1").jSlider('getCurrentSlideIndex');
            slides.splice(current, 0, createSlide());
            var options = jQ("#slider1").jSlider('getOptions');
            options.slides  = slides;
            options.current = current + 1;
            jQ("#slider1").jSlider('remove');
            jQ("#slider1").jSlider(options);
			
            options.current = 1;
            jQ("#opt").val(JSON.stringify(options));
            jQ("div.modal-backdrop").remove();
            jQ('#slide-modal').removeClass('slider-in').addClass("hide");
        });
        
        jQ(".slider-close").bind("click", function(){
            if (jQ("#slider1").hasClass("slider-stopped")) {
                jQ("#slider1").jSlider('play');
            }
            jQ("div.modal-backdrop").remove();
             jQ('#slide-modal').removeClass('slider-in').addClass("hide");
         });

        jQ("#delete").bind("click", function(){
            var current = jQ("#slider1").jSlider('getCurrentSlideIndex');
            slides.splice(current - 1, 1);
            var options = jQ("#slider1").jSlider('getOptions');
            options.slides  = slides;
            if (current > slides.length) {
                options.current = current - 1;
            }
            jQ("#slider1").jSlider('remove');
            jQ("#slider1").jSlider(options);
           
            options.current = 1;
            jQ("#opt").val(JSON.stringify(options));
        });
        
        jQ('input:regex(name, controlButtons|bullets|loop|captionOnHover|aspectRatio)').change(function () {
            var val     = jQ(this).is(":checked"),
                name    = jQ(this).attr('name');
            if(jQ('#slider1').jSlider('setOption', name, val)) {
                var options = jQ('#slider1').jSlider('getOptions');
                jQ('#slider1').jSlider('remove');
                jQ("#slider1").jSlider(options);
                var options     = jQ("#slider1").jSlider('getOptions');
                options.current = 1;
                jQ("#opt").val(JSON.stringify(options));
            }
        });
        
        jQ("input:regex(name, timer|width|height)").bind('input', function() {
            var val     = jQ(this).val(),
                name    = jQ(this).attr('name');
            if(jQ('#slider1').jSlider('setOption', name, val)) {
                var options = jQ('#slider1').jSlider('getOptions');
                jQ('#slider1').jSlider('remove');
                jQ("#slider1").jSlider(options);
                var options = jQ("#slider1").jSlider('getOptions');
                options.current = 1;
                jQ("#opt").val(JSON.stringify(options));
            }
        });
        
        jQ("#effect").change(function(){
            var val = jQ(this).val(),
                name = jQ(this).attr('name');
            if(jQ('#slider1').jSlider('setOption', name, val)) {
                var options = jQ('#slider1').jSlider('getOptions');
                jQ('#slider1').jSlider('remove');
                jQ("#slider1").jSlider(options);
                options.current = 1;
                jQ("#opt").val(JSON.stringify(options));
            }
        });
        
        jQ("#save").bind("click", function(){
            slideOptionChange();
            jQ("div.modal-backdrop").remove();
            jQ('#slide-modal').removeClass('slider-in').addClass("hide");
        });
        
        document.getElementById("bgImage").addEventListener("change", readFile);
    });
    
    function createSlide() {
        var text    = jQ("#text").val().replace(/\r?\n/g, '<br />');
        var slide   = jQ('<div />').addClass("grid").append(jQ('<div />').html(text)),
            image   = document.getElementById("bgImagePreview").getAttribute('src');
        if (image != "") {
            slide.css('background-image', 'url(' + image + ')');
        }
        slide.css('font-size', parseInt(jQ("#fontSize").val(), 10) + "px");
        slide.css('text-align', jQ("#textAlign").val());
        slide.css('color', jQ("#textColor").val());
        slide.css('background-color', jQ("#bgColor").val());
        var obj = {html: jQ('<div />').append(slide).html()};
        if (jQ("#caption").val() !== "") {
            obj.caption = jQ("#caption").val();
        }
		if (jQ("#link").val() !== "") {
            obj.link = jQ("#link").val();
        }
        return obj;
    }
    
    function slideOptionChange() {
        var options_ = jQ('#slider1').jSlider('getOptions');
        options_.current = jQ('#slider1').jSlider('getCurrentSlideIndex');
        if (options_.current > 0) {
            slides[options_.current - 1] = createSlide();
            var options = jQ("#slider1").jSlider('getOptions');
            options.slides  = slides;
            jQ("#slider1").jSlider('remove');
            jQ("#slider1").jSlider(options);
			
            options.current = 1;
            jQ("#opt").val(JSON.stringify(options));
        }
    }
    
    jQuery.expr[':'].regex = function(elem, index, match) {
        var matchParams = match[3].split(','),
            validLabels = /^(data|css):/,
            attr = {
                method: matchParams[0].match(validLabels) ? 
                matchParams[0].split(':')[0] : 'attr',
                property: matchParams.shift().replace(validLabels,'')
            },
            regexFlags = 'ig',
            regex = new RegExp(matchParams.join('').replace(/^\s+|\s+jQ/g,''), regexFlags);
        return regex.test(jQuery(elem)[attr.method](attr.property));
    };
    
    function readFile(e) {
        if (this.files && this.files[0]) {
            jQ("#bgImagePreview").attr('src', URL.createObjectURL(e.target.files[0]));
        } else {
            jQ("#bgImagePreview").attr('src', '');
        }
    }
    
    function rgb2hex(rgb){
        rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
        return (rgb && rgb.length === 4) ? "#" +
            ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
            ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
            ("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
    }
});    