<?php
/**
 * Parent class for KST_Widgets
 * 
 * @package     KitchenSinkHTML5Base
 * @subpackage  KitchenSinkWidgetClasses
 * @version     0.1 
 * @since       0.1
 * @author      zoe somebody 
 * @link        http://beingzoe.com/
 * @author      Scragz 
 * @link        http://scragz.com/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 */
class KST_Widget extends WP_Widget {
    /**
     * Registers a widget class with WP
     * @param String $widget Name of the class to register
     *
    */
    public static function registerWidget($widget) {
        require_once KST_DIR_LIB . "/KST/Widget/{$widget}.php";
        add_action('widgets_init', create_function('', "return register_widget('KST_Widget_{$widget}');"));
    }
}
