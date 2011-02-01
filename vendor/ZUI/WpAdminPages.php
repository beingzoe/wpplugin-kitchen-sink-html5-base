<?php
/**
 * Automatically create and manage WordPress admin menu/pages
 *
 * @package     ZUI
 * @subpackage  WordPress
 * @author      zoe somebody
 * @link        http://beingzoe.com/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @uses        WP_AdminMenus
 * @uses        ZUI_PhpHelper
 * @uses        ZUI_FormHelper
*/

/**
 * Dependent class for auto building the forms for 'auto' setting in ZUI_WpAdminPages
 *
 * @since       0.1
*/
require_once 'FormHelper.php';


/**
 * Methods for creating any number of WordPress Admin Menu Pages
 *
 * Add new pages by:
 *      -callback (echoes output)
 *      -included file (includes output)
 *      -'auto' (builds form content from simple array)(only for options page)
 *
 * Also Methods for adding options to WordPress to manage and
 * adding corresponding page(s) for display
 *
 * Uses an array to manage all information pertaining to menu/page creation
 * and the settings to register/manage if the page is an "options" page
 *
 * WP helper class. Has no concept of namespacing - namespace what you send!
 *
 * @version         0.1
 * @since           0.1
*/
class ZUI_WpAdminPages {

    /**#@+
     * @since       0.1
     * @access      protected
    */
    protected static $_all_page_hooknames = array(); // array of hooknames WP uses to callback when viewing a created menu/page - we might not need to track this
    protected static $_all_parent_slugs = array(
            'dashboard' => 'index.php',
            'posts' => 'edit.php',
            'media' => 'upload.php',
            'links' => 'link-manager.php',
            'pages' => 'edit.php?post_type=page',
            'comments' => 'edit-comments.php',
            'appearance' => 'themes.php',
            'plugins' => 'plugins.php',
            'users' => 'users.php',
            'tools' => 'tools.php',
            'settings' => 'options-general.php'
            );
    /**#@-*/


    /**#@+
     * @since       0.1
     * @access      protected
    */
    protected $_page_data_array; // The current page's option group array if the current page is an option page
    protected $_isCallbackOrTemplate; // Flag to determine where to find the content for this page. 'callback' = function to return. 'template' = file to include.
    protected $_settingsIds = array(); // A flat array of just the settings id's that need registered with WP
    protected $_page_type; // 'options' or 'content';
    /**#@-*/


    /**
     * Constructor for page objects
     * All page types (options or static content) use the same $page_data_array
     *
     * @uses        ZUI_WpAdminPages::_page_data_array
     * @uses        ZUI_WpAdminPages::_isCallbackOrTemplate
     * @uses        ZUI_WpAdminPages::newOptionGroup()
     * @param       required array  $page_array           Info about this OptionsGroup, the page that should display it, and all of the settings that belong to this option_group
    */
    public function __construct( $page_data_array ) {

        // Save the array and use it from now on
        $this->_page_data_array = $page_data_array;

        // Determine how this page will be displayed and store that as a flag
        if (stripos( basename( $this->_page_data_array['view_page_callback'] ), '.php' ) ) {
            $this->_isCallbackOrTemplate = 'template';
        } else if ( 'auto' == $this->_page_data_array['view_page_callback'] ) {
            $this->_isCallbackOrTemplate = 'auto'; // This should be autodetected by the state of the options sent to manage? - see next chunk
        } else {
            $this->_isCallbackOrTemplate = 'callback';
        }

        // Fix the parent_slug - Because we allow friendly names to be used for existing WP sidebars
        $this->_page_data_array['parent_slug'] = $this->fixParentSlug($this->_page_data_array['parent_slug']); // now we can just rely on the array's parent_slug explicitly

        // Check to see if this is an "options" page (i.e. settings) - if the options key is present and it is an array
        if ( isset($this->_page_data_array['options']) && is_array($this->_page_data_array['options']))
            $this->newOptionGroup();

        // Optionally tet them set a priority
        if ( isset($this->_page_data_array['priority']) )
            $priority = $this->_page_data_array['priority'];
        else
            $priority = NULL;

        // Register hook for next step
        add_action('admin_menu', array(&$this, 'prepMenu'), $priority); // Go last if we can so we know where we are it

    }


    /**
     * Register options ('settings' in WP) for an entire option group
     *
     * @param       required array  $option_group_array           Info about this OptionsGroup, the page that should display it, and all of the settings that belong to this option_group
    */
    public function newOptionGroup() {

        // Set type of page
        $this->_page_type = 'options';

        // Get our option keys and save them to register with WP
        foreach ($this->_page_data_array['options'] as $key => $option) {
           if ( is_array($option) ) {
               if ( !empty($key) && in_array($option['type'], ZUI_FormHelper::get_blocks_of_type_form()) ) { // Don't do 'non-form element' blocks
                   $this->_settingsIds[] = $key;
               }
           } else {
               $this->_settingsIds[] = $option;
           }
        }

    }


    /**
     * Method to request a menu/page and settings for it registered (if options page)
     * Adds Menu to WP via addMenu()
     * Adds hook to actually register options (i.e settings) with WP
     *
     * @since       0.1
     * @uses        ZUI_WpAdminPages::addMenu()
     * @uses        add_action() WP function
    */
    public function prepMenu() {

        // Now tell WP all about it - add_menu_page and/or add_submenu_page
        $hookname = $this->addMenu();

        // Hook to register settings with WP so it will manage the updates for us - IF options
        if ( 'options' == $this->_page_type )
            add_action('admin_init', array(&$this, 'registerSettings'));

    }


    /**
     * Add a new menu/page to WordPress (irrespective of type or purpose i.e. doesn't need to be an options page)
     *
     * @since       0.1
     * @uses        ZUI_WpAdminPages::$_page_data_array
     * @uses        add_menu_page() WP function
     * @uses        add_submenu_page() WP function
    */
    function addMenu() {

        // We might want to have defaults merged in before we extract!
        extract($this->_page_data_array);

        if ( !isset($icon_url) )
            $icon_url = NULL;
        if ( !isset($position) )
            $position = NULL;

        // Determine if this is a new top level i.e. new parent_slug or a child menu for an existing menu
        if ( !in_array($this->_page_data_array['parent_slug'], self::$_all_parent_slugs) ) { // must be a top level since we haven't seen it before
            if ( isset($top_only) && 'section_only' == $top_only )
                $callback = create_function('', 'return false;');
            else
                $callback = array($this, 'viewPage');

            $hookname = add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url, $position ); //returns only the hookname registered (i.e. $page_type . '_page_' . $plugin_name) , $icon_url, $position      $view_page_callback
        }

        // Per WP best practice (de facto practice except for comments where they KNOW there are no children coming) the first menu item in section will always be the same as the parent_menu
        // Unless they explicity choose to suppress the submenu
        if ( !isset($top_only) )
            $hookname = add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, array($this, 'viewPage'), $icon_url, $position ); //, $icon_url, $position       $view_page_callback

        // Save the hookname to use later
        self::$_all_page_hooknames[] = $hookname; // This might go away as unnecessary
        self::$_all_parent_slugs[] = $this->_page_data_array['parent_slug'];

        // Return the hookname that WP will use to view the page
        return $hookname;
    }


    /**
     * Callback method for WP to register options (i.e. settings) (so WP can manage)
     *
     * @since       0.1
     * @uses        ZUI_WpAdminPages::$_settingsIds
     * @uses        ZUI_WpAdminPages::$_page_data_array
     * @uses        register_setting() WP function
    */
    public function registerSettings() {
        // Register all the options (i.e. settings) for this option group for WP to manage (WP will handle updates)
        foreach ( $this->_settingsIds as $option) {
            // check to see if $option is a key, if so then we also need to check what type of 'block' it is (for automatic form creation) and only add it if this is an option form element
            register_setting( $this->_page_data_array['option_group_name'], $option );
        }
    }


    /**
     *  Callback method to display ALL types of admin pages
     *
     * @since       0.1
     * @uses        delete_option() WP function
     * @uses        wp_verify_nonce() WP function
     * @todo        wp_verify_nonce() doesn't seem to actually work?
     * @todo        improve security!
    */
    public function viewPage() {

        // If options page check to see if $_REQUEST['action'] was sent for a settings 'reset'
        if ( 'options' == $this->_page_type ) {
            if ( isset( $_REQUEST['action'] ) ) { // If we have an action to take

                $nonce = $_REQUEST['reset_nonce'];
                if ( !wp_verify_nonce($nonce, 'reset_nonce') )
                    die('Security check'); // This doesn't seem to work?

                $request_save_action = $_REQUEST['action']; // only initialized here to prevent warnings
                $redirect_to = $_REQUEST['_wp_http_referer']. "&reset=true";

                // Delete each option
                if ( 'reset' == $request_save_action ) {
                    foreach ($this->_page_data_array['options'] as $key => $block) {
                        delete_option( $key ); // bye-bye
                    }

                    header("Location: " . $redirect_to);
                    exit;
                } // End ACTIONS
            } // END if action
        }

        // Fucked up exception? If you add a top level only and set it's
        //if ( !isset($this->_page_data_array['top_only']) || (isset($this->_page_data_array['top_only']) && 'header_only' != $this->_page_data_array['top_only']) ) {}

        // OUTPUT the actual pages
        echo "<div class='wrap kst_options'>"; //Standard WP Admin content class plus our class to style with
            echo "<h2>" . $this->_page_data_array['page_title'] . "</h2>";
            // If you can't manage your options then you can't have any menus - speed things up a bit
            if ( !current_user_can($this->_page_data_array['capability']) )  {
                wp_die( __('You do not have sufficient permissions to access this page.') );
            }

             // Give response on action
            if ( isset($_REQUEST['updated']) )
                echo "<div id='message' class='updated fade'><p><strong>" . $this->_page_data_array['page_title'] . " saved.</strong></p></div>";
            else if ( isset($_REQUEST['reset']) )
                echo "<div id='message' class='updated fade'><p><strong>" . $this->_page_data_array['page_title'] . " reset.</strong></p></div>";

            // Now get whatever content they told us to get

            if ( 'auto' == $this->_isCallbackOrTemplate ) { // Build them a page using the form builder
                echo "<form method='post' id='poststuff' class='metabox-holder' action='options.php'>";
                    echo '<div class="meta-box-sortables" id="normal-sortables">'; // Attempting to utilize as much WP style/formatting as possible
                        echo ZUI_FormHelper::makeForm($this->_page_data_array);
                        settings_fields( $this->_page_data_array['option_group_name'] );
                        echo "<p class='submit'>";
                            echo "<input type='submit' class='button-primary' value='Save Changes' />";
                        echo "</p>";
                    echo "</div><!--End .normal-sortables-->"; // End normal-sortables
                echo "</form>";

                echo "<form method='post' class='metabox-holder'>";
                    echo "<p class='submit'>";
                        $reset_nonce = wp_create_nonce('reset_nonce'); // make a nonce
                        echo "<input type='hidden' name='reset_nonce' value='" . $reset_nonce . "' />"; //send the nonce
                        settings_fields( $this->_page_data_array['option_group_name'] ); // just need the wp referer to redirect with really
                        echo "<input type='submit' class='button-secondary' value='Reset' title='Delete current settings and reset to defaults' />";
                        echo "<input type='hidden' name='action' value='reset' />";
                    echo "</p>";
                echo "</form>";

            } else if ( 'template' == $this->_isCallbackOrTemplate ) { // Include a .php template
                include $this->_page_data_array['view_page_callback'];

            } else if ( 'callback' == $this->_isCallbackOrTemplate && function_exists($this->_page_data_array['view_page_callback']) ) { // supplied callback function
                $callback = $this->_page_data_array['view_page_callback'];
                $callback(); // Output their callback;

            } else {
                wp_die("zui WP AdminPage Error: We don't know how to display your admin page<br />For the 'view_page_callback' parameter enter either the name of a valid callback function or the full path to the template file to include that contains your content.");
            }

        echo  "</div>"; // End options page 'wrap'

    }


    /**
     * Get the parent_slug for this page
     *
     * Protects us from page name changes and deal with top level menus later
     * Accepts:
     *          A friendly name_ of existing WP sidebar menu titles e.g. 'appearance', 'Plugins', 'settings'
     *          An explicit slug of existing WP sidebar menu sections by their actual slug in WordPress e.g. 'themes.php', 'plugins.php', 'options-general.php'
     *          'new_custom_top_level_menu_slug'
     *              If this is a new top level then parent_slug should match the current menu_slug.
     *              If this is a child menu tiem for a new top level then it should be a menu_slug you have already created
     *              Must match exactly the explicit actual slug of the menu you created or it's own menu_slug.
     *              Must be in the form of a slug e.g. 'my_custom_menu_slug', 'super_duper_plugin_options'
     *
     * @since       0.1
     * @see         http://codex.wordpress.org/Administration_Menus
     * @uses        ZUI_WpAdminPages::$_all_parent_slugs
     * @param       required string $parent_slug_to_check could have gotten it from the objects array but this seemd more flexible
     * @return      string
     * @link        http://codex.wordpress.org/Adding_Administration_Menus
    */
    public static function fixParentSlug($parent_slug_to_check) {
        switch ( $parent_slug_to_check ) {
            case 'dashboard':
                return self::$_all_parent_slugs['dashboard'];
            case 'posts':
                return self::$_all_parent_slugs['posts'];
            case 'media':
                return self::$_all_parent_slugs['media'];
            case 'links':
                return self::$_all_parent_slugs['links'];
            case 'pages':
                return self::$_all_parent_slugs['pages'];
            case 'comments':
                return self::$_all_parent_slugs['comments'];
            case 'appearance':
                return self::$_all_parent_slugs['appearance'];
            case 'plugins':
                return self::$_all_parent_slugs['plugins'];
            case 'users':
                return self::$_all_parent_slugs['users'];
            case 'tools':
                return self::$_all_parent_slugs['tools'];
            case 'settings':
                return self::$_all_parent_slugs['settings'];
            default: // Explicitly sent a WP slug or a new top level slug or is child of custom top level -  must be sent the final slug - you are on your own ;)
                return $parent_slug_to_check;
        }

    }

    /**
     * Get these extant parent_slugs
     *
     * @since       0.1
     * @access      public
     * @return      string
    */
    public static function get_all_parent_slugs() {
        return self::$_all_parent_slugs;
    }

    /**
     * Move one menu section UP in the WP $menu array ABOVE another one
     *
     *
     * Ideally we'll just patch WP_AdminMenu classes to do before, after, and at PROPERLY
     * as this is pretty hacked. For now we'll be content with moving our stuff
     * to a known location however inefficiently
     *
     * @global      array $menu Wp admin menus
     * @uses        WP_AdminMenus
     * @uses        ZUI_PhpHelper
     * @uses        WP_AdminMenus::swap_admin_menu_sections()
     * @uses        ZUI_PhpHelper::array_search_recursive()
    */
    public static function moveMenuSectionUpAboveAnother($menu_key_to_move, $menu_key_to_move_above) {

        global $menu;

        // Check the menu
        if ( array_key_exists($menu_key_to_move, $menu) && is_array($menu[$menu_key_to_move])) {
                $move_this = $menu[$menu_key_to_move][2]; // 2nd index holds the menu_slug WP_AdminMenu classes can work with to 'swap' menus

            // We are going to swap with everything between our start index and appearance for each of our menus
            foreach (array_reverse(array_keys($menu)) as $menu_index) { // Start with the last element before us
                if ($menu_index >= $menu_key_to_move_above && $menu_index <= $menu_key_to_move) { // preserve all the menus that MIGHT exist between us and the goal
                    $swap_with = $menu[$menu_index][2];
                    swap_admin_menu_sections($swap_with, $move_this);
                }
            }
            return TRUE;
        }
        return FALSE;
    }


    /**
     * Find the current key for any menu_slug in the global WordPress $menu array
     *
     * @uses        ZUI_PhpHelper::array_search_recursive()
     * @return      mixed int or FALSE
    */
    public static function findCurrentKeyOfWpMenuSection($menu_slug) {
        // Need a recursive array search - for now
        require_once KST_DIR_VENDOR . '/ZUI/PhpHelper.php';

        global $menu;

        $array_key_path = ZUI_PhpHelper::array_search_recursive($menu_slug, $menu);

        if ( is_array($array_key_path) ) {
            return $array_key_path[0];
        } else {
            return FALSE;
        }
    }

}
