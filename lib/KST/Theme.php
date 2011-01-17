<?php
/**
 * Parent class
 */
require_once KST_DIR_LIB . '/KST.php';

/**
 * Class for managing plugin through KST
 * 
 * @package     KitchenSinkHTML5Base
 * @subpackage  KitchenSinkWidgetClasses
 * @version     0.1 
 * @since       0.1
 * @author zoe somebody http://beingzoe.com/zui/
 */
class KST_Theme extends KST {
    
   public  $testme;
    
    /**#@+
     * @since       0.1
     * @access      protected
     */
    protected $friendly_name;
    protected $prefix;
    protected $developer;
    protected $developer_url;
    protected $theme_content_width;
    protected $theme_excerpt_length;
    protected $theme_seo_title_sep;
    /**#@-*/
    
    /**
     * @since       0.1
     */
    public function __construct($settings) {
        //parent::__construct($settings);
        
        /**#@+
         * KST default settings
         *
         * @since       0.1
         */
        $default_settings = array(
            'friendly_name'             => 'Kitchen Sink',
            'prefix'                    => 'kst_0_2',
            'developer'                 => 'zoe somebody',
            'developer_url'             => 'http://beingzoe.com/',
            'content_width'             => 500,
            'theme_excerpt_length'      => 100,
            'theme_seo_title_sep'       => '&laquo;',           // Defined in Settings core
        );
        $settings = array_merge( $default_settings, $settings );
        
        /**#@+
         * KST theme/plugin settings
         * 
         * @since 0.1
         */
        $this->set_friendly_name( $settings['friendly_name'] );
        $this->set_prefix( $settings['prefix'] );
        $this->set_developer( $settings['developer'] );
        $this->set_developer_url( $settings['developer_url'] );
        $this->set_theme_content_width( $settings['content_width'] );
        $this->set_theme_excerpt_length( $settings['theme_excerpt_length'] );
        $this->set_theme_seo_title_sep( $settings['theme_seo_title_sep'] );
        
        /* These need removed - SEARCH AND REPLACE AFTER Options and Help have been updated*/
        define( 'THEME_NAME',           $settings['friendly_name'] );
        define( 'THEME_ID',             $settings['prefix'] );
        define( 'THEME_DEVELOPER',      $settings['developer'] );
        define( 'THEME_DEVELOPER_URL',  $settings['developer_url'] );
        define( 'THEME_HELP_URL',       "themes.php?page=" . THEME_ID . "_help" );  // path to theme help file
        define( 'KST_SEO_TITLE_SEPARATOR_DEFAULT',           $settings['theme_seo_title_sep'] ); // Really only for SEO "theme_meta_data" I think so not sure it needs to be here
        define( 'CONTENT_WIDTH',        $settings['content_width'] );               // We use this to minimize global scope variables and for sensible defaults
        /**#@-*/
        
        /**
         * Figure out how to access the accessor functions of this instance 
         * from the other internal functions files and classes 
         * and then get rid of all this constant garbage.
         * Used to set default WP excerpt length in kst_excerpt_length() filter
         * 
         * @see     kst_excerpt_length()
         * @since       0.1
         */
        define( 'THEME_EXCERPT_LENGTH',   $settings['theme_excerpt_length'] );
        
        $this->testme = "Hell Yeah! THEME BABY!";
        
    }
    
    /**
     * THEME ONLY PROTECTED MEMBER VARIABLES
     * Acessors and Mutators
     */
    
    /**
     * Get this theme_content_width
     * 
     * @since       0.1
     * @access      public
     */
    public function get_theme_content_width() {
        return $this->theme_content_width;
    }
    
    /**
     * Set this theme_content_width
     * 
     * @since       0.1
     * @access      protected
     */
    protected function set_theme_content_width($value) {
        $this->theme_content_width = $value;
    }
    
    /**
     * Get this theme_excerpt_length
     * 
     * @since       0.1
     * @access      public
     */
    public function get_theme_excerpt_length() {
        return $this->theme_excerpt_length;
    }
    
    /**
     * Set this theme_excerpt_length
     * 
     * @since       0.1
     * @access      protected
     */
    protected function set_theme_excerpt_length($value) {
        $this->theme_excerpt_length = $value;
    }
    
    /**
     * Get this theme_seo_title_sep
     * 
     * @since       0.1
     * @access      public
     */
    public function get_theme_seo_title_sep() {
        return $this->theme_seo_title_sep;
    }
    
    /**
     * Set this theme_seo_title_sep
     * 
     * @since       0.1
     * @access      protected
     */
    protected function set_theme_seo_title_sep($value) {
        $this->theme_seo_title_sep = $value;
    }
    
}

