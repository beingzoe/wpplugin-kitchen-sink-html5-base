<?php
/**
 * Template Name: Contact Form
 * Browse all episodes
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.4
 * @since       0.3
 */

get_header();

/**
 * Contact form shortcode
 *
 * Use [contact_form] on your contact page in the WP admin
 * to give maximum editability to the admin/content before and after the form
 *
 * Edit the heredoc content below with your contact form
 */
if ( !function_exists('kst_shortcode_contact_form') ) {
    function kst_shortcode_contact_form($atts, $content = NULL) {
        extract(shortcode_atts(array(), $atts));

        $output =
<<< EOD

<form id="contact_form" method="post" action="">
    <fieldset>
        <legend>Contact</legend>
        <dl>
            <dt><label for="contact_name">Name:</label></dt>
            <dd>
                <input name="contact_name" type="text" id="contact_name" size="30" />
            </dd>
            <dt><label for="contact_email">E-Mail:</label></dt>
            <dd>
                <input type="text" name="contact_email" id="contact_email" size="30" />
            </dd>
            <dt><label for="contact_message">Message:</label></dt>
            <dd>
                <textarea name="contact_message" id="contact_message" cols="27" rows="3"></textarea>
            </dd>
        </dl>
        <input type="submit" value="Submit" id="contact_submit" name="submit" />
    </fieldset>
</form>
EOD;

        return $output;
    }
}
add_shortcode('contact_form', 'kst_shortcode_contact_form');
?>

<section id="bd" class="clearfix hfeed" role="main">

<?php
    # Send email
    if ( isset($_POST["contact_email"]) ) {

    $the_message ="
    Name: {$_POST['contact_name']}
    Email: {$_POST['contact_email']} \n\n
    {$_POST['contact_message']}
    ";
            $message_sent_thanks = zui_send_mail(
                                    get_option('admin_email'),
                                    $_POST["contact_email"],
                                    "Website contact form",
                                    $the_message,
                                    "Thank you.<br />Your message has been sent"
                                );
            }

            if ( isset($message_sent_thanks) ) {
            ?>
                <div class="wp_entry">
                <h1 class='pagetitle'>Message sent</h1>
                <p><?php echo $message_sent_thanks; ?></p>
                <br />
                </div>
            <?php
            }

    if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

        <div class="wp_entry">
            <?php
                the_content($post->ID);
                edit_post_link( __( 'Edit', 'twentyten' ), '<span class="wp_entry_meta wp_entry_meta_edit">', '</span>' );
            ?>
        </div>

<?php endwhile; ?>

</section><!-- #bd -->

<?php
get_sidebar();
get_footer();
?>
