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
    protected static $_all_kitchen_settings_array;
    /**#@-*/


    /**#@+
     * Core protected variables to keep tabs on all the kitchens
     *
     * @since       0.1
     * @access      protected
    */
    protected static $_extant_options; // ??? I think we are only using this in the new Options class - check and delete Array of options that exist IF they were checked with $this->option_exists();
    protected static $_appliances = array(); // Array of registered appliances (classes, functions/helper libraries)
    protected static $_appliances_loaded = array();
    protected static $_appliances_disabled = array();
    protected static $_appliances_after_theme_setup = array(); // list of appliances deferred after the theme is setup (for pluggable functions)
    protected static $_appliances_can_disable = array(); // site/blog owner disabled
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
    protected function __construct( $options, $preset = NULL ) {

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
        self::$_all_kitchen_settings_array[] = $options; // save this for developers page and?
        $this->_local_appliances = array();

        if ( NULL !== $preset )
            $this->loadPreset($preset);
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
    public function registerAppliance($shortname, $args) { //$shortname, $path, $class_name=false, $require_args = FALSE

        // Set default args - 'friendly_name', 'desc', 'shortname' and 'path' are required
        $defaults = array(
                'class_name'        => FALSE,
                'require_args'      => FALSE,
                'after_setup_theme' => FALSE,
                'can_disable'       => FALSE,
                'is_theme_only'     => FALSE
            );
        $args = array_merge($defaults, $args);

        if (array_key_exists($shortname, self::$_appliances)) { //$shortname, self::$_appliances
            // collision!
            trigger_error("You attempted to register an 'Appliance' ({$shortname}) with Kitchen Sink HTML5 Base that is already register. Please choose a more unique shortname to register.", E_USER_NOTICE);
        } else {

            // Save all appliance args
            self::$_appliances[$shortname] = $args;

            // Make a list of appliances that need to load after the theme
            if ( $args['after_setup_theme'] ) {
                self::$_appliances_after_theme_setup[] = $shortname; // save these to defer loading
            }

            // Add ability for blog owner to override kitchen creator appliances
            if ( $args['can_disable'] ) {
                self::$_appliances_can_disable[] = $shortname;
            }
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
            $args = (array) $appliance;
            // Register the appliance
            $this->registerAppliance($shortname, $args);
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

        $this_kitchen =& $this; // Store kitchen object for new appliance objects
        array_unshift($args, $this_kitchen); // Add kitchen object to beginning of $args array

        // Get the $args for the appliance we are loading
        $appliance = self::$_appliances[$shortname];

        // Check if appliance has been registered and that if it is_theme_only that this is theme kitchen
        if ( array_key_exists($shortname, self::$_appliances) && ( !$appliance['is_theme_only'] || $appliance['is_theme_only'] && 'theme' == $this->type_of_kitchen ) ) {

            // Load appliances if they are not disabled by the site/blog owner
            // Options has to load no matter what - is there a more eloquent way to handle this?
            if ( 'options' != $shortname && KST_Appliance_Options::doesOptionExist("kst_kst_core_disable_{$shortname}") && get_option("kst_kst_core_disable_{$shortname}") ) {

                // Save a list of disabled appliances - so we can turn them back on!
                if ( !in_array($shortname, self::$_appliances_loaded) )
                    self::$_appliances_disabled[] = $shortname;

            } else { // Appliance is NOT disabled by site/blog owner...so load it!

                // Save a list of the appliances that are loaded
                if ( !in_array($shortname, self::$_appliances_loaded) )
                    self::$_appliances_loaded[] = $shortname;

                // Do once as long as some appliance loads - Add hook to load the core options page
                if ( !has_action( 'after_setup_theme', array('KST_Kitchen', 'addLoadedApplianceCoreOptions')) ) { // Only add this once - does WP check for us when we add?
                    add_action('after_setup_theme', array('KST_Kitchen', 'addLoadedApplianceCoreOptions')); // important for sequencing
                }

                // Load the appliance path - if not deferred
                if ( !in_array($shortname, self::$_appliances_after_theme_setup)) {
                    require_once $appliance['path']; // Load the file
                } else {
                    // Defer loading
                    if ( !has_action( 'after_setup_theme', array('KST_Kitchen', 'loadAppliancesAfterThemeSetup')) ) { // Only add this once - does WP check for us when we add?
                        add_action('after_setup_theme', array('KST_Kitchen', 'loadAppliancesAfterThemeSetup')); // important for sequencing
                    }
                }

                // Auto instantiate if is a class (has class_name), and does NOT require args (or it does and it has more than 1 - we always send the kitchen object)
                if ( $appliance['class_name'] && ( !$appliance['require_args'] || 1 < count($args) ) ) { // FALSE if appliance is not a class && it can't require args || the args are greater than 1 because we are inserting the kitchen object
                    $_reflection = new ReflectionClass($appliance['class_name']);
                    $this->{$property} = $_reflection->newInstanceArgs($args);
                    return true;
                } else {
                    return TRUE; // Just tell them we finished
                }

            } // End disabled check

        } else {
            return FALSE;
        }
    }


    /**
     * Load deferred appliances (mostly for pluggable functions)
     * Callback on after_theme_setup action hook
     *
     * @since       0.1
    */
    public static function loadAppliancesAfterThemeSetup() {
        foreach (self::$_appliances_after_theme_setup as $shortname) {
            require_once self::$_appliances[$shortname]['path']; // Load the file
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
                foreach (self::$_appliances as $key => $value) {
                    $this->load($key);
                }
            break;
            case 'default_plus':
                $this->load('wp_sensible_defaults');
                $this->load('wp_sensible_defaults_admin');
                $this->load('help');
                $this->load('seo');
                $this->load('forms');
                $this->load('wordpress');
                $this->load('options');
                $this->load('metabox');
                $this->load('widget_nav_post');
                $this->load('widget_nav_posts');
                $this->load('widget_jit_sidebar');
                $this->load('jit_message');
                $this->load('lightbox');
            break;
            default:
                $this->load('wp_sensible_defaults');
                $this->load('wp_sensible_defaults_admin');
                $this->load('help');
                $this->load('seo');
                $this->load('wordpress');
                $this->load('forms');
                $this->load('lightbox');
            break;
        }
    }

    /**
     * Add loaded appliances to core options group
     * Give the site/blog owner as much control as is reasonable
     *
     * @since       0.1
     * @global      array $GLOBALS['kst_core']
     * @global      array $GLOBALS['kst_core_options']
     * @uses        KST_Kitchen::$kst_core_options
     * @uses        KST_Kitchen::$_appliances
    */
    public static function addLoadedApplianceCoreOptions() {

        // If no kitchen has loaded then simplify the 'about kst core options' and help the site/blog owner
        if ( KST::isCoreOnly() ) {
            unset($GLOBALS['kst_core_options']['options']);
            $GLOBALS['kst_core_options']['options'] = array(
                        'core_main' => array(
                                        "name"      => 'About Kitchen Sink HTML5 Base',
                                        "desc"      => "
                                                        <p>Some themes and plugins rely on Kitchen Sink HTML5 Base (KST) to operate.</p>
                                                        <p>However you current WordPress Install is not using any KST dependent functionality.<br />You should consider deactivating Kitchen Sink HTML5 Base if...</p>
                                                        <ol> <li><small>You are 99% sure you won't be using a KST dependent theme or plugin in the near future</small></li> <li><small>You have verified all <strong>active</strong> <a href='themes.php'>themes</a> and <a href='plugins.php?plugin_status=active'>plugins</a> on your install do not use KST</small></li> <li><small>You have verified all <strong>inactive</strong> <a href='themes.php'>themes</a> and <a href='plugins.php?plugin_status=inactive'>plugins</a> on your install do not use KST</small></li> </ol>
                                                        <p>If all of the above items are true then you should consider deactivating or uninstalling Kitchen Sink HTML5 Base.</p>
                                                        <p style='margin-top: 36px;'>Learn more about <a href='http://beingzoe.com/zui/wordpress/kitchen_sink/'>Kitchen Sink HTML5 Base</a></p>
                                                    ",
                                        "type"      => "section",
                                        "is_shut"   => FALSE
                                        )
                        );
        } else {

            // Create a core appliance section
            $GLOBALS['kst_core_options']['options']['core_appliances']['name'] = 'KST Appliances used in your theme';
            $GLOBALS['kst_core_options']['options']['core_appliances']['desc'] = 'The following features and functionality are used by your theme or plugins.';
            $GLOBALS['kst_core_options']['options']['core_appliances']['type'] = 'section';
            // Add loaded appliances only to that section
            foreach (self::$_appliances_loaded as $shortname) {
                $GLOBALS['kst_core_options']['options'][$shortname . "_section"]['name'] = self::$_appliances[$shortname]['friendly_name'];
                $GLOBALS['kst_core_options']['options'][$shortname . "_section"]['desc'] = self::$_appliances[$shortname]['desc'];
                $GLOBALS['kst_core_options']['options'][$shortname . "_section"]['type'] = 'subsection';
                // Can it be disabled safely?
                if ( TRUE == self::$_appliances[$shortname]['can_disable'] ) {
                    $GLOBALS['kst_core_options']['options']["disable_{$shortname}"]['name'] = 'Disable this appliance';
                    $GLOBALS['kst_core_options']['options']["disable_{$shortname}"]['desc'] = "({$shortname})";
                    $GLOBALS['kst_core_options']['options']["disable_{$shortname}"]['type'] = 'checkbox';
                    $GLOBALS['kst_core_options']['options']["disable_{$shortname}"]['default'] = FALSE;
                }

            }

            if ( 0 < count(self::$_appliances_disabled) ) {
                // Create a core appliance section
                $GLOBALS['kst_core_options']['options']['core_appliances_disabled']['name'] = 'KST Appliances you disabled';
                $GLOBALS['kst_core_options']['options']['core_appliances_disabled']['desc'] = 'Uncheck the <em>Disable this appliance</em> option to reactivate any disabled appliance';
                $GLOBALS['kst_core_options']['options']['core_appliances_disabled']['type'] = 'section';
                // Add loaded appliances only to that section
                foreach (self::$_appliances_disabled as $shortname) {
                    $GLOBALS['kst_core_options']['options'][$shortname . "_section"]['name'] = self::$_appliances[$shortname]['friendly_name'];
                    $GLOBALS['kst_core_options']['options'][$shortname . "_section"]['desc'] = self::$_appliances[$shortname]['desc'];
                    $GLOBALS['kst_core_options']['options'][$shortname . "_section"]['type'] = 'subsection';
                    // Same exact as before it was disabled!
                    $GLOBALS['kst_core_options']['options']["disable_{$shortname}"]['name'] = 'Disable this appliance';
                    $GLOBALS['kst_core_options']['options']["disable_{$shortname}"]['desc'] = "({$shortname})";
                    $GLOBALS['kst_core_options']['options']["disable_{$shortname}"]['type'] = 'checkbox';
                    $GLOBALS['kst_core_options']['options']["disable_{$shortname}"]['default'] = FALSE;
                }
            }

        }

        // Load all core options here!
        $GLOBALS['kst_core']->options->add($GLOBALS['kst_core_options']);
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

    public static function getAllKitchenSettingsArrays() {
        return self::$_all_kitchen_settings_array;
    }


    /**
     * Overload to create disabled appliance objects
     *
     * Accepts a request to use an object property
     * Creates an empty class that will return false
     * for all method calls.
     *
     * We encourage all KST developers to make their
     * appliances capable of being disabled for maximum
     * flexibility for the site/blog owner. However,
     * when doing so be absolutely sure your appliance will
     * kill the site because it NEEDS method calls to return
     * values!
     *
     * @since       0.1
    */
    public function __get($name) {
        $this->{$name} = new KST_disabled;
        return $this->{$name};
    }
}

/**
 * Dummy class to handle overloading any disabled appliance calls
 *
 * @since       0.1
*/
class KST_Disabled {
     public function __call($name, $arguments) {
        return false;
    }
}
