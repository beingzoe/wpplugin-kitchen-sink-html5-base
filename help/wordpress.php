<?php
/**
 * KST_Appliance_Help entry
 * content_source callback functions
 *
 * WordPress tips and tricks
 *
 * @package     KitchenSinkHTML5Base
 * @subpackage  Support
 * @version     0.1
 * @since       0.1
 * @author      zoe somebody
 * @link        http://beingzoe.com/
 * @global      object $_GLOBALS["kst_core"]
 * @uses        KST_Appliance_Help
*/


// Add Help
$kst_core_help_array = array (
        array (
            'page' => 'WordPress',
            'section' => 'Blog Posts',
            'title' => 'Post thumbnails (featured image)',
            'content_source' => 'kstHelpWordpressBlogPosts_postThumbnails'
        ),
        array (
            'page' => 'WordPress',
            'section' => 'Blog Posts',
            'title' => 'Excerpts and More teasers',
            'content_source' => 'kstHelpWordpressBlogPosts_excerptsAndMoreTeasers'
        ),
        array (
            'page' => 'WordPress',
            'section' => 'Blog Posts',
            'title' => 'Multiple page posts',
            'content_source' => 'kstHelpWordpressBlogPosts_multiplePagePostsPages'
        ),
        array (
            'page' => 'WordPress',
            'section' => 'Blog Posts',
            'title' => 'Gallery posts (thumbnail gallery)',
            'content_source' => 'kstHelpWordpressBlogPosts_galleryPostsThumbnailGallery'
        ),

        array (
            'page' => 'WordPress',
            'section' => 'Site Pages',
            'title' => 'Page Titles',
            'content_source' => 'kstHelpWordpressSitePages_pageTitles'
        ),
        array (
            'page' => 'WordPress',
            'section' => 'Site Pages',
            'title' => 'Custom Page Templates',
            'content_source' => 'kstHelpWordpressSitePages_customPageTemplates'
        ),

        array (
            'page' => 'WordPress',
            'section' => 'Misc',
            'title' => 'Custom Fields and MetaBoxes',
            'content_source' => 'kstHelpWordpressMisc_customFieldsAndMetaBoxes'
        )

    );


// Load Help
$GLOBALS["kst_core"]->load('help');
$GLOBALS["kst_core"]->help->add($kst_core_help_array);


/**
 * KST_Appliance_Help entry
 * Wordpress: Blog Posts: Post thumbnails (featured image)
 *
 * @since       0.1
*/
function kstHelpWordpressBlogPosts_postThumbnails() {
    ?>
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
    <?php
}


/**
 * KST_Appliance_Help entry
 * Wordpress: Blog Posts: Excerpts and More teasers
 *
 * @since       0.1
*/
function kstHelpWordpressBlogPosts_excerptsAndMoreTeasers() {
    ?>
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
    <?php
}


/**
 * KST_Appliance_Help entry
 * Wordpress: Blog Posts: Multiple page posts/pages
 *
 * @since       0.1
*/
function kstHelpWordpressBlogPosts_multiplePagePostsPages() {
    ?>
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
    <?php
}


/**
 * KST_Appliance_Help entry
 * Wordpress: Blog Posts: Gallery posts (thumbnail gallery)
 *
 * @since       0.1
*/
function kstHelpWordpressBlogPosts_galleryPostsThumbnailGallery() {
    ?>
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
    <?php
}


/**
 * KST_Appliance_Help entry
 * Wordpress: Site Pages: Page Titles
 *
 * @since       0.1
*/
function kstHelpWordpressSitePages_pageTitles() {
    ?>
        <p><em>
            NOTE: Your theme may automatically create and display the title based
            on the title of page, but by default recommended Kitchen Sink HTML5 Base practice,
            the title for pages should be manually created using the technique below for more
            control over your SEO.
        </em></p>
        <p><em>
            Referring to the title that displays at the top of the content
            NOT the "meta" title that appears in search engines and the title bar of your browser.
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
    <?php
}


/**
 * KST_Appliance_Help entry
 * Wordpress: Site Pages: Custom Page Templates
 *
 * @since       0.1
*/
function kstHelpWordpressSitePages_customPageTemplates() {
    ?>
        <p>
            WordPress offers incredible flexibility as a CMS (Content Management System).
            Theme and plugin developers often include special layout templates that you
            can use on your site pages to present information in a different format or
            layout, as well as to add functionality to certain pages of your site.
        </p>
        <p>
            Examples include contact forms, multi-column layouts, magazine style
            index layouts, or even mini applications.
        </p>
        <p>
            Occasionally these templates can be so complex that you can't really
            even edit the content the page and the page that appears in your
            Pages Admin List is really only a place holder. It will be obvious
            if you come across one of these pages.
        </p>
        <p>
            You will know that the creator of your theme or plugins included custom
            layout templates for you to use if (when creating/editing a Site Page)
            you see "Template" with a dropdown box under it in the right hand sidebar.
        </p>
        <p>
            Choosing a template and publishing the page will use that template instead of
            the default "pages" template that your site pages use.
        </p>
    <?php
}


/**
 * KST_Appliance_Help entry
 * Wordpress: Misc: Custom Fields and MetaBoxes
 *
 * @since       0.1
*/
function kstHelpWordpressMisc_customFieldsAndMetaBoxes() {
    ?>
        <p>
            Among the many fabulous but too little used features of WordPress includes
            custom fields. Custom fields are generic form fields below the text editor
            on posts and pages (and custom post types) that allow theme and plugin
            developers the ability to add unique functionality to posts and pages that
            you have control over per post/page.
        </p>
        <p>
            Examples include changing a quote or content box in the header or sidebar
            on a per page basis, embedding media, and adding extra meta-type information to
            display about posts such as "duration", "citations", "special guests", or
            whatever the theme or plugin developer offered.
        </p>
        <p>
            If your theme includes such things hopefully they will have added a help
            entry for you to learn how to use them.
        </p>
        <p>
            The theme or plugin developer may have alternatively created
            "metaboxes" that appear in place of the "generic custom fields". A metabox
            is a custom mini-form that explicitly tells the contributor what data they
            can enter.
        </p>
    <?php
}
