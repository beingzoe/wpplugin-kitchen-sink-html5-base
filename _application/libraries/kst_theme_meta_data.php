<?php
/**
 * META and SEO functions
 * These are basic functions that should work for most projects.
 * Otherwise don't include the library and use a plugin.
 * No changes need made to templates if this library is not used.
 * 
 * This library is dependent on the kst_theme_options library.
 * 
 * kst_meta_description() and kst_meta_keywords() echo the entire meta tag 
 * as part of the wp_head().
 * 
 * kst_filter_wp_title() filters the wp_title call
 * 
 * kst_filter_body_class looks for a custom field of meta_body_class to allow for
 * flexible styling of layouts or sections
 * 
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     WordPress
 * @subpackage  KitchenSinkThemeLibrary
 * @version     0.3
 * @since       0.1
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @uses        KST_Options class
 * @uses        $
 * @todo        convert to class
 * @todo        protect merging our options with the theme options if $theme_options is not an array
 * @todo        implement user friendly always present post custom fields form like this great tutorial http://sltaylor.co.uk/blog/control-your-own-wordpress-custom-fields/
 * @todo        have this create it's own options page like a plugin to eliminate the dependency on kst_theme_options.php
 * @todo        attempt to build keyword list from post/page tags
 */

/* Requires kst_options; Don't do anything if the options don't exist */
if ( !class_exists('KST_Options') ) return;

/**
 * Instantiate WPAlchemy_MetaBox class
 * Replaces get_post_meta
 *
 * @since       0.3 
 * @uses        WPAlchemy_MetaBox
 */
global $theme_options, $kst_mb_meta_data, $kst_options;
require_once WP_PLUGIN_DIR . '/kitchen-sink-html5-base/_application/classes/WPAlchemy_MetaBox.php'; // WP admin meta boxes
$kst_mb_meta_data = new WPAlchemy_MetaBox( array (
    'id' => '_kst_wp_meta_data',
    'title' => 'SEO &amp; Meta Data',
    'template' => TEMPLATEPATH . '/_application/meta_boxes/kst_theme_meta_data.php',
    'context' => 'normal',
    'priority' => 'high',
));


/**
 * Create the necessary theme options
 * 
 * @since 0.1
 */

    if ( $kst_options->get_option( 'meta_title_sep' ) )
        $meta_title_sep = $kst_options->get_option( 'meta_title_sep' );
    else
        $meta_title_sep = $kst_meta_title_sep_default; //@todo actually create/update the value if form field is blank on save
        
    $options_page = THEME_HELP_URL;
    $meta_theme_options = array (
                                /* Page title settings */
                                array(  "name"    => __('SEO'),
                                        "desc"    => __("
                                                        <p>
                                                            Your theme has basic control over page meta data built-in for SEO purposes.<br />
                                                            Set default title settings and default description/keywords below for general site control.
                                                        </p>
                                                        <p>
                                                        See <a href=\"{$options_page}#seo_post_page_custom_fields\">Post/Page SEO & Meta Data</a>  
                                                            for specifics on how to further customize your SEO for ANY individual post/page.
                                                        </p>    
                                                        <h4>SEO: Meta Title defaults</h4>
                                                    "),
                                        "type"    => "section"),
                                
                                array(  "name"    => __('Always add "Blog Name" to end of title'),
                                        "desc"    => __("Helps add consistency and usability to browser title bar / tab and for SEO purposes."),
                                        "id"      => "meta_title_do_add_blog_name",
                                        "default"     => TRUE,
                                        "type"    => "checkbox"),
                                  
                                array(  "name"  => __('Separator'),
                                        "desc"  => __("Defaults to {$kst_meta_title_sep_default}<br />Character or symbol used to separate title parts e.g. My groovy post {$meta_title_sep} Page 2 {$meta_title_sep} MyBlog.com"),
                                        "id"    => "meta_title_sep",
                                        "default"   => $kst_meta_title_sep_default,
                                        "type"  => "text",
                                        "size"  => "8"),
                                
                                /* Meta tag defaults */
                                array(  "name"    => __('SEO: Meta tag defaults'),
                                        "desc"  => __("
                                                    
                                                    "),
                                        "type"  => "subsection"),
                                
                                /* Meta tag defaults: General */
                                array(  "name"  => __('Global meta keywords'),
                                        "desc"  => __('Default keywords for meta name="keywords".<br />Used for all WP "Posts/Pages" where custom field "meta_page_keywords" is not set and defaults below are blank (where applicable).'),
                                        "id"    => "meta_keywords_global",
                                        "default"   => "",
                                        "type"  => "text",
                                        "size"  => "80"),
                                
                                array(  "name"  => __('Global meta description'),
                                        "desc"  => __('Default description for meta name="description".<br />Used for all WP "Posts/Pages" where custom field "meta_page_description" is not set, description cannot be dynamically created, and/or defaults below are blank (where applicable).'),
                                        "id"    => "meta_description_global",
                                        "default"   => "",
                                        "type"  => "text",
                                        "size"  => "80"),
                                
                                /* Meta tag defaults: Home (if home is not blog index) */
                                array(  "name"  => __('Home meta keywords'),
                                        "desc"  => __('Default keywords for meta name="keywords" on home page<br />Used if custom field "meta_page_keywords" is not set on the home/front page if not the blog index.<br />If blank defaults to "General Meta Keywords" above.'),
                                        "id"    => "meta_keywords_home",
                                        "default"   => "",
                                        "type"  => "text",
                                        "size"  => "80"),
                                
                                array(  "name"  => __('Home meta description'),
                                        "desc"  => __('Default description for meta name="description" on home page<br />Used if custom field "meta_page_description" is not set on the home/front page if not the blog index and cannot be dynamically created.<br />If blank defaults to "General Meta Description" above.'),
                                        "id"    => "meta_description_home",
                                        "default"   => "",
                                        "type"  => "text",
                                        "size"  => "80"),
                                
                                /* Meta tag defaults: Single post */
                                array(  "name"  => __('Post meta keywords'),
                                        "desc"  => __('Default keywords for meta name="keywords" on single posts view<br />Used if custom field "meta_page_keywords" is not set for that Post.<br />If blank defaults to "General Meta Keywords" above.'),
                                        "id"    => "meta_keywords_single",
                                        "default"   => "",
                                        "type"  => "text",
                                        "size"  => "80"),
                                
                                array(  "name"  => __('Post meta description'),
                                        "desc"  => __('Default description for meta name="description" on single post view<br />Used if custom field "meta_page_description" is not set for that Post and cannot be dynamically created or if Add Global Keywords is selected for that post.<br />If blank defaults to "General Meta Description" above.'),
                                        "id"    => "meta_description_single",
                                        "default"   => "",
                                        "type"  => "text",
                                        "size"  => "80"),
                                
                                /* Meta tag defaults: Page */
                                array(  "name"  => __('Page meta keywords'),
                                        "desc"  => __('Default keywords for meta name="keywords" on pages<br />Used if custom field "meta_page_keywords" is not set for that Page.<br />If blank defaults to "General Meta Keywords" above.'),
                                        "id"    => "meta_keywords_page",
                                        "default"   => "",
                                        "type"  => "text",
                                        "size"  => "80"),
                                
                                array(  "name"  => __('Page meta description'),
                                        "desc"  => __('Default description for meta name="description" on pages<br />Used if custom field "meta_page_description" is not set for that Page and cannot be dynamically created or if Add Global Keywords is selected for that page.<br />If blank defaults to "General Meta Description" above.'),
                                        "id"    => "meta_description_page",
                                        "default"   => "",
                                        "type"  => "text",
                                        "size"  => "80"),
                                
                                array(  "name"    => __('Analytics'),
                                        "desc"  => __("
                                                    
                                                    "),
                                        "type"  => "subsection"),
                                
                                array(  "name"  => __('Google Analytics Tracking ID'),
                                        "desc"  => __('Just the tracking number e.g. UA-XXXXXXX-X;<br />If entered Google Analytics tracking code is added automatically<br />Leave blank if not used or using a Google Analytics plugin'),
                                        "id"    => "ga_tracking_id",
                                        "default"   => "",
                                        "type"  => "text",
                                        "size"  => "15")
                                
                                );
                                
    if ( !isset( $theme_options ) ) {
        $theme_options = $meta_theme_options;
    } else {
        $theme_options = array_merge($theme_options, $meta_theme_options); // Put explicit Child Theme Options first
    }
 
 
/**
 * Gets meta description for head
 * 
 * Uses post_meta (custom field) "meta_page_description" if exists
 * If not looks up specific default option
 * If not looks up global default option
 * If none exists it uses "Tagline" (Settings > General) 
 * 
 * @since 0.1
 * @uses get_the_value() from metabox class in place of get_post_meta() get 'meta_page_description' if exists
 * @uses get_bloginfo()
 * @uses $kst_options->get_option()
 */
function kst_meta_description() {
    
    global $post, $kst_mb_meta_data, $kst_options;
    
    $post_custom_field = $kst_mb_meta_data->get_the_value('meta_page_description'); //get custom field via metabox class

    if ( $post_custom_field ) { /* Use post_custom_field custom field if exists */
        $content = $post_custom_field;
    } else if ( is_front_page() && isset($kst_options) && $kst_options->get_option("meta_description_home") ) { /* home page is set to custom page */
        $content = $kst_options->get_option("meta_description_home"); // default set in theme options
    } else if ( is_single() && isset($kst_options) && $kst_options->get_option("single_post_description") ) { /* single article */
        $content = $kst_options->get_option("single_post_description"); // default set in theme options
    } else if ( is_page() && isset($kst_options) && $kst_options->get_option("meta_description_page") ) { /* page */
        $content = $kst_options->get_option("meta_description_page"); // default set in theme options
    } else if ( isset($kst_options) && $kst_options->get_option("meta_description_global") ) { /* global default description in theme options */
        $content = $kst_options->get_option("meta_description_global");
    } else { /* As a last resort use blog description/tagline in SETTINGS > GENERAL */
        $content = get_bloginfo( 'description' );
    }
    
    echo "<meta name=\"description\" content=\"{$content}\" />\n";
}
add_action('wp_head', 'kst_meta_description');

/**
 * Gets meta keywords for head
 * 
 * Uses post_meta (custom field) "meta_page_keywords" if exists
 * If not it uses a default keywords as an argument
 * (set as variable on templates and passed as argument in header)
 * If none exists then keywords are blank
 * 
 * @uses get_the_value() from metabox class in place of get_post_meta() get 'meta_page_keywords' if exists
 * @uses get_bloginfo()
 * @uses $kst_options->get_option()
 */
function kst_meta_keywords() {
    
    global $post, $kst_mb_meta_data, $kst_options;
    
    $post_custom_field = $kst_mb_meta_data->get_the_value('meta_page_keywords'); //get custom field via metabox class
    
    /* Find and use appropriate keywords */
    if ( $post_custom_field ) { /* Use meta_page_keywords custom field if exists */
        $keywords = $post_custom_field;
        /* Should we add post tags to metabox keywords? */
        if ( $kst_mb_meta_data->get_the_value('meta_page_keywords_use_tags') ) {
            $post_tags = get_the_tags($post->ID);
            if ($post_tags) {
                foreach($post_tags as $tag) {
                    $keywords .= ', ' . $tag->name;
                }
            }
        }
        /* Should we add global post keywords to metabox keywords */
        if ( $kst_mb_meta_data->get_the_value('meta_page_keywords_use_global') ) {
            if ( is_page() && isset($kst_options) && $kst_options->get_option("meta_keywords_page") ) /* page */
                $keywords .= ', ' . $kst_options->get_option("meta_keywords_page"); // default set in theme options
            else if ( is_single() && isset($kst_options) && $kst_options->get_option("meta_keywords_single") ) /* single article */
                $keywords .= ', ' . $kst_options->get_option("meta_keywords_single"); // default set in theme options
            else if ( isset($kst_options) && $kst_options->get_option("meta_keywords_global") ) /* fallback on global */
                $keywords .= ', ' . $kst_options->get_option("meta_keywords_global");
        }
    } else if ( is_front_page() && isset($kst_options) && $kst_options->get_option("meta_keywords_home") ) { /* home page is set to custom page */
        $keywords = $kst_options->get_option("meta_keywords_home"); // default set in theme options
    } else if ( is_single() && isset($kst_options) && $kst_options->get_option("meta_keywords_single") ) { /* single article */
        $keywords = $kst_options->get_option("meta_keywords_single"); // default set in theme options
    } else if ( is_page() && isset($kst_options) && $kst_options->get_option("meta_keywords_page") ) { /* page */
        $keywords = $kst_options->get_option("meta_keywords_page"); // default set in theme options
    } else if ( isset($kst_options) && $kst_options->get_option("meta_keywords_global") ) { /* global default description in theme options */
        $keywords = $kst_options->get_option("meta_keywords_global");
    } else {
        $keywords = '';
    }
    
    

    echo "<meta name=\"keywords\" content=\"{$keywords}\" />\n";
}
add_action('wp_head', 'kst_meta_keywords');


/**
 * Makes some changes to the <title> tag, by filtering the output of wp_title().
 * Based on twentyten_filter_wp_title() Twenty Ten 1.0
 * Tweaked to accomodate existing SEO in scope for theme
 * 
 * Uses post_meta (custom field) "meta_page_title" if exists
 * Except feeds which use title from wp_title()
 * 
 * Uses "Site Title" (Settings > General) on 'search' and 
 * if post_meta (custom field) "meta_page_title" DOES NOT exist
 * NOTE: tagline is used for the description zui_meta_description()
 *
 * @uses get_the_value() from metabox class in place of get_post_meta() get 'meta_page_title' if exists
 * @uses is_feed()
 * @uses is_search()
 * @uses is_home()
 * @uses is_front_page()
 * @uses get_search_query()
 * @uses get_bloginfo()
 * @param string $title Title generated by wp_title()
 * @param string $separator The separator passed to wp_title(). 
 * @param boolean  $is_single_title_as_wp_title format title for services like addthis
 * @return string The new title, ready for the <title> tag.
 */
function kst_filter_wp_title( $title, $separator, $is_single_title_as_wp_title = false ) {
    
    // $paged global variable contains the page number of a listing of posts.
    // $page global variable contains the page number of a single post that is paged.
    // Display whichever one applies, if we're not looking at the first page.
    global $paged, $page, $post, $kst_mb_meta_data, $kst_options, $kst_meta_title_sep_default;
    
    if ( isset($kst_options) ) 
        $separator = $kst_options->get_option("meta_title_sep", $kst_meta_title_sep_default); // override wp_title($separator)
    else
        $separator = $kst_meta_title_sep_default; //default
    
    /* Feeds  */ 
    if ( is_feed() ) 
        return $title; // dont' mess with feeds
 
    if ( is_search() && !$is_single_title_as_wp_title  ) { //only if for the <title> not for services like addthis
        // If we're a search, let's start over:
        $title = sprintf( 'Search results for %s', '"' . get_search_query() . '"' );
        // Add a page number if we're on page 2 or more:
        if ( $paged >= 2 )
            $title .= " $separator " . sprintf( 'Page %s', $paged );
        // Add the site name to the end:
        $title .= " $separator " . get_bloginfo( 'name' );
        // We're done. Let's send the new title back to wp_title():
        return $title;
    }
    
    /* Use meta_page_title custom field if exists */ 
    $meta_page_title = $kst_mb_meta_data->get_the_value('meta_page_title'); //get from metabox class
    
    if ( $meta_page_title ) {
        
        $title = $meta_page_title;
        
        // Add a page number if necessary:
        if ( $paged >= 2 || $page >= 2 ) 
            $title .= " $separator " . sprintf( __( ' Page %s ', 'twentyten' ), max( $paged, $page ) );
        
        if ( $kst_options->get_option("meta_title_do_add_blog_name", 1) )
            $title .= " $separator " . get_bloginfo('name'); // if do_add_blog_name EQ true
        
        return $title;
    }
    
    /* Otherwise try to make something... */ 
    
    $do_next_sep = ""; // Lazy or smart?
    
    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) && !$is_single_title_as_wp_title ) { // If we have a site description and we're on the home/front page OR we are filtering a post title for things like addthis, add the description
        $title .= " $do_next_sep " . $site_description;
        $do_next_sep = $separator;
    } else if ( is_single() || is_page() ) { // Else if we are reading and article or page with an actual title
        $do_next_sep = $separator;
    }
    
    // If is the blog index AND NOT the front page i.e. probably cms style w/blog AND NOT formatted for services like addthis
    if ( $site_description && ( is_home() && !is_front_page() ) && !$is_single_title_as_wp_title ) {
        $title .= " $do_next_sep Blog ";
        $do_next_sep = $separator;
    }
    
    if ( $is_single_title_as_wp_title ) //only if for services like addthis not for the <title>
        $do_next_sep = $separator;
    
    // Add a page number if necessary: posts paged AND NOT for services like addthis OR is_single with pages 
    if ( $paged >= 2 && !$is_single_title_as_wp_title || $page >= 2 ) {
        $title .= " $do_next_sep " . sprintf( __( ' Page %s ', 'twentyten' ), max( $paged, $page ) );
        $do_next_sep = $separator;
    }
        
    
    //add the site name to the end:
    if ( $kst_options->get_option("meta_title_do_add_blog_name", 1) )
        $title .= " $separator " . get_bloginfo('name'); // if do_add_blog_name EQ true

    // Return the new title to wp_title():
    return trim($title);
}
add_filter( 'wp_title', 'kst_filter_wp_title', 10, 2 ); // filter and improve page title if this file is included


/**
 * Add custom classes from posts and pages to WP body_class()
 *
 * Under consideration for deprecation
 * Has not been updated to use metabox (TODO?)
 *  
 * @since       0.2
 * @param       required string $part   toc|entry which part do you want?
 * @return      string
 */
function kst_filter_body_class($classes, $class) {
    if ( is_single() || is_page() ) {
        global $post;
        $classes[] = get_post_meta($post->ID, 'meta_body_class', true);
    }
    return $classes;
}
add_filter('body_class','kst_filter_body_class', 10, 2);


    /**
     * kst_theme_help entry
     *
     * See kst_theme_help
     * Help content for zui based theme_help.php
     */
    function kst_theme_help_meta_data($part) {
        if ( $part == 'toc' )
            $output = "<li><a href='#seo'>SEO, meta tags, and Analytics</a></li>";
        else 
            $output = 
<<< EOD
<h2 id="seo">SEO, meta tags, and Analytics</h2>

<p>Your theme has built-in control over the page title and meta tags throughout your site. <br />
How to use these are explained in detail on the "Appearance &gt; Theme Options" page.</p>

<h3 id="seo_post_page_custom_fields">Post/Page SEO &amp; Meta Data</h3>

<p>
    Customize the TITLE, META DESCRIPTION, and META KEYWORDS for ANY POST or PAGE by filling out the "SEO &amp; Meta Data" custom fields under the editor on the post/page edit screen.
</p>
<p><strong>Custom fields and options</strong></p>
<ol>
    <li>Page Title</li>
    <li>Meta Keywords</li>
    <li>Add TAGS to Meta Keywords</li>
    <li>Add Global Keywords to Meta Keywords</li>
    <li>Meta Description</li>
</ol>
<p><strong>How the the page title is created</strong></p>
<ol>
    <li>
        Uses custom field "Page Title" if it exists for that post and page 
        (except the home, blog index, archives, search, and 404) appending "Blog Name" (Settings &gt; General) depending on your settings in "Appearance &gt; Theme Options".
    </li>
    <li>
        Otherwise the page title is created dynamically depending on the type of page is being viewed 
        using the post/page entry title where available in conjunction with relevant criteria (e.g. paged, archive, tag) 
         appending "Blog Name" (Settings &gt; General) depending on your  in "Appearance &gt; Theme Options".
    </li> 
</ol>
<p><strong>How meta keywords/description is created</strong></p>
<ol>
    <li>
        Uses custom field "Meta Keywords" or "Meta Description" if it exists for all posts and pages 
        (except the home, blog index, archives, search, and 404).
        <ol>
            <li>For keywords: If Add TAGS is checked the tags for that post/page are appended on output</li>
            <li>For keywords: If Add Global Keywords is checked the post/page default keywords are appended if they exist. If not the global default keywords are used.</li>
        </ol>
    </li>
    <li>
        If no custom field exists the theme attempts to use the appropriate post/page specifc defaults entered in the theme options.
    </li>
    <li>
        If no post/page specific default exists the theme attempts to use the global defaults entered in the theme options.
    </li>
    <li>
        If no global default exists then the theme uses "Blog Name" and "Tagline" under SETTINGS &gt; GENERAL for the description and the keywords are left blank.
    </li>
</ol>
<p>
    The Home, Blog index, Archives, Search, and 404 pages cannot use the custom fields for the meta and are created dynamically in conjunction with your global defaults.
</p>

<h3>Analytics</h3>

<p>If you enter your Google Analytics Tracking ID in "Theme Options" the Google Analytics tracking code will be added to the end of every page automatically.</p>

<p>
    <strong>Note:</strong> The built-in theme essentially precludes the need to use any plugin for SEO or Google Analytics.<br />
    <em>If for some reason you wish to not use the built-in SEO or Google Analytics options there is no setting for disabling it.<br /> 
    You should be able to safely ignore and leave blank any of these built-in theme options. <br />However, if you begin using a plugin 
    and experience problems or are optimizing simply edit functions.php and comment out the line "requiring" kst_theme_meta_data.php.</em>
</p>

<br /><br />
<a href="#wphead">Top</a>
<br /><br /><br />
EOD;
    
        return $output;
    }

?>
