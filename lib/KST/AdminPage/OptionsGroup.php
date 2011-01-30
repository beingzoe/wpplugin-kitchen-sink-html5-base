<?php
/**
 * Class for managing options in pages created by KST_AdminPage
 * Each instance represents the settings for one menu/page/option_group
 * Parent class included on KST load so KST can have core options without theme or plug
 *
 * @package     KitchenSinkHTML5Base
 * @subpackage  Core
 * @version     0.1
 * @since       0.1
 * @author      zoe somebody http://beingzoe.com/
 * @link        http://beingzoe.com/
 * @author      Scragz http://scragz.com/
 * @link        http://scragz.com/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @uses        KST_AdminPage
 * @todo        WP settings_fields() and WP register_setting() are not working and we are manually updating the options pre 2.7 style
 * @todo        As a consequence of the previous we are not saving serialized data at this time and need to
 * @todo        Do the previous two items and create a "migration" on plugin update to fix old settings to continue to function when we begin serializing
 */
class KST_AdminPage_OptionsGroup extends KST_AdminPage {

    /**
     * @since       0.1
     * @access      public
    */
    public $options_array; // reference to original options array

    /**
     * @since       0.1
     * @access      protected
    */
    protected $settings_options_group; // A group of options that belong to a menu/page associated by menu_slug


    /**
     * @since       0.1
     * @param       required array $options_array passed by reference; Contains options block types and their parameters
     * @param       required string $menu_title
     * @param       required string $menu_slug
     * @param       required string $parent_menu
     * @param       required string $page_title
     * @param       required string $settings_options_group
     * @param       required string $namespace // N.B. Options and options group are namespaced automatically e.g. namespace_optionname, namespace_optionsgroup
    */
    public function __construct(&$options_array, $options = array()) {
        $options += self::defaultOptions();
        parent::__construct($options); // Pass the common stuff on to the parent first

        $this->options_array =& $options_array; // The options array by reference
        $this->settings_options_group = $options['namespace'] . 'options_group';

        // hook to register settings for options
        // SEE todo regarding registering settings - do not delete
        //add_action('admin_init', array(&$this, 'registerSettingsWithWP' ));
    }


    /**
     * Public static accessor to get a namespaced option when you only know the namespace and unnamespaced option
     *
     * Replaces native get_option for convenience
     * So instead of get_option("namespace_admin_email");
     * you can $kst_options->getOption("your_namespace_prefix_","admin_email");
     *
     * @since 0.1
     * @param       required string namespace
     * @param       required string option
     * @param       optional string default ANY  optional, defaults to null
     * @uses        get_option() WP function
     * @return      string
    */
    public static function getOption($namespace, $option, $default = null) {
        $option = $namespace . $option;
        $option_value = get_option( $option, $default); // Ask WP
        return $option_value;
    }


    /**
     * Test for existence of KST theme option REGARDLESS OF TRUENESS of option value
     *
     * Returns true if option exists REGARDLESS OF TRUENESS of option value
     * WP get_option returns false if option does not exist
     * AND if it does and is false
     *
     * Typically only necessary when testing existence to set defaults on first use for radio buttons etc...
     *
     * N.B.: First request is an entire query and obviously a speed hit so use wisely
     *
     * @since       0.1
     * @global      object $wpdb This is wordpress ;)
     * @param       required string $option
     * @return      boolean
    */
    public static function doesOptionExist( $namespace, $option ) {

        global $wpdb; // This IS WordPress ;)

        $namespaced_option = $namespace . $option ;

        // Check to see if the current key exists
        $row = $wpdb->get_row( "SELECT option_value FROM $wpdb->options WHERE option_name = '{$namespaced_option}' LIMIT 1", OBJECT );

        // Return the answer
        if ( is_object( $row ) ) { // The option exists regardless of trueness of value
            return true;
        } else { // The option does not exist at all
            return false;
        }

    }


    /**
     * Register the options with WP
     *
     * @since 0.1
     * @uses KST_AdminPage_OptionsGroup::options_array
     * @uses register_setting() WP function
     * @todo        Make this work; see todo for this class
     *
     * NOTE: Creates option with namespace prepended
     *       "option_1" is saved to the db as "namespace_option_1"
    */
    public function registerSettingsWithWP() {

        // If you can't manage your options then you can't have any menus - speed things up a bit
        if (!current_user_can('manage_options'))
            return false;

        require_once KST_DIR_VENDOR . '/ZUI/FormHelper.php';

        /* Make sure $this->options_array exists and give help */
        if ( !$this->options_array )
            $this->options_array = array (
                                        array(  "name"    => __('You did not define any options'),
                                                "desc"    => __("
                                                                <p>\$options_array DOES NOT EXIST.<br />You must define a \$options_array array to use the built-in theme options library or you should comment it out and not load it at all</p>
                                                                <p>See <a href='http://beingzoe.com/zui/wordpress/kitchen_sink_theme'>Kitchen Sink documentation</a> for help.</p>
                                                            "),
                                                "type"    => "section")
                                    );

        /* Make sure $this->options_array is an array and give help */
        if ( isset($this->options_array) && !is_array($this->options_array) ) {
            $this->options_array = array (
                                        array(  "name"    => __("\$options_array must be an array"),
                                                "desc"    => __("
                                                                <p>See <a href='http://beingzoe.com/zui/wordpress/kitchen_sink_theme'>Kitchen Sink documentation</a> for help.</p>
                                                            "),
                                                "type"    => "section")
                                    );
        }

        /**
         * register our options with WP
         * must register each option to be used in options form
         */
        foreach ($this->options_array as $value) {
            if ( isset($value['id']) && isset($value['type']) && in_array($value['type'], ZUI_FormHelper::get_blocks_of_type_form()) ) {
                register_setting( $this->settings_options_group, $this->namespace . $value['id'] );
            }
        }
    }


    /**
     * Generate markup for options group page
     *
     * @since       0.1
     * @uses        ZUI_FormHelper class
     * @uses        current_user_can() WP function
     * @uses        wp_die() WP function
     * @uses        settings_fields() WP function
     * @uses        wp_nonce_field() WP function
     *
     * NOTE: Creates option with namespace prepended
     *       "option_1" is saved to the db as "namespace_option_1"
    */
    protected function _generate_content() {

        require_once KST_DIR_VENDOR . '/ZUI/FormHelper.php';

        $output = "";

        // If you can't manage your options then you can't have any menus - speed things up a bit
        if ( !current_user_can('manage_options') )  {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }

        if ( !isset($this->options_array[0]['name']) ) {
            wp_die( __('<h3>Your options array looks messed up</h3><p>Verify that you have created an array that contains an array for each option block you need.<br />See the Kitchen Sink HTML5 Base documentation for help.</p>') );
        }

        // Give response on action
        if ( isset($_REQUEST['updated']) )
            $output .= "<div id='message' class='updated fade'><p><strong>" . $this->page_title . " settings saved.</strong></p></div>";
        if ( isset($_REQUEST['reset']) )
            $output .= "<div id='message' class='updated fade'><p><strong>" . $this->page_title . " settings reset.</strong></p></div>";

        $output .= "<form method='post' id='poststuff' class='metabox-holder'>";
            $output .= '<div class="meta-box-sortables" id="normal-sortables">'; // Attempting to utilize as much WP style/formatting as possible

            // Set up variables for block output loop
            $can_save               = FALSE; // Flag to only create form if we have a form element to save
            $kst_do_end_section     = FALSE; // Flag to clean up behind the previous section
            $kst_do_close_section   = FALSE; // Flag sections to be "closed" on load
            $block_previous_type      = FALSE; // Keep track of who we are cleaning up after

            //FOREACH LOOP WAS HERE THAT USED FormHelper


            /* Close section if one was started */
             if ( $kst_do_end_section ) {
                $output .= '</dl>'; // Close .form-table
                $output .= '</div>'; // Close .inside
                $output .= '</div>'; // Close .postexcerpt
            }
            $output .= "</div>"; // End normal-sortables

            if ( $can_save ) {
                //settings_fields( $this->settings_options_group ); // Output nonce, action, and option_page fields
                // N.B. We have to manually outpt the settings_fields because WP echos that (WTF?)
                // See the todo for this class!!!!
                /**
                 * N.B. We have to manually output the settings_fields() because WP echos that (WTF?)
                 * And we further need to manually output wp_referer_field normally called from wp_nonce_field because it doesn't return if you don't echo wp_nonce_field (WTF?)
                 * @link        http://codex.wordpress.org/Function_Reference/settings_fields
                 * @link        http://codex.wordpress.org/Function_Reference/wp_nonce_field
                */
                $output .= "<input type='hidden' name='option_page' value='" . esc_attr($this->settings_options_group) . "' />";
                $output .= '<input type="hidden" name="action" value="update" />';
                $output .= wp_nonce_field($this->settings_options_group . "-options", "_wpnonce", FALSE, FALSE);
                $output .= wp_referer_field(FALSE);
                $output .= "<p class='submit'>";
                    $output .= "<input type='submit' class='button-primary' value='" . __('Save Changes') . "' />";
                    $output .= "<input type='hidden' name='action' value='save' />  ";
                $output .= "</p>";
            }
        $output .= "</form>";

        /* Reset options form */
        if ( $can_save ) {
            $output .= "<form method='post'>";
                $output .= "<p class='submit'>";
                    $output .= "<input type='submit' class='button-secondary' value='Reset' title='Delete current settings and reset to defaults' />";
                    $output .= "<input type='hidden' name='action' value='reset' />";
                $output .= "</p>";
            $output .= "</form>";
        }

        /* You can filter this if you want */
        $output = apply_filters( 'kst_option_page_output', $output );

        /* Okay we are done - so spit it out already */
        return $output;
    }


    /**
     * Everything involving options is namespaced "namespace_"
     * e.g. options, option_group, menu_slugs
     * Still try to be unique to avoid collisions with other KST developers
     *
     * @since       0.1
     * @param       required string $item    unnamespaced option name
     * @uses        KST_AdminPage_OptionsGroup::namespace
     * @return      string
    */
    protected function _prefixWithNamespace( $item ) {
        return $this->namespace . $item;
    }

}

