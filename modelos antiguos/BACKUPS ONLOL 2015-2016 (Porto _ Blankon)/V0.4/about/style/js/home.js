"use strict"
$(window).load(function(){
	
	var 
	
	active_page = 'profile',
	
	/* Hover animation */
	
	menuActiveChange = function(content){
		$('#main-menu').children('.menu').removeClass('menu-active')
		$('#menu-' + content ).addClass('menu-active')
	},
	
	mouseEnterAct = function(content){
		if( !$.ec.isMobile() ){
			ec('#' + content + '-cover').openPage('none')
			$('#cover-side').css('opacity', '0.2')
		}
	},
	
	mouseLeaveAct = function(content){
		if( !$.ec.isMobile() ){
			ec('#' + active_page + '-cover').openPage('none')
			$('#cover-side').css('opacity', '1')
		}
	},
	
	clickAct = function(content){
		active_page = content	
		
		if( $.ec.isMobile() ){
			ec('#' + content + '-cover').openPage('none')
			ec('#' + content + '-page').openPage('none')
		}
		else{
			$('#content-side').css({
				'visibility' : 'hidden',
				'opacity' : '0'
			})
		
			window.setTimeout(function(){	
				ec('#' + content + '-page').openPage('none')
				$('#content-side').css({
					'visibility' : 'visible',
					'opacity' : '1'
				})
			}, 500)
		}
		
		$('#cover-side').css('opacity', '1')
		menuActiveChange(content)
	},
	
	sideResize = function(left_side, right_side){
		$('#cover-side')
		.removeClass('l1 l2 l3 l4 l5 l6 l7 l8 l9 l10 l11 l12')
		.addClass(left_side)
		
		$('#content-side')
		.removeClass('l1 l2 l3 l4 l5 l6 l7 l8 l9 l10 l11 l12')
		.addClass(right_side)
	}
	
	$('#menu-profile').on({
		"mouseenter" : function(){
			if( active_page != 'profile' ){
				mouseEnterAct('profile')
			}
		}, 
		"mouseleave" : function(){
			if( active_page != 'profile' ){
				mouseLeaveAct('profile')
			}
		},
		"click" : function(){
			if( active_page != 'profile' ){
				clickAct('profile')
				sideResize('l5', 'l6')
			}
		}
	})
	$('#menu-resume').on({
		"mouseenter" : function(){
			if( active_page != 'resume' ){
				mouseEnterAct('resume')
			}
		}, 
		"mouseleave" : function(){
			if( active_page != 'resume' ){
				mouseLeaveAct('resume')
			}
		},
		"click" : function(){
			if( active_page != 'resume' ){
				clickAct('resume')
				sideResize('l2', 'l9')
			}
		}
	})
	
	$('#menu-portofolio').on({
		"mouseenter" : function(){
			if( active_page != 'portofolio' ){
				mouseEnterAct('portofolio')
			}
		}, 
		"mouseleave" : function(){
			if( active_page != 'portofolio' ){
				mouseLeaveAct('portofolio')
			}
		},
		"click" : function(){
			if( active_page != 'portofolio' ){
				clickAct('portofolio')
				sideResize('l5', 'l6')
			}
		}
	})
	$('#menu-blog').on({
		"mouseenter" : function(){
			if( active_page != 'blog' ){
				mouseEnterAct('blog')
			}
		}, 
		"mouseleave" : function(){
			if( active_page != 'blog' ){
				mouseLeaveAct('blog')
			}
		},
		"click" : function(){
			if( active_page != 'blog' ){
				clickAct('blog')
				sideResize('l2', 'l9')
			}
		}
	})
	
	$('#menu-contact').on({
		"mouseenter" : function(){
			if( active_page != 'contact' ){
				mouseEnterAct('contact')
			}
		}, 
		"mouseleave" : function(){
			if( active_page != 'contact' ){
				mouseLeaveAct('contact')
			}
		},
		"click" : function(){
			if( active_page != 'contact' ){
				clickAct('contact')
				sideResize('l5', 'l6')
			}
		}
	})
	
	/* Button click */
	$('#menu-toggle').on("click", function(){
		if( $('#menu-toggle').hasClass('menu-toggle-active') ){
			$('#menu-toggle').children('span')
			.removeClass('fa-close')
			.addClass('fa-bars')
			
			$('#menu-toggle').removeClass('menu-toggle-active')
			
			ec('#'+ active_page +'-page').openPage('slide')
		}
		else{
			$('#menu-toggle').children('span')
			.removeClass('fa-bars')
			.addClass('fa-close')
			
			$('#menu-toggle').addClass('menu-toggle-active')
			
			ec('#sub-menu-page').openPage('slide')
		}
	})
	$('#hire-button').on("click", function(){
		if( active_page != 'contact' ){
			ec('#contact-cover').openPage('none')
			clickAct('contact')
			sideResize('l5', 'l6')
		}
	})
	
	
	/* Hire button */
	var
	
	hire_button_toggle = false,
	 
	hire_button_blink = $.ec.timer(function(){
		if( hire_button_toggle ){
			hire_button_toggle = false
			
			$('#hire-button').css({
				'opacity' : '1'
			})
		}
		else{
			hire_button_toggle = true
			
			$('#hire-button').css({
				'opacity' : '0.2' 
			})
		}
	})
	
	hire_button_blink.play(800)
	
	$('#hire-button').on({
		mouseenter : function(){
			hire_button_blink.stop()
			$('#hire-button').css({
				'opacity' : '1'
			})
		},
		mouseleave : function(){
			hire_button_blink.play(800)
		}
	})
	
	
	/* Contact form */
	var doRequest = function(url, target, callback){
		$('#wrapper').css({
			'transform' : 'scale(0.5,0)',
			'-webkit-transform' : 'scale(0.5,0)',
			'-ms-transform' : 'scale(0.5,0)',
			'opacity' : '0'
		})
		$('#loading').css({
			'opacity' : '1'
		})
		$('#loading-box-left, #loading-box-right').css({
			'-webkit-animation-play-state' : 'running', /* Chrome, Safari, Opera */
			'animation-play-state' : 'running'
		})
		$.ajax({
			url : url,
			dataType : 'html'
		})
		.done(function(data){
			$(target).html(data)
			ec(target).initializing()
		})
		.fail(function(){
			$(target).html('<span class="w3-text-green">Enviado correctamente</span></span>')
		})
		.always(function(){
			if( $.isFunction(callback) ){ callback() }
			$('#wrapper').css({
				'transform' : 'scale(1,1)',
				'-webkit-transform' : 'scale(1,1)',
				'-ms-transform' : 'scale(1,1)',
				'opacity' : '1'
			})
			$('#loading').css({
				'opacity' : '0'
			})
			$('#loading-box-left, #loading-box-right').css({
				'-webkit-animation-play-state' : 'paused', /* Chrome, Safari, Opera */
				'animation-play-state' : 'paused'
			})
		})
	}
	
	$('#contact-form-submit').on("click", function(){
		var
			name = $('#contact-form-name').val(),
			email = $('#contact-form-email').val(),
			message = $('#contact-form-message').val(),
			valid = true
		
		$('#contact-form-name').removeClass('form-invalid')
		$('#contact-form-email').removeClass('form-invalid')
		$('#contact-form-message').removeClass('form-invalid')
		$('#contact-form-response').html('')
		
		if( $.ec.isEmpty(name) ){
			valid = false
			$('#contact-form-name').addClass('form-invalid')
		}
		
		if( $.ec.isEmpty(email) ){
			valid = false
			$('#contact-form-email').addClass('form-invalid')
		}
		else if( email.indexOf('@') == -1 ){ //if hasn't char @ on it
			valid = false
			$('#contact-form-email').addClass('form-invalid')
		}
		
		if( $.ec.isEmpty(message) ){
			valid = false
			$('#contact-form-message').addClass('form-invalid')
		}
		
		if( valid == true ){
			$('#contact-form-response').css({'opacity' : '1'})
		}
		else{
			$('#contact-form-response').css({'opacity' : '1'})
			$('#contact-form-response').html('<span class="w3-text-red"><span class="fa fa-close"></span> ¡Revisa los campos en rojo!</span>')
			window.setTimeout(function(){
				$('#contact-form-response').css({'opacity' : '0'})
			}, 3000)
		}
	})
	
	
	/* Blog */
	
	var blog_first_load = false
	
	$('#menu-blog').on("click", function(){
		if( !blog_first_load ){
			blog_first_load = true
			getBlogPage()
		}
	})
	
	/* Pick on categories */
	
	$('#portofolio-button').children('.button').each(function(){
		var 
			target = $(this),
			category = ec(this).getClassValue('category'),
			non_category = $('#portofolio-container').children().not('.category-' + category),
			match_category = $('#portofolio-container').children('.category-' + category),
			all_button = $('#portofolio-button').children('.button')
		
		if( category == 'all' ){
			match_category = $('#portofolio-container').children('.portofolio-item')
		}
		
		$(this).on("click", function(){
			all_button.removeClass('button-active')
			target.addClass('button-active')
			
			$('#portofolio-container').css({
				'visibility' : 'hidden',
				'opacity' : '0',
				'-ms-transform' : 'scale(0,1)', 
				'-webkit-transform' : 'scale(0,1)', 
				'transform' : 'scale(0,1)' 
			})
			
			non_category.hide()
			match_category.show()
			
			$('#portofolio-page').perfectScrollbar('update')
			
			window.setTimeout(function(){
				$('#portofolio-container').css({
					'visibility' : 'visible',
					'opacity' : '1',
					'-ms-transform' : 'scale(1,1)', 
					'-webkit-transform' : 'scale(1,1)', 
					'transform' : 'scale(1,1)' 
				})
			} ,300)
		})
	})
	
	/* Hover */
	$('.timeline').each(function(){
		var target = $(this)
		
		$(this).on({
			"mouseenter" : function(){
				if( !$.ec.isMobile() ){
					target.find('.timeline-pin')
					.addClass('accent-bg')
					.css({
						'-ms-transform' : 'scale(1.5,1.5)', 
						'-webkit-transform' : 'scale(1.5,1.5)', 
						'transform' : 'scale(1.5,1.5)' 
					})
				}
			},
			"mouseleave" : function(){
				if( !$.ec.isMobile() ){
					target.find('.timeline-pin')
					.removeClass('accent-bg')
					.css({
						'-ms-transform' : 'scale(1,1)', 
						'-webkit-transform' : 'scale(1,1)', 
						'transform' : 'scale(1,1)'
					})
				}
			}
		})
	})
	
	$('.portofolio-item').each(function(){
		var 
			target = $(this),
			src = $(this).find('img').attr('src')
			
		$(this).on({
			"mouseenter" : function(){
				if( !$.ec.isMobile() ){
					$('#portofolio-preview')
					.attr('src', src)
					.ec().stayCenter('y', true, true, false)
					
					$('#portofolio-filter').css({
						'opacity' : '1'
					})
				}
			},
			"mouseleave" : function(){
				if( !$.ec.isMobile() ){
					$('#portofolio-filter').css({
						'opacity' : '0'
					})
				}
			}
		})
	})
	
	/* Rotate */
	var 
	
	rotate_toggle = true,
	
	rotate_animation = $.ec.timer(function(){
		if( rotate_toggle ){
			rotate_toggle = false
			
			$('.rotate-animation').css({
				'-ms-transform' : 'rotateY(360deg)', 
				'-webkit-transform' : 'rotateY(360deg)', 
				'transform' : 'rotateY(360deg)' 
			})
		}
		else{
			rotate_toggle = true
			
			$('.rotate-animation').css({
				'-ms-transform' : 'rotateY(0deg)', 
				'-webkit-transform' : 'rotateY(0deg)', 
				'transform' : 'rotateY(0deg)' 
			})
		}
	})
	
	rotate_animation.play(3000)
	
	/* Fade */
	var 
	
	fade_toggle = true,
	
	fade_animation = $.ec.timer(function(){
		if( fade_toggle ){
			fade_toggle = false
			
			$('.fade-animation').css({
				'opacity' : '0.2' 
			})
		}
		else{
			fade_toggle = true
			
			$('.fade-animation').css({
				'opacity' : '1' 
			})
		}
	})
	
	fade_animation.play(1000)
	
	/* Scroll menu */
	$('.perfect-scrollbar').each(function(){
		var target = $(this)
		
		target.perfectScrollbar({
			wheelSpeed : 1,
			suppressScrollX : true
		})
		
		$(window).resize(function(){
			target.perfectScrollbar('update')
		})
		 
	})
	
	/* Loading animation */
	
	$('body').css('overflow-y', 'auto')
	$('#wrapper').css({
		'transform' : 'scale(1,1)',
		'-webkit-transform' : 'scale(1,1)',
		'-ms-transform' : 'scale(1,1)',
		'opacity' : '1'
	})
	$('#loading').css({
		'opacity' : '0'
	})
	$('#loading-box-left, #loading-box-right').css({
		'-webkit-animation-play-state' : 'paused', /* Chrome, Safari, Opera */
		'animation-play-state' : 'paused'
	})
})


/* Contact map */

var myCenter = new google.maps.LatLng(40.3394421, -3.7659655);

function map_initializing() {
	var mapProp = {
		center:myCenter,
		zoom:16,
		panControl:false,
		zoomControl:false,
		mapTypeControl:false,
		scaleControl:false,
		streetViewControl:false,
		overviewMapControl:false,
		rotateControl:false,  
		mapTypeId:google.maps.MapTypeId.ROADMAP
	}
	var 
	map = new google.maps.Map(document.getElementById("contact-cover"), mapProp),
	marker = new google.maps.Marker({
		position:myCenter,
		animation:google.maps.Animation.BOUNCE
	});

	marker.setMap(map);
}

google.maps.event.addDomListener(window, 'load', map_initializing)

/*  Colorpicker  */

$('#demo-toggle').click(function(){
	if( $('#demo-color').hasClass('demo-color-active') ){
		$('#demo-color').removeClass('demo-color-active')
	}
	else{
		$('#demo-color').addClass('demo-color-active')
	}
})

var 

stylePick = function(color){
	$('#style-pick').attr(
		'href',
		'style/css/themes/' + color + '.css'
	)
},

bgPick = function(img){
	$('body').css({
		'background-image' : 'url("style/images/background/' + img + '")'
	})
}

$('#color-pick-1').click(function(){ stylePick('blue') })
$('#color-pick-2').click(function(){ stylePick('blue-grey') })
$('#color-pick-3').click(function(){ stylePick('brown') })
$('#color-pick-4').click(function(){ stylePick('deep-orange') })
$('#color-pick-5').click(function(){ stylePick('deep-purple') })
$('#color-pick-6').click(function(){ stylePick('green') })
$('#color-pick-7').click(function(){ stylePick('indigo') })
$('#color-pick-8').click(function(){ stylePick('khaki') })
$('#color-pick-9').click(function(){ stylePick('pink') })
$('#color-pick-10').click(function(){ stylePick('purple') })
$('#color-pick-11').click(function(){ stylePick('red') })
$('#color-pick-12').click(function(){ stylePick('teal') })
$('#color-pick-13').click(function(){ stylePick('neutral') })
$('#color-pick-14').click(function(){ 
	stylePick('christmas') 
	bgPick('bg_xmas.png')
})

$('#bg-pick-1').click(function(){ bgPick('it_1.png') })
$('#bg-pick-2').click(function(){ bgPick('it_2.png') })
$('#bg-pick-3').click(function(){ 
	$('body').css({
		'background-image' : 'none'
	})
})

/* Soft scroll */
!function t(e,n,r){function o(l,s){if(!n[l]){if(!e[l]){var a="function"==typeof require&&require;if(!s&&a)return a(l,!0);if(i)return i(l,!0);var c=new Error("Cannot find module '"+l+"'");throw c.code="MODULE_NOT_FOUND",c}var u=n[l]={exports:{}};e[l][0].call(u.exports,function(t){var n=e[l][1][t];return o(n?n:t)},u,u.exports,t,e,n,r)}return n[l].exports}for(var i="function"==typeof require&&require,l=0;l<r.length;l++)o(r[l]);return o}({1:[function(t,e,n){"use strict";function r(t){t.fn.perfectScrollbar=function(e){return this.each(function(){if("object"==typeof e||"undefined"==typeof e){var n=e;i.get(this)||o.initialize(this,n)}else{var r=e;"update"===r?o.update(this):"destroy"===r&&o.destroy(this)}return t(this)})}}var o=t("../main"),i=t("../plugin/instances");if("function"==typeof define&&define.amd)define(["jquery"],r);else{var l=window.jQuery?window.jQuery:window.$;"undefined"!=typeof l&&r(l)}e.exports=r},{"../main":7,"../plugin/instances":18}],2:[function(t,e,n){"use strict";function r(t,e){var n=t.className.split(" ");n.indexOf(e)<0&&n.push(e),t.className=n.join(" ")}function o(t,e){var n=t.className.split(" "),r=n.indexOf(e);r>=0&&n.splice(r,1),t.className=n.join(" ")}n.add=function(t,e){t.classList?t.classList.add(e):r(t,e)},n.remove=function(t,e){t.classList?t.classList.remove(e):o(t,e)},n.list=function(t){return t.classList?Array.prototype.slice.apply(t.classList):t.className.split(" ")}},{}],3:[function(t,e,n){"use strict";function r(t,e){return window.getComputedStyle(t)[e]}function o(t,e,n){return"number"==typeof n&&(n=n.toString()+"px"),t.style[e]=n,t}function i(t,e){for(var n in e){var r=e[n];"number"==typeof r&&(r=r.toString()+"px"),t.style[n]=r}return t}var l={};l.e=function(t,e){var n=document.createElement(t);return n.className=e,n},l.appendTo=function(t,e){return e.appendChild(t),t},l.css=function(t,e,n){return"object"==typeof e?i(t,e):"undefined"==typeof n?r(t,e):o(t,e,n)},l.matches=function(t,e){return"undefined"!=typeof t.matches?t.matches(e):"undefined"!=typeof t.matchesSelector?t.matchesSelector(e):"undefined"!=typeof t.webkitMatchesSelector?t.webkitMatchesSelector(e):"undefined"!=typeof t.mozMatchesSelector?t.mozMatchesSelector(e):"undefined"!=typeof t.msMatchesSelector?t.msMatchesSelector(e):void 0},l.remove=function(t){"undefined"!=typeof t.remove?t.remove():t.parentNode&&t.parentNode.removeChild(t)},l.queryChildren=function(t,e){return Array.prototype.filter.call(t.childNodes,function(t){return l.matches(t,e)})},e.exports=l},{}],4:[function(t,e,n){"use strict";var r=function(t){this.element=t,this.events={}};r.prototype.bind=function(t,e){"undefined"==typeof this.events[t]&&(this.events[t]=[]),this.events[t].push(e),this.element.addEventListener(t,e,!1)},r.prototype.unbind=function(t,e){var n="undefined"!=typeof e;this.events[t]=this.events[t].filter(function(r){return n&&r!==e?!0:(this.element.removeEventListener(t,r,!1),!1)},this)},r.prototype.unbindAll=function(){for(var t in this.events)this.unbind(t)};var o=function(){this.eventElements=[]};o.prototype.eventElement=function(t){var e=this.eventElements.filter(function(e){return e.element===t})[0];return"undefined"==typeof e&&(e=new r(t),this.eventElements.push(e)),e},o.prototype.bind=function(t,e,n){this.eventElement(t).bind(e,n)},o.prototype.unbind=function(t,e,n){this.eventElement(t).unbind(e,n)},o.prototype.unbindAll=function(){for(var t=0;t<this.eventElements.length;t++)this.eventElements[t].unbindAll()},o.prototype.once=function(t,e,n){var r=this.eventElement(t),o=function(t){r.unbind(e,o),n(t)};r.bind(e,o)},e.exports=o},{}],5:[function(t,e,n){"use strict";e.exports=function(){function t(){return Math.floor(65536*(1+Math.random())).toString(16).substring(1)}return function(){return t()+t()+"-"+t()+"-"+t()+"-"+t()+"-"+t()+t()+t()}}()},{}],6:[function(t,e,n){"use strict";var r=t("./class"),o=t("./dom");n.toInt=function(t){return parseInt(t,10)||0},n.clone=function(t){if(null===t)return null;if("object"==typeof t){var e={};for(var n in t)e[n]=this.clone(t[n]);return e}return t},n.extend=function(t,e){var n=this.clone(t);for(var r in e)n[r]=this.clone(e[r]);return n},n.isEditable=function(t){return o.matches(t,"input,[contenteditable]")||o.matches(t,"select,[contenteditable]")||o.matches(t,"textarea,[contenteditable]")||o.matches(t,"button,[contenteditable]")},n.removePsClasses=function(t){for(var e=r.list(t),n=0;n<e.length;n++){var o=e[n];0===o.indexOf("ps-")&&r.remove(t,o)}},n.outerWidth=function(t){return this.toInt(o.css(t,"width"))+this.toInt(o.css(t,"paddingLeft"))+this.toInt(o.css(t,"paddingRight"))+this.toInt(o.css(t,"borderLeftWidth"))+this.toInt(o.css(t,"borderRightWidth"))},n.startScrolling=function(t,e){r.add(t,"ps-in-scrolling"),"undefined"!=typeof e?r.add(t,"ps-"+e):(r.add(t,"ps-x"),r.add(t,"ps-y"))},n.stopScrolling=function(t,e){r.remove(t,"ps-in-scrolling"),"undefined"!=typeof e?r.remove(t,"ps-"+e):(r.remove(t,"ps-x"),r.remove(t,"ps-y"))},n.env={isWebKit:"WebkitAppearance"in document.documentElement.style,supportsTouch:"ontouchstart"in window||window.DocumentTouch&&document instanceof window.DocumentTouch,supportsIePointer:null!==window.navigator.msMaxTouchPoints}},{"./class":2,"./dom":3}],7:[function(t,e,n){"use strict";var r=t("./plugin/destroy"),o=t("./plugin/initialize"),i=t("./plugin/update");e.exports={initialize:o,update:i,destroy:r}},{"./plugin/destroy":9,"./plugin/initialize":17,"./plugin/update":21}],8:[function(t,e,n){"use strict";e.exports={maxScrollbarLength:null,minScrollbarLength:null,scrollXMarginOffset:0,scrollYMarginOffset:0,stopPropagationOnClick:!0,suppressScrollX:!1,suppressScrollY:!1,swipePropagation:!0,useBothWheelAxes:!1,useKeyboard:!0,useSelectionScroll:!1,wheelPropagation:!1,wheelSpeed:1}},{}],9:[function(t,e,n){"use strict";var r=t("../lib/dom"),o=t("../lib/helper"),i=t("./instances");e.exports=function(t){var e=i.get(t);e&&(e.event.unbindAll(),r.remove(e.scrollbarX),r.remove(e.scrollbarY),r.remove(e.scrollbarXRail),r.remove(e.scrollbarYRail),o.removePsClasses(t),i.remove(t))}},{"../lib/dom":3,"../lib/helper":6,"./instances":18}],10:[function(t,e,n){"use strict";function r(t,e){function n(t){return t.getBoundingClientRect()}var r=window.Event.prototype.stopPropagation.bind;e.settings.stopPropagationOnClick&&e.event.bind(e.scrollbarY,"click",r),e.event.bind(e.scrollbarYRail,"click",function(r){var i=o.toInt(e.scrollbarYHeight/2),a=e.railYRatio*(r.pageY-window.scrollY-n(e.scrollbarYRail).top-i),c=e.railYRatio*(e.railYHeight-e.scrollbarYHeight),u=a/c;0>u?u=0:u>1&&(u=1),s(t,"top",(e.contentHeight-e.containerHeight)*u),l(t),r.stopPropagation()}),e.settings.stopPropagationOnClick&&e.event.bind(e.scrollbarX,"click",r),e.event.bind(e.scrollbarXRail,"click",function(r){var i=o.toInt(e.scrollbarXWidth/2),a=e.railXRatio*(r.pageX-window.scrollX-n(e.scrollbarXRail).left-i),c=e.railXRatio*(e.railXWidth-e.scrollbarXWidth),u=a/c;0>u?u=0:u>1&&(u=1),s(t,"left",(e.contentWidth-e.containerWidth)*u-e.negativeScrollAdjustment),l(t),r.stopPropagation()})}var o=t("../../lib/helper"),i=t("../instances"),l=t("../update-geometry"),s=t("../update-scroll");e.exports=function(t){var e=i.get(t);r(t,e)}},{"../../lib/helper":6,"../instances":18,"../update-geometry":19,"../update-scroll":20}],11:[function(t,e,n){"use strict";function r(t,e){function n(n){var o=r+n*e.railXRatio,i=e.scrollbarXRail.getBoundingClientRect().left+e.railXRatio*(e.railXWidth-e.scrollbarXWidth);0>o?e.scrollbarXLeft=0:o>i?e.scrollbarXLeft=i:e.scrollbarXLeft=o;var s=l.toInt(e.scrollbarXLeft*(e.contentWidth-e.containerWidth)/(e.containerWidth-e.railXRatio*e.scrollbarXWidth))-e.negativeScrollAdjustment;c(t,"left",s)}var r=null,o=null,s=function(e){n(e.pageX-o),a(t),e.stopPropagation(),e.preventDefault()},u=function(){l.stopScrolling(t,"x"),e.event.unbind(e.ownerDocument,"mousemove",s)};e.event.bind(e.scrollbarX,"mousedown",function(n){o=n.pageX,r=l.toInt(i.css(e.scrollbarX,"left"))*e.railXRatio,l.startScrolling(t,"x"),e.event.bind(e.ownerDocument,"mousemove",s),e.event.once(e.ownerDocument,"mouseup",u),n.stopPropagation(),n.preventDefault()})}function o(t,e){function n(n){var o=r+n*e.railYRatio,i=e.scrollbarYRail.getBoundingClientRect().top+e.railYRatio*(e.railYHeight-e.scrollbarYHeight);0>o?e.scrollbarYTop=0:o>i?e.scrollbarYTop=i:e.scrollbarYTop=o;var s=l.toInt(e.scrollbarYTop*(e.contentHeight-e.containerHeight)/(e.containerHeight-e.railYRatio*e.scrollbarYHeight));c(t,"top",s)}var r=null,o=null,s=function(e){n(e.pageY-o),a(t),e.stopPropagation(),e.preventDefault()},u=function(){l.stopScrolling(t,"y"),e.event.unbind(e.ownerDocument,"mousemove",s)};e.event.bind(e.scrollbarY,"mousedown",function(n){o=n.pageY,r=l.toInt(i.css(e.scrollbarY,"top"))*e.railYRatio,l.startScrolling(t,"y"),e.event.bind(e.ownerDocument,"mousemove",s),e.event.once(e.ownerDocument,"mouseup",u),n.stopPropagation(),n.preventDefault()})}var i=t("../../lib/dom"),l=t("../../lib/helper"),s=t("../instances"),a=t("../update-geometry"),c=t("../update-scroll");e.exports=function(t){var e=s.get(t);r(t,e),o(t,e)}},{"../../lib/dom":3,"../../lib/helper":6,"../instances":18,"../update-geometry":19,"../update-scroll":20}],12:[function(t,e,n){"use strict";function r(t,e){function n(n,r){var o=t.scrollTop;if(0===n){if(!e.scrollbarYActive)return!1;if(0===o&&r>0||o>=e.contentHeight-e.containerHeight&&0>r)return!e.settings.wheelPropagation}var i=t.scrollLeft;if(0===r){if(!e.scrollbarXActive)return!1;if(0===i&&0>n||i>=e.contentWidth-e.containerWidth&&n>0)return!e.settings.wheelPropagation}return!0}var r=!1;e.event.bind(t,"mouseenter",function(){r=!0}),e.event.bind(t,"mouseleave",function(){r=!1});var i=!1;e.event.bind(e.ownerDocument,"keydown",function(a){if((!a.isDefaultPrevented||!a.isDefaultPrevented())&&r){var c=document.activeElement?document.activeElement:e.ownerDocument.activeElement;if(c){for(;c.shadowRoot;)c=c.shadowRoot.activeElement;if(o.isEditable(c))return}var u=0,d=0;switch(a.which){case 37:u=-30;break;case 38:d=30;break;case 39:u=30;break;case 40:d=-30;break;case 33:d=90;break;case 32:d=a.shiftKey?90:-90;break;case 34:d=-90;break;case 35:d=a.ctrlKey?-e.contentHeight:-e.containerHeight;break;case 36:d=a.ctrlKey?t.scrollTop:e.containerHeight;break;default:return}s(t,"top",t.scrollTop-d),s(t,"left",t.scrollLeft+u),l(t),i=n(u,d),i&&a.preventDefault()}})}var o=t("../../lib/helper"),i=t("../instances"),l=t("../update-geometry"),s=t("../update-scroll");e.exports=function(t){var e=i.get(t);r(t,e)}},{"../../lib/helper":6,"../instances":18,"../update-geometry":19,"../update-scroll":20}],13:[function(t,e,n){"use strict";function r(t,e){function n(n,r){var o=t.scrollTop;if(0===n){if(!e.scrollbarYActive)return!1;if(0===o&&r>0||o>=e.contentHeight-e.containerHeight&&0>r)return!e.settings.wheelPropagation}var i=t.scrollLeft;if(0===r){if(!e.scrollbarXActive)return!1;if(0===i&&0>n||i>=e.contentWidth-e.containerWidth&&n>0)return!e.settings.wheelPropagation}return!0}function r(t){var e=t.deltaX,n=-1*t.deltaY;return("undefined"==typeof e||"undefined"==typeof n)&&(e=-1*t.wheelDeltaX/6,n=t.wheelDeltaY/6),t.deltaMode&&1===t.deltaMode&&(e*=10,n*=10),e!==e&&n!==n&&(e=0,n=t.wheelDelta),[e,n]}function i(e,n){var r=t.querySelector("textarea:hover");if(r){var o=r.scrollHeight-r.clientHeight;if(o>0&&!(0===r.scrollTop&&n>0||r.scrollTop===o&&0>n))return!0;var i=r.scrollLeft-r.clientWidth;if(i>0&&!(0===r.scrollLeft&&0>e||r.scrollLeft===i&&e>0))return!0}return!1}function a(a){if(o.env.isWebKit||!t.querySelector("select:focus")){var u=r(a),d=u[0],p=u[1];i(d,p)||(c=!1,e.settings.useBothWheelAxes?e.scrollbarYActive&&!e.scrollbarXActive?(p?s(t,"top",t.scrollTop-p*e.settings.wheelSpeed):s(t,"top",t.scrollTop+d*e.settings.wheelSpeed),c=!0):e.scrollbarXActive&&!e.scrollbarYActive&&(d?s(t,"left",t.scrollLeft+d*e.settings.wheelSpeed):s(t,"left",t.scrollLeft-p*e.settings.wheelSpeed),c=!0):(s(t,"top",t.scrollTop-p*e.settings.wheelSpeed),s(t,"left",t.scrollLeft+d*e.settings.wheelSpeed)),l(t),c=c||n(d,p),c&&(a.stopPropagation(),a.preventDefault()))}}var c=!1;"undefined"!=typeof window.onwheel?e.event.bind(t,"wheel",a):"undefined"!=typeof window.onmousewheel&&e.event.bind(t,"mousewheel",a)}var o=t("../../lib/helper"),i=t("../instances"),l=t("../update-geometry"),s=t("../update-scroll");e.exports=function(t){var e=i.get(t);r(t,e)}},{"../../lib/helper":6,"../instances":18,"../update-geometry":19,"../update-scroll":20}],14:[function(t,e,n){"use strict";function r(t,e){e.event.bind(t,"scroll",function(){i(t)})}var o=t("../instances"),i=t("../update-geometry");e.exports=function(t){var e=o.get(t);r(t,e)}},{"../instances":18,"../update-geometry":19}],15:[function(t,e,n){"use strict";function r(t,e){function n(){var t=window.getSelection?window.getSelection():document.getSelection?document.getSelection():"";return 0===t.toString().length?null:t.getRangeAt(0).commonAncestorContainer}function r(){c||(c=setInterval(function(){return i.get(t)?(s(t,"top",t.scrollTop+u.top),s(t,"left",t.scrollLeft+u.left),void l(t)):void clearInterval(c)},50))}function a(){c&&(clearInterval(c),c=null),o.stopScrolling(t)}var c=null,u={top:0,left:0},d=!1;e.event.bind(e.ownerDocument,"selectionchange",function(){t.contains(n())?d=!0:(d=!1,a())}),e.event.bind(window,"mouseup",function(){d&&(d=!1,a())}),e.event.bind(window,"mousemove",function(e){if(d){var n={x:e.pageX,y:e.pageY},i={left:t.offsetLeft,right:t.offsetLeft+t.offsetWidth,top:t.offsetTop,bottom:t.offsetTop+t.offsetHeight};n.x<i.left+3?(u.left=-5,o.startScrolling(t,"x")):n.x>i.right-3?(u.left=5,o.startScrolling(t,"x")):u.left=0,n.y<i.top+3?(i.top+3-n.y<5?u.top=-5:u.top=-20,o.startScrolling(t,"y")):n.y>i.bottom-3?(n.y-i.bottom+3<5?u.top=5:u.top=20,o.startScrolling(t,"y")):u.top=0,0===u.top&&0===u.left?a():r()}})}var o=t("../../lib/helper"),i=t("../instances"),l=t("../update-geometry"),s=t("../update-scroll");e.exports=function(t){var e=i.get(t);r(t,e)}},{"../../lib/helper":6,"../instances":18,"../update-geometry":19,"../update-scroll":20}],16:[function(t,e,n){"use strict";function r(t,e,n,r){function s(n,r){var o=t.scrollTop,i=t.scrollLeft,l=Math.abs(n),s=Math.abs(r);if(s>l){if(0>r&&o===e.contentHeight-e.containerHeight||r>0&&0===o)return!e.settings.swipePropagation}else if(l>s&&(0>n&&i===e.contentWidth-e.containerWidth||n>0&&0===i))return!e.settings.swipePropagation;return!0}function a(e,n){l(t,"top",t.scrollTop-n),l(t,"left",t.scrollLeft-e),i(t)}function c(){Y=!0}function u(){Y=!1}function d(t){return t.targetTouches?t.targetTouches[0]:t}function p(t){return t.targetTouches&&1===t.targetTouches.length?!0:t.pointerType&&"mouse"!==t.pointerType&&t.pointerType!==t.MSPOINTER_TYPE_MOUSE?!0:!1}function f(t){if(p(t)){w=!0;var e=d(t);b.pageX=e.pageX,b.pageY=e.pageY,g=(new Date).getTime(),null!==y&&clearInterval(y),t.stopPropagation()}}function h(t){if(!Y&&w&&p(t)){var e=d(t),n={pageX:e.pageX,pageY:e.pageY},r=n.pageX-b.pageX,o=n.pageY-b.pageY;a(r,o),b=n;var i=(new Date).getTime(),l=i-g;l>0&&(m.x=r/l,m.y=o/l,g=i),s(r,o)&&(t.stopPropagation(),t.preventDefault())}}function v(){!Y&&w&&(w=!1,clearInterval(y),y=setInterval(function(){return o.get(t)?Math.abs(m.x)<.01&&Math.abs(m.y)<.01?void clearInterval(y):(a(30*m.x,30*m.y),m.x*=.8,void(m.y*=.8)):void clearInterval(y)},10))}var b={},g=0,m={},y=null,Y=!1,w=!1;n&&(e.event.bind(window,"touchstart",c),e.event.bind(window,"touchend",u),e.event.bind(t,"touchstart",f),e.event.bind(t,"touchmove",h),e.event.bind(t,"touchend",v)),r&&(window.PointerEvent?(e.event.bind(window,"pointerdown",c),e.event.bind(window,"pointerup",u),e.event.bind(t,"pointerdown",f),e.event.bind(t,"pointermove",h),e.event.bind(t,"pointerup",v)):window.MSPointerEvent&&(e.event.bind(window,"MSPointerDown",c),e.event.bind(window,"MSPointerUp",u),e.event.bind(t,"MSPointerDown",f),e.event.bind(t,"MSPointerMove",h),e.event.bind(t,"MSPointerUp",v)))}var o=t("../instances"),i=t("../update-geometry"),l=t("../update-scroll");e.exports=function(t,e,n){var i=o.get(t);r(t,i,e,n)}},{"../instances":18,"../update-geometry":19,"../update-scroll":20}],17:[function(t,e,n){"use strict";var r=t("../lib/class"),o=t("../lib/helper"),i=t("./instances"),l=t("./update-geometry"),s=t("./handler/click-rail"),a=t("./handler/drag-scrollbar"),c=t("./handler/keyboard"),u=t("./handler/mouse-wheel"),d=t("./handler/native-scroll"),p=t("./handler/selection"),f=t("./handler/touch");e.exports=function(t,e){e="object"==typeof e?e:{},r.add(t,"ps-container");var n=i.add(t);n.settings=o.extend(n.settings,e),s(t),a(t),u(t),d(t),n.settings.useSelectionScroll&&p(t),(o.env.supportsTouch||o.env.supportsIePointer)&&f(t,o.env.supportsTouch,o.env.supportsIePointer),n.settings.useKeyboard&&c(t),l(t)}},{"../lib/class":2,"../lib/helper":6,"./handler/click-rail":10,"./handler/drag-scrollbar":11,"./handler/keyboard":12,"./handler/mouse-wheel":13,"./handler/native-scroll":14,"./handler/selection":15,"./handler/touch":16,"./instances":18,"./update-geometry":19}],18:[function(t,e,n){"use strict";function r(t){var e=this;e.settings=d.clone(a),e.containerWidth=null,e.containerHeight=null,e.contentWidth=null,e.contentHeight=null,e.isRtl="rtl"===s.css(t,"direction"),e.isNegativeScroll=function(){var e=t.scrollLeft,n=null;return t.scrollLeft=-1,n=t.scrollLeft<0,t.scrollLeft=e,n}(),e.negativeScrollAdjustment=e.isNegativeScroll?t.scrollWidth-t.clientWidth:0,e.event=new c,e.ownerDocument=t.ownerDocument||document,e.scrollbarXRail=s.appendTo(s.e("div","ps-scrollbar-x-rail"),t),e.scrollbarX=s.appendTo(s.e("div","ps-scrollbar-x"),e.scrollbarXRail),e.scrollbarXActive=null,e.scrollbarXWidth=null,e.scrollbarXLeft=null,e.scrollbarXBottom=d.toInt(s.css(e.scrollbarXRail,"bottom")),e.isScrollbarXUsingBottom=e.scrollbarXBottom===e.scrollbarXBottom,e.scrollbarXTop=e.isScrollbarXUsingBottom?null:d.toInt(s.css(e.scrollbarXRail,"top")),e.railBorderXWidth=d.toInt(s.css(e.scrollbarXRail,"borderLeftWidth"))+d.toInt(s.css(e.scrollbarXRail,"borderRightWidth")),s.css(e.scrollbarXRail,"display","block"),e.railXMarginWidth=d.toInt(s.css(e.scrollbarXRail,"marginLeft"))+d.toInt(s.css(e.scrollbarXRail,"marginRight")),s.css(e.scrollbarXRail,"display",""),e.railXWidth=null,e.railXRatio=null,e.scrollbarYRail=s.appendTo(s.e("div","ps-scrollbar-y-rail"),t),e.scrollbarY=s.appendTo(s.e("div","ps-scrollbar-y"),e.scrollbarYRail),e.scrollbarYActive=null,e.scrollbarYHeight=null,e.scrollbarYTop=null,e.scrollbarYRight=d.toInt(s.css(e.scrollbarYRail,"right")),e.isScrollbarYUsingRight=e.scrollbarYRight===e.scrollbarYRight,e.scrollbarYLeft=e.isScrollbarYUsingRight?null:d.toInt(s.css(e.scrollbarYRail,"left")),e.scrollbarYOuterWidth=e.isRtl?d.outerWidth(e.scrollbarY):null,e.railBorderYWidth=d.toInt(s.css(e.scrollbarYRail,"borderTopWidth"))+d.toInt(s.css(e.scrollbarYRail,"borderBottomWidth")),s.css(e.scrollbarYRail,"display","block"),e.railYMarginHeight=d.toInt(s.css(e.scrollbarYRail,"marginTop"))+d.toInt(s.css(e.scrollbarYRail,"marginBottom")),s.css(e.scrollbarYRail,"display",""),e.railYHeight=null,e.railYRatio=null}function o(t){return"undefined"==typeof t.dataset?t.getAttribute("data-ps-id"):t.dataset.psId}function i(t,e){"undefined"==typeof t.dataset?t.setAttribute("data-ps-id",e):t.dataset.psId=e}function l(t){"undefined"==typeof t.dataset?t.removeAttribute("data-ps-id"):delete t.dataset.psId}var s=t("../lib/dom"),a=t("./default-setting"),c=t("../lib/event-manager"),u=t("../lib/guid"),d=t("../lib/helper"),p={};n.add=function(t){var e=u();return i(t,e),p[e]=new r(t),p[e]},n.remove=function(t){delete p[o(t)],l(t)},n.get=function(t){return p[o(t)]}},{"../lib/dom":3,"../lib/event-manager":4,"../lib/guid":5,"../lib/helper":6,"./default-setting":8}],19:[function(t,e,n){"use strict";function r(t,e){return t.settings.minScrollbarLength&&(e=Math.max(e,t.settings.minScrollbarLength)),t.settings.maxScrollbarLength&&(e=Math.min(e,t.settings.maxScrollbarLength)),e}function o(t,e){var n={width:e.railXWidth};e.isRtl?n.left=e.negativeScrollAdjustment+t.scrollLeft+e.containerWidth-e.contentWidth:n.left=t.scrollLeft,e.isScrollbarXUsingBottom?n.bottom=e.scrollbarXBottom-t.scrollTop:n.top=e.scrollbarXTop+t.scrollTop,l.css(e.scrollbarXRail,n);var r={top:t.scrollTop,height:e.railYHeight};e.isScrollbarYUsingRight?e.isRtl?r.right=e.contentWidth-(e.negativeScrollAdjustment+t.scrollLeft)-e.scrollbarYRight-e.scrollbarYOuterWidth:r.right=e.scrollbarYRight-t.scrollLeft:e.isRtl?r.left=e.negativeScrollAdjustment+t.scrollLeft+2*e.containerWidth-e.contentWidth-e.scrollbarYLeft-e.scrollbarYOuterWidth:r.left=e.scrollbarYLeft+t.scrollLeft,l.css(e.scrollbarYRail,r),l.css(e.scrollbarX,{left:e.scrollbarXLeft,width:e.scrollbarXWidth-e.railBorderXWidth}),l.css(e.scrollbarY,{top:e.scrollbarYTop,height:e.scrollbarYHeight-e.railBorderYWidth})}var i=t("../lib/class"),l=t("../lib/dom"),s=t("../lib/helper"),a=t("./instances"),c=t("./update-scroll");e.exports=function(t){var e=a.get(t);e.containerWidth=t.clientWidth,e.containerHeight=t.clientHeight,e.contentWidth=t.scrollWidth,e.contentHeight=t.scrollHeight;var n;t.contains(e.scrollbarXRail)||(n=l.queryChildren(t,".ps-scrollbar-x-rail"),n.length>0&&n.forEach(function(t){l.remove(t)}),l.appendTo(e.scrollbarXRail,t)),t.contains(e.scrollbarYRail)||(n=l.queryChildren(t,".ps-scrollbar-y-rail"),n.length>0&&n.forEach(function(t){l.remove(t)}),l.appendTo(e.scrollbarYRail,t)),!e.settings.suppressScrollX&&e.containerWidth+e.settings.scrollXMarginOffset<e.contentWidth?(e.scrollbarXActive=!0,e.railXWidth=e.containerWidth-e.railXMarginWidth,e.railXRatio=e.containerWidth/e.railXWidth,e.scrollbarXWidth=r(e,s.toInt(e.railXWidth*e.containerWidth/e.contentWidth)),e.scrollbarXLeft=s.toInt((e.negativeScrollAdjustment+t.scrollLeft)*(e.railXWidth-e.scrollbarXWidth)/(e.contentWidth-e.containerWidth))):(e.scrollbarXActive=!1,e.scrollbarXWidth=0,e.scrollbarXLeft=0,t.scrollLeft=0),!e.settings.suppressScrollY&&e.containerHeight+e.settings.scrollYMarginOffset<e.contentHeight?(e.scrollbarYActive=!0,e.railYHeight=e.containerHeight-e.railYMarginHeight,e.railYRatio=e.containerHeight/e.railYHeight,e.scrollbarYHeight=r(e,s.toInt(e.railYHeight*e.containerHeight/e.contentHeight)),e.scrollbarYTop=s.toInt(t.scrollTop*(e.railYHeight-e.scrollbarYHeight)/(e.contentHeight-e.containerHeight))):(e.scrollbarYActive=!1,e.scrollbarYHeight=0,e.scrollbarYTop=0,c(t,"top",0)),e.scrollbarXLeft>=e.railXWidth-e.scrollbarXWidth&&(e.scrollbarXLeft=e.railXWidth-e.scrollbarXWidth),e.scrollbarYTop>=e.railYHeight-e.scrollbarYHeight&&(e.scrollbarYTop=e.railYHeight-e.scrollbarYHeight),o(t,e),i[e.scrollbarXActive?"add":"remove"](t,"ps-active-x"),i[e.scrollbarYActive?"add":"remove"](t,"ps-active-y")}},{"../lib/class":2,"../lib/dom":3,"../lib/helper":6,"./instances":18,"./update-scroll":20}],20:[function(t,e,n){"use strict";var r,o,i=t("./instances"),l=document.createEvent("Event"),s=document.createEvent("Event"),a=document.createEvent("Event"),c=document.createEvent("Event"),u=document.createEvent("Event"),d=document.createEvent("Event"),p=document.createEvent("Event"),f=document.createEvent("Event"),h=document.createEvent("Event"),v=document.createEvent("Event");l.initEvent("ps-scroll-up",!0,!0),s.initEvent("ps-scroll-down",!0,!0),a.initEvent("ps-scroll-left",!0,!0),c.initEvent("ps-scroll-right",!0,!0),u.initEvent("ps-scroll-y",!0,!0),d.initEvent("ps-scroll-x",!0,!0),p.initEvent("ps-x-reach-start",!0,!0),f.initEvent("ps-x-reach-end",!0,!0),h.initEvent("ps-y-reach-start",!0,!0),v.initEvent("ps-y-reach-end",!0,!0),e.exports=function(t,e,n){if("undefined"==typeof t)throw"You must provide an element to the update-scroll function";if("undefined"==typeof e)throw"You must provide an axis to the update-scroll function";if("undefined"==typeof n)throw"You must provide a value to the update-scroll function";if("top"===e&&0>=n)return t.scrollTop=0,void t.dispatchEvent(h);if("left"===e&&0>=n)return t.scrollLeft=0,void t.dispatchEvent(p);var b=i.get(t);return"top"===e&&n>b.contentHeight-b.containerHeight?(t.scrollTop=b.contentHeight-b.containerHeight,void t.dispatchEvent(v)):"left"===e&&n>b.contentWidth-b.containerWidth?(t.scrollLeft=b.contentWidth-b.containerWidth,void t.dispatchEvent(f)):(r||(r=t.scrollTop),o||(o=t.scrollLeft),"top"===e&&r>n&&t.dispatchEvent(l),"top"===e&&n>r&&t.dispatchEvent(s),"left"===e&&o>n&&t.dispatchEvent(a),"left"===e&&n>o&&t.dispatchEvent(c),"top"===e&&(t.scrollTop=r=n,t.dispatchEvent(u)),void("left"===e&&(t.scrollLeft=o=n,t.dispatchEvent(d))))}},{"./instances":18}],21:[function(t,e,n){"use strict";var r=t("../lib/dom"),o=t("../lib/helper"),i=t("./instances"),l=t("./update-geometry");e.exports=function(t){var e=i.get(t);e&&(e.negativeScrollAdjustment=e.isNegativeScroll?t.scrollWidth-t.clientWidth:0,r.css(e.scrollbarXRail,"display","block"),r.css(e.scrollbarYRail,"display","block"),e.railXMarginWidth=o.toInt(r.css(e.scrollbarXRail,"marginLeft"))+o.toInt(r.css(e.scrollbarXRail,"marginRight")),e.railYMarginHeight=o.toInt(r.css(e.scrollbarYRail,"marginTop"))+o.toInt(r.css(e.scrollbarYRail,"marginBottom")),r.css(e.scrollbarXRail,"display","none"),r.css(e.scrollbarYRail,"display","none"),l(t),r.css(e.scrollbarXRail,"display",""),r.css(e.scrollbarYRail,"display",""))}},{"../lib/dom":3,"../lib/helper":6,"./instances":18,"./update-geometry":19}]},{},[1]);

/* Easycall */
if("undefined"==typeof jQuery)throw new Error("EasyCall Need jQuery");!function(e){window.easyCallBox={obj:[],version:"3.3.0",last_edited:"11 October 2015, GMT +7",programmer:"Mias Marthinus"};var t=function(t){var i={};return null!=t?i.o=e(t):e(this).length>0&&(i.o=e(this)),i.slides=[],"undefined"==typeof i.o?!1:(i.isEmpty=function(e){return 0==e||null==e||"undefined"==typeof e||""===e||e===!1?!0:!1},i.randomize=function(t){return e.isArray(t)?t[Math.floor(Math.random()*t.length+0)]:t},i.isTouchDevice=function(){try{return document.createEvent("TouchEvent"),!0}catch(e){return!1}},i.isLandscape=function(){return window.innerWidth>window.innerHeight?!0:!1},i.isMobile=function(){return window.innerWidth>993?!1:!0},i.timer=function(t){var i={};return i.step=e.isFunction(t)?t:function(){i.stop},i.timer=null,i.stop=function(){return null==i.timer?!1:(window.clearInterval(i.timer),void(i.timer=null))},i.play=function(e){e=e||10,i.timer=window.setInterval(i.step,e)},i},0==i.o.length?i:(i.tooltip=function(){},i.countUp=function(t,a){if(isNaN(t))return!1;var s={};s.target_num=t.toString().split(""),s.sum=0,s.val=0,s.i=s.target_num.length-1;for(var n=0;n<s.target_num.length;n++)e(i.o).append('<span class="ec-countUp-'+n+'">0</span>'),s.sum+=parseInt(s.target_num[n]),0==parseInt(s.target_num[n])&&s.sum++;switch(s.timer=i.timer(function(){e(i.o).children(".ec-countUp-"+s.i).text(s.val),s.val>=parseInt(s.target_num[s.i])?(s.i--,s.val=0,s.i<0&&(s.stop(),s.i=s.target_num.length-1)):s.val++}),a){case"fast":a=Math.ceil(100/s.sum);break;case"slow":a=Math.ceil(3e3/s.sum);break;case"normal":a=Math.ceil(2e3/s.sum)}return isNaN(a)&&(a=Math.ceil(1e3/s.sum)),s.delay=a,s.play=function(){s.timer.play(s.delay)},s.stop=function(){s.timer.stop()},s},i.inScope=function(t,a){if(!e.isFunction(t))return!1;var s=function(){e(document).scrollTop()>=e(i.o).offset().top-e(i.o).outerHeight()&&e(document).scrollTop()<=e(i.o).offset().top+e(i.o).outerHeight()?t():e.isFunction(a)&&a()};s(),e(document).scroll(function(){s()})},i.scrollThere=function(t){return e("html, body").filter(":animated")>0?!1:(t=t||"normal",void e("html, body").animate({scrollTop:i.o.offset().top},t))},i.stayCenter=function(t,a,s,n){if("boolean"!=typeof a&&(a=!0),"boolean"!=typeof s&&(s=!0),"boolean"!=typeof n&&(n=!0),!s&&!a)return!1;var o,l=i.o.parent(),c={left:"",top:""};switch((i.isEmpty(i.o.css("position"))||"static"==i.o.css("position"))&&i.o.css({position:"absolute"}),(i.isEmpty(l.css("position"))||"static"==l.css("position"))&&l.css({position:"relative"}),i.o.css("position")){case"fixed":switch(t){case"x":o=function(){i.o.css({left:(e(window).innerWidth()/2-i.o.outerWidth()/2)/e(window).innerWidth()*100+"%"})};break;case"y":o=function(){i.o.css({top:(e(window).innerHeight()/2-i.o.outerHeight()/2)/e(window).innerHeight()*100+"%"})};break;case"x-y":default:o=function(){i.o.css({top:(e(window).innerHeight()/2-i.o.outerHeight()/2)/e(window).innerHeight()*100+"%",left:(e(window).innerWidth()/2-i.o.outerWidth()/2)/e(window).innerWidth()*100+"%"})}}break;case"absolute":case"relative":default:switch(t){case"x":o=function(){i.o.css({left:(l.outerWidth()/2-i.o.outerWidth()/2)/l.outerWidth()*100+"%"})};break;case"y":o=function(){i.o.css({top:(l.outerHeight()/2-i.o.outerHeight()/2)/l.outerHeight()*100+"%"})};break;case"x-y":default:o=function(){i.o.css({left:(l.outerWidth()/2-i.o.outerWidth()/2)/l.outerWidth()*100+"%",top:(l.outerHeight()/2-i.o.outerHeight()/2)/l.outerHeight()*100+"%"})}}}a&&s?(o(),n&&e(window).resize(function(){o()})):a&&!s?(i.isMobile()&&o(),n&&e(window).resize(function(){i.isMobile()?o():i.o.css(c)})):!a&&s&&(i.isMobile()||o(),n&&e(window).resize(function(){i.isMobile()?i.o.css(c):o()}))},i.stayBottom=function(t,a,s){if("boolean"!=typeof t&&(t=!0),"boolean"!=typeof a&&(a=!0),"boolean"!=typeof s&&(s=!0),!a&&!t)return!1;var n=i.o.parent(),o={left:"",top:""};(i.isEmpty(i.o.css("position"))||"static"==i.o.css("position"))&&i.o.css({position:"absolute"}),(i.isEmpty(n.css("position"))||"static"==n.css("position"))&&n.css({position:"relative"});var l=function(){i.o.css({top:(n.outerHeight()-i.o.outerHeight())/n.outerHeight()*100+"%",bottom:"0px"})};t&&a?(l(),s&&e(window).resize(function(){l()})):t&&!a?(i.isMobile()&&l(),s&&e(window).resize(function(){i.isMobile()?l():i.o.css(o)})):!t&&a&&(i.isMobile()||l(),s&&e(window).resize(function(){i.isMobile()?i.o.css(o):l()}))},i.stayRight=function(t,a,s){if("boolean"!=typeof t&&(t=!0),"boolean"!=typeof a&&(a=!0),"boolean"!=typeof s&&(s=!0),!a&&!t)return!1;var n=i.o.parent(),o={left:"",top:""};(i.isEmpty(i.o.css("position"))||"static"==i.o.css("position"))&&i.o.css({position:"absolute"}),(i.isEmpty(n.css("position"))||"static"==n.css("position"))&&n.css({position:"relative"});var l=function(){i.o.css({left:(n.outerWidth()-i.o.outerWidth())/n.outerWidth()*100+"%",right:"0px"})};t&&a?(l(),s&&e(window).resize(function(){l()})):t&&!a?(i.isMobile()&&l(),s&&e(window).resize(function(){i.isMobile()?l():i.o.css(o)})):!t&&a&&(i.isMobile()||l(),s&&e(window).resize(function(){i.isMobile()?i.o.css(o):l()}))},i.getClassValue=function(t){var a;if(i.isEmpty(t))return a;if(!i.isEmpty(i.o.attr("class"))){var s=i.o.attr("class").split(" ");e.each(s,function(e,i){return 0==i.indexOf(t)?(a=i.substr(t.length+1,i.length-1),!1):void 0})}return a},i.hideChild=function(){i.o.find("*").not(".ec-nav").css({visibility:"hidden"})},i.revealChild=function(t,a,s){var n,o=i.o.find("*").not(".ec-nav"),l=o.length;if(i.o.find(":animated").finish(),0==o.length)return!1;switch("object"!=typeof t||i.isEmpty(t)?(n=t,a=a||"normal",s=s):(n=t.animation,a=t.speed||"normal",s=t.callback),a){case"fast":a=100;break;case"slow":a=500;break;case"normal":a=300}(a=parseInt(a))||(a=300);var c=function(t){var r,d=o.eq(t),h=n,p={opacity:d.css("opacity"),top:d[0].style.top,left:d[0].style.left,position:d.css("position")},u=function(){d.css(p)};if(("auto"==p.top||i.isEmpty(p.top))&&(p.top="0px"),("auto"==p.left||i.isEmpty(p.left))&&(p.left="0px"),0==d.filter("*").length||d.parent(".ec-page").length>0)return t==l-1?e.isFunction(s)&&s():c(t+1),!1;switch("random"==h&&(h=i.randomize(["fade","slideDown","slideUp","slideLeft","slideRight"])),r=t==l-1?function(){u(),e.isFunction(s)}:function(){u(),c(t+1)},h){case"slideDown":d.css({opacity:"0",visibility:"visible",top:parseInt(p.top.replace("px",""))-d.outerHeight()/2+"px",position:"relative"}).animate({opacity:p.opacity,top:p.top},a,r);break;case"slideUp":d.css({opacity:"0",visibility:"visible",top:parseInt(p.top.replace("px",""))+d.outerHeight()/2+"px",position:"relative"}).animate({opacity:p.opacity,visibility:"visible",top:p.top},a,r);break;case"slideLeft":d.css({opacity:"0",visibility:"visible",left:parseInt(p.left.replace("px",""))+d.outerWidth()/2+"px",position:"relative"}).animate({opacity:p.opacity,left:p.left},a,r);break;case"slideRight":d.css({opacity:"0",visibility:"visible",left:parseInt(p.left.replace("px",""))-d.outerWidth()/2+"px",position:"relative"}).animate({opacity:p.opacity,left:p.left},a,r);break;case"fade":default:d.css({opacity:"0",visibility:"visible"}).animate({opacity:p.opacity},a,r)}};c(0)},i.openPage=function(t,a,s,n){if(!i.o.parent().hasClass("ec-page")||i.o.hasClass("ec-nav"))return!1;if(i.o.hasClass("ec-active")){for(var o=i.o.parent(),l=!1;o.length>0;){if(o.parent().hasClass("ec-page")&&!o.hasClass("ec-active")){l=!0;break}o=o.parent()}if(!l)return!1}i.o.parent().find(":animated").finish();var c,r=i.o.parent(),d=i.o.siblings(".ec-active").not(".ec-nav").first(),h={width:"100%",zIndex:"1",left:"0px",opacity:"1",top:i.o.css("top")},p={width:"100%",zIndex:"0",left:"0px",opacity:"0",top:d.css("top")},u={height:i.o.outerHeight()+"px",minHeight:i.o.outerHeight()+"px"},f=function(){i.o.css(h),d.css(p),r.css(u);var t=0;r.children().not(".ec-nav").each(function(){e(this).css({top:t+"px"}),t-=e(this).outerHeight()})};r.children().removeClass("ec-active"),i.o.addClass("ec-active");for(var y=r.children().not(".ec-nav"),g=0,C=0;C<y.length;C++)if(y.eq(C).is(i.o)){g=C+1;break}r.hasClass("ec-slideshow")&&(e.easyCall.slides[r.data("slide-id")].stop(),e.easyCall.slides[r.data("slide-id")].play()),i.o.hasClass("ec-slideshow")&&(e.easyCall.slides[i.o.data("slide-id")].stop(),e.easyCall.slides[i.o.data("slide-id")].play()),i.o.find(".ec-slideshow").each(function(){e.easyCall.slides[e(this).data("slide-id")].stop(),e.easyCall.slides[e(this).data("slide-id")].play()}),i.o.siblings().each(function(){e(this).hasClass("ec-slideshow")&&e.easyCall.slides[e(this).data("slide-id")].stop()}),i.o.siblings().find(".ec-slideshow").each(function(){e.easyCall.slides[e(this).data("slide-id")].stop()}),g>0&&(r.children(".ec-nav").find("*").removeClass("ec-disabled"),r.children(".ec-nav").find(".ec-target-page-"+g).addClass("ec-disabled")),"object"!=typeof t||i.isEmpty(t)?(c=t,a=a,s=s):(c=t.animation,a=t.speed,s=t.callback,n=t.callback_before),"random"==c&&(c=i.randomize(["fadeIn","fadeOut","slide","slideFade","stack","stackFade"])),e.isFunction(n)&&n(i.o),r.css({minHeight:"0px"}),r.animate({height:i.o.outerHeight()+"px"},a,function(){for(var e=r.parent();e.length>0;)e.hasClass("ec-page")&&e.css({height:e.children(".ec-active").outerHeight()+"px",minHeight:e.children(".ec-active").outerHeight()+"px"}),e=e.parent()});for(var o=r;o.length>0;){if(o.parent().hasClass("ec-page")&&!o.hasClass("ec-active")){o.easyCall().openPage();break}o=o.parent()}switch(c){case"none":f(),e.isFunction(s)&&s(i.o);break;case"stack":i.o.css(i.o.index()>d.index()?{left:r.outerWidth()+"px",zIndex:"1",opacity:"1"}:{left:"-"+i.o.outerWidth()+"px",zIndex:"1",opacity:"1"}),d.css({left:"0px",zIndex:"0",opacity:"1"}),i.o.animate({left:"0px"},a,function(){f(),e.isFunction(s)&&s(i.o)});break;case"stackFade":i.o.css(i.o.index()>d.index()?{left:r.outerWidth()+"px",zIndex:"1",opacity:"0",width:i.o.outerWidth()+"px"}:{left:"-"+i.o.outerWidth()+"px",zIndex:"1",opacity:"0",width:i.o.outerWidth()+"px"}),d.css({left:"0px",zIndex:"0",opacity:"1",width:d.outerWidth()+"px"}),i.o.animate({left:"0px",opacity:"1"},a,function(){f(),e.isFunction(s)&&s(i.o)});break;case"slide":d.css({left:"0px",zIndex:"0",opacity:"1",width:d.outerWidth()+"px"}),i.o.index()>d.index()?(i.o.css({left:r.outerWidth()+"px",zIndex:"1",opacity:"1",width:i.o.outerWidth()+"px"}),d.animate({left:"-"+d.outerWidth()+"px"},a)):(i.o.css({left:"-"+i.o.outerWidth()+"px",zIndex:"1",opacity:"1",width:i.o.outerWidth()+"px"}),d.animate({left:r.outerWidth()+"px"},a)),i.o.animate({left:"0px"},a,function(){f(),e.isFunction(s)&&s(i.o)});break;case"slideFade":d.css({left:"0px",zIndex:"0",opacity:"1",width:d.outerWidth()+"px"}),i.o.index()>d.index()?(i.o.css({left:r.outerWidth()+"px",zIndex:"1",opacity:"0",width:i.o.outerWidth()+"px"}),d.animate({left:"-"+d.outerWidth()+"px",opacity:"0"},a)):(i.o.css({left:"-"+i.o.outerWidth()+"px",zIndex:"1",opacity:"0",width:i.o.outerWidth()+"px"}),d.animate({left:r.outerWidth()+"px",opacity:"0"},a)),i.o.animate({left:"0px",opacity:"1"},a,function(){f(),e.isFunction(s)&&s(i.o)});break;case"fadeIn":i.o.css({left:"0px",zIndex:"1",opacity:"0"}),d.css({left:"0px",zIndex:"0",opacity:"1"}),i.o.animate({opacity:"1"},a,function(){f(),e.isFunction(s)&&s(i.o)});break;case"fadeOut":default:i.o.css({left:"0px",zIndex:"0",opacity:"1"}),d.css({left:"0px",zIndex:"1",opacity:"1"}),d.animate({opacity:"0"},a,function(){f(),e.isFunction(s)&&s(i.o)})}},i.nextPage=function(e,t,a,s,n){if(!i.o.hasClass("ec-page"))return!1;var o;"object"!=typeof e||i.isEmpty(e)?o=e:(o=e.animation,t=e.speed,s=e.callback,n=e.callback_before,a=e.continuous),"boolean"!=typeof a&&(a=!0);var l=i.o.children(".ec-active").not(".ec-nav").next().not(".ec-nav");if(l.length>0)l.easyCall().openPage(o,t,s,n);else{if(!a)return!1;i.o.children().not(".ec-nav").first().easyCall().openPage(o,t,s,n)}},i.prevPage=function(e,t,a,s,n){if(!i.o.hasClass("ec-page"))return!1;var o;"object"!=typeof e||i.isEmpty(e)?o=e:(o=e.animation,t=e.speed,s=e.callback,n=e.callback_before,a=e.continuous),"boolean"!=typeof a&&(a=!0);var l=i.o.children(".ec-active").not(".ec-nav").prev().not(".ec-nav");if(l.length>0)l.easyCall().openPage(o,t,s,n);else{if(!a)return!1;i.o.children().not(".ec-nav").last().easyCall().openPage(o,t,s,n)}},i.openPageNumber=function(e,t,a,s,n){if(!i.o.hasClass("ec-page"))return!1;var o;if("object"!=typeof e||i.isEmpty(e)?o=e:(o=e.page_number,t=e.animation,a=e.speed,s=e.callback,n=e.callback_before),isNaN(o))return!1;var l=i.o.children().not(".ec-nav").eq(o-1);l.length>0&&l.easyCall().openPage(t,a,s,n)},i.slideshow=function(t){var a={};switch("object"==typeof t?(a.delay=t.delay||"normal",a.animation=t.animation||"fadeOut",a.animation_speed=t.animationSpeed||"normal",a.reveal_child=t.revealChild,a.reveal_child_speed=t.revealChildSpeed||"",a.callback_after=t.callbackAfter,a.callback_before=t.callbackBefore,a.callback=function(t){i.isEmpty(a.reveal_child)||e(t).easyCall().revealChild(a.reveal_child,a.reveal_child_speed,a.callback_after)}):(a.delay=t||"normal",a.animation="fadeOut",a.animation_speed="normal"),a.delay){case"fast":a.delay=3e3;break;case"slow":a.delay=8e3;break;case"normal":a.delay=5e3}return a.timer=i.timer(function(){e.isFunction(a.callback_before)&&a.callback_before(),i.o.easyCall().nextPage(a.animation,a.animation_speed,!0,a.callback,function(t){i.isEmpty(a.reveal_child)||e(t).easyCall().hideChild()})}),a.run=!1,a.isRun=function(){return a.run},a.play=function(){a.run=!0,a.timer.play(a.delay)},a.stop=function(){a.run=!1,a.timer.stop()},a},i.initializing=function(){var t=function(e){if(0==e.length)return!1;for(var t=e.parent(),i=!1;t.length>0;){if(t.filter(".ec-page").length>0){i=!0;break}t=t.parent()}return i?t:!1},a=function(e){if(0==e.length)return!1;for(var t=e,i=!0;t.length>0;){if(t.parent().hasClass("ec-page")&&!t.hasClass("ec-active")){i=!1;break}t=t.parent()}return i};i.o.find('.ec-slideshow,[class*="ec-hover-"]').each(function(){e(this).hasClass("ec-page")||e(this).addClass("ec-page")}),i.o.find(".ec-page").each(function(){var t=0;e(this).children().not(".ec-nav").each(function(){e(this).css({position:"relative","float":"left",left:"0px",top:t+"px",width:"100%",minWidth:"100%",maxWidth:"100%",zIndex:"0",opacity:"0"}),t-=e(this).outerHeight()});var i=e(this).children().not(".ec-nav").first();i.css({zIndex:"1",opacity:"1"}),i.addClass("ec-active"),i.siblings().removeClass("ec-active");var a=i.outerHeight();e(this).css({height:a+"px",minHeight:a+"px"});for(var s=e(this).parent();s.length>0;)s.hasClass("ec-page")&&s.css({height:s.children(".ec-active").outerHeight()+"px",minHeight:s.children(".ec-active").outerHeight()+"px"}),s=s.parent();e(this).children(".ec-nav").find(".ec-target-page-1").addClass("ec-disabled");var n=e(this);e(window).resize(function(){n.css({height:n.children(".ec-active").outerHeight()+"px",minHeight:n.children(".ec-active").outerHeight()+"px"});var t=0;n.children().not(".ec-nav").each(function(){e(this).css({top:t+"px"}),t-=e(this).outerHeight()});for(var i=n.parent();i.length>0;)i.hasClass("ec-page")&&i.css({height:i.children(".ec-active").outerHeight()+"px",minHeight:i.children(".ec-active").outerHeight()+"px"}),i=i.parent()})}),i.o.find(".ec-slideshow").each(function(){var t=e(this).easyCall(),i=t.getClassValue("ec-animation")||"fadeOut",a=t.getClassValue("ec-speed")||"normal",s=t.getClassValue("ec-delay")||"normal",n=t.getClassValue("ec-revealChild"),o=t.getClassValue("ec-revealChildSpeed")||a,l=e.easyCall.slides.length;e(this).data("slide-id",l),e.easyCall.slides[l]=e(this).easyCall().slideshow({animation:i,revealChild:n,revealChildSpeed:o,animationSpeed:a,delay:s}),e(window).resize(function(){e.easyCall.slides[l].run&&(e.easyCall.slides[l].stop(),e.easyCall.slides[l].play())}),e(window).on("visibilitychange",function(){"hidden"==document.visibilityState?e.easyCall.slides[l].run&&(e.easyCall.slides[l].stop(),e.easyCall.slides[l].run=!0):"visible"==document.visibilityState&&e.easyCall.slides[l].run&&(e.easyCall.slides[l].stop(),e.easyCall.slides[l].play())}),e(window).on("blur",function(){e.easyCall.slides[l].run&&(e.easyCall.slides[l].stop(),e.easyCall.slides[l].run=!0)}),e(window).on("focus",function(){"hidden"==document.visibilityState?e.easyCall.slides[l].run&&(e.easyCall.slides[l].stop(),e.easyCall.slides[l].run=!0):"visible"==document.visibilityState&&e.easyCall.slides[l].run&&(e.easyCall.slides[l].stop(),e.easyCall.slides[l].play())})}),i.o.find(".ec-slideshow").each(function(){return a(e(this))?void e.easyCall.slides[e(this).data("slide-id")].play():!1}),i.o.find(".ec-trigger-click").each(function(){if(!e(this).hasClass("ec-slideshow")){var a=t(e(this)),s=e(this).easyCall(),n=s.getClassValue("ec-animation")||"fadeOut",o=s.getClassValue("ec-speed")||"normal",l=s.getClassValue("ec-revealChild"),c=s.getClassValue("ec-revealChildSpeed")||o,r=function(t){i.isEmpty(l)||e(t).easyCall().revealChild(l,c)},d=function(t){i.isEmpty(l)||e(t).easyCall().hideChild()};if(e(this).filter('[class*="ec-target-id-"]').length>0){var h=e(this).easyCall().getClassValue("ec-target-id");e("#"+h).length>0&&e(this).click(function(){e("#"+h).easyCall().openPage(n,o,r,d)})}else if(a.length>0)if(e(this).css({cursor:"pointer"}),e(this).hasClass("ec-target-prevPage"))e(this).click(function(){a.easyCall().prevPage(n,o,!0,r,d)});else if(e(this).filter('[class*="ec-target-page-"]').length>0)for(var p=e(this).attr("class").split(" "),u=1;u<=a.children().not(".ec-nav").length;u++){var f=e.inArray("ec-target-page-"+u,p);if(f>=0){e(this).click(function(){a.easyCall().openPageNumber(u,n,o,r,d)});break}}else e(this).click(function(){a.easyCall().nextPage(n,o,!0,r,d)})}}),i.o.find(".ec-trigger-inScope").each(function(){var t=!1,s=e(this);reveal=e(this).easyCall().getClassValue("ec-reveal"),repeat=s.easyCall().getClassValue("ec-repeat")||"no-repeat",original={opacity:s.css("opacity")},s.hasClass("ec-reveal")&&s.css({opacity:"0",transform:"scale(0.8,0.8)","-webkit-transform":"scale(0.8,0.8)","-ms-transform":"scale(0.8,0.8)"}).addClass("ec-transition"),s.hasClass("ec-revealChild")&&s.easyCall().hideChild(),e(this).easyCall().inScope(function(){if(!t&&a(e(this))){if(t=!0,s.hasClass("ec-reveal")&&s.css({transform:"scale(1,1)","-webkit-transform":"scale(1,1)","-ms-transform":"scale(1,1)"}).animate({opacity:"1"}),s.hasClass("ec-countUp")){var n=s.easyCall().getClassValue("ec-countUpResult"),o=s.easyCall().getClassValue("ec-countUpSpeed")||"normal";i.isEmpty(n)||s.easyCall().countUp(n,o).play()}s.hasClass("ec-revealChild")&&s.easyCall().revealChild("slideDown")}})}),i.o.find('[class*="ec-hover-"]').each(function(){if(e(this).hasClass("ec-page")&&!e(this).hasClass("ec-slideshow")){var t=e(this).easyCall().getClassValue("ec-hover"),a=e(this).easyCall().getClassValue("ec-speed")||"fast",s=e(this).easyCall().getClassValue("ec-revealChild"),n=e(this).easyCall().getClassValue("ec-revealChildSpeed"),o=e(this).children().not(".ec-nav").first(),l=e(this).children().not(".ec-nav").eq(1),c=o.outerHeight()+"px",r=function(t){i.isEmpty(s)||e(t).easyCall().revealChild(s,n)},d=function(t){i.isEmpty(s)||e(t).easyCall().hideChild()};l.css({height:c,maxHeight:c,minHeight:c}),e(this).hover(function(){l.easyCall().openPage(t,a,r,d)},function(){o.easyCall().openPage(t,a)}),e(window).resize(function(){l.css({height:o.outerHeight()+"px",minHeight:o.outerHeight()+"px"})})}}),i.o.find(".ec-center-y").each(function(){e(this).easyCall().stayCenter("y"),e(this).addClass("ec-transition")}),i.o.find(".ec-center-x").each(function(){e(this).easyCall().stayCenter("x"),e(this).addClass("ec-transition")}),i.o.find(".ec-center").each(function(){e(this).easyCall().stayCenter("x-y"),e(this).addClass("ec-transition")}),i.o.find(".ec-bottom").each(function(){e(this).easyCall().stayBottom(),e(this).addClass("ec-transition")}),i.o.find(".ec-right").each(function(){e(this).easyCall().stayRight(),e(this).addClass("ec-transition")}),i.o.find(".ec-center-y-desktop").each(function(){e(this).easyCall().stayCenter("y",!1)}),i.o.find(".ec-center-x-desktop").each(function(){e(this).easyCall().stayCenter("x",!1)}),i.o.find(".ec-center-desktop").each(function(){e(this).easyCall().stayCenter("x-y",!1)}),i.o.find(".ec-bottom-desktop").each(function(){e(this).easyCall().stayBottom(!1)}),i.o.find(".ec-right-desktop").each(function(){e(this).easyCall().stayRight(!1)}),i.o.find(".ec-center-y-mobile").each(function(){e(this).easyCall().stayCenter("y",!0,!1)}),i.o.find(".ec-center-x-mobile").each(function(){e(this).easyCall().stayCenter("x",!0,!1)}),i.o.find(".ec-center-mobile").each(function(){e(this).easyCall().stayCenter("x-y",!0,!1)}),i.o.find(".ec-bottom-mobile").each(function(){e(this).easyCall().stayBottom(!0,!1)}),i.o.find(".ec-right-mobile").each(function(){e(this).easyCall().stayRight(!0,!1)})},i))};e.fn.easyCall=e.fn.ec=t,e.easyCall=e.ec=t(!1),window.easyCall=window.ec=t,e(window).load(function(){console.log();var i=".ec-page{position:relative; overflow:hidden; clear:both;}.ec-nav{position:absolute; top:0px; left:0px; z-index:2;}.ec-disabled{opacity:0.2 !important; cursor:default !important;}.ec-transition{-webkit-transition:-webkit-transform 1s;-o-transition:-webkit-transform 1s;transition:transform 1s;}";e('<style type="text/css">'+i+"</style>").appendTo("head"),t("body").initializing()})}(jQuery);