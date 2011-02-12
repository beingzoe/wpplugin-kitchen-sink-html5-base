/********************
 * Theme Application javascript
 * All theme-wide script is initialized here
 * Based on ZUI by zoe somebody http://beingzoe.com/zui/
********************/

jQuery.noConflict();//WordPress compatibility best practice

jQuery(document).ready(function($) { // noconflict wrapper to use shorthand $() for jQuery() inside of this function

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

});
