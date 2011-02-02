<?php
/**
 * Posts archive generic template
 *
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.1
 * @since       0.1
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 *
 * Based on Twenty Ten archive.php
*/

get_header();

?>

<section id="bd" class="clearfix hfeed" role="main">

    <?php
        /* Queue the first post, that way we know
         * what date we're dealing with (if that is the case).
         *
         * We reset this later so we can run the loop
         * properly with a call to rewind_posts().
         */
        if ( have_posts() )
            the_post();
    ?>

            <h1>
                <?php if ( is_day() ) : ?>
                    <?php printf( __( 'Posts from <span>%s</span>', 'twentyten' ), get_the_date() ); ?>
                <?php elseif ( is_month() ) : ?>
                    <?php printf( __( 'Posts from <span>%s</span>', 'twentyten' ), get_the_date('F Y') ); ?>
                <?php elseif ( is_year() ) : ?>
                    <?php printf( __( 'Posts from <span>%s</span>', 'twentyten' ), get_the_date('Y') ); ?>
                <?php else : ?>
                    <?php _e( 'Blog Archives', 'twentyten' ); ?>
                <?php endif; ?>
            </h1>

    <?php
        /* Since we called the_post() above, we need to
         * rewind the loop back to the beginning that way
         * we can run the loop properly, in full.
         */
        rewind_posts();

        /* Run the loop for the archives page to output the posts.
         * If you want to overload this in a child theme then include a file
         * called loop-archives.php and that will be used instead.
         */
        include( locate_template( array( 'loop.php' ) ) ); /* get_template_part( 'loop' ); */
         //get_template_part( 'loop', 'archive' );
    ?>

</section><!-- #bd -->

<?php
get_sidebar();
get_footer();
?>
