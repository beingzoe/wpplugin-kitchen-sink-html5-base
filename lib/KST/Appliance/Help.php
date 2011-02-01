<?php
/**
 * Class for creating and inserting help into pages created by KST_AdminPage
 * Parent class included on KST load so KST can have core options without theme or plugin
 *
 * @package     KitchenSinkHTML5Base
 * @subpackage  Core
 * @version     0.1
 * @since       0.1
 * @author      zoe somebody
 * @link        http://beingzoe.com/
 * @author      scragz
 * @link        http://scragz.com/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @uses        KST_Kitchen
 * @uses        KST_Appliance_Options
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
    require_once KST_DIR_VENDOR . '/WP/AdminMenu.php';
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
require_once KST_DIR_VENDOR . '/ZUI/WpAdminPages.php';


/**
 * Class methods for creating and accessing help files for site/blog owner
 * Creates WP admin menu/pages (not 'options' pages ;)
 *
 * @since       0.1
 * @uses        ZUI_WpAdminPages
 * @uses        AdminMenu.php
*/
class KST_Appliance_Help extends KST_Appliance {

    /**#@+
     * @since       0.1
     * @access      protected
     * @var         array
    */
    protected static $_help_files = array();
    /**#@-*/


    /**#@+
     * @since       0.1
     * @access      protected
     * @var         array
    */
    protected static $_custom_start_index = 323;
    /**#@-*/



    /**
     * @since       0.1
    */
    public function __construct(&$kitchen) {

        if ( !is_admin() )
            return FALSE; // Nothing to do

        // Common to all pages for this kitchen
        $this->_kitchen = $kitchen;
        $this->_type_of_kitchen = $this->_kitchen->getTypeOfKitchen();

        // Every kitchen needs the basic settings
        $kst_help_settings = array(
                    /* REQUIRED */
                    'friendly_name'       => 'KST Appliance: Core: Help',                 // Required; friendly name used by all widgets, libraries, and classes; can be different than the registered theme name
                    'prefix'              => 'kst_help',                       // Required; Prefix for namespacing libraries, classes, widgets
                    'developer'           => 'zoe somebody',                           // Required; friendly name of current developer; only used for admin display;
                    'developer_url'       => 'http://beingzoe.com/',            // Required; full URI to developer website;
                );

        // Initialize as kitchen and create options page
        $this->_appliance = new KST_Kitchen_Plugin($kst_help_settings);

    }


    /**
     * Register/Add help files to the system
     *
     * Standard help pages and sections created by KST
     *      Theme Help          index and vital info
     *      Features            flashy feature stuff
     *      WordPress           intro to WP with some stuff specific to your kitchen
     *          Blog Posts
     *          Site Pages (cms)
     *          Media
     *          Plugins
     *          Settings
     *      Dev Notes     Notes for other developers about your custom stuff
     *
     *      Theme options*
     *
     * @since       0.1
     * @access      public
     * @param       required array $help_files
     * @return
    */
    public function add($help_files) {
        if (!is_admin())
            return false;

        //echo "namespace=" . $this->getDeveloper_url() . "<br />";
        /*
        echo "Before merge we have (this->_help_files):<br />";
        print_r($this->_help_files);
        echo "<br /><br /><br />";

        echo "Being merged with (passed help_files):<br />";
        print_r($help_files);
        echo "<br /><br /><br />";
*/

        foreach ($help_files as $value) {
            self::$_help_files[$value['page']][$value['section']][$value['title']]['path'] = $value['path'];
            $page_url = self::formatMenuSlug($value['page']);
            $section_url = str_replace(" ", "_", strtolower($value['section']) );
            $title_url = str_replace(" ", "_", strtolower($value['title']) );
            self::$_help_files[$value['page']][$value['section']][$value['title']]['page_url'] = 'admin.php?page=' . $page_url;
            self::$_help_files[$value['page']][$value['section']][$value['title']]['title_url'] = $section_url;
            self::$_help_files[$value['page']][$value['section']][$value['title']]['title_url'] = $title_url;
        }



        //Merge
        //self::$_help_files = array_merge(self::$_help_files, $help_files);

        /*
        echo "After merge we have:<br />";
        print_r($this->_help_files);
        echo "<br /><br /><br />";
*/

        // Add the hook to organize all the submitted pages before we send them to WP
        if ( !has_action( '_admin_menu', 'KST_Appliance_Help::create') ) {
            add_action('_admin_menu', 'KST_Appliance_Help::create', 1999); // important for sequencing - go after Options at 999
        }

    }


    /**
     * Register/Add help files to the system
     *
     * @since       0.1
     * @access      public
     * @global      object $GLOBALS['$kst_core']
     * @uses        KST_Appliance_Help::formatMenuSlug()
     * @uses        KST_Appliance_Help::$_help_files
     * @uses        KST_Appliance_Options::get()
     * @uses        has_action() WP function
     * @uses        add_action() WP function
    */
    public static function create() {

        foreach (self::$_help_files as $page => $sections) {
            if ( !isset($did_help_section) ) { // Create the main help section first time through
                $help_page = array(
                        'parent_slug'           => 'kst_theme_help_section',
                        'parent_menu_title'     => 'Theme Help',
                        'menu_slug'             => 'kst_theme_help_section',
                        'menu_title'            => 'Index',
                        'page_title'            => 'Theme Help Table of Contents',
                        'capability'            => 'edit_posts',
                        'view_page_callback'    => 'KST_Appliance_Help::view',
                        'icon_url'              => NULL,
                        'position'              => self::$_custom_start_index
                    );
                new ZUI_WpAdminPages($help_page); // We now have 'Theme Help' top level section
                $did_help_section = TRUE;
            }
            $help_page = array(
                    'parent_slug'           => 'kst_theme_help_section',
                    'page_title'            => "{$page} help and info",
                    'menu_title'            => "{$page}",
                    'menu_slug'             => self::formatMenuSlug($page),
                    'capability'            => 'edit_posts',
                    'view_page_callback'    => 'KST_Appliance_Help::view'
                );
            new ZUI_WpAdminPages($help_page); // We now have 'Theme Help' top level section
        }

        // And we'll move them around after we are done
        // If blog owner allows and we haven't already added this hook
        if ( $GLOBALS['kst_core']->options->get('do_allow_kst_to_move_admin_menus') && !has_action( '_admin_menu', 'KST_Appliance_Help::moveHelpMenu') ) {
            add_action('admin_menu', 'KST_Appliance_Help::moveHelpMenu', 2000);
        }

    }


    /**
     * Output the help file
     *
     * @since       0.1
     * @uses        KST_Appliance_Help::formatMenuSlug()
     * @uses        KST_Appliance_Help::$_help_files
     * @uses        wp_die() WP function
     * @uses        $_REQUEST['page'] required to determine page output - menu_slug
    */
    public static function view() {
        if ( !isset($_REQUEST['page']) )
            wp_die("We cannot find the help file you requested.");

        $current = $_REQUEST['page'];

        if ( 'kst_theme_help_section' == $current ) {

            $output = "";
            $output .= "<p>These help files contain explanation and overview for the various aspects of your theme, some of the plugin/features, as well as tips and answers on basic (and even some advanced) WordPress usage.</p>";
            $output .= self::makeToc();

            echo $output;

        } else { // Any other help page

            echo self::makeToc($current);

            foreach (self::$_help_files as $page => $sections) {
                if ( self::formatMenuSlug($page) != $current )
                    continue; // not the right page

                foreach ($sections as $section => $titles) {
                    echo "<h2 id='{$path['section_url']}'>{$section}</h2>";
                    foreach ($titles as $title => $path) {
                        echo "<h3 id='{$path['title_url']}'>{$title}</h3>";
                        include $path['path'];
                    } // End of the title loop
                } // End of the section loop
            } // End of the page loop

        } // End IF TOC or Help Page

    }


    /**
     * Move the Help menu above appearance
     *
     * @since       0.1
    */
    public static function moveHelpMenu() {
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
    }


    /**
     * DRY: Loop and make the Table of Contents
     *
     * Will do all pages (index) or any single page
     *
     * @since       0.1
     * @uses        KST_Appliance_Help::::$_help_files
     * @param       optional string $menu_slug
     * @return      string formatted ol list (WP?)
     * @todo        Style this ourselves!
    */
    protected static function makeToc($menu_slug = NULL) {
        $output = "";
        foreach (self::$_help_files as $page => $sections) {
            if ( NULL !== $menu_slug && $menu_slug != self::formatMenuSlug($page) )
                    continue; // not the right page
            $page_output = ""; // new page start over
            $section_output = "<ol>"; // open section list
            foreach ($sections as $section => $titles) {
                $title_output = ""; // new section so erase titles
                foreach ($titles as $title => $path) {
                    $page_url = self::$_help_files[$page][$section][$title]['page_url'];
                    $section_url = self::$_help_files[$page][$section][$title]['section_url'];
                    $title_url = self::$_help_files[$page][$section][$title]['title_url'];

                    $title_output .= "<li><a href='{$page_url}#{$title_url}'>{$title}</a></li>"; // Open and Close title li
                }  // End of the title loop
                $section_output .= "<li><a href='{$page_url}#{$section_url}'>{$section}</a> <ol>"; // Open section li - Open title list
                $section_output .= $title_output;
                $section_output .= "</ol></li>"; // Close title list - Close section li
            } // End of the section loop

            if ( NULL == $menu_slug )
                $page_output .= "<h3><a href='{$page_url}'>$page</a></h3>";

            $output .= $page_output . $section_output;
            $output .= "</ol>"; // close section list
        } // End of the page loop
        return $output;
    }

    /**
     * DRY: format the menu slug
     *
     * @since       0.1
     * @access      protected
     * @param       required string $page_name
     * @return      string lowercased_underscored_page_name
    */
    protected static function formatMenuSlug($page_name) {
        return str_replace(" ", "_", strtolower("kst_help_" . $page_name) );
    }

}

