<?php
/**
 * Class for managing plugin through KST
 *
 * @package     KitchenSinkHTML5Base
 * @subpackage  Core
 * @version     0.1
 * @since       0.1
 * @author      zoe somebody
 * @link        http://beingzoe.com/
 * @author      Scragz
 * @link        http://scragz.com/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
*/

/**
 * Parent class
*/
require_once KST_DIR_LIB . '/KST/Kitchen.php';

class KST_Kitchen_Theme extends KST_Kitchen {

    /**#@+
     * @since       0.1
     * @access      protected
    */
    protected $theme_content_width;
    protected $theme_excerpt_length;
    protected $theme_seo_title_sep;
    /**#@-*/

    /**
     * @since       0.1
    */
    public function __construct($settings, $preset=null) {

        /**#@+
         * KST theme specific default settings
         *
         * @since       0.1
         */
        $default_settings = array(
            'content_width'             => 500,
            'theme_excerpt_length'      => 100,
            'theme_seo_title_sep'       => '&laquo;',           // Defined in Settings core
        );
        $settings = array_merge( $default_settings, $settings );


        /**#@+
         * KST common "kitchen" theme/plugin settings
         *
         * @since 0.1
        */
        $this->type_of_kitchen = 'theme';
        $this->set_theme_content_width( $settings['content_width'] );
        $this->set_theme_excerpt_length( $settings['theme_excerpt_length'] );
        $this->set_theme_seo_title_sep( $settings['theme_seo_title_sep'] );

        parent::__construct($settings, $preset); // Now set the common stuff (has to be last because of preset)


        /**
         * kst_theme_init
         * Define contstants used throughout KST
         *
         * @param $options array
         * @see THEME_HELP_URL path to theme help file
         * @see CONTENT_WIDTH
         * @see THEME_EXCERPT_LENGTH
         * @global $content_width WP width used to protect layout by limiting content width; WP best practice
         * @global $theme_excerpt_length Override default WP excerpt length; Used by kst_excerpt_length() filter
        */
        /* These need removed - SEARCH AND REPLACE AFTER Options and Help have been updated*/
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

