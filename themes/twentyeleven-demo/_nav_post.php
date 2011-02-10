<?php
/**
 * Display navigation to next/previous/more post to post when applicable
 * DRY include/partial
 *
 * Note that by default this also includes a list of the 5 most recent posts
 * immediately following the previous/next.
 *
 * Aside
 *      ul
 *          li
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.1
 * @since       0.1
*/
?>

<aside class="wp_next_previous_more clearfix">
    <ul>
        <li class="previous"><?php previous_post_link( '%link', '<strong><span>&larr;</span> PREVIOUS:</strong><br />%title' ); ?></li>
        <li class="next"><?php next_post_link( '%link', '<strong>NEXT:<span>&rarr;</span></strong><br />%title' ); ?></li>
        <li class="more">
            <span class="more_title">Other recent posts...</span>
            <ul>
            <?php
                $next_post = get_adjacent_post(true,'',false);
                $previous_post = get_adjacent_post(true,'',true);
                if ( $next_post && $previous_post )
                    $exclude = $next_post->ID . "," . $previous_post->ID;
                else if ( $next_post )
                    $exclude = $next_post->ID;
                else if ( $previous_post )
                    $exclude = $previous_post->ID;
                else
                    $exclude = NULL;

                global $post, $id;

                $tmp_post = $post; /* save the original loop */
                $tmp_id = $id; /* save the original loop */

                $featured_posts = get_posts("numberposts=5&exclude=$exclude");
                foreach($featured_posts as $post) :
                    setup_postdata($post);
            ?>
                    <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
            <?php
                    $post = $tmp_post; /* restore the original loop */
                    $id = $tmp_id; /* restore the original loop */
                    endforeach;
            ?>
            </ul>
        </li>
    </ul>
</aside>
