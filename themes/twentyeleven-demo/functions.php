<?php
/**
 * Twenty Eleven Parent Theme based on Kitchen Sink HTML5 Base
 *
 * Awesomeness description
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.4
 * @since       0.1
 *
 * 1) Set your settings
 * 2) Register your theme with Kitchen Sink HTML5 Base
 * 3) Load whatever KST you want/need
 * 4) Create
*/

/* SET YOUR SETTINGS */


/*
 * KST Base theme settings array
 * Various Kitchen Sink HTML5 Base settings for your theme
*/
$twenty_eleven_settings = array(
    /* REQUIRED */
    // Friendly name used by all widgets, libraries, and classes; can be different than the registered theme name
    'friendly_name'             => 'Twenty Eleven',
    // Prefix for namespacing libraries, classes, widgets
    'prefix'                    => 'ksd_0_1',
    // Friendly name of current developer; only used for admin display;
    'developer'                 => 'zoe somebody',
    // Full URI to developer website;
    'developer_url'             => 'http://beingzoe.com/',
    // As a global variable mainly used by WP but will bear in mind in KST as a constant; maximum width of images in posts
    'content_width'             => 500,
    'theme_excerpt_length'      => 100,
    /* OPTIONAL */
    // Separator between title bar title segments
    'theme_seo_title_sep'       => '&laquo;',
);

/*
 * Array for options page
*/
$theme_options = array (
        // Layout options
        array(  "name"      => __('Layout'),
                "desc"      => __("
                                <p><em>Options affecting the layout and presentation of your site.</em></p>
                                <p>See \"Appearance &gt; Theme Help\" to learn more about asides.</p>
                            "),
                "type"      => "section",
                "is_shut"   => FALSE ),

        array(  "name"      => __('Asides Category'),
                "desc"      => __('Pick the category to use as your sideblog'),
                "id"        => "layout_category_aside",
                "type"      => "select_wp_categories",
                "args"      => array( )
                ),

        array(  "name"      => __('Gallery Category SLUG'),
                "desc"      => __('Pick the category to use for gallery posts'),
                "id"        => "layout_category_gallery",
                "type"      => "select_wp_categories",
                "args"      => array( )
                )
    );
/* Sample Bonus array to test extra menus and stuff */
$theme_options2 = array (
    array(  "name"    => __('Some notes'),
            "desc"  => __("
                        <p><em>Just wanted to say hi and let you know in this demo this array appears as two different menu items.</em></p>
                        "),
            "type"  => "section",
            "is_shut"   => FALSE ),

    array(  "name"  => __('Favorite color'),
            "desc"  => __('Red? Green? Blue?'),
            "id"    => "favorite_color",
            "default"   => "",
            "type"  => "text",
            "size"  => "15"),

    array(  "name"  => __('Checkbox default on'),
            "desc"  => __('This is on by default usually'),
            "id"    => "checkbox_default_on",
            "default"   => "1",
            "type"  => "checkbox"),

    array(  "name"  => __('Checkbox default off'),
            "desc"  => __('This is on by default usually'),
            "id"    => "checkbox_default_off",
            "default"   => "0",
            "type"  => "checkbox"),

    array(  "name"    => __('TEST2'),
            "desc"  => __("

                        "),
            "type"  => "section"),

    array(  "name"  => __('TEST RADIO BUTTON'),
            "desc"  => __('What choice will you make?'),
            "id"    => "TEST_RADIO_BUTTON",
            "default"   => "this radio 3",
            "type"  => "radio",
            "options" => array(     "this radio 1",
                                    "this radio 2",
                                    "this radio 3",
                                    "this radio 4",
                                    "this radio 5"
                                        )
            ),



    array(    "name"    => __('Textarea'),
                    "desc"    => __("What you type here will indicate the possibility of success."),
                    "id"      => "textarea_id",
                    "std"     => __("You do not have to put any defaults"),
                    "type"    => "textarea",
                    "rows" => "2",
                    "cols" => "55"
                    ),

    array(    "name"    => __('Select'),
                    "desc"    => __("There are many choices awaiting"),
                    "id"      => "TEST_SELECT",
                    "default"     => "Select 4",
                    "type"    => "select",
                    "options" => array(    "Select 1",
                                        "Select 2",
                                        "Select 3",
                                        "Select 4",
                                        "Select 5"
                                        )
                    ),

    array(  "name"  => __('Asides Category'),
            "desc"  => __('Pick the category to use as your sideblog'),
            "id"    => "TEST_ASIDES_CATEGORY_SELECTOR",
            "type"  => "select_wp_categories",
            "args" => array(

                                        )
            ),

    array(  "name"  => __('Featured Page'),
            "desc"  => __('Choose the page to feature'),
            "id"    => "TEST_PAGE_SELECTOR",
            "type"  => "select_wp_pages",
            "args" => array(

                                        )
            ),

    array(    "name"    => __('MultiSelect'),
                    "desc"    => __("There are many choices awaiting and you can have them all"),
                    "id"      => "TEST_MULTISELECT",
                    "default"     => "Select 5",
                    "type"    => "select",
                    "multi"   => TRUE,
                    "size"   => "8",
                    "options" => array(    "Select 1",
                                        "Select 2",
                                        "Select 3",
                                        "Select 4",
                                        "Select 5",
                                        "Select 6",
                                        "Select 7",
                                        "Select 8"
                                        )
                    )

);


if ( class_exists('KST') ) {

    /* REGISTER YOUR THEME WITH KITCHEN SINK HTML5 BASE
     * Option #1: Create your $theme object and pass a preset configuration
     *
     * default, minimum, and_the_kitchen_sink
    */
    //$my_theme = new KST_Kitchen_Theme($twenty_eleven_settings, 'and_the_kitchen_sink');

    /* REGISTER YOUR THEME WITH KITCHEN SINK HTML5 BASE
     * Option #2: Just create your $theme object
     *
     * Then init the features you want to use individually
    */
    $my_theme = new KST_Kitchen_Theme($twenty_eleven_settings);

    /* HTML5 Boilerplate, WP normalization, and smart stuff */
    $my_theme->initSensibleDefaults();
    $my_theme->initHelp(); // KST THEME HELP
    //KST::initSEO(); // KST SEO and META DATA
    $my_theme->initContact(); // KST easy flexible email and contact forms

    /* Load and use KST_Asides class to manage asides side blog */
    require_once KST_DIR_LIB . '/KST/Asides.php'; // Class to save aside post for clean delayed output
    $loop_asides = new KST_Asides( $my_theme->getOption('layout_category_aside') );

    $my_theme->initWidgetNavPost(); // WP WIDGET: KST post to post next/previous post buttons for sidebar (only on single blog posts)
    $my_theme->initWidgetNavPosts(); // WP WIDGET: KST Page to page older/newer browse posts buttons for sidebar (only on single blog posts)

    /* OPTIONAL: WP Media Normalization */
    $my_theme->initWPMediaNormalize(); // WP Media Normalization: preset for all media normalization: Auto lightboxing, mp3player, etc...
    //$KST::initJqueryLightbox(); // WP Media Normalization: Just gallery and caption normalization with automatic lightboxing
    //$KST::initMp3Player(); // WP Media Normalization: mp3plaery shortcode and automatic player when an mp3 is directly linked

    /* OPTIONAL: Plugin-ish features */
    $my_theme->initJqueryJitMessage(); //KST/jQuery: KST JIT (Just-in-Time) message (sliding out a panel on a trigger)
    $my_theme->initWidgetJitSidebar(); // WP WIDGET: KST JIT (Just-in-Time) Sidebar (Magic relative/fixed sidebars)
    $my_theme->initJqueryToolsScrollable(); // KST/jQuery: tools: scrollable content (content slideshow with shortcodes)
    $my_theme->initJqueryCycle(); // KST/jQuery: malsup cycle content (content slideshow with shortcodes)


    /* Create Admin Options menus/pages
     * Uses your $theme_options array(s) to create admin menus/pages
    */
    $my_theme->newOptionsGroup($theme_options, 'Theme Options', 'appearance');
    $my_theme->newOptionsGroup($theme_options2, 'Theme Options 2', 'appearance');
    $my_theme->newOptionsGroup($theme_options2, 'Theme Options 3', 'appearance');

    $theme_options2[] = array(  "name"    => __('ADDED FLOOP AFTER WE ALREADY PASSED IT'),
                                "desc"  => __("
                                            <p><em>YEAH</em></p>
                                            "),
                                "type"  => "section",
                                "is_shut"   => FALSE
                            );

    // Any other KST dependent code should be here to protect it in case the plugin is removed

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
 * REGISTER WIDGETIZED SIDEBAR/AREAS
*/


    /* NOT PART OF KST - just something I like to do
     * DRY: Widget area formatting arguments
     * Sets theme-wide widgetized area formatting arguments
    */
    $kst_widget_area_format_args = array( /* Common sidebar */
                               'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                               'after_widget'  => '</aside>',
                               'before_title'  => '<h2 class="widget_title">',
                               'after_title'   => '</h2>'
                           );
    $kst_widget_area_format_args_footer = array( /* Common footer */
                                'before_widget' => '<aside id="%1$s" class="ft_widget %2$s">',
                                'after_widget'  => '</aside>',
                                'before_title'  => '<h2 class="widget_title">',
                                'after_title'   => '</h2>'
                            );



    /* Blog only primary sidebar - all posts, archives, etc... */
    register_sidebar( array_merge( array(
        'name'          => 'Blog Sidebar',
        'id'            => 'widgets_blog',
        'description'   => 'Sidebar content for blog articles',
    ), $kst_widget_area_format_args ) );
    /* Pages only primary sidebar - assuming CMS style theme */
    register_sidebar( array_merge( array(
        'name'          => 'Pages Sidebar',
        'id'            => 'widgets_pages',
        'description'   => 'Sidebar content for pages',
    ), $kst_widget_area_format_args ) );
    /* Home (frontpage) only primary sidebar */
    register_sidebar( array_merge( array(
        'name'          => 'Home Sidebar',
        'id'            => 'widgets_home',
        'description'   => 'Sidebar content for home page',
    ), $kst_widget_area_format_args ) );

    /* For the footer */
    $kst_sidebars_footer = kst_widget_create_multiple_areas( 3, 'Footer Area', 'widgets_ft', 'One of three widget areas in footer. Does not appear if no widgets added.', $kst_widget_area_format_args_footer ); // Create multiple consecutive named-numbered widget areas ("sidebars") e.g. wfooter_1, wfooter_2, wfooter_3; args = $how_many, $name, $id, $description





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
define( 'HEADER_IMAGE', '%s/_assets/images/headers/twentyten/path.jpg' ); // The %s is a placeholder for the theme template directory URI.
define( 'HEADER_IMAGE_WIDTH', apply_filters( 'twentyten_header_image_width', 940 ) );
define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'twentyten_header_image_height', 198 ) );
define( 'NO_HEADER_TEXT', true );
add_custom_image_header( 'kst_add_custom_image_header', 'kst_add_custom_image_header_admin' );

register_default_headers( array(
    'concave' => array(
        'url' => '%s/_assets/images/headers/twentyten/concave.jpg',
        'thumbnail_url' => '%s/_assets/images/headers/twentyten/concave-thumbnail.jpg',
        'description' => __( 'Concave', 'twentyten' )
    ),
    'forestfloor' => array(
        'url' => '%s/_assets/images/headers/twentyten/forestfloor.jpg',
        'thumbnail_url' => '%s/_assets/images/headers/twentyten/forestfloor-thumbnail.jpg',
        'description' => __( 'Forest Floor', 'twentyten' )
    ),
    'path' => array(
        'url' => '%s/_assets/images/headers/twentyten/path.jpg',
        'thumbnail_url' => '%s/_assets/images/headers/twentyten/path-thumbnail.jpg',
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
