<?php
/**
 * Single post template
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

<?php
    if ( have_posts() ) while ( have_posts() ) {
        the_post();
?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <?php include( locate_template( array( '_entry_header.php' ) ) ); /* get_template_part('_entry_header.php'); Article Header */ ?>

            <?php
                /* show featured image if exists, page 1 if paged, and we have not included a flag otherwise not to */
                if ( has_post_thumbnail() && $page == 1 && !get_post_meta($post->ID, 'hide_featured_image', true) ) {
                    echo "<div class='post-thumbnail-single'>";
                    the_post_thumbnail('post-thumbnail-single', array('class' => 'aligncenter post-thumbnail-single') );
                    echo "</div>";
                }
            ?>

            <div class="wp_entry clearfix">
                <?php the_content(); ?>
            </div><!-- .wp_entry -->

            <?php
                include( locate_template( array( '_wp_link_pages.php' ) ) ); // get_template_part('_wp_link_pages.php'); Paged articles <!--next-->
                include( locate_template( array( '_entry_footer.php' ) ) ); // get_template_part('_entry_footer.php'); Article Footer
                include( locate_template( array( '_nav_post.php' ) ) ); // get_template_part('_nav_post.php'); next/previous/recent/featured post to post
                comments_template( '', true ); // Comments and comment form
            ?>

        </article><!-- #post-## -->

<?php } /* end of if while loop */ ?>

</section><!-- #bd -->

<?php
get_sidebar();
get_footer();
?>
