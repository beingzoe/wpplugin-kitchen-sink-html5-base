/**
 * JIT (just-in-time) Floating Sidebar
 * Inspired by http://www.webresourcesdepot.com/smart-floating-banners/
 * 
 * @version 0.1
 * Requires jQuery v1.3.2 or later
 * @author zoe somebody
 * @todo pluginify
 */

(function($) { 

$.fn.jit_sidebar = function(options) {
    log("yes");
    jit = $(".widget_jit_sidebar");
    $(".widget_jit_sidebar ~ .widget").wrapAll("<div id='widget_jit_sidebar'></div>");
    viewportHeight = window.innerHeight ? window.innerHeight : $(window).height(); // With Opera 9.5, $(window).height() returns the document height

    $(window).scroll(function(){
            
        /* protect from viewportHeight being shorter than the jit and funking things up (and making bottom-most widgets unusable */
        if  ( $("#widget_jit_sidebar").height() < viewportHeight && $(window).scrollTop() > jit.offset().top ){
            $("#widget_jit_sidebar").css({
                position: "fixed",
                top: "0px",
                width: $("#widget_jit_sidebar").width() /* in case the width wasn't explicit - breaks fluid layout though */
              }); 
        }
        if  ($(window).scrollTop() <= jit.offset().top){
            $("#widget_jit_sidebar").css({
                position: "relative",
                top: jit.offset
              });
        }
    }); 
};
    
})(jQuery);
