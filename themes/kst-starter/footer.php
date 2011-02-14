<?php
/**
 * Footer partial include
 *
 * Closes <div id="pg"><div id="doc">, <body>, <html>
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  kst-starter
 * @version     0.1
 * @since       0.1
 * @global      object $_GLOBALS["my_theme"]
*/
?>
</div><!-- close #pg -->
<footer id="ft" class="clearfix" role="contentinfo">
    <?php
    echo '<section id="ft_widgets" class="widgets clearfix">';
        $GLOBALS["my_theme"]->wordpress->dynamicSidebars('footer_area_4');
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
