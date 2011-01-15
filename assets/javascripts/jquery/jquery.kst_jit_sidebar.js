/**
 * JIT (just-in-time) Floating Sidebar
 * Inspired by http://www.webresourcesdepot.com/smart-floating-banners/
 * 
 * @version 0.1
 * Requires jQuery v1.3.2 or later
 * @author zoe somebody
 * @todo figure out if it is overlapping the footer and quit fixing
 * @todo pluginify
 */

(function($) { 

$.fn.jit_sidebar = function(options) {
    
    jit = $(".widget_jit_sidebar");
    
    $(".widget_jit_sidebar ~ .widget").wrapAll("<div class='jit_sidebar_container'></div>");
    
    viewportHeight = window.innerHeight ? window.innerHeight : $(window).height(); // With Opera 9.5, $(window).height() returns the document height

    $(window).scroll(function(){
            
        /* protect from viewportHeight being shorter than the jit and funking things up (and making bottom-most widgets unusable */
        if  ( $(".jit_sidebar_container").height() < viewportHeight && $(window).scrollTop() > jit.offset().top ){
            $(".jit_sidebar_container").css({
                position: "fixed",
                top: "0px",
                width: $(".jit_sidebar_container").width() /* in case the width wasn't explicit - breaks fluid layout though */
              }); 
        }
        if  ($(window).scrollTop() <= jit.offset().top){
            $(".jit_sidebar_container").css({
                position: "relative",
                top: jit.offset
              });
        }
    }); 
};
    
})(jQuery);
