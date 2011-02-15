<?php
/**
 * KST SENSIBLE DEFAULTS COMMON FUNCTIONS
 * For both admin and frontend
 *
 * Stuff you would normally do in your functions.php and shouldn't have to think about
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink/
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  Base
 * @version     0.1
 * @since       0.1
 * @todo        add_action( 'admin_print_styles', 'kstLoadAdminLoginCss' ) needs to use theirs if it exists or a default if not
*/

/**#@+
 * ADD and REMOVE WP JUNK
 * remove_action(), remove_theme_support()
 * add_filter()
 * add_action()
 * add_theme_support()
 *
 * @since       0.1
*/
add_theme_support( 'post-thumbnails' ); // Theme uses featured image post/page thumbnails
add_action( 'login_head', 'kstLoadAdminLoginCss' ); // Add style for wp-login (uses admin css: style_admin.css)
/**#@-*/


/**
 * kstLoadAdminLoginCss
 * Load custom stylesheet for admin and wp-login (they share a stylesheet)
 *
 * NOT ADMIN ONLY because wp-login is not treated as part of admin
 *
 * Echoes output
 *
 * @since       0.1
 * @uses        get_stylesheet_directory_uri() WP function
 * @uses        add_editor_style() WP function
*/
if ( !function_exists('kstLoadAdminLoginCss') ) {
    function kstLoadAdminLoginCss() {
        add_editor_style(); // Style the TinyMCE editor a little bit in editor-style.css
        echo '<link rel="stylesheet" href="'. get_stylesheet_directory_uri() . '/style_admin.css" type="text/css" />'."\n"; // Must exist in your theme!
    }
}
