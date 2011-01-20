/********************
 * Theme Application javascript
 * All theme-wide script is initialized here
 * Based on ZUI by zoe somebody http://beingzoe.com/zui/
********************/

jQuery.noConflict();//WordPress compatibility best practice

jQuery(document).ready(function($) { // noconflict wrapper to use shorthand $() for jQuery() inside of this function

    /*
     * kst_jquery_lightbox
     * using http://fancybox.net/
     *
     * #wp a[href$=.jpg]
     *      select all images in a wp post/page that link to an image
     *      is there a cleaner way to .png|.jpg|.gif ?
     * a.lightbox
     *      manually lightboxed image
     *
     * Any lightboxed image with the same "rel" will be grouped in a gallery
     */
    if(jQuery().fancybox) {
        $(" a.lightbox, .wp_entry a[href$=.jpg], .wp_entry a[href$=.png], .wp_entry a[href$=.gif] ")
            .attr({
              rel: "galleryize"
            })
            .fancybox({
                titlePosition: 'over'
            })
    };

    /*
     * Scrollable
     */
    if(jQuery().scrollable) {

        $("div.scrollables")
            .scrollable({
                speed: 500,
                circular: true,
                items: '.scroll_items',
                next: '.scrollables_next',
                prev: '.scrollables_previous'
                })
            .autoscroll({
                autoplay: true,
                interval: '4000'
            });
        /*
        $("div.scrollables.with_nav")
            .navigator({
                navi: '.scrollables_nav',
                idPrefix: $(this).attr('id')
            });
            */
    };

    /*
     * Cyclable
     */
    if(jQuery().cycle) {
        $('.cyclable_default .cycle_items').cycle({
            fx: 'scrollVert',
              speed: 1500,
              timeout: 6000,
              delay: 500,
              pause: 1,
              pauseOnPagerHover: 1,
              prev: '.cycle_previous',
              next: '.cycle_next',
              pager: '.cycle_pager'
        });
    }

    /* experiments */

    /* corners
    $('#hd').corner("bottom 10px");
    $('#sb, .next_previous, #respond').corner("10px");
    $('#ft').corner("top 10px");
    $('.wp_loop_date').corner("left 10px");
    $('.jit_box_info').corner("left");
    $('.jit_box_close').corner("bottom 5px");
    */
});
