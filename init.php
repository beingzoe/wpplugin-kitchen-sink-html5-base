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
 * CONSTANTS
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
 * Register plugin themes directory for developing the bundled themes
 *
 * Usage: (in wp-config.php)
 *  Use KST plugin themes directory as theme_root
 * define('KST_BUNDLED_THEME_DEV', true);
 *
 * @since       0.1
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
 * Array to register core appliances
 *
 * @since       0.1
 * @see         KST::$_appliances;
*/
$kst_bundled_appliances = array(
    'options' => array(
            'path'                  => KST_DIR_LIB . '/KST/Options.php',
            'class_name'            => 'KST_Options'
            //,'require_args'          => TRUE
        ),
    'wp_sensible_defaults' => array(
            'path'                  => KST_DIR_LIB . '/functions/wp_sensible_defaults.php',
            'class_name'            => FALSE
        ),
    'wp_sensible_defaults_admin'    => array(
                'path'              => KST_DIR_LIB . '/functions/wp_sensible_defaults_admin.php',
                'class_name'        => FALSE
        ),
    'helpold' => array(
            'path'                  => KST_DIR_LIB . '/functions/theme_help.php',
            'class_name'            => FALSE
        ),
    /*
    'help' => array(
            'path'                  => KST_DIR_LIB . '/KST/Help.php',
            'class_name'            => 'KST_AdminPage_Help'
        ),
    */
    /*
    'seo' => array(
            'path'                  => KST_DIR_LIB . '/functions/seo.php',
            'class_name'            => FALSE
        ),
    */
    'wordpress' => array(
            'path'                  => KST_DIR_LIB . '/KST/Wordpress.php',
            'class_name'            => 'KST_Wordpress'
        ),
    /*
    'contact' => array(
            'path'                  => KST_DIR_LIB . '/functions/contact.php',
            'class_name'            => FALSE
        ),
    */
    'widgetNavPost' => array(
            'path'                  => KST_DIR_LIB . '/KST/Widget/NavPost.php',
            'class_name'            => FALSE
        ),
    'widgetNavPosts' => array(
            'path'                  => KST_DIR_LIB . '/KST/Widget/NavPosts.php',
            'class_name'            => FALSE
        ),
    'widgetJitSidebar' => array(
            'path'                  => KST_DIR_LIB . '/KST/Widget/JitSidebar.php',
            'class_name'            => FALSE
        ),
    'jqueryLightbox' => array(
            'path'                  => KST_DIR_LIB . '/functions/jquery/lightbox.php',
            'class_name'            => FALSE
        ),
    'mp3Player' => array(
            'path'                  => KST_DIR_LIB . '/functions/mp3_player.php',
            'class_name'            => FALSE
        ),
    'jitMessage' => array(
            'path'                  => KST_DIR_LIB . '/functions/jquery/jit_message.php',
            'class_name'            => FALSE
        ),
    'jqueryCycle' => array(
            'path'                  => KST_DIR_LIB . '/functions/jquery/cycle.php',
            'class_name'            => FALSE
        ),
    'jqueryToolsScrollable'         => array(
            'path'                  => KST_DIR_LIB . '/functions/jquery/scrollables.php',
            'class_name'            => FALSE
        ),
);


/**
 * Every kitchen needs the basic settings
 * The KST core acts like it's own kitchen
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


/**
 * Define the options for the core
 * KST core settings editable from WP admin irrespective of theme
 *
 * @since       0.1
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
                                                    "is_shut"   => FALSE
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



                                    'doAllowKstToMoveAdminMenus' => array(
                                                    "name"    => 'Allow Moving Admin Menus',
                                                    "desc"    => "Defaults to TRUE.<br /><br />Allows KST themes and plugins to reorganize admin menus.<br />Only affects menus created through KST.",
                                                    "default"     => TRUE,
                                                    "type"    => "checkbox",
                                                    )

                                    )
        );


/**#@+
 * Initialize KST core
 *
 * @since       0.1
*/

// Instantiate the core as it's own 'kitchen'
$kst_core = new KST_Kitchen_Plugin($kst_core_settings);

// Register bundled appliances
$kst_core->registerAppliances($kst_bundled_appliances);

// Add the core options/about page
$kst_core->load('options');
$kst_core->options->addGroup($kst_core_options);

// Add WP hooks for core functionality
add_action('activated_plugin', 'KST::loadAsFirstPlugin');
add_action('plugins_loaded', 'KST::pluginsAreLoaded'); // Set whether the plugins are loaded so we can treat plugins and the active theme differently
/**#@-*/


// Now we wait for a plugin or theme to initialize...
