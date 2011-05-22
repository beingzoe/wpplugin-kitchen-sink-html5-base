<?php
/**
Plugin Name:    Kitchen Sink HTML5 Base
Plugin URI:     http://beingzoe.com/zui/wordpress/kitchen_sink/
Description:    Library of awesomeness and a "reset" to create a sensible starting point
Version:        0.1
Author:         zoe somebody
Author URI:     http://beingzoe.com/
License:        MIT
 **
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  Core
 * @uses        KST
 * @uses        KST_Kitchen
 * @uses        KST_Kitchen_Theme
 * @uses        KST_Kitchen_Plugin
 * @uses        KST_Appliance_Options class
 * @todo        replace WP_PLUGIN_DIR and any of these constants per Codex?
*/


/**#@+
 * CONSTANTS
 *
 * @since       0.1
 * @uses        get_site_url() WP function
 * @uses        get_current_theme() WP function
 * @uses        WP_PLUGIN_URL WP constant
*/
define( 'KST_DIR',                      dirname(__FILE__) );                                            // Absolute path to KST
define( 'KST_DIR_LIB',                  KST_DIR . '/lib' );
define( 'KST_DIR_VENDOR',               KST_DIR . '/vendor' );
define( 'KST_DIR_TEMPLATES',            KST_DIR . '/templates' );
define( 'KST_DIR_ASSETS',               KST_DIR . '/assets' );
define( 'WP_URI_SITE',                  get_site_url() );                                               // Current WP site uri
define( 'KST_URI',                      WP_PLUGIN_URL . '/' . basename(KST_DIR) );                      // Current uri to KST
define( 'KST_URI_VENDOR',               KST_URI . '/vendor' );
define( 'KST_URI_TEMPLATES',            KST_URI . '/templates' );
define( 'KST_URI_ASSETS',               KST_URI . '/assets' );
define( 'KST_THEME_NAME_CURRENT',       get_current_theme() );
define( 'KST_HELP_SECTION_SEPARATOR',   '<br /><br /><a href="#wphead">Top</a><br /><br /><br />' );    // For separating sections and creating a back to top link
/**#@-*/


/**
 * Register plugin themes directory for developing the bundled themes
 *
 * Usage: (in wp-config.php)
 *  Use KST plugin themes directory as theme_root
 * define('KST_BUNDLED_THEME_DEV', true);
 *
 * @since       0.1
 * @uses        register_theme_directory() WP function
 * @uses        KST_BUNDLED_THEME_DEV
 * @uses        WP_PLUGIN_DIR
 * @uses        KST_DIR
*/
if (defined('KST_BUNDLED_THEME_DEV') && KST_BUNDLED_THEME_DEV) {
    register_theme_directory( WP_PLUGIN_DIR . '/' . basename(KST_DIR) . '/themes' );
}


/**
 * Parent class for Kitchen Sink HTML5 Base
 *
 * @since       0.1
*/
require_once KST_DIR_LIB . '/KST.php';
require_once KST_DIR_LIB . '/KST/Kitchen.php';
require_once KST_DIR_LIB . '/KST/Kitchen/Core.php';
require_once KST_DIR_LIB . '/KST/Kitchen/Plugin.php';
require_once KST_DIR_LIB . '/KST/Kitchen/Theme.php';
require_once KST_DIR_LIB . '/KST/Appliance.php';

/**
 * Array to register core appliances
 *
 * @since       0.1
 * @see         KST::$_appliances;
*/
$kst_bundled_appliances = array(
    'wp_sensible_defaults' => array(
            'friendly_name'         => 'Theme Sensible defaults',
            'desc'                  => 'Functions and functionality that make sense for most theme functions.php',
            'path'                  => KST_DIR_LIB . '/functions/wp_sensible_defaults.php',
            'after_setup_theme'     => TRUE,
            'is_theme_only'         => TRUE
        ),
    'wp_sensible_defaults_admin' => array(
            'friendly_name'         => 'Theme Admin Sensible defaults',
            'desc'                  => 'Functions and functionality to enhance the admin for site/blog owners',
            'path'                  => KST_DIR_LIB . '/functions/wp_sensible_defaults_admin.php',
            'after_setup_theme'     => TRUE,
            'is_theme_only'         => TRUE
        ),
    'help' => array(
            'friendly_name'         => 'Help for KST',
            'desc'                  => 'Help for using WordPress and especially your KST based themes and plugins',
            'path'                  => KST_DIR_LIB . '/KST/Appliance/Help.php',
            'class_name'            => 'KST_Appliance_Help',
            'can_disable'           => TRUE
        ),
    'seo' => array(
            'friendly_name'         => 'SEO &amp; Meta',
            'desc'                  => 'Take control of your SEO and meta data.',
            'path'                  => KST_DIR_LIB . '/KST/Appliance/Seo.php',
            'class_name'            => 'KST_Appliance_Seo',
            'can_disable'           => TRUE
        ),
    'forms' => array(
            'friendly_name'         => 'Forms',
            'desc'                  => 'Methods for creating and processing forms in KST',
            'path'                  => KST_DIR_LIB . '/KST/Appliance/Forms.php',
            'class_name'            => 'KST_Appliance_Forms'
        ),
    'options' => array(
            'friendly_name'         => 'Options',
            'desc'                  => 'Methods for creating and using custom options groups',
            'path'                  => KST_DIR_LIB . '/KST/Appliance/Options.php',
            'class_name'            => 'KST_Appliance_Options'
        ),
    'metabox' => array(
            'friendly_name'         => 'Custom Field Metaboxes',
            'desc'                  => 'Methods for creating and using post_meta data with WP metaboxes',
            'path'                  => KST_DIR_LIB . '/KST/Appliance/MetaBox.php',
            'class_name'            => 'KST_Appliance_MetaBox'
        ),
    'additional_image_sizes' => array(
            'friendly_name'         => 'Additional Intermediate Image Sizes',
            'desc'                  => 'Add as many extra image sizes as you need besides the predefined WordPress defaults (thumbnail, medium, large)',
            'path'                  => KST_DIR_LIB . '/KST/Appliance/AdditionalImageSizes.php',
            'class_name'            => 'KST_Appliance_AdditionalImageSizes',
            'can_disable'           => TRUE
        ),
    'wordpress' => array(
            'friendly_name'         => 'WordPress enhancements',
            'desc'                  => 'Methods supplementing or replacing default WordPress functions',
            'path'                  => KST_DIR_LIB . '/KST/Wordpress.php',
            'class_name'            => 'KST_Wordpress'
        ),
    'widget_nav_post' => array(
            'friendly_name'         => 'Widget: KST: Next/Previous buttons',
            'desc'                  => 'Post to post navigation in the sidebar',
            'path'                  => KST_DIR_LIB . '/KST/Widget/NavPost.php',
            'is_theme_only'         => TRUE
        ),
    'widget_nav_posts' => array(
            'friendly_name'         => 'Widget: KST: Older/Newer buttons',
            'desc'                  => 'Page to page navigation in the sidebar',
            'path'                  => KST_DIR_LIB . '/KST/Widget/NavPosts.php',
            'is_theme_only'         => TRUE
        ),
    'widget_jit_sidebar' => array(
            'friendly_name'         => 'Widget: KST: JIT Sidebar Start',
            'desc'                  => 'Allows floating some content in sidebar when the page is scrolled (to keep that content in view)',
            'path'                  => KST_DIR_LIB . '/KST/Widget/JitSidebar.php',
            'is_theme_only'         => TRUE
        ),
    'lightbox' => array(
            'friendly_name'         => 'Lightbox (Fancybox)',
            'desc'                  => 'Lightboxes images automatically when directly linked or in galleries',
            'path'                  => KST_DIR_LIB . '/functions/jquery/lightbox.php',
            'is_theme_only'         => TRUE
        ),
    'mp3player' => array(
            'friendly_name'         => 'mp3 player',
            'desc'                  => 'Automatically creates a media player when an mp3 is directly linked or by shortcode',
            'path'                  => KST_DIR_LIB . '/functions/mp3_player.php',
        ),
    'jit_message' => array(
            'friendly_name'         => 'JIT Message',
            'desc'                  => 'Slides a call-to-action box out',
            'path'                  => KST_DIR_LIB . '/KST/Appliance/JitMessage.php',
            'class_name'            => 'KST_Appliance_JitMessage',
            'is_theme_only'         => TRUE
        ),
    'slideshow_cycle' => array(
            'friendly_name'         => 'Slideshow: Malsup Cycle',
            'desc'                  => 'Creates slideshows with shortcodes',
            'path'                  => KST_DIR_LIB . '/functions/jquery/cycle.php',
            'is_theme_only'         => TRUE
        ),
    'slideshow_tools_scrollable' => array(
            'friendly_name'         => 'Slideshow: Tools Scrollable',
            'desc'                  => 'Creates slideshows with shortcodes',
            'path'                  => KST_DIR_LIB . '/functions/jquery/scrollables.php',
            'is_theme_only'         => TRUE
        ),
    'asides' => array(
            'friendly_name'         => 'Asides and custom formatting',
            'desc'                  => 'Methods for "unobtrusively" and dynamically adding "asides" and custom loop/single markup',
            'path'                  => KST_DIR_LIB . '/KST/Appliance/Asides.php',
            'class_name'            => 'KST_Appliance_Asides',
            'is_theme_only'         => TRUE
        ),
);


/**
 * Every kitchen needs the basic settings
 * The KST core acts like it's own kitchen
 *
 * NOTE: core options are loaded in KST_Kitchen::addLoadedApplianceCoreOptions()
 *       but this could change in the future if there are other reasons to create
 *       core options based on the current install
 *
 * @since       0.1
*/
$kst_core_settings = array(
            /* REQUIRED */
            'friendly_name'       => 'Kitchen Sink HTML5 Base',                 // Required; friendly name used by all widgets, libraries, and classes; can be different than the registered theme name
            'prefix'              => 'kst_core',                       // Required; Prefix for namespacing libraries, classes, widgets
            'developer'           => 'zoe somebody',                           // Required; friendly name of current developer; only used for admin display;
            'developer_url'       => 'http://beingzoe.com/',            // Required; full URI to developer website;
        );


/**#@+
 * Initialize KST core
 *
 * @since       0.1
 * @uses        KST_Kitchen::registerAppliances()
 * @uses        KST_Kitchen::load()
 * @uses        KST_Appliance_Options::add()
*/

// Instantiate the core as it's own 'kitchen'
$kst_core = new KST_Kitchen_Core($kst_core_settings);
/**#@-*/


/**#@+
 * Define the options for the core
 * KST core settings editable from WP admin irrespective of theme
 *
 * @since       0.1
 * @uses        KST_Kitchen
 * @uses        KST_Kitchen::registerAppliances
 * @uses        KST_Appliance_Options
 * @uses        KST_Kitchen::load()
*/

$kst_core_options = array(
    'parent_slug'           => 'core', // required;

    'menu_title'            => 'About KST',
    //'menu_slug'             => 'theme_options', // Optional unless creating a new top level section; Defaults to underscored/lowercased menu_slug
    'page_title'            => 'Kitchen Sink HTML5 Base Core Settings', // Optional; will use "$this->friendly_name menu_title" by default
    'capability'            => 'manage_options', // Optional; Defaults to 'manage_options' for options pages and 'edit_posts' (contributor and up) for other
    'view_page_callback'    => "auto", //auto OR layout_options_output OR 'view_page_callback'    => "{$current_theme_directory}/_layout_options_form.php",
    'options'               => array(
                        'core_main' => array(
                                        "name"      => 'About Kitchen Sink HTML5 Base Settings',
                                        "desc"      => "
                                                        <p><em>Your current theme or a plugin you are using relies on Kitchen Sink HTML5 Base (KST) to operate.</em></p>
                                                        <p>KST settings generally only modify KST itself.<br />KST based themes and plugins will have their own options.<br />Only edit these settings if you experience conflicts or unwanted behavior with non-KST WordPress plugins.</p>
                                                    ",
                                        "type"      => "section",
                                        "is_shut"   => TRUE
                                        ),

                        'core_admin_interface' => array(
                                        "name"      => 'Admin Interface',
                                        "desc"      => "
                                                        <p><em>Settings affecting your WordPress administrative interface.</em></p>
                                                    ",
                                        "type"      => "section",
                                        "is_shut"   => TRUE
                                        ),

                        'core_admin_interface_menus' => array(
                                        "name"      => 'Admin Menus',
                                        "desc"      => "",
                                        "type"      => "subsection"
                                        ),

                        'do_allow_kst_to_move_admin_menus' => array(
                                        "name"    => 'Allow Moving Admin Menus',
                                        "desc"    => "Defaults to TRUE.<br /><br />Allows KST themes and plugins to reorganize admin menus.<br />Only affects menus created through KST.",
                                        "default"     => TRUE,
                                        "type"    => "checkbox",
                                        )
                        )
        );

// Register bundled appliances
$kst_core->registerAppliances($kst_bundled_appliances);
// Load the options appliance - actual core options currently loaded in KST_Kitchen::addLoadedApplianceCoreOptions()
$kst_core->load('options');
/**#@-*/


/**#@+
 * Add WP hooks for core functionality
 *
 * @since       0.1
 * @uses        add_action() WP Function
*/
add_action('activated_plugin', array('KST', 'loadAsFirstPlugin'));
add_action('plugins_loaded', array('KST', 'pluginsAreLoaded')); // Set whether the plugins are loaded so we can treat plugins and the active theme differently
/**#@-*/

// Now we wait for a plugin or theme to initialize...
