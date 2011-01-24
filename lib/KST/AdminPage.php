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


    /**
     * @since       0.1
     * @param       required string $namespace prefix to prepend everything with
     * @param       required string $menu_title actual text to display in menu
     * @param       required string $parent_menu Used to internally to track and determine final menu positions i.e. set correct parent_slugs
     * @param       required string $page_title Explicit title to use on page
    */
    public function __construct($menu_title, $menu_slug, $parent_menu, $page_title, $namespace) {
        $this->namespace = $namespace;
        $this->menu_title = $menu_title;
        $this->menu_slug = $menu_slug;
        $this->parent_menu = $parent_menu;
        $this->page_title = $page_title;
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

