<?php

/**
 * Add hook to load core settings menu once the menu is available to add
 */
//add_action('admin_menu', 'kst_load_settings_core');

/**
 * Define the settings for the core
 */
$kst_settings_core = array (
            // Layout options
            array(  "name"      => __('Kitchen Sink Base core settings'),
                    "desc"      => __("
                                    <p><em>There are no core settings at this time</em></p>
                                "),
                    "type"      => "section",
                    "is_shut"   => FALSE ),
    
        );

/**
 * Instantiate core menu
 */
function kst_load_settings_core() {
    //global $kst_options;

    /**
     * Create KST core options page
     */
    //$kst_options = new KST_Options('kst_settings_core', 'settings', 'KST Core', 'Kitchen Sink Core options');
    
}