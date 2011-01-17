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
function kst_init($settings) {
    global $content_width;

    $default_settings = array(
        'theme_name'                => 'Kitchen Sink',
        'theme_id'                  => 'kst_0_2',
        'theme_developer'           => 'zoe somebody',
        'theme_developer_url'       => 'http://beingzoe.com/',
        'content_width'             => 500,
        'theme_excerpt_length'      => 100,
        'theme_seo_title_sep'       => '&laquo;',
        
        'sensible_defaults'         => FALSE,                            // TRUE | theme | plugin | admin 
        'help'                      => FALSE,                            // TRUE 
        'seo'                       => FALSE,                            // TRUE
        'contact'                   => FALSE,                            // TRUE
        'widget_nav_posts'          => FALSE,                            // TRUE | post | posts (default TRUE = all)
        
        'widget_jit_sidebar'        => FALSE,                            // TRUE
        'jquery_lightbox'           => FALSE,                            // TRUE
        'jquery_cycle'              => FALSE,                            // TRUE
        'jquery_tools'              => FALSE,                            // TRUE | scrollable | tabs | tooltip | overlay | form
        'jquery_jit_message'        => FALSE                             // TRUE 
    );
    $settings = array_merge( $default_settings, $settings );

    /**#@+
     * KST theme/plugin settings
     * @since 0.1
     */
    /**
     * Current path of KST plugin
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
    /**
     * Set by developer for the theme
     */
    define( 'THEME_NAME',           $settings['theme_name'] );
    define( 'THEME_ID',             $settings['theme_id'] );
    define( 'THEME_DEVELOPER',      $settings['theme_developer'] );
    define( 'THEME_DEVELOPER_URL',  $settings['theme_developer_url'] );
    define( 'THEME_HELP_URL',       "themes.php?page=" . THEME_ID . "_help" );  // path to theme help file
    define( 'KST_SEO_TITLE_SEPARATOR_DEFAULT',           $settings['theme_name'] ); // Really only for SEO "theme_meta_data" I think so not sure it needs to be here
    define( 'CONTENT_WIDTH',        $settings['content_width'] );               // We use this to minimize global scope variables and for sensible defaults
    /**
     * Override default WP excerpt length; Used by kst_excerpt_length() filter
     * @see kst_excerpt_length()
     */
    define( 'THEME_EXCERPT_LENGTH',   $settings['theme_excerpt_length'] );
    /**#@-*/

    
    /**
     * Load/Initialize their requests
     */
    
    
}
        

