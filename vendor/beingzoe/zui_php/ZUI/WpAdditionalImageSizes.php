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
 *     -choose which sizes to do
 *     -choose an extended time limit for the script to run
 *     -optionally show a list of images that are skipped during image creation
 *
 * Future versions will also offer the ability to delete images when you
 * delete a custom image size.
 *
 * @package     ZUI
 * @subpackage  WordPress
 * @version     0.1.3
 * @since       0.1
 * @author      Walter Vos
 * @link        http://www.waltervos.com/
 * @author      zoe somebody
 * @link        http://beingzoe.com/
 * @link        http://www.waltervos.com/wordpress-plugins/additional-image-sizes/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @todo        Add ability to delete unused images (especially deleted sizes)
 * @todo        While functional this code is not very elegant - optimize and cleanup
*/

if ( !is_admin() )
    return; // not needed in front end ever - speed things up


/**
 * Add hooks for static methods in ZUI_WpAdditionalImageSizes
 * At this time the "plugin" does not create it's own menu?
 *
 * @since       0.1
*/
add_action('admin_init', array('ZUI_WpAdditionalImageSizes', 'registerWpAisSettings') ); // Register our serialized option
add_filter('intermediate_image_sizes', array('ZUI_WpAdditionalImageSizes', 'addSizesToWpIntermediateSizes'), 1); // Tell WP there are more image sizes
add_filter('attachment_fields_to_edit', array('ZUI_WpAdditionalImageSizes', 'appendAttachmentFieldsWithAdditionalSizes'), 11, 2); // Add our sizes to media forms


/**
 * Static methods for creating and maintaining additional images sizes
 * Also resizes WordPress predefined image sizes (thumbnail, medium, large)
 * if the size has changed.
 *
 * @version       0.1
 * @uses          ZUI_WpAdminPages
*/
class ZUI_WpAdditionalImageSizes {

    /**#@+
     * @since       0.1.2
     * @access      protected
    */
    protected static $_images_regenerated_this_attempt = 0;
    protected static $_images_regenerate_from_offset = 0;
    /**#@-*/


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
     * by this plugin get added to the available intermediate_image_sizes. This is how WP will
     * use the sizes defined by this plugin in various situations (i.e. media uploader insert size into post)
     * The actual options are added to the database when a new option is saved during managePost()
     *
     * @since       0.1
     * @uses        ZUI_WpAdditionalImageSizes::getAddtionalSizesFromWpOptions()
     * @uses        update_option() WP function
     * @param       array $sizes
     * @return      array
    */
    public static function addSizesToWpIntermediateSizes($sizes) {
        $aisz_sizes = self::getAddtionalSizesFromWpOptions();
        $aisz_sizes = array_merge($sizes, array_keys($aisz_sizes) );
        return $aisz_sizes;
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
                        <td><?php echo $value['size_w']; ?>px</td>
                        <td><?php echo $value['size_h']; ?>px</td>
                        <td><?php echo ($value['crop']) ? 'crop' : ''; ?></td>
                    </tr>
                            <?php
                            }
                            ?>
                    <tr class="alternate iedit form-table">
                        <th class="manage-column column-author" scope="col"><strong><label for="ais_new_size">New:</label></strong></th>
                        <td><strong><input type="text" name="ais_size_name" id="ais_new_size" value="<?php echo (isset($messages['errors']) && isset($_POST['ais_size_name'])) ? $_POST['ais_size_name'] : ''; ?>" /></strong></td>
                        <td><input type="text" name="ais_size_w" class="small-text" value="<?php echo (isset($messages['errors']) && isset($_POST['ais_size_w'])) ? $_POST['ais_size_w'] : ''; ?>" />px</td>
                        <td><input type="text" name="ais_size_h" class="small-text" value="<?php echo (isset($messages['errors']) && isset($_POST['ais_size_h'])) ? $_POST['ais_size_h'] : ''; ?>" />px</td>
                        <td><input type="checkbox" name="ais_size_crop" value="1" <?php echo (isset($messages['errors'])) ? ((1 == $size_crop) ? 'checked="checked"' : '') : ''; ?> /></td>
                    </tr>
                        <?php
                        }
                        else {
                            ?>
                    <tr class="alternate iedit form-table">
                        <th class="check-column" scope="row"><input type="checkbox" value="" name="ais_size_delete[]"/></th>
                        <td><strong><input type="text" name="ais_size_name" value="<?php echo (isset($messages['errors'])) ? $_POST['ais_size_name'] : ''; ?>" /></strong></td>
                        <td><input type="text" name="ais_size_w" class="small-text" value="<?php echo (isset($messages['errors'])) ? $_POST['ais_size_w'] : ''; ?>" />px</td>
                        <td><input type="text" name="ais_size_h" class="small-text" value="<?php echo (isset($messages['errors'])) ? $_POST['ais_size_h'] : ''; ?>" />px</td>
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
                This feature will also create new sizes for the <a href="options-media.php">predefined WordPress image sizes</a> (thumbnail, medium, large) if you have edited those.
            </p>
            <form method="post" action="">
                <p class="submit">
                    <?php
                        settings_fields('aisz_sizes');
                        $numberposts = ( isset($_POST['numberposts']))          ? $_POST['numberposts']
                                                                                : 50;
                        $set_time_limit = ( isset($_POST['set_time_limit']))    ? $_POST['set_time_limit']
                                                                                : 30;
                        $all_image_sizes = self::getAllImageSizes();
                        $sizes_to_check = ( isset($_POST['sizes_to_check']))    ? $_POST['sizes_to_check']
                                                                                : 'all';

                    ?>
                    <input type="hidden" name="regenerate_images" value="true" />
                    <input type="hidden" name="offset" id="offset" value="<?php echo self::$_images_regenerate_from_offset; ?>" />
                    <input type="submit" class="button-primary" value="<?php _e('Generate copies of new sizes'); ?>" />
                    <?php if ( 0 < self::$_images_regenerated_this_attempt && 0 < self::$_images_regenerate_from_offset ) {  ?>
                        We checked <strong><?php echo self::$_images_regenerated_this_attempt; ?></strong> images on the last attempt and there are still more to do!
                    <?php } ?>
                </p>
                <div style="margin: 18px 8px;">
                    <p> Choose what size(s) to check
                        <select name="sizes_to_check" id="sizes_to_check">
                            <option value='all'<?php if ('all' == $sizes_to_check) echo " selected='selected'"; ?>>All</option>
                            <option value="custom_only"<?php if ('custom_only' == $sizes_to_check) echo " selected='selected'"; ?>>Custom sizes only</option>
                            <option value="wordpress_only"<?php if ('wordpress_only' == $sizes_to_check) echo " selected='selected'"; ?>>WordPress sizes only</option>
                            <?php foreach ($all_image_sizes as $size => $values) {
                                echo "{$size} == {$sizes_to_check}<br />";
                                $selected = ($size == $sizes_to_check)  ? " selected='selected'"
                                                                        : "";
                                echo "<option value='{$size}'{$selected}>{$size}</option>";
                            } ?>
                        </select>
                    </p>
                    <p>
                        <label for="numberposts">How many images to attempt per batch?</label>
                        <input type="text" name="numberposts" id="numberposts" value="<?php echo $numberposts; ?>" size="3" /> <br />
                        <em>
                            We will attempt to process as many images as you think your sytem can handle in a single page load.
                            You should increase this number until you start seeing the friendly 'we had to stop the script message'
                            (or something bad happens like the world ends and you have a blank screen ;). Note that the number your
                            system can handle varies greatly depending on how many sizes you are asking it to do at once.
                        </em>
                    </p>
                    <p>
                        <label for="set_time_limit">Attempt to increase the max execution time?</label>
                        <input type="text" name="set_time_limit" id="set_time_limit" value="<?php echo $set_time_limit; ?>" size="2" /> <br />
                        <em>
                            The time in seconds beyond the default maximum execution time on your server you would like to extend processing.
                            This will allow you to process more images at once. However if you are on shared hosting be kind
                            to your fellow site owners. To be safe we've capped this at 60 seconds. So if the default is 30 and you set this to 60
                            it means your script will run for nearly 90 seconds (we shave off a bit to play it safe).
                            This only works if you're server is not running in "safe mode". Set to "0" to disable.
                        </em>
                    </p>
                    <p>
                        <input type="checkbox" name="show_skipped" id="show_skipped" value="1" <?php if (isset($_POST['show_skipped'])) echo "checked='checked' "; ?> />
                        <label for="show_skipped">Show skipped image messages</label>
                    </p>
                </div>
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
                // WP needs individual entries - used to be in intermediate_image_sizes filter
                // WP checks to see if the images exist before allowing them to be selected for use so this should be safe
                // Leaving note in for a few version in case I am wrong ;)
                update_option("{$_POST['ais_size_name']}_size_w", $_POST['ais_size_w']);
                update_option("{$_POST['ais_size_name']}_size_h", $_POST['ais_size_h']);
                update_option("{$_POST['ais_size_name']}_crop", $size_crop);

                $messages['success'][] = 'An additional image size named <strong>' . $_POST['ais_size_name'] . '</strong> was added.<br />You should generate copies of this new size now.';
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
                delete_option("{$delete}_size_w");
                delete_option("{$delete}_size_h");
                delete_option("{$delete}_crop");
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
     * success messages. It only makes copies for X images at a time based on form selection.
     *
     * @since       0.1
     * @uses        ZUI_WpAdditionalImageSizes::getAllImageAttachments()
     * @uses        ZUI_WpAdditionalImageSizes::getWpPredefinedImageSizes()
     * @uses        ZUI_WpAdditionalImageSizes::getAddtionalSizesFromWpOptions()  since 0.1.3
     * @uses        ZUI_WpAdditionalImageSizes::getAllImageSizes() since 0.1.3
     * @uses        ZUI_WpAdditionalImageSizes::mightBreakScriptToAvoidTimeout()
     * @uses        wp_upload_dir() WP function
     * @uses        wp_get_attachment_metadata() WP function
     * @uses        wp_update_attachment_metadata() WP function
     * @uses        get_post_meta() WP function
     * @uses        image_make_intermediate_size() WP function
     * @uses        get_option() WP function
     * @return      array
    */
    function regenerateImages() {

        // Set it up
        $start = strtotime('now');
        $max_execution_time = ini_get('max_execution_time');
        $max_execution_time = round($max_execution_time / 1.25); // Let's divide that max_execution_time by 1.25 just to play it a little bit safe (we don't know when WordPress started to run so $now is off as well)
        $did_increase_time_limit = FALSE; // Flag to be able to let them know we had to increase the time limit and still tell them we succeeded - clumsy as hell
        $did_finish_batch = TRUE; // Flag to know if we finished the batch or aborted - set to FALSE if we break the processing loop
        $did_resize_an_image = FALSE; // Flag to know if we actually resized anything to customize success message in conjunction with $did_finish_all
        $did_finish_all = FALSE; // Flag to know if we finished them ALL - set to TRUE if a query for images comes up empty
        $messages = array(); // Sent back to managePost() and viewOptionsPage() to print results
        $i = 0; // How many images we managed to process before we stopped the script to prevent a timeout
        $offset = ( isset($_POST['offset']) && 0 < $_POST['offset'] )   ? ($_POST['offset'] - 1) // Back up one in case we didn't get to all the sizes for that one
                                                                        : 0; // Where we should start from; for get_post() in getAllImageAttachments()
        $numberposts = ( isset($_POST['numberposts']) && 0 < $_POST['numberposts'] )    ? $_POST['numberposts'] // Back up one in case we didn't get to all the sizes for that one
                                                                                        : 50; // Where we should start from; for get_post() in getAllImageAttachments()
        $basedir = wp_upload_dir();
        $basedir = $basedir['basedir'];
        $wp_image_sizes = self::getWpPredefinedImageSizes();

        // Set the sizes we are going to do based on site/blog owner choice
        if ( isset($_POST['sizes_to_check']) && 'all' != $_POST['sizes_to_check'] ) {
            switch ($_POST['sizes_to_check']) {
                case "custom_only":
                    $all_image_sizes = self::getAddtionalSizesFromWpOptions();
                    $messages['success'][] = 'Checked custom image sizes only.';
                break;
                case "wordpress_only":
                    $all_image_sizes = $wp_image_sizes;
                    $messages['success'][] = 'Checked WordPress sizes (thumbnail, medium, large) only.';
                break;
                default:
                    $all_image_sizes = array_intersect_key(self::getAddtionalSizesFromWpOptions(), array($_POST['sizes_to_check'] => array())); // This is super sloppy but for now there can be only one
                    $messages['success'][] = "Checked <strong>{$_POST['sizes_to_check']}</strong> image size only.";
                break;
            }
        } else {
            $all_image_sizes = self::getAllImageSizes();
            $messages['success'][] = 'Checked all custom and WordPress sizes (thumbnail, medium, large) image sizes.';
        }

        // Get an image batch with the quantity they requested
        $images = self::getAllImageAttachments( array('offset' => $offset, 'numberposts' => $numberposts) ); // Get image attachements starting at the offset

        // Loop the batch and resize as necessary
        if (!empty($images)) {
            foreach ($images as $image) {

                $metadata = wp_get_attachment_metadata($image->ID);
                $file = get_post_meta($image->ID, '_wp_attached_file', true); // could we use the guid for this instead of another query?

                foreach ($all_image_sizes as $size => $values) {

                    // Check to see if we are close to timing out
                    $do_break_script = self::mightBreakScriptToAvoidTimeout($start, $max_execution_time);
                    if ( TRUE === $do_break_script) {
                        $did_finish_batch = FALSE;
                        $did_finish_all = FALSE;
                        break 2;
                    } else if ( 'extended' === $do_break_script ) {
                        $messages['success'][] = 'We increased the time limit at your request.';
                        $did_increase_time_limit = TRUE;
                    }

                    $size_width = $all_image_sizes[$size]['size_w'];
                    $size_height = $all_image_sizes[$size]['size_h'];
                    $size_crop = $all_image_sizes[$size]['crop'];

                    // Check to see:
                    //      if the size does not exist yet AND is a user defined size
                    //      OR if the size has changed (for WP predefined sizes)
                    if ( ( !isset($metadata['sizes'][$size]) && !array_key_exists($size, $wp_image_sizes) )
                         || ( array_key_exists($size, $wp_image_sizes) && isset($metadata['sizes'][$size]) && !empty($all_image_sizes[$size]['size_w']) && ($metadata['sizes'][$size]['width'] != $all_image_sizes[$size]['size_w'] && $metadata['sizes'][$size]['height'] != $all_image_sizes[$size]['size_h']) )
                        )
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
                            $messages['success'][] = '<strong>RESIZED:</strong> "' . $image->post_title . '" to size "' . $size . '"';
                            $did_resize_an_image = TRUE;
                        } else {
                            // Sick of looking at the skipped messages
                            if ( isset($_POST['show_skipped']) ) {
                                // Assumed the image was too small to be created/resized so just send a tentative success message
                                $messages['success'][] = 'SKIPPED: "' . $image->post_title . '" is already smaller than the requested size "' . $size . '"';
                            }
                        }
                    } else {
                        // Sick of looking at the skipped messages and we all know the predefined sizes exist anyway
                        if ( isset($_POST['show_skipped']) ) {
                            $messages['success'][] =  'SKIPPED: "' . $size . '" already exists for "' . $image->post_title . '"';
                        }
                    } // End if we resized or not
                } // End sizes loop
                $i++;
                $offset++;
            } // End images loop
        } else {
            $did_finish_all = TRUE;
        }// End if we have images

        // Since we finished the batch we should did a quick check for one more
        if ( $did_finish_batch ) {
            $images = self::getAllImageAttachments( array('offset' => $offset, 'numberposts' => 1) ); // we might have finished with this batch
            if (empty($images))
                $did_finish_all = TRUE; // Yay we are done!
        }

        // Set the number of images we got to this attempt
        self::$_images_regenerated_this_attempt = $i;

        if ( !$did_resize_an_image && $did_finish_all ) {
            $messages['success'][] = 'All is well, no new copies needed to be created.';
            $i = 0; // Reset because we are good!
        } else if ( $did_finish_all ) {
            $messages['success'][] = '<strong>All done!</strong>';
            $i = 0; // Reset because we finished!
        } else if ( $did_finish_batch ) { // We finished the request batch quantity but not all of the images
            $messages['success'][] = "We finished checking a whole batch of {$i}.<br />Consider increasing the number of images per batch until you start seeing the friendly 'we had to stop the script message'.";
            $messages['success'][] = "<strong>But...we're not quite finished yet. Press the generate button again to continue where we left off.</strong>";
            self::$_images_regenerate_from_offset = $offset;
        } else { // We aborted the script to avoid timing out
            $messages['success'][] = "We checked {$i} images out of an attempted {$numberposts}.";
            $messages['success'][] = "<strong>Not quite finished yet. Just press the generate button again to continue where we left off.</strong>";
            self::$_images_regenerate_from_offset = $offset;
        }

        return $messages;
    }


    /**
     * Determine if we should stop running this script before we timeout
     * Attempt to increase the php time limit if not in safe mode
     *
     * @since       0.1.2
     * @param       required timestamp $start strtotime time the script started running
     * @param       required int $max_execution_time
    */
    public static function mightBreakScriptToAvoidTimeout($start, $max_execution_time) {

        static $set_time_limit = FALSE;
        static $_do_try_to_extend_time_limit = TRUE; // When creating new sizes we try to increase the time limit if not in safe mode; TRUE if not in safe_mode; I was going to just name this is_safe_mode but other criteria might come up for not extending
        static $_do_extend_time_limit = 'maybe'; // If not in safe_mode this tracks whether we 'maybe', 'can', or did (which becomes an int value of the new execution time)
        static $_new_start = FALSE; // Used to set a new start time if we make it to 'can' $_do_extend_time_limit

        // If this server isn't in safe_mode then we can try to extend the time limit at the last second
        if ( TRUE === $_do_try_to_extend_time_limit && ini_get('safe_mode') )
            $_do_try_to_extend_time_limit = FALSE; // safe_mode is no go

        // If we haven't checked yet and we aren't in safe_mode
        if ( 'maybe' == $_do_extend_time_limit && $_do_try_to_extend_time_limit ) {
            $_do_extend_time_limit = 'can'; // We have a tenative yes we can
            // Set the actual time out if we get to use it - keep it reasonable
            if ( FALSE === $set_time_limit && isset($_POST['set_time_limit']) && 0 == $_POST['set_time_limit'] ) {
                $_do_try_to_extend_time_limit = FALSE;
                $set_time_limit = 0;
            } else if ( FALSE === $set_time_limit && isset($_POST['set_time_limit']) && is_numeric($_POST['set_time_limit']) && 60 <= $_POST['set_time_limit'] ) {
                $set_time_limit = $_POST['set_time_limit'];
            } else if ( FALSE === $set_time_limit ) {
                $set_time_limit = 60;
            }
        }

        // On subsequent passes if we have an integer that is our new time limit - set new times
        if ( is_int($_do_extend_time_limit )) {
            $start = $_new_start;
            $max_execution_time = $_do_extend_time_limit; // That is an integer when we have a $new_start
        }

        $now = strtotime('now');
        $current_execution_time = $now - $start;

        // We check against the original max_execution_time first
        if ($max_execution_time - $current_execution_time < 2) {
            // We are about to time out but can we extend the time limit?
            if ( 'can' == $_do_extend_time_limit ) {
                $_new_start = strtotime('now'); // We can so we will
                $_do_extend_time_limit = ($set_time_limit - 2); // Keep our original 2 second cushion
                set_time_limit($set_time_limit);
                return 'extended'; // Keep on rockin the free world
            }
            return TRUE; // Nope about to expire
        }
        return FALSE; // Keep on rockin the free world
    }


    /**
     * Gets an array of images uploaded on the blog. Returns false when no result was found
     *
     * @since       0.1
     * @uses        get_posts() WP function
     * @return      array|boolean
    */
    public static function getAllImageAttachments($args = array()) {
        $defaults = array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'numberposts' => -1,
            'offset' => 0,
            'post_status' => null,
            'post_parent' => null, // any parent
        );
        $args = array_merge($defaults, $args);
        $attachments = get_posts($args);

        if (empty($attachments))
            return false;
        else
            return $attachments;
    }

}
