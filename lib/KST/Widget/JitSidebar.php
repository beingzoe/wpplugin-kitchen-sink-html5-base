<?php
/**
 * Parent class
 */
require_once KST_DIR_LIB . '/KST/Widget.php';

/**
 * WordPress Widget Class to trigger JIT (just-in-time) floating sidebar
 *
 * Any widget _below this_ in that sidebar is auto-wrapped with a div
 * and will start to 'float' (containing element becomes position:fixed)
 * when it is scrolled to the top of the screen. Becomes normal inline content
 * again when the page is scrolled back down.
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  Widgets
 * @version     0.1
 * @todo        in js figure out if it is overlapping the footer and quit fixing
 * @todo        pluginify js for use outside of Kitchen Sink
 */
class KST_Widget_JitSidebar extends KST_Widget {

    /**
     * Widget constructor
     *
     * @since       0.1
     * @uses WP_Widget()
     */
    function KST_Widget_JitSidebar() {
        $widget_name        = 'KST: JIT Sidebar Start';
        $widget_ops          = array('classname' => 'widget_jit_sidebar', 'description' => __( "Any widget BELOW this widget will magically float down the page as you scroll when it reaches the top of the window.") );
        parent::WP_Widget(false, $widget_name, $widget_ops);
    }

    /**
     * Filter widget content for output
     *
     * @since       0.1
     * @see         WP_Widget::widget
     * @param       required array $args
     * @param       required array $instance
     * @uses        add_action()
     */
    function widget($args, $instance) {
        extract( $args );
        //no settings so no variables to get/set
        add_action('wp_footer', array('KST_Widget_JitSidebar', 'printJitSidebarScripts'));
        echo $before_widget;
        echo $after_widget;
    }

    /**
     * Save widget sidebar settings (from form)
     *
     * @since 0.1
     * @see WP_Widget::update
     * @param       required array $new_instance
     * @param       required array $old_instance
     * @return      array
     */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        //no settings so nothing to save
        return $instance;
    }

    /**
     * Widget edit form
     *
     * @since       0.1
     * @see WP_Widget::form
     */
    function form($instance) {
        //no settings so just give them more help
        echo '<p>Any widget added below this one will magically start floating when they are scrolled to the top of the page.</p>';
    }

    /**
     * Print styles and scripts only if we need them
     *
     * We must register and print them ourselves because we can't enqueue by the time shortcodes are executing
     * Use add_action('wp_footer', 'print_scrollable_scripts'); to safely load javascript
     *
     * @since       0.1
     * @uses        wp_register_script() WP function
     * @uses        get_template_directory_uri() WP function
     * @uses        wp_print_scripts() WP function
     */
    public static function printJitSidebarScripts() {
        wp_register_script('jit_sidebar', KST_URI_ASSETS . '/javascripts/jquery/jquery.kst_jit_sidebar.js' , array('jquery') , '0.1', true);
        /* just print the script directly to the page with wp_footer */
        wp_print_scripts('jit_sidebar');
        echo '<script type="text/javascript">jQuery(document).ready(function($) { if(jQuery().jit_sidebar) { $(this).jit_sidebar(); }; });</script>';
    }

}
add_action('widgets_init', create_function('', 'return register_widget("KST_Widget_JitSidebar");'));
