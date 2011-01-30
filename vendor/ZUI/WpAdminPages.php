<?php
/**
 * Page level doc block
 *
 *
*/

/**
 * Dependent class
*/
//require_once 'FormHelper2.php';
require_once 'FormHelper2.php';
/**
 * Methods for creating any number of WordPress Admin Menu Pages
 *
 * Methods for adding options to WordPress to manage and
 * encapsulating access to adding corresponding page(s)
 *
 * WP helper class. Has no concept of namespacing - namespace what you send!
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
    protected $_option_group_array; // The current page's option group array if the current page is an option page
    protected $_isCallbackOrTemplate; // Flag to determine where to find the content for this page. 'callback' = function to return. 'template' = file to include.
    protected $_settingsIds = array(); // A flat array of just the settings id's that need registered with WP
    /**#@-*/

    /**
     * Register options ('settings' in WP) for an option group
     *
     * @param       required array  $option_group_array           Info about this OptionsGroup, the page that should display it, and all of the settings that belong to this option_group
    */
    public function newOptionGroup( $option_group_array ) {

        /*echo "we added the new options group.<br />";

        print_r($option_group_array);*/

        // Save the array
        $this->_option_group_array = $option_group_array;

        // Determine how this page will be displayed and store that as a flag
        if (stripos( basename( $this->_option_group_array['view_page_callback'] ), '.php' ) ) {
            $this->_isCallbackOrTemplate = 'template';
        } else if ( 'auto' == $this->_option_group_array['view_page_callback'] ) {
            $this->_isCallbackOrTemplate = 'auto'; // This should be autodetected by the state of the options sent to manage? - see next chunk
        } else {
            $this->_isCallbackOrTemplate = 'callback';
        }

        // Get our option keys and save them to register with WP
        foreach ($this->_option_group_array['options'] as $key => $option) {
           if ( is_array($option) ) {
               if ( !empty($key) ) {
                   $this->_settingsIds[] = $key;
                   /*echo "key=" . $key . "<br />";*/
               }
           } else {
               $this->_settingsIds[] = $option;
               /*echo "option=" . $option . "<br />";*/
           }
        }

        // Fix the parent_slug - Because we allow friendly names to be used for existing WP sidebars
        $this->_option_group_array['parent_slug'] = $this->_getParentSlug($this->_option_group_array['parent_slug']); // now we can just rely on the array's parent_slug explicitly

        // Register hook for next step
        add_action('admin_menu', array(&$this, 'prepMenu'), 999); // Go last if we can so we know where we are it

        /*
        echo "<br /><br />settingsids=<br />";
        print_r($this->_settingsIds);
        echo "<br /><br />";

        echo "<br /><br />";
        print_r($this->_option_group_array);
        echo "<br /><br />";

        echo "<br /><br />";
        print_r($this->_option_group_array['options']);
        echo "<br /><br />";
        */




    }


    /**
     * Method to register options (i.e. settings) with WP to manage
    */
    public function prepMenu() {

        //echo "we are prepping the date to create the menu.<br />";

        // Now tell WP all about it - add_menu_page and/or add_submenu_page
        $hookname = $this->addMenu();

        // Hook to register settings with WP so it will manage the updates for us
        add_action('admin_init', array(&$this, 'registerSettings'));

    }


    /**
     * Add a new menu/page to WordPress (irrespective of type or purpose i.e. doesn't need to be an options page)
     *
     * @since       0.1
     * @uses        ZUI_WpAdminPages::$_option_group_array
     * @uses        add_menu_page() WP function
     * @uses        add_submenu_page() WP function
    */
    function addMenu() {

        // We might want to have defaults merged in before we extract!
        extract($this->_option_group_array);

        /*
        echo "<br />Extracted...<br />";
        echo "parent_slug=" . $parent_slug . "<br />";
        echo "page_title=" . $page_title . "<br />";
        echo "menu_title=" . $menu_title . "<br />";
        echo "capability=" . $capability . "<br />";
        echo "menu_slug=" . $menu_slug . "<br />";
        echo "view_page_callback=" . $view_page_callback . "<br />";
        echo "icon_url=" . $icon_url . "<br />";
        echo "position=" . $position . "<br />";


        */
        // Determine if this is a new top level i.e. new parent_slug or a child menu for an existing menu
        if ( !in_array($this->_option_group_array['parent_slug'], self::$_all_parent_slugs) ) { // must be a top level since we haven't seen it before
            $hookname = add_menu_page( $page_title, $menu_title, $capability, $menu_slug, array($this, 'viewPage') ); //returns only the hookname registered (i.e. $page_type . '_page_' . $plugin_name) , $icon_url, $position      $view_page_callback
        }

        // Per WP best practice (de facto practice except for comments where they KNOW there are no children coming) the first menu item in section will always be the same as the parent_menu
        $hookname = add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, array($this, 'viewPage') ); //, $icon_url, $position       $view_page_callback

        // Save the hookname to use later
        self::$_all_page_hooknames[] = $hookname; // This might go away as unnecessary
        self::$_all_parent_slugs[] = $this->_option_group_array['parent_slug'];

        // Return the hookname that WP will use to view the page
        return $hookname;
    }


    /**
     * Callback method for WP to register options (i.e. settings) (so WP can manage)
     *
     * @since       0.1
     * @uses        ZUI_WpAdminPages::$_settingsIds
     * @uses        ZUI_WpAdminPages::$_option_group_array
     * @uses        register_setting() WP function
    */
    public function registerSettings() {
        // Register all the options (i.e. settings) for this option group for WP to manage (WP will handle updates)
        foreach ( $this->_settingsIds as $option) {
            // check to see if $option is a key, if so then we also need to check what type of 'block' it is (for automatic form creation) and only add it if this is an option form element
            register_setting( $this->_option_group_array['option_group_name'], $option );
        }
    }


    /**
     *  Callback method to display ALL types of admin pages
    */
    public function viewPage() {

                    // This should only exist for Options (i.e. settings) pages
                    if ( isset( $_REQUEST['action'] ) ) { // If we have an action to take
                        $request_save_action = $_REQUEST['action']; // only initialized here to prevent warnings

                        if ( 'top' == $this->_option_group_array['parent_slug'] || 'top-item' == $this->_option_group_array['parent_slug'] ) { // WP uses admin.php for custom top level and children (top-item) so ignore parent_slug and hope they change that
                            $base_page = 'admin.php' . "?page=";
                        } else if ( 'pages' == $this->_option_group_array['parent_slug'] ) { // Fix stupid pages exception with the damn query string in the slug, grrr
                            $base_page = $this->_option_group_array['parent_slug'] . "&page=";
                        } else {
                            $base_page = $this->_option_group_array['parent_slug'] . "?page="; // Finally what it should be
                        }

                        // Delete each option
                        if ( 'reset' == $request_save_action ) {
                            foreach ($this->_option_group_array['options'] as $key => $block) {
                                delete_option( $block['id'] ); // bye-bye
                            }
                            header("Location: " . $base_page . $this->_option_group_array['menu_slug'] . "&reset=true");
                            exit;
                        } // End ACTIONS
                        //} // END if correct page
                    } // END if action

        // OUTPUT the actual pages
        echo "<div class='wrap kst_options'>"; //Standard WP Admin content class plus our class to style with
            echo "<h2>" . $this->_option_group_array['page_title'] . "</h2>";
            // If you can't manage your options then you can't have any menus - speed things up a bit
            if ( !current_user_can($this->_option_group_array['capability']) )  {
                wp_die( __('You do not have sufficient permissions to access this page.') );
            }

             // Give response on action
            if ( isset($_REQUEST['updated']) )
                echo "<div id='message' class='updated fade'><p><strong>" . $this->_option_group_array['page_title'] . " settings saved.</strong></p></div>";
            else if ( isset($_REQUEST['reset']) )
                echo "<div id='message' class='updated fade'><p><strong>" . $this->_option_group_array['page_title'] . " settings reset.</strong></p></div>";

            // Now get whatever content they told us to get
            if ( 'auto' == $this->_isCallbackOrTemplate ) { // Build them a page using the form builder
                //print_r($this->_option_group_array['options']);

                echo "<form method='post' id='poststuff' class='metabox-holder' action='options.php'>";
                    echo '<div class="meta-box-sortables" id="normal-sortables">'; // Attempting to utilize as much WP style/formatting as possible
                        echo ZUI_FormHelperX::makeForm($this->_option_group_array);
                        settings_fields( $this->_option_group_array['option_group_name'] );
                        echo "<p class='submit'>";
                            echo "<input type='submit' class='button-primary' value='Save Changes' />";
                        echo "</p>";
                    echo "</div><!--End .normal-sortables-->"; // End normal-sortables
                echo "</form>";

                echo "<form method='post'>";
                    echo "<p class='submit'>";
                        echo "<input type='submit' class='button-secondary' value='Reset' title='Delete current settings and reset to defaults' />";
                        echo "<input type='hidden' name='action' value='reset' />";
                    echo "</p>";
                echo "</form>";

            } else if ( 'template' == $this->_isCallbackOrTemplate ) { // Include a .php template
                //echo "should be including file now<br />";
                //echo $this->_option_group_array['view_page_callback'];
                include $this->_option_group_array['view_page_callback'];
            } else if ( 'callback' == $this->_isCallbackOrTemplate && function_exists($this->_option_group_array['view_page_callback']) ) { // supplied callback function
                $callback = $this->_option_group_array['view_page_callback'];
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
    protected function _getParentSlug($parent_slug_to_check) {
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

    } // END get_parent_slug()

}
