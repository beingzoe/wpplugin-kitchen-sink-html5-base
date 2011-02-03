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
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @uses        KST_Kitchen
 * @uses        KST_Appliance_Options
*/


/**
 * Companion classes to encapsulate access to admin menu global arrays $menu and $submenu
 * Likely to be included in future WP core (version 3.2 ???)
 *
 * @since       0.1
 * @uses        is_admin() WP function
*/
if ( is_admin() ) {
    //require_once KST_DIR . '/help/features.php';
    require_once KST_DIR . '/help/wordpress.php';
    require_once KST_DIR . '/help/marketing.php';
}



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
    protected static $_help_files_plugins = array();
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

        // Every kitchen needs the basic settings
        $appliance_settings = array(
                    'friendly_name'       => 'KST Appliance: Core: Help',
                    'prefix'              => 'kst_help',
                    'developer'           => 'zoe somebody',
                    'developer_url'       => 'http://beingzoe.com/',
                );

        // Declare as core
        $this->_is_core_appliance = TRUE;
        // Common appliance
        parent::_init($kitchen, $appliance_settings);

    }


    /**
     * Register/Add help files to the system
     *
     * There can ONLY be ONE ENTRY per page/section/title by design
     * This enables overwriting/replacing (in the pluggable WP tradition) of entries
     *
     * Usually we defer to the theme for things like this but in this case plugins
     * rule the day since they modify 'everything' after the fact ;) For convenience
     * known pages are listed here, but see the wiki for a complete list of
     * default KST pages, sections, and titles that can be overwritten.
     *
     * Standard help pages and sections created by KST
     *      Theme Help          Index and vital info
     *      Features            Flashy feature stuff
     *      WordPress           Intro to WP with some stuff specific to your kitchen
     *      Marketing           About promotional features, seo, and such
     *      Developers          List of all KST theme/plugin developers with url's and otes for other developers about your custom stuff
     *
     * @since       0.1
     * @access      public
     * @param       required array $help_files
     * @return
    */
    public function add($help_files) {

        if (!is_admin())
            return false; // Nothing to do

        // Separate plugins into their own array so they can overwrite
        if ( !in_array($this->_type_of_kitchen, array('theme','core')) ) { // is a 'plugin'
            $temp_array = &self::$_help_files_plugins;
        } else { // is 'theme' or 'core'
            $temp_array = &self::$_help_files;
        }

        foreach ($help_files as $value) {
            $page_slug = self::formatMenuSlug($value['page']);
            $section_slug = str_replace(" ", "_", strtolower($value['section']) );
            $title_slug = str_replace(" ", "_", strtolower($value['title']) );

            $content_source = $value['content_source'];

            $temp_array[$value['page']][$value['section']][$value['title']]['page_url'] = 'admin.php?page=' . $page_slug;
            $temp_array[$value['page']][$value['section']][$value['title']]['section_slug'] = $section_slug;
            $temp_array[$value['page']][$value['section']][$value['title']]['title_slug'] = $title_slug;

            $temp_array[$value['page']][$value['section']][$value['title']]['content_source'] = $content_source;

            if ( is_string($content_source) && stripos( basename( $content_source ), '.php' ) ) { // include file
                $temp_array[$value['page']][$value['section']][$value['title']]['content_type'] = 'include';
            } else if ( is_array($content_source) || FALSE == strpos($content_source, " ") ) { // if it LOOKS like a valid callback - Could also do is_callable but...?
                $temp_array[$value['page']][$value['section']][$value['title']]['content_type'] = 'callback';
            } else { // Echo as raw html - PHP not allowed
                $temp_array[$value['page']][$value['section']][$value['title']]['content_type'] = 'string';
            }
        }

        // Add the hook to organize all the submitted pages before we send them to WP
        //if ( !has_action( '_admin_menu', array('KST_Appliance_Help','create')) ) {
            add_action('_admin_menu', array('KST_Appliance_Help', 'create'), 1999); // important for sequencing - go after Options at 999
        //}

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

        // Plugins overwrite - unset from main for clean merge
        foreach (self::$_help_files_plugins as $page => $sections) {
            foreach ($sections as $section => $titles) {
                foreach ($titles as $title => $value) {
                    unset(self::$_help_files[$page][$section][$title]);
                }
            }
        }

        /*
        echo "<br />Core and Theme Help<br />";
        print_r(self::$_help_files);
        echo "<br /><br /><br />";

        echo "<br />Plugin Help<br />";
        print_r(self::$_help_files_plugins);
        echo "<br /><br /><br />";
        */

        // Merge back in plugins so they can modify entries
        self::$_help_files = array_merge_recursive(self::$_help_files, self::$_help_files_plugins); //+= self::$_help_files_plugins

        /*
        echo "<br />MERGED <br />";
        print_r(self::$_help_files_plugins);
        echo "<br /><br /><br />";
        */

        foreach (self::$_help_files as $page => $sections) {
            if ( !isset($did_help_section) ) { // Create the main help section first time through
                $help_page = array(
                        'parent_slug'           => 'kst_theme_help_section',
                        'parent_menu_title'     => 'Theme Help',
                        'menu_slug'             => 'kst_theme_help_section',
                        'menu_title'            => 'Index',
                        'page_title'            => 'Theme Help Table of Contents',
                        'capability'            => 'edit_posts',
                        'view_page_callback'    => array('KST_Appliance_Help','view'),
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
                    'view_page_callback'    => array('KST_Appliance_Help','view')
                );
            new ZUI_WpAdminPages($help_page); // We now have 'Theme Help' top level section
        }

        // And we'll move them around after we are done
        // If blog owner allows and we haven't already added this hook
        if ( $GLOBALS['kst_core']->options->get('do_allow_kst_to_move_admin_menus') && !has_action( '_admin_menu', 'KST_Appliance_Help::moveHelpMenu') ) {
            add_action('admin_menu', array('KST_Appliance_Help', 'moveHelpMenu'), 2000);
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
            $output .= <<<EOD
            <p>
                These help files describe any unique functionality of your theme, <br />
                some of the plugin/features, as well as tips and answers on basic <br />
                (and even some advanced) WordPress usage..<br />
                Below are some topics on how to get the most out of your site.
            </p>
            <p>
                <em>
                    This page is not intended to explain basic use of WordPress to manage your blog or website. <br />
                    For help with using WordPress in general visit the
                    <a href="http://codex.wordpress.org/">WordPress Codex</a> especially the section <a href="http://codex.wordpress.org/Getting_Started_with_WordPress#WordPress_for_Beginners">WordPress for Beginners</a>.
                </em>
            </p>
            <p>
                <em>If you need help editing the theme itself or questions on using it contact the <a href='admin.php?page=kst_developers'>developers</a></em>.
            </p>
EOD;
            $output .= self::makeToc();

            echo $output;

        } else { // Any other help page

            $toc = self::makeToc($current);

            echo $toc;

            foreach (self::$_help_files as $page => $sections) {
                if ( self::formatMenuSlug($page) != $current )
                    continue; // not the right page

                //natsort($sections); // Egalitarian

                foreach ($sections as $section => $titles) {
                    echo "<h2 id='{$path['section_slug']}'>{$section}</h2>";

                    //natcasesort($titles); // Egalitarian
                    //$titles = array_reverse($titles); // Why?

                    foreach ($titles as $title => $path) {
                        echo "<h3 id='{$path['title_slug']}'>{$title}</h3>";

                        if ( 'include' == $path['content_type'] ) { // include file - array_key_exists('path', $path)
                            include $path['content_source'];
                        } else if ( 'callback' == $path['content_type'] ) { // execute callback - array_key_exists('callback', $path)
                            $callback = $path['content_source'];
                            call_user_func($callback); // Output their callback;
                        } else if ( 'string' == $path['content_type'] ) {
                            echo $path['content_source'];
                        } // Else do nothing because something is unreachable?
                        echo "<div class='top'><br /><br /><a href='#wphead'>Top</a><br /><br /><br /></div>";
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
            //natsort($sections); // Egalitarian
            foreach ($sections as $section => $titles) {
                $title_output = ""; // new section so erase titles
                //natcasesort($titles); // Egalitarian
                //$titles = array_reverse($titles); // Why?
                foreach ($titles as $title => $path) {
                    $page_url = self::$_help_files[$page][$section][$title]['page_url'];
                    $section_slug = self::$_help_files[$page][$section][$title]['section_slug'];
                    $title_slug = self::$_help_files[$page][$section][$title]['title_slug'];

                    $title_output .= "<li><a href='{$page_url}#{$title_slug}'>{$title}</a></li>"; // Open and Close title li
                }  // End of the title loop
                $section_output .= "<li><a href='{$page_url}#{$section_slug}'>{$section}</a> <ol>"; // Open section li - Open title list
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

