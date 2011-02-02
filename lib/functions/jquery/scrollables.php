<?php
/**
 * Scrollable slider box from shortcodes using jquery.tools
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  Plugins:Media
 * @version     0.1
 * @todo        convert to class
 * @todo        just refigure the whole damn thing? Possibly choose between cycle and scrollables?
 * @todo        navi active class and click seek gets messed up if multiple instances and one does not have a nav
 * @todo        get rid of all the clumsy and duplicated bullshit
 * @todo        figure out a way to only include the css if it is needed
 *
 * jquery.tools.scrollable examples and documentation at: http://flowplayer.org/tools/scrollable/
 * WordPress kst_jquery_scrollables library implementation documentation http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 *
 * N.B.: You can only have one scrollable per post/page with the shortcodes
 *       If you need more you will have to place the scrollable code manually
 *       either in html mode or hardcoded in a template.
 *
 *
 * USAGE: (manually invoke)(must be manual if you need more than one per post/page)
 *
 * Call print_scrollable_scripts() found in this library to safely load stylesheet and javascript
 * Then just include your scrollables markup the old fashioned way
 *
 *
 * USAGE: (from wp-admin in posts/pages via shortcodes)
 *
 * The shortcodes may be used in any order.
 * The minimum setup is at least one [scrollable_slide /]
 *
 * ADD SLIDE(s)
 * [scrollable_slide]any valid markup use absolute paths to be safe[/scrollable_slide]
 *
 * ADD CUSTOM CLASS for scrollables container
 * [scrollable_class]my_custom_class[/scrollable_class]
 *
 * ADD SCROLLABLE HEADER LAYOUT/CONTENT
 * [scrollable_header]Header content with markup okay[/scrollable_header]
 *
 * ADD SCROLLABLE FOOTER LAYOUT/CONTENT
 * [scrollable_header]Header content with markup okay[/scrollable_header]
 *
 * ADD PAGER NAV BAR (no content, just a flag)
 * [scrollable_nav]
 *
 *
 * INTERNALS
 *
 * When any of the shortcodes are invoked, a variable variable is created
 * this_set = scrollable_data_{$post->ID} which populates an array with
 * scrollable data for that post/page. Using shortcodes only one
 * scrollable slider may be created per post/page.
 *
 * this_set = array(
                    [class]     => 'scrollables',
                    [header]    => NULL; string;
                    slides      => array( 'content', 'content', 'content')
                    [nav]       => NULL;
                    [footer]    => NULL; string;
                )
 *
 */

/**
 * Initialize scrollable
 */
/* Register tools so it may be invoked manually or by shortcodes */
//N.B.:shortcodes usage inject stylesheet with javascript since we don't know if it is needed until it is too late it enqueue it

//ACTUALY N.B.THIS: the injected stylesheet doesn't work correctly so we are just loading the styles all the time for now
//wp_register_style('scrollables', get_stylesheet_directory_uri() . '/assets/stylesheets/scrollables.css'); //
//wp_enqueue_style('scrollables');
//ACTUALLY ACTUALLY since we are loading it all the time anyway I just put it in the main stylesheet

/* Register shortcodes */
add_shortcode('scrollable_class', 'kst_shortcode_scrollable_class'); //Add shortcode handler
add_shortcode('scrollable_header', 'kst_shortcode_scrollable_header'); //Add shortcode handler
add_shortcode('scrollable_footer', 'kst_shortcode_scrollable_footer'); //Add shortcode handler
add_shortcode('scrollable_slide', 'kst_shortcode_scrollable_slide'); //Add shortcode handler
add_shortcode('scrollable_nav', 'kst_shortcode_scrollable_nav'); //Add shortcode handler
/* Add filter to replace our shortcode placeholder with the scrollable output */
add_filter('the_content', 'kst_shortcode_scrollables_output', 11);

/**
 * Shortcode handler: scrollable_class
 *
 * Add custom class for scrollables container wrapper
 *
 * @since       0.1
 * @global      object $post WP current post data being output
 * @param       required array $atts
 * @param       required string $content
 * @uses        add_action() WP function
 * @return      string
 *
 * This also has to globalize it's own variables until it is converted to a class
 */
function kst_shortcode_scrollable_class($atts, $content = null) {

    if ( !$content )
        return false; //nothing to do

    global $post;

    $this_set = "scrollable_data_{$post->ID}"; //set this dynamic variable to stay sane; ${"$this_set"}

    global ${"$this_set"};

    if ( !isset(${"$this_set"}) ) {
        ${"$this_set"}["class"][] = $content;
        add_action('wp_footer', 'print_scrollable_scripts');
        return "scrollable_placeholder";
    }

    ${"$this_set"}["class"][] = $content;
}

/**
 * Shortcode handler: scrollable_header
 *
 * Add header for scrollable
 *
 * @since       0.1
 * @global      object $post WP current post data being output
 * @param       required array $atts
 * @param       required string $content
 * @uses        add_action() WP function
 * @return      string
 *
 * This also has to globalize it's own variables until it is converted to a class
 */
function kst_shortcode_scrollable_header($atts, $content = null) {

    if ( !$content )
        return false; //nothing to do

    global $post;

    $this_set = "scrollable_data_{$post->ID}"; //set this dynamic variable to stay sane; ${"$this_set"}

    global ${"$this_set"};

    if ( !isset(${"$this_set"}) ) {
        ${"$this_set"}["header"][] = $content;
        add_action('wp_footer', 'print_scrollable_scripts');
        return "scrollable_placeholder";
    }

    ${"$this_set"}["header"][] = $content;
}

/**
 * Shortcode handler: scrollable_footer
 *
 * Add footer for scrollable
 *
 * @since       0.1
 * @global      object $post WP current post data being output
 * @param       required array $atts
 * @param       required string $content
 * @uses        add_action() WP function
 * @return      string
 *
 * This also has to globalize it's own variables until it is converted to a class
 */
function kst_shortcode_scrollable_footer($atts, $content = null) {

    if ( !$content )
        return false; //nothing to do

    global $post;

    $this_set = "scrollable_data_{$post->ID}"; //set this dynamic variable to stay sane; ${"$this_set"}

    global ${"$this_set"};

    if ( !isset(${"$this_set"}) ) {
        ${"$this_set"}["footer"][] = $content;
        add_action('wp_footer', 'print_scrollable_scripts');
        return "scrollable_placeholder";
    }

    ${"$this_set"}["footer"][] = $content;
}

/**
 * Shortcode handler: scrollable_nav
 *
 * Add nav elements for scrollable
 *
 * @since       0.1
 * @global      object $post WP current post data being output
 * @param       required array $atts
 * @param       required string $content
 * @uses        add_action() WP function
 * @return      string
 *
 * This also has to globalize it's own variables until it is converted to a class
 */
function kst_shortcode_scrollable_nav($atts, $content = null) {

    global $post;

    $this_set = "scrollable_data_{$post->ID}"; //set this dynamic variable to stay sane; ${"$this_set"}

    global ${"$this_set"};

    if ( !isset(${"$this_set"}) ) {
        ${"$this_set"}["nav"][] = true;
        add_action('wp_footer', 'print_scrollable_scripts');
        return "scrollable_placeholder";
    }

    ${"$this_set"}["nav"][] = true;
}

/**
 * Shortcode handler: scrollable_slide
 *
 * Add content to current set of scrollable slides
 *
 * @since       0.1
 * @global      object $post WP current post data being output
 * @param       required array $atts
 * @param       required string $content
 * @uses        add_action() WP function
 * @return      string
 *
 * This also has to globalize it's own variables until it is converted to a class
 */
function kst_shortcode_scrollable_slide($atts, $content = null) {
    /*
    extract(shortcode_atts(array(
        'class'     => 'scrollable_default'
    ), $atts));
    */
    if ( !$content )
        return false; //nothing to do

    global $post;

    $this_set = "scrollable_data_{$post->ID}"; //set this dynamic variable to stay sane; ${"$this_set"}

    global ${"$this_set"};

    if ( !isset(${"$this_set"}) ) {
        //${"$this_set"} = array(); //array to store the content as it comes and trigger the output if exists
        //${"$this_set"}["class"][] = $class;
        ${"$this_set"}["slides"][] = $content; //add the content to the end of the array
        add_action('wp_footer', 'print_scrollable_scripts');
        return "scrollable_placeholder";
    }

    ${"$this_set"}["slides"][]  = $content; //add the content to the end of the array

    /* Repeat for each slide which will be output hooking onto "wp" */
}


/**
 * Output the scrollable with content
 *
 *
 * @since       0.1
 * @global      object $post WP current post data being output
 * @param       required string $content
 * @return      string
 *
 * This also has to globalize it's own variables until it is converted to a class
 */
function kst_shortcode_scrollables_output($content) {

    global $post;

    $this_set = "scrollable_data_{$post->ID}"; //set this dynamic variable to stay sane

    global ${"$this_set"};

    /* make sure we have a set and it contains at least one slide */
    if ( !${"$this_set"} || !isset( ${"$this_set"}["slides"] ) ) {
        $content = str_replace( 'scrollable_placeholder' , '' , $content ); //no slides so cleanup the placeholder
        return $content;
    }

    /* Use their custom class if it exists */
    isset( ${"$this_set"}["class"] )
        ? $class = ${"$this_set"}["class"][0]
        : $class = 'scrollable_default';

    /* Multiple instances with and without a nav cannot be called with navigator */
    isset( ${"$this_set"}["nav"] )
        ? $with_nav = 'with_nav'
        : $with_nav = '';

    /* Start the scrollables wrapper container */
    $ha =
<<< EOD
<div id="{$class}_{$post->ID}" class="scrollables {$with_nav} {$class}">
EOD;

    /* If we have a header then include it */
    if ( isset( ${"$this_set"}["header"] ) ) {
    $ha .=
<<< EOD
    <div class="scrollables_header">{${"$this_set"}["header"][0]}</div>
EOD;
    }

    /* scrollable_scrollables scroll_items scroll_item */
    $ha .=
<<< EOD
    <div class="scrollables_scrollable">
        <div class="scroll_items">
EOD;

    /* Output each slide */
    foreach (${"$this_set"}["slides"] as $slide) {
        $ha .= "<div class='scroll_item'>{$slide}</div>";
    }

    /* Close .scroll_items and .scrollables_scrollable */
    $ha .=
<<< EOD
        </div>
    </div>
EOD;

    /* If a pager nav was requested  */
    if ( isset( ${"$this_set"}["nav"] ) ) {
    $ha .=
<<< EOD
    <div class="scrollables_nav"></div>
EOD;
    }

    /* next/previous containers must exist (hide via css if necessary */
    $ha .=
<<< EOD
    <div class="scrollables_next" title="Scroll next">&raquo;</div>
    <div class="scrollables_previous" title="Scroll previous">&laquo;</div>
EOD;


    /* If we have a footer then include it */
     if ( isset( ${"$this_set"}["footer"] ) ) {
    $ha .=
<<< EOD
    <div class="scrollables_footer">{${"$this_set"}["footer"][0]}</div>
EOD;
    }

    /* close the scrollables wrapper container */
    $ha .= '</div>';

    /* replace our placeholder string with the final output */
    $content = str_replace( 'scrollable_placeholder' , $ha , $content );

    /* Ah, done */
    return $content;
}

/**
 * Print styles and scripts only if we need them
 *
 * We must register and print them ourselves because we can't enqueue by the time shortcodes are executing
 * Use add_action('wp_footer', 'print_scrollable_scripts'); to safely load stylesheet and javascript for manual scrollables usage
 *
 * @since       0.1
 * @uses        wp_register_script() WP function
 * @uses        wp_print_scripts() WP function
 */
function print_scrollable_scripts() {

        /* Load jQuery tools plugin and external stylesheet */
        /*
        stylesheet cannot be enqueued because we missed the call and not printed because <link> not permitted outside head
        this is a big clusterfuck of stupidity
        Without creating my own custom wp_head type call I cannot load the css only when it is needed
        If I load the stylesheet when it is needed by javascript (see below) it isn't loaded by the browser in time
        to show the right slide to start (starts on the last slide). So for the time being I am just loading the css
        all the time and/or considering putting it back into style.css so that way at least it is less trips to the server

            jQuery(document).ready(function($) { // noconflict wrapper to use shorthand $() for jQuery() inside of this function
                $('head').prepend($('<link>').attr({
                    rel: 'stylesheet',
                    type: 'text/css',
                    media: 'screen',
                    href: '<?php echo KST_URI_ASSETS . '/stylesheets/scrollables.css' ?>'
                }));
            });
        */

        //wp_register_script('jquery-tools', KST_URI_ASSETS . '/javascripts/jquery/jquery.tools.min.js' , array('jquery') , '1.2.3', true);
        //http://cdn.jquerytools.org/1.2.3/all/jquery.tools.min.js
        //http://cdn.jquerytools.org/1.2.5/all/jquery.tools.min.js
        //http://cdn.jquerytools.org/1.2.5/tiny/jquery.tools.min.js
        wp_register_script('jquery-tools', 'http://cdn.jquerytools.org/1.2.5/all/jquery.tools.min.js' , array('jquery','application') , '1.2.3', true);
        /* just print the script directly to the page with wp_footer */
        wp_print_scripts('jquery-tools');
}



// Add Help
$kst_core_help_array = array (
        array (
            'page' => 'Features',
            'section' => 'Content Slideshows',
            'title' => 'Appliance: Media: Tools Scrollable',
            'content_source' => 'kstHelpApplianceMediaSlideShow_scrollable'
        )
    );


// Load Help
// Needs to be converted to an appliance!


/**
 * KST_Appliance_Help entry
 * Features: Appliance: Media: Slideshow Cycle
 *
 * @since       0.1
*/
function kstHelpApplianceMediaSlideShow_scrollable() {
    ?>
        <h4>USAGE: (via shortcodes)</h4>

        <p>
        The shortcodes may be used in any order.<br />
        The minimum setup is at least one [scrollable_slide /]
        </p>

        <p>
        <strong>ADD SLIDE(s) (required)</strong><br />
        [scrollable_slide]any valid markup use absolute paths to be safe[/scrollable_slide]
        </p>

        <p>
        (optional) <strong>ADD CUSTOM CLASS</strong> for scrollables container <br />
        [scrollable_class]my_custom_class[/scrollable_class]
        </p>

        <p>
        (optional) <strong>ADD SCROLLABLE HEADER LAYOUT/CONTENT</strong> <br />
        [scrollable_header]Header content with markup okay[/scrollable_header]
        </p>

        <p>
        (optional) <strong>ADD SCROLLABLE FOOTER LAYOUT/CONTENT</strong> <br />
        [scrollable_header]Header content with markup okay[/scrollable_header]
        </p>

        <p>
        (optional) <strong>ADD PAGER NAV BAR</strong> (no content, just a flag) <br />
        [scrollable_nav]
        </p>

        <p>
            <strong>Developer note:</strong> This is handled via a KST library in the _application directory, invoked in functions.php, and called from _assets/javascript/script.js
        </p>
    <?php
}
