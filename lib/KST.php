<?php 
/**
 * KST_Options
 * Kitchen Sink Class: Options
 * Methods to rapidly create and use WordPress options (and just admin content pages)
 * Creates menu item and builds options/content page using options array you create
 * 
 * @author		zoe somebody
 * @link        http://beingzoe.com/
 * @author		Scragz
 * @link        http://scragz.com/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  KitchenSinkClasses
 * @version     0.1
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 */
class KST {
    /**
     * Set is_plugins_loaded to false until WP tells us that the plugins are loaded
     * Hook set in init.php
     * @see     KST::plugins_are_loaded()
     */
    private static $_plugins_are_loaded = false;
    
    /**
     * Theme/Plugin "activates" the plugin 
     * Allow designers/developers to choose what and how they use KST
     * 
     * @since 0.1
     */
    public function __construct( $settings ) {
        KST::init_core();
        /* Make sure they set a 'prefix' in their setting array or give help */
        if ( isset($settings['prefix']) ) { // Need an id to proceed
            
            /* Define a variable variable constant for each doodad so they can reference their own object */
            define($settings['prefix'], $settings['prefix']); // constant named by prefix with a value of it's own name
        } else { // Help them
            exit('<h1>No "prefix" has been given in your settings array</h1><p>Make sure you have created a settings array with the required settings included</p>');
        }
    }
    
    /**
     * Set is_plugins_loaded
     * Flag to know whether we are initializing a plugin or the active theme
     * Hook set in init.php
     * 
     * @since       0.1
     
     */
     public static function plugins_are_loaded() {
         self::$_plugins_are_loaded = true;
     }
     
    /**
     * Get is_plugins_loaded
     * Flag to know whether we are initializing a plugin or the active theme
     * 
     * @since       0.1
     */
     public static function are_plugins_loaded() {
         return self::$_plugins_are_loaded;
     }
     
    
    /**
     * PUBLIC STATIC INIT FEATURE METHODS
     */
     
    /**
     * KST presets
     * 
     * @since       0.1
     */
    public static function new_with_preset_configuration( $settings, $preset = 'default') {
        $new = self.new($settings);
        switch ($preset) {
            case 'minimum':
                self::init_sensible_defaults();
                self::init_help();
            break;
            case 'and_the_kitchen_sink':
                self::init_sensible_defaults();
                self::init_help();
                self::init_seo();
                self::init_contact();
                self::init_widget_nav_post();
                self::init_widget_nav_posts();
                self::init_wp_media_normalize();
                self::init_kst_jquery_jit_message();
                self::init_widget_jit_sidebar();
                self::init_widget_kst_jquery_cycle();
                self::init_kst_jquery_tools_scrollable();
            break;
            default:
                self::init_sensible_defaults();
                self::init_help();
                self::init_seo();
                self::init_contact();
            break;
        }
        return $new;
    }
    
    /**
     * Load KST Base classes
     * 
     * @since       0.1
     */
    public static function init_base() {
        /**
         * KST itself needs options so just require the class for everyone
         * @see     settings_core.php
         */
        require_once KST_DIR_LIB . '/KST/Options.php';
        require_once KST_DIR_LIB . '/KST/Doodad.php';
    }
     
     
    /**
     * HTML5 Boilerplate, WP normalization, and smart stuff
     * 
     * @since       0.1
     */
    public static function init_sensible_defaults() {
        require_once KST_DIR_LIB . '/functions/wp_sensible_defaults.php';
        require_once KST_DIR_LIB . '/functions/wp_admin.php';
    }
    
    /**
     * KST extensible help file(s)
     * 
     * @since       0.1
     */
    public static function init_help() {
        require_once KST_DIR_LIB . '/functions/theme_help.php';  
    }
    
    /**
     * KST basic SEO
     * Require KST_options (autoloads if necessary)
     * Creates it's own options
     * Creates a meta box for editing SEO per post page
     * 
     * @since       0.1
     */
    public static function init_seo() {
        require_once KST_DIR_LIB . '/functions/seo.php';  
    }
    
    /**
     * KST email contact functionality
     * 
     * @since       0.1
     * @todo        Make this
     */
    public static function init_contact() {
          
    }
    
    
    /**
     * WP WIDGET: KST post to post next/previous post buttons for sidebar (only on single blog posts)
     * 
     * @since       0.1
     */
    public static function init_widget_nav_post() {
          require_once KST_DIR_LIB . '/KST/Widget/NavPost.php'; 
    }
    
    /**
     * WP WIDGET: KST Page to page older/newer browse posts buttons for sidebar (only on single blog posts)
     * 
     * @since       0.1
     */
    public static function init_widget_nav_posts() {
          require_once KST_DIR_LIB . '/KST/Widget/NavPosts.php'; // Page to page posts older/newer (only on indexes i.e. blog home, archives)
    }
    
    /**
     * WP media normalization bundled preset
     * lightbox, mp3player, etc...
     * 
     * @since       0.1
     */
    public static function init_wp_media_normalize() {
        self::init_kst_jquery_lightbox();
        self::init_kst_mp3_player();
    }
    
    /**
     * KST/jQuery: lightbox library using fancybox and sensible integration 
     * with all posts/pages and attachment/caption normalization.
     * Especially filters hacks for [gallery] shortcode
     * 
     * @since       0.1
     */
    public static function init_kst_jquery_lightbox() {
        require_once KST_DIR_LIB . '/functions/jquery/lightbox.php'; // javascript lightbox; 
    }
    
    /**
     * KST mp3player
     * auto load mp3 player when mp3 linked directly with shortcodes
     * used in attachment.php if exists
     * 
     * @since       0.1
     */
    public static function init_kst_mp3_player() {
         require_once KST_DIR_LIB . '/functions/mp3_player.php'; // mp3 player shortcode - 
    }
    
    
    
    /**
     * KST/jQuery: KST JIT (Just-in-Time) message (sliding out a panel on a trigger)
     * Creates a meta box for easily selecting the post to link to or the message to "say"
     * 
     * @since       0.1
     */
    public static function init_kst_jquery_jit_message() {
        require_once KST_DIR_LIB . '/functions/jquery/jit_message.php'; 
    }
    
    /**
     * WP WIDGET: KST JIT (Just-in-Time) Sidebar (Magic relative/fixed sidebars)
     * Throw in sidebar widgets just above the last few items you want to scroll down the page
     * 
     * @since       0.1
     */
    public static function init_widget_jit_sidebar() {
        require_once KST_DIR_LIB . '/KST/Widget/JITSidebar.php';
    }
    
    /**
     * KST/jQuery: malsup cycle content (content slideshow with shortcodes)
     * Once invoked you can use cycle normally as well
     * 
     * @since       0.1
     */
    public static function init_widget_kst_jquery_cycle() {
        require_once KST_DIR_LIB . '/functions/jquery/cycle.php'; 
    }
    
    /**
     * KST/jQuery: tools: scrollable (content slideshow with shortcodes)
     * Once invoked you can use cycle normally as well
     * 
     * @since       0.1
     */
    public static function init_kst_jquery_tools_scrollable() {
        require_once KST_DIR_LIB . '/functions/jquery/scrollables.php';
    }

}
