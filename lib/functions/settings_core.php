<?php
/**
 * Core settings and initialization
 *
 * KST behaves like a any other kitchen (plugin).
 * The only difference being that it initializes various
 * shared static members for the other kitchens to use and communicate
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  Core
 * @version     0.1
 * @since       0.1
 */

// Set the core appliances array for KST::$_appliances;
function kst_bundled_appliances() {
    $kst_bundled_appliances = array(
        'wp_sensible_defaults' => array(
                'path'          => KST_DIR_LIB . '/functions/wp_sensible_defaults.php',
                'class_name'    => FALSE
            ),
        'wp_sensible_defaults_admin' => array(
                'path'          => KST_DIR_LIB . '/functions/wp_sensible_defaults_admin.php',
                'class_name'    => FALSE
            ),
        'help' => array(
                'path'          => KST_DIR_LIB . '/functions/theme_help.php',
                'class_name'    => FALSE
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
    return $kst_bundled_appliances;
}

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

