<?php
/**
 * Footer partial include
 * 
 * Closes <div id="pg"><div id="doc">, <body>, <html>
 * 
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package WordPress
 * @subpackage kitchenSinkTheme
 * @version     0.4
 * @since       1.0
 * 
 * Based on kitchenSink theme Version 0.3 and ZUI by zoe somebody http://beingzoe.com/zui/
 */
 
global $kst_options, $kst_sidebars_footer; // This (and all template globals needs resolved and removed to put these templates in the core as defaults)
 
?>
</div><!-- close #pg -->
<footer id="ft" class="clearfix">
    <?php 
    wp_nav_menu( array( 
                'theme_location'    => 'ft_menu',
                'container'         => 'nav', 
                'container_class'   => 'clearfix', 
                'menu_id'           => 'ft_menu',
                'sort_column'       => 'menu_order',
                'depth'             => '1'
                ) );
    echo "this is here";
    kst_widget_output_multiple_areas($kst_sidebars_footer);
    ?>
    <div id="ft_logo">
        <a href="<?php echo home_url(); ?>/" title="<?php bloginfo('name'); ?> Home"><?php bloginfo('name'); ?></a>
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

<!--[if lt IE 7 ]>
<script src="<?php echo get_template_directory_uri(); ?>/_assets/javascripts/libraries/dd_belatedpng.js"></script>
<script> DD_belatedPNG.fix('img, .png_bg'); </script>
<![endif]-->

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script>!window.jQuery && document.write(unescape('%3Cscript src="<?php echo get_template_directory_uri(); ?>/_assets/javascripts/jquery/jquery-1.4.4.min.js"%3E%3C/script%3E'))</script>
  
<?php 
    wp_footer();
    
    /* KST built-in google analytics output with HTML5 Boilerplate script */
    
    if ( isset($kst_options) && $kst_options->get_option("ga_tracking_id") ) {
?>
        <script type="text/javascript">
            var _gaq = [['_setAccount', '<?php echo $kst_options->get_option("ga_tracking_id"); ?>'], ['_trackPageview']];
            (function(d, t) {
            var g = d.createElement(t),
                s = d.getElementsByTagName(t)[0];
            g.async = true;
            g.src = ('https:' == location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            s.parentNode.insertBefore(g, s);
            })(document, 'script');
        </script>
    <?php } ?>

</body>
</html>
