<?php 
/**
 * jQuery jit_message (just-in-time message) box
 * using WordPress custom field for activation and content
 * 
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     WordPress
 * @subpackage  KitchenSinkThemeLibraries
 * @version     0.1
 * @since       0.2
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @todo        turn the javascript into jQuery plugin
 * @todo        add options
 * @todo        add option for trigger_selector
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

/**
 * Initialize
 */
add_action('wp', 'kst_jit_init'); 

/**
 * Instantiate WPAlchemy_MetaBox class
 * Replaces get_post_meta
 * 
 * @since 0.3
 * @uses        WPAlchemy_MetaBox
 */
global $kst_mb_jit_message;
require_once WP_PLUGIN_DIR . '/kitchen-sink-html5-base/vendor/WPAlchemy/MetaBox.php'; // WP admin meta boxes
$kst_mb_jit_message = new WPAlchemy_MetaBox( array (
    'id' => '_kst_jit_message',
    'title' => 'JIT (Just-in-Time) Message Box',
    'template' => TEMPLATEPATH . '/templates/meta_boxes/kst_jquery_jit_message.php',
    'context' => 'normal',
    'priority' => 'high',
    'view' => WPALCHEMY_VIEW_START_CLOSED
));


/**
 * Check for jit_message in current post
 * Wait until "wp" function to be sure we have the global $post variable to use
 * 
 * @since       0.1
 * @global      $post
 * @global      $jit_message
 * @global      $kst_mb_jit_message
 * @uses        WPAlchemy_MetaBox::get_the_value()
 * @uses        WPAlchemy_MetaBox
 * @uses        kst_jit_output()
 * @uses        wp_enqueue_style() WP function
 * @uses        wp_enqueue_style() WP function
 * @uses        get_template_directory_uri() WP function
 * @uses        add_action() WP function
 */
function kst_jit_init() {
    
    global $post;
    
    if ( is_single() || is_page() ) { 
        
        global $jit_message, $kst_mb_jit_message;
        
        //$jit_message = get_post_meta($post->ID, 'jit_message', true);
        $jit_message = $kst_mb_jit_message->get_the_value('jit_message'); //get custom field via metabox class
        
        /* if we have a jit_message then set it up and do stuff */
        if ( $jit_message ) {
            wp_enqueue_style('jit_message', get_template_directory_uri() . '/assets/stylesheets/jit_message.css');
            wp_enqueue_script('jit_message', get_template_directory_uri() . '/assets/javascripts/jquery/jquery.kst_jit_message.js' , array('jquery') , '0.1', true);
            add_action('wp_footer', 'kst_jit_output');
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
 * @global      $jit_message
 * @uses        get_posts() WP function
 * @uses        get_permalink() WP function
 * @uses        has_post_thumbnail() WP function
 * @uses        get_the_post_thumbnail() WP function
 * @todo        for random try to get in the same category/tag combination first then just categories then tags then random
 */
function kst_jit_output() {
    global $jit_message;
    
    /* Test what type of message it is */
    if ( is_numeric( $jit_message ) ) {
        /* specific post (by id) */
        $jit_posts = get_posts("numberposts=1&include={$jit_message}"); 
        $jit_box_class = 'post';
    } else if ( strtolower($jit_message) == 'random' || $jit_message == '?' ) {
        /* a random post */
        $jit_posts = get_posts("numberposts=1&orderby=rand");
        $jit_box_class = 'random';
    } else {
        /* explicit message: format it here */
        $jit_box_class = 'message';
        $output_jit_box_image = "";
        $output_jit_box_info = '<div class="jit_box_info">';
        if (preg_match("/([\<])([^\>]{1,})*([\>])/i", $jit_message)) { //Is there markup?
            $output_jit_box_info .= $jit_message;
        } else { //guess not so format it
            $output_jit_box_info .= "<h1>{$jit_message}</h1>";
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
                } //end if has_post_thumbnail
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
    
?>
    <div id="jit_box" class="<?php echo $jit_box_class; ?>"><?php echo $output_jit_box_image; ?> <?php echo $output_jit_box_info; ?><div class="jit_box_close"><a>Close</a></div></div>
    <script type="text/javascript">jQuery(document).ready(function($) { if(jQuery().jit_message) { $(this).jit_message(); }; });</script>
<?php
}


/**
 * kst_theme_help entry
 *
 * See kst_theme_help
 * Help content for zui based theme_help.php
 * 
 * @since       0.2
 * @param       required string $part   toc|entry which part do you want?
 * @return      string
 */
function kst_theme_help_jit_message($part) {
    if ( $part == 'toc' )
        $output = "<li><a href='#lib_jit_message'>JIT (Just in time) message box</a></li>";
    else 
        $output = 
<<< EOD
<h3 id="lib_jit_message">JIT (Just in time) message box</h3>

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
    <strong>Developer note:</strong> This is handled via a KST library in the _application directory, invoked in functions.php, and called from _assets/javascript/application.js
</p>

<br /><br />
<a href="#wphead">Top</a>
<br /><br /><br />

EOD;

    return $output;
}

