<?php 
/**
 * Meta Box content for theme SEO and meta data
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
 
    /**
     * @global object $kst_mb_meta_data
     */
    global $kst_mb_meta_data;
    $kst_mb_meta_data->the_meta(); //get meta data for post
?>
<div class="kst_meta_box">
    <p>
        SEO meta data control for this post/page.<br />
        If no data is entered here defaults will be used/created using the post/page content and the settings in "<a href='<?php echo THEME_OPTIONS_URL; ?>'>Appearance &gt; Theme Options</a>".<br />
        For more information about how the SEO meta data is used see "<a href='<?php echo THEME_HELP_URL; ?>#seo'>Appearance &gt; Theme Help</a>".
    </p>
    <table class="form-table">
        <?php $mb->the_field('meta_page_title'); ?>
        <tr valign="top"> 
            <th scope="row">
                <label for="<?php $mb->the_name(); ?>">Page Title</label>
            </th>                    
            <td>
                <input type="text" name="<?php $mb->the_name(); ?>" id="<?php $mb->the_name(); ?>" size="50" value="<?php $mb->the_value(); ?>"/>
                <span><em>If empty defaults to entry title</em></span>
            </td>
        </tr>
        <?php $mb->the_field('meta_page_keywords'); ?>
        <tr valign="top"> 
            <th scope="row">
                <label for="<?php $mb->the_name(); ?>">Meta Keywords</label>
            </th>
            <td>
                <input type="text" name="<?php $mb->the_name(); ?>" id="<?php $mb->the_name(); ?>" size="50" value="<?php $mb->the_value(); ?>"/>
                <span><em> If empty defaults to GLOBAL KEYWORDS</em></span>
            </td>
        </tr>
        
        <tr valign="top"> 
            <th scope="row">
                
            </th>
            <td>
                <?php $mb->the_field('meta_page_keywords_use_tags'); ?> 
                <input type="checkbox" name="<?php $mb->the_name(); ?>" id="<?php $mb->the_name(); ?>" value="1"<?php if ( $mb->is_value('1') ) echo ' checked="checked"'; ?> /> <label for="<?php $mb->the_name(); ?>">Add TAGS to Meta Keywords above<?php $mb->is_value(''); ?></label>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php $mb->the_field('meta_page_keywords_use_global'); ?>
                <input type="checkbox" name="<?php $mb->the_name(); ?>" id="<?php $mb->the_name(); ?>" value="1"<?php if ( $mb->is_value('1') ) echo ' checked="checked"'; ?> /> <label for="<?php $mb->the_name(); ?>">Add GLOBAL KEYWORDS to Meta Keywords above<?php $mb->is_value(''); ?></label>
            </td>
        </tr>
        <?php $mb->the_field('meta_page_description'); ?>
        <tr valign="top"> 
            <th scope="row">
                <label for="<?php $mb->the_name(); ?>">Meta Description</label>
            </th>
            <td>
                <textarea name="<?php $mb->the_name(); ?>" id="<?php $mb->the_name(); ?>" cols="50" rows="2"><?php $mb->the_value(); ?></textarea><br />
                <span><em>If empty defaults to GLOBAL DESCRIPTION</em></span>
            </td>
        </tr>
    </table>
</div>