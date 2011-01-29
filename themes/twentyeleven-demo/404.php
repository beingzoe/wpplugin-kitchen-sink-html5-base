<?php
/**
 * 404 (not found) template
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.4
 * @since       1.0
 * @todo        create a dynamic page that includes recent content/featured/etc...
 */

get_header();

?>

<section id="bd" class="clearfix" role="main">
    <section id="page-other" class="page error404 not-found">
    <?php
    _e( '<h1>WOOPS! We can\'t find that page!</h1>', 'twentyten' );
    _e( '<p>Try searching or check the menu!</p>', 'twentyten' );
    get_search_form();
    ?>
    </section><!-- #page-other -->
</section><!-- close #bd -->

<?php
get_sidebar();
get_footer();
?>
