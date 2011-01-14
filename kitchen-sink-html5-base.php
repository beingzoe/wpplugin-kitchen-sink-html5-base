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
function kst_theme_init($settings) {
    global $content_width;

    $default_settings = array(
        'theme_name'                => 'Kitchen Sink',
        'theme_id'                  => 'kst_0_2',
        'theme_developer'           => 'zoe somebody',
        'theme_developer_url'       => 'http://beingzoe.com/',
        'content_width'             => 500,
        'theme_excerpt_length'      => 100
    );
    $settings = array_merge( $default_settings, $settings );

    /**
     * KST theme/plugin settings
     */
    define( 'KST_DIR',               dirname(__FILE__) ); // Current path of KST plugin; Since 0.4
    define( 'KST_DIR_CLASSES',       KST_DIR . '/_application/classes/' );  // Since 0.4
    define( 'KST_DIR_LIBRARIES',     KST_DIR . '/_application/libraries/' ); // Since 0.4
    define( 'KST_DIR_METABOXES',     KST_DIR . '/_application/meta_boxes/' ); // Since 0.4
    define( 'KST_DIR_WIDGETS',       KST_DIR . '/_application/widgets/' ); // Since 0.4
    define( 'KST_DIR_SHORTCODES',    KST_DIR . '/_application/shortcodes/' ); // Since 0.4
    define( 'KST_DIR_IMAGES',        KST_DIR . '/_assets/images/' ); // Since 0.4
    define( 'KST_DIR_STYLESHEETS',   KST_DIR . '/_assets/stylesheets/' ); // Since 0.4
    define( 'KST_DIR_JAVASCRIPTS',   KST_DIR . '/_assets/javascripts/' ); // Since 0.4
    define( 'KST_DIR_SWF',           KST_DIR . '/_assets/swf/' ); // Flash files; Since 0.4
    define( 'THEME_NAME_CURRENT',    get_current_theme() ); // 
    define( 'THEME_NAME',            $settings['theme_name'] ); // Set by developer for the theme
    define( 'THEME_ID',              $settings['theme_id'] );
    define( 'THEME_DEVELOPER',       $settings['theme_developer'] );
    define( 'THEME_DEVELOPER_URL',   $settings['theme_developer_url'] );
    define( 'THEME_HELP_URL',        "themes.php?page=" . THEME_ID . "_help" ); // path to theme help file
    define( 'CONTENT_WIDTH',         $settings['content_width'] ); // We use this to minimize global scope variables and for sensible defaults
    /**
     * Override default WP excerpt length; Used by kst_excerpt_length() filter
     * @see kst_excerpt_length()
     */
    define( 'THEME_EXCERPT_LENGTH',   $settings['theme_excerpt_length'] );
    
}
        

