<?php
/**
 * KST SENSIBLE DEFAULTS FUNCTIONS
 *
 * Functions only required if we are in the WP admin
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  Core
 * @version     0.1
 * @since       0.1
 */

/**
 * Add hook to load core settings menu once the menu is available to add
 */
$kst_core_settings = array(
            /* REQUIRED */
            'friendly_name'       => 'Kitchen Sink HTML5 Plugin Pack',                 // Required; friendly name used by all widgets, libraries, and classes; can be different than the registered theme name
            'prefix'              => 'kst_plugin_pack_',                       // Required; Prefix for namespacing libraries, classes, widgets
            'developer'           => 'zoe somebody',                           // Required; friendly name of current developer; only used for admin display;
            'developer_url'       => 'http://beingzoe.com/',            // Required; full URI to developer website;
        );

/**
 * Define the settings for the core
 */

$kst_core_options = array (
            array(  "name"      => __('Kitchen Sink Base core settings'),
                    "desc"      => __("
                                    <p><em>There are no core settings at this time</em></p>
                                "),
                    "type"      => "section",
                    "is_shut"   => FALSE ),

        array(  "name"  => __('PLUGIN Favorite color'),
                "desc"  => __('Red? Green? Blue?'),
                "id"    => "favorite_color",
                "default"   => "",
                "type"  => "text",
                "size"  => "15"),

        array(  "name"    => __('PLUGIN TEST2'),
                "desc"  => __("

                            "),
                "type"  => "section"),

        array(  "name"  => __('PLUGIN TEST RADIO BUTTON'),
                "desc"  => __('What choice will you make?'),
                "id"    => "TEST_RADIO_BUTTON",
                "default"   => "this radio 3",
                "type"  => "radio",
                "options" => array(     "this radio 1",
                                        "this radio 2",
                                        "this radio 3",
                                        "this radio 4",
                                        "this radio 5"
                                            )
                ),



        array(    "name"    => __('PLUGIN Textarea'),
                        "desc"    => __("What you type here will indicate the possibility of success."),
                        "id"      => "textarea_id",
                        "std"     => __("You do not have to put any defaults"),
                        "type"    => "textarea",
                        "rows" => "2",
                        "cols" => "55"
                        ),

        array(    "name"    => __('PLUGIN Select'),
                        "desc"    => __("There are many choices awaiting"),
                        "id"      => "TEST_SELECT",
                        "default"     => "Select 4",
                        "type"    => "select",
                        "options" => array(    "Select 1",
                                            "Select 2",
                                            "Select 3",
                                            "Select 4",
                                            "Select 5"
                                            )
                        ),

        array(  "name"  => __('PLUGIN Asides Category'),
                "desc"  => __('Pick the category to use as your sideblog'),
                "id"    => "TEST_ASIDES_CATEGORY_SELECTOR",
                "type"  => "select_wp_categories",
                "args" => array(

                                            )
                ),

        array(  "name"  => __('PLUGIN Featured Page'),
                "desc"  => __('Choose the page to feature'),
                "id"    => "TEST_PAGE_SELECTOR",
                "type"  => "select_wp_pages",
                "args" => array(

                                            )
                ),

        array(    "name"    => __('PLUGIN MultiSelect'),
                        "desc"    => __("There are many choices awaiting and you can have them all"),
                        "id"      => "TEST_MULTISELECT",
                        "default"     => "Select 5",
                        "type"    => "select",
                        "multi"   => TRUE,
                        "size"   => "8",
                        "options" => array(    "Select 1",
                                            "Select 2",
                                            "Select 3",
                                            "Select 4",
                                            "Select 5",
                                            "Select 6",
                                            "Select 7",
                                            "Select 8"
                                            )
                        )

    );

/**
 * Instantiate core menu
*/
$my_plugin = new KST_Kitchen_Plugin($kst_core_settings);

$my_plugin->newOptionsGroup($kst_core_options, 'KST Settings', 'appearance') ;

function kst_load_settings_core() {
    //global $kst_options;

    /**
     * Create KST core options page
     */
    //$kst_options = new KST_Options('kst_settings_core', 'settings', 'KST Core', 'Kitchen Sink Core options');

}
