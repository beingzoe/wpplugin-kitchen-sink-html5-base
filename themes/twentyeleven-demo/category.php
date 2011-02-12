<?php
/**
 * Category archive template
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.1
 * @since       0.1
 *
 * Based on Twenty Ten category.php
*/

get_header();

?>

<section id="bd" class="clearfix hfeed" role="main">

    <h1><?php
        printf( __( '<span class="smaller quiet">Category:</span> %s', 'twentyten' ), '<span>' . single_cat_title( '', false ) . '</span>' );
    ?></h1>
    <?php
        $category_description = category_description();
        if ( ! empty( $category_description ) )
            echo '<div class="archive-meta">' . $category_description . '</div>';

    /* Run the loop for the category page to output the posts.
     * If you want to overload this in a child theme then include a file
     * called loop-category.php and that will be used instead.
     */
    include( locate_template( array( 'loop.php' ) ) ); /* get_template_part( 'loop' ); */
    //get_template_part( 'loop', 'category' );
    ?>

</section><!-- #bd -->

<?php get_sidebar(); //include( locate_template( array( 'sidebar.php' ) ) ); /* get_sidebar(); */ ?>
<?php get_footer(); //include( locate_template( array( 'footer.php' ) ) ); /* get_footer(); */ ?>
