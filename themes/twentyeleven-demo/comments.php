<?php
/**
 * Comments partial/include template
 *
 * Current comments and the comment form.
 * Formatting/display of comments handled by a callback to kstFormatWpListComments() in functions.php
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

<section id="comments">

<?php


if ( post_password_required() ) {

    // No password then you get know comments - post is password protected
    echo "<p class='nopassword'>" . _e( 'This post is password protected. Enter the password to view any comments.', 'twentyten' ) . "</p>";
    echo "</section><!-- #comments -->"; // Has to close itself because...
    return; // We stop the rest of comments.php

} else if ( !comments_open() ) {

    // The party is over - no further discussion to be had here
    echo "<p class='nocomments'>" .  __( 'Comments are closed for this post.<br />Email us if you still have something to say.', 'twentyten' ) . "</p>";


} else if ( have_comments() ) {

    // Woohoo! We have comments and we are going to share them.
    echo "<header>";
        echo "<h3 id='wp_comments_title'>" .  sprintf( _n( 'One Response to %2$s', '%1$s Responses to %2$s', get_comments_number(), 'twentyten' ),
                                                        number_format_i18n( get_comments_number() ), '<em>' . get_the_title() . '</em>' );
        echo "</h3>";

        // Show comments navigation if paged
        include( locate_template( array( '_nav_comments.php' ) ) );
    echo "</header>";

    /**
     * Outputting comments using wp_list_comments and TWO CALLBACKS
     *
     * @see         wp_sensible_defaults.php
     * @see         kstFormatWpListComments()
     * @see         kstFormatWpListCommentsEnd()
     *
     * These callback functions only exist if you are using the ''wp_sensible_defaults' appliance
     * found in /libs/functions/wp_sensible_defaults.php
     * Create your markup 'template' for the comments by "plugging" the two functions above.
     *
     * Or make your own callbacks if you are not using the 'wp_sensible_defaults' appliance.
     * If you are not using 'wp_sensible_defaults' then you MUST create your own callbacks for wp_list_comments()
    */
    wp_list_comments('style=div&type=comment&callback=kstFormatWpListComments&end-callback=kstFormatWpListCommentsEnd'); // Using wp_sensible_defaults callbacks

    echo "<footer>";
        // Show comments navigation if paged
        include( locate_template( array( '_nav_comments.php' ) ) );
    echo "</footer>";

} else { // or, if we don't have comments:


} // End have_comments()

// Comment form arguments - Altered markup to allow for more customization - ideally <p> would be replaced with a more semantically correct element
// See comment-template.php comment_form()
$req = get_option( 'require_name_email' );
$aria_req = ( $req ? " aria-required='true'" : '' );
$comment_form_fields =  array(
	'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . ( $req ? '<span class="required">*</span>' : '' ) . '</label> ' .
	            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
	'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . ( $req ? '<span class="required">*</span>' : '' ) . '</label> ' .
	            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
	'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label>' .
	            '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
);

/**
 * Hack to fix clearing this crazy form with the floating I like to do here
*/
if (!function_exists('kst_comment_form_clearfix')) {
    function kst_comment_form_clearfix($post_ID)  {
       echo "<div class='clear clearfix'>&nbsp;</div>";
    }
}
add_action('comment_form', 'kst_comment_form_clearfix');
comment_form( array('fields' => $comment_form_fields) );
?>

</section><!-- #comments -->
