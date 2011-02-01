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
 * @todo        Finish disable appliances
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
    protected static $_extant_options; // ??? I think we are only using this in the new Options class - check and delete Array of options that exist IF they were checked with $this->option_exists();
    protected static $_appliances; // Array of bundled appliances (classes, functions/helper libraries)
    protected static $_disabled_appliances = array();
    /**#@-*/

    protected static $_admin_pages; // Store all menus/pages from all registered kst kitchens

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
    public function registerAppliance($shortname, $path, $class_name=false, $require_args = FALSE) {
        if ( !is_array(self::$_appliances) ) self::$_appliances = array();
        if (array_key_exists($shortname, self::$_appliances)) {
            // collision!
            trigger_error("You attempted to register an 'Appliance' ({$shortname}) with Kitchen Sink HTML5 Base that is already register. Please choose a more unique shortname to register.", E_USER_NOTICE);
        } else {
            //self::$_appliances[$shortname] = [];
            self::$_appliances[$shortname]['path'] = $path;
            self::$_appliances[$shortname]['class_name'] = $class_name; // FALSE if appliance is not a class
            self::$_appliances[$shortname]['require_args'] = $require_args; // FALSE if appliance is not a class
            // Add ability for blog owner to override kitchen creator appliances
            $GLOBALS['kst_core_options']['options'][$shortname]['name'] = $shortname;
            $GLOBALS['kst_core_options']['options'][$shortname]['desc'] = 'Disable this appliance';
            $GLOBALS['kst_core_options']['options'][$shortname]['type'] = 'checkbox';
            $GLOBALS['kst_core_options']['options'][$shortname]['default'] = FALSE;
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

            // require_args is used to prevent an appliance from auto instantiating itself if it requires parameters to do so
            if ( array_key_exists('require_args', $appliance))
                $require_args = $appliance['require_args'];
            else
                $require_args = FALSE;

            // Register the appliance
            $this->registerAppliance($shortname, $appliance['path'], $appliance['class_name'], $require_args);
        }
    }


    /**
     * Load appliances (classes, functions/helper libraries)
     *
     * If the file being included is a class (has a class_name) it will be
     * instantiated using the supplied shortname or a custom object name if supplied
     *
     * @param       $shortname String or Array The appliance shortname or an array of the shortname and the property (object variable name) you want to use to access this appliance
     * @params      *args Variable amount of remaining arguments will be passed to the constructor
    */
    public function load($shortname) {
        $args = func_get_args(); // Get args to send to class
        array_shift($args); // get rid of $shortname
        if (is_array($shortname)) {
            list($shortname, $property) = $shortname; // object property name
        } else {
            $property = $shortname;
        }
        $this_kitchen = &$this; // Store kitchen object for new appliance objects
        $args[] = $this_kitchen; // Add it to the $args array

        // Find the known appliance to load
        if ( array_key_exists($shortname, self::$_appliances) ) {
            $appliance = self::$_appliances[$shortname];
            require_once $appliance['path']; // Load the file
            // Auto instantiate IF not disabled, is a class (has class_name), and does NOT require args (or it does and it has more than 1 - we always send the kitchen object)
            if ( !in_array($shortname, self::$_disabled_appliances) && $appliance['class_name'] && ( !$appliance['require_args'] || 1 < count($args) ) ) { // FALSE if appliance is not a class && it can't require args || the args are greater than 1 because we are inserting the kitchen object
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
                //$this->load('help');
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
                //$this->load('help');
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
        $options['menu_slug'] = $this->prefixWithNamespace( str_replace( " ", "_", $options['menu_title'] ) );

        return KST_AdminPage::addOptionPage($options_array, $options);
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
    public function prefixWithNamespace( $item ) {
        return $this->namespace . $item;
    }

    /**
     * Appliances to disable at blog owners request
     *
     * Experimental.
     *
     * @since       0.1
     * @param       required string $item    unnamespaced option name
     * @uses        KST_Kitchen::$_disabled_appliances
    */
    public static function setDisabledAppliances($disabled_list) {
        if ( !empty($disabled_list) && is_string($disabled_list)) {
            self::$_disabled_appliances = explode(",", $disabled_list);
        }
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
     * Get this type_of_kitchen
     *
     * @since       0.1
     * @access      public
     * @return      string
    */
    public function getTypeOfKitchen() {
        return $this->type_of_kitchen;
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
     * Get this namespace
     *
     * @since       0.1
     * @access      public
     * @return      string
    */
    public function getNamespace() {
        return $this->namespace;
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
