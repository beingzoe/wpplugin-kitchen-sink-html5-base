<?php
/**
 * Class for adding menus/pages to WP admin
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
 * Compansion classes to encapsulate access to admin menu global arrays $menu and $submenu
 * Likely to be included in future WP core (version 3.2 ???)
 *
 * @since       0.1
 * @see         WP_AdminMenuSection
 * @see         WP_AdminMenuItem
 * @uses        WP_AdminMenu
*/
if ( !function_exists('add_admin_menu_section') && !class_exists('WP_AdminMenuSection') ) {
    require_once KST_DIR_VENDOR . '/WP/AdminMenu.php';
}

class KST_AdminPage {

    /**#@+
     * @since       0.1
     * @access      protected
    */
    protected $namespace;
    protected $menu_title;
    protected $menu_slug; // virtual page name sent to and used by WP;
    protected $parent_menu;
    protected $parent_slug; // WP section slugs; i.e. themes.php or options-general.php or if custom top admin.php?????
    protected $page_title;
    /**#@-*/




    /**#@+
     * Core protected variables to keep tabs on all the kitchens
     *
     * @since       0.1
     * @access      protected
    */
    protected static $_admin_pages; // Store all menus/pages from all registered kst kitchens
    /**#@-*/

    /**
     * @since       0.1
     * @param       required array $options:
     *              required string namespace prefix to prepend everything with
     *              required string menu_title actual text to display in menu
     *              required string parent_menu Used to internally to track and determine final menu positions i.e. set correct parent_slugs
     *              required string page_title Explicit title to use on page
    */
    public function __construct($options) {
        $options += self::defaultOptions();
        $this->namespace = $options['namespace'];
        $this->menu_title = $options['menu_title'];
        $this->menu_slug = $options['menu_slug'];
        $this->parent_menu = $options['parent_menu'];
        $this->page_title = $options['page_title'];
    }

    /**
     * This class and its children require a lot of optional arguments that need defaults.
     * @return array
    */
    static public function defaultOptions() {
        return array(
            'menu_title' => '',
            'menu_slug' => '',
            'page_title' => '',
            'parent_menu' => 'kst',
            'namespace' => null,
            'type_of_kitchen' => 'plugin',
            'friendly_name' => '');
    }


    /**
     * Register new admin "options" page with KST
     * We will save them up and output them all at once.
     *
     * All new pages are added to WP Admin here.
     * This is called after the WP global $menu is set
     * and we have already acted on it if necessary
     * to prevent overwriting existing menus and
     * all KST created menus go where they need to be.
     *
     * @since       0.1
     * @access      public
     * @uses        WP_AdminMenu::add_admin_menu_separator()
     * @uses        add_menu_page() WP function
     * @uses        add_submenu_page() WP function
     * @uses        current_user_can() WP function
     * @param       required object $page everything ready-to-go to send to WordPress to add the page
     * @return      boolean
    */
    public static function create($page) {

        // Do new top level
        if ( in_array( $page->parent_menu, array('top')) ) {
            $testthis = add_menu_page( $page->page_title, $page->menu_title, 'manage_options', $page->menu_slug, array($page,'manage'), ''); //, $icon_url, $position
            // Do submenu; Always add a submenu (if parent the menu_title is used for both parent and submenu per WP best practice) */
            add_submenu_page($page->menu_slug, $page->page_title, $page->menu_title, 'manage_options', $page->menu_slug, array($page,'manage') );//'make_menu_shit'
            //print_r($testthis);
        } else {
            $updated_parent_slug = $page->_getParentSlug($page->parent_menu);
            add_submenu_page($updated_parent_slug, $page->page_title, $page->menu_title, 'manage_options', $page->menu_slug, array($page,'manage') );//'make_menu_shit'
        }

        return true;
    }

    /**
     * Kitchen wants a new option group
     *
     * @since 0.1
     * @uses         KST_AdminPage_OptionsGroup::getOption()
     * @param       required array $options_array
     * @param       required array $options of:
     *              required string namespace prefix to prepend everything with
     *              required string menu_title actual text to display in menu
     *              required string parent_menu Used to internally to track and determine final menu positions i.e. set correct parent_slugs
     *              required string page_title Explicit title to use on page
     * @return      object
     * @todo        test to see if it is faster to let action hook be called multiple times or limit it with has_action()?
    */
    public static function addOptionPage($options_array, $options = array()) {
        $options += self::defaultOptions();
        // Only need actual menu/pages if in WP Admin - speed things up
        if ( is_admin() ) {

            // Make sure we have or array to save all the pages to
            if ( !is_array(self::$_admin_pages) )
                self::$_admin_pages = array();

            // Begin Testing the menu_slug for dupes
            $i = 0;
            $still_checking = TRUE;
            while ( $still_checking ) {
                $unique_menu_slug = $options['menu_slug']; // Reset the unique test name each loop
                if ($i > 0) { // After first loop append _$i to try and find a unique slug
                    $unique_menu_slug .= "_" . $i;
                }
                // Check if the $unique slug exists in our master pages array
                $does_key_exist = ( array_key_exists($unique_menu_slug,  self::$_admin_pages) )
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
            $options['menu_slug'] = $unique_menu_slug;

            // Save this options page object in static member variable to create the actual pages with later
            // We won't actually add the menus or markup html until we have them all and do sorting if necessary and prevent overwriting existing menus
            $new_page = new KST_AdminPage_OptionsGroup( $options_array, $options );

            // Naming the keys using the menu_slug so we can manipulate our menus later
            self::$_admin_pages[$options['menu_slug']] = array( 'type'=>$options['type_of_kitchen'], 'name'=>$options['friendly_name'], 'parent_menu'=>$options['parent_menu'], 'page'=>$new_page);

            if ( !has_action( 'admin_menu', 'KST_AdminPage::createAdminPages' ) ) {
                add_action('admin_menu', 'KST_AdminPage::createAdminPages',999); // hook to create menus/pages in admin AFTER we have ALL the options
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
        $temp_admin_pages = self::$_admin_pages;
        $temp_core_options = array();
        $temp_kst_theme_options = array();
        $temp_kst_plugin_options = array();
        $temp_theme_options = array();
        $temp_plugin_options = array();
        $temp_plugin_other = array();
        // Reorganize: Determine if a kitchen exists and build grouped temporary arrays
        foreach ( $temp_admin_pages as $key => $page ) {
            if ($page['parent_menu'] <> 'core') {
                $doCoreOnly = FALSE; // We have a kitchen so core options will be moved accordingly
                if ( 'kst' == $page['parent_menu'] ) {
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
            unset( $temp_admin_pages[$key] ); // Remove this item from the master array
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

                // Is this menu is the child of a custom top level -  look for the parent menu
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
                $new_page = self::create($page['page']);
            }
        }

        if ( FALSE == $custom_start_index ) {
            // Lastly put all of this above 'Appearance' in the sidebar if we have at least one KST managed menu
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
     * Register the options with WP
     *
     * @since       0.1
     * @uses        KST_AdminPage_OptionsGroup::_prefixWithNamespace()
     * @uses        KST_AdminPage_OptionsGroup::options_array
     * @uses        current_user_can() WP function
     * @uses        wp_die() WP function
     *
     * NOTE: Creates option with namespace prepended
     *       "option_1" is saved to the db as "namespace_option_1"
    */
    public function manage() {

        // This is an options page and we need to think about saving/resetting
        if ( isset( $_REQUEST['action'] ) ) { // If we have an action to take
            $kst_option_save_action = $_REQUEST['action']; // only initialized here to prevent warnings

            if ( 'top' == $this->parent_menu || 'top-item' == $this->parent_menu ) { // WP uses admin.php for custom top level and children (top-item) so ignore parent_slug and hope they change that
                $base_page = 'admin.php' . "?page=";
            } else if ( 'pages' == $this->parent_menu ) { // Fix stupid pages exception with the damn query string in the slug, grrr
                $base_page = $this->parent_slug . "&page=";
            } else {
                $base_page = $this->parent_slug . "?page="; // Finally what it should be
            }

            if ( $_GET['page'] ==  $this->menu_slug ) {  // If we are even on our options page
                if ( 'save' == $kst_option_save_action ) {
                    foreach ($this->options_array as $value) {
                        if ( $value['type'] != 'section' ) {
                            $option_name = $this->_prefixWithNamespace( $value['id'] ); // Name of option to insert
                            $option_value = $_REQUEST[ $option_name ]; // Value of option being inserted
                            $result = update_option( $option_name , $option_value ); // oh-oh-oh it's magic
                        }
                    }
                    header("Location: " . $base_page . $this->menu_slug . "&updated=true");
                    exit;
                } else if( 'reset' == $kst_option_save_action ) {
                    foreach ($this->options_array as $value) {
                        delete_option( $this->_prefixWithNamespace( $value['id'] ) ); // bye-bye
                    }
                    header("Location: " . $base_page . $this->menu_slug . "&reset=true");
                    exit;
                } // End ACTIONS
            } // END if correct page
        } // END if action

        // OUTPUT the actual pages
        echo "<div class='wrap kst_options'>"; //Standard WP Admin content class plus our class to style with
        /*if ( function_exists('screen_icon') )
            screen_icon('options-general');*/
            echo "<h2>" . $this->page_title . "</h2>";
            echo $this->_generate_content();
        echo  "</div>"; // End options page 'wrap'
    }


    /**
     * Get the menu slug for this page
     *
     * Protects us from page name changes and deal with top level menus later
     *
     * @since       0.1
     * @uses        KST_Options::getParentMenuMenuSlug()
     * @uses        KST_Options::parent_menu
     * @return      string
     * @link        http://codex.wordpress.org/Adding_Administration_Menus
    */
    protected function _getParentSlug($parent_menu) {
        switch ( $parent_menu ) {
            case 'top':
                return $this->menu_slug;
            case 'dashboard':
                return 'index.php';
            case 'posts':
                return 'edit.php';
            case 'media':
                return 'upload.php';
            case 'links':
                return 'link-manager.php';
            case 'pages':
                return 'edit.php?post_type=page';
            case 'comments':
                return 'edit-comments.php';
            case 'appearance':
                return 'themes.php';
            case 'plugins':
                return 'plugins.php';
            case 'users':
                return 'users.php';
            case 'tools':
                return 'tools.php';
            case 'settings':
                return 'options-general.php';
            default: // Child of custom top level must be sent the final slug
                return $parent_menu;
        }

    } // END get_parent_slug()

    /**
     * set the parent_slug
     *
     * Used internally to determine menu position
     *
     * @since       0.1
     * return       string
    */
    public function setParentSlug($parent_slug) {
        $this->parent_slug = $parent_slug;
    }

    /**
     *
     * @since       0.1
     * @access      public
     * @return      string
    */
    public function getMenuSlug() {
        return $this->menu_slug;
    }


    /**
     * Get the parent menu's menu slug from passed parent menu object (for submenus)
     *
     * Test the object by getting the parent's menu slug
     * If it fails die and help them out.
     *
     * @since       0.1
     * @uses        KST_Options::$parent_menu_object
     * @uses        KST_Options::$menu_slug
     * return       string
    */
    public function getParentMenuMenuSlug() {

        /*
        if ( is_object($this->parent_menu_object) ) {
            $slug = $this->parent_menu_object->menu_slug;
            if ( empty( $slug ) ) // We can't find what we need in the object you passed
                exit("<h2>Woah, wrong object buddy!</h2><p>I don't recognize that object. Make sure you are passing <code><strong>\$my_parent_menu_object</strong></code> <em>(not in quotes, with the '$', y'know THE object ;)</em> .</p><p>If you aren't in the middle of setting up your custom admin menus using the 'Kitchen Sink KST_Options class' then something is terribly wrong with the world.</p>");
            return $slug;
        } else {
            exit("<h2>Something is terribly wrong</h2><p>Something thinks in Kitchen Sink class KST_Options needs something from it's parent menu object. But that object doesn't exit.</p>");
        }
        */
    }


    /**
     * Get the parent_menu
     *
     * Used internally to determine menu position
     *
     * @since       0.1
     * return       string
    */
    public function getParentMenu() {
        return $this->parent_menu;
    }

    /**
     * Get the parent_menu
     *
     * Used internally to determine menu position
     *
     * @since       0.1
     * return       string
    */
    public function setParentMenu($parent_menu) {
        $this->parent_menu = $parent_menu;
    }

    /**
     * Get this page_title
     *
     * Get text for the h1 for this page
     *
     * @since       0.1
     * return       string
    */
    public function getPageTitle() {
        return $this->page_title;
    }

    /**
     * Get this menu_title
     *
     * Get text for menu for this page
     *
     * @since       0.1
     * return       string
    */
    public function getMenuTitle() {
        return $this->menu_title;
    }

}

