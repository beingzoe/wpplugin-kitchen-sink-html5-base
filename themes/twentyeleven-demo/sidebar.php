<?php
/**
 * Default "blog" sidebar partial include
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.1
 * @since       0.1
*/

?>
<section id="sb" class="wp_sidebar widgets clearfix" role="complementary">
<?php
if ( is_front_page() )
    $GLOBALS["my_theme"]->wordpress->dynamicSidebar('Home Sidebar');
else if ( is_page()  )
    $GLOBALS["my_theme"]->wordpress->dynamicSidebar('Pages Sidebar');
else
    $GLOBALS["my_theme"]->wordpress->dynamicSidebar('Blog Sidebar');
?>
</section><!-- #sb -->
