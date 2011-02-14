<?php
/**
 * Development Starter Theme for Kitchen Sink HTML5 Base WordPress Plugin
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink/
 * @link        https://github.com/beingzoe/wpplugin-kitchen-sink-html5-base
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  kst-starter
 * @version     0.1
 * @since       0.1
 *
 * Checklist ;)
 * 1) Preset a few things
 * 2) Make a theme object with Kitchen Sink HTML5 Base (we call it a kitchen)
 * 3) Load some appliances (features/plugins/classes) for your kitchen
 * 4) Create
 *
 * First use:
 *      -
 *      -Search and replace "my_theme" with "your_namespace"
 *      -Open style.css and edit the theme info
 *      -Open functions.php (YOU ARE HERE)
 *          -Edit the 'my_theme_settings' array with your custom theme info
 *          -Initialize your theme/plugin (kitchen) object
 *          -Load whatever features/plugins (appliances;) you want to use in your 'kitchen'
 *
 * NOTE: The 'wp_sensible_defaults' appliance loads your stylesheet,
 * adds jQuery with HTML5 flair, and does all that stuff like adding 'theme_support'
 * for all that stuff you were going to turn on and include (and have to do for
 * wordpress.org theme review). It will seem strange at first to not see those things
 * in your functions.php file (and even longer to remember what it handles for you)
 * but we think the "base" and convenience it provides is well worth it.
 *
 * You can review the KST WordPress "sensible defaults" in the plugin folder:
 * /lib/functions/...
 *
 * The point of this starter theme is to make your own!
 * With each KST update the starter themes will undoubtedly change but we will
 * provide a detailed change log for you to keep you personal "KST base" theme
 * up-to-date.
 *
 * That said, KST is NOT a prescribed method, just a bunch of methods to enhance
 * the process of theme development. We will make sure that the plugin is as
 * backwards compatible as possible and keep all your sites as awesome as possible.
 * So please, make your base theme better than this starter and then send us a copy.
 * And most imporantly please delete all these "helpful" comments ;)
 *
 * See the documentation at https://github.com/beingzoe/wpplugin-kitchen-sink-html5-base/wiki
 * for all the groovy awesomeness that is handled for you and how to use everything but the kitchen sink
 *
 * OH, and remember to save a namespace...
 * Make sure to rename the global variables used in this file!
 * e.g. $my_theme_settings, $my_theme, etc...
 * In fact a search and replace of the whole theme for $my_theme will hook you up!
*/

// KST BASE THEME SETTINGS ARRAY
// Various Kitchen Sink HTML5 Base settings for your theme - rename the variable!
$my_theme_settings = array(
    /* REQUIRED */
    'friendly_name'             => 'My Theme',              // Friendly name used by all widgets, libraries, and classes; can be different than the registered theme name
    'prefix'                    => 'my_theme',              // Prefix for namespacing libraries, classes, widgets
    'developer'                 => 'zoe somebody',          // Friendly name of current developer; only used for admin display;
    'developer_url'             => 'http://beingzoe.com/',  // Full URI to developer website;
    /* REQUIRED for WP best practice */
    'content_width'             => 500,                     // maximum width of images in posts
    'theme_excerpt_length'      => 100,                     // Default auto excerpt length
    /* OPTIONAL */
    'theme_seo_title_sep'       => '&laquo;',               // Separator between title bar title segments
);

// NEED TO PROTECT YOUR THEME FROM KST BEING UNINSTALLED
if ( class_exists('KST') ) {

    // REGISTER YOUR THEME WITH KITCHEN SINK HTML5 BASE
    $my_theme = new KST_Kitchen_Theme($my_theme_settings);

    // LOAD A PRESET CONFIGURATION - default, default_plus, minimum, and_the_kitchen_sink
    // You may alternatively pass the preset value as a 2nd argument when invoking your kitchen above and delete this method call
    // OR GO ALA CARTE - Not everybody likes presets so load what you want
    // e.g. $my_theme->load('wp_sensible_defaults');
    // See https://github.com/beingzoe/wpplugin-kitchen-sink-html5-base/wiki/Docs_reference_appliance_all_load_shortname_preset_list
    $my_theme->loadPreset('but_the_kitchen_sink');

    // An array to create a theme options page - make one array per options page you need (u)
    $my_theme_options = array(
        'parent_slug'           => 'kst',
        'menu_title'            => 'my_theme Settings',
        'page_title'            => 'my_theme Settings',
        'capability'            => 'manage_options',
        'view_page_callback'    => "auto",
        'options'               => array( /* see wiki for syntax */ )
        );

    // CREATE ADMIN OPTIONS MENUS/PAGES - Don't forget to make an array
    //$my_theme->options->add($my_theme_options);

    // Use some of the nifty WordPress function replacements for a big time saver (and a cleaner kitchen)
    $my_theme->wordpress->registerSidebar('Blog Sidebar', 'Sidebar content for blog articles');
    $my_theme->wordpress->registerSidebar('Pages Sidebar', 'Sidebar content for pages');
    $my_theme->wordpress->registerSidebar('Home Sidebar', 'Sidebar content for home page');
    $my_theme->wordpress->registerSidebars(4, 'Footer Area');

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
        echo "<h1>Pretty cool!<br />You are using a Kitchen Sink based WordPress theme<br />HOWEVER...</h1><p>...you have not activated the Kitchen Sink HTML5 Base plugin in WordPress OR you haven't included it as library in your theme.<br />See the <a href='http://beingzoe.com/zui/wordpress/kitchen_sink/'>documentation</a> if you need assistance.</p><p><a href='#'>Sign in</a> to WordPress.";
        exit;
    }
}

/*
 * Now just do whatever normal WordPress themey things you would do
 * If you loaded the 'wp_sensible_defaults' appliance then you probably don't have much
 * left to do except modify style.css and tweak a few templates.
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


/* FUNCTIONS -
 * It is a functions.php file after all ;)
*/

