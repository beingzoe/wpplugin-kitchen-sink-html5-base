<?php
/**
 * WordPress Widget Class to show older/newer paged posts buttons in dynamic sidebar
 *
 * @package     KitchenSinkHTML5Base
 * @subpackage  Widgets
 * @version     0.1
 * @since       0.1
 * @author      zoe somebody http://beingzoe.com/zui/
*/

/**
 * Parent class
 */
require_once KST_DIR_LIB . '/KST/Widget.php';

class KST_Widget_NavPosts extends KST_Widget {

    /**
     * Widget constructor
     *
     * @since       0.1
     * @uses        WP_Widget()
     */
    function KST_Widget_NavPosts() {
        $widget_name        = 'KST: Older/Newer buttons';
        $widget_options     = array(    'classname'     => 'widget_theme_nav_posts clearfix',
                                        'description'   => __( "Displays Older/Newer buttons for paged posts navigation. Will only show on blog index or archive with &gt; 1 page of results. ") );
        //$control_options  = array( 'width' => 500, 'height' => 300 );
        parent::WP_Widget(false, $widget_name, $widget_options);
    }

    /**
     * Filter widget content for output
     *
     * @since       0.1
     * @see         WP_Widget::widget
     * @global      object $wp_query WP main query
     * @uses        widget()
     * @uses        is_home()
     * @uses        is_archive()
     * @uses        apply_filters()
     * @uses        next_posts_link()
     * @uses        get_next_posts_link()
     * @uses        previous_posts_link()
     * @uses        get_previous_posts_link()
     */
    function widget($args, $instance) {
        extract( $args );

        global $wp_query;

        if ( ( is_home() || is_archive() ) && $wp_query->max_num_pages > 1 ) {

            $title      = apply_filters('widget_title', $instance['title']);
            $next       = $instance['next'];
            $previous   = $instance['previous'];

            echo $before_widget;
            if ( $title )
                echo $before_title . $title . $after_title;

            if ( get_next_posts_link() ) {
                echo '<div class="previous" title="Browse older posts">';
                    next_posts_link( "$next" ); /* older */
                echo '</div>';
            }
            if ( get_previous_posts_link() ) {
                echo '<div class="next" title="Browse newer posts">';
                    previous_posts_link( "$previous" ); /* newer */
                echo '</div>';
            }
            echo $after_widget;
        }
    }

    /**
     * Save widget sidebar settings (from form)
     *
     * @since       0.1
     * @see         WP_Widget::update
     * @param       required array $new_instance
     * @param       required array $old_instance
     * @return      array
     */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title']      = strip_tags($new_instance['title']);
        $instance['next']       = strip_tags($new_instance['next']);
        $instance['previous']   = strip_tags($new_instance['previous']);
        return $instance;
    }

    /**
     * Widget edit form
     *
     * @since       0.1
     * @see         WP_Widget::form
     * @uses        form()
     * @uses        get_field_id()
     * @uses        get_field_name()
     * @uses        esc_attr()
     * @uses        _e()
     */
    function form($instance) {
        $title = esc_attr($instance['title']);
        $next = esc_attr($instance['next']);
        $previous = esc_attr($instance['previous']);
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('next'); ?>"><?php _e('Older:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('next'); ?>" name="<?php echo $this->get_field_name('next'); ?>" type="text" value="<?php echo $next; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('previous'); ?>"><?php _e('Newer:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('previous'); ?>" name="<?php echo $this->get_field_name('previous'); ?>" type="text" value="<?php echo $previous; ?>" /></label></p>
        <?php
    }
}

add_action('widgets_init', create_function('', 'return register_widget("KST_Widget_NavPosts");'));
