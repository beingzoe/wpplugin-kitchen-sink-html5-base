<?php
/*
 * The primary purpose of the _kst_bootstrap_theme.php is to protect the theme from
 * the Kitchen Sink HTML5 Base plugin not being installed or deactivated.
 *
 * It does this by including the minimum requiste resources for any theme to run
 * regardless of whether the 'wp_sensible_defaults' appliance is being used or not
 *
 * Dummy kitchen/appliance classes are created to return false for all requests
 * to what would be the theme and plugin. In this way the appearance of the
 * site is maintained and most errors are avoided from the plugin not being present.
 *
 * A message is displayed letting the site/blog owner know what is going on if
 * Kitchen Sink HTML5 Base is uninstalled or deactivated. Future versions of the
 * bootstrapper will include functionality to allow the site/blog owner to
 * install or activate the plugin automatically as soon as the theme is activated
 * and the plugin is not present.
 *
 *
 * A secondary benefit of this file is helping passing theme review.
 *
 * When the theme-checker plugin is run it looks for certain things that are
 * required or recommended by theme review. In this case the boostrapper has
 * all of those things in the event that the theme is relying on those things
 * existing from the KST plugin and it is not present on the install. Thus
 * literally fulfilling the requirments/recommendations regardles of how the
 * theme is utilizing KST.
 *
 * This is not an attempt to trick theme reviewers but is simply a practical
 * way of both dealing with the missing plugin issue and simultaneously
 * not making it look like a theme is instantly invalid via the theme-check plugin
 * just because the developer is using Kitchen Sink HTML5 Base.
 *
 *
 * The functionality of this file only happens if KST is not present on the install.
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.1
 * @since       0.1
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink/
 * @todo        Figure out automatic install of plugin on theme activation
 */
if (!class_exists('KST_Kitchen_Theme')) {

    if ( !is_admin() ) {

        // LOAD CSS for theme (like parent were a child ;)
        wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/style.css', false, '0.1', 'all' ); // WP default stylesheet "your_theme/style.css" (MUST EXIST IN YOUR THEME!!!)

        // Theme-wide Plugins and Application JS (HTML5 BOILERPLATE)
        wp_enqueue_script('plugins', get_stylesheet_directory_uri() . '/assets/javascripts/plugins.js' , array( 'jquery' ) , '0.1', true); // "your_theme/assets/javascripts/plugins.js" (MUST EXIST IN YOUR THEME!!!)
        wp_enqueue_script('application', get_stylesheet_directory_uri() . '/assets/javascripts/script.js' , array( 'jquery' ) , '0.1', true); // "your_theme/assets/javascripts/script.js" (MUST EXIST IN YOUR THEME!!!)
    }

    wp_enqueue_script( 'comment-reply' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'post-thumbnails' );
    add_editor_style();
    $content_width = 500; // required; For theme design compliance and WP best practice

    echo "<div style='width: 100%; margin: -18px -18px 0 -18px !important; padding: 27px 36px 9px; border-bottom: 1px solid #666; background: #ccc none; color: #444;'>Your theme requires the Kitchen Sink HTML5 Base plugin to work.<br />If you are upgrading WordPress disregard this message as it will disappear when your upgrade is complete.<br />Otherwise please install and activate Kitchen Sink HTML5 Base.</div>";
    /**
     * Fake fallback class to protect theme from missing plugin
     *
     * @since       0.1
    */
    class KST_Kitchen_Theme {
        private $data = array();
        public function exists() {
            return false;
        }
        public function __set($name, $value) {
            if ( !method_exists($name,'exists') )
                $this->{$name} = new KST_DoesNotExist;
            return $this->{$name};
        }
        public function __get($name) {
            if ( !method_exists($name,'exists') )
                $this->{$name} = new KST_DoesNotExist;
            return $this->{$name};
        }
        public function __call($name, $arguments) {
            return false;
        }
        public static function __callStatic($name, $arguments) {
             return false;
        }
    }

    /**
     * Dummy class to handle overloading non-existent KST appliance objects
     *
     * @since       0.1
    */
    class KST_DoesNotExist {
        public function exists() {
            return false;
        }
        public function __set($name, $value) {
            return false;
        }
        public function __get($name) {
            return false;
        }
        public function __call($name, $arguments) {
            return false;
        }
    }
}
