<?php
/**
 * Search results template
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.1
 * @since       0.1
*/

get_header();

?>

<section id="bd" class="clearfix hfeed" role="main">
<article id="page-other" class="page">
<?php if ( have_posts() ) : ?>

    <h1><?php printf( __( 'Search Results for: %s', 'twentyten' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
    <?php
    /* Run the loop for the search to output the results.
     * If you want to overload this in a child theme then include a file
     * called loop-search.php and that will be used instead.
     */
    include( locate_template( array( 'loop.php' ) ) ); /* get_template_part( 'loop' ); */
    //get_template_part( 'loop', 'search' );
    ?>

<?php else : ?>
    <h1>Search</h1>
    <p><?php _e( 'Huh, nothing matched your search criteria.<br />Please try again with some different keywords.', 'twentyten' ); ?></p>
    <?php get_search_form(); ?>

<?php endif; ?>
</article><!-- #page-other -->
</section><!-- #bd -->

<?php
get_sidebar();
get_footer();
?>
