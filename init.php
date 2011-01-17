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
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     WordPress 
 * @subpackage  KitchenSinkPlugin
 */
 
/**
 * kst_theme_init
 * Define contstants used throughout KST
 *
 * @param $options array
 * @see THEME_NAME_CURRENT
 * @see THEME_ID
 * @see THEME_DEVELOPER
 * @see THEME_DEVELOPER_URL
 * @see THEME_HELP_URL path to theme help file
 * @see THEME_OPTIONS_URL
 * @see CONTENT_WIDTH
 * @see THEME_EXCERPT_LENGTH
 * @global $content_width WP width used to protect layout by limiting content width; WP best practice
 * @global $theme_excerpt_length Override default WP excerpt length; Used by kst_excerpt_length() filter
 */

 
/**
 * In order for the companion plugin concept to work we have to make sure that
 * Base loads first, so on activation we move it the start of the list.
 *
 * This might could be done better
 * 
 * @see     http://wordpress.org/support/topic/how-to-change-plugins-load-order
 */
add_action("activated_plugin", "this_plugin_first");
function this_plugin_first() {
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

 
/**#@+
 * theme/plugin settings common to any instance
 * @since 0.1
 */
define( 'KST_DIR',              dirname(__FILE__) );                        // Absolute path to KST
define( 'KST_DIR_LIB',          KST_DIR . '/lib' );                  
define( 'KST_DIR_VENDOR',       KST_DIR . '/vendor' );
define( 'KST_DIR_TEMPLATES',    KST_DIR . '/templates' );
define( 'KST_DIR_ASSETS',       KST_DIR . '/assets' );
define( 'WP_URI_SITE',          get_site_url() );                           // Current WP site uri
define( 'KST_URI',              WP_PLUGIN_URL . '/' .  basename(KST_DIR) ); // Current uri to KST
define( 'KST_URI_LIB',          KST_URI . '/lib' );
define( 'KST_URI_VENDOR',       KST_URI . '/vendor' );
define( 'KST_URI_TEMPLATES',    KST_URI . '/templates' );
define( 'KST_URI_ASSETS',       KST_URI . '/assets' );
define( 'THEME_NAME_CURRENT',   get_current_theme() );
/**#@-*/



/* KST core settings editable from WP admin irrespective of theme
 * This creates ...
 */ 
require_once KST_DIR_LIB . '/functions/settings_core.php';

/**
 * Parent class for Kitchen Sink HTML5 Base
 * 
 * @since       0.1  
 */
require_once dirname(__FILE__) . '/lib/KST.php';
$kst = new KST();

/**
 * Set whether the plugins are loaded so we can treat plugins and the active theme differently
 */
add_action('plugins_loaded', array(KST, 'plugins_are_loaded'));

