/*
 *  Document   : base_ui_chat.js
 *  Author     : pixelcave
 */
var BaseUIChat=function(){var t,e,a,i,n,r,o,c,s,h=function(){switch(t=jQuery(window),e=jQuery("#header-navbar"),a=jQuery("#page-footer"),i=jQuery(".js-chat-container"),n=jQuery(".js-chat-head"),r=jQuery(".js-chat-talk"),o=jQuery(".js-chat-people"),c=jQuery(".js-chat-form"),i.data("chat-mode")){case"full":u(),jQuery(window).on("resize orientationchange",function(){clearTimeout(s),s=setTimeout(function(){u()},150)});break;case"fixed":u(i.data("chat-height"));break;case"popup":u(i.data("chat-height")),i.css({position:"fixed",right:"10px",bottom:0,display:"inline-block",padding:0,width:"70%","max-width":"420px","min-width":"300px","z-index":"1031"});break;default:return!1}r.scrollLock(),c.on("submit",function(t){t.preventDefault();var e=jQuery(".js-chat-input",jQuery(this));d(e.data("target-chat-id"),e.val(),"self",e)})},u=function(s){if(s)h=s;else{var h=t.height()-e.outerHeight()-a.outerHeight()-n.outerHeight()-(parseInt(i.css("padding-top"))+parseInt(i.css("padding-bottom")));200>h&&(h=200)}o&&o.css("height",h),r.css("height",h-c.outerHeight())},d=function(t,e,a,i){var n=jQuery('.js-chat-talk[data-chat-id="'+t+'"]');if(e&&n.length){var r="animated fadeIn push-50-l",o="bg-gray-lighter";"self"===a&&(r="animated fadeInUp push-50-r",o="bg-gray-light"),n.append('<div class="block block-rounded block-transparent push-15 '+r+'"><div class="block-content block-content-full block-content-mini '+o+'">'+jQuery("<div />").text(e).html()+"</div></div>"),n.animate({scrollTop:n[0].scrollHeight},150),i&&i.val("")}};return{init:function(){h()},addMessage:function(t,e,a){d(t,e,a,!1)}}}();jQuery(function(){BaseUIChat.init()});