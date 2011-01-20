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
define( 'KST_DIR',              dirname(__FILE__) );                        // Absolute path to KST
define( 'KST_DIR_LIB',          KST_DIR . '/lib' );
define( 'KST_DIR_VENDOR',       KST_DIR . '/vendor' );
define( 'KST_DIR_TEMPLATES',    KST_DIR . '/templates' );
define( 'KST_DIR_ASSETS',       KST_DIR . '/assets' );
define( 'WP_URI_SITE',          get_site_url() );                           // Current WP site uri
define( 'KST_URI',              WP_PLUGIN_URL . '/' . basename(KST_DIR) ); // Current uri to KST
define( 'KST_URI_VENDOR',       KST_URI . '/vendor' );
define( 'KST_URI_TEMPLATES',    KST_URI . '/templates' );
define( 'KST_URI_ASSETS',       KST_URI . '/assets' );
define( 'THEME_NAME_CURRENT',   get_current_theme() );
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
require_once KST_DIR_LIB . '/functions/settings_core.php';

/**
 * Add hooks for core functionality
*/
add_action('activated_plugin', 'loadAsFirstPlugin');
add_action('plugins_loaded', 'KST::pluginsAreLoaded'); // Set whether the plugins are loaded so we can treat plugins and the active theme differently

// Now we wait for a plugin or theme to initialize...
