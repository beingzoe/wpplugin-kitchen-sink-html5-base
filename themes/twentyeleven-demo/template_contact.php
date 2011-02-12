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
 * @version     0.1
 * @since       0.1
*/

get_header();

$contact_form = <<< EOD
    <form action="" method="post" id="form_id">
        <label>Name</label>
        <input type="text" name="contact_name" id="contact_name" value="{contact_name}" />
        <br />
        <label>Email</label>
        <input type="text" name="contact_email" id="contact_email" value="{contact_email}" />
        <input type="submit" name="submit" value="Send" />
    </form>
EOD;

$text_template = <<< EOD
New email from {contact_name}

Name: {contact_name}
Email: {contact_email}

EOD;

$form_array = array(
        'form_id'                   => 'contact_form', // unique identifier of form for ajax or to set one time values
        'form_template'             => $contact_form, // 'auto','<form>...</form>'|valid_callback|'form_id' - The actual form to be display/processed/sent/returned - if not sent then they will handle that themselves - if they did we make a shortcode for it as well as a varialbe they can retrieve from the ojbect
        //'redirect_to'               => 'http://beingzoedev/zui/wordpress/3_0/test/', // no query strings

        'to'                        => 'me@beingzoe.com',
        'reply_to'                  => 'me@beingzoe.com',
        'cc'                        => '',
        'bcc'                       => '',
        'from'                      => 'me@beingzoe.com',
        'subject'                   => 'Thank you for contacting us',

        'success_template'          => '<p>Your form was sent {contact_name}.</p><p>The message sent was:</p><div style="margin-top: 36px; border: 1px solid gray;"><pre>{message}</pre></div>',
        'failure_template'          => '<p>We could not send your form for some reason {contact_name}.</p>',
        'revalidate_template'       => '<p>Please check the form and try again {contact_name}</p>',
        'text_template'             => $text_template,
        'html_template'             => 'This would be the email html template to parse',
        'tags_values'                => array(
                'Sale Announcment' => '<p class="big_sale_email_announcement">Everyone on the mailing list recieves 50% off</p>'
            ) // Array of custom tags to use in templates with their corresponding value

    );

                // Page loads - we load 'forms' - could be done in functions.php along with defaults
                //$my_theme->load('forms');
                $GLOBALS['my_theme']->load('forms');
                // Maybe we set defaults
                //$GLOBALS['my_theme']->forms->setDefaults($defaults_array);

                // Other methods to deal with
                //$GLOBALS['my_theme']->forms->process();
                //$GLOBALS['my_theme']->forms->validate();
                //$GLOBALS['my_theme']->forms->send();

// in local template:
// We add our current form to the object - but some of it could already have been set as defaults
$GLOBALS['my_theme']->forms->add($form_array);
// Then we wait for the shortcode or direct request to output the form (unless they just sent a 'form_id' then they will do the rest (process() etc...))

// template html and then...content containing [shortcode] OR ...
//$GLOBALS['my_theme']->forms->output('contact_form'); // returns modified form with nonce and what not

/*
Other methods:
getFieldValue();
setFieldValue();
jsonEncode();

escapeHtml();

process();

$GLOBALS['my_theme']->contact->newForm(); // Send the markup for your form and the send settings
*/








/**
 * Contact form shortcode
 *
 * Use [contact_form] on your contact page in the WP admin
 * to give maximum editability to the admin/content before and after the form
 *
 * Edit the heredoc content below with your contact form
*/
/*
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
*/
?>

<section id="bd" class="clearfix hfeed" role="main">

<?php
/*
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

    if ( have_posts() ) while ( have_posts() ) : the_post();
    */
    if ( have_posts() ) while ( have_posts() ) : the_post();
?>
        <article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="wp_entry clearfix">
                <?php
                    // h1 is generally handled in the Page content (unlike posts)
                    the_content($post->ID);
                ?>
            </div>

            <?php
                include( locate_template( array( '_wp_link_pages.php' ) ) ); // get_template_part('_wp_link_pages.php'); Paged articles <!--next-->
                edit_post_link( __( 'Edit', 'twentyten' ), '<span class="wp_entry_meta wp_entry_meta_edit">', '</span>' );
            ?>
        </article><!-- .page -->

    <?php endwhile; ?>

</section><!-- #bd -->

<?php
get_sidebar();
get_footer();
?>
