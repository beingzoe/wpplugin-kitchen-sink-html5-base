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
    protected static $theme_content_width = 500; // Same as WP default if it didn't exist
    protected static $theme_excerpt_length = 100; // Same as WP default it if didn't exist
    protected static $theme_seo_title_sep = '&laquo;'; // Defined in settings_core
    /**#@-*/

    /**
     * @since       0.1
    */


    /**
     * Theme/Plugin instance constructor
     *
     * @since       0.1
     * @access      protected
     * @param       required array $settings
     * @param       optional string $preset // As a convenience you can just pass a preset when you make your kitchen
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
            'theme_seo_title_sep'       => '&laquo;',
        );
        $settings = array_merge( $default_settings, $settings );


        /**#@+
         * KST common "kitchen" theme/plugin settings
         *
         * @since 0.1
        */
        $this->type_of_kitchen = 'theme';
        self::setThemeContentWidth( $settings['content_width'] );
        self::setThemeExcerptLength( $settings['theme_excerpt_length'] );

        // Set the meta title segment separator if the theme developer included it
        if ( isset($settings['theme_seo_title_sep']) )
            self::setThemeSeoTitleSep( $settings['theme_seo_title_sep'] );

        parent::__construct($settings, $preset); // Now set the common stuff (has to be last because of preset)

    }

    /**
     * THEME ONLY PROTECTED MEMBER VARIABLES
     * Acessors and Mutators
    */

    /**
     * Get static theme_content_width
     *
     * @since       0.1
     * @access      public
    */
    public static function getThemeContentWidth() {
        return self::$theme_content_width;
    }

    /**
     * Set static theme_content_width
     *
     * @since       0.1
     * @access      protected
    */
    public function setThemeContentWidth($value) {
        self::$theme_content_width = $value;
    }


    /**
     * Get this theme_excerpt_length
     *
     * @since       0.1
     * @access      public
    */
    public static function getThemeExcerptLength() {
        return self::$theme_excerpt_length;
    }


    /**
     * Set this theme_excerpt_length
     *
     * @since       0.1
     * @access      protected
    */
    public function setThemeExcerptLength($value) {
        self::$theme_excerpt_length = $value;
    }


    /**
     * Get this theme_seo_title_sep
     *
     * @since       0.1
     * @access      public
    */
    public static function getThemeSeoTitleSep() {
        return self::$theme_seo_title_sep;
    }


    /**
     * Set this theme_seo_title_sep
     *
     * @since       0.1
     * @access      protected
    */
    public function setThemeSeoTitleSep($value) {
        self::$theme_seo_title_sep = $value;
    }

}

