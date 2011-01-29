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
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  Core
*/

/**#@+
 * Theme/Plugin settings common to any instance
 * @since 0.1
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
 * Registers the themes directory as the theme_root
 * for developing the bundled themes.
 *
 * Usage:
 * in wp-config.php
 * // use KST plugin themes directory as theme_root
 * define('KST_BUNDLED_THEME_DEV', true);
 *
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
require_once KST_DIR_LIB . '/KST/Kitchen/Plugin.php';
require_once KST_DIR_LIB . '/KST/Kitchen/Theme.php';

/**
 * Load remaining core class files
 *
 * @since       0.1
*/
require_once KST_DIR_LIB . '/KST/AdminPage.php';
require_once KST_DIR_LIB . '/KST/AdminPage/OptionsGroup.php';
require_once KST_DIR_LIB . '/KST/AdminPage/Help.php';

/**
 * KST core settings editable from WP admin irrespective of theme
 * Instantiates Options for use by themes, plugins, and internals
 * And starts a common Admin Menu theme authors are encouraged to use
 *
 * @since       0.1
*/
// Set the core appliances array for KST::$_appliances;
$kst_bundled_appliances = array(
    'wp_sensible_defaults' => array(
            'path'          => KST_DIR_LIB . '/functions/wp_sensible_defaults.php',
            'class_name'    => FALSE
        ),
    'wp_sensible_defaults_admin' => array(
                'path'          => KST_DIR_LIB . '/functions/wp_sensible_defaults_admin.php',
                'class_name'    => FALSE
        ),
    'helpold' => array(
            'path'          => KST_DIR_LIB . '/functions/theme_help.php',
            'class_name'    => FALSE
        ),
    'help' => array(
            'path'          => KST_DIR_LIB . '/functions/theme_help.php',
            'class_name'    => 'KST_AdminPage_Help'
        ),
    /*
    'seo' => array(
            'path'          => KST_DIR_LIB . '/functions/seo.php',
            'class_name'    => FALSE
        ),
    */
    'wordpress' => array(
            'path'          => KST_DIR_LIB . '/KST/Wordpress.php',
            'class_name'    => 'KST_Wordpress'
        ),
    /*
    'contact' => array(
            'path'          => KST_DIR_LIB . '/functions/contact.php',
            'class_name'    => FALSE
        ),
    */
    'widgetNavPost' => array(
            'path'          => KST_DIR_LIB . '/KST/Widget/NavPost.php',
            'class_name'    => FALSE
        ),
    'widgetNavPosts' => array(
            'path'          => KST_DIR_LIB . '/KST/Widget/NavPosts.php',
            'class_name'    => FALSE
        ),
    'widgetJitSidebar' => array(
            'path'          => KST_DIR_LIB . '/KST/Widget/JitSidebar.php',
            'class_name'    => FALSE
        ),
    'jqueryLightbox' => array(
            'path'          => KST_DIR_LIB . '/functions/jquery/lightbox.php',
            'class_name'    => FALSE
        ),
    'mp3Player' => array(
            'path'          => KST_DIR_LIB . '/functions/mp3_player.php',
            'class_name'    => FALSE
        ),
    'jitMessage' => array(
            'path'          => KST_DIR_LIB . '/functions/jquery/jit_message.php',
            'class_name'    => FALSE
        ),
    'jqueryCycle' => array(
            'path'          => KST_DIR_LIB . '/functions/jquery/cycle.php',
            'class_name'    => FALSE
        ),
    'jqueryToolsScrollable' => array(
            'path'          => KST_DIR_LIB . '/functions/jquery/scrollables.php',
            'class_name'    => FALSE
        ),
);

// Every kitchen needs the basic settings
$kst_core_settings = array(
            /* REQUIRED */
            'friendly_name'       => 'Kitchen Sink HTML5 Base',                 // Required; friendly name used by all widgets, libraries, and classes; can be different than the registered theme name
            'prefix'              => 'kst_core',                       // Required; Prefix for namespacing libraries, classes, widgets
            'developer'           => 'zoe somebody',                           // Required; friendly name of current developer; only used for admin display;
            'developer_url'       => 'http://beingzoe.com/',            // Required; full URI to developer website;
        );


// Define the options for the core
$kst_core_options = array (
    array(  "name"      => __('Kitchen Sink Base core settings'),
            "desc"      => __("
                            <p><em>There are no core settings at this time.<br />But we sure are glad you are using Kitchen Sink HTML5 Base.</em></p>
                            <p>Various links to the documentation, git repos, and other stuff will also be here soon.</p>
                        "),
            "type"      => "section",
            "is_shut"   => FALSE )
    );

/**
 * Instantiate the core as it's own 'kitchen'
*/
$kst_core = new KST_Kitchen_Plugin($kst_core_settings);

/* Register bundled appliances */
$kst_core->registerAppliances($kst_bundled_appliances);

/* Add the core options/about page */
$kst_core->addOptionPage($kst_core_options, array('menu_title' => 'About KST', 'menu_slug' => 'core')) ;

/* Add WP hooks for core functionality */
add_action('activated_plugin', 'KST::loadAsFirstPlugin');
add_action('plugins_loaded', 'KST::pluginsAreLoaded'); // Set whether the plugins are loaded so we can treat plugins and the active theme differently

// Now we wait for a plugin or theme to initialize...
