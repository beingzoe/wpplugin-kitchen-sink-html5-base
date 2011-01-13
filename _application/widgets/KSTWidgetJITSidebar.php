<?php
/**
 * WordPress Widget Class to trigger JIT (just-in-time) floating sidebar
 * 
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     WordPress
 * @subpackage  KitchenSinkWidgetClasses
 * @version     0.1 
 */
 
class KSTWidgetJITSidebar extends WP_Widget {
    /**
     * Widget constructor
     * 
     * @since 0.1
     * @uses WP_Widget()
     */
    function KSTWidgetJITSidebar() {
        $widget_ops = array('classname' => 'widget_jit_sidebar', 'description' => __( "Any widget BELOW this widget will magically float down the page as you scroll when it reaches the top of the window.") );
        parent::WP_Widget(false, $name = 'Theme: JIT Sidebar Start', $widget_ops);	
    }
    
    /**
     * Filter widget content for output
     * 
     * @see         WP_Widget::widget
     * @param       required array $args
     * @param       required array $instance
     * @uses        add_action()
     */
    function widget($args, $instance) {	
        extract( $args );
        //no settings so no variables to get/set
        add_action('wp_footer', 'print_jit_message_scripts');
        echo $before_widget;
        echo $after_widget; 
    }
    
    /**
     * Save widget sidebar settings (from form)
     * 
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
     * @see WP_Widget::form
     */
    function form($instance) {				
        //no settings so just give them more help
        ?>
            <p>Any widget added below this one will magically start floating when they are scrolled to the top of the page.</p>
        <?php 
    }

} // class KSTWidgetNavPost

// register KSTWidgetNavPost widget
add_action('widgets_init', create_function('', 'return register_widget("KSTWidgetJITSidebar");'));

/**
 * Print styles and scripts only if we need them
 * 
 * We must register and print them ourselves because we can't enqueue by the time shortcodes are executing
 * Use add_action('wp_footer', 'print_scrollable_scripts'); to safely load javascript 
 * 
 * @since       0.4
 * @uses        wp_register_script() WP function
 * @uses        get_template_directory_uri() WP function
 * @uses        wp_print_scripts() WP function
 */
function print_jit_message_scripts() {
        wp_register_script('jit_sidebar', get_template_directory_uri() . '/_assets/javascripts/jquery/jquery.kst_jit_sidebar.js' , array('jquery') , '0.1', true);
        /* just print the script directly to the page with wp_footer */
        wp_print_scripts('jit_sidebar');
        echo '<script type="text/javascript">jQuery(document).ready(function($) { if(jQuery().jit_sidebar) { $(this).jit_sidebar(); }; });</script>';
}

?>
