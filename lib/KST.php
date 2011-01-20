<?php 
/**
 * KST_Options
 * Kitchen Sink Class: Options
 * Methods to rapidly create and use WordPress options (and just admin content pages)
 * Creates menu item and builds options/content page using options array you create
 * 
 * @author		zoe somebody
 * @link        http://beingzoe.com/
 * @author		Scragz
 * @link        http://scragz.com/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  KitchenSinkClasses
 * @version     0.1
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
*/
class KST {
    
    public static $extant_options; // Array of options that exist IF they were checked with $this->option_exists();
    
    /**#@+
     * @since       0.1
     * @access      protected
    */
    protected $friendly_name;
    protected $prefix;
    protected $namespace;
    protected $developer;
    protected $developer_url;
    protected $admin_pages; // Store all menus/pages from one registered kst doodads
    protected static $admin_pages; // Store all menus/pages from all registered kst doodads
    /**#@-*/
    
    /**
     * Set is_plugins_loaded to false until WP tells us that the plugins are loaded
     * Hook set in init.php
     * @see         KST::pluginsAreLoaded()
     * @access      private
    */
    private static $_plugins_are_loaded = false;
    
    
    /*TESTING TEMPORARY METHODS*/
    public function echo_this() {
        echo "Yup we can do that with " . $this->getFriendlyName() . " " . $this->getPrefix() . " " . $this->getDeveloper() . " " . $this->getDeveloper_url();
    }
    public function test_static_AdminPages() {
        //return self::$admin_pages;
        print_r( self::$admin_pages );
    }
    
    
    /**
     * Register new admin "options" page with KST
     * We will save them up and output them all at once.
     * 
     * @since       0.1
     * @access      public
     * @param       required array $options_array options block types and their parameters
     * @param       required string $menu_title actual text to display in menu
     * @param       optional string $parent_menu 
     * @param       optional string $page_title Explicit title to use on page, defaults to "friendly_name menu_title"
    */
    public function newOptionsPage($options_array, $menu_title, $parent_menu = 'kst', $page_title = FALSE) {

        // Create generic title if none given
        $page_title = ( $page_title ) ? $page_title
                                      : $this->getFriendlyName() . " " . $menu_title;
        
        // Save this options page object in KST static member variable to create the actual pages with later
        // We won't actually add the menus or markup hmtl until we have them all and do sorting if necessary and prevent overwriting existing menus
        $new_page = new KST_AdminPage_OptionsPage( $options_array, $menu_title, $parent_menu, $page_title, $this->namespace );
        // Naming the keys using the menu_slug so we can manipulate our menus later
        self::$admin_pages[$new_page->get_menu_slug()] = $new_page; 
        
        add_action('admin_menu', 'KST_AdminPage::create_admin_pages'); // hook to create menus/pages in admin AFTER we have ALL the options
        
    }
    
    /**
     * Public accessor to get KST managed and namespaced WP theme option
     * 
     * Replaces native get_option for convenience
     * So instead of get_option("namespace_admin_email");
     * you can $my_theme_object->getOption("admin_email");
     * 
     * @since 0.1
     * @see         KST_AdminPage_OptionsPage::getOption()
     * @param       required string option 
     * @param       optional string default ANY  optional, defaults to null
     * @uses        KST_Options::do_namespace_option_id
     * @uses        get_option() WP function
     * @return      string
    */
    public function getOption($option, $default = null) {
        return KST_AdminPage_OptionsPage::getOption($this->namespace, $option, $default);
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
     * @see         KST_AdminPage_OptionsPage::getOption()
     * @global      $wpdb
     * @param       required string $option 
     * @return      boolean
    */ 
    public function doesOptionExist( $option ) {
        
        $namespaced_option = $this->namespace . $option;
        $skip_it = FALSE; // Flag used to help skip the query if we've checked it before
        
        // Check to see if the current key exists 
        if ( !array_key_exists( $namespaced_option, self::$extant_options ) ) { // Don't know yet, so make a query to test for actual row
            $does_exist = KST_AdminPage_OptionsPage::getOption($this->namespace, $option, $default);
        } else { // The option name exists in our "extantoptions" array so just skip it
            $skip_it = true;
        }
        
        /* Return the answer */
        if ( $skip_it || is_object( $row ) ) { // The option exists regardless of trueness of value
            self::$extant_options[$namespaced_option]['exists'] = true; // Save in array if exists to minimize repeat queries
            return true;
        } else { // The option does not exist at all
            return false;
        }
    }
    
    /**
     * Everything involving options is namespaced "kst_prefix_"
     * e.g. options, option_group, menu_slugs
     * Still try to be unique to avoid collisions with other KST developers
     * 
     * @since       0.1
     * @param       required string $item    unprefixed option name
     * @uses        KST_AdminPage_OptionsPage::prefix
     * @return      string
    */
    protected function _formatNamespace() {
        return "kst_" . $this->prefix . "_";
    }
    
    /**
     * Public accessor for static member variable self::$admin_pages
     * 
     * @since       0.1
     * @return      array
    */
    public static function getAdminPages() {
        return self::$admin_pages;
    }
    
    /**
     * Public accessor for getting stored objects from static member variable self::$admin_pages array
     * 
     * @since       0.1
     * @param       required string $key
     * @return      object
    */
    public static function getAdminPage( $key ) {
        return self::$admin_pages['$key'];
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
    
        
    /*
     * PUBLIC STATIC INIT METHODS
    */
     
    /**
     * KST presets
     * 
     * @since       0.1
    */
    public static function initPreset( $preset = 'default') {
        switch ($preset) {
            case 'minimum':
                self::initSensibleDefaults();
                self::initHelp();
            break;
            case 'and_the_kitchen_sink':
                self::initSensibleDefaults();
                self::initHelp();
                self::initSEO();
                self::initContact();
                self::initWidgetNavPost();
                self::initWidgetNavPosts();
                self::initWPMediaNormalize();
                self::initJqueryJitMessage();
                self::initWidgetJitSidebar();
                self::initJqueryCycle();
                self::initJqueryToolsScrollable();
            break;
            default:
                self::initSensibleDefaults();
                self::initHelp();
                self::initSEO();
                self::initContact();
            break;
        }
    }
    
    /**
     * HTML5 Boilerplate, WP normalization, and smart stuff
     * 
     * @since       0.1
    */
    public static function initSensibleDefaults() {
        require_once KST_DIR_LIB . '/functions/wp_sensible_defaults.php';
        require_once KST_DIR_LIB . '/functions/wp_admin.php';
    }
    
    /**
     * KST extensible help file(s)
     * 
     * @since       0.1
    */
    public static function initHelp() {
        require_once KST_DIR_LIB . '/functions/theme_help.php';  
    }
    
    /**
     * KST basic SEO
     * Require KST_options (autoloads if necessary)
     * Creates it's own options
     * Creates a meta box for editing SEO per post page
     * 
     * @since       0.1
    */
    public static function initSEO() {
        require_once KST_DIR_LIB . '/functions/seo.php';  
    }
    
    /**
     * KST email contact functionality
     * 
     * @since       0.1
     * @todo        Make this
    */
    public static function initContact() {
          
    }
    
    /**
     * WP WIDGET: KST post to post next/previous post buttons for sidebar (only on single blog posts)
     * 
     * @since       0.1
    */
    public static function initWidgetNavPost() {
        KST_Widget::registerWidget('NavPost');
    }
    
    /**
     * WP WIDGET: KST Page to page older/newer browse posts buttons for sidebar (only on single blog posts)
     * 
     * @since       0.1
    */
    public static function initWidgetNavPosts() {
        KST_Widget::registerWidget('NavPosts');
    }
    
    /**
     * WP media normalization bundled preset
     * lightbox, mp3player, etc...
     * 
     * @since       0.1
    */
    public static function initWPMediaNormalize() {
        self::initJqueryLightbox();
        self::initMp3Player();
    }
    
    /**
     * KST/jQuery: lightbox library using fancybox and sensible integration 
     * with all posts/pages and attachment/caption normalization.
     * Especially filters hacks for [gallery] shortcode
     * 
     * @since       0.1
    */
    public static function initJqueryLightbox() {
        require_once KST_DIR_LIB . '/functions/jquery/lightbox.php'; // javascript lightbox; 
    }
    
    /**
     * KST mp3player
     * auto load mp3 player when mp3 linked directly with shortcodes
     * used in attachment.php if exists
     * 
     * @since       0.1
    */
    public static function initMp3Player() {
         require_once KST_DIR_LIB . '/functions/mp3_player.php'; // mp3 player shortcode - 
    }
    
    /**
     * KST/jQuery: KST JIT (Just-in-Time) message (sliding out a panel on a trigger)
     * Creates a meta box for easily selecting the post to link to or the message to "say"
     * 
     * @since       0.1
    */
    public static function initJqueryJitMessage() {
        require_once KST_DIR_LIB . '/functions/jquery/jit_message.php'; 
    }
    
    /**
     * WP WIDGET: KST JIT (Just-in-Time) Sidebar (Magic relative/fixed sidebars)
     * Throw in sidebar widgets just above the last few items you want to scroll down the page
     * 
     * @since       0.1
    */
    public static function initWidgetJitSidebar() {
        KST_Widget::registerWidget('JitSidebar');
    }
    
    /**
     * KST/jQuery: malsup cycle content (content slideshow with shortcodes)
     * Once invoked you can use cycle normally as well
     * 
     * @since       0.1
    */
    public static function initJqueryCycle() {
        require_once KST_DIR_LIB . '/functions/jquery/cycle.php'; 
    }
    
    /**
     * KST/jQuery: tools: scrollable (content slideshow with shortcodes)
     * Once invoked you can use cycle normally as well
     * 
     * @since       0.1
    */
    public static function initJqueryToolsScrollable() {
        require_once KST_DIR_LIB . '/functions/jquery/scrollables.php';
    }
    
    /**
     * PUBLIC STATIC HELPER METHODS
    */
     
    /**
     * Set is_plugins_loaded
     * Flag to know whether we are initializing a plugin or the active theme
     * Hook set in init.php
     * 
     * @since       0.1
    */
    public static function pluginsAreLoaded() {
        self::$_plugins_are_loaded = true;
    }
     
    /**
     * Get is_plugins_loaded
     * Flag to know whether we are initializing a plugin or the active theme
     * 
     * @since       0.1
    */
    public static function arePluginsLoaded() {
        return self::$_plugins_are_loaded;
    }
     
    /**
     * In order for the companion plugin concept to work we have to make sure that
     * Base loads first, so on activation we move it the start of the list.
     *
     * This might could be done better
     * 
     * @since       0.1
     * @see         http://wordpress.org/support/topic/how-to-change-plugins-load-order
     * @todo        Clean this up
     * @todo        Create a hook for KST plugin developers to use to make sure they load after this
    */
    public static function loadAsFirstPlugin() {
        global $active_plugins;
        // ensure path to this file is via main wp plugin path
        $wp_path_to_this_file = preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR."/$2", __FILE__);
        $this_plugin = plugin_basename(trim($wp_path_to_this_file));
        $active_plugins = get_option('active_plugins');
        $this_plugin_key = array_search($this_plugin, $active_plugins);
        if ($this_plugin_key) { // if it's 0 it's the first plugin already, no need to continue
            array_splice($active_plugins, $this_plugin_key, 1);
            array_unshift($active_plugins, $this_plugin);
            update_option('active_plugins', $active_plugins);
        }
    }
}
