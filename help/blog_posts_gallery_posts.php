<?php
/**
 * Help file partial include
 * This file should not contain anything other than content markup starting at <h3> in the document tree
 *
 * @version     0.1
 * @since       0.1
*/
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


<?php echo KST_HELP_SECTION_SEPARATOR; ?>

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

<?php echo KST_HELP_SECTION_SEPARATOR; ?>

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

<?php echo KST_HELP_SECTION_SEPARATOR; ?>

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

<?php echo KST_HELP_SECTION_SEPARATOR; ?>



