<?php
/**
 * Default "blog" sidebar partial include
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     WordPress
 * @subpackage  kitchenSinkTheme
 * @version     0.4
 * @since       0.1
 */

global $kst_sidebar; // global variable problem that can't exist if these are templates in the core
 
?>
<section id="sb" class="wp_sidebar widgets clearfix">
<?php
if ( is_home() )
    $kst_sidebar = 'widgets_home';
else if ( is_page()  )
    $kst_sidebar = 'widgets_pages';
else
    $kst_sidebar = 'widgets_blog';
    
if ( ! dynamic_sidebar( $kst_sidebar ) ) { ?>
<aside id="search" class="sb_widget widget_search">
    <?php get_search_form(); ?>
</aside>
<aside id="meta" class="sb_widget ">
    <h2 class="widget_title"><?php _e( 'Meta', 'twentyten' ); ?></h2>
    <ul>
        <?php wp_register(); ?>
        <li><?php wp_loginout(); ?></li>
        <?php wp_meta(); ?>
    </ul>
</aside>
<?php 
}
?>
</section><!-- #sb -->
