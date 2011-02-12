<?php
/**
 * Infamous WP loop displays posts and post content.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop.php or
 * loop-template.php, where 'template' is the loop context
 * requested by a template. For example, loop-index.php would
 * be used if it exists and we ask for the loop with:
 * <code>get_template_part( 'loop', 'index' );</code>
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.1
 * @since       0.1
*/

global $twenty_eleven_options;

//query_posts( "posts_per_page=10&paged={$paged}&cat=" . $cat );


/* If no posts, i.e. an empty archive page */
if ( ! have_posts() ) { ?>
     echo $cool;
    <section id="page-other" class="page no_results">
        <header class="wp_entry_header">
            <h1><?php _e( 'No posts or pages found', 'twentyten' ); ?></h1>
        </header>
        <div class="wp_entry clearfix">
            <p><?php _e( 'Try searching or check the menu!', 'twentyten' ); ?></p>
            <?php get_search_form(); ?>
        </div><!-- .entry-content -->
    </section><!-- #post-0 -->
<?php
} //end if !have_posts

/* Don't ask for this stuff every loop */
//$asides_gallery = $twenty_eleven_options->getOption("layout_category_gallery_slug");
//$asides_aside   = $twenty_eleven_options->getOption("layout_category_aside_slug");

/* Start the Loop */
while ( have_posts() ) {
    the_post(); // Get them

    if ( !is_sticky() )
        the_date('', '<h1 class="wp_loop_date">', '</h1>');


    /* ASIDES */
    /* Gallery Category Asides */
    if ( 0 == 1 && is_category( $asides_gallery ) && in_category( _x($asides_gallery, 'gallery category slug', 'twentyten') ) ) {
?>
        <article id="<?php echo get_post_type() . '-' . $post->ID; ?>" <?php post_class(); ?>>

            <?php include( locate_template( array( '_entry_header.php' ) ) ); /* get_template_part('_entry_header.php'); Article Header */ ?>

            <div class="wp_entry clearfix">
<?php
            if ( post_password_required() ) {
                the_content();
            } else {
                $images = get_children( array( 'post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order', 'order' => 'ASC', 'numberposts' => 999 ) );
                $total_images = count( $images );
                $image = array_shift( $images );
                $image_img_tag = wp_get_attachment_image( $image->ID, 'thumbnail' );
?>
                <div class="gallery-thumb">
                    <a class="size-thumbnail" href="<?php the_permalink(); ?>" rel="bookmark"><?php echo $image_img_tag; ?></a>
                </div>
<?php           printf( __( '<p><em>This gallery contains <a %1$s>%2$s photos</a>.</em></p>', 'twentyten' ),
                            'href="' . get_permalink() . '" title="' . sprintf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ) . '" rel="bookmark"',
                            $total_images
                        );
                the_excerpt();
            }
?>
            </div><!-- .wp_entry -->
            <footer class="wp_entry_footer">
                <a href="<?php echo get_term_link( _x($asides_gallery, 'gallery category slug', 'twentyten'), 'category' ); ?>" title="<?php esc_attr_e( 'View posts in the Gallery category', 'twentyten' ); ?>"><?php _e( 'More Galleries', 'twentyten' ); ?></a>
                <span class="meta-sep">|</span>
                <span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
                <?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
            </footer><!-- .wp_entry_footer -->
        </article><!-- #post-## -->
<?php
    /* Asides Category Asides */
    /*
    } else if ( in_category( _x($asides_aside, 'asides category slug', 'twentyten') ) ) {
        $loop_asides->add($post);
        $kst_temp_loop_asides .= '<div class="wp_entry"><a href="' . get_permalink() . '" style="float: left;"><span class="meta-nav">' . get_the_time() . '&raquo;</span></a>' . wptexturize($post->post_content) . '</div>';
        */
?>

<?php
    /* ALL OTHER POSTS */
    } else {
        /*
        if ( in_category( _x($asides_aside, 'asides category slug', 'twentyten') ) ) {
            continue;
        }
        */
?>
        <article id="<?php echo get_post_type() . '-' . $post->ID; ?>" <?php post_class( 'entry' ); ?>>
            <?php include( locate_template( array( '_entry_header.php' ) ) ); /* get_template_part('_entry_header.php'); Article Header */ ?>
            <div class="wp_entry clearfix">
            <?php
            /* show post thumbnail with container for more flexible formatting but not in asides (or whatever) */
            if ( has_post_thumbnail() ) {
                echo "<div class='post-thumbnail'><a href='" . get_permalink() . "'>";
                the_post_thumbnail('thumbnail', array('class' => 'aligncenter post-thumbnail') );
                echo "</a></div>";
            }

            /* always show a forced excerpt when not on blog index or if an excerpt exists */
            if ( is_archive() || is_search() || has_excerpt() ) {
                the_excerpt();
                echo '<a href="" class="more-link">Read the full post <span class="meta-nav">&#8230;</span></a>';
            } else {
                the_content( __( 'Read the full post <span class="meta-nav">&#8230;</span>', 'twentyten' ) );
            }
?>
            </div><!-- .wp_entry -->
<?php
            include( locate_template( array( '_wp_link_pages.php' ) ) ); // get_template_part('_wp_link_pages.php'); Paged articles <!--next-->
            include( locate_template( array( '_entry_footer.php' ) ) ); // get_template_part('_entry_footer.php');
?>
        </article><!-- #post-## -->

<?php
        comments_template( '', true );
    } // END main loop IF
}; // END the loop

include( locate_template( array( '_nav_posts.php' ) ) ); // get_template_part('_nav_posts.php'); next/previous/recent/featured index to index

?>
