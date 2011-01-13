<?php
/**
 * jquery Fancybox gallery stuff
 * 
 * Enqueues fancybox 1.3.4 and fixes [gallery] shortcode output 
 * 
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     WordPress
 * @subpackage  KitchenSinkThemeLibraries
 * @version     0.2
 * @since       0.2
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @link        http://fancybox.net/
 * @link        http://www.viper007bond.com/wordpress-plugins/jquery-lightbox-for-native-galleries/ (thank you for making sense of the attachment link)
 * @link        http://wordpress.org/support/topic/295401 (thank you)
 * @todo        This seems to be not working right in conjunction with lightbox on attachment.php template
 * @todo        The WP filters seem like a kluge is there a better way? Perhaps adding rel with post id or something?
 * @todo        Figure out a better way to deal with the stylesheet (merge all stylesheets into one and gzip on the fly?)
 * 
 * You still need to call it in your javascript:
 * 
 * $("dt.gallery-icon a").fancybox({
            titlePosition: 'over'
    });
 */

 
/*
 * Load Fancybox via wphead();
 */
wp_enqueue_style('fancybox', get_stylesheet_directory_uri() . '/_assets/stylesheets/fancybox.css');
wp_enqueue_script('fancybox', get_template_directory_uri() . '/_assets/javascripts/jquery/jquery.fancybox-1.3.4.js' , array('jquery','application') , '1.3.4', true);

/**
 * Force gallery thumbnails to link to the fullsize image
 * 
 * @since       0.1
 * @uses        is_feed() WP Function
 * @uses        is_admin() WP Function
 * @uses        get_post() WP Function
 * @uses        wp_get_attachment_url() WP Function
 */
function kst_fullsize_attachment_link( $link, $id ) {
    // The lightbox doesn't function inside feeds obviously, so don't modify anything
    if ( is_feed() || is_admin() )
        return $link;

    $post = get_post( $id );

    if ( 'image/' == substr( $post->post_mime_type, 0, 6 ) )
        return wp_get_attachment_url( $id );
    else
        return $link;
}
add_filter( 'attachment_link', 'kst_fullsize_attachment_link', 10, 2 );

/*
 * Add rel="" to image attachment links for lightbox galleries
 * Deprecated in place of javascript solution
 */                                    
/*
function add_lighbox_rel( $attachment_link ) {
    if( strpos( $attachment_link , 'href') != false && strpos( $attachment_link , '<img') != false )
        $attachment_link = str_replace( 'href' , 'rel="attachment_link" href' , $attachment_link );
    return $attachment_link;
}
add_filter( 'wp_get_attachment_link' , 'add_lighbox_rel' );
*/

/**
 * kst_theme_help entry
 *
 * See kst_theme_help
 * Help content for zui based theme_help.php
 * 
 * @since       0.2
 * @param       required string $part   toc|entry which part do you want?
 * @return      string
 */
function kst_theme_help_lightbox($part) {
    if ( $part == 'toc' )
        $output = "<li><a href='#lib_lightbox'>Image lightbox / galleries</a></li>";
    else 
        $output = 
<<< EOD
<h3 id="lib_lightbox">Image lightbox / galleries</h3>
<p>
    By default all images you upload and insert in your posts/pages or show using the [gallery] shortcode 
    will automatically lightbox.
</p>
<p>
    You may also lightbox content by adding the class ".lightbox" to any anchor.<br />
</p>
<p>
    <strong>Developer note:</strong> This is handled via a KST library in the _application directory, invoked in functions.php, and called from _assets/javascript/application.js
</p>

<br /><br />
<a href="#wphead">Top</a>
<br /><br />
EOD;

    return $output;
}

