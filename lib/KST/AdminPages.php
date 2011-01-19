<?php
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

/**
 * Class for adding menus/pages to WP admin
 * 
 * @package     KitchenSinkHTML5Base
 * @subpackage  KitchenSinkWidgetClasses
 * @version     0.1 
 * @since       0.1
 * @author      zoe somebody 
 * @link        http://beingzoe.com/
 * @author      Scragz 
 * @link        http://scragz.com/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 */
class KST_AdminPages {
       
    /**#@+
     * @since       0.1
     * @access      protected
    */
    protected $admin_pages; // Array containing the pages to add

    /**#@-*/
    
    /**
     * @since       0.1
    */
    public function __construct() { 
    
    }
    
    /**
     
     * 
     * @since       0.1
    */
   
    
    /**
     * Register new admin "options" page with KST
     * We will save them up and output them all at once.
     *
     * All new pages are added to WP Admin here
     * This is called after the WP global $menu is set
     * and we have already acted on it if necessary 
     * to prevent overwriting existing menus and 
     * all KST created menus go where they need to be.
     * 
     * @since       0.1
     * @access      public
     * @param       required array $options_array passed by reference; Contains options block types and their parameters
     * @param       required string $menu_title actual text to display in menu
     * @param       optional string $parent_menu 
     * @param       optional string $page_title Explicit title to use on page, defaults to "friendly_name menu_title"
    */
    public static function create_admin_pages() {
        
         // If you can't manage your options then you can't have any menus - speed things up a bit
        if (!current_user_can('manage_options'))
            return;
        
        // Logic to reorganize menus would go here
        
        //print_r(KST::getKSTAdminPages());
        
        // Tell WP we want these pages
        foreach ( KST::getKSTAdminPages() as $page ) {
            
            /* Are we adding a top level menu? */
            if ( 'top' == $page->parent_menu ) {
                add_admin_menu_separator(58); // WP_AdminMenu function
                add_menu_page( $page->page_title, $page->menu_title, 'manage_options', $page->menu_slug, 'KST_AdminPages::manage_page', '', 59); //, $icon_url, $position
                
            }
            
            //print_r($page);
            //echo "parent_slug = " . $page->parent_slug . " pagetitle = " . $page->page_title . " menutitle = " . $page->menu_title . " function = " . 'manage_options' . " menu_slug = " . $page->menu_slug . "<br />";
            
            /* Always add a submenu (if parent the menu_title is used for both parent and submenu per WP best practice) */
            add_submenu_page($page->parent_slug, $page->page_title, $page->menu_title, 'manage_options', $page->menu_slug, array($page, 'manage_page') );//'make_menu_shit'
        }
        
    }
    
    /**
     * Register the options with WP
     * 
     * @since       0.1
     * @uses        KST_AdminPage_OptionsPages::_formatInNamespace()
     * @uses        KST_AdminPage_OptionsPages::options_array 
     * @uses        current_user_can() WP function
     * @uses        wp_die() WP function
     * 
     * NOTE: Creates option with namespace prepended
     *       "option_1" is saved to the db as "namespace_option_1"
    */
    public function manage_page() {
        
        // This is an options page and we need to think about saving/resetting
        if ( isset( $_REQUEST['action'] ) ) { // If we have an action to take
            $kst_option_save_action = $_REQUEST['action']; // only initialized here to prevent warnings
            
            if ( 'top' == $this->parent_menu || 'custom' == $this->parent_menu ) { // WP uses admin.php for custom top level and children so ignore parent_slug and hope they change that
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
                            $option_name = $this->_formatInNamespace( $value['id'] ); // Name of option to insert
                            $option_value = $_REQUEST[ $option_name ]; // Value of option being inserted
                            $result = update_option( $option_name , $option_value ); // oh-oh-oh it's magic
                        }
                    } 
                    header("Location: " . $base_page . $this->menu_slug . "&updated=true");  
                    exit;  
                } else if( 'reset' == $kst_option_save_action ) {  
                    foreach ($this->options_array as $value) {  
                        delete_option( $this->_formatInNamespace( $value['id'] ) ); // bye-bye
                    }  
                    header("Location: " . $base_page . $this->menu_slug . "&reset=true");  
                    exit;  
                } // End ACTIONS
            } // END if correct page 
        } // END if action
        
        echo $this->_generate_page(); 
    }
    
    /**
     * Get the menu slug for this page
     * 
     * Protects us from page name changes and deal with top level menus later
     * 
     * @since       0.1
     * @uses        KST_Options::get_parent_menu_menu_slug()
     * @uses        KST_Options::parent_menu
     * @return      string
     * @link        http://codex.wordpress.org/Adding_Administration_Menus
    */
    protected function _getParentSlug() {
        switch ( $this->parent_menu ) {
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
            case 'top':
                return $this->menu_slug;
            case 'custom':
                return $this->get_parent_menu_menu_slug();
            default:
                exit("<h2>Where should we put this fancy menu you are making?</h2><p>We can't find the parent_menu you specified (" . $this->parent_menu . ").</p><p>Do one of the following:</p><ul><li>Pass a known WP menu name (e.g. 'appearance', 'settings')</li><li>Pass 'top' (i.e. a new top level menu)</li><li>Or pass the entire object of a custom parent menu you already created</li></ul><p>If you aren't in the middle of setting up your custom admin menus using the 'Kitchen Sink KST_Options class' then something is terribly wrong with the world.</p>");
        }
        
    } // END get_parent_slug()
    
    /**
     * Get the parent menu's menu slug from passed parent menu object (for submenus)
     * 
     * Test the object by getting the parent's menu slug
     * If it fails die and help them out.
     * 
     * @since       0.1
     * @uses        KST_Options::parent_menu_object
     * @uses        KST_Options::menu_slug
     * return       string
    */
    public function get_parent_menu_slug() {
        if ( is_object($this->parent_menu_object) ) {
            $slug = $this->parent_menu_object->menu_slug;
            if ( empty( $slug ) ) // We can't find what we need in the object you passed
                exit("<h2>Woah, wrong object buddy!</h2><p>I don't recognize that object. Make sure you are passing <code><strong>\$my_parent_menu_object</strong></code> <em>(not in quotes, with the '$', y'know THE object ;)</em> .</p><p>If you aren't in the middle of setting up your custom admin menus using the 'Kitchen Sink KST_Options class' then something is terribly wrong with the world.</p>");
            return $slug;
        } else {
            exit("<h2>Something is terribly wrong</h2><p>Something thinks in Kitchen Sink class KST_Options needs something from it's parent menu object. But that object doesn't exit.</p>");
        }
    }
    
    /**
     * 
     * @since       0.1
    */
    public function get_menu_slug() {
        return $this->menu_slug;
    }
    
    
}

