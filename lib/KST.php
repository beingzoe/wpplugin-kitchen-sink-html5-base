<?php
/**
 * KST_Appliance_Options
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
 * @subpackage  Core
 * @version     0.1
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink/
 * @todo        replace WP_PLUGIN_DIR and any of these constants per Codex?
*/
class KST {

     /**#@+
     *
     * @since       0.1
     * @access      protected
     * @var         boolean
     * @see         KST::pluginsAreLoaded();
     * @see         KST_Kitchen_Theme::__construct()
     * @see         KST_Kitchen_plugin::__construct()
    */
    protected static $_plugins_are_loaded = false; // Set _plugins_are_loaded to false until WP tells us that the plugins are loaded - Hook set in init.php
    protected static $_is_core_only = TRUE; // Set _core_only to TRUE until a plugin or theme loads - Used to minimize impact of KST when running without a KST dependent kitchen
    /**#@-*/


    /**#@+
     * Various central urls mostly Help/support
     * Stored centrally in KST in case it needs to change because of Kitchen settings
     *
     * @since       0.1
     * @access      protected
     * @var         string
     * @see         KST::getHelpIndex()
    */
    protected static $_kst_theme_options_parent_slug = FALSE; // Central access to help
    protected static $_help_index = 'admin.php?page=kst_theme_help_section'; // Central access to help
    protected static $_help_developers = 'admin.php?page=kst_theme_help_developers'; // central access to developers page
    /**#@-*/


    /**
     * PUBLIC STATIC HELPER METHODS
    */

    /**
     * Get $_is_core_only
     *
     * @since       0.1
     * @access      public
     * @uses        KST::$_is_core_only
    */
    public static function isCoreOnly() {
        return self::$_is_core_only;
    }


    /**
     * Set $_is_core_only
     *
     * @since       0.1
     * @access      public
     * @uses        KST::$_is_core_only
    */
    public static function setIsCoreOnly($value) {
        self::$_is_core_only = $value;
    }


    /**
     * Get the current KST 'Theme Options' Parent Slug
     *
     * @since       0.1
     * @access      public
     * @uses        KST::$_kst_theme_options_parent_slug
    */
    public static function getKstThemeOptionsParentSlug() {
        return self::$_kst_theme_options_parent_slug;
    }


    /**
     * Set/store current KST 'Theme Options' Parent Slug
     * for use by all kitchens/appliances
     * Should only be accessed by KST core (please ;)
     *
     * @since       0.1
     * @access      public
     * @uses        KST::$_kst_theme_options_parent_slug
    */
    public static function setKstThemeOptionsParentSlug($value) {
        self::$_kst_theme_options_parent_slug = $value;
    }


    /**
     * Get the current help file index
     *
     * @since       0.1
     * @access      public
     * @uses        KST::$_help_index
    */
    public static function getDeveloperIndex() {
        return self::$_help_developers;
    }


    /**
     * Get the current help file index
     *
     * @since       0.1
     * @access      public
     * @uses        KST::$_help_index
    */
    public static function getHelpIndex() {
        return self::$_help_index;
    }


    /**
     * Set the current help file index
     *
     * @since       0.1
     * @access      public
     * @uses        KST::$_help_index
     * @param       required string $path_to_file
    */
    public static function setHelpIndex($path_to_file) {
        self::$_help_index = $path_to_file;
    }


    /**
     * Set is_plugins_loaded via WP hook callback
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
     * This might could be done better (I lifted this from somewhere??)
     *
     * @since       0.1
     * @see         http://wordpress.org/support/topic/how-to-change-plugins-load-order
     * @uses        plugin_basename() WP Function
     * @uses        get_option() WP Function
     * @uses        update_option() WP Function
     * @uses        WP_PLUGIN_DIR WP constant
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


    /**
     * Hide KST from update checks from WordPress.org repository
     *
     * Check to see if a developer wants to stop update checks
     * Enables the use of KST without developers or KST worrying
     * if it is going to break things in future development.
     *
     * Wherever possible we will remain backwards compatible
     * If we plan to get rid of functionality it will be deprecated.
     *
     * How the version number is tracked:
     * Version 1.2.3 = upgrade.update.patch
     *
     * Only a change in the upgrade number guarantees code needs updated to stay stable
     * i.e. Updating from Version 1.2.3 to 2.0 means you will need to update your code
     *
     * A change in the update number could require an update to your code to use the new functionality
     * i.e. Updating from Version 1.2.3 to 1.3.0 means significant new features have been added
     *
     * A change in the patch number should not require an update to your code
     * i.e. Updating from Version 1.2.3 to 1.2.4 means something was fixed or optimized
     *
     * Your initial settings array allows you to choose for your installation and implementation
     * of KST what, if any, updates should be checked for from the WP repository. This is controlled
     * using the 'limit_update_check' setting in your kitchen settings array.
     *
     * Possible values for 'limit_update_check':
     *      -none
     *      -patch (default)
     *      -update
     *      -upgrade
     *
     * @since       0.1
    */
    /*
    public static function limitUpdateCheck( $r = NULL, $url = NULL) {
        echo "limitUpdateCheck happened<br />";


        if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) ) {
            return $r; // Not a plugin update request. Bail immediately.
        }
        echo "It is a plugin update check!<br /><br />";
        return $r;
        $kst_update_limit = get_option('kst_update_limit', 'patch');
        if ( 'none' == $kst_update_limit ) {
            return $r;
        } else if ( 'patch' == $kst_update_limit ) {

        }

    }

    public static function checkUpdateCheck($value) {

        $kst_updates = get_option('_kst_site_transient_update_plugins');

        if (empty($kst_updates)) {

        }


        echo "<br />pre_set_site_transient_update_plugins value<pre>";
        print_r($value);
        echo "</pre><br />";

            // Check to see if KST has a potential update
            if ( isset($value->response['theme-check/theme-check.php']) ) {

        echo "We have a pre_set_... match...<br />";
                // Check to see
                if (current_version_upgrade)

                $value->response_deferred_patch['theme-check/theme-check.php'] = $value->response['theme-check/theme-check.php'];
                unset($value->response['theme-check/theme-check.php']);

        echo "<br />pre_set_site_transient_update_plugins updated value<pre>";
        print_r($value);
        echo "</pre><br />";

            }

            return $value;
        }
        */

}
