<?php
/**
 * Twenty Eleven Parent Theme based on Kitchen Sink HTML5 Base
 *
 * Awesomeness description
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @link        https://github.com/beingzoe/wpplugin-kitchen-sink-html5-base
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.1
 * @since       0.1
 *
 * 1) Preset a few things
 * 2) Make a theme object with Kitchen Sink HTML5 Base (we call it a kitchen)
 * 3) Load some appliances (features/plugins/classes) for your kitchen
 * 4) Create
*/

// KST Base theme settings array
// Various Kitchen Sink HTML5 Base settings for your theme
$twenty_eleven_settings = array(
    /* REQUIRED */
    'friendly_name'             => 'Twenty Eleven',         // Friendly name used by all widgets, libraries, and classes; can be different than the registered theme name
    'prefix'                    => 'kst_demo',               // Prefix for namespacing libraries, classes, widgets
    'developer'                 => 'zoe somebody',          // Friendly name of current developer; only used for admin display;
    'developer_url'             => 'http://beingzoe.com/',  // Full URI to developer website;
    /* REQUIRED for WP best practice */
    'content_width'             => 500,                     // maximum width of images in posts
    'theme_excerpt_length'      => 100,                     // Default auto excerpt length
    /* OPTIONAL */
    'theme_seo_title_sep'       => '&laquo;',               // Separator between title bar title segments
);

// Temp while making seo class
define('KST_SEO_TITLE_SEPARATOR_DEFAULT', '&laquo;');


if ( class_exists('KST') ) {

    // REGISTER YOUR THEME WITH KITCHEN SINK HTML5 BASE
    $my_theme = new KST_Kitchen_Theme($twenty_eleven_settings);

    // LOAD A PRESET CONFIGURATION - default, minimum, and_the_kitchen_sink
    // You may alternatively pass the preset value as a 2nd argument when invoking your kitchen above and delete this method call
    $my_theme->loadPreset('and_the_kitchen_sink');

    /*
    // OR GO ALA CARTE - Not everybody likes presets so load what you want
    // e.g. $my_theme->load('wp_sensible_defaults');
    */

    // An array to create a theme options page - make one array per options page you need (u)
    $twentyeleven_options = array(
        'parent_slug'           => 'kst',
        'menu_title'            => 'Layout Settings',
        'page_title'            => 'Custom Layout Settings',
        'capability'            => 'manage_options',
        'view_page_callback'    => "auto",
        'options'               => array(
                'sample_section' => array(
                                "name"      => 'Sample Options',
                                "desc"      => "<p><em>Sample options for layout and presentation</em></p>",
                                "type"      => "section",
                                "is_shut"   => FALSE
                                ),

                'TEST_SELECT' => array(
                        "name"    => __('PLUGIN Select'),
                        "desc"    => __("There are many choices awaiting"),
                        "default"     => "Select 2",
                        "type"    => "select",
                        'multi'     => TRUE,
                        'size'      => 3,
                        'form_attr' => 'style="height: auto;"',
                        "options" => array(
                                            "Select 1" => 10,
                                            "Select 2" => 20,
                                            "Select 3" => 30,
                                            "Select 4" => 40,
                                            "Select 5" => 50
                                            )
                        ),

                'sample_wp_categories' => array(
                                "name"      => 'Featured Category',
                                "desc"      => 'This doesn\'t really do anything but demonstrates a select_wp_categories option block',
                                "default"   => "Bob",
                                "type"      => "select_wp_categories",
                                "args"      => array( )
                                ),

                'sample_checkbox' => array(
                                "name"      => 'Turn something on',
                                "desc"      => "Checked by default.",
                                "default"   => TRUE,
                                "type"      => "checkbox",
                                )
                )
        );


    // CREATE ADMIN OPTIONS MENUS/PAGES - Don't forget to make an array
    $my_theme->load('options');
    $my_theme->options->add($twentyeleven_options);

    // Use some of the nifty WordPress function replacements for a big time saver (and a cleaner kitchen)
    $my_theme->wordpress->registerSidebar('Blog Sidebar', 'Sidebar content for blog articles');
    $my_theme->wordpress->registerSidebar('Pages Sidebar', 'Sidebar content for pages');
    $my_theme->wordpress->registerSidebar('Home Sidebar', 'Sidebar content for home page');
    $my_theme->wordpress->registerSidebars(3, 'Footer Area');

    // Jit Message Settings
    $jit_message_settings = array( //array(&$this, 'callbackInit')
                'content_source'    => 'posts', // posts|or_valid_callback; use 'or_valid_callback' where you will use separate logic to determine message and the site/blog owner can't choose per post/page/custom
                'trigger'           => '.wp_entry', // .wp_entry - .wp_entry_footer, #ft
                'wrapper'           => '#jit_box',
                'side'              => 'right',
                'top_or_bottom'     => 'bottom',
                'speed_in'          => 300,
                'speed_out'         => 100
            );

    $my_theme->jit_message->add($jit_message_settings);

    // Any other KST dependent code should be here to protect it in case the "KST plugin framework" is removed




} else {
    // Needs to check if it is in the admin section OR in the login page (login is not in the admin)
    if ( is_admin() ) {
        return;
    } else {
        // Having a FUN and useful help message would be cool.
    echo "<h1>Pretty cool!<br />You are using a Kitchen Sink based WordPress theme<br />HOWEVER...</h1><p>...you have not activated the KST Plugin in WordPress OR you haven't included it as library in your theme.<br />See the <a href='http://beingzoe.com/zui/wordpress/kitchen_sink_theme'>documentation</a> if you need assistance.</p><p><a href='#'>Sign in</a> to WordPress.";
        exit;
    }
}

/*
 * Now just do whatever normal WordPress themey things you would do
 * If you loaded the 'wp_sensible_defaults' appliance then you probably don't have much
 * left to do except modify style.css and tweak a few templates.
 *
 * 'wp_sensible_defaults' loads your stylesheet, adds jQuery with HTML5 flair,
 * and does all that stuff like adding 'theme_support' for all that stuff you
 * were going to turn on and include (and have to for wordpress.org theme review).
 *
 * See the documentation at https://github.com/beingzoe/wpplugin-kitchen-sink-html5-base/wiki
 * for all the groovy awesomeness that is handled for you and how to use everything but the kitchen sink
*/








/* BELOW HERE will not be in the base as is
 * but there will be some placeholder sections for this stuff with some examples maybe?
 *
 * Some of the stuff below was just to run the TwentyEleven theme
 * and will not be included at all is duly noted
*/

/* ADD AND REMOVE JUNK -
 * See documentation (online or in plugin) for things that are added
 * and removed by default as sensible defaults
*/
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');

/*
 * REGISTER THEME MENUS WITH BUILT-IN WP MENUS
 * Also set default fallback menu functions
*/
register_nav_menu('hd_menu', 'Masthead Menu'); //primary site nav
register_nav_menu('ft_menu', 'Footer Menu'); //footer nav


/*
* CLEAN UP
* Anything to error check or add to child theme features?
* Only really important if you are also developing a child theme
* or distributing your parent theme expecting people to make child themes for it
*/

/* Hook to do stuff after child themes functions.php loads */
add_action( 'after_setup_theme', 'kst_after_child_theme' ); // Delay final setup until both Parent and Child theme functions.php are loaded

/* Do that hook you gotta do */
function kst_after_child_theme() { // Fix/Undo/Check things that a child theme maybe forgot to do or did wrong */


} // END kst_theme_execute()







/* Below here may or may not be involved with the base theme as well as a number of other things above here */

/* TwentyEleven: Custom background */
add_custom_background();

/* TwentyEleven: Custom Header */
define( 'HEADER_TEXTCOLOR', '' );
define( 'HEADER_IMAGE', '%s/assets/images/headers/twentyten/path.jpg' ); // The %s is a placeholder for the theme template directory URI.
define( 'HEADER_IMAGE_WIDTH', apply_filters( 'twentyten_header_image_width', 940 ) );
define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'twentyten_header_image_height', 198 ) );
define( 'NO_HEADER_TEXT', true );
add_custom_image_header( 'kst_add_custom_image_header', 'kst_add_custom_image_header_admin' );

register_default_headers( array(
    'concave' => array(
        'url' => '%s/assets/images/headers/twentyten/concave.jpg',
        'thumbnail_url' => '%s/assets/images/headers/twentyten/concave-thumbnail.jpg',
        'description' => __( 'Concave', 'twentyten' )
    ),
    'forestfloor' => array(
        'url' => '%s/assets/images/headers/twentyten/forestfloor.jpg',
        'thumbnail_url' => '%s/assets/images/headers/twentyten/forestfloor-thumbnail.jpg',
        'description' => __( 'Forest Floor', 'twentyten' )
    ),
    'path' => array(
        'url' => '%s/assets/images/headers/twentyten/path.jpg',
        'thumbnail_url' => '%s/assets/images/headers/twentyten/path-thumbnail.jpg',
        'description' => __( 'Path', 'twentyten' )
    )
) );

/*
 * Front-end callback function for add_custom_image_header();
 * Use to insert styles or whatever
*/
function kst_add_custom_image_header() {

}

/*
 * Admin callback function for add_custom_image_header();
 * Use to insert styles or whatever; Required by WP;
*/
function kst_add_custom_image_header_admin() {

}
