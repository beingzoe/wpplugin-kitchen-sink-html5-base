<?php
/**
 * Abstraction to utilize the awesome WPAlchemy_MetaBox class in KST
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink/
 * @link        https://github.com/beingzoe/wpplugin-kitchen-sink-html5-base/wiki/Docs_appliance_core_metaboxes
 * @link        https://github.com/beingzoe/wpplugin-kitchen-sink-html5-base/wiki/Docs_appliance_core_options
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  Core
 * @version     0.1
 * @since       0.1
 * @uses        WPAlchemy_MetaBox
 * @uses        ZUI_FormHelper
*/


/**
 * Companion class to quickly create WP metaboxes (for post/page/custom custom fields)
 *
 * @since       0.1
 *
*/
require_once KST_DIR_VENDOR . '/farinspace/WPAlchemy/WPAlchemy/MetaBox.php';


/**
 * Methods for creating multiple metabox objects
 * (using WPAlchemy_MetaBox) in KST context.
 *
 * @since       0.1
 * @uses        WPAlchemy_MetaBox
 * @uses        ZUI_FormHelper
*/
class KST_Appliance_MetaBox extends KST_Appliance {

    /**#@+
     * @access protected
     * @var array
    */
    protected $_form_array = array();
    protected static $_all_property_arrays = array(); // array of all metabox objects
    /**#@-*/


    /**#@+
     * @access protected
     * @var string
    */
    protected $_working_field;
    /**#@-*/


    /**
     * Constructor
     *
     * @since       0.1
     * @param       required object $kitchen
     * @uses        add_action() WP function
    */
    public function __construct(&$kitchen) {

        // Every kitchen needs the basic settings
        $appliance_settings = array(
                    'friendly_name'       => 'KST Appliance: Core: MetaBox',
                    'prefix'              => 'kst_metabox',
                    'developer'           => 'zoe somebody',
                    'developer_url'       => 'http://beingzoe.com/',
                );

        // Declare as core
        $this->_is_core_appliance = TRUE;
        // Common appliance
        parent::_init($kitchen, $appliance_settings, NULL);

    }


    /**
     * Create a new metabox using WPAlchemy_MetaBox.
     * Performs basic checks to see if everything needed exists
     * and optionally provides the ability to auto generate the form
     * for the metabox.
     *
     * @since       0.1
     * @uses        KST_Kitchen::prefixWithNamespace()
     * @uses        wp_die() WP function
     * @see         /templates/metabox/kst_metabox.php
     * @param       required array $settings
    */
    public function add($args) {
        // Instantiate WPAlchemy_MetaBox class  - Replaces get_post_meta()

        if ( !is_array($args) || !isset($args['id']) || !isset($args['template']) ) //!isset($args['property']) || !isset($args['template']) || !isset($args['settings'])
            wp_die("Something is wrong with your MetaBox settings array!<br />Make sure you are passing a settings array with the requisite settings");

        // Get the most important stuff
        //$property = $args['settings']['id'];
        $property = $args['id'];

        if ( isset($args['template']) && 'auto' == $args['template'] && !isset($args['options']) ) {
            wp_die("You indicated that you wanted to auto create your MetaBox form but supplied no 'options' array setting containing the form block information.");

        } elseif ( 'auto' == $args['template'] ) {

            // Hijack the template to use
            $args['template'] = KST_DIR_TEMPLATES . '/metabox/kst_metabox.php';

            // Save each metabox array args for kst_metabox.php template to get at later
            self::$_all_property_arrays[$args['id']] = $args;

        } elseif ( 'template' == $args['template'] && !isset($args['settings']['template']) ) {
            wp_die("You indicated that you wanted to use a template (native WPAlchemy_MetaBox style) but did not supply a 'settings' array with the necessary information.");
        } // Else just pass on the $settings to the template default WPAlchemy_MetaBox style

        // Make that metabox
        //$this->_property = $property;
        $this->{$property} =& new WPAlchemy_MetaBox($args);
        return $this->{$property};
    }


    /**
     * Retrieve a stored metabox settings array to output in
     * the kst_metabox.php template (auto form builder)
     *
     * @since       0.1
     * @param       optional string $metabox_id
     * @return      array
    */
    public static function getPropertyArrays($metabox_id = NULL) {
        if ( NULL === $metabox_id )
            return self::$_all_property_arrays;
        else
            return self::$_all_property_arrays[$metabox_id];
    }


    /**
     * All setup settings for WPAlchemy_MetaBox work with KST_MetaBox
     * A few methods for accessing an WPAlchemy_MetaBox through KST_MetaBox
     * have not been tested yet however.
     *
     * Methods not tested in KST_MetaBox (yet?)
     *      -the_index()
     *      -get_the_index()
     *      *-is_value($value) n.b.: is_value($name, $value) does work ???? This might be resolved now that the_field works
    */
    public function __call($name, $arguments) {

        // Because of the working field concept some WPAlchemy_MetaBox methods need help to work this way
        // have_fields_and_multi() functions differently because of the group concept so no exception is required
        if ( 'the_field' == $name || 'have_fields' == $name ) {
            $this->_working_field = $arguments[0]; // There can only be one working field and we need to hold it until another working field is set;
        }

        // Unfortunately we need to find the metabox property that this method call belongs to - loop our
        foreach (self::$_all_property_arrays as $metabox_id => $metabox_settings) {
            foreach ( $metabox_settings['options'] as $option_id => $option_settings ) {

                if ( (isset($arguments[0]) && $arguments[0] == $option_id) || $this->_working_field == $option_id ) { // Only catches method calls where metabox 'name/id' is the first argument OR a working field as been set, so WPAlchemy methods such as is_value($value) will fail unless the working_field has been set
                    $property = $metabox_id;
                    $new_args = implode(', ', $arguments);

                    if ( empty($new_args) )
                        $new_args = NULL;

                    // Proxy call $this metabox property
                    return $this->$property->$name($new_args);
                }
            }
        }
    }

}

