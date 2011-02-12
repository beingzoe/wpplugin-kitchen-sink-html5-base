/********************
 * Admin javascript
 * All admin-wide script is initialized here
 * Based on ZUI by zoe somebody http://beingzoe.com/zui/
********************/

jQuery(document).ready(function($) { // noconflict wrapper to use shorthand $() for jQuery() inside of this function

    $(".fade").slideDown().delay(10000).fadeOut(400); //Fade out wp #message boxes

    /* Toggle Theme Options */
    $(".closed .inside").slideUp();
    $(".hndle").click( function() {
            $(this).next(".inside").slideToggle().parent().toggleClass('closed');
    })

});
