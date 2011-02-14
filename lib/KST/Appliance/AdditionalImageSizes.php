<?php
/**
 * Class for creating and inserting help into pages created by KST_AdminPage
 * Parent class included on KST load so KST can have core options without theme or plugin
 *
 * @package     KitchenSinkHTML5Base
 * @subpackage  Core
 * @version     0.1
 * @since       0.1
 * @author      zoe somebody
 * @link        http://beingzoe.com/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @uses        KST_Kitchen
 * @uses        KST_Appliance_Options
*/


/**
 * Companion classes to encapsulate access to admin menu global arrays $menu and $submenu
 * Likely to be included in future WP core (version 3.2 ???)
 *
 * @since       0.1
 * @see         WP_AdminMenuSection
 * @see         WP_AdminMenuItem
 * @uses        AdminMenu.php
*/
if ( !function_exists('add_admin_menu_section') && !class_exists('WP_AdminMenuSection') ) {
    require_once KST_DIR_VENDOR . '/mikeschinkel/WP/WP/AdminMenu.php';
}


/**
 * Companion class to quickly create WP admin menu/pages and auto build the forms if necessary
 *
 * Uses an array to manage all information pertaining to menu/page creation and the settings to register/manage
 *
 * @since       0.1
 * @uses        ZUI_WpAdminPages
 * @uses        ZUI_FormHelper
 * @see         WpAdminPages.php
 * @see         FormHelper.php
*/
require_once KST_DIR_VENDOR . '/beingzoe/zui_php/ZUI/WpAdminPages.php';


/**
 * Companion class to create and manage additional image sizes for post/pages/custom
 *
 * @since       0.1
 * @uses        ZUI_WpAdminPages
 * @uses        ZUI_FormHelper
 * @see         WpAdminPages.php
 * @see         FormHelper.php
*/
require_once KST_DIR_VENDOR . '/beingzoe/zui_php/ZUI/WpAdditionalImageSizes.php';


/**
 * Class methods for creating and accessing help files for site/blog owner
 * Creates WP admin menu/pages (not 'options' pages ;)
 *
 * @since       0.1
 * @uses        ZUI_WpAdminPages
 * @uses        AdminMenu.php
*/
class KST_Appliance_AdditionalImageSizes extends KST_Appliance {

    /**#@+
     * @since       0.1
     * @access      protected
     * @var         string
    */
    protected static $_menu_is_loaded = FALSE;
    /**#@-*/


    /**#@+
     * @since       0.1
     * @access      protected
     * @var         array
    */
    /**#@-*/


    /**
     * @since       0.1
    */
    public function __construct(&$kitchen) {

        if ( !is_admin() )
            return FALSE; // Nothing to do

        // Every kitchen needs the basic settings
        $appliance_settings = array(
                    'friendly_name'       => 'KST Appliance: Media: Additional Image Sizes',
                    'prefix'              => 'kst_wp_ais',
                    'developer'           => 'zoe somebody',
                    'developer_url'       => 'http://beingzoe.com/',
                );

        // Add Help
        $appliance_help = array (
                array (
                    'page' => 'Features',
                    'section' => 'Utility',
                    'title' => 'Additional Image Sizes <em>(image size management)</em>',
                    'content_source' => array('KST_Appliance_AdditionalImageSizes', 'help')
                ),
                array (
                    'page' => 'WordPress',
                    'section' => 'Blog Posts',
                    'title' => 'Additional Image Sizes <em>(image size management)',
                    'content_source' => array('KST_Appliance_AdditionalImageSizes', 'help')
                )
            );

        // Declare as core
        $this->_is_core_appliance = TRUE;
        // Common appliance
        parent::_init($kitchen, $appliance_settings, NULL, $appliance_help); // $appliance_options not being sent because we are adding two menu items

        $parent_slug = array('kst','media'); //upload.php

        if ( !self::$_menu_is_loaded ) {
            $this->_appliance->load('options');
            foreach ($parent_slug as $slug) {
                $appliance_options = array(
                    'parent_slug'           => $slug,
                    'menu_slug'             => "wp_ais",
                    'menu_title'            => 'Image Sizes',
                    'page_title'            => 'Create and Manage Additional Image Sizes',
                    'capability'            => 'manage_options',
                    'view_page_callback'    => array('ZUI_WpAdditionalImageSizes','viewOptionsPage'),
                    'icon_url'              => NULL,
                    'position'              => NULL,
                    'options'               => array(
                            // NOTE: While this is a good example of using the Options appliance with a callback
                            // instead of auto if you want it to manage your options for you you would need a
                            // single dimension array containing the options to manage
                            // In this case we are  letting the vendor plugin code handle that */
                        )
                    );
                $this->_appliance->options->add($appliance_options);
                self::$_menu_is_loaded = TRUE;
            }
        }

    }


    /**
     * KST_Appliance_Help entry
     * Features: Appliance: Additional Image Sizes
     *
     * @since       0.1
    */
    public static function help() {
        ?>
            <p>
                The Additional Image Sizes plugin enables you to create custom sizes of images for more rich and complex layouts.
                It is based on
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

        <?php
    }


}
