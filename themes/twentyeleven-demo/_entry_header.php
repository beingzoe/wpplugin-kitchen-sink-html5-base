<?php
/**
 * Display post "entry" header/masthead
 * DRY include/partial
 *
 * Typically displays the title of the post and various meta info such as author, date, comments
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
<header class="wp_entry_header clearfix">
<?php

    /* title */
    if ( is_single() )
        echo '<h1>' . get_the_title() . "</h1> \n";
    else
        echo "<h2>\n<a href=\""
                . get_permalink()
                . '" title="'
                . sprintf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) )
                . '" rel="bookmark">'
                . the_title( '', '', 0 )
                . "</a>\n</h2>";

    /* Author by line */
    printf( '<span class="wp_entry_meta wp_entry_meta_author vcard">By %1$s</span>',
        sprintf( '<a class="url fn n" href="%1$s" title="%2$s">%3$s</a>',
            get_author_posts_url( get_the_author_meta( 'ID' ) ),
            sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ),
            get_the_author()
        )
    );

    /* Date */
    printf( '<time datetime="%1$s" class="wp_entry_meta wp_entry_meta_date"><abbr class="published" title="%1$s">%2$s</abbr></time>',
        esc_attr( get_the_time("Y-m-d\TH:i:s\Z") ),
        get_the_date()
    );

    echo "\n";
?>
</header><!-- .wp_entry_header -->
