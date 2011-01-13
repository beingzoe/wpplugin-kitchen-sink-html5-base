<?php
/**
 * KST_Options
 * Kitchen Sink Class: Options
 * Methods to rapidly create and use WordPress options (and just admin content pages)
 * Creates menu item and builds options/content page using options array you create
 * 
 * @author		zoe somebody
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     WordPress
 * @subpackage  KitchenSinkClasses
 * @version     0.4
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @todo        finish adding size option to control input sizes (do it better)
 * 
 * http://codex.wordpress.org/Adding_Administration_Menus
 * http://codex.wordpress.org/Creating_Options_Pages
 * Using code and concepts from the Biblioteca framework theme (http://wpshout.com/)
 */

class KST_Options {
    
    /**#@+
     * @access private
     * @var string
     */
    private $prefix;                    // id/prefix to namespace the options; For consistency I use the same theme_id or plugin_id on all option pages in said theme/plugin; "_" appended by default e.g. 'prefix' = 'prefix_'
    private $doodad_name;               // Friendly theme/plugin name
    private $settings_options_group;    // String name of options group name used by WordPress
    private $options_array_name;        // NAME of the Array containing the options to use for page e.g. 'my_array' NOT '$my_array';
    private $parent_menu;               
    private $parent_menu_slug;          // If submenu of custom top level menu; Otherwise empty;
    private $parent_slug;               // The actual slug WP needs to know to determine new menu item location;
    private $menu_title;                // Title of your pages menu as it appears in the sidebar
    private $menu_slug;                 // WP page name for this menu/page; appended as query string to parent_slug as virtual page
    private $page_title;
    
    /**#@+
     * @access private
     * @var array
     */
    private $extant_options;            // Array of options that exist IF they were checked with $this->option_exists();
    private $options_array;             // Your array used globally by reference (via option_array_name; Options/content for options page;
    private $option_type_of_section;    // Array of form output blocks "of type section" (not a form element); Used for conditionally formatting output;
    
    /**#@+
     * @access private
     * @var object
     */
    private $parent_menu_object;        // The whole darn parent menu object if this is a submenu of custom top level menu
    
    /**
     * Initialize the class
     * 
     * @since       0.1
     * @param       string prefix                   // id/prefix to namespace the options; For consistency I use the same theme_id or plugin_id on all option pages in said theme/plugin; "_" appended by default e.g. 'prefix' = 'prefix_'
     * @param       string doodad_name              // Friendly theme/plugin name
     * @param       string options_array_name       // NAME of the Array containing the options to use for page e.g. 'my_array' NOT '$my_array';
     * @param       array options_array             // Your array used globally by reference (via option_array_name; Options/content for options page;
     * @param       object|string parent_menu       //'top' if top-level OR 'friendly name' of menu to put your new menu under OR object of custom top level parent; Any WP built-in sidebar title e.g. posts, appearance, Settings; Case Insenstive; Used to find correct slug to add menu;
     * @param       string menu_title               // Title of your pages menu as it appears in the sidebar
     * @param       string optional page_title      // A custom header title to show at the top of the page; Defaults to ...
     * @uses        KST_Options::get_parent_slug()  
     * @uses        KST_Options::set_page_title()
     * @uses        is_admin() WP function
     * @uses        add_action() WP function
     */
    public function __construct($prefix, $doodad_name, $options_array_name, $parent_menu, $menu_title, $page_title = FALSE) {
        
        $this->prefix = $prefix . "_"; // Cause it looks better in the address bar
        $this->doodad_name = $doodad_name;
        $this->settings_options_group = $this->prefix . "_options_group";
        $this->options_array_name = $options_array_name;
        
        /* Check the $parent_menu parameter to determine parent/sub menu relationship */
        if ( is_object( $parent_menu ) ) { // You passed an object so this is a submenu
            $this->parent_menu = 'custom'; // Parent menu must be a known string value
            $this->parent_menu_object = $parent_menu; // So put the object in it's own variable
            /* Get the parent_slug so we can use it */            
            //$this->parent_slug = $this->get_parent_menu_slug(); // Public accessor to private variable from parent menu
            //if ( empty( $this->get_parent_menu_slug() ) ) // We can't find what we need in the object you passed
                //exit("<h2>Woah, wrong object buddy!</h2><p>I don't recognize that object. Make sure you are passing <code><strong>\$my_parent_menu_object</strong></code> <em>(not in quotes, with the '$', y'know THE object ;)</em> .</p><p>If you aren't in the middle of setting up your custom admin menus using the 'Kitchen Sink KST_Options class' then something is terribly wrong with the world.</p>");
        } else {
            $this->parent_menu = strtolower( $parent_menu ); // 'top' or friendly WP menu name
        }
        
        $this->menu_title = $menu_title;
        $this->menu_slug = $this->prefix . str_replace( " ", "_", $this->menu_title );
        $this->parent_slug = $this->get_parent_slug();
        
        $this->page_title = $this->set_page_title( $page_title );
        $this->extant_options = array();
        $this->option_type_of_section = array('section', 'subsection');
        
        /* Initialize the process of creating the options menu and page */
        if ( is_admin() ) { // Perhaps redundant insurance that WP won't do stuff unneccesarily
            add_action('admin_menu', array(&$this, 'add_page')); // Tell WP we want a page and menu for it
        }

    }
    
    /**
     * format option name as saved with "id" prefix
     * 
     * @since       0.1
     * @param       required string $option    unprefixed option name
     * @uses        KST_Options::prefix
     * @return      string
     */
    public function format_option_id( $option ) {
        return $this->prefix . "_" . $option;
    }
    
    /**
     * Get KST WP theme option
     * 
     * Replaces native get_option for convenience
     * So instead of get_option("prefix_admin_email");
     * you can $kst_options->get_option("admin_email");
     *
     * get_option("meta_keywords_home", $default)
     * 
     * @since 0.2
     * @param       required string option 
     * @param       optional string default ANY  optional, defaults to NULL
     * @uses        KST_Options::format_option_id
     * @uses        get_option() WP function
     * @return      string
     */
    public function get_option($option, $default = NULL) {
        
        $_option = $this->format_option_id( $option );
        $_option_value = get_option( $_option, $default); // Ask WP
        
        return $_option_value;
    }
    
    
    /**
     * Test for existence of KST theme option REGARDLESS OF TRUENESS of option value
     *
     * Returns true if option exists REGARDLESS OF TRUENESS of option value
     * WP get_option returns false if option does not exist 
     * AND if it does and is false
     * 
     * Typically only necessary when testing existence to set defaults on first use for radio buttons etc...
     *
     * N.B.: First request is an entire query and obviously a speed hit so use wisely
     *       Multiple tests for the same option are saved and won't affect load time as much
     * 
     * @since       0.2
     * @global      $wpdb
     * @param       required string $option 
     * @uses        KST_Options::format_option_id()
     * @uses        KST_Options::extant_options
     * @return      boolean
     */ 
    public function option_exists( $option ) {
        
        global $wpdb; // This IS WordPress ;)
        
        $_option = $this->format_option_id( $option );
        $skip_it = FALSE; // Flag used to help skip the query if we've checked it before
        
        /* Check to see if the current key exists */
        if ( !array_key_exists( $_option, $this->extant_options ) ) { // Don't know yet, so make a query to test for actual row
            $row = $wpdb->get_row( "SELECT option_value FROM $wpdb->options WHERE option_name = '{$_option}' LIMIT 1", OBJECT );
        } else { // The option name exists in our "extant_options" array so just skip it
            $skip_it = true;
        }
        
        /* Return the answer */
        if ( $skip_it || is_object( $row ) ) { // The option exists regardless of trueness of value
            $this->extant_options[$option]['exists'] = TRUE; // Save in array if exists to minimize repeat queries
            return true;
        } else { // The option does not exist at all
            return false;
        }
        
    }
    
    
    /**
     * Build the options menu
     * 
     * Loops the $this->options_array array
     * Outputs (echo) form data for editing after it has been parsed
     *
     * Optionally you may also filter the output before it goes to the screen
     * 
     * @since       0.1
     * @uses        KST_Options::doodad_name
     * @uses        KST_Options::format_option_id()
     * @uses        KST_Options::get_option()
     * @uses        KST_Options::option_exists
     * @uses        KST_Options::print_table_row_open()
     * @uses        KST_Options::print_form_labels()
     * @uses        KST_Options::print_form_descriptions()
     * @uses        KST_Options::print_table_row_close()
     * @uses        KST_Options::might_close_section
     * @uses        KST_Options::might_close_table
     * @uses        KST_Options::option_type_of_section
     * @uses        KST_Options::settings_options_group
     * @uses        current_user_can() WP function
     * @uses        wp_die() WP function
     * @uses        screen_icon() WP function ???
     * @uses        settings_fields() WP function (outputs required hidden fields based on register_settings())
     * @uses        __() WP function localize if language exists
     * @uses        _e() WP function 
     * @uses        selected() WP function
     * @uses        checked() WP function
     * @uses        wp_parse_args() WP function
     * @uses        wp_dropdown_categories() WP function
     * @uses        wp_dropdown_pages() WP function
     * @uses        apply_filters() WP function
     */
    public function kst_options() {
        
        $output = "";
        
        if (!current_user_can('manage_options'))  {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }
        
        /* OUTPUT the actual options page */
        $output .= "<div class='wrap kst_options'>"; //Standard WP Admin content class plus our class to style with
        /*
        if ( function_exists('screen_icon') ) 
            screen_icon('options-general');
            */
        $output .= "<h2>" . $this->page_title . "</h2>";
        
        /* Give response on action */
        if ( isset($_REQUEST['updated']) ) 
            $output .= "<div id='message' class='updated fade'><p><strong>" . $this->doodad_name . " settings saved.</strong></p></div>";
        if ( isset($_REQUEST['reset']) ) 
            $output .= "<div id='message' class='updated fade'><p><strong>" . $this->doodad_name . " settings reset.</strong></p></div>";
        
        $output .= "<form method='post' id='poststuff' class='metabox-holder'>";
            $output .= '<div class="meta-box-sortables" id="normal-sortables">'; // Attempting to utilize as much WP style/formatting as possible
            
            /* Set up variables for block output loop */
            $can_save               = FALSE; // Flag to only create form if we have a form element to save
            $kst_do_end_section     = FALSE; // Flag to clean up behind the previous section
            $kst_do_close_section   = FALSE; // Flag sections to be "closed" on load
            $block_previous_type      = FALSE; // Keep track of who we are cleaning up after
            
            /* loop and output options to build page/form */
            foreach ($this->options_array as $block) {
                
                $this_option        = $this->format_option_id( $block['id'] ); // Prefixed option name as stored in database
                $this_value         = $this->get_option( $block['id'] ); // Value of prefixed option name in database
                $this_exists        = $this->option_exists( $block['id'] ); // Boolean existence of the current option
                $this_attr_name_id  = " name='{$this_option}' id='{$this_option}'"; // Used on every element
                
                /* Start new table if previous was a 'section' */
                if ( !in_array( $block['type'], $this->option_type_of_section ) && $block_previous_type && in_array( $block_previous_type, $this->option_type_of_section ) )
                    $output .= '<dl class="form-table">';
                
                switch ( $block['type'] ) {
                    
                    case 'text':
                        
                        $can_save = TRUE;
                        
                        /* Get and format the text value="" */
                        if ( $this_exists ) 
                            $value = ' value="' . $this_value . '"'; 
                        else if ( !$this_exists ) 
                            $value = ' value="' . $block['default'] . '"';
                        else
                            $value = ' value=""';
                        
                        /* Get and format the text size="" */
                        $size = ( empty( $block['size'] ) ) ? " size='60'" 
                                                            : ' size="' . $block['size'] . '"'; // Use yours
                        
                        /* Output the block */
                        $output .= $this->print_table_row_open( $this->print_form_labels( $block['id'], $block['name'] ) );
                            $output .= '<input type="text"' . $value . $this_attr_name_id . $size . ' />'; 
                            $output .= $this->print_form_descriptions( $block['desc'] );
                        $output .= $this->print_table_row_close();
                        
                        /* Cleanup */
                        $block_previous_type = 'text';
                        
                    break; // END text input block
                    
                    case 'textarea':
                        
                        $can_save = TRUE;
                        
                        /* Get and format the textarea cols="" */
                        if ( isset( $block['cols'] ) && !empty( $block['cols'] ) )
                            $textarea_cols = ' cols="' . $block['cols'] . '"';
                        else 
                            $textarea_cols = ' cols="50"'; 
                            
                        /* Get and format the textarea rows="" */
                        if ( isset( $block['rows'] ) && !empty( $block['rows'] ) )
                            $textarea_rows = ' cols="' . $block['rows'] . '"';
                        else 
                            $textarea_rows = ' cols="5"'; 
                        
                        /* Output the block */
                        $output .= $this->print_table_row_open( $this->print_form_labels( $block['id'], $block['name'] ) );
                            $output .= '<textarea' . $this_attr_name_id . $textarea_cols . $textarea_rows . '>';
                            if( $this_value != "") {
                                $output .= __( stripslashes( $this_value ) );
                            }else{
                                $output .= __( $block['default'] );
                            }
                            $output .= '</textarea>';
                            $output .= $this->print_form_descriptions( $block['desc'] );
                        $output .= $this->print_table_row_close();
                        
                        /* Cleanup */
                        $block_previous_type = 'textarea';
                        
                    break;
                    
                    case 'select':
                        
                        $can_save = TRUE;
                        
                        /* Add style to clean up page and overrides WP style of height: 2em for multiple selects */
                        $style = ' style="min-width: 150px; height: auto;"'; // 
                        
                        /* is it a multiple select? */
                        $multi = "";
                        $size = "";
                        $do_as_array = "";
                        if ( isset($block['multi']) && $block['multi'] ) { // Yup, so now prep the element
                            $do_as_array = '[]';
                            $multi = ' multiple="multiple"';
                            $size = ' size="5"';
                            if ( isset($block['size']) && $block['size'] ) 
                                $size = ' size="' . $block['size'] . '"'; // Use yours instead of defaults
                        }
                        
                        /* Output the block */
                        $output .= $this->print_table_row_open( $this->print_form_labels( $block['id'], $block['name'] ) );
                            $output .= "<select name='" . $this_option . $do_as_array . "' id='" . $this_option . "'" . $multi . $size . $style . "'>";
                            /* Loop over the select options */
                            foreach ($block['options'] as $option) {
                                //echo $option;
                                $selected = ( ($block['default'] && !$this_exists && $option == $block['default']) || (is_array( $this_value ) && in_array($option, $this_value )) )
                                          ? selected( 1, 1, FALSE )
                                          : selected( $this_value, $option, FALSE );
                                $output .= "<option value='{$option}'{$selected}>{$option}</option>";
                            }
                            $output .= "</select>";
                            $output .= $this->print_form_descriptions( $block['desc'] );
                        $output .= $this->print_table_row_close();
                        
                        /* Cleanup */
                        $block_previous_type = 'select';
                        
                    break;
                    
                    case 'select_category':
                        
                        $can_save = TRUE;
                        
                        /* Set sensible defaults especially select NONE if we don't find a default later */
                        $defaults =  array(
                            "show_option_none"   => "None",
                            "selected"           => "-1",
                            "name"               => $this_option,
                            "orderby"            => "name",
                            "hierarchical"       => 1, 
                            'echo'               => 0
                        );
                        $args = wp_parse_args( $block['args'], $defaults );
                        
                        /* See if we have a default and merge it in for WP to check */
                        if ( $this_value ) {
                            $selected = array( "selected" => $this_value );
                            $args = wp_parse_args( $selected, $args );
                        }
                        
                        /* Output the block */
                        $output .= $this->print_table_row_open( $this->print_form_labels( $block['id'], $block['name'] ) );
                            $output .= wp_dropdown_categories( $args );
                            $output .= $this->print_form_descriptions( $block['desc'] );
                        $output .= $this->print_table_row_close();
                        
                        /* Cleanup */
                        $block_previous_type = 'select_category';
                        
                    break;
                    
                    case 'select_page':
                        
                        $can_save = TRUE;
                        
                        /* Set sensible defaults especially select NONE if we don't find a default later */
                        $defaults =  array(
                            "show_option_none"   => "None",
                            "selected"           => "-1",
                            "name"               => $this_option,
                            'echo'               => 0
                        );
                        $args = wp_parse_args( $block['args'], $defaults );
                        
                        /* See if we have a default and merge it in for WP to check */
                        if ( $this_value ) {
                            $selected = array( "selected" => $this_value );
                            $args = wp_parse_args( $selected, $args );
                        }
                        
                        /* Output the block */
                        $output .= $this->print_table_row_open( $this->print_form_labels( $block['id'], $block['name'] ) );
                            $output .=wp_dropdown_pages( $args );
                            $output .= $this->print_form_descriptions( $block['desc'] );
                        $output .= $this->print_table_row_close();
                        
                        /* Cleanup */
                        $block_previous_type = 'select_page';
                        
                    break;
                    
                    case 'radio':
                        
                        $can_save = TRUE;

                        /* Output the block */
                        $output .= $this->print_table_row_open( __($block['name']) );
                            /* Loop over the select options */
                            foreach ($block['options'] as $key=>$keyvalue) {
                                /* Find out if this input is checked */
                                 $checked = ( ( $block['default'] && $this->option_exists( $block['id'] ) === FALSE ) && $block['default'] == $keyvalue ) 
                                          ? checked( 1, 1, FALSE )
                                          : checked( $this_value, $keyvalue, FALSE );
                                /* Radio buttons are exceptional and don't use the shared $attr_name_id variable 
                                 * The 'id' must be unique but the 'name' the same for each. 
                                 * Similar issue in getting the value.
                                 * And labels go after the radio.
                                 */
                                $output .= ' <input  type="radio" name="' . $this_option . '" id="' . $this_option . $key . '" value="' . $keyvalue . '"' . $checked . '/> ';
                                $output .= $this->print_form_labels( $block['id'] . $key, $keyvalue );
                            }
                            $output .= $this->print_form_descriptions( $block['desc'] );
                        $output .= $this->print_table_row_close();
                        
                        /* Cleanup */
                        $block_previous_type = 'radio';
                        
                    break;
                    
                    case 'checkbox':
                        
                        $can_save = TRUE;
                        
                        /* Find out if the box is checked */  
                        $checked = ( $block['default'] && $this->option_exists( $block['id'] ) === FALSE ) 
                                          ? checked( 1, 1, FALSE )
                                          : checked( $this_value, '1', FALSE );

                        /* Output the block */
                        $output .= $this->print_table_row_open( __($block['name']) );
                            $output .= '<input  type="checkbox"' . $this_attr_name_id . ' value="1"' . $checked . '/> ';
                        $output .= $this->print_form_labels( $block['id'], '<span class="description">' . $block['desc'] . '</span>' ); // Sorry, stupid exception since the label of the check box is the description; see print_form_descriptions()
                        $output .= $this->print_table_row_close();
                        
                        /* Cleanup */
                        $block_previous_type = 'checkbox';
                        
                    break;
                    
                    case 'section':
                        
                        /* Shall we close (javascript hide/close the section on load? */
                        if ( isset($block['is_shut']) && $block['is_shut'] ) 
                            $kst_do_close_section = ' closed';
                        
                        /* Er, bad naming here...I mean should we close the div containers? */
                        $output .= $this->might_close_section( $kst_do_end_section, $block_previous_type );
                        
                        /* Output the block */
                        $output .= '<div class="postbox ' . $kst_do_close_section . '">';
                            $output .= '<div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>' .  __($block['name']) . '</span></h3>';
                                $output .= '<div class="inside">';
                                $output .= __($block['desc']);
                                // We MIGHT close this section later on - so yeah the div's aren't close here
                        
                        /* Cleanup */
                        $kst_do_end_section = TRUE;
                        $kst_do_close_section = FALSE;
                        $block_previous_type = 'section';
                        
                    break;
                    
                    case 'subsection':
                        
                        $output .= $this->might_close_table( $block_previous_type );
                        
                        /* Output the block */
                        $output .= "<h4>" . __($block['name']) . "</h4>";
                        $output .= __($block['desc']);
                        
                        /* Cleanup */
                        $block_previous_type = 'subsection';
                        
                    break;
                    
                    default:
                        /* Should it be closed by default? */
                        if ( isset($block['wrap_as']) && !empty( $block['wrap_as'] ) && $block['wrap_as'] != 'section' ) 
                            $kst_wrap_as = $block['wrap_as'];
                        else 
                            $kst_wrap_as = 'subsection';
                            
                        $output .= $this->might_close_table( $block_previous_type );
                            
                        $output .= __($block['desc']);
                        $block_previous_type = $kst_wrap_as;
                    break;
                }
            }
            
            /* Close section if one was started */
             if ( $kst_do_end_section ) {
                $output .= '</dl>'; // Close .form-table
                $output .= '</div>'; // Close .inside
                $output .= '</div>'; // Close .postexcerpt
            }
            $output .= "</div>"; // End normal-sortables
            
            if ( $can_save ) {
                settings_fields( $this->settings_options_group ); // Output nonce, action, and option_page fields
                $output .= "<p class='submit'>";
                    $output .= "<input type='submit' class='button-primary' value='" . __('Save Changes') . "' />";
                    $output .= "<input type='hidden' name='action' value='save' />  ";
                $output .= "</p>";
            }
        $output .= "</form>";
        
        /* Reset options form */
        if ( isset($can_save) ) {
            $output .= "<form method='post'>";
                $output .= "<p class='submit'>";
                    $output .= "<input type='submit' class='button-secondary' value='Reset' title='Delete current settings and reset to defaults' />";
                    $output .= "<input type='hidden' name='action' value='reset' />";  
                $output .= "</p>";
            $output .= "</form>";
        }
        
        $output .= "</div>"; // End options page 'wrap'
        
        /* You can filter this if you want */
        $output = apply_filters( 'kst_option_page_output', $output );
        
        /* Okay we are done - so spit it out already */
        echo $output;
        
    } // End kst_options()
    
    /**
     * Add hooks to create options menu and page
     * 
     * @since       0.1
     * @param       KST_Options::options_array
     * @uses        KST_Options::register_settings()
     * @uses        KST_Options::manage_page()
     * @uses        add_theme_page() WP function
     * @uses        add_action() WP function
     */
    public function add_page() {
        /* Get their options array now (hopefully late enough to catch any stragglers) */
        $this->options_array =& $GLOBALS[$this->options_array_name]; 
        
        /* register settings for options */
        add_action( 'admin_init', array(&$this, 'register_settings' )); 
        
        /* Are we adding a top level menu? */
        if ( 'top' == $this->parent_menu ) {
            add_menu_page( $this->page_title, $this->menu_title, 'manage_options', $this->menu_slug, array(&$this, 'manage_page') ); //, $icon_url, $position
        }
        
        /* Always add a submenu (if parent the menu_title is used for both parent and submenu per WP best practice) */
        add_submenu_page($this->parent_slug, $this->page_title, $this->menu_title, 'manage_options', $this->menu_slug, array(&$this, 'manage_page') );//'make_menu_shit'
        
    }
    
    /**
     * Register the options with WP
     * 
     * @since 0.1
     * @uses KST_Options::format_option_id()
     * @uses KST_Options::options_array 
     * @uses register_setting() WP function
     * 
     * NOTE: Creates option with prefix prepended
     *       "option_1" is saved to the db as "prefix_option_1"
     */
    public function register_settings() {
        
        /* Make sure $this->options_array exists and give help */
        if ( !$this->options_array )
            $this->options_array = array (
                                        array(  "name"    => __('You did not define any options'),
                                                "desc"    => __("
                                                                <p>\$options_array DOES NOT EXIST.<br />You must define a \$options_array array to use the built-in theme options library or you should comment it out and not load it at all</p>
                                                                <p>See <a href='http://beingzoe.com/zui/wordpress/kitchen_sink_theme'>Kitchen Sink documentation</a> for help.</p>
                                                            "),
                                                "type"    => "section")
                                    );
            
        /* Make sure $this->options_array is an array and give help */
        if ( isset($this->options_array) && !is_array($this->options_array) ) {
            $this->options_array = array (
                                        array(  "name"    => __("\$options_array must be an array"),
                                                "desc"    => __("
                                                                <p>See <a href='http://beingzoe.com/zui/wordpress/kitchen_sink_theme'>Kitchen Sink documentation</a> for help.</p>
                                                            "),
                                                "type"    => "section")
                                    );
        }
        
        /**
         * register our options with WP
         * must register each option to be used in options form
         */
        foreach ($this->options_array as $value) {
            if ( isset($value['id']) )
                register_setting( $this->settings_options_group, $this->format_option_id( $value['id'] ) );
        }
    }
    
    /**
     * Saves/resets the form AND calls the page/form builder
     * 
     * @since       0.1
     * @uses        KST_Options::kst_options()
     * @uses        KST_Options::format_option_id
     * @uses        KST_Options::parent_menu
     * @uses        KST_Options::parent_slug
     * @uses        KST_Options::menu_slug
     * @uses        update_option() WP Function
     * @uses        delete_option() WP function
     */
    public function manage_page() { 
        
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
                            $option_name = $this->format_option_id( $value['id'] ); // Name of option to insert
                            $option_value = $_REQUEST[ $option_name ]; // Value of option being inserted
                            $result = update_option( $option_name , $option_value ); // oh-oh-oh it's magic
                        }
                    } 
                    header("Location: " . $base_page . $this->menu_slug . "&updated=true");  
                    exit;  
                } else if( 'reset' == $kst_option_save_action ) {  
                    foreach ($this->options_array as $value) {  
                        delete_option( $this->format_option_id( $value['id'] ) ); // bye-bye
                    }  
                    header("Location: " . $base_page . $this->menu_slug . "&reset=true");  
                    exit;  
                } // End ACTIONS
            } // END if correct page 
        } // END if action
            
        $this->kst_options(); // Otherwise build load the page
    } // END manage_menu
    
    
    /**
     * Get the menu slug for this page
     * 
     * Protects us from page name changes and deal with top level menus later
     * 
     * @since       0.1
     * @uses        KST_Options::get_parent_menu_slug()
     * @uses        KST_Options::parent_menu
     * @return      string
     * @link        http://codex.wordpress.org/Adding_Administration_Menus
     */
    private function get_parent_slug() {
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
                return $this->get_parent_menu_slug();
            default:
                exit("<h2>Where should we put this fancy menu you are making?</h2><p>We can't find the parent_menu you specified (" . $this->parent_menu . ").</p><p>Do one of the following:</p><ul><li>Pass a known WP menu name (e.g. 'appearance', 'settings')</li><li>Pass 'top' (i.e. a new top level menu)</li><li>Or pass the entire object of a custom parent menu you already created</li></ul><p>If the parent menu was created like...</p><p><code>\$my_parent_object = new KST_Options(...);</code></p><p>Then you would pass the object like...</p><p><code>\$my_submenu_object = new KST_Options('...', '...', '...', <strong>\$my_parent_object</strong>, '...' );</code></p><p>If you aren't in the middle of setting up your custom admin menus using the 'Kitchen Sink KST_Options class' then something is terribly wrong with the world.</p>");
        }
        
    } // END get_parent_slug()
    
    /**
     * Get the parent menu slug from passed parent menu object (for submenus)
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
     * In kct_options() page/form output:
     * Long page title to display as header on page 
     * 
     * @since       0.1
     * @param       required string $title the title you passed off to us because you don't like my formatting ;) 
     * @uses        KST_Options::doodad_name
     * @uses        KST_Options::menu_title
     * @return      string
     */
    private function set_page_title( $title ) {
        return ( $title )   ?  $title
                            : $this->doodad_name . " " . $this->menu_title;
    }
    
    /**
     * In kct_options() page/form output:
     * output form element labels
     * 
     * @since       0.1
     * @param       required string $id the actual id we are using on the form element
     * @param       required string $text the text for the label
     * @uses        KST_Options::format_option_id()
     * @return      string
     */
    private function print_form_labels( $id, $text ) {
        return '<label for="' . $this->format_option_id( $id ) . '">' . __($text) . '</label>';
    }
    
    /**
     * In kct_options() page/form output:
     * output form element descriptions
     * 
     * @since       0.1
     * @param       required string $text the description being output
     * @return      string
     */
    private function print_form_descriptions( $text ) {
        return '<span class="description">' . __($text) . '</span>';
    }
    
    /**
     * In kct_options() page/form output:
     * output open of form element wrappers
     * 
     * @since       0.1
     * @return      string
     */
    private function print_table_row_open( $text ) {
        return "<dt>{$text}</dt><dd>";
    }
    
    /**
     * In kct_options() page/form output:
     * output end of form element wrappers 
     * 
     * @since       0.1
     * @return      string
     */
    private function print_table_row_close() {
        return "</dd>";
    }
    
    /**
     * In kct_options() page/form output:
     * Close previous section IF not first section AND a section already has been opened 
     * 
     * @since       0.1
     * @param       required boolean $kst_do_end_section
     * @param       required string $block_previous_type 
     * @uses        KST_Options::might_close_table()
     * @return      string
     */
    private function might_close_section( $kst_do_end_section, $block_previous_type ) {
        $output = "";
        if ( $kst_do_end_section ) {
            $output .= $this->might_close_table( $block_previous_type );
            $output .= '</div>'; // Close .inside
            $output .= '</div>'; // Close .postexcerpt
        }
        return $output;
    }
    
    /**
     * In kct_options() page/form output:
     * Close definition list </dl>
     * 
     * @since       0.1
     * @param       required string $block_previous_type 
     * @return      string
     */
    private function might_close_table( $block_previous_type ) {
        $output = "";
        if ( $block_previous_type && ( $block_previous_type != 'section' &&  $block_previous_type != 'subsection' ) ) {
            $output .= '</dl>'; // Close .form-table
        }
        return $output;
    }
    
}

