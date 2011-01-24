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
 * @subpackage  Core
 * @version     0.1
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
*/
class KST {

    /**
     * Set is_plugins_loaded to false until WP tells us that the plugins are loaded
     * Hook set in init.php
     *
     * @since       0.1
     * @access      private
     * @see         KST::pluginsAreLoaded()
    */
    protected static $_plugins_are_loaded = false;

    /**#@+
     * Core protected variables to keep tabs on all the kitchens
     *
     * @since       0.1
     * @access      protected
    */
    protected static $extant_options; // Array of options that exist IF they were checked with $this->option_exists();
    protected static $all_admin_pages; // Store all menus/pages from all registered kst kitchens
    /**#@-*/


    /**
     * KST presets
     *
     * @since       0.1
    */
    public static function initPreset( $feature = 'default') {
        switch ($preset) {
            case 'minimum':
                self::initSensibleDefaults();
                self::initHelp();
            break;
            case 'default':
                self::initSensibleDefaults();
                self::initHelp();
                self::initSEO();
                self::initContact();
            break;
            case 'and_the_kitchen_sink':
                self::initSensibleDefaults();
                self::initHelp();
                self::initSEO();
                self::initContact();
                self::initWidgetNavPost();
                self::initWidgetNavPosts();
                self::initWPMediaNormalize();
                self::initJqueryJitMessage();
                self::initWidgetJitSidebar();
                self::initJqueryCycle();
                self::initJqueryToolsScrollable();
            break;
            default:
                $method = $feature;
                self::$method();
            break;
        }
    }


    /**
     * HTML5 Boilerplate, WP normalization, and smart stuff
     *
     * @since       0.1
    */
    public static function initSensibleDefaults() {
        require_once KST_DIR_LIB . '/functions/wp_sensible_defaults.php';
        require_once KST_DIR_LIB . '/functions/wp_admin.php';
    }


    /**
     * KST extensible help file(s)
     *
     * @since       0.1
    */
    public static function initHelp() {
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
    public static function initSEO() {
        require_once KST_DIR_LIB . '/functions/seo.php';
    }


    /**
     * KST email contact functionality
     *
     * @since       0.1
     * @todo        Make this
    */
    public static function initContact() {

    }


    /**
     * WP WIDGET: KST post to post next/previous post buttons for sidebar (only on single blog posts)
     *
     * @since       0.1
    */
    public static function initWidgetNavPost() {
        require_once KST_DIR_LIB . '/KST/Widget.php';
        KST_Widget::registerWidget('NavPost');
    }


    /**
     * WP WIDGET: KST Page to page older/newer browse posts buttons for sidebar (only on single blog posts)
     *
     * @since       0.1
    */
    public static function initWidgetNavPosts() {
        require_once KST_DIR_LIB . '/KST/Widget.php';
        KST_Widget::registerWidget('NavPosts');
    }


    /**
     * WP media normalization bundled preset
     * lightbox, mp3player, etc...
     *
     * @since       0.1
    */
    public static function initWPMediaNormalize() {
        self::initJqueryLightbox();
        self::initMp3Player();
    }


    /**
     * KST/jQuery: lightbox library using fancybox and sensible integration
     * with all posts/pages and attachment/caption normalization.
     * Especially filters hacks for [gallery] shortcode
     *
     * @since       0.1
    */
    public static function initJqueryLightbox() {
        require_once KST_DIR_LIB . '/functions/jquery/lightbox.php'; // javascript lightbox;
    }


    /**
     * KST mp3player
     * auto load mp3 player when mp3 linked directly with shortcodes
     * used in attachment.php if exists
     *
     * @since       0.1
    */
    public static function initMp3Player() {
         require_once KST_DIR_LIB . '/functions/mp3_player.php'; // mp3 player shortcode -
    }


    /**
     * KST/jQuery: KST JIT (Just-in-Time) message (sliding out a panel on a trigger)
     * Creates a meta box for easily selecting the post to link to or the message to "say"
     *
     * @since       0.1
    */
    public static function initJqueryJitMessage() {
        require_once KST_DIR_LIB . '/functions/jquery/jit_message.php';
    }


    /**
     * WP WIDGET: KST JIT (Just-in-Time) Sidebar (Magic relative/fixed sidebars)
     * Throw in sidebar widgets just above the last few items you want to scroll down the page
     *
     * @since       0.1
    */
    public static function initWidgetJitSidebar() {
        require_once KST_DIR_LIB . '/KST/Widget.php';
        KST_Widget::registerWidget('JitSidebar');
    }


    /**
     * KST/jQuery: malsup cycle content (content slideshow with shortcodes)
     * Once invoked you can use cycle normally as well
     *
     * @since       0.1
    */
    public static function initJqueryCycle() {
        require_once KST_DIR_LIB . '/functions/jquery/cycle.php';
    }


    /**
     * KST/jQuery: tools: scrollable (content slideshow with shortcodes)
     * Once invoked you can use cycle normally as well
     *
     * @since       0.1
    */
    public static function initJqueryToolsScrollable() {
        require_once KST_DIR_LIB . '/functions/jquery/scrollables.php';
    }


    /**
     * PUBLIC STATIC HELPER METHODS
    */

    /**
     * Set is_plugins_loaded via WP hook callback
     * Flag to know whether we are initializing a plugin or the active theme
     * Hook set in init.php
     *
     * @since       0.1
    */
    public static function pluginsAreLoaded() {
        self::$_plugins_are_loaded = true;
    }


    /**
     * Get is_plugins_loaded
     * Flag to know whether we are initializing a plugin or the active theme
     *
     * @since       0.1
    */
    public static function arePluginsLoaded() {
        return self::$_plugins_are_loaded;
    }


    /**
     * In order for the companion plugin concept to work we have to make sure that
     * Base loads first, so on activation we move it the start of the list.
     *
     * This might could be done better
     *
     * @since       0.1
     * @see         http://wordpress.org/support/topic/how-to-change-plugins-load-order
     * @todo        Clean this up
     * @todo        Create a hook for KST plugin developers to use to make sure they load after this
    */
    public static function loadAsFirstPlugin() {
        global $active_plugins;
        // ensure path to this file is via main wp plugin path
        $wp_path_to_this_file = preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR."/$2", __FILE__);
        $this_plugin = plugin_basename(trim($wp_path_to_this_file));
        $active_plugins = get_option('active_plugins');
        $this_plugin_key = array_search($this_plugin, $active_plugins);
        if ($this_plugin_key) { // if it's 0 it's the first plugin already, no need to continue
            array_splice($active_plugins, $this_plugin_key, 1);
            array_unshift($active_plugins, $this_plugin);
            update_option('active_plugins', $active_plugins);
        }
    }

}
