<?php
/**
 * Additional Image Sizes for WordPress Image Media
 *
 * Create and delete additional image sizes (in addition to the predefined WordPress
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
 *     -deletes images for deleted sizes and cleans up attachment metadata
 *     -choose which sizes to do
 *     -choose an extended time limit for the script to run
 *     -optionally show a list of images that are skipped during image creation
 *
 * @package     ZUI
 * @subpackage  WordPress
 * @version     0.1.7
 * @since       0.1
 * @author      Walter Vos
 * @link        http://www.waltervos.com/
 * @author      zoe somebody
 * @link        http://beingzoe.com/
 * @link        http://www.waltervos.com/wordpress-plugins/additional-image-sizes/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @todo        Add ability to delete UNUSED images (not used in posts/pages/custom)
 * @todo        Add ability to edit existing custom sizes
 * @todo        Ajaxify add new image sizes
 * @todo        Ajaxify create new image sizes
 * @todo        Attempt to auto continue (via ajax) creating new image sizes after a delay
 * @todo        While functional some of this code is not very elegant - optimize and cleanup
 * @todo        There are already too many variables - attempt to simplify logic
 * @todo        Get in on the core 3.2 image size discussion and talk to filosofo
*/

if ( !is_admin() )
    return; // not needed in front end ever - speed things up


/**
 * Add hooks for static methods in ZUI_WpAdditionalImageSizes
 * The init file creates the menu/page that uses this class
 *
 * @since       0.1
*/
add_action('admin_init', array('ZUI_WpAdditionalImageSizes', 'registerSettings') ); // Register our serialized option
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
     * This function gets the user defined image sizes from the blog options
     *
     * @since       0.1
     * @uses        get_option() WP function
     * @param       optional boolean $force
     * @return      array
    */
    public static function getSizesCustom($force = FALSE) {
        static $sizes_custom = NULL; // cache this - speed things up
        if ( NULL === $sizes_custom || TRUE === $force) { // Allow forcing a new get if option might have been updated
            $sizes_custom = get_option('aisz_sizes');
            if (!is_array($sizes_custom))
                $sizes_custom = array();
        }
        return $sizes_custom;
    }


    /**
     * This function returns WordPress' predefined image sizes
     *
     * @since       0.1
     * @return      array
     * @uses        get_option() WP function
    */
    public static function getSizesWp() {
        static $sizes_wp = NULL; // cache this - speed things up
        if ( NULL === $sizes_wp) {
            $wp_size_names = array('thumbnail', 'medium', 'large' );
            $sizes_wp = array();
            foreach ( $wp_size_names as $size_name ) {
                $sizes_wp[$size_name] = array(
                        'size_w'    => get_option("{$size_name}_size_w"),
                        'size_h'    => get_option("{$size_name}_size_h"),
                        'crop'      => get_option("{$size_name}_crop")
                    );
            }
        }
        return $sizes_wp;
    }


    /**
     * This function returns ALL image sizes (name, size_w, size_h, crop)
     * for all images (predefined WP and User defined)
     *
     * @since       0.1
     * @return      array
     * @uses        ZUI_WpAdditionalImageSizes::getSizesWp()
     * @uses        ZUI_WpAdditionalImageSizes::getSizesCustom()
    */
    public static function getAllImageSizes() {
        static $sizes_all = NULL; // cache this - speed things up
        if ( NULL === $sizes_all) {
            $sizes_all = array_merge(self::getSizesWp(), self::getSizesCustom());
        }
        return $sizes_all;
    }


    /**
     * Method for updating ALL custom sizes in the WP options table
     * Also used to optionally delete individual sizes at the same time
     * but must still update ALL remaining sizes
     *
     * @since       0.1.5
     * @uses        update_option() WP function
     * @param       optional array $sizes_update
     * @param       optional array $sizes_delete only the first dimension of size keys is necessary
    */
    public static function updateOptionAiszSizes($sizes_update = NULL, $sizes_delete = NULL) {
        // Set the messages array to return
        $messages = array();
        // Get the current custom sizes
        $sizes_custom = self::getSizesCustom();
        // Check to see if we are deleting any first
        if ( NULL !== $sizes_delete ) {
            foreach ($sizes_delete as $delete) {
                // $delete holds the size name
                unset($sizes_custom[$delete]); // Remove that size
                delete_option("{$delete}_size_w");
                delete_option("{$delete}_size_h");
                delete_option("{$delete}_crop");
                $messages['success'][] = "The size named <strong>$delete</strong> was deleted";
            }
            $messages['success'][] = "You should delete copies of old size(s) now.";
        }
        if ( NULL !== $sizes_update ) {
            foreach ($sizes_update as $size => $values) {
                $sizes_custom[$size] = array(
                        'size_w'    => $values['size_w'],
                        'size_h'    => $values['size_h'],
                        'crop'      => $values['crop']
                    );
                // WP needs individual entries for each size as well
                update_option("{$size}_size_w", $values['size_w']);
                update_option("{$size}_size_h", $values['size_h']);
                update_option("{$size}_crop", $values['crop']);
                $messages['success'][] = "An additional image size named <strong>{$size}</strong> was added.";
            }
            $messages['success'][] = "You should generate copies of new size(s) now.";
        }
        update_option('aisz_sizes', $sizes_custom); // Update OUR option
        self::getSizesCustom(TRUE); // Update the cached $sizes_custom
        return $messages;
    }


    /**
     * This function is hooked to 'admin_init' and registers the necessary
     * settings/options for this plugin to work with WordPress Settings API
     *
     * @since       0.1
     * @uses        register_setting() WP function
    */
    public static function registerSettings() {
        register_setting('aisz_options', 'aisz_sizes');

    }


    /**
     * This function is hooked to intermediate_image_sizes and makes sure that the sizes defined
     * by this plugin get added to the available intermediate_image_sizes. This is how WP will
     * use the sizes defined by this plugin in various situations (i.e. media uploader insert size into post)
     * The actual options are added to the database when a new option is saved during managePost()
     *
     * @since       0.1
     * @uses        ZUI_WpAdditionalImageSizes::getSizesCustom()
     * @uses        update_option() WP function
     * @param       array $sizes
     * @return      array
    */
    public static function addSizesToWpIntermediateSizes($sizes) {
        $sizes_custom = self::getSizesCustom();
        $sizes_custom = array_merge($sizes, array_keys($sizes_custom) );
        return $sizes_custom;
    }


    /**
     * This function is almost a replica of image_size_input_fields() which is found in
     * wp-admin/includes/media.php. It is hooked onto attachment_fields_to_edit. Unfortunately
     * WordPress doesn't provide an easy way to filter the data in the function
     * image_attachment_fields_to_edit itself, so we append the html from image_size_input_field().
     *
     * @since       0.1
     * @uses        ZUI_WpAdditionalImageSizes::getSizesCustom()
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
            $sizes_custom = self::getSizesCustom();
            if (is_array($sizes_custom)) {
                foreach($sizes_custom as $key => $value) {
                    $size_names[$key] = $key;
                }
            }
            foreach ( $size_names as $size => $label ) {
                $downsize = image_downsize($post->ID, $size);

                // is this size selectable?
                $enabled = ( $downsize[3] || 'full' == $size );
                $css_id = "image-size-{$size}-{$post->ID}";

                // We must do a clumsy search of the existing html to determine is something has been checked yet
                if ( FALSE === strpos('checked="checked"', $form_fields['image-size']['html']) ) {

                    if ( empty($check) )
                        $check = get_user_setting('imgsize'); // See if they checked a custom size last time

                    $checked = '';

                    // if this size is the default but that's not available, don't select it
                    if ( $size == $check || str_replace(" ", "", $size) == $check ) {
                        if ( $enabled )
                            $checked = " checked='checked'";
                        else
                            $check = '';
                    } elseif ( !$check && $enabled && 'thumbnail' != $size ) {
                        // if $check is not enabled, default to the first available size that's bigger than a thumbnail
                        $check = $size;
                        $checked = " checked='checked'";
                    }
                }
                $html = "<div class='image-size-item' style='min-height: 50px; margin-top: 18px;'><input type='radio' " . disabled( $enabled, false, false ) . "name='attachments[$post->ID][image-size]' id='{$css_id}' value='{$size}'$checked />";
                //$html = "<div class='image-size-item' style='min-height: 50px; margin-top: 18px;'><input type='radio' ".( $enabled ? '' : "disabled='disabled'")."name='attachments[$post->ID][image-size]' id='{$css_id}' value='{$size}'".( $checked == $size ? " checked='checked'" : '') ." />";

                $html .= "<label for='{$css_id}'>$label</label>";
                // only show the dimensions if that choice is available
                if ( $enabled )
                    $html .= " <label for='{$css_id}' class='help'>" . sprintf( "(%d&nbsp;&times;&nbsp;%d)", $downsize[1], $downsize[2] ). "</label>";

                $html .= '</div>';

                $out .= $html;
            }
            $form_fields['image-size']['html'] .= $out;
        } // End protect from Media editor

        return $form_fields;
    }


    /**
     * Manage variables and cookies for options page
     *
     * @since       0.1.5
     * @uses        ZUI_WpAdditionalImageSizes::getAllImageSizes()
     * @uses        ZUI_WpAdditionalImageSizes::getSizesCustom()
     * @return      array of variables to be extracted and used to fill form values
    */
    public static function getAndSetOptionsPageVariables() {
        $variables = array();
        // Manage custom sizes
        if (isset($_COOKIE['aisz_generate_form'])) {
            $have_cookie_aisz_generate_form = TRUE;
            extract( unserialize(stripcslashes($_COOKIE['aisz_generate_form'])) );
        } else {
            $have_cookie_aisz_generate_form = FALSE;
        }
        // Create new/missing sizes
        $variables['sizes_to_check'] = ( isset($_POST['sizes_to_check']))   ? $_POST['sizes_to_check']
                                                                            : (( $have_cookie_aisz_generate_form )  ? $sizes_to_check : 'all');
        $variables['numberposts'] = ( isset($_POST['numberposts']))         ? $_POST['numberposts']
                                                                            : (( $have_cookie_aisz_generate_form )  ? $numberposts : 50); // Shared by create/delete for now
        $variables['set_time_limit'] = ( isset($_POST['set_time_limit']))   ? $_POST['set_time_limit']
                                                                            : (( $have_cookie_aisz_generate_form )  ? $set_time_limit : 30); // Shared by create/delete for now
        $variables['simulate_resize'] = ( isset($_POST['simulate_resize'])) ? $_POST['simulate_resize']
                                                                            : (( $have_cookie_aisz_generate_form && !isset($_POST['sizes_to_check']) )
                                                                                                                    ? $simulate_resize : FALSE);
        $variables['show_skipped'] = ( isset($_POST['show_skipped']))       ? $_POST['show_skipped']
                                                                            : (( $have_cookie_aisz_generate_form && !isset($_POST['sizes_to_check']) )
                                                                                                                    ? $show_skipped : FALSE);
        // All the checkboxes with cookies should be like this ;)
        if ( isset($_POST['replace_sizes']) ) { // Just keep using it
            $variables['replace_sizes'] = $_POST['replace_sizes'];
        } else if ( isset($_POST['sizes_to_check']) ) { // They said NO
            $variables['replace_sizes'] = FALSE;
        } else if ( $have_cookie_aisz_generate_form ) {
            $variables['replace_sizes'] = $replace_sizes;
        } else {
            $variables['replace_sizes'] = TRUE;
        }
        $variables['advanced_create_sizes'] = ( isset($_POST['advanced_create_sizes'])) ? $_POST['advanced_create_sizes']
                                                                                        : (( $have_cookie_aisz_generate_form && !isset($_POST['advanced_create_sizes']) && !isset($_POST['delete_images_for_deleted_sizes']) )
                                                                                                                    ? $advanced_create_sizes : 'hide'); // This is getting stupid and I should just use if's or find a better way

        // Delete images for deleted sizes
        // No cookies for delete form

        // Save those settings - must check to see if headers have been sent since 0.1.7
        if ( !headers_sent() ) {
            $serialized_variables = serialize($variables);
            $expiry = 60 * 60 * 24 * 60 + time();
            setcookie('aisz_generate_form', $serialized_variables, $expiry);
        }
        // Add a couple more things we don't want to save for Delete images for deleted sizes
        $variables['simulate_delete'] = ( !isset($_POST['delete_images_for_deleted_sizes']))    ? 1
                                                                                                : (( isset($_POST['simulate_delete']) ) ? 1 : 0);
        $variables['advanced_delete'] = ( isset($_POST['advanced_delete']))                     ? $_POST['advanced_delete']
                                                                                                : 'hide';
        // Necessary for all
        $variables['all_image_sizes'] = self::getAllImageSizes();
        $variables['sizes_custom'] = self::getSizesCustom();
        // Give it back
        return $variables;
    }


    /**
     * This echoes the HTML for the admin page
     *
     * @since       0.1
     * @uses        global $_wp_additional_image_sizes WP since 0.1.5
     * @uses        ZUI_WpAdditionalImageSizes::managePost()
     * @uses        ZUI_WpAdditionalImageSizes::getAndSetOptionsPageVariables()
     * @uses        settings_fields() WP function
    */
    public static function viewOptionsPage() {
        global $_wp_additional_image_sizes;
        $messages = self::managePost();
        extract(self::getAndSetOptionsPageVariables());
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
            <form method="post" action="" id="form_sizes">
                    <?php settings_fields('aisz_sizes'); ?>
                <table cellspacing="0" class="widefat page fixed">
                    <thead>
                        <tr>
                            <th class="manage-column column-author" scope="col">Delete</th>
                            <th class="manage-column column-title" scope="col">Size name</th>
                            <th class="manage-column column-author" scope="col" title="Width OR Height may be blank for proportional resize/crop.">Width</th>
                            <th class="manage-column column-author" scope="col" title="Width OR Height may be blank for proportional resize/crop.">Height</th>
                            <th class="manage-column column-author" scope="col">Crop?</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="manage-column column-author" scope="col">Delete</th>
                            <th class="manage-column column-title" scope="col">Size name</th>
                            <th class="manage-column column-author" scope="col" title="Width OR Height may be blank for proportional resize/crop.">Width</th>
                            <th class="manage-column column-author" scope="col" title="Width OR Height may be blank for proportional resize/crop.">Height</th>
                            <th class="manage-column column-author" scope="col">Crop?</th>
                        </tr>
                    </tfoot>

                        <?php
                        if (!empty($sizes_custom)) {
                            foreach ($sizes_custom as $key => $value) {
                                ?>
                    <tr class="alternate iedit" id="<?php echo $key; ?>">
                        <th class="check-column" scope="row"><input type="checkbox" value="<?php echo $key; ?>" name="ais_size_delete[]"/></th>
                        <td><strong><?php echo $key; ?></strong></td>
                        <td><?php
                            if (!empty($value['size_w']))
                                echo "{$value['size_w']}px";
                            else
                                echo "<abbr title='proportional'>&prop;</abbr>";
                        ?></td>
                        <td><?php
                            if (!empty($value['size_h']))
                                echo "{$value['size_h']}px";
                            else
                                echo "<abbr title='proportional'>&prop;</abbr>";
                        ?></td>
                        <td><?php echo ($value['crop']) ? 'crop' : ''; ?></td>
                    </tr>
                            <?php
                            } // End display custom sizes
                        } // End if !empty

                        // CLUMSY duplicate code - same as above - but not sure what to do with this yet but we need to acknowledge and be aware of these
                        if (!empty($_wp_additional_image_sizes)) {
                            foreach ($_wp_additional_image_sizes as $key => $value) {
                                ?>
                    <tr class="alternate iedit" id="<?php echo $key; ?>" title="Additional uneditable sizes added by another plugin or theme via set_post_thumbnail_size() or add_image_size(). These sizes are ignored while creating or deleting sizes with this plugin.">
                        <th class="check-column" scope="row"></th>
                        <td><em><?php echo $key; ?></em></td>
                        <td><em><?php
                            if (!empty($value['width']))
                                echo "{$value['width']}px";
                            else
                                echo "<abbr title='proportional'>&prop;</abbr>";
                        ?></em></td>
                        <td><em><?php
                            if (!empty($value['height']))
                                echo "{$value['height']}px";
                            else
                                echo "<abbr title='proportional'>&prop;</abbr>";
                        ?></em></td>
                        <td><em><?php echo ($value['crop']) ? 'crop' : ''; ?></em></td>
                    </tr>
                            <?php
                            } // End display custom sizes
                        } // End if !empty
                            ?>

                    <tr class="alternate iedit form-table">
                        <th class="manage-column column-author" scope="col"><strong><label for="ais_new_size">New:</label></strong></th>
                        <td><strong><input type="text" name="ais_size_name[0]" id="ais_new_size" value="<?php echo (isset($messages['errors']) && isset($_POST['ais_size_name'][0])) ? $_POST['ais_size_name'][0] : ''; ?>" /></strong></td>
                        <td><input type="text" name="ais_size_w[0]" class="small-text" title="Width OR Height may be blank for proportional resize/crop." value="<?php echo (isset($messages['errors']) && isset($_POST['ais_size_w'][0])) ? $_POST['ais_size_w'][0] : ''; ?>" />px</td>
                        <td><input type="text" name="ais_size_h[0]" class="small-text" title="Width OR Height may be blank for proportional resize/crop." value="<?php echo (isset($messages['errors']) && isset($_POST['ais_size_h'][0])) ? $_POST['ais_size_h'][0] : ''; ?>" />px</td>
                        <td><input type="checkbox" name="ais_size_crop[0]" value="1" <?php echo (isset($messages['errors'])) ? ((isset($_POST['ais_size_crop'][0])) ? 'checked="checked"' : '') : ''; ?> /></td>
                    </tr>
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
            <form method="post" action="" id="form_create_sizes" class="aisz_settings">
                <p class="submit">
                    <?php
                        settings_fields('aisz_sizes');
                    ?>
                    <input type="hidden" name="advanced_create_sizes" value="<?php if ($advanced_create_sizes) echo $advanced_create_sizes; ?>"  />
                    <input type="hidden" name="regenerate_images" value="true" />
                    <input type="hidden" name="offset" id="offset_create" value="<?php echo isset($_POST['regenerate_images']) ? self::$_images_regenerate_from_offset : 0; ?>" />
                    <input type="submit" class="button-primary" value="<?php _e('Generate copies of new sizes'); ?>" />
                    <?php if ( isset($_POST['regenerate_images']) && 0 < self::$_images_regenerated_this_attempt && 0 < self::$_images_regenerate_from_offset ) {  ?>
                        We checked <strong><?php echo self::$_images_regenerated_this_attempt; ?></strong> images on the last attempt and there are still more to do!
                    <?php } ?>
                </p>
                <div class="">
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
                        <input type="checkbox" name="simulate_resize" id="simulate_resize" value="1" <?php if ($simulate_resize) echo "checked='checked' "; ?> />
                        <label for="simulate_resize">Check but don't actually resize</label>
                    </p>
                    <p>
                        <a id="advanced_create_sizes" class="show_hide_advanced">Advanced settings</a>
                    </p>
                    <div class="aisz_advanced_settings advanced_create_sizes show_hide">
                        <p>
                            <label for="numberposts">How many images to attempt per batch?</label>
                            <input type="text" name="numberposts" id="numberposts" value="<?php echo $numberposts; ?>" size="3" /> <br />
                            <em>
                                We will attempt to process as many images as you think your sytem can handle in a single page load.
                                You should increase this number until you start seeing the friendly 'we had to stop the script message'
                                (or something bad happens like the world ends and you have a blank screen ;). Note that the number your
                                system can handle varies greatly depending on how many sizes you are asking it to do at once and whether
                                you are just checking or actually resizing images.
                            </em>
                        </p>
                        <p>
                            <label for="set_time_limit_create">Attempt to increase the max execution time?</label>
                            <input type="text" name="set_time_limit" id="set_time_limit_create" value="<?php echo $set_time_limit; ?>" size="2" /> <br />
                            <em>
                                The time in seconds beyond the default maximum execution time on your server you would like to extend processing.
                                This will allow you to process more images at once. However if you are on shared hosting be kind
                                to your fellow site owners. To be safe we've capped this at 60 seconds. So if the default is 30 and you set this to 60
                                it means your script will run for nearly 90 seconds (we shave off a bit to play it safe).
                                This only works if you're server is not running in "safe mode". Set to "0" to disable.
                            </em>
                        </p>
                        <p>
                            <input type="checkbox" name="replace_sizes" id="replace_sizes" value="1" <?php if ($replace_sizes) echo "checked='checked' "; ?> />
                            <label for="replace_sizes">Replace WordPress sizes</label> <br />
                            <em>
                                There is no easy way to remove WordPress 'thumbnail', 'medium', and 'large' sizes except by deleting them while we create
                                the new size. If this box is checked when making new predefined WordPress image sizes the old image will be deleted,
                                ostensibly replacing that image size.
                            </em>
                        </p>
                        <p>
                            <input type="checkbox" name="show_skipped" id="show_skipped" value="1" <?php if ($show_skipped) echo "checked='checked' "; ?> />
                            <label for="show_skipped">Show skipped image messages</label>
                        </p>
                    </div>
                </div>
            </form>
            <h3>Delete images for deleted sizes</h3>
            <p>
                When you delete an image size that size will not be created for all NEW images you upload.  <br />
                However, images created for deleted sizes still exist on the server as well as the image attachment metadata for those sizes.  <br />
                This feature will physically delete those images from the server as well as the image attachment meta data for those sizes. <br />
                <strong>Use this feature at your own risk. There is no undo.</strong>
            </p>
            <p>
                <a id="advanced_delete" class="show_hide_advanced">Show form</a>
            </p>
            <form method="post" action="" id="form_delete" class="advanced_delete aisz_settings show_hide">
                <p class="submit">
                    <?php
                        settings_fields('aisz_sizes');
                    ?>
                    <input type="hidden" name="advanced_delete" value="<?php if ($advanced_delete) echo $advanced_delete; ?>"  />
                    <input type="hidden" name="offset" id="offset_delete" value="<?php echo isset($_POST['delete_images_for_deleted_sizes']) ? self::$_images_regenerate_from_offset : 0; ?>" />
                    <input type="submit" class="button-primary" name="delete_images_for_deleted_sizes" id="delete_images_for_deleted_sizes" value="<?php _e('Delete images of deleted sizes'); ?>" />
                    <?php if ( isset($_POST['delete_images_for_deleted_sizes']) && 0 < self::$_images_regenerated_this_attempt && 0 < self::$_images_regenerate_from_offset ) {  ?>
                        We checked <strong><?php echo self::$_images_regenerated_this_attempt; ?></strong> images on the last attempt and there are still more to do!
                    <?php } ?>
                </p>
                <div class="aisz_advanced_settings">
                    <p>
                        <input type="checkbox" name="simulate_delete" id="simulate_delete" value="1" <?php if ($simulate_delete) echo "checked='checked' "; ?> />
                        <label for="simulate_delete">Check but don't actually delete</label> <br />
                        <em>
                            The delete images feature works by checking the metadata for each image and comparing any sizes you created but have since deleted. If a size is listed in the image attachment metadata
                            but not listed in your current custom sizes the image will be deleted. Be very sure you you will not need that size again.
                            WordPress default sizes are unaffected and cannot be deleted. We highly recommend first running this
                            with "Check but don't actually delete" turned on once to make sure it is doing what you want/expect. Also note
                            that if some other plugin created additional image sizes that you can choose from the media uploader to insert into posts/pages/custom
                            and those sizes don't exist here they will be deleted as well. So please check before actually deleting ;)
                        </em>
                    </p>
                    <p>
                        <label for="numberposts">How many images to attempt per batch?</label>
                        <input type="text" name="numberposts" id="numberposts" value="<?php echo $numberposts; ?>" size="3" /> <br />
                        <em>
                            Works the same as the "how many" in create images. If you are just checking you could turn this up pretty high.
                        </em>
                    </p>
                    <p>
                        <label for="set_time_limit_delete">Attempt to increase the max execution time?</label>
                        <input type="text" name="set_time_limit" id="set_time_limit_delete" value="<?php echo $set_time_limit; ?>" size="2" /> <br />
                        <em>
                            Works the same as the time limit in create images.
                        </em>
                    </p>
                    <p>
                        <strong>N.B.:</strong>
                        <em>
                            If you were using a theme that used <code>set_post_thumbnail_size()</code> or <code>add_image_size()</code> to create new image sizes but have
                            since switched to a theme that does not or uses different sizes via <code>add_image_size()</code> you will see a lot of images
                            that will be deleted the first time you run "Delete images of deleted sizes" even though you have not created any custom sizes yet.
                            If you plan on switching back to another theme that used those sizes we recommend not using the delete feature of this tool as we have
                            no way of determining (at this point) what sizes belonged to your deleted custom sizes and belonged to an old theme or plugins custom
                            sizes added via <code>add_image_size()</code>. There is a workaround to this found in the FAQ in readme.txt.
                        </em>
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
     * @uses        ZUI_WpAdditionalImageSizes::getSizesCustom()
     * @uses        ZUI_WpAdditionalImageSizes::getSizesWp()
     * @uses        absint() WP function
     * @uses        update_option() WP function
     * @uses        get_option() WP function
     * @uses        $_POST
     * @return      array
    */
    public static function managePost() {

        if ( isset($_POST['option_page']) && $_POST['option_page'] != 'aisz_sizes' )
            return false; // Without this, I'm not doing anything!

        $messages = array();

        // The request appears valid, let's see what the user wanted.
        // 1. regenerate copies of existing images,
        // 2. delete images
        // 3. add/remove a size

        // First possibility, the user wants to regenerate copies of existing images. We run
        // self::regenerateImages() and return it's return value
        if ( isset($_POST['regenerate_images']) ) {
            return self::regenerateImages();
        }

        // Second possibility, the user wants to delete copies of existing images. We run
        // self::regenerateImages() and return it's return value
        if ( isset($_POST['delete_images_for_deleted_sizes']) ) {
            return self::regenerateImages();
        }

        // Third possibility, the user wants to add a new size, or delete an exising one. First, we'll
        // initialize some variables:
        $sizes_custom = self::getSizesCustom();
        $sizes_wp = self::getSizesWp();

        // Add a size
        if ( isset($_POST['ais_size_name']) ) { // Must have a $_POST to do this

            $update_count = count($_POST['ais_size_name']);
            //echo "Updating {$update_count} many sizes<br />";

            for ($i=0; $i < $update_count; $i++) {

                    $size_crop = ( isset($_POST['ais_size_crop'][$i]) ) ? $_POST['ais_size_crop'][$i]
                                                        : FALSE;

                    // We need a name for that size you know
                    if (!isset($_POST['ais_size_name'][$i]) || $_POST['ais_size_name'][$i] == '') {
                        $messages['errors']['size_name'] = __('Please enter a name for this size');
                    }

                    // Sizes must be an integer
                    if ( !is_numeric($_POST['ais_size_w'][$i]) ) { //!absint($_POST['ais_size_w'][$i])
                        // Blank or Zero is fine as well
                        if ( !empty($_POST['ais_size_w'][$i]) ) {
                            $messages['errors']['width'] = __('The width you entered is not valid.');
                        }
                    }

                    // Sizes must be an integer
                    if ( !is_numeric($_POST['ais_size_h'][$i]) ) { //!absint($_POST['ais_size_h'][$i])
                        // Blank or Zero is fine as well
                        if ( !empty($_POST['ais_size_h'][$i]) ) {
                            $messages['errors']['height'] = __('The height you entered is not valid.');
                        }
                    }

                    // At least one of width and height must be set
                    if ( empty($_POST['ais_size_w'][$i]) && empty($_POST['ais_size_h'][$i]) ) {
                        $messages['errors']['width_height'] = __('Width and height can\'t both be blank or "0".<br /><em>One or the other may be left blank to indicate proportional resize/crop.</em>');
                    }

                    // Is it a name they already added? Or a WP reserved name?
                    if (isset($sizes_custom[$_POST['ais_size_name'][$i]])) {
                        $messages['errors'][] = __('A size with this name already exists');
                    } else if (isset($sizes_wp[$_POST['ais_size_name'][$i]])) {
                        $messages['errors'][] = __('This size name is reserved, please choose another one');
                    }

                    // There were no errors, so add it to the $sizes_update array and update it
                    if (empty($messages['errors'])) {
                        $sizes_update = array();
                        $sizes_update[$_POST['ais_size_name'][$i]] = array(
                                'size_w'    => $_POST['ais_size_w'][$i],
                                'size_h'    => $_POST['ais_size_h'][$i],
                                'crop'      => $size_crop
                            );
                        $messages += self::updateOptionAiszSizes($sizes_update);
                    } // End if empty($messages['errors'])
            } // End loop of custom sizes to update
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
            $messages += self::updateOptionAiszSizes(NULL, $_POST['ais_size_delete']);
        }

        // When no new size was entered and no existing size was deleted we
        // don't have to send any validation errors.
        // Crop is ignored for obvious reasons
        if (isset($messages['errors']['width_height']) && isset($messages['errors']['size_name'])) { // && !isset($_POST['ais_size_delete'][0])
            unset($messages['errors']);
            $messages['errors'][] = 'Nothing added, nothing deleted.';
        }

        return $messages;
    }


    /**
     * This method looks for images that don't have copies of the sizes defined by this plugin, and
     * then tries to make those copies. It returns an array of messages divided into error messages and
     * success messages. It only makes copies for X images at a time based on form selection.
     *
     * Also the same routine (for now) for deleting images and cleaning the attachment metadata
     *
     * @since       0.1
     * @uses        global $_wp_additional_image_sizes WP since 0.1.5
     * @uses        ZUI_WpAdditionalImageSizes::getAllImageAttachments()
     * @uses        ZUI_WpAdditionalImageSizes::getSizesWp()
     * @uses        ZUI_WpAdditionalImageSizes::getSizesCustom()  since 0.1.3
     * @uses        ZUI_WpAdditionalImageSizes::getAllImageSizes() since 0.1.3
     * @uses        ZUI_WpAdditionalImageSizes::mightBreakScriptToAvoidTimeout()
     * @uses        wp_upload_dir() WP function
     * @uses        wp_get_attachment_metadata() WP function
     * @uses        wp_update_attachment_metadata() WP function
     * @uses        get_post_meta() WP function
     * @uses        image_make_intermediate_size() WP function
     * @uses        get_option() WP function
     * @return      array
     * @todo        abstract create/delete better
     * @todo        minimize the number of variables, especially the flags
    */
    function regenerateImages() {
        global $_wp_additional_image_sizes; // Need this to prevent deleting legit set_post_thumbnail_size() and add_image_size() images

        // Set it up
        $start = strtotime('now');
        $max_execution_time = ini_get('max_execution_time');
        $max_execution_time = round($max_execution_time / 1.25); // Let's divide that max_execution_time by 1.25 just to play it a little bit safe (we don't know when WordPress started to run so $now is off as well)
        $did_increase_time_limit = FALSE; // Flag to be able to let them know we had to increase the time limit and still tell them we succeeded - clumsy as hell
        $did_finish_batch = TRUE; // Flag to know if we finished the batch or aborted - set to FALSE if we break the processing loop
        $did_delete_an_image = FALSE; // Flag to know if we deleted (or would have) any images or attacment metadata
        $did_resize_an_image = FALSE; // Flag to know if we resized (or would have) any images to customize success message in conjunction with $did_finish_all
        $did_finish_all = FALSE; // Flag to know if we finished them ALL - set to TRUE if a query for images comes up empty
        $messages = array(); // Sent back to managePost() and viewOptionsPage() to print results
        $i = 0; // How many images we managed to process before we stopped the script to prevent a timeout
        $offset = ( isset($_POST['offset']) && 0 < $_POST['offset'] )   ? ($_POST['offset'] - 1) // Back up one in case we didn't get to all the sizes for that one
                                                                        : 0; // Where we should start from; for get_post() in getAllImageAttachments()
        $numberposts = ( isset($_POST['numberposts']) && 0 < $_POST['numberposts'] )    ? $_POST['numberposts'] //
                                                                                        : 50;
        $basedir = wp_upload_dir();
        $basedir = $basedir['basedir'];
        $sizes_wp = self::getSizesWp();

        // Set the sizes we are going to do based on site/blog owner choice
        // For delete this defaults to all
        if ( isset($_POST['sizes_to_check']) && 'all' != $_POST['sizes_to_check'] ) {
            switch ($_POST['sizes_to_check']) {
                case "custom_only":
                    $sizes_to_check = self::getSizesCustom();
                    $messages['success'][] = 'Checked custom image sizes only.';
                break;
                case "wordpress_only":
                    $sizes_to_check = $sizes_wp;
                    $messages['success'][] = 'Checked WordPress sizes (thumbnail, medium, large) only.';
                break;
                default:
                    $sizes_to_check = array_intersect_key(self::getSizesCustom(), array($_POST['sizes_to_check'] => array())); // This is super sloppy but for now there can be only one
                    $messages['success'][] = "Checked <strong>{$_POST['sizes_to_check']}</strong> image size only.";
                break;
            }
        } else if ( isset($_POST['sizes_to_check']) && 'all' == $_POST['sizes_to_check'] ) {
            $sizes_to_check = self::getAllImageSizes();
            $messages['success'][] = 'Checked all custom and WordPress (thumbnail, medium, large) image sizes.';
        } else if ( isset($_POST['delete_images_for_deleted_sizes']) ) {
            $sizes_to_check = self::getAllImageSizes();
            $messages['success'][] = 'Checked all images metadata.';
        }

        // Get an image batch with the quantity they requested
        $images = self::getAllImageAttachments( array('offset' => $offset, 'numberposts' => $numberposts) ); // Get image attachements starting at the offset

        // Loop the batch and resize as necessary
        if (!empty($images)) {
            foreach ($images as $image) {

                $metadata = wp_get_attachment_metadata($image->ID);
                $file = get_post_meta($image->ID, '_wp_attached_file', true);

                if ( isset($_POST['regenerate_images']) ) { // We can skip this if not regenerating - abstract and separate regenerate/delete
                    foreach ($sizes_to_check as $size => $values) {

                        // Check to see if we are close to timing out - GRRR redundancy - we have to check this in the image loop as well if this is a delete - we need to abstract and separate regenerate/delete
                        $do_break_script = self::mightBreakScriptToAvoidTimeout($start, $max_execution_time);
                        if ( TRUE === $do_break_script) {
                            $did_finish_batch = FALSE;
                            $did_finish_all = FALSE;
                            break 2;
                        } else if ( 'extended' === $do_break_script ) {
                            $messages['success'][] = 'We increased the time limit at your request.';
                            $did_increase_time_limit = TRUE;
                        }

                        $size_width = $sizes_to_check[$size]['size_w'];
                        $size_height = $sizes_to_check[$size]['size_h'];
                        $size_crop = $sizes_to_check[$size]['crop'];

                        // We will try to make the size if:
                        //      size does not exist in the image meta data yet
                        //      OR if the size DOES exist in the image meta data but has changed (new size has a width AND metadata width doesn't match new width AND metadata height doesn't match new height)
                        if ( isset($_POST['regenerate_images'])
                             && ( !isset($metadata['sizes'][$size])   // && !array_key_exists($size, $sizes_wp)
                             || ( !empty($sizes_to_check[$size]['size_w']) && ($metadata['sizes'][$size]['width'] != $sizes_to_check[$size]['size_w'] && $metadata['sizes'][$size]['height'] != $sizes_to_check[$size]['size_h']) ) //array_key_exists($size, $sizes_wp) && isset($metadata['sizes'][$size]) &&
                            )
                             ) {
                            $image_path = $basedir . '/' . $file;

                            // Simulate resize or do it for real?
                            if ( isset($_POST['simulate_resize'])) {
                                $result = self::simulate_image_make_intermediate_size(
                                    $image_path,
                                    $size_width,
                                    $size_height,
                                    $size_crop
                                );
                            } else {
                                $result = image_make_intermediate_size(
                                    $image_path,
                                    $size_width,
                                    $size_height,
                                    $size_crop
                                );
                            }

                            // If the image was (or would be) resized
                            if ($result) {
                                if ( isset($_POST['simulate_resize'])) {
                                    // Just a simulation
                                    if ( isset($_POST['replace_sizes']) && array_key_exists($size, $sizes_wp) ) {
                                        $messages['success'][] =  "WOULD REPLACE: {$metadata['sizes'][$size]['file']} for new {$size} size";
                                    }
                                    $messages['success'][] = '<strong>WOULD CREATE:</strong> "' . $image->post_title . '" to size "' . $size . '"';
                                } else {
                                    // WP sizes cannot be deleted later so clean up as we go is in order
                                    // If they want us to and this size is a WP size
                                    if ( isset($_POST['replace_sizes']) && array_key_exists($size, $sizes_wp) ) {
                                        $path_parts = pathinfo($basedir . '/' . $file);
                                        $delete_wp_file = $path_parts['dirname'] . "/" . $metadata['sizes'][$size]['file'];
                                        //$delete_wp_file = $basedir . '/' . $metadata['sizes'][$size]['file'];
                                        $delete_wp_file = str_replace("\\", "/", $delete_wp_file);
                                        // Attempt to delete this old WP size
                                        if ( @unlink($delete_wp_file) ) {
                                            $messages['success'][] =  "REPLACED: {$metadata['sizes'][$size]['file']} for new {$size} size";
                                        } // No alternate message on fail for now
                                    }
                                    // Update the metadata - if a wp replaced size named key is overwritten anyway
                                    $metadata['sizes'][$size] = array(
                                        'file' => $result['file'], 'width' => $result['width'], 'height' => $result['height']
                                    );
                                    wp_update_attachment_metadata($image->ID, $metadata);
                                    $messages['success'][] = '<strong>CREATED:</strong> "' . $image->post_title . '" to size "' . $size . '"';
                                }
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
                            if ( isset( $_POST['show_skipped']) && isset($_POST['simulate_resize']) ) {
                                $messages['success'][] =  'WOULD SKIP: "' . $size . '" already exists for "' . $image->post_title . '"';
                            } else if ( isset($_POST['show_skipped']) ) {
                                $messages['success'][] =  'SKIPPED: "' . $size . '" already exists for "' . $image->post_title . '"';
                            }
                        } // End if we resized or not
                    } // End sizes loop
                }


                if ( !empty($_wp_additional_image_sizes)) {
                    $sizes_to_check_plus_additional = array_merge(array_keys($sizes_to_check), array_keys($_wp_additional_image_sizes));
                } else {
                    $sizes_to_check_plus_additional = array_keys($sizes_to_check);
                }
                $metadata_sizes_not_in_current_all_sizes = array_diff( array_keys($metadata['sizes']), $sizes_to_check_plus_additional ); //sizes_to_check


                if ( isset($_POST['delete_images_for_deleted_sizes']) ) {

                    $metadata_after = $metadata;

                    // Leaving in some of this testing stuff for a couple of versions
                    /*
                    echo "<br />metadata before<pre>";
                    print_r($metadata);
                    echo "</pre><br />";
                    */

                    if ( 0 < count($metadata_sizes_not_in_current_all_sizes) ) {
                        foreach ($metadata_sizes_not_in_current_all_sizes as $defunct_size) {

                            if ( 'post-thumbnail' == $defunct_size)
                                continue; // kludge to protect known named size created by set_post_thumbnail_size()

                                $path_parts = pathinfo($basedir . '/' . $file);
                                $delete_current_file = $path_parts['dirname'] . "/" . $metadata_after['sizes'][$defunct_size]['file'];
                                $delete_current_file = str_replace("\\", "/", $delete_current_file);
                                if ( isset($_POST['simulate_delete']) ) {
                                    $image = wp_load_image( $delete_current_file );
                                    if ( is_resource( $image ) ) {
                                        $messages['success'][] =  "<strong>WOULD DELETE:</strong> {$defunct_size} {$delete_current_file}";
                                        imagedestroy( $image ); // Free up memory
                                    } else {
                                        $messages['errors'][] =  "<strong>Can't find:</strong> {$delete_current_file}<br /><em>The attachment metadata would be deleted</em>";
                                        $messages['success'][] =  "WOULD DELETE METADATA: for {$delete_current_file}";
                                    }
                                } else {
                                    if ( @unlink($delete_current_file) ) {
                                        $messages['success'][] =  "<strong>DELETED:</strong> {$defunct_size} {$delete_current_file}";
                                         unset($metadata_after['sizes'][$defunct_size]); // We unset this in a copy of the metadata array to be sure (and for testing)
                                    } else {
                                        $messages['success'][] =  "Deleted metadata only:  {$defunct_size} {$delete_current_file}";
                                        unset($metadata_after['sizes'][$defunct_size]); // We unset this in a copy of the metadata array to be sure (and for testing)
                                    }
                                }
                                $did_delete_an_image = TRUE;

                                // Check to see if we are close to timing out - GRRR redundancy - we have to check this in the size loop as well
                                $do_break_script = self::mightBreakScriptToAvoidTimeout($start, $max_execution_time);
                                if ( TRUE === $do_break_script) {
                                    $did_finish_batch = FALSE;
                                    $did_finish_all = FALSE;
                                    break 2;
                                } else if ( 'extended' === $do_break_script ) {
                                    $messages['success'][] = 'We increased the time limit at your request.';
                                    $did_increase_time_limit = TRUE;
                                }

                        }
                    } else {
                        $messages['success'][] = "No sizes to delete for {$image->post_title}";
                    }

                    // UPDATE THE METADATA with the removed/deleted sizes
                    if ( !isset($_POST['simulate_delete']) ) {
                        wp_update_attachment_metadata($image->ID, $metadata_after);
                    }

                    /* // Leaving in some of this testing stuff for a couple of versions
                    echo "<br />metadata after<pre>";
                    print_r($metadata_after);
                    echo "</pre><br />";
                    */
                }

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

        // Set the continue form "submit/click" link
        if ( isset($_POST['delete_images_for_deleted_sizes']) )
            $continue_link = "<a id='continue_deleting'>Continue deleting where we left off with the same settings</a>";
        else
            $continue_link = "<a id='continue_creating'>Continue creating where we left off with the same settings</a>";

        // Give them a final status message for this batch
        if ( !$did_resize_an_image && $did_finish_all && !isset($_POST['delete_images_for_deleted_sizes']) ) {
            $messages['success'][] = 'All is well, no new copies created.';
            $i = 0; // Reset because we are done!
        } else if (!$did_delete_an_image && $did_finish_all && isset($_POST['delete_images_for_deleted_sizes']) ) {
            $messages['success'][] = 'All is well, no images to delete.';
            $i = 0; // Reset because we are done!
        } else if ( $did_finish_all ) {
            $messages['success'][] = '<strong>All done!</strong>';
            $i = 0; // Reset because we finished!
        } else if ( $did_finish_batch ) { // We finished the request batch quantity but not all of the images
            $messages['success'][] = "We finished checking a whole batch of {$i}.<br />Consider increasing the number of images per batch until you start seeing the friendly 'we had to stop the script message'.";
            $messages['success'][] = "<strong>But...we're not quite finished yet. {$continue_link} or use the form to adjust settings and continue.</strong>";
            self::$_images_regenerate_from_offset = $offset;
        } else { // We aborted the script to avoid timing out
            $messages['success'][] = "We checked {$i} images out of an attempted {$numberposts}.";
            $messages['success'][] = "<strong>Not quite finished yet. {$continue_link} or use the form to adjust settings and continue.</strong>";
            self::$_images_regenerate_from_offset = $offset;
        }

        return $messages;
    }


    /**
     * Method for testing if an image WOULD be resized
     * Simulates the validation that occurs prior to actually beginning the resize
     * process that would negate the need for or prevent the image from being resized.
     *
     * We use image_make_intermediate_size() to create our images
     * This method recreates that. See media.php
     *
     * @since       0.1.5
     * @uses        wp_load_image() WP function
     * @uses        image_resize_dimensions() WP function
    */
    public static function simulate_image_make_intermediate_size($file, $width, $height, $crop=false) {

        // Begin image_make_intermediate_size($file, $width, $height, $crop=false)
        if ( $width || $height ) {
            // Begin image_resize( $file, $max_w, $max_h, $crop = false, $suffix = null, $dest_path = null, $jpeg_quality = 90 )
            $image = wp_load_image( $file );
            if ( !is_resource( $image ) )
                return FALSE; //return new WP_Error( 'error_loading_image', $image, $file );

            $size = @getimagesize( $file );
            if ( !$size )
                return FALSE; //return new WP_Error('invalid_image', __('Could not read image size'), $file);
            list($orig_w, $orig_h, $orig_type) = $size;

            $dims = image_resize_dimensions($orig_w, $orig_h, $width, $height, $crop); // $max_w, $max_h
            if ( !$dims )
                return FALSE; //return new WP_Error( 'error_getting_dimensions', __('Could not calculate resized image dimensions') );
            list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $dims;
        }

        imagedestroy( $image ); // Free up memory

        // Return value of image_make_intermediate_size()
        return array(
				'file' => basename( $file ),
				'width' => $dst_w,
				'height' => $dst_h,
			);
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

        static $set_time_limit = FALSE; // By default just don't do it
        static $_do_try_to_extend_time_limit = TRUE; // When creating new sizes we try to increase the time limit if not in safe mode; TRUE if not in safe_mode; I was going to just name this is_safe_mode but other criteria might come up for not extending
        static $_do_extend_time_limit = 'maybe'; // If not in safe_mode this tracks whether we 'maybe', 'can', or did (which becomes an int value of the new execution time)
        static $_new_start = FALSE; // Used to set a new start time if we make it to 'can' $_do_extend_time_limit

        // If this server isn't in safe_mode and they didn't disable extended time limit then we can try to extend the time limit at the last second
        if ( TRUE === $_do_try_to_extend_time_limit && (ini_get('safe_mode') || 0 == $_POST['set_time_limit']) ) {
            $_do_try_to_extend_time_limit = FALSE;
            $_do_extend_time_limit = FALSE;
        }

        // If we haven't checked yet and we aren't in safe_mode
        if ( 'maybe' == $_do_extend_time_limit && $_do_try_to_extend_time_limit ) {
            $_do_extend_time_limit = 'can'; // We have a tenative yes we can
            // Set the actual time out if we get to use it - keep it reasonable
            if ( FALSE === $set_time_limit && isset($_POST['set_time_limit']) && 0 == $_POST['set_time_limit'] ) {
                // can't or disabled
                $_do_try_to_extend_time_limit = FALSE;
                $set_time_limit = 0;
            } else if ( FALSE === $set_time_limit && isset($_POST['set_time_limit']) && is_numeric($_POST['set_time_limit']) && 60 >= $_POST['set_time_limit'] ) {
                // Extend time limit by site/blog owner choice
                $set_time_limit = $_POST['set_time_limit'];
            } else if ( FALSE === $set_time_limit ) {
                // Cap their asses at 60 seconds
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
