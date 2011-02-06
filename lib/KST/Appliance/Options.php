<?php
/**
 * Create admin menu/pages within the Kitchen Sink HTML5 Base environment (i.e your kitchen)
 *
 * Included dependent classes WP_AdminMenuSection, WP_AdminMenuItem, and ZUI_WpAdminPages
 *
 * Provides abstracted access to the ZUI_WpAdminPages to encapsultate the creation
 * and management of menu/pages through your kitchen object in a more context
 * sensitive and aware manner.
 *
 * @package     KitchenSinkHTML5Base
 * @subpackage  Core
 * @author      zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink/
 * @link        https://github.com/beingzoe/wpplugin-kitchen-sink-html5-base/wiki/Docs_appliance_core_options
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @uses        KST_Kitchen
 * @todo        is_shut and show/hide sections isn't working i.e. implement $do_collapse_section has to do with the new simple templating
 * @todo        our admin pages need styling love that needs to be in the dev starter themes
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
 * Class methods for creating, accessing, and maintaining WP admin menu/pages
 * within the context of Kitchen Sink HTML5 Base
 *
 * @since       0.1
 * @uses        ZUI_WpAdminPages
 * @uses        ZUI_FormHelper
 * @uses        AdminMenu.php
*/
class KST_Appliance_Options extends KST_Appliance {

    /**#@+
     * @since       0.1
     * @access      protected
     * @var         array
    */
    protected static $_option_pages = array('kst_theme'=> array(),'kst_plugin'=> array(),'core'=> array(),'theme'=> array(),'plugin'=> array());
    protected static $_extant_options = array(); // Options checked for extant-ness
    /**#@-*/


    /**#@+
     * @since       0.1
     * @access      protected
     * @var         int
    */
    protected static $_custom_start_index = 223; // where we start storing our menus - hopefully out of everybody's way
    protected static $_custom_end_index; // where we end - not used ?
    /**#@-*/


    /**
     * Constructor - saves the kitchen object that this instance belongs to
     * and other information common to all options pages in this kitchen
     *
     * @since       0.1
     * @uses        KST_Kitchen::getTypeOfKitchen()
     * @param       required array $kitchen the kitchen object this instance belongs to in KST (by reference)
     *              required string namespace prefix to prepend everything with
     *              required string menu_title actual text to display in menu
     *              required string parent_menu Used to internally to track and determine final menu positions i.e. set correct parent_slugs
     *              required string page_title Explicit title to use on page
    */
    public function __construct(&$kitchen) {

        // Every kitchen needs the basic settings
        $appliance_settings = array(
                    'friendly_name'       => 'KST Appliance: Core: Options',
                    'prefix'              => 'kst_options',
                    'developer'           => 'zoe somebody',
                    'developer_url'       => 'http://beingzoe.com/',
                );

        // Declare as core
        $this->_is_core_appliance = TRUE;
        // Common appliance
        parent::_init($kitchen, $appliance_settings);
    }


    /**
     * Add option groups to the OptionGroups object for this kitchen
     *
     * Collects all option group pages to actually be created in WP after we can sort them
     *
     * @since       0.1
     * @uses        ZUI_WpAdminPages::get_all_parent_slugs()
     * @uses        ZUI_FormHelper::get_blocks_of_type_form()
     * @uses        KST_Kitchen::prefixWithNamespace()
     * @uses        KST_Appliance_Options::_doesOptionExist()
     * @uses        wp_die() WP function
     * @uses        get_option() WP function
     * @uses        has_action() WP function
     * @uses        add_action() WP function
     * @param       required array $options see documentation for details
     * @return      string The namespaced menu slug
     * @todo        Could this be sped up by saving already added-add()s as an option (i.e. save self::$_extant_options and check against that first? how would we update it?)
    */
    public function add($options) {


        $blocks_of_type_form = ZUI_FormHelper::get_blocks_of_type_form();

        // Set the key to save each group of options under - useed to sort the options later
        if ( 'core' == $options['parent_slug'] ) {
            $group_key = 'core';
        } else if ( 'kst' == $options['parent_slug'] && in_array($this->_type_of_kitchen, array('plugin','core')) ) {
            $group_key = 'kst_plugin';
        } else if ( 'kst' == $options['parent_slug'] && 'theme' == $this->_type_of_kitchen ) {
            $group_key = 'kst_theme';
        } else if ( in_array($this->_type_of_kitchen, array('plugin','core')) ) {
            $group_key = 'plugin';
        } else if ( 'theme' == $this->_type_of_kitchen ) {
            $group_key = 'theme';
        }

        // parent_slug is required
        if ( !isset($options['parent_slug']) )
            wp_die('You need to include a parent_slug in your options page arrays.');

        // If parent_slug is not in the known parent_slugs it is a new top
        // Otherwise if it is kst managed menu (or core) just let it through as is and we'll deal with it during create()
        if ( in_array($group_key, array('plugin','theme')) && !in_array($options['parent_slug'], ZUI_WpAdminPages::get_all_parent_slugs())) { // If it is a new top the menu_slug needs to match the parent slug
            $options['parent_slug'] = ZUI_WpAdminPages::fixParentSlug($options['parent_slug']); // now we can just rely on the array's parent_slug explicitly
            $options['menu_slug'] = $options['parent_slug'];
        // Must be a child menu then
        } else {
            // Deal with the optional menu_slug - always namespaced KST_YOUR_PREFIX_menu_slug
            if ( !isset($options['menu_slug']) ) { // Make the menu slug if it doesn't exist
                $options['menu_slug'] = $this->_kitchen->prefixWithNamespace( str_replace( " ", "_", $options['menu_title'] ) );
            } else { // They sent one so use it
                $options['menu_slug'] = $this->_kitchen->prefixWithNamespace( $this->menu_slug );
            }
        }

        // Namespace all the options - everything beyond here is namespaced
        // AND set defaults for extant checking - speed things up later
        foreach ($options['options'] as $key => $block) {
            $namespaced_key = $this->_kitchen->prefixWithNamespace( $key ); // store the key to operate with in this loop
            $options['options'][$namespaced_key] = $options['options'][$key]; // copy the key with namespace
            unset($options['options'][$key]); // delete the old key

            // Check to see if the option is a form element, if the value exists and set it appropriately
            if ( in_array($options['options'][$namespaced_key]['type'],$blocks_of_type_form) && $this->_doesOptionExist($namespaced_key) ) { /**/
                $options['options'][$namespaced_key]['value'] = get_option($namespaced_key); // Just use WP - it's already namespaced here
            } else if ( in_array($options['options'][$namespaced_key]['type'],$blocks_of_type_form) ) {
                $options['options'][$namespaced_key]['value'] = NULL; // Must send NULL to tell it the option doesn't exist otherwise empty() just means they chose no option
            }

            // If there is a default for this FORM option just save it now so get('option') can return it for them always - quickly
            if ( in_array($options['options'][$namespaced_key]['type'],$blocks_of_type_form) && isset($options['options'][$namespaced_key]['default']) ) {
                self::$_extant_options[$namespaced_key]['default'] = $options['options'][$namespaced_key]['default'];
            } else if ( in_array($options['options'][$namespaced_key]['type'],$blocks_of_type_form) ) {
                self::$_extant_options[$namespaced_key]['default'] = NULL;
            }
        }


        // Now we can set the option_group safely - always match the menu_slug
        $options['option_group_name'] = $options['menu_slug'];

        // If they didn't explicity request special formatting
        if ( !isset($options['section_open_template']) ) {
            $options['section_open_template'] = '<div class="postbox"><div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>{section_name}</span></h3><div class="inside">';
            $options['section_close_template'] = '</div><!--End .inside--></div><!--End .postexcerpt-->';
        }

        $options['priority'] = 999; // We like to go last-ish

        // Save the cleaned up array
        self::$_option_pages[$group_key][$options['menu_slug']] = $options; //

        if (!is_admin())
            return; // nothing to do

        // Add the hook to organize all the submitted pages before we send them to WP
        //if ( !has_action( '_admin_menu', array('KST_Appliance_Options', 'create')) ) {
            add_action('_admin_menu', array('KST_Appliance_Options', 'create'), 999); // important for sequencing
        //}

        // And return the key?
        return $options['menu_slug'];

    }


     /**
     * Organize the pages and then submit them to WP
     *
     *
     * @since       0.1
     * @uses        ZUI_WpAdminPages
    */
    public static function create() {

        // Prep
        $doKSTMenus = ( (count(self::$_option_pages['kst_theme'])) || (count(self::$_option_pages['kst_plugin'])) ) ? TRUE
                                                                                                                    : FALSE; // Whether to give core it's own menu
        // If we have KST Menus then get the menu/page that the section link should go to
        if ( $doKSTMenus ) {
            if ( 0 != count(self::$_option_pages['kst_theme']) ) { // It's a themes world so they go first
                $first_kst_menu_item        = current(self::$_option_pages['kst_theme']);
                $first_kst_menu_slug        = $first_kst_menu_item['menu_slug'];
                $first_kst_menu_page_title  = $first_kst_menu_item['page_title'];
                $first_kst_menu_capability  = $first_kst_menu_item['capability'];
            } else { // Just a plugin so first plugin in gets it? Maybe we should consider a special plugins index page if there are more than one?
                $first_kst_menu_item        = current(self::$_option_pages['kst_plugin']);
                $first_kst_menu_slug        = $first_kst_menu_item['menu_slug'];
            }
            // Add the KST 'Theme Options' menu now so it exists to put the rest in
            $kst_managed_theme_options_array = array(
                'parent_slug'           => $first_kst_menu_slug,
                'menu_slug'             => $first_kst_menu_slug,
                'top_only'              => 'section_only',
                'menu_title'            => 'Theme Options',
                'page_title'            => 'Theme Options',//$first_kst_menu_page_title,
                'capability'            => $first_kst_menu_capability,
                'view_page_callback'    => "auto",
                'section_open_template' => '<div class="postbox"><div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>{section_name}</span></h3><div class="inside">',
                'section_close_template'=> '</div><!--End .inside--></div><!--End .postexcerpt-->',
                'icon_url'              => NULL,
                'position'              => self::$_custom_start_index
            );
            $kst_managed_theme_options = new ZUI_WpAdminPages($kst_managed_theme_options_array); // We now have 'Theme Options' top level section

            // Set all the KST managed menus to the new menu section
            foreach ( self::$_option_pages as $groupkey => $pages ) {
                if ( in_array($groupkey, array('kst_theme', 'kst_plugin', 'core')) ) { // Only dealing with KST managed menus for now
                    foreach ($pages as $key => $page) {
                        self::$_option_pages[$groupkey][$key]['parent_slug'] = $first_kst_menu_slug;
                    }
                }
            }
        } else { // Update core options to use 'settings' since no kitchen exists
            foreach (self::$_option_pages['core'] as $key => $page) {
                $page['parent_slug'] = 'settings';
            }
        } // Done Organizing KST/Core managed menus/pages...

        // Loop 'em all...
        foreach ( self::$_option_pages as $pages ) {
            foreach ( $pages as $key => $page ) {
                // ..and FINALLY create each page (register it with WP - well send it off to that awesome abstraction I wrote ;)
                new ZUI_WpAdminPages($page);
            }
        }

        // And we'll move them around after we are done
        // If blog owner allows and we haven't already added this hook
        //if ( $GLOBALS['kst_core']->options->get('do_allow_kst_to_move_admin_menus') && !has_action( '_admin_menu', 'KST_Appliance_Options::moveKSTMenus') ) {
            add_action('admin_menu', array('KST_Appliance_Options', 'moveKSTMenus'), 1000);
        //}

    }


    /**
     * Method to move KST managed menus to a specific location
     *
     * Nothing fancy right now. Just move our 'Theme Options' menu above
     * appearance and add a separator to make it pretty
     *
     * @since       0.1
     * @uses        ZUI_WpAdminPages::moveMenuSectionUpAboveAnother()
     * @uses        add_admin_menu_separator() WP_AdminMenus function
     * @return      boolean
    */
    public static function moveKSTMenus() {

        if ( !$GLOBALS['kst_core']->options->get('do_allow_kst_to_move_admin_menus') )
            return false; // abide the blog owner

        global $menu;

        // Get current index of Appearance
        $menu_key_to_move_above = ZUI_WpAdminPages::findCurrentKeyOfWpMenuSection('Appearance');

        // Move menu if it exists
        if ( $menu_key_to_move_above ) {
            ZUI_WpAdminPages::moveMenuSectionUpAboveAnother(self::$_custom_start_index,$menu_key_to_move_above);
        } else {
            return FALSE;
        }
        /*
        // Get current index of Appearance
        $menu_key_to_move_above = ZUI_WpAdminPages::findCurrentKeyOfWpMenuSection('Appearance');

        // Insert an extra separator to make it prominent and clean
        $menu[998] = array('','','','',''); // need a dummy element to overwrite at ?safe? location
        add_admin_menu_separator(998);

        // Move menu if it exists
        if ( $menu_key_to_move_above )
            ZUI_WpAdminPages::moveMenuSectionUpAboveAnother(998,$menu_key_to_move_above);
        else
            return FALSE;
            */
        return TRUE;
    }


    /**
     * Test for existence of KST theme option REGARDLESS OF TRUENESS of option value
     *
     * Returns true if option exists REGARDLESS OF TRUENESS of option value
     * Returns false ONLY if option DOES NOT EXIST
     * Native WP get_option() returns indeterminate empty($value) so is useless for defaults
     *
     * NOTE: This is a highly optimized piece of simplistic beauty. Harnessing the
     *       strength of WP's autoload options and wp_cache_get() as well as our own
     *       $_extant_options array that stores 'exists','default','value' from the
     *       first access.
     *
     *
     * Typical use for testing existence to set defaults on first use for radio buttons etc...
     * Used extensively by get(). With this method we have eliminated the need for
     * using WP's get_option() altogether while namespacing everything for people and
     * I think simplifying the entire process of creating and using options.
     *
     * N.B.: First request is an entire query and obviously a speed hit so use wisely
     *
     * @since       0.1
     * @global      object $wpdb This is wordpress ;)
     * @param       required string $option
     * @return      boolean
    */
    protected function _doesOptionExist( $option ) {
        // Check to see if the current key exists
        if ( !array_key_exists($option, self::$_extant_options) ) { // First request so ask WordPress...

            // simulating part of the get_option() WP function just to get extant-ness - speed things up
            $alloptions = wp_load_alloptions(); // Gets all autoloaded options - cached if already done

            if ( isset( $alloptions[$option] ) ) {
                $value = $alloptions[$option];
            } else {
                $value = wp_cache_get( $option, 'options' );
            }

            if ( false === $value ) { // Checked with wp and does NOT exist
                self::$_extant_options[$option]['exists'] = FALSE; // Save - speed things up
                return FALSE;
            } else { // Checked with wp and DOES exist
                self::$_extant_options[$option]['exists'] = TRUE; // Save - speed things up
                self::$_extant_options[$option]['option_value'] = $value; // Save
                return TRUE;
            }

        } else if ( FALSE === self::$_extant_options[$option]['exists'] ) { // Checked ALREADY and RETURNED FALSE ALREADY
            return FALSE;
        } else { // Checked ALREADY and DOES exist with a value
            return TRUE;
        }

    }


    /**
     * Public accessor to get a namespaced option created by a particular kitchen
     *
     * Fully cached brilliance relying on _doesOptionExist() and WP option caching
     * Returns set value OR the default created in the original options array!
     *
     * Replaces native get_option for convenience
     * So instead of get_option("namespace_admin_email");
     * you can just $my_kitchen->options->get('admin_email');
     *
     * @since 0.1
     * @uses        KST_Kitchen::prefixWithNamespace()
     * @uses        KST_Appliance_Options::_doesOptionExist()
     * @uses        KST_Appliance_Options::$_extant_options
     * @param       required string $option
     * @param       optional string $default ANY  optional, defaults to null
     * @return      string value of option OR the default for the option
    */
    public function get($option, $default = NULL) {

        // Namespace it
        $namespaced_option = $this->_kitchen->prefixWithNamespace( $option );

        // Check if the default exists for this
        if ( $this->_doesOptionExist($namespaced_option) ) { // Check if it exists first - speed things up
            $option_value = self::$_extant_options[$namespaced_option]['option_value'];
        } else {
            // If passed $default is null then see if a default was originally set
            if ( NULL == $default ) {
                if ( array_key_exists($namespaced_option, self::$_extant_options) && array_key_exists('default', self::$_extant_options[$namespaced_option]) ) {
                    $default = self::$_extant_options[$namespaced_option]['default'];
                }
            }
            $option_value = $default;
        }

        return $option_value;

    }

}
