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
 * @package     KitchenSinkHTML5Base
 * @subpackage  Theme
 * @version     0.2
 * @since       0.1
 * @uses        kst_theme_help_meta_data() to include install specific help content in context
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @todo        convert to class
 * @todo        find a better way to do this shit
 */
?>

<p>
    This help page explains any unique functionality in your theme.<br />
    WordPress is a powerful tool with many options not used in every theme. <br />
    Below are some topics on how to get the most out of your <em><?php echo THEME_NAME; ?> custom theme</em>.
</p>
<p>
    <em>
        This page is not intended to explain basic use of WordPress to manage your blog or website. <br />
        For help with using WordPress in general visit the
        <a href="http://codex.wordpress.org/">WordPress Codex</a> especially the section <a href="http://codex.wordpress.org/Getting_Started_with_WordPress#WordPress_for_Beginners">WordPress for Beginners</a>.
    </em>
</p>
<p>
    <em>If you need help editing the theme itself or questions on using it contact <a href='<?php echo THEME_DEVELOPER_URL; ?>'><?php echo THEME_DEVELOPER; ?></a></em>
</p>

<p><strong>Major Topics</strong></p>
<ol>
    <?php
            if ( function_exists('kst_theme_help_meta_data') ) echo kst_theme_help_meta_data('toc');
    ?>
    <li>
        <a href="#posts">Blog POSTS</a>
        <ol>
            <li><a href="#post_thumbnails_featured_image">Post thumbnails (featured image)</a></li>
            <li><a href="#excerpts_more">Excerpts and More teasers</a></li>
            <li><a href="#multiple_page_posts">Multiple page posts</a></li>
            <li><a href="#gallery_posts">Gallery posts (thumbnails)</a></li>
            <li><a href="#aside_asides">Aside posts (Asides/Sideblog)</a></li>
        </ol>
    </li>
    <li>
        <a href="#pages">Site PAGES</a>
        <ol>
            <li><a href="#page_titles">Page titles</a></li>
            <li><a href="#special_layouts">Special layouts</a></li>
        </ol>
    </li>
    <li>
        <a href="#custom_theme_notes">Custom theme notes</a>
        <ol>
            <li><a href="#custom_fields">About custom fields</a></li>
        </ol>
    </li>
    <li>
        <a href="#extras">Flashy features and extra functionality</a>
        <ol>
            <?php
                if ( function_exists('kst_theme_help_lightbox') ) echo kst_theme_help_lightbox('toc');
                if ( function_exists('kst_theme_help_cyclable') ) echo kst_theme_help_cyclable('toc');
                if ( function_exists('kst_theme_help_scrollables') ) echo kst_theme_help_scrollables('toc');
                if ( function_exists('kst_theme_help_jit_message') ) echo kst_theme_help_jit_message('toc');
            ?>
        </ol>
    </li>
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

<?php
    if ( function_exists('kst_theme_help_meta_data') ) echo kst_theme_help_meta_data('entry');
?>

<h2 id="posts">Blog POSTS</h2>
<p>The following features will help you use your blog like a pro by using advanced techniques while posting.</p>
<br />



<h3 id="post_thumbnails_featured_image">Post thumbnails (featured image)</h3>
<p>
    Each post can use an image as a "featured image" aka "post thumbnail" on the blog index and in archives.
</p>
<p>
    When reading a post (permalink) a larger version of the "featured image" appears at the top of the post.
</p>
<p>
    <strong>How to use a "featured image" on a post:</strong>
</p>
<p>
    Option 1: (you are uploading multiple pictures anyway)<br />
    Above the buttons above the editor you will see "Upload/Insert" and four icons representing the different media you can upload.
    Click the first button that looks like a photo. In the popup that appears on the first "tab" labeled "From your computer"
    Upload all the images for that post.
    Click the link labeled "Show" next to the image you would like to use. A form will appear.
    At the bottom of the form, click the link labeled "Use as featured image".
</p>
<p>
    Option 2: (you only need to upload the "featured image")<br />
    In the right sidebar of the post edit screen at the bottom click the link labeled "Set featured image".<br />
    A form will appear. Upload the image you would like to use.<br />
    Once the image is uploaded a form will appear where you can edit the image and meta data.<br />
    At the bottom of the form, click the link labeled "Use as featured image".
</p>
<p><em>
    NOTE: If you just want to show the thumbnail but not the fullsize image when reading
    the post add the custom field "hide_featured_image" with a value of "1". See
    <a href="#custom_fields">custom fields below</a> for more info.
</em></p>


<br /><br />
<a href="#wphead">Top</a>
<br /><br />

<h3 id="excerpts_more">Excerpts and More teasers</h3>
<p>
    Your theme uses the excerpt field as well as the &lt;--more--&gt; tag giving
    you many options for formatting your blog index. Many themes use either the
    excerpt or the &lt;--more--&gt; tag to control what content appears on your
    blog index.
</p>
<p>
    Posts are displayed on the index in one of these formats (and order shown):
</p>
<ol>
    <li>Title and excerpt (if you type anything in the excerpt field)</li>
    <li>Title and the post up to the &lt;--more--&gt; (if you insert &lt;--more--&gt; in the post)</li>
    <li>Title and the entire post (if you do not enter an excerpt or the &lt;--more--&gt; tag)</li>
</ol>
<p><em>i.e. If you enter an excerpt and include a &lt;--more--&gt; tag only the excerpt will display</em></p>
<p>
    <strong>How to use the "more" tag:</strong>
</p>
<p>
    In "Visual" mode:<br />
    Place the cursor <em>after</em> the content you would like to appear on the blog index for that post.
    Click the button above the post editor that looks like
    a rectangle cut in two pieces (a small rectangle on top and a longer rectangle on the button).
    A line that says "more" will appear.
</p>
<p>
    In "html" mode:<br />
    Place the cursor <em>after</em> the content you would like to appear on the blog index for that post.
    Type &lt;--more--&gt; on it's own line.
</p>
<p>
Note: You may also customize the "more" link text that is shown per post (only possible using "html" mode).<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Simply type the text to use after "more" e.g. &lt;--more But wait, there's more! --&gt;
</p>
<br /><br />
<a href="#wphead">Top</a>
<br /><br />

<h3 id="multiple_page_posts">Multiple page posts</h3>
<p>
    For long posts that you would like to break up into smaller chunks for readability
    your theme uses the  &lt;--nextpage--&gt; tag.
</p>
<p>
    When the post is being read it will look for &lt;--nextpage--&gt;
    and create a pager box (e.g. Pages: [1] [2] [3]) for as many &lt;--nextpage--&gt;
    tags as you included in your post.
</p>
<p>
    <strong>How to use the "nextpage" tag:</strong>
</p>
<p>
    In "Visual" mode:<br />
    <em>This cannot be done in "visual" mode. You will need to go into "html" mode for this which makes you a real pro.</em>
<p>
    In "html" mode:<br />
    Place the cursor <em>after</em> the content that is the first "page" of your post.
    Type &lt;--nextpage--&gt; on it's own line. Repeat for each "page" of your post.
    <em>Note that you do not need to put &lt;--nextpage--&gt; at the end of the last "page" (end of post).</em>
</p>

<br /><br />
<a href="#wphead">Top</a>
<br /><br />

<h3 id="gallery_posts">Gallery posts (thumbnails)</h3>
<p>
    If you want to share a lot of pictures in a post sometimes you don't want them
    all to appear "full size" creating a <em>very</em> long post. Instead you can
    turn the post into a gallery of thumbnails that will show all the pictures you
    uploaded for that post in rows 3 thumbnails across. This is done with the [gallery] shortcode.
</p>
<p>
    <strong>How to use the "gallery" shortcode:</strong>
</p>
<p><em>Note that you can compose a normal post with text and images as well as use the [gallery] shortcode.</em></p>
<p>
    In "Visual" mode:<br />
    Place the cursor where you would like the gallery thumbnails to appear.
    Above the buttons above the editor you will see "Upload/Insert" and four icons representing the different media you can upload.
    Click the first button that looks like a photo. In the popup that appears on the first "tab" labeled "From your computer"
    upload all the images for that post.
    When you are done  click the "tab" labeled "Gallery", scroll to the bottom and click the "Insert Gallery" button.
    The popup will close and a large image icon will appear in your post indicating where the thumbnails will appear.
</p>
<p>
    In "html" mode:<br />
    Place the cursor where you would like the gallery thumbnails to appear.
    Type [gallery] on it's own line.
</p>

<br /><br />
<a href="#wphead">Top</a>
<br /><br />

<h3 id="aside_asides">Aside posts (Asides/Sideblog)</h3>
<p>
    <em>When you lean towards someone and tell them a little bit of information, you are making an "aside" comment. In blogs, you can do that on your blog by passing on small bits of information to your readers called Asides.
    Also known as remaindered links or linkblog, Asides were originally implemented by Matt Mullenweg, developer of WordPress, and it soon spread far and wide and became a very popular method of adding little bits of information to your blog.
    Learn more about Asides <a href='http://codex.wordpress.org/Adding_Asides'>here</a> and <a href='http://churchcrunch.com/a-genius-way-to-keep-your-blog-content-fresh-create-an-aside-category/'>here</a></em>
</p>
<h4>"Asides" Categories</h4>
<p>Your theme has more than one type of "aside". </p>
<ol>
    <li>ASIDES <em>(traditional as described above)</em></li>
    <li>GALLERY <em>(for posts consisting mostly pictures typically as thumbnails with a lightbox)</em></li>
</ol>

<br /><br />
<a href="#wphead">Top</a>
<br /><br /><br />



<h2>Site PAGES</h2>

<br />

<h3 id="page_titles">Page titles</h3>
<p><em>
    Referring to the title that displays at the top of the content
    NOT the "meta" title that appears in search engines and the title bar of your browser.
    For information about the "meta" title please see SEO below and your "Appearance &gt; Theme Options" page.
</em></p>
<p>
    Creating and editing pages on your site is as easy as posting to the blog.<br />
    <em>HOWEVER</em>, where blog POSTS automatically show the title as entered
    on the POST edit screen PAGES in your theme on the other hand require you to enter the title manually.
    This is for a couple of practical reasons. Often you will want a longer title to display on the page
    for visitors than you want to see in the PAGE list when editing.
    SEO requires rewording the title that is displayed on the page
    but you don't need your whole backend PAGE list organization to change with it.
</p>
<p>
    <strong>How to create the title of your PAGE:</strong>
</p>
<p>
    In "Visual" mode:<br />
    Type the title of the page in the content edit box.
    Select the text for the title like you would in your word processor.
    In the edit buttons above the edit box choose the "format" drop down (select) box
    (typically displays "paragraph" by default). Choose "HEADING 1".
</p>
<p>
    In "html" mode:<br />
    Type the title of the page in the content edit box.
    BEFORE the text type  &lt;h1&gt; and AFTER type &lt;/h1&gt;
    e.g. My groovy title becomes &lt;h1&gt;My groovy title&lt;/h1&gt;
</p>

<br /><br />
<a href="#wphead">Top</a>
<br /><br />

<h3 id="special_layouts">Special layouts</h3>
<p><strong>Special layouts you can edit</strong></p>
<p>
    Your theme was designed to be as user friendly as possible
    while giving you control over as much content as possible.
    However, complex layouts on your site might have complex HTML markup
    in the content you can edit in the WP-admin. Don't worry, you can still
    edit the content but you need to be aware of it.
</p>
<p>
    It will be obvious if you come across one of these pages
</p>
<p>
    We have left comments on any pages where this is true. Ideally you should use
    "html mode" to edit those pages to be sure you aren't
    accidentally deleting important &lt;html&gt;HTML tags&lt;/html&gt;. However,
    unless the comment explicitly says to use "html mode" you are probably safe
    to edit the text and images in "visual mode" so long as you don't delete the comments
    e.g. &lt;--This is a comment and you shouldn't delete it--&gt;
</p>
<p><strong>Special layouts you can NOT edit</strong></p>
<p>
    These are typically contact forms or other layouts that require
    functionality not feasible to be editable through the WP admin.
    Contact your developer for assistance.
</p>

<br /><br />
<a href="#wphead">Top</a>
<br /><br /><br />



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



<br /><br />
<a href="#wphead">Top</a>
<br /><br />

<h2 id="extras">Flashy extra features and functionality</h2>

<br />

<?php
if ( function_exists('kst_theme_help_lightbox') ) echo kst_theme_help_lightbox('entry');
if ( function_exists('kst_theme_help_cyclable') ) echo kst_theme_help_cyclable('entry');
if ( function_exists('kst_theme_help_scrollables') ) echo kst_theme_help_scrollables('entry');
if ( function_exists('kst_theme_help_jit_message') ) echo kst_theme_help_jit_message('entry');
?>



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
