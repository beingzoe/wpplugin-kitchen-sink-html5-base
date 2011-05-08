<?php
/**
 * META and SEO functions
 * These are basic functions that should work for most projects.
 * Otherwise don't include the library and use a plugin.
 * No changes need made to templates if this library is not used.
 *
 * This library is dependent on the KST_Appliance_Options class
 *
 * echoMetaDescription() and echoMetaKeywords() echo the entire meta tag
 * as part of the wp_head().
 *
 * filterWpTitle() filters the wp_title call
 *
 * filterBodyClass looks for a custom field of meta_body_class to allow for
 * flexible styling of layouts or sections
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink/
 * @link        https://github.com/beingzoe/wpplugin-kitchen-sink-html5-base/wiki/Docs_appliance_plugins_marketing_seo
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  Plugins:Marketing
 * @version     0.1
 * @since       0.1
 * @uses        KST_Kitchen
 * @uses        KST_Appliance_Options
 * @uses        WPAlchemy_MetaBox
 * @todo        convert to class
 * @todo        implement user friendly always present post custom fields form like this great tutorial http://sltaylor.co.uk/blog/control-your-own-wordpress-custom-fields/
 * @todo        attempt to build keyword list from post/page tags
 */


/**
 * Companion class
 *
 * Uses WPAlchemy_MetaBox to create custom field metaboxes for per post/page seo
 *
 * @since       0.1
*/
require_once KST_DIR_VENDOR . '/farinspace/WPAlchemy/WPAlchemy/MetaBox.php'; // WP admin meta boxe


/**
 * Basic seo and meta tag control
 *
 * @since       0.1
 * @uses
*/
class KST_Appliance_Seo extends KST_Appliance {

    /**#@+
     * @since       0.1
     * @access      protected
     * @var         object
    */
    protected $_metabox_fields; // metabox custom fields property object
    /**#@-*/


    /**#@+
     * @since       0.1
     * @access      protected
     * @var         string
    */
    protected $_meta_title_sep; // meta segment separator
    protected $_meta_title_sep_default; // default meta segment separator
    /**#@-*/

    /**
     * Constructor - saves the kitchen object that this instance belongs to
     * and other information common to all options pages in this kitchen
     *
     * @since       0.1
     * @uses        KST_Kitchen::getTypeOfKitchen()
     * @uses        WPAlchemy_MetaBox
     * @param       required array $kitchen the kitchen object this instance belongs to in KST (by reference)
    */
    public function __construct(&$kitchen) {

        // Every kitchen needs the basic settings
        $appliance_settings = array(
                    'friendly_name'       => 'KST Appliance: Plugin: Marketing: SEO',
                    'prefix'              => 'kst_seo',
                    'developer'           => 'zoe somebody',
                    'developer_url'       => 'http://beingzoe.com/',
                );

        // Add Help
        $appliance_help = array (
                array (
                    'page' => 'Marketing',
                    'section' => 'SEO, Meta Data, and Analytics',
                    'title' => 'SEO',
                    'content_source' => array('KST_Appliance_Seo', 'helpSeoSeo')
                ),
                array (
                    'page' => 'Marketing',
                    'section' => 'SEO, Meta Data, and Analytics',
                    'title' => 'Analytics',
                    'content_source' => array('KST_Appliance_Seo', 'helpSeoAnalytics')
                ),
                array (
                    'page' => 'Marketing',
                    'section' => 'SEO, Meta Data, and Analytics',
                    'title' => 'Other Meta Data',
                    'content_source' => array('KST_Appliance_Seo', 'helpSeoOtherMetaData')
                ),
                array (
                    'page' => 'Marketing',
                    'section' => 'Microformats',
                    'title' => 'Sitewide vCard (Microformat)',
                    'content_source' => array('KST_Appliance_Seo', 'helpSeoMicroformatVcard')
                )

            );

        // Declare as core
        $this->_is_core_appliance = TRUE;
        // Common appliance
        parent::_init($kitchen, $appliance_settings, NULL, $appliance_help);

        // Get optional THEME meta title segment separator default
        // This just sets the options default - we get the option later to actual use
        $this->_meta_title_sep_default = KST_Kitchen_Theme::getThemeSeoTitleSep();

        // Create the seo admin options
        $options_page = '#temp';//THEME_HELP_URL;
        $appliance_options = array(
                'parent_slug'           => 'kst',
                'menu_title'            => 'SEO and Meta',
                'page_title'            => 'SEO and Meta Data Settings',
                'capability'            => 'manage_options',
                'view_page_callback'    => "auto",
                'options'               => array(
                        // Page title settings
                        'seo_main' => array(
                                            "name"    => __('SEO'),
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
                        'meta_title_do_add_blog_name' => array(
                                            "name"    => __('Always add "Blog Name" to end of title'),
                                            "desc"    => __("Helps add consistency and usability to browser title bar / tab and for SEO purposes."),
                                            "default"     => TRUE,
                                            "type"    => "checkbox"),
                        'meta_title_sep' => array(
                                            "name"  => __('Separator'),
                                            "desc"  => __("Defaults to" . $this->_meta_title_sep_default . "<br />Character or symbol used to separate title parts e.g. My groovy post " . $this->_meta_title_sep_default . " Page 2 " . $this->_meta_title_sep_default . " MyBlog.com"),
                                            "default"   => $this->_meta_title_sep_default,
                                            "type"  => "text",
                                            "size"  => "8"),
                        //Meta tag defaults
                        'seo_meta_tags' => array(
                                            "name"    => __('SEO: Meta tag defaults'),
                                            "desc"  => __("

                                                        "),
                                            "type"  => "subsection"),
                        // Meta tag defaults: General
                        'meta_keywords_global' => array(
                                            "name"  => __('Global meta keywords'),
                                            "desc"  => __('Default keywords for meta name="keywords".<br />Used for all WP "Posts/Pages" where custom field "meta_page_keywords" is not set and defaults below are blank (where applicable).'),
                                            "default"   => "",
                                            "type"  => "text",
                                            "size"  => "80"),
                        'meta_description_global' => array(
                                            "name"  => __('Global meta description'),
                                            "desc"  => __('Default description for meta name="description".<br />Used for all WP "Posts/Pages" where custom field "meta_page_description" is not set, description cannot be dynamically created, and/or defaults below are blank (where applicable).'),
                                            "default"   => "",
                                            "type"  => "text",
                                            "size"  => "80"),
                        // Meta tag defaults: Home (if home is not blog index)
                        'meta_keywords_home' => array(
                                            "name"  => __('Home meta keywords'),
                                            "desc"  => __('Default keywords for meta name="keywords" on home page<br />Used if custom field "meta_page_keywords" is not set on the home/front page if not the blog index.<br />If blank defaults to "General Meta Keywords" above.'),
                                            "default"   => "",
                                            "type"  => "text",
                                            "size"  => "80"),
                        'meta_description_home' => array(
                                            "name"  => __('Home meta description'),
                                            "desc"  => __('Default description for meta name="description" on home page<br />Used if custom field "meta_page_description" is not set on the home/front page if not the blog index and cannot be dynamically created.<br />If blank defaults to "General Meta Description" above.'),
                                            "default"   => "",
                                            "type"  => "text",
                                            "size"  => "80"),
                        // Meta tag defaults: Blog (if home is not blog index)
                        'meta_title_blog' => array(
                                            "name"  => __('Blog page title'),
                                            "desc"  => __('Title to use on blog indexes (e.g. blog, archives)<br />Always used, but on archives current taxonomy is appended (e.g. Blog Title &laquo; Some tags)<br />If blank defaults to "most recent post title".'),
                                            "default"   => "",
                                            "type"  => "text",
                                            "size"  => "80"),
                        'meta_keywords_blog' => array(
                                            "name"  => __('Blog meta keywords'),
                                            "desc"  => __('Default keywords for meta name="keywords" on blog indexes (e.g. blog, archives)<br />Used if custom field "meta_page_keywords" is not set on the home/front page if not the blog index.<br />If blank defaults to "General Meta Keywords" above.'),
                                            "default"   => "",
                                            "type"  => "text",
                                            "size"  => "80"),
                        'meta_description_blog' => array(
                                            "name"  => __('Blog meta description'),
                                            "desc"  => __('Default description for meta name="description" on blog indexes (e.g. blog, archives)<br />Used if custom field "meta_page_description" is not set on the home/front page if not the blog index and cannot be dynamically created.<br />If blank defaults to "General Meta Description" above.'),
                                            "default"   => "",
                                            "type"  => "text",
                                            "size"  => "80"),
                        // Meta tag defaults: Single post */
                        'meta_keywords_single' => array(
                                            "name"  => __('Post meta keywords'),
                                            "desc"  => __('Default keywords for meta name="keywords" on single posts view<br />Used if custom field "meta_page_keywords" is not set for that Post.<br />If blank defaults to "General Meta Keywords" above.'),
                                            "default"   => "",
                                            "type"  => "text",
                                            "size"  => "80"),
                        'meta_description_single' => array(
                                            "name"  => __('Post meta description'),
                                            "desc"  => __('Default description for meta name="description" on single post view<br />Used if custom field "meta_page_description" is not set for that Post and cannot be dynamically created or if Add Global Keywords is selected for that post.<br />If blank defaults to "General Meta Description" above.'),
                                            "default"   => "",
                                            "type"  => "text",
                                            "size"  => "80"),
                        // Meta tag defaults: Page */
                        'meta_keywords_page' => array(
                                            "name"  => __('Page meta keywords'),
                                            "desc"  => __('Default keywords for meta name="keywords" on pages<br />Used if custom field "meta_page_keywords" is not set for that Page.<br />If blank defaults to "General Meta Keywords" above.'),
                                            "default"   => "",
                                            "type"  => "text",
                                            "size"  => "80"),
                        'meta_description_page' => array(
                                            "name"  => __('Page meta description'),
                                            "desc"  => __('Default description for meta name="description" on pages<br />Used if custom field "meta_page_description" is not set for that Page and cannot be dynamically created or if Add Global Keywords is selected for that page.<br />If blank defaults to "General Meta Description" above.'),
                                            "id"    => "",
                                            "default"   => "",
                                            "type"  => "text",
                                            "size"  => "80"),
                        'seo_analytics' => array(  "name"    => __('Analytics'),
                                            "desc"  => __("

                                                        "),
                                            "type"  => "subsection"),
                        'ga_tracking_id' => array(  "name"  => __('Google Analytics Tracking ID'),
                                            "desc"  => __('Just the tracking number e.g. UA-XXXXXXX-X;<br />If entered Google Analytics tracking code is added automatically<br />Leave blank if not used or using a Google Analytics plugin'),
                                            "default"   => "",
                                            "type"  => "text",
                                            "size"  => "15"),

                        'seo_microformat_vcard' => array(
                                            "name"    => __('Sitewide vCard (Microformat)'),
                                            "desc"    => __("
                                                            <p>
                                                                By filling out the following information a sitewide microformat vCard will be added at the
                                                                bottom of every page. When someone browsing your site using a microformat enabled browser/plugin
                                                                (or bots) they will be able to add the vCard info to their contacts automatically. At some
                                                                point we will be adding the full range of microformat options.
                                                            </p>
                                                            <p>
                                                                At minimum you need to enter a Name and Website for the vCard to be included.
                                                            </p>
                                                        "),
                                            "type"    => "section"),
                        'doShowVcard' => array(
                                            "name"    => __('Show vCard'),
                                            "desc"    => __("Defaults to FALSE"),
                                            "default"     => FALSE,
                                            "type"    => "checkbox"),
                        'vCardName' => array(
                                            "name"    => __('Name'),
                                            "desc"    => __("Full Name / Company Name"),
                                            "default"     => get_bloginfo('name'),
                                            "type"    => "text",
                                            "size"    => 30),
                        'vCardUrl' => array(
                                            "name"    => __('Website'),
                                            "desc"    => __("Complete URL e.g. http://example.org/"),
                                            "default"     => "http://" . $_SERVER['HTTP_HOST'] . "/",
                                            "type"    => "text",
                                            "size"    => 100),
                        'vCardTelWork' => array(
                                            "name"    => __('Work Phone'),
                                            "desc"    => __(""),
                                            "default"     => NULL,
                                            "type"    => "text",
                                            "size"    => 17),
                        'vCardTelFax' => array(
                                            "name"    => __('Fax'),
                                            "desc"    => __(""),
                                            "default"     => NULL,
                                            "type"    => "text",
                                            "size"    => 17),
                        'vCardTelMobile' => array(
                                            "name"    => __('Mobile Phone'),
                                            "desc"    => __(""),
                                            "default"     => NULL,
                                            "type"    => "text",
                                            "size"    => 17)

                        )
                );

        $this->_appliance->load('options');
        $this->_appliance->options->add($appliance_options);

        // Instantiate WPAlchemy_MetaBox class  - Replaces get_post_meta()

        $appliance_metabox = array(
            'id' => '_kst_wp_meta_data',
            'title' => 'SEO &amp; Meta Data',
            'template'  => 'auto',
            'context' => 'normal',
            'priority' => 'high',
            'options'           => array(
                'explanation' => array(
                        'name'      => 'Page Title',
                        'desc'      => '<div>If no data is entered here defaults will be used/created using the post/page content and the settings in...</div>',
                        'type'      => 'custom',
                        'wrap_as'   => 'subsection'
                    ),
                'meta_page_title' => array(
                        'name'      => 'Page Title',
                        'desc'      => 'If empty defaults to entry title',
                        'type'      => 'text',
                        "size"  => "80"
                    ),
                'meta_page_keywords' => array(
                        'name'      => 'Meta Keywords',
                        'desc'      => 'If empty defaults to GLOBAL KEYWORDS',
                        'type'      => 'text',
                        "size"  => "80"
                    ),
                'meta_page_keywords_use_tags' => array(
                        'name'      => 'Append post tags as keywords',
                        'desc'      => 'Add Post TAGS to Meta Keywords above',
                        'type'      => 'checkbox',
                        'default'   => TRUE
                    ),
                'meta_page_keywords_use_global' => array(
                        'name'      => 'Append global keywords',
                        'desc'      => 'Add GLOBAL KEYWORDS to Meta Keywords above',
                        'type'      => 'checkbox'
                    ),
                'meta_page_description' => array(
                        'name'      => 'Meta Description',
                        'desc'      => 'If empty defaults to GLOBAL DESCRIPTION',
                        'type'      => 'text',
                        "size"  => "80"
                    ),
                'meta_body_class' => array(
                        'name'      => 'Body class(es)',
                        'desc'      => 'Advanced.<br />Add css classes for custom styling/layouts',
                        'type'      => 'text'
                    )
                )
        );

        $this->_appliance->load('metabox');
        $this->_appliance->metabox->add($appliance_metabox);

        // Set blog owner meta title segment separator if it exists
        $this->_meta_title_sep = $this->_appliance->options->get( 'meta_title_sep' );

        // Add WP hooks
        add_action('wp_head', array(&$this, 'echoMetaDescription'));
        add_action('wp_head', array(&$this, 'echoMetaKeywords'));
        add_filter( 'wp_title', array(&$this, 'filterWpTitle'), 10, 2 ); // filter and improve page title if this file is included
        if ( $this->_appliance->options->get("ga_tracking_id") ) {
            add_action('wp_footer', array(&$this, 'echoGoogleAnalyticsBoilerPlateStyle'));
        }
        if ( $this->_appliance->options->get('doShowVcard') && $this->_appliance->options->get('vCardName') && $this->_appliance->options->get('vCardUrl') ) {
            add_action('wp_footer', array(&$this, 'echoMicroformatVcard'));
        }

        add_filter('body_class', array(&$this, 'filterBodyClass'), 10, 2);

    }


    /**
     * Gets meta description for head
     *
     * Uses post_meta (custom field) "meta_page_description" if exists
     * If not looks up specific default option
     * If not looks up global default option
     * If none exists it uses "Tagline" (Settings > General)
     *
     * @since       0.1
     * @uses get_the_value() from metabox class in place of get_post_meta() get 'meta_page_description' if exists
     * @uses get_bloginfo()
     * @uses $this->_appliance->options->get()
    */
    public function echoMetaDescription() {

        global $post; // WP global post object for current post

        if (is_singular()) {
            $this->_appliance->metabox->the_field('meta_page_description');
            $post_custom_field = $this->_appliance->metabox->get_the_value(); //get custom field via metabox class
        } else {
            $post_custom_field = FALSE;
        }

        if ( $post_custom_field ) { /* Use post_custom_field custom field if exists */
            $content = $post_custom_field;
        } else if ( is_home() && $this->_appliance->options->get("meta_description_blog") ) { /* home page is set to custom page */
            $content = $this->_appliance->options->get("meta_description_blog"); // default set in theme options
        } else if ( is_front_page() && $this->_appliance->options->get("meta_description_home") ) { /* home page is set to custom page */
            $content = $this->_appliance->options->get("meta_description_home"); // default set in theme options
        } else if ( is_single() && $this->_appliance->options->get("meta_description_single") ) { /* single article */
            $content = $this->_appliance->options->get("meta_description_single"); // default set in theme options
        } else if ( is_page() && $this->_appliance->options->get("meta_description_page") ) { /* page */
            $content = $this->_appliance->options->get("meta_description_page"); // default set in theme options
        } else if ( $this->_appliance->options->get("meta_description_global") ) { /* global default description in theme options */
            $content = $this->_appliance->options->get("meta_description_global");
        } else { /* As a last resort use blog description/tagline in SETTINGS > GENERAL */
            $content = get_bloginfo( 'description' );
        }

        echo "<meta name=\"description\" content=\"{$content}\" />\n";
    }


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
     * @uses $this->_appliance->options->get()
    */
    function echoMetaKeywords() {

        global $post, $kst_seo;

        if (is_singular())
            $post_custom_field = $this->_appliance->metabox->get_the_value('meta_page_keywords'); //get custom field via metabox class
        else
            $post_custom_field = FALSE;

        /* Find and use appropriate keywords */
        if ( $post_custom_field ) { /* Use meta_page_keywords custom field if exists */
            $keywords = $post_custom_field;
            /* Should we add global post keywords to metabox keywords */
            if ( is_singular() && $this->_appliance->metabox->get_the_value('meta_page_keywords_use_global') ) {
                if ( is_page() && $this->_appliance->options->get("meta_keywords_page") ) /* page */
                    $keywords .= ', ' . $this->_appliance->options->get("meta_keywords_page"); // default set in theme options
                else if ( is_single() && $this->_appliance->options->get("meta_keywords_single") ) /* single article */
                    $keywords .= ', ' . $this->_appliance->options->get("meta_keywords_single"); // default set in theme options
                else if ( $this->_appliance->options->get("meta_keywords_global") ) /* fallback on global */
                    $keywords .= ', ' . $this->_appliance->options->get("meta_keywords_global");
            }
        } else if ( is_home() && $this->_appliance->options->get("meta_keywords_blog") ) { /* home page is set to custom page */
            $keywords = $this->_appliance->options->get("meta_keywords_blog"); // default set in theme options
        } else if ( is_front_page() && $this->_appliance->options->get("meta_keywords_home") ) { /* home page is set to custom page */
            $keywords = $this->_appliance->options->get("meta_keywords_home"); // default set in theme options
        } else if ( is_single() && $this->_appliance->options->get("meta_keywords_single") ) { /* single article */
            $keywords = $this->_appliance->options->get("meta_keywords_single"); // default set in theme options
        } else if ( is_page() && $this->_appliance->options->get("meta_keywords_page") ) { /* page */
            $keywords = $this->_appliance->options->get("meta_keywords_page"); // default set in theme options
        } else if ( $this->_appliance->options->get("meta_keywords_global") ) { /* global default description in theme options */
            $keywords = $this->_appliance->options->get("meta_keywords_global");
        } else {
            $keywords = '';
        }

        /* Should we add post tags to metabox keywords? */
        if ( is_singular() && $this->_appliance->metabox->get_the_value('meta_page_keywords_use_tags') ) {
            $post_tags = get_the_tags($post->ID);
            if ($post_tags) {
                foreach($post_tags as $tag) {
                    $keywords .= ', ' . $tag->name;
                }
            }
        }

        echo "<meta name=\"keywords\" content=\"{$keywords}\" />\n";
    }


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
     * @uses        get_the_value() from metabox class in place of get_post_meta() get 'meta_page_title' if exists
     * @uses        is_feed()
     * @uses        is_search()
     * @uses        is_home()
     * @uses        is_front_page()
     * @uses        get_search_query()
     * @uses        get_bloginfo()
     * @global      object $post
     * @param       string $title Title generated by wp_title()
     * @param       string $separator The separator passed to wp_title().
     * @param       boolean  $is_single_title_as_wp_title format title for services like addthis
     * @return      string The new title, ready for the <title> tag.
    */
    public function filterWpTitle( $title, $separator, $is_single_title_as_wp_title = false ) {

        // $paged global variable contains the page number of a listing of posts.
        // $page global variable contains the page number of a single post that is paged.
        // Display whichever one applies, if we're not looking at the first page.
        global $paged, $page, $post;

        $separator = $this->_meta_title_sep; // override wp_title($separator)

        // Feeds
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

        if ( is_home() && !$is_single_title_as_wp_title  ) { // is_home = blog index no matter what
            //
            global $post;

            $meta_title_blog = $this->_appliance->options->get("meta_title_blog");
            if ( !empty($meta_title_blog) )
                $title = $meta_title_blog;
            else
                $title = $post->post_title;

            // Add a page number if we're on page 2 or more:
            if ( $paged >= 2 )
                $title .= " $separator " . sprintf( 'Page %s', $paged );
            // Add the site name to the end:
            if ( $this->_appliance->options->get("meta_title_do_add_blog_name", 1) )
                $title .= " $separator " . get_bloginfo('name'); // if do_add_blog_name EQ true
            // We're done. Let's send the new title back to wp_title():
            return $title;
        }

        // Use meta_page_title custom field if exists
        if ( is_singular() )
            $meta_page_title = $this->_appliance->metabox->get_the_value('meta_page_title'); //get from metabox class
        else
            $meta_page_title = FALSE;

        if ( $meta_page_title ) {

            $title = $meta_page_title;

            // Add a page number if necessary:
            if ( $paged >= 2 || $page >= 2 )
                $title .= " $separator " . sprintf( __( ' Page %s ', 'twentyten' ), max( $paged, $page ) );

            if ( $this->_appliance->options->get("meta_title_do_add_blog_name", 1) )
                $title .= " $separator " . get_bloginfo('name'); // if do_add_blog_name EQ true

            return $title;
        }

        // Otherwise try to make something...
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
        if ( $this->_appliance->options->get("meta_title_do_add_blog_name", 1) )
            $title .= " $separator " . get_bloginfo('name'); // if do_add_blog_name EQ true

        // Return the new title to wp_title():
        return trim($title);
    }


    /**
     * KST built-in google analytics output with HTML5 Boilerplate script
     *
     *
    */
    public function echoGoogleAnalyticsBoilerPlateStyle() {
    ?>
        <script type="text/javascript">
            var _gaq = [['_setAccount', '<?php echo $this->_appliance->options->get("ga_tracking_id"); ?>'], ['_trackPageview']];
            (function(d, t) {
            var g = d.createElement(t),
                s = d.getElementsByTagName(t)[0];
            g.async = true;
            g.src = ('https:' == location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            s.parentNode.insertBefore(g, s);
            })(document, 'script');
        </script>
    <?php
    }


    /**
     * KST built-in google analytics output with HTML5 Boilerplate script
     *
     *
    */
    public function echoMicroformatVcard() {
        $vCardName = $this->_appliance->options->get("vCardName");
        $vCardUrl = $this->_appliance->options->get("vCardUrl");
        $vCardTelWork = $this->_appliance->options->get("vCardTelWork");
        $vCardTelFax = $this->_appliance->options->get("vCardTelFax");
        $vCardTelMobile = $this->_appliance->options->get("vCardTelMobile") ;

        echo '<address class="hmeta vcard">';
            echo '<a class="fn org url" href="' . $vCardUrl . '">' . $vCardName . '</a>';
            if ( $vCardTelWork || $vCardTelFax || $vCardTelMobile ) {
                echo '<span class="tel">';
                    if ( $vCardTelWork )
                        echo '<span class="tel">'. '<span class="type">Work</span>' . $vCardTelWork . '</span>';
                    if ( $vCardTelWork )
                        echo '<span class="tel">'. '<span class="type">Fax</span>' . $vCardTelFax . '</span>';
                    if ( $vCardTelMobile )
                        echo '<span class="tel">'. '<span class="type">Mobile</span>' . $vCardTelMobile . '</span>';
                echo '</span>';
                //echo '<span class="adr">';
                //echo '</span>';
            }
        echo '</address>';
    }



    /**
     * Add custom classes from posts and pages to WP body_class()
     *
     * Under consideration for deprecation
     * Has not been updated to use metabox (TODO?)
     *
     * Or is this whole thing moot now? Just make templates? But this is a good optional...
     *
     * @since       0.1
     * @param       required string $part   toc|entry which part do you want?
     * @return      string
    */
    public function filterBodyClass($classes, $class) {
        if ( is_single() || is_page() ) {
            global $post;
            $meta_body_class = $this->_appliance->metabox->get_the_value('meta_body_class'); //get from metabox class
            if ( is_string($meta_body_class) ) {
                $classes[] = $meta_body_class;
            }
            $classes[] = get_post_meta($post->ID, 'meta_body_class', true); // deprecated
        }
        return $classes;
    }


    /**
     * KST_Appliance_Help entry
     * SEO: SEO
     *
     * @since       0.1
    */
    public static function helpSeoSeo() {
        ?>
            <p>
            Your theme has built-in control over the page title and meta tags throughout your site.
            BE SURE to set the GLOBAL DEFAULTS for SEO settings on the "Theme Options &gt; <a href="admin.php?page=kst_kst_seo_SEO_and_Meta">SEO</a>" page.
            where you will also find context specific help for SEO and meta data.
            </p>

            <h3 id="seo_post_page_custom_fields">SEO per Post/Page</h3>

            <p>Directly below the editor while adding/updating posts and pages you will see a metabox for editing SEO meta data for that post.</p>

            <ol>
                <li>Page Title</li>
                <li>Meta Keywords</li>
                <li>Add TAGS to Meta Keywords (post tags + global)</li>
                <li>Add Global Keywords to Meta Keywords (these + global)</li>
                <li>Meta Description</li>
            </ol>

            <p><strong>How the the page title is created</strong></p>
            <ol>
                <li>
                    Uses custom field "Page Title" if it exists for that post and page
                    (except the home, blog index, archives, search, and 404) appending "Blog Name" (Settings &gt; <a href="options-general.php">General</a>) depending on your custom theme settings in "Theme Options &gt; <a href="admin.php?page=kst_kst_seo_SEO_and_Meta">SEO</a>".
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
            <p>
                <strong>Note:</strong> The built-in "appliances" (embedded plugins) essentially precludes the need to use any plugin for SEO or Google Analytics.<br />
                <em>If for some reason you wish to not use the built-in SEO or Google Analytics options there is no setting for disabling it.<br />
                You should be able to safely ignore and leave blank any of these built-in theme options. <br />However, if you begin using a plugin
                and experience problems or are optimizing simply edit functions.php and comment out the line "requiring" kst_theme_meta_data.php.</em>
            </p>
            <p><small>Applies to Kitchen Sink HTML5 Base bundled SEO</small></p>
        <?php
    }


    /**
     * KST_Appliance_Help entry
     * SEO: SEO
     *
     * @since       0.1
    */
    public static function helpSeoAnalytics() {
        ?>
            <p>If you enter your Google Analytics Tracking ID in "Theme Options" the Google Analytics tracking code will be added to the end of every page automatically.</p>
            <p><small>Applies to Kitchen Sink HTML5 Base bundled SEO</small></p>
        <?php
    }


    /**
     * KST_Appliance_Help entry
     * SEO: SEO
     *
     * @since       0.1
    */
    public static function helpSeoMicroformatVcard() {
        ?>
            <p>Microfomats help bots index your content and more and more are becoming integrated into everyday applications.</p>
            <p>
                At some point we will be adding the full range of microformat options. For now know that your site is optimized
                to use hCard specifications in general (like the list of posts in the blog and post/page articles). But you can
                also set some contact info about you or your organization in the SEO settings under Theme Options and an hCard
                will be added at the bottom of every page. When someone browsing your site using a microformat enabled browser/plugin
                (or bots) they will be able to add the vCard info to their contacts automatically.
            </p>
        <?php
    }

}
