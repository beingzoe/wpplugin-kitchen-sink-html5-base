<?php
/**
 * Display navigation to next/previous pages of posts when applicable
 * DRY include/partial
 *
 * Note that by default this also includes a list of the 5 most recent posts
 * immediately following the previous/next.
 *
 * nav
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
if ( $wp_query->max_num_pages > 1 ) {
?>
    <nav class="wp_next_previous_more clearfix" role="navigation">
        <ul>
        <li class="previous"><?php next_posts_link( '<strong><span>&larr;</span> Older posts:</strong>' ); ?></li>
        <li class="next"><?php previous_posts_link( '<strong>Newer posts:<span>&rarr;</span></strong>' ); ?></li>
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
                else
                    $exclude = $previous_post->ID;

                $featured_posts = get_posts("numberposts=5&exclude=$exclude");
                foreach($featured_posts as $post) :
                setup_postdata($post);
                ?>
                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </ul>
    </nav>
<?php }
