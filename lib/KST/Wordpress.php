<?php
/**
 * KST_Wordpress
 *
 * Replacement (and enhanced) replacements for certain WP functions and what not
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  Base
 * @version     0.1
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink/
 * @link        https://github.com/beingzoe/wpplugin-kitchen-sink-html5-base
*/

/**
 * Parent class
*/
require_once KST_DIR_LIB . '/KST/Kitchen/Theme.php';

class KST_Wordpress extends KST_Kitchen_Theme {

    /**
     * Wordpress replacement methods constructor
    */
    public function __construct() {
    }


    /**
     * WP replacement for
     * register_sidebar
     *
     * Defaults are HTML5 semantic awesome so you should never need to use the args
     * A little CSS goes a long way ;)
     *
     * @since       0.1
     * @access      public
     * @see         KST_Wordpress::dynamicSidebar
     * @uses        register_sidebar() WP function
     * @param       required string $name
     * @param       required string $description
     * @param       optional array  $args send any of the arguments for WP register_sidebar()
     *                  optional string $id defaults to $name
     *                  optional string $before_widget
     *                  optional string $after_widget
     *                  optional string $before_title
     *                  optional string $after_title
     * @return      string id that was registered
     * @todo        $context to allow for automagic conditonal output e.g. is_home; e.g. $context = all|home|blog|pages|page-template|etc
    */
    public function registerSidebar($name, $description, $args = NULL ) {

        $defaults = array(
            'name'          => $name,
            'id'            => $name,
            'description'   => $description,
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => '</aside>',
            'before_title'  => '<h2 class="widget_title">',
            'after_title'   => '</h2>'
            );
        $args = wp_parse_args( $args, $defaults );

        return register_sidebar( $args );

    }

    /**
     * WP replacement for
     * dynamic_sidebar
     *
     * Echoes output
     *
     * @since       0.1
     * @see         KST_Wordpress::registerSidebar
     * @access      public
     * @uses        register_sidebar() WP function
     * @param       required string $name
     * @todo        consider not having any default or a flag to turn that shit off
    */
    public function dynamicSidebar($name) {
        if ( ! dynamic_sidebar( $name ) ) {
            echo "<aside class='widget widget_search'>";
                get_search_form();
            echo "</aside>";
            echo "<aside class='widget widget_meta'>";
                echo "<h2 class='widget_title'>" . _e( 'Meta', 'twentyten' ) . "</h2>";
                echo "<ul>";
                        wp_register();
                        echo "<li>";
                        wp_loginout();
                        echo "</li>";
                        wp_meta();
                echo "</ul>";
            echo "</aside>";
        };
    }


    /**
     * WP enhanced replacement for
     * register_sidebars (multiple CONSECUTIVE NAMED with shared description)
     *
     * USAGE EXAMPLE:   Create in functions.php:                $my_kitchen->wordpress->registerSidebars(3, 'Footer Widget Areas');
     *                  Ouput ALL AT ONCE in your template:     $GLOBALS["my_theme"]->wordpress->dynamicSidebar('footer_widget_areas_3');
     *
     * USAGE EXAMPLE:   Create in functions.php:                $my_kitchen->wordpress->registerSidebars(3, 'Footer Widget Areas');
     *                  Ouput ALL AT ONCE in your template:     $GLOBALS["my_theme"]->wordpress->dynamicSidebar('footer_widget_areas_3');
     *
     * N.B. Unlike registerSidebar() the $description param DOES NOT EXIST and must
     *      be sent in the $args if you want to customize it
     *
     * N.B. Unlike registerSidebar() an $id is created for the entire GROUP of sidebars
     *      from the $name param. Underscored_with_how_many (see usage above).
     *      And if you think you'll forget the method returns that so you pin it on your wall.
     *
     * N.B. Remember: As in floating next to each other or one after the other
     * If you are just lazy and don't need multiple sidebars named CONSECUTIVLEY
     * then just use the built-in WP function register_sidebars() and dynamic_sidebar
     * the traditional way with the WP API
     *
     * @since       0.1
     * @access      public
     * @see         KST_Wordpress::dynamicSidebars
     * @uses        register_sidebar() WP function
     * @param       required int    $how_many The number of consecutive sidebars to create
     * @param       required string $name
     * @param       optional array  $args send any of the arguments for WP register_sidebar()
     *                  optional string $description
     *                  optional string $id defaults to $name
     *                  optional string $before_widget
     *                  optional string $after_widget
     *                  optional string $before_title
     *                  optional string $after_title
     * @return      string id that was registered
     * @todo        sprint the description so they could add their own custom counter message like the default
    */
    public function registerSidebars($how_many, $name, $args = NULL) {

        $id = trim(str_replace(" ", "_", strtolower($name)));

        $defaults = array(
            'description'   => "1 of {$how_many} consecutive widget areas. Does not appear if no widgets added.",
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => '</aside>',
            'before_title'  => '<h2 class="widget_title">',
            'after_title'   => '</h2>'
            );
        $args = wp_parse_args( $args, $defaults );

        for ($i = 1; $i <= $how_many; $i++) {
            $this_id = "{$id}_{$i}";
            register_sidebar( array_merge( array(
                'name'          => "{$name} {$i}",
                'id'            => $this_id,
            ), $args ) ); // Merge our defaults with these
        }

        return $id . "_" . $how_many; //register_sidebar( $args );
    }


    /**
     * WP enhancement for
     * automagically outputting multiple CONSECUTIVE dynamic_sidebars
     *
     * As in floating next to each other or one after the other
     * If you are just lazy and don't need multiple sidebars named CONSECUTIVLEY
     * then just use the built-in WP function register_sidebars() and dynamic_sidebar
     * the traditional way with the WP API
     *
     * For outputting NAMED CONSECUTIVE registered sidebars quickly
     *
     * Echoes output
     *
     * @since       0.1
     * @see         KST_Wordpress::registerSidebars
     * @access      public
     * @uses        register_sidebar() WP function
     * @param       required string $name
    */
    public function dynamicSidebars($id) {

        $id = trim(str_replace(" ", "_", strtolower($id))); // Do it again in case they don't quite get it but are close i.e. use the name with the number

        $segments = explode('_', $id);
        $how_many = end($segments);
        $id = substr_replace($id, '', -1, strlen($how_many)); // str_replace("", "{$how_many}", $id;

        $i = 1;
        while ( $i <= $how_many ) {
            echo "<section class='ft_widget'>";
                dynamic_sidebar( $id . $i );
            echo "</section>";
            $i++;
        }
    }
}
