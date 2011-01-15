/**
 * jQuery jit message plugin: slide a message box in on a triggerable point in the page
 * 
 * Finds #jit_box and hides it off the right side of the page
 * "slides" it on "fixed" to the right side
 * at a distance from the bottom of the window set in CSS
 * only after the trigger element scrolls onto screen.
 *
 * Slides it off again when trigger element scrolls back off.
 * 
 * @version 0.1
 * Requires jQuery v1.3.2 or later
 * @author zoe somebody
 * @todo pluginify
 * @todo add options
 * @todo add option for jit_box element id/class
 * @todo add option for trigger element id/class
 * @todo what to do about the 20px shadow I am accounting for here?
 * @todo settings args for direction/speed jason found this version happening from the top http://blog.okcupid.com/index.php/the-mathematics-of-beauty/
 */

(function($) { 

$.fn.jit_message = function(options) {
    log("yes");
    /* jit box */
    if ( $('#jit_box').length ) {
        
        var trigger_top = $('.wp_entry_footer').offset().top - $(window).height();
        var jit_box_width = $('#jit_box').width() + 40;
        
        $('#jit_box').css( "right", -jit_box_width );
        
        $(window).scroll(function(){
                
            if ($(window).scrollTop() > trigger_top) {
                $('#jit_box').animate({'right':'0px'},300)
            } else {
                $('#jit_box').stop(true).animate({'right':-jit_box_width},100);
            }
        });
        /* you can close the jit */ 
        $('.jit_box_close').bind('click',function(){
                $('#jit_box').remove();
        }); 
    }
    
};
    
})(jQuery);
