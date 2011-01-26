<?php
/**
 * Class for managing plugin through KST
 *
 * @package     KitchenSinkHTML5Base
 * @subpackage  Core
 * @version     0.1
 * @since       0.1
 * @author      zoe somebody
 * @link        http://beingzoe.com/
 * @author      Scragz
 * @link        http://scragz.com/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
*/

/**
 * Parent class
*/
require_once KST_DIR_LIB . '/KST.php';

class KST_Kitchen extends KST {

    /**#@+
     * @since       0.1
     * @access      protected
    */
    protected $type_of_kitchen;
    protected $friendly_name;
    protected $prefix;
    protected $namespace;
    protected $developer;
    protected $developer_url;
    protected $_appliance;
    /**#@-*/


    /**
     * Theme/Plugin instance constructor
     *
     * @since       0.1
     * @access      protected
     * @param       required array $settings
     * @param       optional string $preset // As a convenience you can just pass a preset when you make your kitchen
    */
    protected function __construct( $settings, $preset=null ) {

        $default_settings = array(
            'friendly_name'             => 'Kitchen Sink',
            'prefix'                    => 'kst_0_2',
            'developer'                 => 'zoe somebody',
            'developer_url'             => 'http://beingzoe.com/'
        );
        $settings = array_merge( $default_settings, $settings );

        $this->_setFriendlyName( $settings['friendly_name'] );
        $this->_setPrefix( $settings['prefix'] );
        $this->_setDeveloper( $settings['developer'] );
        $this->_setDeveloper_url( $settings['developer_url'] );
        $this->namespace = "kst_" . $this->prefix . "_";
        $this->_appliance = array();

        // Anything to help a brother out - sure we'll load that preset
        if ($preset) {
            $this->loadPreset($preset);
        }
    }


    /**
     * Registers a loadable appliance and shortname for it
     *
     * This is intended for plugins using KST that want to load
     * custom classes and be able to access them or give access to them
     * with the same syntax as the bundled appliances. While the load() method
     * will simply 'include' a file, unless you want to make it available for
     * the theme developer to call programatically later, just include
     * functions library and heaven forbid, procedural code, normally through php
     *
     * @since       0.1
     * @access      public
     * @param       required string $shortname unique identifier in KST for your appliance
     * @param       required string $path /absolute/path/including/filename.php to the class or functions library to load
     * @param       optional string $class_name The name of the class to invoke if the appliance is a class file
    */
    public function registerAppliance($shortname, $path, $class_name=false) {
        if (array_key_exists($shortname, $_appliances)) {
            // collision!
            trigger_error("You attempted to register an 'Appliance' ({$shortname}) with Kitchen Sink HTML5 Base that is already register. Please choose a more unique shortname to register.", E_USER_NOTICE);
        } else {
            //self::$_appliances[$shortname] = [];
            self::$_appliances[$shortname]['path'] = $path;
            self::$_appliances[$shortname]['class_name'] = $class_name; // FALSE if appliance is not a class
        }
    }


    /**
     * Load appliances (classes, functions/helper libraries)
     *
     * If the file being included is a class (has a class_name) it will be
     * instantiated using the supplied shortname or a custom object name if supplied
     *
     * @param $shortname String or Array The appliance shortname or an array of the shortname and the property you want to use to access this appliance
     * @params *args Variable amount of remaining arguments will be passed to the constructor
    */
    public function load($shortname) {
        $args = func_get_args();
        array_shift($args); // get rid of $shortname
        if (is_array($shortname)) {
            list($shortname, $property) = $shortname;
        } else {
            $property = $shortname;
        }
        if (array_key_exists($shortname, self::$_appliances)) { // Find the known appliance to load
            $appliance = self::$_appliances[$shortname];
            require_once $appliance['path']; // Load the file
            if ( $appliance['class_name'] ) { // FALSE if appliance is not a class
                $_reflection = new ReflectionClass($appliance['class_name']);
                $this->{$property} = $_reflection->newInstanceArgs($args);
                return true;
            } else {
                return TRUE; // Just tell them we finished
            }
        } else {
            return FALSE;
        }
    }

    /**
     * KST presets
     *
     * @since       0.1
    */
    public function loadPreset( $preset = 'default') {
        switch ($preset) {
            case 'minimum':
                $this->load('wp_sensible_defaults');
                $this->load('help');
            break;
            case 'and_the_kitchen_sink':
                foreach (parent::$_appliances as $key => $value) {
                    $this->load($key);
                }
            break;
            default:
                $this->load('wp_sensible_defaults');
                $this->load('help');
                $this->load('seo');
                $this->load('wordpress');
                $this->load('contact');
            break;
        }
    }

    /**
     *
    */
    public function __get($name) {

        //$this->_appliance[$name]

        echo "Getting '$name'\n<br />";
        if (array_key_exists($name, $this->_appliance)) {
            return $this->_appliance[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    /**
     * Kitchen wants a new option group
     *
     * @since 0.1
     * @uses         KST_AdminPage_OptionsGroup::getOption()
     * @param       required string option
     * @param       optional string default ANY  optional, defaults to null
     * @return      object
     * @todo        test to see if it is faster to let action hook be called multiple times or limit it with has_action()?
    */
    public function addOptionPage($options_array, $menu_title, $parent_menu = 'kst', $page_title = FALSE) {
        // Only need actual menu/pages if in WP Admin - speed things up
        if ( is_admin() ) {

            // Make sure we have or array to save all the pages to
            if ( !isset(parent::$_all_admin_pages) )
                parent::$_all_admin_pages = array();

            // Create generic title if none given
            $page_title = ( $page_title ) ? $page_title
                                          : $this->getFriendlyName() . " " . $menu_title;

            // Create a namespaced menu slug from their menu title then we'll check it for duplicates
            $menu_slug = $this->_prefixWithNamespace( str_replace( " ", "_", $menu_title ) );

            // Begin Testing the menu_title for dupes
            $i = 0;
            $still_checking = TRUE;

            while ( $still_checking ) {
                $unique_menu_slug = $menu_slug; // Reset the unique test name each loop
                if ($i > 0) { // After first loop append _$i to try and find a unique slug
                    $unique_menu_slug .= "_" . $i;
                }
                // Check if the $unique slug exists in our master pages array
                $does_key_exist = ( array_key_exists($unique_menu_slug,  parent::$_all_admin_pages) )
                                ? TRUE
                                : FALSE;

                if ( $does_key_exist ) {
                    // Let them know during debug that there is duplicate menu title (i.e. menu_slug) to help keep menus clean and not overwrite existing menus
                    trigger_error("Try to use unique menu titles. A menu/page in a plugin or theme already exists with the menu title '{$menu_title}'", E_USER_NOTICE);
                } else {
                    $still_checking = FALSE; // We have a unique name so stop checking
                }
                $i++;
            }

            // Save this options page object in KST_Kitchen static member variable to create the actual pages with later
            // We won't actually add the menus or markup html until we have them all and do sorting if necessary and prevent overwriting existing menus
            $new_page = new KST_AdminPage_OptionsGroup( $options_array, $menu_title, $unique_menu_slug, $parent_menu, $page_title, $this->namespace );

            // Naming the keys using the menu_slug so we can manipulate our menus later
            parent::$_all_admin_pages[$unique_menu_slug] = array( 'type'=>$this->type_of_kitchen, 'name'=>$this->friendly_name, 'parent_menu'=>$parent_menu, 'page'=>$new_page);

            if ( !has_action( 'admin_menu', 'KST_Kitchen::createAdminPages' ) ) {
                add_action('admin_menu', 'KST_Kitchen::createAdminPages',999); // hook to create menus/pages in admin AFTER we have ALL the options
            }

            return $new_page; // Give them back the object if they want to mess with it
        } else {
            return false; // We only need the actual page and object if we are in the admin
        }
    }


    /**
     * Time to make those pages
     * Callback for admin_init WP action hook (after all non-kst menus are done on previous admin_menu hook to protect them and us)
     *
     * @since 0.1
     * @uses         KST_AdminPage_OptionsGroup::getOption()
     * @param       required string option
     * @param       optional string default ANY  optional, defaults to null
     * @return      object
    */
    public static function createAdminPages() {

        // Reorganize: Set up temporary arrays and settings to group everything
        $doCoreOnly = TRUE;
        $custom_start_index = 223;
        $temp__all_admin_pages = parent::$_all_admin_pages;
        $temp_core_options = array();
        /*$temp_kst_options = array();*/
        $temp_kst_theme_options = array();
        $temp_kst_plugin_options = array();
        $temp_theme_options = array();
        $temp_plugin_options = array();
        $temp_plugin_other = array();
        // Reorganize: Determine if a kitchen exists and build grouped temporary arrays
        foreach ( $temp__all_admin_pages as $key => $page ) {
            if ($page['parent_menu'] <> 'core') {
                $doCoreOnly = FALSE; // We have a kitchen so core options will be moved accordingly
                if ( 'kst' == $page['parent_menu'] ) {
                    /*$temp_kst_options[$key] = $page;*/
                    if ( $page['type'] == 'theme') {
                        $temp_kst_theme_options[$key] = $page; // KST managed theme options
                    } else {
                        $temp_kst_plugin_options[$key] = $page; // KST managed plugin options
                    }
                } else if ( $page['type'] == 'theme') {
                    $temp_theme_options[$key] = $page; // Theme chose WP or custom top
                }  else if ( $page['type'] == 'plugin' ) {
                    $temp_plugin_options[$key] = $page; // Plugin chose WP or custom top
                }
            } else {
                $temp_core_options[$key] = $page; // Core options
            }
            unset( $temp__all_admin_pages[$key] ); // Remove this item from the master array
        }

        // Reorganize: Assign 'kst' and 'core' menus a proper parent_menu and slug
            if ( (0 != count($temp_kst_theme_options) || 0 != count($temp_kst_plugin_options)) && FALSE == $doCoreOnly ) {
                // We have KST managed menus so find the first menu item going into the KST 'Theme Options' menu
                if ( 0 != count($temp_kst_theme_options) ) { // It's a themes world so they go first
                    $first_kst_menu_item = current($temp_kst_theme_options);
                    $first_kst_menu_slug = $first_kst_menu_item['page']->getMenuSlug();
                } else { // Just a plugin so first plugin in gets it
                    $first_kst_menu_item = current($temp_kst_plugin_options);
                    $first_kst_menu_slug = $first_kst_menu_item['page']->getMenuSlug();
                }
                // Add the actual KST 'Theme Options' menu now so it exists to put all these in

                add_menu_page( $first_kst_menu_item['page']->getPageTitle(), 'Theme Options', 'manage_options', $first_kst_menu_slug, array($first_kst_menu_item['page'],'manage'), '', $custom_start_index); // wildly unique index to use later
                $custom_start_index = FALSE; // Don't use it again everything will index from there
                // Set all these to use that menu i.e. give them all the parent menu's slug
                foreach (array($temp_kst_theme_options, $temp_kst_plugin_options, $temp_core_options) as $pages ) {
                    foreach ($pages as $key => $page) {
                        $pages[$key]['parent_menu'] = $first_kst_menu_slug;
                        $pages[$key]['page']->setParentMenu($first_kst_menu_slug);
                    }
                }
            } else { // None of the menus used the KST 'Theme Options' menu so we have to do this -
                foreach ($temp_core_options as $key => $page) {
                    // Update core options to use 'settings'
                    $temp_core_options[$key]['parent_menu'] = 'settings';
                    $temp_core_options[$key]['page']->setParentMenu('settings');
                }
            }
        // Done Organizing KST/Core managed menus/pages...

        // Tell WP we want these pages - loop our separated temporary arrays...
        foreach ( array($temp_kst_theme_options, $temp_kst_plugin_options, $temp_theme_options, $temp_plugin_options, $temp_core_options) as $pages ) {
            // And then loop that to get the individual page array element
            foreach ( $pages as $page ) {

                // Is this menu the child of a custom top level - reverse the master pages array and look for the menu title
                if ( !in_array($page['parent_menu'], array('Theme Options','top', 'dashboard', 'posts', 'media', 'links', 'pages', 'comments', 'appearance', 'plugins', 'users', 'tools', 'settings' )) ) {
                    // We need to loop again to find the key of the custom top level parent menu
                    foreach ($pages as $searchkey => $searchvalue ) {
                        // We look for the menu_title the child claims to belong to...
                        if ( $page['parent_menu'] == $searchvalue['page']->getMenuTitle() ) {
                            // We found it so set the parent menu to that menus $menu_slug
                            $find_parent = $searchkey;
                            $parent_slug = $pages[$find_parent]['page']->getMenuSlug(); // Get the found parent menu slug
                            $page['parent_menu'] = $parent_slug;
                            $page['page']->setParentMenu($parent_slug);
                            break; // found it so move on!
                        }
                    }
                }
                // FINALLY create this page (register it with WP)
                $new_page = KST_AdminPage::create($page['page']);
            }
        }

        if ( FALSE == $custom_start_index ) {
            // Lastly put all of this above appear
            global $menu;

            $menu[] = array('','','','','');; // Need a dummy element to stick our separator in
            end($menu);         // Move the internal pointer to the end of the array
            $end_key = key($menu); // Get the key
            add_admin_menu_separator($end_key); // Add a separator at that key
            reset($menu); // Put the array back

            $i = 0;
            $start_at = 223; // starting at our ridiculous index
            $found_slot = FALSE;
            while ( FALSE == $found_slot ) {
                $current_slot = $start_at + $i;
                if ( array_key_exists($current_slot, $menu) && is_array($menu[$current_slot])) {
                    $move_this = $menu[$current_slot][2];
                    swap_admin_menu_sections('separator-last', $move_this);
                    swap_admin_menu_sections('options-general.php', $move_this);
                    swap_admin_menu_sections('tools.php', $move_this);
                    swap_admin_menu_sections('users.php', $move_this);
                    swap_admin_menu_sections('plugins.php', $move_this);
                    swap_admin_menu_sections('themes.php', $move_this);

                } else {
                    break;
                }
                $i++;
            }
        }
    }


    /**
     * Public accessor to get KST managed and namespaced WP theme option
     *
     * @since 0.1
     * @uses         KST_AdminPage_OptionsGroup::getOption()
     * @param       required string option
     * @param       optional string default ANY  optional, defaults to null
     * @return      string
    */
    public function getOption($option, $default = null) {
        return KST_AdminPage_OptionsGroup::getOption($this->namespace, $option, $default);
    }


    /**
     * Test for existence of KST theme option REGARDLESS OF trueNESS of option value
     *
     * Returns true if option exists REGARDLESS OF trueNESS of option value
     * WP get_option returns false if option does not exist
     * AND if it does and is false
     *
     * Typically only necessary when testing existence to set defaults on first use for radio buttons etc...
     *
     * N.B.: First request is an entire query and obviously a speed hit so use wisely
     *       Multiple tests for the same option are saved and won't affect load time as much
     *
     * @since       0.1
     * @see         KST_AdminPage_OptionsGroup::getOption()
     * @global      object $wpdb This is wordpress ;)
     * @param       required string $option
     * @return      boolean
    */
    public function doesOptionExist( $option ) {

        $namespaced_option = $this->_prefixWithNamespace($option);
        $skip_it = FALSE; // Flag used to help skip the query if we've checked it before

        // Check to see if the current key exists
        if ( !array_key_exists( $namespaced_option, self::$_extant_options ) ) { // Don't know yet, so make a query to test for actual row
            $does_exist = KST_AdminPage_OptionsGroup::getOption($this->namespace, $option, $default);
        } else { // The option name exists in our "extantoptions" array so just skip it
            $skip_it = true;
        }

        // Return the answer
        if ( $skip_it || $does_exist ) { // The option exists regardless of trueness of value
            self::$_extant_options[$namespaced_option]['exists'] = true; // Save in array if exists to minimize repeat queries
            return true;
        } else { // The option does not exist at all
            return false;
        }
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


    /**
     * COMMON THEME/PLUGIN GET(public) and SET (PROTECTED) MEMBER VARIABLES
     * Acessors and Mutators
    */

    /**
     * Get this friendly_name
     *
     * @since       0.1
     * @access      public
     * @return      string
    */
    public function getFriendlyName() {
        return $this->friendly_name;
    }


    /**
     * Set this friendly_name
     *
     * @since       0.1
     * @access      protected
    */
    protected function _setFriendlyName($value) {
        $this->friendly_name = $value;
    }


    /**
     * Get this prefix
     *
     * @since       0.1
     * @access      public
     * @return      string
    */
    public function getPrefix() {
        return $this->prefix;
    }


    /**
     * Set this prefix
     *
     * @since       0.1
     * @access      protected
    */
    protected function _setPrefix($value) {
        $this->prefix = $value;
    }


    /**
     * Get this developer
     *
     * @since       0.1
     * @access      public
     * @return      string
    */
    public function getDeveloper() {
        return $this->developer;
    }


    /**
     * Set this developer
     *
     * @since       0.1
     * @access      protected
    */
    protected function _setDeveloper($value) {
        $this->developer = $value;
    }


    /**
     * Get this developer_url
     *
     * @since       0.1
     * @access      public
     * @return      string
    */
    public function getDeveloper_url() {
        return $this->developer_url;
    }


    /**
     * Set this developer_url
     *
     * @since       0.1
     * @access      protected
    */
    protected function _setDeveloper_url($value) {
        $this->developer_url = $value;
    }


    /**
     * Get a custom kitchen variable
     *
     * @since       0.1
     * @access      public
     * @return      string
    */
    public function getVar($variable) {
        return $this->{$variable};
    }


    /**
     * Set a custom kitchen variable
     *
     * @since       0.1
     * @access      public
    */
    public function setVar($variable, $value) {
        $this->{$variable} = $value;
    }


}
