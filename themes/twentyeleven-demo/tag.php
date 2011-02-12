<?php
/**
 * Tag archive template
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.1
 * @since       0.1
*/

get_header();

?>

<section id="bd" class="clearfix hfeed" role="main">

    <h1><?php
        printf( __( '<span class="smaller quiet">Content Tagged:</span> %s', 'twentyten' ), '<span>' . single_tag_title( '', false ) . '</span>' );
    ?></h1>

    <?php
    /* Run the loop for the tag archive to output the posts
     * If you want to overload this in a child theme then include a file
     * called loop-tag.php and that will be used instead.
     */
    include( locate_template( array( 'loop.php' ) ) ); /* get_template_part( 'loop' ); */
    //get_template_part( 'loop', 'tag' );
    ?>

</section><!-- #bd -->

<?php
get_sidebar();
get_footer();
?>
