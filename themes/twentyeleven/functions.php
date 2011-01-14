<?php
/**
 * Kitchen Sink Parent Theme (KST) based on HTML5 Boilerplate and ZUI
 * 
 * 
 * 
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     WordPress
 * @subpackage  kitchenSinkTheme
 * @version     0.4
 * @since       0.1
 * 
 * 1) Set your settings
 * 2) INSTANTIATE Kitchen Sink HTML5 Base 
 * 3) Load whatever KST you want/need
 * 4) Use it
 * 
 *  1) Initialize Parent Theme (happens after Child Theme functions.php has loaded)
 *  2) Activate Theme
 *  3) Execute Theme (Error check child theme or load stand-alone theme stuff)
 *  4) Theme Functions
 */

/* SETTINGS
 */
     
    /**
     * Various required KST settings
     */
    $kst_settings = array(
        'theme_name'                => 'Twenty Eleven',                 //required; friendly name used by all widgets, libraries, and classes
        'theme_id'                  => 'ksd_0_1',                       //required; Prefix for namespacing libraries, classes, widgets
        'theme_developer'           => 'Joe',                           //required; friendly name of current developer; only used for admin display;
        'theme_developer_url'       => 'http://google.com/',            //required; full URI to developer website;
        'content_width'             => 500,                             //required; as a global variable mainly used by WP but will bear in mind in KST as a constant; maximum width of images in posts
        'theme_excerpt_length'      => 100
    );
 
     /*
    $theme_name             = "Kitchen Sink Demo";      
    $theme_id               = "ksd_0_1";                
    $theme_developer        = "zoe somebody";           
    $theme_developer_url    = "http://beingzoe.com/";   
    */
    
    /* WP required */
    
    
    /* Things that need put into the plugin */
    $kst_meta_title_sep_default       = "&laquo;";
    $theme_excerpt_length             = 100; //
    
    
    /* Only needed if you are using the built-in KST_OPTIONS CLASS (make whatever options you like */
    $theme_options = array (
            // Layout options
            array(  "name"      => __('Layout'),
                    "desc"      => __("
                                    <p><em>Options affecting the layout and presentation of your site.</em></p>
                                    <p>See \"Appearance &gt; Theme Help\" to learn more about asides.</p>
                                "),
                    "type"      => "section",
                    "is_shut"   => FALSE ),
            
            array(  "name"      => __('Asides Category'),
                    "desc"      => __('Pick the category to use as your sideblog'),
                    "id"        => "layout_category_aside",
                    "type"      => "select_category",
                    "args"      => array( )
                    ),
            
            array(  "name"      => __('Gallery Category SLUG'),
                    "desc"      => __('Pick the category to use for gallery posts'),
                    "id"        => "layout_category_gallery",
                    "type"      => "select_category",
                    "args"      => array( )
                    )
        );
            
        
/* INSTANTIATE KITCHEN SINK HTML5 BASE 
 * Presets a few necessary things and will be used later to enhance functionality
 */ 
    //require_once WP_PLUGIN_DIR . '/kitchen-sink-html5-base/kitchen-sink-html5-base.php'; // Uncomment and edit path if you want to use KST without it showing up in plugin list
    //$test = new KST_HTML5_BASE(); // Make it go
    //NOTE: This is a temporary hack until we decide on how we can protect the theme if somehow KST isn't loaded (ala turning off the plugin)
    if ( function_exists( 'kst_theme_init' ) ) {
        kst_theme_init($kst_settings);
    } else {
        // Having a FUN and useful help message would be cool.
        echo "<h1>Pretty cool!<br />You are using a Kitchen Sink based WordPress theme<br />HOWEVER...</h1><p>...you have not activated the KST Plugin in WordPress OR you haven't included it as library in your theme.<br />See the <a href='http://beingzoe.com/zui/wordpress/kitchen_sink_theme'>documentation</a> if you need assistance.</p><p><a href='#'>Sign in</a> to WordPress.";
        // Needs to check if it is in the admin section OR in the login page (login is not in the admin)
        if ( is_admin() ) {
            return;
        } else {
            exit;
        }
        
    }

        
/* INCLUDE THEME-WIDE LIBRARIES/FUNCTIONALITY
 * This is why you are using KST...
 */
        
    /* KST sensible defaults (no hassle html5 and and core stuff) 
     * Don't include if you want to do it ALL yourself and just want access to the classes/libraries/functionality
     */ 
    require_once KST_DIR_LIB . '/KST/functions/wp_sensible_defaults.php';
    
    /* KST_Options
     * Uses your $theme_options array(s) to create admin menus/pages 
     */ 
    require_once KST_DIR_LIB . '/KST/Options.php';
    /* Add your menus/pages */ 
    $kst_options = new KST_Options(THEME_ID, THEME_NAME, 'theme_options', 'top', 'Theme Options');
    $more_options = new KST_Options(THEME_ID, THEME_NAME, 'theme_options2', $kst_options, 'More Options', 'My CUSTOM page TITLE');
    $more_options2 = new KST_Options(THEME_ID, THEME_NAME, 'theme_options2', $kst_options, 'More Options2', 'Important Settings');
    
    /* KST SEO and META DATA */
    require_once KST_DIR_LIB . '/KST/functions/theme_meta_data.php'; // SEO and other meta data built-in; Dependent on theme_options;
    
    /* KST THEME HELP */
    require_once KST_DIR_LIB . '/KST/functions/theme_help.php'; // Activates and includes admin help file theme_help.php 
    
    /* KST mp3 player with shortcode */
    require_once KST_DIR_LIB . '/KST/functions/mp3_player.php'; // mp3 player shortcode - used in attachment.php if exists
    
    /* KST/jQuery: lightbox library using fancybox */
    require_once KST_DIR_LIB . '/KST/functions/jquery/lightbox.php'; // javascript lightbox; includes hacks for [gallery] shortcode
    
    /* KST/jQuery: Content slideshow - CHOOSE either scrollables or cycle */
        /* KST/jQuery: tools: scrollable */
        require_once KST_DIR_LIB . '/KST/functions/jquery/scrollables.php'; // javascript content slideshow; create scrollable and shortcode to use it
        /* KST/jQuery: malsup cycle content */
        require_once KST_DIR_LIB . '/KST/functions/jquery/cycle.php'; // javascript content slideshow; create scrollable and shortcode to use it
        
    /* KST/jQuery: KST JIT message */
    require_once KST_DIR_LIB . '/KST/functions/jquery/jit_message.php'; // javascript JIT (just in time) message box and metabox custom fields to use it
    
    /* KST SEND MAIL - ??? */
    //require_once KST_DIR_LIB . '/KST/functions/zui_send_mail.php'; // send email abstraction functions    
    
    /* WIDGETS
     */
        /* KST next/previous post buttons for sidebar */
        require_once KST_DIR_LIB . '/KST/Widget/NavPost.php'; // Post to post next/previous buttons (only on single blog posts)
        
        /* KST older/PREVIOUS post buttons for sidebar */
        require_once KST_DIR_LIB . '/KST/Widget/NavPosts.php'; // Page to page posts older/newer (only on indexes i.e. blog home, archives)
        
        /* KST JIT (Just-in-Time Sidebar */
        require_once KST_DIR_LIB . '/KST/Widget/JITSidebar.php'; // Magic floating sidebars
    
    /**
     * @uses    -the plugin path
     * @uses    KST_ASIDE_ASIDES class
     * @uses    KST_Options::get_option() 
     * @uses    is_admin() WP function
     */
    if ( !is_admin() ) {
        /* Load and use KST_Asides class to manage asides side blog */
        require_once KST_DIR_LIB . '/KST/Asides.php'; // Class to save aside post for clean delayed output
        $loop_asides = new KST_Asides( $kst_options->get_option('layout_category_aside') );
    }

    
    
    
    
    
    
/* BELOW HERE will not be in the base as is 
 * but there will be some placeholder sections for this stuff with some examples maybe? 
 * 
 * Some of the stuff below was just to run the TwentyEleven theme 
 * and will not be included at all is duly noted
 */

    /* ADD AND REMOVE JUNK - 
     * See documentation (online or in plugin) for things that are added and removed by default as sensible defaults
     */
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wlwmanifest_link');
        
    /**
     * REGISTER THEME MENUS WITH BUILT-IN WP MENUS
     * Also set default fallback menu functions
     */
        register_nav_menu('hd_menu', 'Masthead Menu'); //primary site nav
        register_nav_menu('ft_menu', 'Footer Menu'); //footer nav
        
    /**
     * REGISTER WIDGETIZED SIDEBAR/AREAS
     */
     
     
                                                                                             /* NOT PART OF KST - just something I like to do
                                                                                              * DRY: Widget area formatting arguments
                                                                                              * Sets theme-wide widgetized area formatting arguments
                                                                                              */
                                                                                             $kst_widget_area_format_args = array( /* Common sidebar */
                                                                                                                           'before_widget' => '<aside id="%1$s" class="sb_widget %2$s">',
                                                                                                                           'after_widget'  => '</aside>',
                                                                                                                           'before_title'  => '<h2 class="widget_title">',
                                                                                                                           'after_title'   => '</h2>'
                                                                                                                       );
                                                                                             $kst_widget_area_format_args_footer = array( /* Common footer */
                                                                                                                            'before_widget' => '<aside id="%1$s" class="ft_widget %2$s">',
                                                                                                                            'after_widget'  => '</aside>',
                                                                                                                            'before_title'  => '<h2 class="widget_title">',
                                                                                                                            'after_title'   => '</h2>'
                                                                                                                        );
    
        
        
        /* Blog only primary sidebar - all posts, archives, etc... */
        register_sidebar( array_merge( array(
            'name'          => 'Blog Sidebar',
            'id'            => 'widgets_blog',
            'description'   => 'Sidebar content for blog articles',
        ), $kst_widget_area_format_args ) );
        /* Pages only primary sidebar - assuming CMS style theme */
        register_sidebar( array_merge( array(
            'name'          => 'Pages Sidebar',
            'id'            => 'widgets_pages',
            'description'   => 'Sidebar content for pages',
        ), $kst_widget_area_format_args ) );
        /* Home (frontpage) only primary sidebar */
        register_sidebar( array_merge( array(
            'name'          => 'Home Sidebar',
            'id'            => 'widgets_home',
            'description'   => 'Sidebar content for home page',
        ), $kst_widget_area_format_args ) );
        
        /* For the footer */
        $kst_sidebars_footer = kst_widget_create_multiple_areas( 3, 'Footer Area', 'widgets_ft', 'One of three widget areas in footer. Does not appear if no widgets added.', $kst_widget_area_format_args_footer ); // Create multiple consecutive named-numbered widget areas ("sidebars") e.g. wfooter_1, wfooter_2, wfooter_3; args = $how_many, $name, $id, $description
            

        


/**
 * CLEAN UP
 * Anything to error check or add to child theme features?
 * Only really important if you are also developing a child theme 
 * or distributing your parent theme expecting people to make child themes for it
 */ 
 
    /* Hook to do stuff after child themes functions.php loads */
    add_action( 'after_setup_theme', 'kst_after_child_theme' ); // Delay final setup until both Parent and Child theme functions.php are loaded
    
    /* Do that hook you gotta do */
    function kst_after_child_theme() { // Fix/Undo/Check things that a child theme maybe forgot to do or did wrong */
        
        
    } // END kst_theme_execute()




























/* Below here may or may not be involved with the base theme as well as a number of other things above here */

    /* TwentyEleven: Custom background */
    add_custom_background();
    
    /* TwentyEleven: Custom Header */
    define( 'HEADER_TEXTCOLOR', '' );
    define( 'HEADER_IMAGE', '%s/_assets/images/headers/twentyten/path.jpg' ); // The %s is a placeholder for the theme template directory URI.
    define( 'HEADER_IMAGE_WIDTH', apply_filters( 'twentyten_header_image_width', 940 ) );
    define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'twentyten_header_image_height', 198 ) );
    define( 'NO_HEADER_TEXT', true );
    add_custom_image_header( 'kst_add_custom_image_header', 'kst_add_custom_image_header_admin' );
    
    register_default_headers( array(
        'concave' => array(
            'url' => '%s/_assets/images/headers/twentyten/concave.jpg',
            'thumbnail_url' => '%s/_assets/images/headers/twentyten/concave-thumbnail.jpg',
            'description' => __( 'Concave', 'twentyten' )
        ),
        'forestfloor' => array(
            'url' => '%s/_assets/images/headers/twentyten/forestfloor.jpg',
            'thumbnail_url' => '%s/_assets/images/headers/twentyten/forestfloor-thumbnail.jpg',
            'description' => __( 'Forest Floor', 'twentyten' )
        ),
        'path' => array(
            'url' => '%s/_assets/images/headers/twentyten/path.jpg',
            'thumbnail_url' => '%s/_assets/images/headers/twentyten/path-thumbnail.jpg',
            'description' => __( 'Path', 'twentyten' )
        )
    ) );

    /**
     * Front-end callback function for add_custom_image_header();
     * Use to insert styles or whatever 
     * 
     * @since 0.4
     */
    function kst_add_custom_image_header() { 
        
    }
    
    /**
     * Admin callback function for add_custom_image_header();
     * Use to insert styles or whatever; Required by WP;
     * 
     * @since 0.4
     */
    function kst_add_custom_image_header_admin() { 
        
    }


            /* Sample Bonus array to test extra menus and stuff */
            $theme_options2 = array ( 
                array(  "name"    => __('Some notes'),
                        "desc"  => __("
                                    <p><em>Just wanted to say hi and let you know in this demo this array appears as two different menu items.</em></p>
                                    "),
                        "type"  => "section",
                        "is_shut"   => FALSE ),
                
                array(  "name"  => __('Favorite color'),
                        "desc"  => __('Red? Green? Blue?'),
                        "id"    => "favorite_color",
                        "default"   => "",
                        "type"  => "text",
                        "size"  => "15"), 
                
                array(  "name"    => __('TEST2'),
                        "desc"  => __("
                                    
                                    "),
                        "type"  => "section"),
                
                array(  "name"  => __('TEST RADIO BUTTON'),
                        "desc"  => __('What choice will you make?'),
                        "id"    => "TEST_RADIO_BUTTON",
                        "default"   => "this radio 3",
                        "type"  => "radio",
                        "options" => array(     "this radio 1", 
                                                "this radio 2", 
                                                "this radio 3", 
                                                "this radio 4", 
                                                "this radio 5"
                                                    )
                        ),
                
                
                
                array(    "name"    => __('Textarea'),
                                "desc"    => __("What you type here will indicate the possibility of success."),
                                "id"      => "{$theme_id}_textarea_id",
                                "std"     => __("You do not have to put any defaults"),
                                "type"    => "textarea",
                                "rows" => "2",
                                "cols" => "55"
                                ),
                
                array(    "name"    => __('Select'),
                                "desc"    => __("There are many choices awaiting"),
                                "id"      => "TEST_SELECT",
                                "default"     => "Select 4",
                                "type"    => "select",
                                "options" => array(    "Select 1", 
                                                    "Select 2",
                                                    "Select 3",
                                                    "Select 4",
                                                    "Select 5"
                                                    ) 
                                ),
                
                array(  "name"  => __('Asides Category'),
                        "desc"  => __('Pick the category to use as your sideblog'),
                        "id"    => "TEST_ASIDES_CATEGORY_SELECTOR",
                        "type"  => "select_category",
                        "args" => array(     
                                            
                                                    )
                        ),
                
                array(  "name"  => __('Featured Page'),
                        "desc"  => __('Choose the page to feature'),
                        "id"    => "TEST_PAGE_SELECTOR",
                        "type"  => "select_page",
                        "args" => array(     
                                            
                                                    )
                        ),
                
                array(    "name"    => __('MultiSelect'),
                                "desc"    => __("There are many choices awaiting and you can have them all"),
                                "id"      => "TEST_MULTISELECT",
                                "default"     => "Select 5",
                                "type"    => "select",
                                "multi"   => TRUE,
                                "size"   => "8",
                                "options" => array(    "Select 1", 
                                                    "Select 2",
                                                    "Select 3",
                                                    "Select 4",
                                                    "Select 5",
                                                    "Select 6",
                                                    "Select 7",
                                                    "Select 8"
                                                    ) 
                                )
                
            );

