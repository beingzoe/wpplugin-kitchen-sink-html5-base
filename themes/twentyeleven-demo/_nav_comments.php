<?php
/**
 * Display navigation to next/previous COMMENTS page(s) when applicable
 * DRY include/partial
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.1
 * @since       0.1
*/
if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { // Comment navigation?
    echo "<nav class='wp_comments_nav' role='navigation'>";
        echo "<div class='previous'>" . get_previous_comments_link( __( '&larr; Older Comments', 'twentyten' ) ) . "</div>";
        echo "<div class='next'>" . get_next_comments_link( __( 'Newer Comments &rarr;', 'twentyten' ) ) . "</div>";
    echo "</nav><!-- .navigation -->";
} // End check for comment navigation
