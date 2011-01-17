<?php
/**
 * Twenty Eleven Parent Theme based on Kitchen Sink HTML5 Base
 * 
 * Awesomeness description
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
 * 4) Make Theme
 */
 
 /* TESTING: Checking the menu for shit
 add_action('admin_footer', 'test3223');
 function test3223() {
     print_r($GLOBALS['menu']);
 }
 */
  
 
/* SETTINGS
 */
     
    /**
     * Various Kitchen Sink HTML5 Base settings
     */
    $kst_settings = array(
        /* REQUIRED */
        'theme_name'                => 'Twenty Eleven',                 // Required; friendly name used by all widgets, libraries, and classes; can be different than the registered theme name
        'prefix'                 => 'ksd_0_1',                       // Required; Prefix for namespacing libraries, classes, widgets
        'theme_developer'           => 'zoe somebody',                           // Required; friendly name of current developer; only used for admin display;
        'theme_developer_url'       => 'http://beingzoe.com/',            // Required; full URI to developer website;
        'content_width'             => 500,                             // Required; as a global variable mainly used by WP but will bear in mind in KST as a constant; maximum width of images in posts
        'theme_excerpt_length'      => 100,
        /* OPTIONAL */
        'theme_seo_title_sep'       => '&laquo;',                       // Optional; Separator between title bar title segments
    );
    
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
    
    /**#@+
     * The farther we get down this list the more likely it is low priority or to be considered for companion plugins
     * @since       0.1
     */
    //NOTE: This is a temporary hack until we decide on how we can protect the theme if somehow KST isn't loaded (ala turning off the plugin) but this is the criteria
    if ( class_exists('KST') ) {

        
        /* Invoke the plugin to use it */
        KST::init($kst_settings);
        
        
        //echo KST::{ksd_0_1}->testme;
        
        
        
        /* OPTIONAL: Load preset configuration  
         * default, minimum, and_the_kitchen_sink */
        //$KST::init_preset_configuration('and_the_kitchen_sink');
        
        /* OPTIONAL: Load individual functionality - Autoloading classes? (have that just in case but also allow loading through here?) */
        
        /* Load and use KST_Options
         * Uses your $theme_options array(s) to create admin menus/pages 
         */ 
        require_once KST_DIR_LIB . '/KST/Options.php';
        /* Add your menus/pages */ 
        $twenty_eleven_options = new KST_Options('theme_options', 'top', 'Theme Options');
        $more_options = new KST_Options('theme_options2', $twenty_eleven_options, 'More Options', 'My CUSTOM page TITLE');
        $more_options2 = new KST_Options('theme_options2', $twenty_eleven_options, 'More Options2', 'Important Settings');
        $twenty_eleven_options2 = new KST_Options('theme_options', 'top', 'Other Options');
        $more_options = new KST_Options('theme_options2', $twenty_eleven_options2, 'More Other', 'My CUSTOM page TITLE');
        $more_options2 = new KST_Options('theme_options2', $twenty_eleven_options2, 'More Other2', 'Important Settings');
        
        /* HTML5 Boilerplate, WP normalization, and smart stuff */
        KST::init_sensible_defaults();
        /* KST THEME HELP */
        KST::init_help();
        
        
        
        /* KST SEO and META DATA */
        KST::init_seo();
        /* KST easy flexible email and contact forms */
        KST::init_contact();
        
        /* Load and use KST_Asides class to manage asides side blog */
        require_once KST_DIR_LIB . '/KST/Asides.php'; // Class to save aside post for clean delayed output
        $loop_asides = new KST_Asides( $twenty_eleven_options->get_option('layout_category_aside') );
        
        /* WP WIDGET: KST post to post next/previous post buttons for sidebar (only on single blog posts) */
        KST::init_widget_nav_post();
        /* WP WIDGET: KST Page to page older/newer browse posts buttons for sidebar (only on single blog posts) */
        KST::init_widget_nav_posts();
        
        /* WP Media Normalization: preset for all media normalization: Auto lightboxing, mp3player, etc... */
        KST::init_wp_media_normalize();
        //$KST::init_kst_jquery_lightbox();
        //$KST::init_kst_mp3_player();
        
        /* OPTIONAL: Should become separate plugin(s) package(s) */
        
        /* KST/jQuery: KST JIT (Just-in-Time) message (sliding out a panel on a trigger) */
        KST::init_kst_jquery_jit_message();
        /* WP WIDGET: KST JIT (Just-in-Time) Sidebar (Magic relative/fixed sidebars)  */
        KST::init_widget_jit_sidebar();
        /* KST/jQuery: tools: scrollable content (content slideshow with shortcodes)  */
        KST::init_kst_jquery_tools_scrollable();
        /* KST/jQuery: malsup cycle content (content slideshow with shortcodes) */
        KST::init_widget_kst_jquery_cycle();
        
        /**#@-*/

        //echo KST::{THEME_ID}->testme;
        
        
    } else {
        // Needs to check if it is in the admin section OR in the login page (login is not in the admin)
        if ( is_admin() ) {
            return;
        } else {
            // Having a FUN and useful help message would be cool.
        echo "<h1>Pretty cool!<br />You are using a Kitchen Sink based WordPress theme<br />HOWEVER...</h1><p>...you have not activated the KST Plugin in WordPress OR you haven't included it as library in your theme.<br />See the <a href='http://beingzoe.com/zui/wordpress/kitchen_sink_theme'>documentation</a> if you need assistance.</p><p><a href='#'>Sign in</a> to WordPress.";
            exit;
        }
        
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
                                                                                                                           'before_widget' => '<aside id="%1$s" class="widget %2$s">',
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
                                "id"      => "textarea_id",
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

