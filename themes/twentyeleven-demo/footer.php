<?php
/**
 * Footer partial include
 *
 * Closes <div id="pg"><div id="doc">, <body>, <html>
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.1
 * @since       0.1
 * @global      object $_GLOBALS["my_theme"]
 *
 * Based on kitchenSink theme Version 0.3 and ZUI by zoe somebody http://beingzoe.com/zui/
*/
?>
</div><!-- close #pg -->
<footer id="ft" class="clearfix" role="contentinfo">
    <?php
    echo "<nav id='ft_menu' class='clearfix' rel='navigation'>"; //manually creating container for ARIA landmark roles
    wp_nav_menu( array(
                'theme_location'    => 'ft_menu',
                'container'         => false,
                'container_class'   => false,
                'menu_id'           => 'ft_menu_list',
                'sort_column'       => 'menu_order',
                'depth'             => '1',
                'fallback_cb'     => 'kstWpNavMenuFallbackCb'
                ) );
    echo "</nav>";
    echo '<section id="ft_widgets" class="widgets clearfix">';
    $GLOBALS["my_theme"]->wordpress->dynamicSidebars('footer_area_3');
    echo '</section>';
    ?>
    <div id="ft_logo">
        <a href="<?php echo home_url(); ?>/" title="<?php bloginfo('name'); ?> Home" rel="home"><?php bloginfo('name'); ?></a>
    </div>
    <div id="ft_legal">
        Copyright &copy; 2010, Somebody All Rights Reserved
    </div>
</footer>

</div><!-- close #doc -->

<?php wp_footer(); ?>

</body>
</html>
