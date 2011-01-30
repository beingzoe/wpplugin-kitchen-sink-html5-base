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

class KST_Kitchen {

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
    protected $_local_appliances;
    /**#@-*/


    /**#@+
     * Core protected variables to keep tabs on all the kitchens
     *
     * @since       0.1
     * @access      protected
    */
    protected static $_extant_options; // Array of options that exist IF they were checked with $this->option_exists();
    protected static $_appliances; // Array of bundled appliances (classes, functions/helper libraries)
    /**#@-*/


    /**
     * Theme/Plugin instance constructor
     *
     * @since       0.1
     * @access      protected
     * @param       required array $options:
     *              required string friendly_name
     *              required string prefix
     *              required string developer
     *              required string developer_url
    */
    protected function __construct( $options) {

        $defaults = array(
            'friendly_name'             => '',
            'prefix'                    => '',
            'developer'                 => '',
            'developer_url'             => ''
        );
        $options += $defaults;

        $this->_setFriendlyName( $options['friendly_name'] );
        $this->_setPrefix( $options['prefix'] );
        $this->_setDeveloper( $options['developer'] );
        $this->_setDeveloper_url( $options['developer_url'] );
        $this->namespace = "kst_" . $this->prefix . "_";
        $this->_local_appliances = array();
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
        if ( !is_array(self::$_appliances) ) self::$_appliances = array();
        if (array_key_exists($shortname, self::$_appliances)) {
            // collision!
            trigger_error("You attempted to register an 'Appliance' ({$shortname}) with Kitchen Sink HTML5 Base that is already register. Please choose a more unique shortname to register.", E_USER_NOTICE);
        } else {
            //self::$_appliances[$shortname] = [];
            self::$_appliances[$shortname]['path'] = $path;
            self::$_appliances[$shortname]['class_name'] = $class_name; // FALSE if appliance is not a class
        }
    }

    /**
     * Shortcut to bulk register an array of appliances.
     *
     * @param array $appliances
     * @return void
     * @see registerAppliance()
    */
    public function registerAppliances($appliances) {
        foreach ($appliances as $shortname => $appliance) {
            $this->registerAppliance($shortname, $appliance['path'], $appliance['class_name']);
        }
    }


    /**
     * Load appliances (classes, functions/helper libraries)
     *
     * If the file being included is a class (has a class_name) it will be
     * instantiated using the supplied shortname or a custom object name if supplied
     *
     * @param       $shortname String or Array The appliance shortname or an array of the shortname and the property you want to use to access this appliance
     * @params      *args Variable amount of remaining arguments will be passed to the constructor
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
                $this->load('helpold');
                $this->load('help');
            break;
            case 'and_the_kitchen_sink':
                foreach (self::$_appliances as $key => $value) {
                    $this->load($key);
                }
            break;
            default:
                $this->load('wp_sensible_defaults');
                $this->load('wp_sensible_defaults_admin');
                $this->load('helpold');
                $this->load('help');
                $this->load('seo');
                $this->load('wordpress');
                $this->load('contact');
            break;
        }
    }

    /**
     * Kitchen wants a new option group
     *
     * @since 0.1
     * @uses         KST_AdminPage::addOptionPage()
     * @param       required array $options_array
     * @param       required array options:
     *              required string menu_title
     *              required string parent_menu
     *              required string page_title
     * @return      object
    */
    public function addOptionPage($options_array, $options = array()) {
        $defaults = array(
            'menu_title' => '',
            'parent_menu' => 'kst',
            'page_title' => '');
        $options += $defaults;
        $options['namespace'] = $this->namespace;
        $options['type_of_kitchen'] = $this->type_of_kitchen;
        $options['friendly_name'] = $this->friendly_name;
        // Create generic title if none given
        $page_title = ( empty($options['page_title']) ) ? $options['page_title']
                                      : $this->getFriendlyName() . " " . $options['menu_title'];
        // Create a namespaced menu slug from their menu title
        $options['menu_slug'] = $this->_prefixWithNamespace( str_replace( " ", "_", $options['menu_title'] ) );

        return KST_AdminPage::addOptionPage($options_array, $options);
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


}
