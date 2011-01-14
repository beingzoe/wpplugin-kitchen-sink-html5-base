<?php
/**
 * Meta Box content for JIT Message 
 * 
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  KitchenSinkMetaBoxes
 * @version     0.1
 * @since       0.1
 * @uses        WPAlchemy_MetaBox
 */
    global $kst_mb_jit_message;
    $kst_mb_jit_message->the_meta(); //get meta data for post
?>
<div class="kst_meta_box">
    <p>
        Add a promotional content box that "slides out" from the side of the page (when the page is scrolled down to the end of the post/page before the comments) encouraging visitors to view another post/page or see a message when they scroll to the end of a post/page
        For more information about using the JIT Message Box see "<a href='<?php echo THEME_HELP_URL; ?>#seo'>Appearance &gt; Theme Help</a>".
    </p>
    <p>
        Enter the page_id, post_id, 'random' (without the quotes), or 'Any Message, html allowed' (without the quotes).
    </p>
    <table class="form-table">
        <?php $mb->the_field('jit_message'); ?>
        <tr valign="top"> 
            <th scope="row">
                <label for="<?php $mb->the_name(); ?>">Message</label>
            </th>
            <td>
                <input type="text" name="<?php $mb->the_name(); ?>" id="<?php $mb->the_name(); ?>" size="50" value="<?php $mb->the_value(); ?>"/>
                <span><em>e.g. '342', 'random', '&lt;p&gt;Any message, &lt;a href='#'&gt;html allowed&lt;/a&gt;&lt;/p&gt;</em></span>
            </td>
        </tr>
    </table>
</div>