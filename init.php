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
    'options' => array(
            'path'                  => KST_DIR_LIB . '/KST/Appliance/Options.php',
            'class_name'            => 'KST_Appliance_Options'
            //,'require_args'          => TRUE
        ),
    'helpold' => array(
            'path'                  => KST_DIR_LIB . '/functions/theme_help.php',
            'class_name'            => FALSE
        ),
    'help' => array(
            'path'                  => KST_DIR_LIB . '/KST/Appliance/Help.php',
            'class_name'            => 'KST_Appliance_Help'
        ),
    'wp_sensible_defaults' => array(
            'path'                  => KST_DIR_LIB . '/functions/wp_sensible_defaults.php',
            'class_name'            => FALSE
        ),
    'wp_sensible_defaults_admin'    => array(
                'path'              => KST_DIR_LIB . '/functions/wp_sensible_defaults_admin.php',
                'class_name'        => FALSE
        ),
    'seo' => array(
            'path'                  => KST_DIR_LIB . '/KST/Appliance/Seo.php',
            'class_name'            => 'KST_Appliance_Seo'
        ),
    'wordpress' => array(
            'path'                  => KST_DIR_LIB . '/KST/Wordpress.php',
            'class_name'            => 'KST_Wordpress'
        ),
    'metabox' => array(
            'path'                  => KST_DIR_LIB . '/KST/Appliance/MetaBox.php',
            'class_name'            => 'KST_Appliance_MetaBox'
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
    'asides' => array(
            'path'                  => KST_DIR_LIB . '/KST/Appliance/Asides.php',
            'class_name'            => 'KST_Appliance_Asides'
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


/**#@+
 * Initialize KST core
 *
 * @since       0.1
 * @uses        KST_Kitchen::registerAppliances()
 * @uses        KST_Kitchen::load()
 * @uses        KST_Appliance_Options::addGroup()
*/

// Instantiate the core as it's own 'kitchen'
$kst_core = new KST_Kitchen_Plugin($kst_core_settings);

// Register bundled appliances
$kst_core->registerAppliances($kst_bundled_appliances);
/**#@-*/


/**#@+
 * Define the options for the core
 * KST core settings editable from WP admin irrespective of theme
 *
 * @since       0.1
 * @uses        KST_Kitchen
 * @uses        KST_Appliance_Options
 * @uses        KST_Kitchen::load()
 * @uses        KST_Kitchen::setDisabledAppliances()
 * @uses        KST_Appliance_Options::addGroup()
 * @uses        KST_Appliance_Options::get()

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

                        'do_allow_kst_to_move_admin_menus' => array(
                                        "name"    => 'Allow Moving Admin Menus',
                                        "desc"    => "Defaults to TRUE.<br /><br />Allows KST themes and plugins to reorganize admin menus.<br />Only affects menus created through KST.",
                                        "default"     => TRUE,
                                        "type"    => "checkbox",
                                        ),

                        'core_disable_these_appliances' => array(
                                        "name"      => 'Disable appliances',
                                        "desc"      => "Experimental feature. Use at your own risk!<br />Allow blog owner to shut off certain appliances (i.e they want a separate plugin to handle that functionality).<br />Only class objects can be disabled at this time.<br />",
                                        "type"      => "section"
                                        ),

                        'disable_these_appliances' => array(
                                        "name"      => 'Disable appliances',
                                        "desc"      => "Comma separated list of appliance 'shortnames' to disable.",
                                        "type"      => "text",
                                        "default"   => FALSE
                                        )
                        )
        );


// Add the core options/about page
$kst_core->load('options');
$kst_core->options->addGroup($kst_core_options);
KST_Kitchen::setDisabledAppliances( $kst_core->options->get('disable_these_appliances') );
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
