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
 * @see inspiration: http://blog.okcupid.com/index.php/the-mathematics-of-beauty/
*/

(function($) {

$.fn.jit_message = function(options) {

    // Set defaults
    var params = {
      'trigger'     : '.wp_entry_footer',
      'wrapper'     : '#jit_box',
      'side'        : 'right',
      'speed_in'    : 300,
      'speed_out'   : 100
    };

    return this.each(function() { // Allow chaining

        // Merge options
        if ( options ) {
            params = $.extend( params, options );
        }

        if ( $(params.wrapper).length ) {

            $(params.wrapper).addClass(params.side).css({
                position:   'fixed',
                display:    'block',
            });

            var trigger_top = $(params.trigger).offset().top - $(window).height();
            var jit_box_width = $(params.wrapper).outerWidth(true);
            var jit_box_height = $(params.wrapper).outerHeight(true);

            // Set side position
            if ('right' == params.side) {
                //$(params.wrapper).css( "right", -jit_box_width );
                $(params.wrapper).css({
                    right:      -jit_box_width,
                    bottom:     '50px'
                });
            } else if ('left' == params.side) {
                 $(params.wrapper).css({
                    left:       -jit_box_width,
                    bottom:     '50px'
                });
            } else if ('top' == params.side) {
                 $(params.wrapper).css({
                    top:        -jit_box_height,
                    margin:     '0 auto'
                });
            } else if ('bottom' == params.side) {
                 $(params.wrapper).css({
                    bottom:        -jit_box_height,
                    margin:     '0 auto'
                });
            }

            $(window).scroll(function(){

                if ($(window).scrollTop() > trigger_top) {
                    if ('right' == params.side) {
                        $(params.wrapper).animate({'right':'0px'},params.speed_in)
                    } else if ('left' == params.side) {
                        $(params.wrapper).animate({'left':'0px'},params.speed_in)
                    } else if ('top' == params.side) {
                        $(params.wrapper).animate({'top':'0px'},params.speed_in)
                    } else if ('bottom' == params.side) {
                        $(params.wrapper).animate({'bottom':'0px'},params.speed_in)
                    }
                } else {
                    if ('right' == params.side) {
                        $(params.wrapper).stop(true).animate({'right':-jit_box_width},params.speed_out)
                    } else if ('left' == params.side) {
                        $(params.wrapper).stop(true).animate({'left':-jit_box_width},params.speed_out)
                    } else if ('top' == params.side) {
                        $(params.wrapper).stop(true).animate({'top':-jit_box_height},params.speed_out)
                    } else if ('bottom' == params.side) {
                        $(params.wrapper).stop(true).animate({'bottom':-jit_box_height},params.speed_out)
                    }

                }
            });

            // you can close the jit
            $('.jit_box_close').bind('click',function(){
                    $(params.wrapper).remove();
            });
        }

    });



};

})(jQuery);
