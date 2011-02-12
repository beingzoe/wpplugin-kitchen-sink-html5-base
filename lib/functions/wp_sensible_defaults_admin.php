<?php
/**
 * KST ADMIN ONLY SENSIBLE DEFAULTS FUNCTIONS
 *
 * Functions only required if we are in the WP admin
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  Base
 * @version     0.1
 * @since       0.1
*/

// No admin - no need - skip it - speed things up
if ( is_admin() ) {


/**
 * Functions common to 'wp_sensible_defaults' and 'wp_sensible_defauls_admin'
 *
 * @since       0.1
*/
require_once KST_DIR_LIB . '/functions/wp_sensible_defaults_common.php';


/**#@+
 * ADD and REMOVE WP JUNK
 * remove_action(), remove_theme_support()
 * add_filter()
 * add_action()
 * add_theme_support()
 *
 * @since       0.1
*/
// remove admin specific junk
add_action( 'admin_menu' , 'kstRemoveAdminMetaBoxes' ); // Removes meta boxes from post/page not needed for theme
add_action( 'wp_dashboard_setup', 'kstEditAdminDashboardWidgets' );
add_filter( 'favorite_actions', 'kstEditAdminFavoriteActionsQuickLinks' );
add_filter( 'tiny_mce_before_init', 'kstEditAdminTinymce' );


/**
 * INITIALIZE HTML
 * style.css
 * modernizr.js (HTML5 Boilerplate)
 * dd_belatedpng.js (HTML5 Boilerplate)
 * jquery.js (HTML5 Boilerplate)
 * plugins.js (HTML5 Boilerplate)
 * script.js (HTML5 Boilerplate)
 *
 * @since       0.1
*/
add_action( 'admin_print_styles', 'kstLoadAdminLoginCss' ); // Load custom admin stylesheet "your_theme/style_admin.css" (MUST EXIST IN YOUR THEME!!!)
add_action( 'admin_print_scripts', 'kstLoadAdminJs' ); // Load custom admin stylesheet "your_theme/assets/javascripts/script_admin.css" (MUST EXIST IN YOUR THEME!!!)

/**
 * FUNCTIONS
 * All functions are "pluggable" by themes unless noted otherwise
 *
 * @see     wp_sensible_defaults.php
 * @see     wp_sensible_defaults_common.php for shared functions
*/

/**
 * ADMIN: Add quick links (favorites) to WP quick link dropdown
 *
 * Add new links to the quick links $array
 * or unset existing links
 *
 * @since       0.1
 * @param       array $actions
 * @return      array
 * @link        http://webdesignfan.com/customizing-the-wordpress-admin-area/ by Pippin Williamson
 * @link        http://sixrevisions.com/wordpress/how-to-customize-the-wordpress-admin-area/
*/
if ( !function_exists('kstEditAdminFavoriteActionsQuickLinks') ) {
    function kstEditAdminFavoriteActionsQuickLinks( $actions ) {
        //unset($actions['edit-comments.php']); // Example removing comments quick link
        $actions[KST::getHelpIndex()] = array('Theme Help', 'edit_posts'); // Add

        return $actions;
    }
}


/**
 * ADMIN: Remove meta boxes
 *
 * Unclutter
 * Remove meta boxes from post/page not used/needed by this WP theme
 *
 * @since       0.1
 * @uses        remove_meta_box() WP function
 * @link        http://webdesignfan.com/customizing-the-wordpress-admin-area/ by Pippin Williamson
 * @link        http://sixrevisions.com/wordpress/how-to-customize-the-wordpress-admin-area/
*/
if ( !function_exists('kstRemoveAdminMetaBoxes') ) {
    function kstRemoveAdminMetaBoxes() {
        remove_meta_box( 'trackbacksdiv' , 'post' , 'normal' ); // Remove Send Legacy Trackbacks
        remove_meta_box( 'trackbacksdiv' , 'page' , 'normal' ); // Remove Send Legacy Trackbacks
    }
}

/**
 * ADMIN: TinyMCE
 *
 * TinyMCE "Format" dropdown - reorder/add block level elements
 * Disable advanced elements
 *
 * @since       0.1
 * @return      array
*/
if ( !function_exists('kstEditAdminTinymce') ) {
    function kstEditAdminTinymce( $initArray ) {
        //@see http://wiki.moxiecode.com/index.php/TinyMCE:Control_reference
        $initArray['theme_advanced_blockformats']   = 'p,h1,h2,h3,h4,h5,h6,address,pre,code,div';
        $initArray['theme_advanced_disable']        = 'forecolor';

        return $initArray;
    }
}

/**
 * ADMIN: Dashboard widgets
 *
 * Add and remove WP admin dashboard widgets using $wp_meta_boxes WP global array
 *
 * @since       0.1
 * @global      array $wp_meta_boxes
 * @uses        wp_add_dashboard_widget() WP function
 * @uses        kstAddAdminDashboardForThemeSupport() callback to echo content for dashboard widget
 * @link        http://webdesignfan.com/customizing-the-wordpress-admin-area/ by Pippin Williamson
 * @link        http://sixrevisions.com/wordpress/how-to-customize-the-wordpress-admin-area/
 * @todo
*/
if ( !function_exists('kstEditAdminDashboardWidgets') ) {
    function kstEditAdminDashboardWidgets() {
        global $wp_meta_boxes;

        /* Add Widgets */
        wp_add_dashboard_widget( "theme_help", "Theme Help", 'kstAddAdminDashboardForThemeSupport');

        /* Remove Widgets */
        //unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']); //google blogsearch incomng
        unset($wp_meta_boxes['dashboard']['normal']['core']['blogplay_db_widget']); //sociable

        /* Put widgets at the top (only works if user has not already sorted their dashboard manually */
        //backup original dashboard array
        $original_dashboard_array = $wp_meta_boxes['dashboard']['normal']['core'];
        /* Theme Support */
        //move in array
        $dashboard_temp = array("theme_help" => $original_dashboard_array["theme_help"]); //backup our widget from the dashboard array
        unset($original_dashboard_array["theme_help"]); //delete the widget we just added to the dashboard array
        $sorted_dashboard = array_merge($dashboard_temp, $original_dashboard_array); ///merge our widget back in at the top
        //replace dashboard array with sorted array
        $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;

    }
}

/**
 * ADMIN: Dashboard Custom Theme Support Widget
 *
 * Adds Theme Help and Support Dashboard widget with links to help options and help pages
 * Echoes output to be displayed in our dashboard widget
 *
 * @since       0.1
*/
if ( !function_exists('kst_admin_dashboard_theme_support') ) {
    function kstAddAdminDashboardForThemeSupport() {
        echo "<p>Be sure to configure your various theme and plugin options. Learn more about using WordPress and get the most out of your theme/plugins in <a href='" . KST::getHelpIndex() . "'>Theme Help</a></p>";
        echo "<p>Need more help?<br /><a href='" . KST::getDeveloperIndex() . "'>Contact the developers</a>.";
    }
}


/**
 * kstLoadAdminJs
 * Load custom scripts_admin.js for admin and wp-login (they share a stylesheet)
 *
 * NOT ADMIN ONLY because wp-login is not treated as part of admin
 *
 * Echoes output
 *
 * @since       0.1
 * @uses        get_stylesheet_directory_uri() WP function
 * @uses        add_editor_style() WP function
*/
if ( !function_exists('kstLoadAdminJs') ) {
    function kstLoadAdminJs() {
        wp_enqueue_script('kst_script_admin', get_stylesheet_directory_uri() . '/assets/javascripts/script_admin.js' , array( 'jquery' ) , '0.1', true); // "your_theme/assets/javascripts/plugins.js" (MUST EXIST IN YOUR THEME!!!)
    }
}


} // END if is_admin()
