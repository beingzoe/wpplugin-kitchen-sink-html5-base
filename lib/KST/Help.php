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
 * @author      Scragz
 * @link        http://scragz.com/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @uses        KST_AdminPage
*/
class KST_Help {

    /**
     * @since       0.1
     * @access      protected
    */
    protected $_help_files;


    /**
     * @since       0.1
    */
    public function __construct() {
        $this->_help_files = array();
        add_action('admin_menu', 'KST_AdminPage_Help::addMenus',1000); // hook to create menus/pages in admin AFTER the option page menus are created
    }


    /**
     * Register/Add help files to the system
     *
     * @since       0.1
     * @access      public
     * @param       required array $help_files
     * @return
    */
    public function add($help_files) {
        if (!is_admin())
            return false;
        /*
        //echo "namespace=" . $this->getDeveloper_url() . "<br />";
        echo "Before merge we have (this->_help_files):<br />";
        print_r($this->_help_files);
        echo "<br /><br /><br />";

        echo "Being merged with (passed help_files):<br />";
        print_r($help_files);
        echo "<br /><br /><br />";
        */

        //Merge
        $this->_help_files = array_merge($this->_help_files, $help_files);
        /*
        echo "After merge we have:<br />";
        print_r($this->_help_files);
        echo "<br /><br /><br />";
        */
    }


    /**
     * Register/Add help files to the system
     *
     * @since       0.1
     * @access      public
     * @param       required array $help_files
     * @return
    */
    public static function addMenus($help_files) {
        if (!is_admin())
            return false;

        //echo "We would be adding help menus";

        // loop the array
        //$this->_help_files
        // send the stuff we need to create
        $theme_help_menu_section = array(
                'page_title' => 'Theme Help',
                'menu_title' => 'Theme Help',
                'menu_slug' => 'kst_theme_help',
                'parent_menu' => 'top'
            );

/*
        $defaults = array(
            'menu_title' => '',
            'parent_menu' => 'kst',
            'page_title' => '');
        $options += $defaults;
        $options['namespace'] = $this->namespace;
        $options['type_of_kitchen'] = $this->type_of_kitchen;
        $options['friendly_name'] = $this->friendly_name;
        // Create generic title if none given
        $page_title = ( empty($options['page_title']) ) ? $options['page_title']
                                      : $this->getFriendlyName() . " " . $options['menu_title'];
        // Create a namespaced menu slug from their menu title
        $options['menu_slug'] = $this->prefixWithNamespace( str_replace( " ", "_", $options['menu_title'] ) );

        return KST_AdminPage::addOptionPage($options_array, $options);


        */


        //KST_AdminPage::create($theme_help_menu_section);

        //$testthis = add_menu_page( $page->page_title, $page->menu_title, 'manage_options', $page->menu_slug, array($page,'manage'), ''); //, $icon_url, $position
        //add_submenu_page($updated_parent_slug, $page->page_title, $page->menu_title, 'manage_options', $page->menu_slug, array($page,'manage') );//'make_menu_shit'
    }
}

