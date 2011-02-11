<?php
/**
 * KST SENSIBLE DEFAULTS FUNCTIONS
 *
 * Stuff you would normally do in your functions.php and shouldn't have to think about
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  Base
 * @version     0.1
 * @since       0.1
 * @todo        find a real way to do the HTML5 Boilerplate jquery fallback method that is legit and still shows jquery as loaded for dependencies
*/


/**
 * Functions common to 'wp_sensible_defaults' and 'wp_sensible_defauls_admin'
 *
 * @since       0.1
*/
require_once KST_DIR_LIB . '/functions/wp_sensible_defaults_common.php';


/**
 * Set variable because WP expects it
 * Maximum width of content layout for WP image editor/output
 * WP actually uses this variable in the global scope
 *
 * @since       0.1
 * @global      $content_width WP width used to protect layout by limiting content width; WP best practice
*/
$content_width                   = KST_Kitchen_Theme::getThemeContentWidth();


/**#@+
 * Filters for SHORTCODES
 *
 * @since       0.1
*/
add_shortcode( 'wp_caption', 'kstFilterShortcodeCaptionAndWpCaption' );
add_shortcode( 'caption', 'kstFilterShortcodeCaptionAndWpCaption' );
/**#@-*/


/**#@+
 * ADD and REMOVE WP JUNK
 * remove_action(), remove_theme_support()
 * add_filter()
 * add_action()
 * add_theme_support()
 *
 * @since       0.1
*/
add_filter( 'the_title', 'kstFilterTheTitleToIncludePageNumber', 10, 2);
add_filter( 'excerpt_length', array('KST_Kitchen_Theme', 'getThemeExcerptLength') );
add_filter( 'gallery_style', 'kstRemoveGalleryInvalidCss' ); //Remove invalid gallery inline css
add_action( 'wp_print_scripts', 'kstLoadWpThreadedCommentReplyJs' ); // Load threaded comment reply javascript if needed
add_action( 'login_head', 'kstLoadAdminLoginCss' ); // Add style for wp-login (uses admin css: style_admin.css)
add_theme_support( 'automatic-feed-links' ); // Add default posts and comments RSS feed links to head
add_theme_support( 'post-thumbnails' ); // Theme uses featured image post/page thumbnails
/**#@-*/


/**
 * INITIALIZE HTML
 * style.css
 * modernizr.js (HTML5 Boilerplate)
 * dd_belatedpng.js (HTML5 Boilerplate)
 * jquery.js (HTML5 Boilerplate)
 * plugins.js (HTML5 Boilerplate)
 * script.js (HTML5 Boilerplate)
 *
 * @since       0.1
 * @todo        Apparently we need to load this stuff (and what else?) via add_action('init', 'kst_init_this_shit')
 * @todo        jquery boilerplate style is a fucked up hack, fix this
*/
if ( !is_admin() ) { //front end only initialize (admin handled under ADD JUNK)
    // LOAD CSS for theme (like parent were a child ;)
    wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/style.css', false, '0.1', 'all' ); // WP default stylesheet

    // HTML5 BOILERPLATE: Load Modernizr
    wp_register_script( 'modernizr', KST_URI_ASSETS . '/javascripts/libraries/modernizr-1.6.min.js', false, '1.6', false);
    wp_enqueue_script( 'modernizr' );

    // HTML5 BOILERPLATE: Load dd_belatedpng.js
    add_action('get_footer', 'dd_belatedpng_js_hack');
    function dd_belatedpng_js_hack() {
        $output = "<!--[if lt IE 7 ]>";
        $output .= "<script src='" . KST_URI_ASSETS . "/javascripts/libraries/dd_belatedpng.js'></script>";
        $output .= "<script> DD_belatedPNG.fix('img, .png_bg'); </script>";
        $output .= "<![endif]-->";
        echo $output;
    }


    // HTML5 BOILERPLATE: Load jQuery: Register jQuery as hack but load via action hook ('get_footer') using HTML5Boilerplate with fallback; TODO: FIND A BETTER WAY
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', KST_URI_ASSETS . '/javascripts/empty.js', false, 'x', true);
    wp_enqueue_script( 'jquery' );

    // HTML5 BOILERPLATE: This actually loads jquery - what to do about wp_enqueue_script and jquery dependencies to this RIGHT?
    add_action('get_footer', 'html5_boiler_plate_jquery_hack');
    function html5_boiler_plate_jquery_hack() {
        $output = "<script src='//ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js'></script>";
        $output .= "<script>!window.jQuery && document.write(unescape('%3Cscript src=\"" . KST_URI_ASSETS . "/javascripts/jquery/jquery-1.4.4.min.js\"%3E%3C/script%3E'))</script>";
        echo $output;
    }

    // Theme-wide Plugins and Application JS (HTML5 BOILERPLATE)
    wp_enqueue_script('plugins', get_stylesheet_directory_uri() . '/assets/javascripts/plugins.js' , array( 'jquery' ) , '0.1', true);
    wp_enqueue_script('application', get_stylesheet_directory_uri() . '/assets/javascripts/script.js' , array( 'jquery' ) , '0.1', true);
}


/**
 * FUNCTIONS
 * All functions are "pluggable" by themes unless noted otherwise
 *
 * @see     wp_sensible_defaults_admin.php
 * @see     wp_sensible_defaults_common.php for shared functions
*/



/**
 * kstWpNavMenuFallbackCb
 * Make a compatible default menu for wp_nav_menu() with consistent selectors
 * Javascript that creates comment form at reply link
 *
 * @since       0.1
 * @uses        wp_list_pages() WP function
*/
if ( !function_exists('kstWpNavMenuFallbackCb') ) {
    function kstWpNavMenuFallbackCb(){
        // Just a simple pages menu - no home - site/blog owner should make their menus
        $list_args['echo'] = false;
        $list_args['title_li'] = '';
        $list_args['depth'] = 1;
        $menu = str_replace( array( "\r", "\n", "\t" ), '', wp_list_pages($list_args) );
        $menu = str_replace("page_item", "menu-item", $menu ); // to match the wp_nav_menu for styling
        $menu = '<ul class="menu">' . $menu . '</ul>';
        echo $menu;
    }
}


/**
 * kstLoadWpThreadedCommentReplyJs
 * Load built-in WP comment-reply javascript
 * Javascript that creates comment form at reply link
 *
 * @since       0.1
 * @uses        is_singular() WP function
 * @uses        comments_open() WP function
 * @uses        get_option() WP function
 * @uses        wp_enqueue_script() WP function
*/
if ( !function_exists('kstLoadWpThreadedCommentReplyJs') ) {
    function kstLoadWpThreadedCommentReplyJs(){
        if ( is_singular() AND comments_open() AND ( get_option( 'thread_comments' ) ) )
            wp_enqueue_script( 'comment-reply' );
    }
}


/**
 * Remove inline styles printed when the gallery shortcode is used.
 * From twentyten
 *
 * @since       0.1
 * @param       required string $css
 * @return      string
*/
if ( !function_exists('kstRemoveGalleryInvalidCss') ) {
    function kstRemoveGalleryInvalidCss( $css ) {
        return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
    }
}


/**
 * Filter the_title to include the page number if article is paged
 *
 * @since       0.4
 * @global      $multipage
 * @global      object $page WP current page of post in loop being displayed
 * @global      object $post WP current post data being output
 * @param       required string $title
 * @param       required string $id
 * @return      string
*/
if ( !function_exists('kstFilterTheTitleToIncludePageNumber') ) {
    function kstFilterTheTitleToIncludePageNumber($title, $id) {
        global $multipage, $page, $post;
        // If current post is multipage AND the post title we are filtering is for the same post we are viewing
        // Otherwise this filter caused other functions using the_title (e.g. next_post_link() ) to incorrectly get filtered
        if ( $multipage && $page > 1 && $id == $post->ID )
            $title .= " (Page {$page})";

        return $title;
    }
}

/**
 * kstFormatWpListComments filtering wp_list_comments();
 *
 * Callback function to filter/replace the default WP comments markup
 * Default markup is limiting. Customize carefully though and be nice to plugins
 *
 * li is not closed by WordPress design; closed automatically on output
 *
 * @see         kstFormatWpListCommentsEnd()
 * @see         wp_list_comments() WP function
 * @global      object $comment WP current comments for the current $post
 * @uses        comment_class()
 * @uses        comment_ID()
 * @uses        get_avatar()
 * @uses        __()
 * @uses        get_comment_link()
 * @uses        edit_comment_link()
 * @uses        get_wp_comment_author_link()
 * @uses        get_comment_date()
 * @uses        get_comment_time()
 * @uses        comment_text()
 * @uses        comment_reply_link()
 * @todo        pingbacks last
*/
if ( !function_exists('kstFormatWpListComments') ) {
    function kstFormatWpListComments($comment, $args, $depth) {

        $GLOBALS['comment'] = $comment; ?>

        <article id="wp_comment-<?php comment_ID(); ?>"<?php comment_class('wp_comment clearfix'); ?>>
<?php
            if ($comment->comment_approved == '0') {
                 echo "<span class='wp_comment_moderated'>" . __('Your comment is awaiting moderation.') . "</span>";
            }
?>

<?php if ( get_option('show_avatars') ) : ?>
            <div class="wp_comment_avatar">
                <?php echo get_avatar($comment, $size='48' ); ?>
            </div>
<?php endif; ?>

            <div>
                <div class="wp_comment_author vcard">
                    <?php
                        //printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_wp_comment_author_link())
                        printf(__('<cite class="fn">%s</cite>'), get_comment_author_link())
                    ?>
                </div>

                <div class="wp_comment_meta commentmetadata">
                    <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"
                    ><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a>
                </div>

                <div class="wp_comment_comment">
                    <?php comment_text(); ?>
                </div>

                <div class="reply">
                    <?php
                        comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth'], 'add_below' => 'wp_comment')));
                        edit_comment_link(__('Edit'),'  ','');
                    ?>
                </div>
            </div>
    <?php
    }
}

/**
 * Callback function to wp_list_comments() specifically 'end-callback'
 *
 * kstFormatWpListCommentsEnd() is needed for closing the parent comment article
 * if threaded comments are to be nested.
 *
 * If you decide to NOT NEST threaded comments and want to close the comment container
 * in kstFormatWpListComments() and you are not using a <ul>, <ol>, or <div> as the container
 * e.g. <article> or <aside> then you still need this function to prevent WP from outputting
 * a closing </li>
 *
 * @see         kstFormatWpListComments()
 * @see         wp_list_comments() WP function
*/
if ( !function_exists('kstFormatWpListCommentsEnd') ) {
    function kstFormatWpListCommentsEnd() {
        echo "</article>"; // Not closed in opening function so threaded comments are nested
    }
}




/**
 * kstFilterShortcodeCaptionAndWpCaption
 *
 * Outputs 'insert media'>[caption] 'better'
 * This output is identical to the layout in attachment.php
 *
 * Output
    <div class="wp_attachment">
        <a><img>
        <div class="wp_caption">
    </div>
 *
 * @since 0.2
 * @param       required array $attr
 * @param       optional string $content
 * @uses        apply_filters() WP function
 * @uses        shortcode_atts() WP function
 * @uses        do_shortcode() WP function
 * @return      string
 * @link        http://wpengineer.com/filter-caption-shortcode-in-wordpress/
 *              Thanks wpengineer for having dealt with this already
 */
if ( !function_exists('kstFilterShortcodeCaptionAndWpCaption') ) {
    function kstFilterShortcodeCaptionAndWpCaption($attr, $content = null) {

        // Allow plugins/themes to override the default caption template.
        $output = apply_filters('kst_wp_caption_shortcode', '', $attr, $content);

        if ( $output != '' )
            return $output;

        extract(shortcode_atts(array(
            'id'	=> '',
            'align'	=> '',
            'width'	=> '',
            'caption' => ''
        ), $attr));

        if ( $id )
            $id = 'id="' . $id . '" ';

        if (!empty($width) )

        $width = ( !empty($width) )
            ? ' style="width: ' . ((int) $width) . 'px" '
            : '';

        $better = '<div ' . $id . $width . 'class="wp_attachment">';
        $better .= do_shortcode( $content );
        if ( !empty($caption) )
            $better .= '<div class="wp_caption">' . $caption .'</div>';
        $better .= '</div>';

        return $better;
    }
}
