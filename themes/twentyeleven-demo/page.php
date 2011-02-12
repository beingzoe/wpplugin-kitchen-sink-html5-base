<?php
/**
 * Page template
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

    <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

        <article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>

            <?php /* the_title(); not used for most cms style installs for SEO control */ ?>

            <div class="wp_entry clearfix">
                <?php
                    // h1 is generally handled in the Page content (unlike posts
                    the_content();
                ?>
            </div><!-- .wp_entry -->

            <?php
                include( locate_template( array( '_wp_link_pages.php' ) ) ); // get_template_part('_wp_link_pages.php'); Paged articles <!--next-->
                edit_post_link( __( 'Edit', 'twentyten' ), '<span class="wp_entry_meta wp_entry_meta_edit">', '</span>' );
            ?>

        </article><!-- .page -->

    <?php endwhile; ?>

</section><!-- close #bd -->

<?php
get_sidebar();
get_footer();
?>
