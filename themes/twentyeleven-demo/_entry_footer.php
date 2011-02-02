<?php
/**
 * Display post "entry" footer meta
 * DRY include/partial
 *
 * Typically displays a share link, categories, tags, and an edit link for admins
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
<footer>
<?php
    // Author bio
    if ( ( is_single() || is_page() ) && get_the_author_meta( 'description' ) ) { // Author has a bio
?>
        <div id="wp_entry_author">
            <div id="wp_entry_author_avatar">
                <?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'kst_author_bio_avatar_size', 60 ) ); ?>
            </div>
            <div id="wp_entry_author_info">
                <h2><?php printf( esc_attr__( 'About %s', 'twentyten' ), get_the_author() ); ?></h2>
                <?php the_author_meta( 'description' ); ?>
            </div>
            <div id="wp_entry_author_links">
                <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
                    <?php printf( __( '%s\'s posts', 'twentyten' ), get_the_author() ); ?>
                </a>
                |
                <a href="<?php the_author_meta( 'url' ); ?>">
                    <?php printf( __( '%s\'s website', 'twentyten' ), get_the_author() ); ?>
                </a>
            </div>
        </div><!-- #wp_entry_author -->
<?php
    } // get_the_author_meta
?>
    <div class="wp_entry_footer">
<?php
        // Sociable - if installed
        if (function_exists('sociable_html')) {
            echo sociable_html();
        }
        if ( count( get_the_category() ) ) {
?>
            <span class="wp_entry_meta wp_entry_meta_category">
                <?php printf( __( '<span class="%1$s">Categories:</span> %2$s', 'twentyten' ), '', get_the_category_list( ', ' ) ); ?>
            </span>
            |
<?php
        }

        // Tags
        $tags_list = get_the_tag_list( '', ', ' );
        if ( $tags_list ) {
?>
            <span class="wp_entry_meta wp_entry_meta_tags">
                <?php printf( __( '<span class="%1$s">Tags:</span> %2$s', 'twentyten' ), '', $tags_list ); ?>
            </span>
            |
<?php
        }

        // Comments link
        if ( !is_single() ) {
?>
            <span class="wp_entry_meta wp_entry_meta_comments"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
            |
<?php
        }
?>
<?php
        // Edit link
        edit_post_link( __( 'Edit', 'twentyten' ), '<span class="wp_entry_meta wp_entry_meta_edit">', '</span> |' );
?>
    </div>
</footer><!-- .wp_entry_footer -->
