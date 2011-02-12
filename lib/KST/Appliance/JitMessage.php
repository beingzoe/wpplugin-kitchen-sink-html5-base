<?php
/**
 * jQuery jit_message (just-in-time message) box
 * using WordPress custom field for activation and content
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink/
 * @link        https://github.com/beingzoe/wpplugin-kitchen-sink-html5-base/wiki/Docs_appliance_plugins_marketing_jit_message
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  Plugins:Marketing
 * @version     0.1
 * @since       0.1
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink/
 * @todo        turn the javascript into jQuery plugin for non-WP usage
 *
 *
 * Does nothing if not on a single post/page and custom field "jit_message" does not exist
 *
 * If viewing a post/page and custom field "jit_message" exists
 * Enqueues jit_message 0.1 plugin
 * Then creates the #jit_box by
 *      * finding a specific post by ID; jit_message = int;
 *      * finding a random post; jit_message = "random" or "?";
 *      * outputting explicit text/markup; jit_message = any other string
 *        May contain any markup if not then it is wrapped in h1
 *
*/

class KST_Appliance_JitMessage extends KST_Appliance {


    /**#@+
     * @access      protected
     * @var         array
    */
    protected $_args = array();
    /**#@-*/

    /**#@+
     * @access      protected
     * @var         string
    */
    protected $_jit_message = FALSE;
    /**#@-*/


    /**
     * Constructor
     *
     * @since       0.1
     * @param       required object $kitchen
    */
    public function __construct(&$kitchen) {

        // Add Help
        $appliance_help = array (
                array (
                    'page' => 'Features',
                    'section' => 'Other "sliders"',
                    'title' => 'Appliance: Media: JIT Message',
                    'content_source' => array('KST_Appliance_JitMessage', 'helpJitMessage')
                ),
                array (
                    'page' => 'Marketing',
                    'section' => 'Call To Action',
                    'title' => 'Appliance: Media: JIT Message',
                    'content_source' => array('KST_Appliance_JitMessage', 'helpJitMessage')
                )
            );

        // Every kitchen needs the basic settings
        $appliance_settings = array(
                    'friendly_name'       => 'KST Appliance: Plugin: Media: JIT Message',
                    'prefix'              => 'kst_jit_message',
                    'developer'           => 'zoe somebody',
                    'developer_url'       => 'http://beingzoe.com/'
                );

        // Declare as core
        $this->_is_core_appliance = TRUE;
        // Common appliance
        parent::_init($kitchen, $appliance_settings, NULL, $appliance_help);

    }


    /**
     * Initialize a type of JIT (Just-in-Time) Message
     *
     * Because of the nature of this feature, default usage allows only one jit at a time
     * Initializing again will result in overwriting the first one
     *
     * @since       0.1
     * @param       optional array  $args override default JitMessage settings
     *              -content_source 'posts'|any_valid_callback - note that if any_valid_callback the js is enqueued automatically and you merely need to create your own container and populate it - adjusting/checking the other default settings is probably a good idea
     *              -trigger        '#selector' - any valid css selector
     *              -wrapper        '.selector' - any valid css selector
     *              -side           'top|right|bottom|left' - what side should it slide in from?
     *              -speed_in       int - how many milliseconds to be fully in? Default: 300
     *              -speed_out      int - how many milliseconds to be fully out? Default: 100
     * @uses        add_action() WP function
    */
    public function add($args = array()) {

        // Set JIT load settings
        $appliance_defaults = array(
                'content_source'    => 'posts', // posts|or_valid_callback
                'trigger'           => '.wp_entry_footer', // ANY valid css selector found in your WP templates; defaults to '.wp_entry_footer'
                'wrapper'           => '#jit_box', // must be an ID selector ONLY formatted #id_value defaults to '#jit_box'
                'side'              => 'right',
                'top_or_bottom'     => 'top',
                'speed_in'          => 300,
                'speed_out'         => 100
            );
        $this->_args = array_merge($appliance_defaults, $args);

        // Add hooks to initialize
        if ( 'posts' == $this->_args['content_source']) {

            // Do the metabox
            $appliance_metabox = array(
                'id' => '_kst_jit_message',
                'title' => 'JIT (Just-in-Time) Message',
                'template'  => 'auto',
                'context' => 'normal',
                'priority' => 'high',
                'view' => WPALCHEMY_VIEW_START_CLOSED,
                'options'           => array(
                    'jit_header' => array(
                            'name'      => '',
                            'desc'      => '<div>Select a post or a message to promote i.e "slide" out prompting visitor to "also see".</div>',
                            'type'      => 'custom',
                            'wrap_as'   => 'subsection'
                        ),
                    'jit_message' => array(
                            'name'      => 'ID or Message',
                            'desc'      => 'ID of post to, any valid html content, or "RANDOM"',
                            'type'      => 'text'
                        ),
                    'jit_footer' => array(
                            'name'      => '',
                            'desc'      => "<div><p>Add a promotional content box that 'slides out' from the side of the page (when the page is scrolled down to the end of the post/page before the comments) encouraging visitors to view another post/page or see a message when they scroll to the end of a post/page. For more information about using the JIT Message Box see the Theme Help.</p><p>Enter the page_id, post_id, 'random' (without the quotes), or 'Any Message, html allowed' (without the quotes).</p></div>",
                            'type'      => 'subsection'
                        )
                    )
            );
            $this->_appliance->load('metabox');
            $this->_appliance->metabox->add($appliance_metabox);

            // Add the hook
            add_action('wp', array(&$this, 'postsInit'));

        } else if ( is_callable($this->_args['content_source']) ) {

            // Just add the hook
            wp_enqueue_script('jit_message', KST_URI_ASSETS . '/javascripts/jquery/jquery.kst_jit_message.js' , array('jquery') , '0.1', true);
            add_action('wp_footer', $this->_args['content_source']);
        }

    }


    /**
     * Check for jit_message in current post
     * Wait until "wp" function to be sure we have the global $post variable to use
     *
     * @since       0.1
     * @global      object $post WP current post data being output
     * @uses        WPAlchemy_MetaBox::get_the_value()
     * @uses        WPAlchemy_MetaBox
     * @uses        kst_jit_output()
     * @uses        wp_enqueue_style() WP function
     * @uses        wp_enqueue_style() WP function
     * @uses        get_template_directory_uri() WP function
     * @uses        add_action() WP function
    */
    public function postsInit() {

        global $post; // This is WordPress ;)

        if ( is_single() || is_page() ) {

            // Do we have one?
            $this->_jit_message = $this->_appliance->metabox->get_the_value('jit_message'); //get custom field via metabox class

            // Yay! We have one...
            if ( FALSE != $this->_jit_message && !empty($this->_jit_message) ) {
                // Enqueue js and add a hook to output it
                //wp_enqueue_style('jit_message', KST_URI_ASSETS . '/stylesheets/jit_message.css'); // opting to have this permanently in demo/starter themes and we'll add a hook maybe for people to decide if they want to include this separate or not
                wp_enqueue_script('jit_message', KST_URI_ASSETS . '/javascripts/jquery/jquery.kst_jit_message.js' , array('jquery') , '0.1', true);
                add_action('wp_footer', array(&$this, 'output'));
            }

        }
    }


    /**
     * Build and insert the jit box
     *
     * Prints the box directly to the browser
     *
     * Uses custom field "jit_message" to trigger:
     *      int = explicit post_id
     *      string = explicit message?
     *
     * @since       0.1
     * @uses        KST_Appliance_JitMessage::$_jit_message
     * @uses        get_posts() WP function
     * @uses        get_permalink() WP function
     * @uses        has_post_thumbnail() WP function
     * @uses        get_the_post_thumbnail() WP function
     * @todo        for random try to get in the same category/tag combination first then just categories then tags then random
     */
    public function output() {

        /* Test what type of message it is */
        if ( is_numeric( $this->_jit_message ) ) {
            /* specific post (by id) */
            $jit_posts = get_posts("numberposts=1&include={$this->_jit_message}");
            $jit_box_class = 'post';
        } else if ( strtolower($this->_jit_message) == 'random' || $this->_jit_message == '?' ) {
            /* a random post */
            $jit_posts = get_posts("numberposts=1&orderby=rand");
            $jit_box_class = 'random';
        } else {
            /* explicit message: format it here */
            $jit_box_class = 'message';
            $output_jit_box_image = "";
            $output_jit_box_info = '<div class="jit_box_info">';
            if (preg_match("/([\<])([^\>]{1,})*([\>])/i", $this->_jit_message)) { //Is there markup?
                $output_jit_box_info .= $this->_jit_message;
            } else { //guess not so format it
                $output_jit_box_info .= "<h1>{$this->_jit_message}</h1>";
            }
            $output_jit_box_info .= '</div>';
        }

        /* Yay we found the post ($jit_posts) so format it */
        if ( isset( $jit_posts ) ) {
            $the_permalink = get_permalink($jit_posts[0]->ID);
            $the_title = $jit_posts[0]->post_title;
            $output_jit_box_image = "";
            if ( has_post_thumbnail($jit_posts[0]->ID) ) {
                $the_post_thumbnail = get_the_post_thumbnail($jit_posts[0]->ID, 'thumbnail'); //get default size thumbnail

            $output_jit_box_image =
            <<<EOD
            <div class="jit_box_image">
                <a href="{$the_permalink}">{$the_post_thumbnail}</a>
            </div>
EOD;
            } else {

            }//end if has_post_thumbnail
            $output_jit_box_info =
            <<<EOD
            <div class="jit_box_info">
                <h1>
                    You might also like...<br />
                    <a href="{$the_permalink}">{$the_title}</a>
                </h1>
            </div>
EOD;
        }

        $jit_message_params = "
            'trigger'       : '{$this->_args['trigger']}',
            'wrapper'       : '{$this->_args['wrapper']}',
            'side'          : '{$this->_args['side']}',
            'top_or_bottom' : '{$this->_args['top_or_bottom']}',
            'speed_in'      : {$this->_args['speed_in']},
            'speed_out'     : {$this->_args['speed_out']}
        ";
?>
        <div id="<?php echo str_replace('#', '', $this->_args['wrapper']); ?>" class="<?php echo $jit_box_class; ?>"><?php echo $output_jit_box_image; ?> <?php echo $output_jit_box_info; ?><div class="jit_box_close"><a>Close</a></div></div>
        <!--[if gt IE 6 ]><!-->
        <script type="text/javascript">jQuery(document).ready(function($) { if(jQuery().jit_message) { $(this).jit_message({<?php echo $jit_message_params; ?>}); }; });</script>
        <!--<![endif]-->
    <?php
    }


    /**
     * KST_Appliance_Help entry
     * Features: Appliance: Fancybox Lightbox
     *
     * @since       0.1
    */
    public static function helpJitMessage() {
        ?>
            <p>
                Have another page or a message you would like to share with them as they finish
                reading a post or page? The JIT box will "slide out" from the right side of the
                page when the visitor scrolls to the end of the article (where the comments would start).
            </p>
            <p>
                You can include a specific post or page, a random post or page,
                or just a text message (html is allowed).
            </p>
            <p>
                You can make this box appear on any post or page using the "JIT message box" custom field edit
                box beneath the post/page editor. The "JIT message box" edit box is close by default. You will
                need to click it to open it.
            </p>

            <p><strong>Link to a specific post or page</strong></p>
            <ol>
                <li>Find the ID of the post/page you want to link to</li>
                <li>
                    Edit the post/page you want to link FROM
                    <ol>
                        <li>
                            In the "message" field enter the ID
                            <ol>
                                <li>Message: XXX (e.g. 330, 2, 5913)</li>
                            </ol>
                        </li>
                    </ol>
                </li>
                <li>Update/publish the post</li>
            </ol>

            <p><strong>Link to a random post or page</strong></p>
            <ol>
                <li>
                    Edit the post/page you want to link from
                    <ol>
                        <li>
                            In the "message" field type 'random'
                            <ol>
                                <li>Message: random</li>
                            </ol>
                        </li>
                    </ol>
                </li>
                <li>Update/publish the post</li>
            </ol>

            <p><strong>JIT message a text message</strong></p>
            <ol>
                <li>
                    Edit the post/page you want to have the message
                    <ol>
                        <li>
                            In the "message" field type the message (html allowed)
                            <ol>
                            <li>Message: ANY TEXT OR VALID HTML <br />e.g. <p>We are having a <a href='http://mysite.com/big-sale.html'>big sale</a> and you should check it out.</p></li>
                            </ol>
                        </li>
                    </ol>
                </li>
                <li>Update/publish the post</li>
            </ol>

            <p>
                <strong>Future feature update:</strong> We will be adding friendlier controls for this down the road with a ton more options and features. For now we apologize for making you dig up post id's like this :)
            </p>
            <p>
                <strong>Developer note:</strong> This is handled via a KST library in the _application directory, invoked in functions.php, and called from assets/javascript/script.js
            </p>
        <?php
    }

}

