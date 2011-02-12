<?php
/**
 * Display paged post/page pager bar/links
 * DRY include/partial
 *
 * Rarely used but pretty cool feature
 * <!--nextpage-->
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.1
 * @since       0.1
 */
global $numpages, $multipage, $more;
if ( $multipage ) {
    echo '<nav class="wp_entry_pager" role="navigation">';
        if ( !has_excerpt() )
            $more = 1; // Tell WordPress there is "more" even in an index loop so first page is not clickable and show [next] link appears

        // Standard [1][2][3] paged entry
        wp_link_pages( array( 'pagelink' => '<span>%</span>', 'before' => '' . __( 'Pages:', 'twentyten' ), 'after' => '' ) );

        // Next/Previous links for page to page navigation
        wp_link_pages( array( 'before' => '<span class="wp_entry_pager_next">', 'after' => '</span>', 'next_or_number' => 'next', 'nextpagelink' => 'Next', 'previouspagelink' => 'Previous' ) );
    echo '</nav>';
}
