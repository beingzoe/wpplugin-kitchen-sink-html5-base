<?php
/**
 * Author archive template
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.1
 * @since       0.1
 *
 * Based on Twenty Ten author.php
*/

get_header();

?>

<section id="bd" class="clearfix hfeed" role="main">

<?php
    /* Queue the first post to get author data.
     * Reset after rewind_posts()
     */
    if ( have_posts() )
        the_post();
?>
        <header>
            <div id="entry-author-info">
                <div id="author-avatar">
                    <?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'kst_author_bio_avatar_size', 60 ) ); ?>
                </div><!-- #author-avatar -->
<?php
                /* If a user has filled out their description show it */
                if ( get_the_author_meta( 'description' ) ) { ?>
                    <div id="author-description">
                        <h2><?php printf( __( 'About %s', 'twentyten' ), get_the_author() ); ?></h2>
                        <?php the_author_meta( 'description' ); ?>
                    </div><!-- #author-description	-->
                </div><!-- #entry-author-info -->
<?php
                } /* END author description */
?>
        <h1 class="author"><?php printf( __( '<span class="smaller quiet">Content by:</span> %s', 'twentyten' ), "<span class='vcard'><a class='url fn n' href='" . get_author_posts_url( get_the_author_meta( 'ID' ) ) . "' title='" . esc_attr( get_the_author() ) . "' rel='me'>" . get_the_author() . "</a></span>" ); ?></h1>
        </header>
<?php
        rewind_posts(); //

            /* Run the loop for the author archive page to output the authors posts
             * If you want to overload this in a child theme then include a file
             * called loop-author.php and that will be used instead.
             */
            include( locate_template( array( 'loop.php' ) ) ); /* get_template_part( 'loop' ); */
            //get_template_part( 'loop', 'author' );
?>

</section><!-- #bd -->

<?php get_sidebar(); //include( locate_template( array( 'sidebar.php' ) ) ); /* get_sidebar(); */ ?>
<?php get_footer(); //include( locate_template( array( 'footer.php' ) ) ); /* get_footer(); */ ?>
