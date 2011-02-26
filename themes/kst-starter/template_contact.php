<?php
/**
 * Template Name: Contact Form
 * Browse all episodes
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  kst-starter
 * @version     0.1
 * @since       0.1
*/

get_header();

$contact_form = <<< EOD
        <form action="" method="post" id="form_id">
            <dl class="clearfix">
            <dt><label for="contact_name">Your Name</label></dt>
            <dd><input type="text" name="contact_name" id="contact_name" value="{contact_name}" size="30" /></dd>
            <dt><label for="contact_email">Email Address</label></dt>
            <dd><input type="text" name="contact_email" id="contact_email" value="{contact_email}" size="30" /></dd>
            <dt><label for="contact_phone_number">Phone Number</label></dt>
            <dd><input type="text" name="contact_phone_number" id="contact_phone_number" value="{contact_phone_number}" size="30" /></dd>
            <dt><label for="contact_company_name">Company Name</label></dt>
            <dd><input type="text" name="contact_company_name" id="contact_company_name" value="{contact_company_name}" size="30" /></dd>
            <dt><label for="contact_message">Message</label></dt>
            <dd><textarea name="contact_message" id="contact_message" rows="10" cols="50">{contact_message}</textarea></dd>
            <dt>&nbsp;</dt>
            <dd><input type="submit" name="submit" value="Send" class="awesome" /></dd>
            </dl>
        </form>
EOD;

    $text_template = <<< EOD
New message from your contact form at yourlendersolutions.com

Company Name: {contact_company_name}
Name: {contact_name}
Email: {contact_email}
Phone Number: {contact_phone_number}

{some tag}

{contact_message}

EOD;

$form_array = array(
        'form_id'                   => 'contact_form', // unique identifier of form for ajax or to set one time values
        'form_template'             => $contact_form, // 'auto','<form>...</form>'|valid_callback|'form_id' - The actual form to be display/processed/sent/returned - if not sent then they will handle that themselves - if they did we make a shortcode for it as well as a varialbe they can retrieve from the ojbect
        //'redirect_to'               => 'http://beingzoedev/zui/wordpress/3_0/test/', // no query strings

        'to'                        => $GLOBALS['my_theme']->options->get('contact_primary_to_address'),
        'reply_to'                  => $GLOBALS['my_theme']->options->get('contact_primary_to_address'),
        'from'                      => $GLOBALS['my_theme']->options->get('contact_primary_to_address'),
        'subject'                   => 'New message from your website',

        'success_template'          => '<p>Thank you {contact_name}. Your message was sent .<br />We will get back to you shortly</p>',
        'failure_template'          => '<p>We could not send your form for some reason {contact_name}.</p>',
        'revalidate_template'       => '<p>Please check the form and try again {contact_name}</p>{some tag}',
        'text_template'             => $text_template,
        'html_template'             => 'This would be the email html template to parse',
        'tags_values'                => array(
                'some tag' => '<p>Some output</p>'
            ) // Array of custom tags to use in templates with their corresponding value

    );


    // in local template:
    // We add our current form to the object - but some of it could already have been set as defaults
    $GLOBALS['my_theme']->forms->add($form_array);
    // Then we wait for the shortcode or direct request to output the form (unless they just sent a 'form_id' then they will do the rest (process() etc...))
?>

<section id="bd" class="clearfix hfeed" role="main">

<?php
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
