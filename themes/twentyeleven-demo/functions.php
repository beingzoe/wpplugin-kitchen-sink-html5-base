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
 * @version     0.4
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
    'prefix'                    => 'ksd_0_1',               // Prefix for namespacing libraries, classes, widgets
    'developer'                 => 'zoe somebody',          // Friendly name of current developer; only used for admin display;
    'developer_url'             => 'http://beingzoe.com/',  // Full URI to developer website;
    /* REQUIRED for WP best practice */
    'content_width'             => 500,                     // maximum width of images in posts
    'theme_excerpt_length'      => 100,                     // Default auto excerpt length
    /* OPTIONAL */
    'theme_seo_title_sep'       => '&laquo;',               // Separator between title bar title segments
);


if ( class_exists('KST') ) {

    // REGISTER YOUR THEME WITH KITCHEN SINK HTML5 BASE
    $my_theme = new KST_Kitchen_Theme($twenty_eleven_settings);

    // LOAD A PRESET CONFIGURATION - default, minimum, and_the_kitchen_sink
    // You may alternatively pass the preset value as a 2nd argument when invoking your kitchen above and delete this method call
    $my_theme->loadPreset('and_the_kitchen_sink');

    /*
    // OR GO ALA CARTE - Not everybody likes presets so load what you want
    $my_theme->load('wp_sensible_defaults');
    $my_theme->load('help');
    $my_theme->load('seo');
    $my_theme->load('wordpress');
    $my_theme->load('contact');
    $my_theme->load('widgetNavPost');
    $my_theme->load('widgetNavPosts');
    $my_theme->load('widgetJitSidebar');
    $my_theme->load('jqueryLightbox');
    $my_theme->load('mp3Player');
    $my_theme->load('jitMessage');
    $my_theme->load('jqueryCycle');
    $my_theme->load('jqueryToolsScrollable');
    */


    $help1 = array(
            array (
                'title' => 'Intro to WP1',
                'page' => 'Using WordPress',
                'section' => 'Post thumbnails',
                'path' => 'blog_posts_excerpts_and_more_teasers.php'
                ),
            array (
                'title' => 'loving WP2',
                'page' => 'Using WordPress',
                'section' => 'Post thumbnails',
                'path' => 'blog_posts_post_thumbnails.php'
                ),
        );
    $help2 = array(
            array (
                'title' => 'Intro to WP3',
                'page' => 'Using WordPress',
                'section' => 'Post thumbnails',
                'path' => 'blog_posts_multi_page_paged_content.php'
                ),
            array (
                'title' => 'loving WP4',
                'page' => 'Using WordPress',
                'section' => 'Post thumbnails',
                'path' => 'blog_posts_gallery_posts.php'
                ),
        );
    $help3 = array(
            array (
                'title' => 'Intro to WP5',
                'page' => 'Using WordPress',
                'section' => 'Post thumbnails',
                'path' => '/relative/or/not/is/the/question5'
                ),
            array (
                'title' => 'loving WP6',
                'page' => 'Using WordPress',
                'section' => 'Post thumbnails',
                'path' => '/relative/or/not/is/the/question6'
                ),
            array (
                'title' => 'Sociable',
                'page' => 'Plugins',
                'section' => 'Sociable',
                'path' => '/relative/or/not/is/the/question6'
                ),
        );

    // Add help files
    //$my_theme->help->add($help1);
    //$my_theme->help->add($help2);
    //$my_theme->help->add($help3);

    // Use some of the nifty WordPress function replacements for a big time saver (and a cleaner kitchen)
    $my_theme->wordpress->registerSidebar('Blog Sidebar', 'Sidebar content for blog articles');
    $my_theme->wordpress->registerSidebar('Pages Sidebar', 'Sidebar content for pages');
    $my_theme->wordpress->registerSidebar('Home Sidebar', 'Sidebar content for home page');
    $my_theme->wordpress->registerSidebars(3, 'Footer Area');


    // Any other KST dependent code should be here to protect it in case the "KST plugin framework" is removed




    //Our example application
    $current_theme_directory = get_stylesheet_directory();
    //echo "current theme dir=" . $current_theme_directory . "<br />" ;
    $option_group_array = array(
            'option_group_name'     => 'layout_settings',

            'parent_slug'           => 'layout_settings', //explicit existing WP menu literal page name OR an explicit custom top level menu_slug that has already been created

            'page_title'            => 'Theme Layout Settings',
            'menu_title'            => 'LAYOUT SETTINGS',
            'menu_slug'             => 'layout_settings',
            'capability'            => 'manage_options',
            'view_page_callback'    => "auto", //auto OR layout_options_output OR 'view_page_callback'    => "{$current_theme_directory}/_layout_options_form.php",
            //'icon_url'              => '?',
            //'position'              => '',

            'section_open_template' => '<div class="postbox"><div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>{section_name}</span></h3><div class="inside">',
            'section_close_template' => '</div><!--End .inside--></div><!--End .postexcerpt-->',

            'options'               => array(
                                        'sample_options_section' => array(
                                                "name"      => 'Sample Options',
                                                "desc"      => "
                                                                <p><em>Sample options affecting the layout and presentation of your site.</em></p>
                                                                <p>See \"Appearance &gt; Theme Help\" to learn more about asides.</p>
                                                            ",
                                                "type"      => "section",
                                                "is_shut"   => FALSE
                                                ),

                                        'would_default_to_bob' => array(
                                                "name"      => 'Your name',
                                                "desc"      => 'Just who you are.',
                                                "value"     => get_option('would_default_to_bob'), // send null or leave out 'value' to use default
                                                "default"   => "Bob",
                                                "type"      => "text"
                                                ),

                                        'textarea_id' => array(
                                                "name"    => 'PLUGIN Textarea',
                                                "desc"    => "What you type here will indicate the possibility of success.",
                                                "value"     => get_option('textarea_id'), // send null or leave out 'value' to use default
                                                "default"     => "You do not have to put any defaults",
                                                "type"    => "textarea",
                                                "rows" => "2",
                                                "cols" => "55"
                                                ),

                                        'sample_options_subsection' => array(
                                                "name"      => 'Some subsection',
                                                "desc"      => "
                                                                <p>Y'know an explanation goes here. This is a subsection to demarcate the rest of this section of the form</p>
                                                            ",
                                                "type"      => "subsection",
                                                "is_shut"   => FALSE
                                                ),

                                        'TEST_SELECT' => array(
                                                "name"    => __('PLUGIN Select'),
                                                "desc"    => __("There are many choices awaiting"),
                                                "value"     => get_option('TEST_SELECT'), // send null or leave out 'value' to use default
                                                "default"     => "Select 2",
                                                "type"    => "select",
                                                "options" => array(    "Select 1",
                                                                    "Select 2",
                                                                    "Select 3",
                                                                    "Select 4",
                                                                    "Select 5"
                                                                    )
                                                ),

                                        'TEST_MULTISELECT' => array(
                                                "name"    => __('PLUGIN MultiSelect'),
                                                "desc"    => __("There are many choices awaiting and you can have them all"),
                                                "value"     => get_option('TEST_MULTISELECT'), // send null or leave out 'value' to use default
                                                "default"     => "Select 5",
                                                "type"    => "select",
                                                "multi"   => TRUE,
                                                "size"   => "8",
                                                "form_attr" => 'style="min-width: 150px; height: auto;"',
                                                "options" => array(    "Select 1",
                                                                    "Select 2",
                                                                    "Select 3",
                                                                    "Select 4",
                                                                    "Select 5",
                                                                    "Select 6",
                                                                    "Select 7",
                                                                    "Select 8"
                                                                    )
                                                ),

                                        'TEST_RADIO_BUTTON' => array(
                                                "name"  => __('PLUGIN TEST RADIO BUTTON'),
                                                "desc"  => __('What choice will you make?'),
                                                "value"     => get_option('TEST_RADIO_BUTTON'), // send null or leave out 'value' to use default
                                                "default"   => "this radio 4",
                                                "type"  => "radio",
                                                "options" => array(     "this radio 1",
                                                                        "this radio 2",
                                                                        "this radio 3",
                                                                        "this radio 4",
                                                                        "this radio 5"
                                                                            )
                                                ),

                                        'some_new_category_selector' => array(
                                                "name"      => 'Featured Category',
                                                "desc"      => 'This doesn\'t really do anything but is an example option block',
                                                "type"      => "select_wp_categories",
                                                "args"      => array('selected'=>get_option('some_new_category_selector'))
                                                ),

                                        'some_new_page_selector' => array(
                                                "name"      => 'Featured Page',
                                                "desc"      => 'This doesn\'t really do anything but is an example option block',
                                                "type"      => "select_wp_pages",
                                                "args"      => array('selected'=>get_option('some_new_page_selector'))
                                                ),

                                        'checkbox_example' => array(
                                                "name"      => 'Checkbox default on',
                                                "desc"      => 'This is CHECKED by default usually and could be used somewhere in your templates',
                                                "id"        => "checkbox_example",
                                                "default"   => TRUE,
                                                "type"      => "checkbox"
                                                ),


                                        'thing_1' => array(
                                                "name"      => 'Sample Options',
                                                "desc"      => "
                                                                <p><em>Sample options affecting the layout and presentation of your site.</em></p>
                                                                <p>See \"Appearance &gt; Theme Help\" to learn more about asides.</p>
                                                            ",
                                                "type"      => "section",
                                                "is_shut"   => FALSE
                                                ),

                                        'thing_2' => array(
                                                "name"      => 'Your name',
                                                "desc"      => 'Just who you are.',
                                                "value"     => get_option('thing_2'), // send null or leave out 'value' to use default
                                                "default"   => "Bob",
                                                "type"      => "text"
                                                ),

                                        'thing_3' => array(
                                                "name"    => 'PLUGIN Textarea',
                                                "desc"    => "What you type here will indicate the possibility of success.",
                                                "value"     => get_option('thing_3'), // send null or leave out 'value' to use default
                                                "default"     => "You do not have to put any defaults",
                                                "type"    => "textarea",
                                                "rows" => "2",
                                                "cols" => "55"
                                                ),

                                        'thing_4' => array(
                                                "name"    => __('PLUGIN Select'),
                                                "desc"    => __("There are many choices awaiting"),
                                                "value"     => get_option('thing_4'), // send null or leave out 'value' to use default
                                                "default"     => "Select 2",
                                                "type"    => "select",
                                                "options" => array(    "Select 1",
                                                                    "Select 2",
                                                                    "Select 3",
                                                                    "Select 4",
                                                                    "Select 5"
                                                                    )
                                                ),

                                        'thing_5' => array(
                                                "name"    => __('PLUGIN MultiSelect'),
                                                "desc"    => __("There are many choices awaiting and you can have them all"),
                                                "value"     => get_option('thing_5'), // send null or leave out 'value' to use default
                                                "default"     => "Select 5",
                                                "type"    => "select",
                                                "multi"   => TRUE,
                                                "size"   => "8",
                                                "form_attr" => 'style="min-width: 150px; height: auto;"',
                                                "options" => array(    "Select 1",
                                                                    "Select 2",
                                                                    "Select 3",
                                                                    "Select 4",
                                                                    "Select 5",
                                                                    "Select 6",
                                                                    "Select 7",
                                                                    "Select 8"
                                                                    )
                                                ),

                                        'thing_6' => array(
                                                "name"  => __('PLUGIN TEST RADIO BUTTON'),
                                                "desc"  => __('What choice will you make?'),
                                                "value"     => get_option('thing_6'), // send null or leave out 'value' to use default
                                                "default"   => "this radio 4",
                                                "type"  => "radio",
                                                "options" => array(     "this radio 1",
                                                                        "this radio 2",
                                                                        "this radio 3",
                                                                        "this radio 4",
                                                                        "this radio 5"
                                                                            )
                                                ),

                                        'thing_7' => array(
                                                "name"      => 'Featured Category',
                                                "desc"      => 'This doesn\'t really do anything but is an example option block',
                                                "type"      => "select_wp_categories",
                                                "args"      => array('selected'=>get_option('thing_7'))
                                                ),

                                        'thing_8' => array(
                                                "name"      => 'Featured Page',
                                                "desc"      => 'This doesn\'t really do anything but is an example option block',
                                                "type"      => "select_wp_pages",
                                                "args"      => array('selected'=>get_option('thing_8'))
                                                ),

                                        'thing_9' => array(
                                                "name"      => 'Checkbox default on',
                                                "desc"      => 'This is CHECKED by default usually and could be used somewhere in your templates',
                                                "value"     => get_option('thing_9'), // send null or leave out 'value' to use default
                                                "default"   => TRUE,
                                                "type"      => "checkbox"
                                                )
                                    )
        );


    $option_group_array_2 = array(
            'option_group_name'     => 'random_extra_shit',

            'parent_slug'           => 'layout_settings', //explicit existing WP menu literal page name OR an explicit custom top level menu_slug that has already been created

            'page_title'            => 'Random Extra Settings',
            'menu_title'            => 'RANDOM',
            'menu_slug'             => 'random_extra_shit',
            'capability'            => 'manage_options',
            'view_page_callback'    => "auto", //auto OR layout_options_output OR 'view_page_callback'    => "{$current_theme_directory}/_layout_options_form.php",
            //'icon_url'              => '?',
            //'position'              => '',

            'section_open_template' => '<div class="postbox"><div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>{section_name}</span></h3><div class="inside">',
            'section_close_template' => '</div><!--End .inside--></div><!--End .postexcerpt-->',

            'options'               => array(
                                        'sample_options_section' => array(
                                                "name"      => 'Random extra shit',
                                                "desc"      => "
                                                                <p><em>This is not important but you should fill it out</em></p>
                                                            ",
                                                "type"      => "section",
                                                "is_shut"   => FALSE
                                                ),

                                        'silly_extra_setting' => array(
                                                "name"      => 'Secret code',
                                                "desc"      => 'Some random characters to randomize with',
                                                "value"     => get_option('silly_extra_setting'), // send null or leave out 'value' to use default
                                                "default"   => "Bob",
                                                "type"      => "text"
                                                )
                                        )

        );

     $callback_page_array = array(
            'option_group_name'     => 'other_settings_fake',

            'parent_slug'           => 'layout_settings', //explicit existing WP menu literal page name OR an explicit custom top level menu_slug that has already been created

            'page_title'            => 'Callback Settings',
            'menu_title'            => 'via callback',
            'menu_slug'             => 'other_settings_fake',
            'capability'            => 'manage_options',
            'view_page_callback'    => "layout_options_output", //auto OR layout_options_output OR 'view_page_callback'    => "{$current_theme_directory}/_layout_options_form.php",
            'options'               => array(
                                        'not_really'
                                        )
            //'icon_url'              => '?',
            //'position'              => '',
        );

     $file_page_array = array(
            'option_group_name'     => 'settings_from_file',

            'parent_slug'           => 'layout_settings', //explicit existing WP menu literal page name OR an explicit custom top level menu_slug that has already been created

            'page_title'            => 'My files settings form',
            'menu_title'            => 'via file',
            'menu_slug'             => 'settings_from_file',
            'capability'            => 'manage_options',
            'view_page_callback'    => "{$current_theme_directory}/_layout_options_form.php", //auto OR layout_options_output OR 'view_page_callback'    => "{$current_theme_directory}/_layout_options_form.php",
            'options'               => array(
                                        'background_color',
                                        'footer_copyright_text'
                                        )
            //'icon_url'              => '?',
            //'position'              => '',
        );

        /*
    $layout_options = new ZUI_WpAdminPages($option_group_array);
    $random_options = new ZUI_WpAdminPages($option_group_array_2);
    $callback_options = new ZUI_WpAdminPages($callback_page_array);
    $file_options = new ZUI_WpAdminPages($file_page_array);
*/

    function layout_options_output() {
    ?>

    <p>This is just a test of the call back output.</p>

    <?php
    }

    /*
     * KST will handle this stuff for uniformity:
            'section_open_template' => '<div class="postbox"><div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>{section_name}</span></h3><div class="inside">',
            'section_close_template' => '</div><!--End .inside--></div><!--End .postexcerpt-->',
            'option_group_name'     => 'layout_settings', // option_group_name is the same as the menu_slug
            'menu_slug'             => 'layout_settings', // Optional unless creating a new top level section; Defaults to underscored/lowercased menu_slug
     *
     */


    // An array to create a theme options page - make one array per options page you need (u)
    $twentyeleven_options = array(
                'parent_slug'           => 'kst', // 'kst' OR explicit existing WP menu literal page name OR an explicit custom top level menu_slug that has already been created

                'menu_title'            => 'Crazy stuff',
                //'menu_slug'             => 'theme_options', // Optional unless creating a new top level section; Defaults to underscored/lowercased menu_slug
                'page_title'            => 'Crazy awesome settings', // Optional; will use "$this->friendly_name menu_title" by default
                'capability'            => 'manage_options', // Optional; Defaults to 'manage_options' for options pages and 'edit_posts' (contributor and up) for other
                'view_page_callback'    => "auto", //auto OR layout_options_output OR 'view_page_callback'    => "{$current_theme_directory}/_layout_options_form.php",
                'options'               => array(
                                    'twentyeleven_sample_section' => array(
                                                    "name"      => 'Sample Options',
                                                    "desc"      => "
                                                                    <p><em>Sample options affecting the layout and presentation of your site.</em></p>
                                                                    <p>See \"Appearance &gt; Theme Help\" to learn more about asides.</p>
                                                                ",
                                                    "type"      => "section",
                                                    "is_shut"   => FALSE
                                                    ),

                                    'twentyeleven_layout_category_aside' => array(
                                                    "name"      => 'Featured Category',
                                                    "desc"      => 'This doesn\'t really do anything but demonstrates a select_wp_categories option block',
                                                    //"value"     => get_option('would_default_to_bob'), // send null or leave out 'value' to use default
                                                    "default"   => "Bob",
                                                    "type"      => "select_wp_categories",
                                                    "args"      => array( )
                                                    ),

                                    'twentyeleven_checkbox_example' => array(
                                                    "name"    => 'Checkbox default on',
                                                    "desc"    => "This is CHECKED by default usually",
                                                    //"value"     => get_option('textarea_id'), // send null or leave out 'value' to use default
                                                    "default"     => TRUE,
                                                    "type"    => "checkbox",
                                                    )

                                    )
        );
    $twentyeleven_options2 = array(
                'parent_slug'           => 'foo', // twentyeleven_crazy_stuff2 'kst' OR explicit existing WP menu literal page name OR an explicit custom top level menu_slug that has already been created
                'menu_title'            => 'Crazy stuff2',
                'page_title'            => 'Crazy awesome settings2', // Optional; will use "$this->friendly_name menu_title" by default
                'capability'            => 'manage_options', // Optional; Defaults to 'manage_options' for options pages and 'edit_posts' (contributor and up) for other
                'view_page_callback'    => "auto", //auto OR layout_options_output OR 'view_page_callback'    => "{$current_theme_directory}/_layout_options_form.php",
                'options'               => array(
                                    'twentyeleven_sample_options_section2' => array(
                                                    "name"      => 'Sample Options',
                                                    "desc"      => "
                                                                    <p><em>Sample options affecting the layout and presentation of your site.</em></p>
                                                                    <p>See \"Appearance &gt; Theme Help\" to learn more about asides.</p>
                                                                ",
                                                    "type"      => "section",
                                                    "is_shut"   => FALSE
                                                    ),

                                    'twentyeleven_layout_category_aside2' => array(
                                                    "name"      => 'Featured Category',
                                                    "desc"      => 'This doesn\'t really do anything but demonstrates a select_wp_categories option block',
                                                    //"value"     => get_option('would_default_to_bob'), // send null or leave out 'value' to use default
                                                    "default"   => "Bob",
                                                    "type"      => "select_wp_categories",
                                                    "args"      => array( )
                                                    ),

                                    'twentyeleven_checkbox_example2' => array(
                                                    "name"    => 'Checkbox default on',
                                                    "desc"    => "This is CHECKED by default usually",
                                                    //"value"     => get_option('textarea_id'), // send null or leave out 'value' to use default
                                                    "default"     => TRUE,
                                                    "type"    => "checkbox",
                                                    )

                                    )
        );

    // CREATE ADMIN OPTIONS MENUS/PAGES - Don't forget to make an array
    $my_theme->load('options');
    $my_theme->options->addGroup($twentyeleven_options);
    $my_theme->options->addGroup($twentyeleven_options2);

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
