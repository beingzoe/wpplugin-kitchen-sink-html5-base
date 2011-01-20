<?php
/**
 * Comments partial/include template
 *
 * Current comments and the comment form.
 * Formatting/display of comments handled by a callback to kst_format_wp_list_comments() in functions.php
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  Theme
 * @version     0.4
 * @since       1.0
 */
?>

<section id="#comments">

<?php 
if ( post_password_required() ) {
    echo '<section id="comments"><p class="nopassword">' . _e( 'This post is password protected. Enter the password to view any comments.', 'twentyten' ) . '</p></section><!-- #comments -->';
    return; // Stop the rest of comments.php
};

 if ( have_comments() ) : ?>
			<h3 id="comments-title"><?php
			printf( _n( 'One Response to %2$s', '%1$s Responses to %2$s', get_comments_number(), 'twentyten' ),
			number_format_i18n( get_comments_number() ), '<em>' . get_the_title() . '</em>' );
			?></h3>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'twentyten' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
			</div> <!-- .navigation -->
<?php endif; /* check for comment navigation */ ?>

			<ol class="commentlist">
				<?php
					/**
                      * wp_list_comments is dumb
                      * There is no reason for this kind of rigamorale to customize html
                      */
					wp_list_comments('style=ol&type=comment&callback=kst_format_wp_list_comments'); // See functions.php to edit
				?>
			</ol>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'twentyten' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
			</div><!-- .navigation -->
<?php endif; // check for comment navigation ?>

<?php else : // or, if we don't have comments:

	/* If there are no comments and comments are closed,
	 * let's leave a little note, shall we?
	 */
	if ( ! comments_open() ) :
?>
	<p class="nocomments"><?php _e( 'Comments are closed.', 'twentyten' ); ?></p>
<?php endif; /* end ! comments_open() */ ?>

<?php endif; /* end have_comments() */ ?>

<?php
/*. $aria_req*/
$fields =  array(
	'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . ( $req ? '<span class="required">*</span>' : '' ) . '</label> ' .
	            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"'  . ' /></p>',
	'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . ( $req ? '<span class="required">*</span>' : '' ) . '</label> ' .
	            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . ' /></p>',
	'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label>' .
	            '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
); ?>


<?php 
    function comment_form_fix($post_ID)  {
       echo "<div class='clear clearfix'>&nbsp;</div>";
    }
    add_action('comment_form', 'comment_form_fix');
    $comment_form_args = array('fields' => $fields);
    comment_form( $comment_form_args ); 
?>

</section><!-- #comments -->
