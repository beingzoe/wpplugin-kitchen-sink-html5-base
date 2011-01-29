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
 * @version     0.4
 * @since       1.0
 *
 * Based on kitchenSink theme Version 0.3 and ZUI by zoe somebody http://beingzoe.com/zui/
 */
?>
</div><!-- close #pg -->
<footer id="ft" class="clearfix" role="contentinfo">
    <?php
    echo "<nav class='clearfix' rel='navigation'>"; //manually creating container for ARIA landmark roles
    wp_nav_menu( array(
                'theme_location'    => 'ft_menu',
                'container'         => false,
                'container_class'   => false,
                'menu_id'           => 'ft_menu',
                'sort_column'       => 'menu_order',
                'depth'             => '1'
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

<address class="hmeta vcard">
    <a class="fn org url" href="http://beingzoe.com/">Company Name</a>
    <span class="adr">
        <span class="tel">
            <span class="type">Work</span> 619-123-4579
        </span>
        <span class="tel">
            <span class="type">Fax</span> 619-456-4579
        </span>
    </span>
</address>

<?php wp_footer(); ?>

</body>
</html>
