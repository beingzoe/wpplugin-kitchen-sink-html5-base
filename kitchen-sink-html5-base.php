<?php
/**
Plugin Name:    Kitchen Sink HTML5 Base
Plugin URI:     http://beingzoe.com/zui/wordpress/kitchen_sink_theme
Description:    Library of awesomeness and a "reset" to create a sensible starting point
Version:        0.1
Author:         zoe somebody
Author URI:     http://beingzoe.com/
License:        MIT
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     WordPress 
 * @subpackage  KitchenSinkPlugin
 */
 
/**
 * kst_theme_init
 * Define contstants used throughout KST
 *
 * @param $options array
 * @see THEME_NAME_CURRENT
 * @see THEME_ID
 * @see THEME_DEVELOPER
 * @see THEME_DEVELOPER_URL
 * @see THEME_HELP_URL path to theme help file
 * @see THEME_OPTIONS_URL
 * @see CONTENT_WIDTH
 * @see THEME_EXCERPT_LENGTH
 * @global $content_width WP width used to protect layout by limiting content width; WP best practice
 * @global $theme_excerpt_length Override default WP excerpt length; Used by kst_excerpt_length() filter
 */
function kst_theme_init($options) {
    global $content_width, $theme_excerpt_length;

    $default_options = array(
        'theme_name' => 'Kitchen Sink',
        'theme_id' => 'kst_0_2',
        'theme_developer' => 'zoe somebody',
        'theme_developer_url' => 'kst_0_2',
        'content_width' => 500,
        'theme_excerpt_length' => 100
    );
    $options = array_merge( $default_options, $options );

    /**#@+
     * KST theme settings constant
     */
    define( 'THEME_NAME_CURRENT',    get_current_theme() );
    define( 'THEME_NAME',            $options['theme_name'] );
    define( 'THEME_ID',              $options['theme_id'] );
    define( 'THEME_DEVELOPER',       $options['theme_developer'] );
    define( 'THEME_DEVELOPER_URL',   $options['theme_developer_url'] );
    define( 'THEME_HELP_URL',        "themes.php?page=" . THEME_ID . "_help" ); // path to theme help file
    define( 'THEME_OPTIONS_URL',     "themes.php?page=" . THEME_ID . "_options" ); // path to theme options
    /**
     * WP width used to protect layout by limiting content width; WP best practice
     */
    define( 'CONTENT_WIDTH',          $options['content_width'] );
    /**
     * Override default WP excerpt length; Used by kst_excerpt_length() filter
     * @see kst_excerpt_length()
     */
    define( 'THEME_EXCERPT_LENGTH',   $options['theme_excerpt_length'] );
    /**#@-*/

    $content_width = CONTENT_WIDTH;
    $theme_excerpt_length = THEME_EXCERPT_LENGTH;
}
        

/**
 * FUNCTIONS
 * All functions are "pluggable" by themes unless noted otherwise
 */
      
    /* FUNCTIONS: ADD AND REMOVE WP JUNK
     */
    
    /**
     * kst_admin_login_css
     * Load custom stylesheet for admin and wp-login (they share a stylesheet) 
     *
     * NOT ADMIN ONLY because wp-login is not treated as part of admin
     *
     * @since       0.3
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
     * @since 0.3 
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
     * @since 0.4
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
     * @since 0.4
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
     * @since       0.3
     * @global      $theme_excerpt_length
     * @return      string 
     */
    if ( !function_exists('kst_excerpt_length') ) {
        function kst_excerpt_length() {
            global $theme_excerpt_length;
            return $theme_excerpt_length;
        }
    }
     
    /**
     * Remove inline styles printed when the gallery shortcode is used.
     * From twentyten
     * 
     * @since       0.3
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
     * @since       0.4
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
    if ( is_admin() )
        require_once WP_PLUGIN_DIR . '/kitchen-sink-html5-base/_application/libraries/kst_fn_admin.php';
    
?>
