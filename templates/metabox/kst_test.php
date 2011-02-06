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
    //global $kst_mb_jit_message;
    //$kst_mb_jit_message->the_meta(); //get meta data for post

    $GLOBALS["my_theme"]->metabox->pickle->the_meta()
?>


<div class="kst_meta_box">
    <p>
        Test metabox
    </p>
    <p>
        Enter stuff.
    </p>
    <table class="form-table">
        <?php //$mb->the_field('jit_message');
        ?>
        <tr valign="top">
            <th scope="row">
                <label for="">Message</label>
            </th>
            <td>
                <input type="text" name="<?php $GLOBALS["my_theme"]->metabox->pickle->the_name('testpicklebox'); ?>" id="<?php $GLOBALS["my_theme"]->metabox->pickle->the_name('testpicklebox'); ?>" size="50" value="<?php $GLOBALS["my_theme"]->metabox->pickle->the_value('testpicklebox'); ?>"/>
                <span><em>e.g. '342', 'random', '&lt;p&gt;Any message, &lt;a href='#'&gt;html allowed&lt;/a&gt;&lt;/p&gt;</em></span>
            </td>
        </tr>
    </table>
</div>