<?php 
/**
 * Theme Help
 * Creates menu item and includes <theme>/theme_help.php
 * 
 * Other built-in KST libraries and classes include their own help file entry 
 * theme_help_LIBRARYorCLASSname($part)
 *      $part = 
 *          toc (table of contents list entry)
 *          entry = (actual help entry in help)
 * 
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     WordPress
 * @subpackage  KitchenSinkThemeLibrary
 * @version     0.1
 * @since       0.2
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @todo        convert to class
 * @todo        make use our new KST_OPTIONS class
 */
 
if ( is_admin() ) 
    add_action('admin_menu', 'kst_theme_help_menu');

/** 
 * Add menu page for theme help
 * 
 * @since   0.2
 * @uses    THEME_ID
 * @uses    THEME_NAME
 * @uses    add_submenu_page() WP function
 * @uses    kst_theme_help_page()
 */
function kst_theme_help_menu() {
    /* add new menu under Appearance */
    add_submenu_page("themes.php", THEME_NAME . " Theme Help and Notes", "Theme Help", "publish_posts", THEME_ID . "_help", "kst_theme_help_page");
}

/** 
 * Build the page to display at our new menu item
 * 
 * @since   0.2
 * @uses    THEME_ID
 * @uses    THEME_NAME
 * @uses    TEMPLATEPATH
 * @uses    current_user_can() WP function
 * @uses    wp_die() WP function
 * @uses    screen_icon() WP function
 * @uses    theme_help.php
 */
function kst_theme_help_page() {
    
    if (!current_user_can('publish_posts'))  {
        wp_die( __('You do not have sufficient permissions to access this page.') );
    }
    
    /* OUTPUT the actual help page */
    echo "<div class='wrap'>"; //Standard WP Admin content class
    if ( function_exists('screen_icon') ) screen_icon();
    echo "<h2>". THEME_NAME . " theme help</h2>";
    
    $help_file = TEMPLATEPATH . '/theme_help.php';
    
    if ( file_exists( $help_file ) ) 
        include( $help_file );
    else 
        echo "<p>No special help required for this theme</p>";

    echo "</div>"; //.wrap
}
?>
