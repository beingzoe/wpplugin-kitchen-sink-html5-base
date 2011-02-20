<?php
/**
 * Additional Image Sizes for WordPress Image Media
 *
 * Create additional image sizes (in addition to the predefined WordPress
 * defaults thumbnail, medium and large size that are default) for your WordPress site/blog.
 * Will also resize the predefined WordPress sizes if the size in Media > Settings has been edited.
 *
 * This plugin is a fork/rewrite of Additional image sizes created by Walter Vos
 * The last version from Walter was Version: 1.0.2 and is still available on
 * the WordPress plugin directory.
 *
 * The new version eliminiates a variety of bugs in the original
 * and does everything the original did plus...
 *     -resizes predefined WordPress sizes if they have been changed in the admin
 *
 * Future versions will also offer the ability to delete images when you
 * delete a custom image size.
 *
 * @package     ZUI
 * @subpackage  WordPress
 * @version     0.1.1
 * @since       0.1
 * @author      Walter Vos
 * @link        http://www.waltervos.com/
 * @author      zoe somebody
 * @link        http://beingzoe.com/
 * @link        http://www.waltervos.com/wordpress-plugins/additional-image-sizes/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
*/

if ( !is_admin() )
    return; // not needed in front end ever - speed things up


/**
 * Add hooks for static methods in ZUI_WpAdditionalImageSizes
 * At this time the "plugin" does not create it's own menu?
 *
 * @since       0.1
*/
add_action('admin_init', array('ZUI_WpAdditionalImageSizes', 'registerWpAisSettings') );
add_filter('intermediate_image_sizes', array('ZUI_WpAdditionalImageSizes', 'setAddtionalSizesInWpOptions'), 1);
add_filter('attachment_fields_to_edit', array('ZUI_WpAdditionalImageSizes', 'appendAttachmentFieldsWithAdditionalSizes'), 11, 2);


/**
 * Static methods for creating and maintaining additional images sizes
 * Also resizes WordPress predefined image sizes (thumbnail, medium, large)
 * if the size has changed.
 *
 * @version       0.1
 * @uses          ZUI_WpAdminPages
*/
class ZUI_WpAdditionalImageSizes {

    /**
     * This function registers the necessary settings/options
     * for this plugin to work with WordPress Settings API
     *
     * @since       0.1
     * @uses        register_setting() WP function
    */
    public static function registerWpAisSettings() {
        register_setting('aisz_options', 'aisz_sizes');
    }


    /**
     * This function is hooked to intermediate_image_sizes and makes sure that the sizes defined
     * by this plugin get added to the blog options. Because of this, WP will automatically
     * use the sizes defined by this plugin in various situations
     *
     * @since       0.1
     * @uses        ZUI_WpAdditionalImageSizes::getAddtionalSizesFromWpOptions()
     * @uses        update_option() WP function
     * @param       array $sizes
     * @return      array
    */
    public static function setAddtionalSizesInWpOptions($sizes) {
        $ais_sizes = self::getAddtionalSizesFromWpOptions();
        if (!empty($ais_sizes)) {
            foreach ($ais_sizes as $key => $value) {
                $sizes[] = $key;
                update_option("{$key}_size_w", $value['size_w']);
                update_option("{$key}_size_h", $value['size_h']);
                update_option("{$key}_crop", $value['crop']);
            }
        }
        return $sizes;
    }


    /**
     * This function gets the user defined image sizes from the blog options
     *
     * @since       0.1
     * @uses        get_option() WP function
     * @return      array
    */
    public static function getAddtionalSizesFromWpOptions() {
        return get_option('aisz_sizes');
    }


    /**
     * This function returns WordPress' predefined image sizes
     *
     * @since       0.1
     * @return      array
     * @uses        get_option() WP function
    */
    public static function getWpPredefinedImageSizes() {
        $wp_size_names = array('thumbnail', 'medium', 'large' );
        $wp_preset_array = array();
        foreach ( $wp_size_names as $size_name ) {
            $wp_preset_array[$size_name] = array(
                    'size_w'    => get_option("{$size_name}_size_w"),
                    'size_h'    => get_option("{$size_name}_size_h"),
                    'crop'      => get_option("{$size_name}_crop")
                );
        }
        return $wp_preset_array;
    }


    /**
     * This function returns ALL image sizes (name, size_w, size_h, crop)
     * for all images (predefined WP and User defined)
     *
     * @since       0.1
     * @return      array
     * @uses        ZUI_WpAdditionalImageSizes::getWpPredefinedImageSizes()
     * @uses        ZUI_WpAdditionalImageSizes::getAddtionalSizesFromWpOptions()
    */
    public static function getAllImageSizes() {
        $wp_sizes = self::getWpPredefinedImageSizes();
        $user_sizes = self::getAddtionalSizesFromWpOptions();
        $all_images_sizes = array_merge($wp_sizes, $user_sizes);
        return $all_images_sizes;
    }


    /**
     * This function is almost a replica of image_size_input_fields() which is found in
     * wp-admin/includes/media.php. It is hooked onto attachment_fields_to_edit. Unfortunately
     * WordPress doesn't provide an easy way to filter the data in the function
     * image_attachment_fields_to_edit itself, so we append the html from image_size_input_field().
     *
     * @since       0.1
     * @uses        ZUI_WpAdditionalImageSizes::getAddtionalSizesFromWpOptions()
     * @uses        image_downsize() WP Function
     * @param       required string $form_fields
     * @param       required object $post
     * @return      string
    */
    public static function appendAttachmentFieldsWithAdditionalSizes($form_fields, $post) {

        // Protect from being view in Media editor where there are no sizes
        if ( isset($form_fields['image-size']) ) {
            $out = NULL;
            $size_names = array();
            $ais_user_sizes = self::getAddtionalSizesFromWpOptions();
            if (is_array($ais_user_sizes)) {
                foreach($ais_user_sizes as $key => $value) {
                    $size_names[$key] = $key;
                }
            }
            foreach ( $size_names as $size => $name ) {
                $downsize = image_downsize($post->ID, $size);

                // is this size selectable?
                $enabled = ( $downsize[3] || 'full' == $size );
                $css_id = "image-size-{$size}-{$post->ID}";

                // if this size is the default but that's not available, don't select it
                if ( (isset($checked) && $checked && !$enabled) || !isset($checked) )
                    $checked = FALSE;

                // if $checked was not specified, default to the first available size that's bigger than a thumbnail
                if ( !$checked && $enabled && 'thumbnail' != $size )
                    $checked = $size;

                $html = "<div class='image-size-item'><input type='radio' ".( $enabled ? '' : "disabled='disabled'")."name='attachments[$post->ID][image-size]' id='{$css_id}' value='{$size}'".( $checked == $size ? " checked='checked'" : '') ." />";

                $html .= "<label for='{$css_id}'>" . __($name). "</label>";
                // only show the dimensions if that choice is available
                if ( $enabled )
                    $html .= " <label for='{$css_id}' class='help'>" . sprintf( __("(%d&nbsp;&times;&nbsp;%d)"), $downsize[1], $downsize[2] ). "</label>";

                $html .= '</div>';

                $out .= $html;
            }
            $form_fields['image-size']['html'] .= $out;
        } // End protect from Media editor

        return $form_fields;
    }


    /**
     * This echoes the HTML for the admin page
     *
     * @since       0.1
     * @uses        ZUI_WpAdditionalImageSizes::managePost()
     * @uses        ZUI_WpAdditionalImageSizes::getAddtionalSizesFromWpOptions()
     * @uses        settings_fields() WP function
    */
    public static function viewOptionsPage() {
        $messages = self::managePost();
        $size_crop = ( isset($_POST['ais_size_crop']) ) ? $_POST['ais_size_crop']
                                                        : FALSE;
?>
            <?php if (isset($messages['errors'])) { ?>
            <div class="error below-h2" id="message">
                <p><strong>Something(s) went wrong:</strong></p>
                <ul>
                    <?php foreach ($messages['errors'] as $error) { ?>
                    <li><?php echo $error; ?></li>
                    <?php } ?>
                </ul>
            </div>
            <?php } ?>
            <?php if (isset($messages['success'])) { ?>
            <div class="updated below-h2" id="message">
                <p><strong>That went quite well:</strong></p>
                <ul>
                    <?php foreach ($messages['success'] as $success) { ?>
                    <li><?php echo $success; ?></li>
                    <?php } ?>
                </ul>
            </div>
            <?php } ?>
            <form method="post" action="">
                    <?php settings_fields('aisz_sizes'); ?>
                    <?php
                    $ais_user_sizes = self::getAddtionalSizesFromWpOptions();
                    ?>
                <table cellspacing="0" class="widefat page fixed">
                    <thead>
                        <tr>
                            <th class="manage-column column-author" scope="col">Delete</th>
                            <th class="manage-column column-title" scope="col">Size name</th>
                            <th class="manage-column column-author" scope="col">Width</th>
                            <th class="manage-column column-author" scope="col">Height</th>
                            <th class="manage-column column-author" scope="col">Crop?</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="manage-column column-author" scope="col">Delete</th>
                            <th class="manage-column column-title" scope="col">Size name</th>
                            <th class="manage-column column-author" scope="col">Width</th>
                            <th class="manage-column column-author" scope="col">Height</th>
                            <th class="manage-column column-author" scope="col">Crop?</th>
                        </tr>
                    </tfoot>
                        <?php
                        if (!empty($ais_user_sizes)) {
                            foreach ($ais_user_sizes as $key => $value) {
                                ?>
                    <tr class="alternate iedit" id="<?php echo $key; ?>">
                        <th class="check-column" scope="row"><input type="checkbox" value="<?php echo $key; ?>" name="ais_size_delete[]"/></th>
                        <td><strong><?php echo $key; ?></strong></td>
                        <td><?php echo $value['size_w']; ?></td>
                        <td><?php echo $value['size_h']; ?></td>
                        <td><?php echo ($value['crop']) ? 'yes' : 'no'; ?></td>
                    </tr>
                            <?php
                            }
                            ?>
                    <tr class="alternate iedit form-table">
                        <th class="manage-column column-author" scope="col"><strong><label for="ais_new_size">New:</label></strong></th>
                        <td><strong><input type="text" name="ais_size_name" id="ais_new_size" value="<?php echo (isset($messages['errors']) && isset($_POST['ais_size_name'])) ? $_POST['ais_size_name'] : ''; ?>" /></strong></td>
                        <td><input type="text" name="ais_size_w" class="small-text" value="<?php echo (isset($messages['errors']) && isset($_POST['ais_size_w'])) ? $_POST['ais_size_w'] : ''; ?>" /></td>
                        <td><input type="text" name="ais_size_h" class="small-text" value="<?php echo (isset($messages['errors']) && isset($_POST['ais_size_h'])) ? $_POST['ais_size_h'] : ''; ?>" /></td>
                        <td><input type="checkbox" name="ais_size_crop" value="1" <?php echo (isset($messages['errors'])) ? ((1 == $size_crop) ? 'checked="checked"' : '') : ''; ?> /></td>
                    </tr>
                        <?php
                        }
                        else {
                            ?>
                    <tr class="alternate iedit form-table">
                        <th class="check-column" scope="row"><input type="checkbox" value="" name="ais_size_delete[]"/></th>
                        <td><strong><input type="text" name="ais_size_name" value="<?php echo (isset($messages['errors'])) ? $_POST['ais_size_name'] : ''; ?>" /></strong></td>
                        <td><input type="text" name="ais_size_w" class="small-text" value="<?php echo (isset($messages['errors'])) ? $_POST['ais_size_w'] : ''; ?>" /></td>
                        <td><input type="text" name="ais_size_h" class="small-text" value="<?php echo (isset($messages['errors'])) ? $_POST['ais_size_h'] : ''; ?>" /></td>
                        <td><input type="checkbox" name="ais_size_crop" value="1" <?php echo (isset($messages['errors'])) ? ((1 == $size_crop) ? 'checked="checked"' : '') : ''; ?> /></td>
                    </tr>
                        <?php
                        }
                        ?>
                </table>
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" />
                </p>
            </form>
            <h3>Create missing/changed image sizes</h3>
            <p>
                When you add an additional image size that size is automatically created for all NEW images you upload.  <br />
                After creating new image sizes above you need to create the missing image sizes.  <br />
                This feature will also resize the <a href="options-media.php">predefined WordPress image sizes</a> (thumbnail, medium, large) if you have edited those.
            </p>
            <form method="post" action="">
                    <?php settings_fields('aisz_sizes'); ?>
                <input type="hidden" name="regenerate_images" value="true" />
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Generate copies of new sizes'); ?>" />
                    <br /><br />
                    <input type="checkbox" name="show_skipped" id="show_skipped" value="1" style="margin-left: 8px;" /> <label for="show_skipped">Show skipped image messages</label>
                </p>
            </form>
<?php
    }


    /**
     * This function handles POST's that are made from the additional image sizes admin screen. It
     * returns an array of messages, successs and error messages. I would like this to be a little
     * less ugly but that would probably mean rewriting the plugin from scratch and this works, so...
     *
     * @since       0.1
     * @uses        ZUI_WpAdditionalImageSizes::getAddtionalSizesFromWpOptions()
     * @uses        ZUI_WpAdditionalImageSizes::getWpPredefinedImageSizes()
     * @uses        absint() WP function
     * @uses        update_option() WP function
     * @uses        get_option() WP function
     * @uses        $_POST
     * @return      array
    */
    public static function managePost() {

        if ( isset($_POST['option_page']) && $_POST['option_page'] != 'aisz_sizes' )
            return false; // Without this, I'm not doing anything!

        // The request appears valid, let's see what the user wanted. There are two possibilities:
        // 1. regenerate copies of existing images,
        // 2. add/remove a size

        // First possibility, the user wants to regenerate copies of existing images. We run
        // self::regenerateImages() and return it's return value
        if ( isset($_POST['regenerate_images']) && $_POST['regenerate_images']) {
            return self::regenerateImages();
        }

        // Second possibility, the user wants to add a new size, or delete an exising one. First, we'll
        // initialize some variables:
        $messages = array();
        $ais_user_sizes = self::getAddtionalSizesFromWpOptions();
        $ais_predef_sizes = self::getWpPredefinedImageSizes();

        $size_crop = ( isset($_POST['ais_size_crop']) ) ? $_POST['ais_size_crop']
                                                        : FALSE;

        // Add a size
        if ( isset($_POST['ais_size_w']) ) { // Must have a $_POST to do this

            // Sizes must be an integer
            if (!absint($_POST['ais_size_w'])) {
                // Zero is fine as well
                if ($_POST['ais_size_w'] != 0) {
                    $messages['errors']['width'] = __('The width you entered is not valid.');
                }
            }
            // Sizes must be an integer
            if (!absint($_POST['ais_size_h'])) {
                // Zero is fine as well
                if ($_POST['ais_size_h'] != 0) {
                    $messages['errors']['height'] = __('The height you entered is not valid.');
                }
            }
            // At least one of width and height must be set
            if ($_POST['ais_size_w'] == 0 && $_POST['ais_size_h'] == 0) {
                $messages['errors']['width_height'] = __('Width and heigh can\'t both be 0 (zero)');
            }
            // This will probably never be true
            if ( $size_crop && 1 != $size_crop ) {
                $messages['errors'][] = __('I received some unexpected data. I didn\'t know what to do with it. Sorry!');
            }
            // We need a name for that size you know
            if (!isset($_POST['ais_size_name']) || $_POST['ais_size_name'] == '') {
                $messages['errors']['size_name'] = __('Please enter a name for this size');
            }
            if (isset($ais_user_sizes[$_POST['ais_size_name']])) {
                $messages['errors'][] = __('A size with this name already exists');
            }
            if (isset($ais_predef_sizes[$_POST['ais_size_name']])) {
                $messages['errors'][] = __('This size name is reserved, please choose another one');
            }

            // There were no errors, so we're gonna go ahead and add the new size
            if (empty($messages['errors'])) {
                $ais_user_sizes[$_POST['ais_size_name']] = array(
                        'size_w'    => $_POST['ais_size_w'],
                        'size_h'    => $_POST['ais_size_h'],
                        'crop'      => $size_crop
                    );
                update_option('aisz_sizes', $ais_user_sizes);
                $messages['success'][] = 'An additional image size named <strong>' . $_POST['ais_size_name'] . '</strong> was added.';
            }

        }

        // Remove a size
        if (isset($_POST['ais_size_delete'])) {
            // If none of size_name, height and width were set, but delete IS set, the
            // user just wanted to delete something. No need to bug him with validation errors.
            if (isset($messages['errors']['width_height']) && isset($messages['errors']['size_name'])) {
                // But if crop was set, maybe the user wanted to add a size anyway?
                if ( $size_crop && 1 == $size_crop) {
                    /* Do nothing */
                } else {
                    unset($messages['errors']);
                }
            }
            foreach ($_POST['ais_size_delete'] as $delete) {
                // $delete holds the size name
                unset($ais_user_sizes[$delete]);
                $messages['success'][] = "The size named <strong>$delete</strong> was deleted";
            }
            update_option('aisz_sizes', $ais_user_sizes);
            $ais_user_sizes = get_option('aisz_sizes');
        }

        // When no new size was entered and no existing size was deleted we don't have to send any
        // validation errors.
        if (isset($messages['errors']['width_height']) && isset($messages['errors']['size_name']) && !isset($_POST['ais_size_delete'])) {
            if ( $size_crop && 1 == $size_crop ) {
                    // Crop was set, maybe the user wanted to add a size anyway?
                }
                else {
                    unset($messages['errors']);
                    $messages['errors'][] = 'Nothing added, nothing deleted.';
                }
        }

        return $messages;
    }


    /**
     * This function looks for images that don't have copies of the sizes defined by this plugin, and
     * then tries to make those copies. It returns an array of messages divided into error messages and
     * success messages. It only makes copies for 10 images at a time.
     *
     * @since       0.1
     * @uses        ZUI_WpAdditionalImageSizes::getAllImageAttachments()
     * @uses        ZUI_WpAdditionalImageSizes::getWpPredefinedImageSizes()
     * @uses        getAllImageSizes()
     * @uses        apply_filters() WP function
     * @uses        wp_upload_dir() WP function
     * @uses        wp_get_attachment_metadata() WP function
     * @uses        wp_update_attachment_metadata() WP function
     * @uses        get_post_meta() WP function
     * @uses        image_make_intermediate_size() WP function
     * @uses        image_resize() WP function
     * @uses        get_option() WP function
     * @return      array
    */
    function regenerateImages() {
        // This can take a while, so we'll keep track of execution time to exit prematurely when
        // it gets too high.
        $start = strtotime('now');
        $max_execution_time = ini_get('max_execution_time');

        // Let's divide that max_execution_time by 2 just to be on the safe side (we don't know
        // when WordPress started to run so $now is off as well).
        $max_execution_time = $max_execution_time / 2;

        $messages = array();
        $images = self::getAllImageAttachments();
        $sizes = apply_filters('intermediate_image_sizes', array('thumbnail', 'medium', 'large'));
        $basedir = wp_upload_dir();
        $basedir = $basedir['basedir'];

        $wp_image_sizes = self::getWpPredefinedImageSizes();
        $all_image_sizes = self::getAllImageSizes();

        if (!empty($images)) {
            foreach ($images as $image) {
                $metadata = wp_get_attachment_metadata($image->ID);

                $file = get_post_meta($image->ID, '_wp_attached_file', true);
                // could we use the guid for this instead of another query?

                foreach ($sizes as $size) {

                    // Manage how long we execute this in one shot to prevent timeouts
                    $now = strtotime('now');
                    $current_execution_time = $now - $start;
                    if ($max_execution_time - $current_execution_time < 2) {
                        break;
                    }

                    $size_width = $all_image_sizes[$size]['size_w'];
                    $size_height = $all_image_sizes[$size]['size_h'];
                    $size_crop = $all_image_sizes[$size]['crop'];

                    // Check to see:
                    //      if the size does not exist yet AND is a user defined size
                    //      OR if the size has changed (for WP predefined sizes)
                    // The logic goes if the image is landscape then test against the width not matching and if portrait then test against the height
                    // Need to test this against an image that could defy this logic (or a size not created because it was too small)
                    if ( ( !isset($metadata['sizes'][$size]) && !array_key_exists($size, $wp_image_sizes) ) ||
                        (
                            (isset($metadata['sizes'][$size]) && !empty($all_image_sizes[$size]['size_w']) && $metadata['sizes'][$size]['width'] > $metadata['sizes'][$size]['height'] && $metadata['sizes'][$size]['width'] != $all_image_sizes[$size]['size_w'])
                            || (isset($metadata['sizes'][$size]) && !empty($all_image_sizes[$size]['size_h']) && $metadata['sizes'][$size]['height'] > $metadata['sizes'][$size]['width'] && $metadata['sizes'][$size]['height'] != $all_image_sizes[$size]['size_h'])
                        ) )
                        {
                        $image_path = $basedir . '/' . $file;
                        $result = image_make_intermediate_size(
                            $image_path,
                            $size_width,
                            $size_height,
                            $size_crop
                        );
                        if ($result) {
                            $metadata['sizes'][$size] = array(
                                'file' => $result['file'], 'width' => $result['width'], 'height' => $result['height']
                            );
                            wp_update_attachment_metadata($image->ID, $metadata);
                            $messages['success'][] = 'RESIZED: "' . $image->post_title . '" to size "' . $size . '"';

                        } else {
                            // Sick of looking at the skipped messages
                            if ( isset($_POST['show_skipped']) ) {
                                // Assumed the image was too small to be created/resized so just send a tentative success message
                                $messages['success'][] = 'SKIPPED: "' . $image->post_title . '" is already smaller than the requested size "' . $size . '"';
                            }
                        }
                    } else {
                        // Sick of looking at the skipped messages and we all know the predefined sizes exist anyway
                        if ( isset($_POST['show_skipped']) && !array_key_exists($size, $wp_image_sizes) ) {
                            $messages['success'][] =  'SKIPPED: "' . $size . '" already exists for "' . $image->post_title . '"';
                        }
                    }
                }
                $now = strtotime('now');
                $current_execution_time = $now - $start;
                if ($max_execution_time - $current_execution_time < 2) {
                    $messages['success'][] = '<strong>Not quite finished yet.<br />We had to stop the script midway because it had been running for too long.<br />Just press the button again to continue where we left off.</strong>';
                    break;
                }
            }
        }
        if (empty($messages)) $messages['success'][] = 'All is well, no new copies needed to be created.';
        return $messages;
    }


    /**
     * Gets an array of images uploaded on the blog. Returns false when no result was found
     *
     * @since       0.1
     * @uses        get_posts() WP function
     * @return      array|boolean
    */
    function getAllImageAttachments() {
        /* Get attachments */
        $args = array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'numberposts' => -1,
            'post_status' => null,
            'post_parent' => null, // any parent
        );
        $attachments = get_posts($args);

        if (empty($attachments))
            return false;
        else
            return $attachments;
    }

}
