<?php
/**
 * Display navigation to next/previous pages when applicable
 * DRY include/partial
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.4
 * @since       1.0
 */
if ( $wp_query->max_num_pages > 1 ) {
?>
    <nav class="wp_next_previous_more clearfix">
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
                else
                    $exclude = $previous_post->ID;

                $featured_posts = get_posts("numberposts=5&exclude=$exclude");
                foreach($featured_posts as $post) :
                setup_postdata($post);
                ?>
                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                <?php endforeach; ?>
            </ul>
            <!--
        <div class="previous"><?php next_posts_link( __( '<strong><span>&larr;</span> Older posts:</strong>', 'twentyten' ) ); ?></div>
        <div class="next"><?php previous_posts_link( __( '<strong>Newer posts:<span>&rarr;</span></strong>', 'twentyten' ) ); ?></div>
        <h2 style="clear: both; margin-top: 18px;">Other recent posts...</h2>
        <ul style="clear: both;">
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
        <p>&nbsp;</p>
        -->
    </nav>
<?php } ?>
