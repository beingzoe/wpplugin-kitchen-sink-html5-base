<?php
/**
 * Initialize Theme with sensible defaults
 * and spare yourself needless junk in your functions.php
 * and get all of the HTML5 goodness ready to go
 */
 
/* Settings */
$content_width                   = CONTENT_WIDTH;   // WP actually uses this variable in the global scope

/* SHORTCODES
 * Mostly loaded in child theme or kst_theme_execute() if running as stand-alone theme
 * Some shortcodes are loaded in context e.g. [mp3player] for a complete list
 */
add_shortcode( 'wp_caption', 'kst_caption_shortcode_filtered' );
add_shortcode( 'caption', 'kst_caption_shortcode_filtered' );


/* REMOVE WP JUNK
 * remove_action, remove_theme_support, etc...
 *
 * This is mostly left to the discretion of the Child Theme
 * Key exception being to create useful "pluggable" functions and hook/filters the Child Theme might want
 *
 * All Kitchen Sink "REMOVE WP JUNK" functions that can be "plugged":
 *      kst_admin_remove_meta_boxes();      Remove built-in WP custom field meta boxes from post/page not needed for theme
 *      kst_remove_gallery_css();           Remove invalid inline style block added by WP [gallery] shortcode
 */
if ( is_admin() ) { // remove admin specific junk
    add_action( 'admin_menu' , 'kst_admin_remove_meta_boxes' ); // Removes meta boxes from post/page not needed for theme
}
add_filter( 'gallery_style', 'kst_remove_gallery_css' ); //Remove invalid gallery inline css


/* ADD WP JUNK
 * add_action, add_theme_support, etc...
 *
 * Theme support added by default for:
 *      add_editor_style()
 *      automatic-feed-links
 *      post-thumbnails
 *
 * All Kitchen Sink "Add WP Junk" functions that can be "plugged":
 *      kst_threaded_comment_reply();   Loads threaded comment reply javascript if needed;
 *      kst_admin_login_css();          Load custom admin stylesheet style_admin.css (also shared and loaded by wp-login.php)
 */
if ( is_admin() ) { // Add admin specific junk
    add_action( 'admin_print_styles', 'kst_admin_login_css' ); // Load custom admin stylesheet style_admin.css
}

add_action( 'wp_print_scripts', 'kst_threaded_comment_reply' ); // Load threaded comment reply javascript if needed
add_action( 'login_head', 'kst_admin_login_css' ); // Add style for wp-login (uses admin css: style_admin.css)
add_theme_support( 'automatic-feed-links' ); // Add default posts and comments RSS feed links to head
add_theme_support( 'post-thumbnails' ); // Theme uses featured image post/page thumbnails
    /*
    NOTE:   I no longer use the WP post-thumbnail due to problems with it not being included
            in edits to images using the WP admin image editor (i.e. cropping)
            -Theme support for "featured image / post thumbnails" is turned on
            -I use the built-in WP 'thumbnail' image size for the thumbnails
            -If I need a 'post' size featured image I install the plugin "Additional Image Sizes"
                I always install this plugin and create the image size:
                "single-post-max-width" (full content width image for layout)

    OTHERWISE if you want to use the featured image functionality this is it:
    set_post_thumbnail_size( 150, 150, false ); // Normal post thumbnails; the_post_thumbnail();
    add_image_size( 'post-thumbnail-single', 700, 9999, false ); // Full content width; the_post_thumbnail('post-thumbnails-single');
    */


/* ADD/REMOVE/FILTER WP JUNK
 * Still more tweaking of the WP core. Where we can just add/remove/customize in one swoop
 *
 * All Kitchen Sink "ADD AND REMOVE JUNK ALL AT ONCE" functions that can be overloaded:
 *      kst_admin_dashboard_customize()     Add/Remove/Customize WP admin dashboard widgets
 *      kst_admin_favorite_actions()        Add/Remove/Customize quick links from WP admin favorites drop down
 *      kst_admin_edit_tinymce()            Customize admin TinyMCE toolbars/dropdowns
 *      kst_the_title()                     Add page number to title if <!--next-->; article is paged
 *      kst_the_content_more_link()         Wr
 */
if ( is_admin() ) { //Add/Remove/Edit admin specific junk
    add_action( 'wp_dashboard_setup', 'kst_admin_dashboard_customize' );
    add_filter( 'favorite_actions', 'kst_admin_favorite_actions' );
    add_filter( 'tiny_mce_before_init', 'kst_admin_edit_tinymce' );
    add_filter( 'excerpt_length', 'kst_excerpt_length');
}
add_filter( 'the_title', 'kst_the_title', 10, 2);
add_filter( 'excerpt_length', 'kst_excerpt_length');


/* INITIALIZE HTML
 * Parent Theme loads style.css, modernizr, jQuery (from Google CDN), and application.js for the Child Theme
 * TODO: Apparently we need to load this stuff (and what else?) via add_action('init', 'kst_init_this_shit')
 */
if ( !is_admin() ) { //front end only initialize (admin handled under ADD JUNK)
    /* LOAD CSS */
    wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/style.css', false, '0.1', 'all' ); // WP default stylesheet

    /* Load JAVASCRIPT */

    /* Load Modernizr */
    wp_register_script( 'modernizr', get_template_directory_uri() . '/_assets/javascripts/libraries/modernizr-1.6.min.js', false, '1.6', false);
    wp_enqueue_script( 'modernizr' );

    /* Load jQuery: Register jQuery as hack but load in footer.php using HTML5Boilerplate with fallback; TODO: FIND A BETTER WAY */
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', get_template_directory_uri() . '/_assets/javascripts/empty.js', false, 'x', true);
    wp_enqueue_script( 'jquery' );

    /* Theme-wide Plugins and Application JS */
    wp_enqueue_script('plugins', get_stylesheet_directory_uri() . '/_assets/javascripts/plugins.js' , array( 'jquery' ) , '0.1', true);
    wp_enqueue_script('application', get_stylesheet_directory_uri() . '/_assets/javascripts/application.js' , array( 'jquery' ) , '0.1', true);
} else {
    wp_enqueue_script('application_admin', get_stylesheet_directory_uri() . '/_assets/javascripts/application_admin.js' , array( 'jquery' ) , '0.1', true);
}


/**
 * FUNCTIONS: Sensible Defaults (mostly callbacks)
 */
 

/**
 * FUNCTIONS
 * All functions are "pluggable" by themes unless noted otherwise
 */

/**
 * kst_admin_login_css
 * Load custom stylesheet for admin and wp-login (they share a stylesheet) 
 *
 * NOT ADMIN ONLY because wp-login is not treated as part of admin
 *
 * @since       0.1
 * @uses        get_stylesheet_directory_uri() WP function
 */
if ( !function_exists('kst_admin_login_css') ) {
    function kst_admin_login_css() {
        add_editor_style(); // Style the TinyMCE editor a little bit in editor-style.css
        echo '<link rel="stylesheet" href="'. get_stylesheet_directory_uri() . '/style_admin.css" type="text/css" />'."\n";
    }
}

/** 
 * kst_threaded_comment_reply
 * Load built-in WP comment-reply javascript 
 * Javascript that creates comment form at reply link
 * 
 * @since       0.1 
 * @uses        is_singular() WP function
 * @uses        comments_open() WP function
 * @uses        get_option() WP function
 * @uses        wp_enqueue_script() WP function
 */
if ( !function_exists('kst_threaded_comment_reply') ) {
    function kst_threaded_comment_reply(){
        if ( is_singular() AND comments_open() AND ( get_option( 'thread_comments' ) ) )
            wp_enqueue_script( 'comment-reply' );
    }
}


/**
 * kst_widget_create_multiple_areas
 * Create multiple consectuive widget areas to be displayed in sequence
 *
 * Use 
 * Useful for floated column widget areas that share characteristics
 * Creates "named" sidebars with 
 * 
 * @since       0.1
 * @param       required array $array   The  
 * @uses        dynamic_sidebar() WP function
 */
function kst_widget_create_multiple_areas($how_many, $name, $id, $description, $args = NULL) {
    $defaults = array(
                'before_widget' => '<aside id="%1$s" class="sb_widget %2$s">',
                'after_widget'  => '</aside>',
                'before_title'  => '<h2 class="widget_title">',
                'after_title'   => '</h2>'
                );
    $args = wp_parse_args( $args, $defaults );
    
    $kst_sidebars_footer = array(); //used to loop and output multiple sidebars
    for ($i = 1; $i <= $how_many; $i++) {
        $this_id = "{$id}_{$i}";
        register_sidebar( array_merge( array(
            'name'          => "{$name} {$i}",
            'id'            => $this_id,
            'description'   => "1 of {$how_many} widget areas in footer. Does not appear if no widgets added.",
        ), $args ) );
        $kst_sidebars_footer[] = $this_id;
    }
    
    return $kst_sidebars_footer;
}

/**
 * kst_widget_output_multiple_areas
 * Output multiple widget areas in sequence
 * 
 * Useful for floated column widget areas that share characteristics
 * Creates "named" sidebars with 
 * 
 * @since       0.1
 * @param       required array $array   The  
 * @uses        dynamic_sidebar() WP function
 */
function kst_widget_output_multiple_areas($array) {
    if ( !$array ) 
        return;
    foreach ($array as $sidebar) {
        dynamic_sidebar( $sidebar );
    }
}
    
/**
 * FUNCTIONS: ADD/MODIFY/FILTER OUTPUT AND OVERRIDE EXISTING FUNCTIONALITY 
 */
    
/**
 * Set the WP automatic excerpt length
 * 
 * @since       0.1
 * @param       constant THEME_EXCERPT_LENGTH  set by KST during KST init
 * @return      string 
 */
if ( !function_exists('kst_excerpt_length') ) {
    function kst_excerpt_length() {
        return THEME_EXCERPT_LENGTH; 
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
if ( !function_exists('kst_remove_gallery_css') ) {
    function kst_remove_gallery_css( $css ) {
        return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
    }
}

/**
 * Filter the_title to include the page number if article is paged
 * 
 * @since       0.1
 * @global      $multipage
 * @global      $page
 * @global      $post
 * @param       required string $title
 * @param       required string $id
 * @return      string
 */
if ( !function_exists('kst_the_title') ) {
    function kst_the_title($title, $id) {
        global $multipage, $page, $post;
        /* If current post is multipage AND the post title we are filtering is for the same post we are viewing 
         * Otherwise this filter caused other functions using the_title (e.g. next_post_link() ) to incorrectly get filtered  
         */
        if ( $multipage && $page > 1 && $id == $post->ID )
            $title .= " (Page {$page})";
        return $title;
    }
}

/** 
 * kst_format_wp_list_comments filtering wp_list_comments();
 * 
 * Callback function to filter/replace the default WP comments markup
 * Default markup is limiting. Customize carefully though and be nice to plugins
 * 
 * li is not closed by WordPress design; closed automatically on output
 *
 * @global      $comment
 * @uses        comment_class()
 * @uses        comment_ID()
 * @uses        get_avatar()
 * @uses        __()
 * @uses        get_comment_link()
 * @uses        edit_comment_link()
 * @uses        get_comment_author_link()
 * @uses        get_comment_date()
 * @uses        get_comment_time()
 * @uses        comment_text()
 * @uses        comment_reply_link()
 */
if ( !function_exists('kst_format_wp_list_comments') ) {
    function kst_format_wp_list_comments($comment, $args, $depth) {
        
        $GLOBALS['comment'] = $comment; ?>
        
        <?php if ($comment->comment_approved == '0') : ?>
            <li class="comment-moderate"><?php _e('Your comment is awaiting moderation.') ?></li>
        <?php endif; ?>
        
        <li id="li-comment-<?php comment_ID() ?>" <?php comment_class('clearfix'); ?>>
            
            <?php if ( get_option('show_avatars') ) : ?>
                <div class="comment_avatar">
                    <?php echo get_avatar($comment, $size='48' ); ?>
                </div>
            <?php endif; ?>
            
            <div id="comment-<?php comment_ID(); ?>" <?php comment_class('clearfix'); ?>>
                <div class="comment-author vcard">
                    <?php 
                        //printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link())
                        printf(__('<cite class="fn">%s</cite>'), get_comment_author_link())
                    ?>
                </div>
            
                <div class="comment-meta commentmetadata">
                    <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"
                    ><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a>
                </div>
                
                <div class="comment-comment">
                    <?php comment_text(); ?>
                </div>
                
                <div class="reply">
                    <?php 
                        comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth'])));
                        edit_comment_link(__('Edit'),'  ',''); 
                    ?>
                </div>
            </div>
    <?php
    }
}
        
/**
 * kst_caption_shortcode_filtered 
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
 * @since       0.1
 * @param       required array $attr
 * @param       optional string $content
 * @uses        apply_filters() WP function
 * @uses        shortcode_atts() WP function
 * @uses        do_shortcode() WP function
 * @return      string
 * @link        http://wpengineer.com/filter-caption-shortcode-in-wordpress/
 *              Thanks wpengineer for having dealt with this already
 */
if ( !function_exists('kst_caption_shortcode_filtered') ) {
    function kst_caption_shortcode_filtered($attr, $content = null) {
     
        // Allow plugins/themes to override the default caption template.
        $output = apply_filters('img_caption_shortcode', '', $attr, $content);
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


/* FUNCTIONS: Admin ONLY
 */
if ( is_admin() ) {
    require_once WP_PLUGIN_DIR . '/kitchen-sink-html5-base/_application/libraries/kst_fn_admin.php';
}
    


