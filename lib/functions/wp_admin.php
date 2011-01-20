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

/* REMOVE WP JUNK
 * remove_action, remove_theme_support, etc...
 *
 *
 * All Kitchen Sink "REMOVE WP JUNK" functions that can be "plugged":
 *      kst_admin_remove_meta_boxes();      Remove built-in WP custom field meta boxes from post/page not needed for theme
 */
if ( is_admin() ) { // remove admin specific junk
    add_action( 'admin_menu' , 'kst_admin_remove_meta_boxes' ); // Removes meta boxes from post/page not needed for theme
}

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
if ( !function_exists('kst_admin_favorite_actions') ) {
    function kst_admin_favorite_actions( $actions ) {
        //unset($actions['edit-comments.php']); // Example removing comments quick link
        $actions[THEME_HELP_URL] = array('Theme Help', 'edit_posts'); // Add
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
if ( !function_exists('kst_admin_remove_meta_boxes') ) {
    function kst_admin_remove_meta_boxes() {
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
if ( !function_exists('kst_admin_edit_tinymce') ) {
    function kst_admin_edit_tinymce( $initArray ) {
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
 * @global      $wp_meta_boxes
 * @uses        wp_add_dashboard_widget() WP function
 * @uses        kst_cb_admin_dashboard_theme_support() callback to echo content for dashboard widget
 * @link        http://webdesignfan.com/customizing-the-wordpress-admin-area/ by Pippin Williamson
 * @link        http://sixrevisions.com/wordpress/how-to-customize-the-wordpress-admin-area/
 */
if ( !function_exists('kst_admin_dashboard_customize') ) {
    function kst_admin_dashboard_customize() {
        global $wp_meta_boxes;

        /* Add Widgets */
        wp_add_dashboard_widget( THEME_ID . "_help", THEME_NAME . " Theme Help", 'kst_cb_admin_dashboard_theme_support');

        /* Remove Widgets */
        //unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']); //google blogsearch incomng
        unset($wp_meta_boxes['dashboard']['normal']['core']['blogplay_db_widget']); //sociable

        /* Put widgets at the top (only works if user has not already sorted their dashboard manually */
        //backup original dashboard array
        $original_dashboard_array = $wp_meta_boxes['dashboard']['normal']['core'];
        /* Theme Support */
        //move in array
        $dashboard_temp = array(THEME_ID . "_help" => $original_dashboard_array[THEME_ID . "_help"]); //backup our widget from the dashboard array
        unset($original_dashboard_array[THEME_ID . "_help"]); //delete the widget we just added to the dashboard array
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
    function kst_cb_admin_dashboard_theme_support() {
        echo "<p>Be sure to configure your theme SEO and options in <a href='" . THEME_OPTIONS_URL . "'>Appearance &gt; Theme Options</a><br />Learn more about using WordPress and your custom " . THEME_NAME . " theme in <a href='" . THEME_HELP_URL . "'>Appearance &gt; Theme Help</a></p>";
        echo "<p>Need more help?<br />Contact the developer, <a href='" . THEME_DEVELOPER_URL . "'>" . THEME_DEVELOPER . "</a>.";
    }
}

