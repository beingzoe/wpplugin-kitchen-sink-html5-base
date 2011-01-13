<?php
/**
 * Initialize Theme with sensible defaults 
 * and spare yourself needless junk in your functions.php 
 * and get all of the HTML5 goodness ready to go
 */ 

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
?>
