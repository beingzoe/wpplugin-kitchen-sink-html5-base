<?php
/**
 * Parent class
 */
require_once KST_DIR_LIB . '/KST.php';

/**
 * Class for theme/plugin
 * 
 * @package     KitchenSinkHTML5Base
 * @subpackage  KitchenSinkWidgetClasses
 * @version     0.1 
 * @since       0.1
 * @author zoe somebody http://beingzoe.com/zui/
 */
class KST_Plugin extends KST {
    
    public $testme;
    //const THEME_ID  = 'ksd_0_1';
    /**
     * @since       0.1
     */
    public function __construct($settings) {
        parent::__construct($settings);
        
        /**#@+
         * KST default settings
         *
         * @since       0.1
         */
        $default_settings = array(
            'theme_name'                => 'Kitchen Sink',
            'prefix'                 => 'kst_0_2',
            'theme_developer'           => 'zoe somebody',
            'theme_developer_url'       => 'http://beingzoe.com/',
            'content_width'             => 500,
            'theme_excerpt_length'      => 100,
            'theme_seo_title_sep'       => '&laquo;',           // Defined in Settings core
        );
        $settings = array_merge( $default_settings, $settings );
        
        
        /**
         * Setup and define a theme OR plugin(s)
         */
        if ( KST::are_plugins_loaded() ) { // We are defining a theme
             
            /**#@+
             * KST theme/plugin settings
             * 
             * @since 0.1
             */
            define( 'THEME_NAME',           $settings['theme_name'] );
            define( 'THEME_ID',             $settings['prefix'] );
            define( 'THEME_DEVELOPER',      $settings['theme_developer'] );
            define( 'THEME_DEVELOPER_URL',  $settings['theme_developer_url'] );
            define( 'THEME_HELP_URL',       "themes.php?page=" . THEME_ID . "_help" );  // path to theme help file
            define( 'KST_SEO_TITLE_SEPARATOR_DEFAULT',           $settings['theme_seo_title_sep'] ); // Really only for SEO "theme_meta_data" I think so not sure it needs to be here
            define( 'CONTENT_WIDTH',        $settings['content_width'] );               // We use this to minimize global scope variables and for sensible defaults
            /**#@-*/
            
            /**
             * Used to set default WP excerpt length in kst_excerpt_length() filter
             * 
             * @see     kst_excerpt_length()
             * @since       0.1
             */
            define( 'THEME_EXCERPT_LENGTH',   $settings['theme_excerpt_length'] );
            
            $this->testme = "Hell Yeah! THEME BABY!";
            
        } else { // We are defining a plugin
            
            $this->testme = "OH Yeah! PLUGIN DADDY!";
            
        }
        
        
    }
    
}

