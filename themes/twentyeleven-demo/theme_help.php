<?php
/**
 * Theme Help
 * Master help file for current theme
 *
 * Uses a mix of boilerplate (client/install agnostic) KST help
 * Other built-in KST libraries and classes include their own help file entry
 * theme_help_LIBRARYorCLASSname($part)
 *      $part =
 *          toc (table of contents list entry)
 *          entry = (actual help entry in help)
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.2
 * @since       0.1
 * @uses        kst_theme_help_meta_data() to include install specific help content in context
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @todo        convert to class
 * @todo        find a better way to do this
 */
?>



<p><strong>Major Topics</strong></p>
<ol>
    <li>
        <a href="#posts">Blog POSTS</a>
        <ol>
            <li><a href="#aside_asides">Aside posts (Asides/Sideblog)</a></li>
        </ol>
    <li>
        <a href="#plugins">Plugins</a>
        <ol>
            <li><a href="#ais">Additional Image Sizes <em>(image size management)</em></a></li>
            <li><a href="#sociable">Sociable <em>(Social Networking/Marketing)</em></a></li>
            <li><a href="#subscribecomments">Subscribe to Comments <em>(email comment notification)</em></a></li>
            <li><a href="#tinymce_advanced">TinyMCE Advanced <em>(Admin WYSIWYG)</em></a></li>
        </ol>
    </li>
    <li>
        <a href="#developer">Developer notes</a>
    </li>
</ol>

<br /><br />

<h2 id="posts">Blog POSTS</h2>
<p>The following features will help you use your blog like a pro by using advanced techniques while posting.</p>
<br />



<h2 id="custom_theme_notes">Custom theme notes</h2>

<br />

<h3 id="custom_fields">About custom fields</h3>
<p>
    Custom fields are used in your theme. These are unique pieces of information to tell your theme how to display
    and perform special functions. The most important custom fields have been incorporated directly into
    your theme and will appear as "meta boxes" beneath the "content editor" on post and pages
    i.e. "SEO and Meta Data", "JIT (Just-in-Time) Message Box", or any custom display items you can edit per page.
    Custom fields that do not easily editable in the "meta boxes" are generally advanced extra functionality. Below is
    a list of the custom fields in your theme.
</p>
<p>
    Custom fields are found below the "content editor" of any post/page.
</p>
<ol>
    <li>
        Custom field: <strong>hide_featured_image</strong><br />
        Value: <strong>1</strong><br />
        <em>Hides the featured image when viewing posts but still shows the thumbnail when browsing posts</em>
    </li>
    <li>
        Custom field: <strong>meta_page_class</strong><br />
        Value: <strong>any_custom_class_name</strong><br />
        <em>Adds a class to the body for CSS styling and javascript purposes</em>
    </li>
</ol>





<h2 id="plugins">Plugins</h2>

<p><em>Not all plugins installed in your theme are listed below. Only plugins that were utilized as core to the design/functionality of your theme are addressed below. </em></p>

<br />


<h3 id="ais">Additional Image Sizes <em>(image size management)</em></h3>
<p>
    This plugin allows the creation of custom sizes of images for more rich and complex layouts.
    <a href="http://www.waltervos.com/wordpress-plugins/additional-image-sizes/">"Additional Image Sizes" (http://www.waltervos.com/wordpress-plugins/additional-image-sizes/)</a>.<br />
    If you are using "featured images / post thumbnails" this plugin is also used to create a custom size for displaying
    the featured image on posts as necessary.
</p>
<p>
    The plugin also allows for easy recreation/regeneration of new/revised sizes for existing images later on.
</p>
<h4>Editing image sizes</h4>
<p>
    Go to "Media > Additional Image Sizes".
</p>
<ol>
    <li>In the table of image sizes at the top of the page locate the image size you would like to edit.</li>
    <li>Edit the width (in pixels) e.g. 333 </li>
    <li>Edit the height (in pixels) e.g. 238 </li>
    <li>OPTIONAL: Click the "crop" box if you want it to crop the image to the dimensions specified.</li>
    <li>NOTE: You may leave EITHER the width OR the height blank to indicate that the image should be resized explicitly to the width/height specified and the other dimension should be resized proportionally.</li>
    <li>Click "SAVE CHANGES"</li>
    <li>Once the page has reloaded click "Generate copies of new sizes" at the bottom of the settings page.</li>
</ol>

<h4>Create a new image size</h4>
<p><em>At any time you may wish to have a new image size that is available when you upload images to put in posts/pages.</em></p>
<p>
    Go to "Media > Additional Image Sizes".
</p>
<ol>
    <li>In the table of image sizes at the top of the page locate the blank size at the bottom of the table.</li>
    <li>Follow the instructions for editing an image size above.</li>
    <li>Click "SAVE CHANGES"</li>
    <li>Once the page has reloaded click "Generate copies of new sizes" at the bottom of the settings page.</li>
</ol>

<h4>Deleting an image size</h4>
<p><strong>N.B.: This does not delete the actual images, only the image size is removed from future use.</strong> Posts/pages using the deleted image size will continue to display normally.</p>
<p><strong>N.B.: You cannot delete an image size AND create an image size at the same time.</strong></p>
<p>
    Go to "Media > Additional Image Sizes".
</p>
<ol>
    <li>In the table of image sizes at the top of the page locate the image size(s) line(s) you wish to delete.</li>
    <li>Click the checkbox in the left hand column labeled "Delete"</li>
    <li>Click "SAVE CHANGES"</li>
</ol>

<p><strong>N.B.:</strong> In some cases the "Generate copies of new sizes" function will respond with an error IF you have cropped an uploaded image in the WP admin image editor. This is an unfortunate minor bug having to do with new WP image managment features. In nearly all cases the new/revised image sizes have been created despite the warning message. This only applies if you have edited an image using the WP admin image editor.</p>


<br /><br />
<a href="#wphead">Top</a>
<br /><br />


<h3 id="sociable">Sociable <em>(Social Networking/Marketing)</em></h3>
<p>
    To enhance your social networking/marketing your theme uses the plugin
    <a href="http://blogplay.com/sociable-for-wordpress/">"Sociable" (http://blogplay.com/sociable-for-wordpress/)</a>.
</p>
<p>
    This adds social networking bookmarking links as well as print and email buttons in each post
    in order to encourage people promoting your posts.
</p>
<h4>Editing your Sociable links</h4>
<p>
    Under "Settings > Sociable" you will find a list of available services.<br />
    Select/de-select the services you think your demographic uses or would be interested in.
</p>
<p><strong>N.B. Sociable is manually integrated into your theme and changing any other settings should only be done by your developer or with the understanding that your layout could be adversely affected.</strong></p>

<br /><br />
<a href="#wphead">Top</a>
<br /><br />

<h3 id="subscribecomments">Subscribe to Comments <em>(email comment notification)</em></h3>
<p>
    When commenting visitors can choose to be alerted via email when follow-up comments are posted on the same post
    <a href="http://txfx.net/wordpress-plugins/subscribe-to-comments/">"Subscribe to Comments" (http://txfx.net/wordpress-plugins/subscribe-to-comments/)</a>.
</p>
<h4>Editing your Subscribe to Comments settings</h4>
<p>
    Under "Settings > Subscribe to Comments" there are several settings YOU SHOULD REVIEW and update as necessary:<br />
</p>
<ol>
    <li>"From" name for notifications<br /><em>The name that will appear in the email sent to comment notification subscribers.</em></li>
    <li>"From" e-mail addresss for notifications<br /><em>The address that will appear as the reply address in the email sent to comment notification subscribers.</em></li>
    <li>Not subscribed<br /><em>Label next to the checkbox to subscribe in the comments form WHEN THE VISITOR IS NOT SUBSCRIBED.</em></li>
    <li>Subscribed<br /><em>Label next to the checkbox to subscribe in the comments form WHEN THE VISITOR IS ALREADY SUBSCRIBED.</em></li>
    <li>Entry Author<br /><em>Label next to the checkbox to subscribe for the author of the post.</em></li>
</ol>
<p><strong>N.B. The subscribe to comments is integrated into your theme and changing any settings pertaining to the layout or appearance should only be done by your developer or with the understanding that your layout could be adversely affected. </strong></p>

<br /><br />
<a href="#wphead">Top</a>
<br /><br />



<h3 id="tinymce_advanced">TinyMCE Advanced <em>(Admin WYSIWYG)</em></h3>
<p>
    TinyMCE is the WordPress Admin WYSIWYG editor for posts/pages.<br />
    This plugin makes it easy to add/remove buttons from the editor as well as control some advanced functionality of the editor.
</p>
<p>
    Developers and advanced users may edit this at "Settings > TinyMCE Advanced".
</p>
<p><strong>Note:</strong> This is not a necessary plugin but has proven useful in improving the editing experience for everyday users by adding/removing functionality as needed and included specific styles they may use in the editor.</p>

<br /><br />
<a href="#wphead">Top</a>
<br /><br />


<h2 id="developer">Developer Notes</h2>

<p>Coming Soon.</p>

<br /><br />
<a href="#wphead">Top</a>
<br /><br />
