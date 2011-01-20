<?php
/**
 * Class for creating forms programmatically
 * Creates dl style form pattern
 *
 * @package     ZUI
 * @subpackage  Helpers
 * @version     0.1
 * @since       0.1
 * @author      zoe somebody
 * @link        http://beingzoe.com/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @todo        Add name/value pairs like WP custom fields
*/
class ZUI_FormHelper {

    /**#@+
     * @access private
     * @var array
    */
    private static $blocks_of_type_section;    // Array of form output blocks "of type section" (not a form element); Used for conditionally formatting output;
    private static $blocks_of_type_all;        // Array of ALL form output block types possible; Anything is custom and we just dump the block['desc']
    /**#@-*/

    /**#@+
     * Array of block types considered sections for decision making
     * Array of ALL KNOWN block types considered sections for decision making
     *
     * @since       0.1
     * @return      array
    */
    public static function get_blocks_of_type_section() {
        return self::$blocks_of_type_section = array('section', 'subsection');
    }
    public static function get_blocks_of_type_all() {
        return self::$blocks_of_type_all = array('section', 'subsection', 'text', 'radio',  'textarea', 'select', 'select_wp_categories', 'select_wp_pages' );//, 'input_checkbox'
    }
    /**#@-*/

    /**
     * Markup dt
     *
     * @since       0.1
     * @param       optional string $content
     * @param       optional string $custom_attr any extra parameter="" to add - include a leading space
     * @return      string
    */
    public static function dt( $content = NULL, $custom_attr = NULL ) {
        return "<dt{$custom_attr}>{$content}</dt>";
    }

    /**
     * Markup dt
     *
     * @since       0.1
     * @param       optional string $content
     * @param       optional string $custom_attr any extra parameter="" to add - include a leading space
     * @return      string
    */
    public static function dd( $content = NULL, $custom_attr = NULL ) {
        return "<dd{$custom_attr}>{$content}</dd>";
    }

    /**
     * Markup label for form element with proper id
     *
     * @since       0.1
     * @param       required string $id the actual id we are using on the form element
     * @param       required string $content the text for the label
     * @param       optional string $custom_attr any extra parameter="" to add - include a leading space
     * @return      string
    */
    public static function label( $id, $content, $custom_attr = NULL ) {
        return '<label for="' . $id . '"' . $custom_attr . '>' . $content . '</label>';
    }

    /**
     * Most form elements share the same
     * name="this" id="this" pattern
     *
     * @since       0.1
     * @param       required string $id/name to use
     * @return      string
    */
    public static function same_name_id($id) {
        return " name='{$id}' id='{$id}'";
    }

    /**
     * Markup text input
     *
     * @since       0.1
     * @param       required string $id
     * @param       optional string $value
     * @param       optional string|int $size
     * @param       optional string|int $maxlength
     * @param       optional string $custom_attr any extra parameter="" to add - include a leading space
     * @return      string
    */
    public static function input_text($id, $value = NULL, $size = 23, $maxlength = 255, $custom_attr = NULL) {
        // attributes
        $value = " value='$value'";
        $size = " size='$value'";
        $maxlength = " maxlength='$maxlength'";
        // return
        return "<input type='text'" . $value . self::same_name_id($id) . $size . $maxlength . $custom_attr . ' />';
    }

    /**
     * Markup radio input
     *
     * @since       0.1
     * @param       required string $id
     * @param       optional string $key
     * @param       optional string $keyvalue
     * @param       optional string $select_this
     * @return      string
    */
    public static function input_radio($id, $key, $keyvalue, $select_this) {

        $checked = self::checked($select_this, $keyvalue);
        /* Radio buttons are exceptional
         * The 'id' must be unique but the 'name' the same for each.
         * Similar issue in getting the value.
         * And labels go after the radio.
         */
        $output = ' <span class="radio"><input  type="radio" name="' . $id . '" id="' . $id . $key . '" value="' . $keyvalue . '"' . $checked . '/> ';
        $output .= self::label( $id . $key, $keyvalue ) . "</span> ";
        return $output;
    }

    /**
     * Markup checkbox input
     *
     * @since       0.1
     * @param       required string $id
     * @param       optional string $key
     * @param       optional string $keyvalue
     * @param       optional string $select_this
     * @return      string
    */
    public static function input_checkbox($id, $select_this ) {
        $checked = self::checked($select_this);
        return '<input  type="checkbox"' . self::same_name_id($id) . ' value="1"' . $checked . '/> ';
    }

    /**
     * Markup textarea
     *
     * @since       0.1
     * @param       required string $id
     * @param       optional string $value
     * @param       optional string|int  $cols
     * @param       optional string|int $rows
     * @param       optional string $custom_attr any extra parameter="" to add - include a leading space
     * @return      string
    */
    public static function textarea($id, $value = NULL, $cols = 50, $rows = 5, $custom_attr = NULL) {
        // attributes
        $cols = ' cols="' . $cols . '"';
        $rows = ' rows="' . $rows . '"';
        // return
        return "<textarea" . self::same_name_id($id) . $cols . $rows . $custom_attr . ">" . $value . "</textarea>";
    }

    /**
     * Markup select container - options are done separately
     *
     * @since       0.1
     * @see         ZUI_FormHelper::option()
     * @param       required string $id
     * @param       required string $options all the options already formatted and selected ready to output
     * @param       optional string $value
     * @param       optional string|int $size
     * @param       optional boolean $multi
     * @param       optional string $custom_attr any extra parameter="" to add - include a leading space
     * @return      string
    */
    public static function select($id, $options, $size = 1, $multi = FALSE, $custom_attr = NULL) {
        // attributes
        $size = " size='$size'";
        $do_as_array = "";
        if ( $multi ) { // multiple selector
            $do_as_array = '[]';
            $multi = "[]' multiple='multiple";
        }
        // Create output
        return "<select" . " name='" . $id . $multi . "' id='" . $id . "'" . $size . $custom_attr . ">" . $options . "</select>";
    }

    /**
     * Markup option container - just one at a time
     * Call this in your loop for your options
     * Save the output of the loop and then send that as a
     * preformatted string of $options for ZUI_FormHelper::select()
     *
     * @since       0.1
     * @see         ZUI_FormHelper::select()
     * @param       required string $value value=""
     * @param       required string $select_this what to match against for a selected=""
     * @param       optional string $text text shown in dropdown
     * @param       optional string $custom_attr any extra parameter="" to add - include a leading space
     * @return      string
    */
    public static function option($value = NULL, $select_this = NULL, $text = NULL, $custom_attr = NULL) {
        // attributes
        if ( !empty($value) )
            $value_attr = " value='{$value}'";
        else if ( empty($value) )
            $value_attr = " value='{$text}'";

        $selected = self::selected($select_this, $value);

        // return
        return "<option {$value_attr}{$selected}{$custom_attr}>{$text}</option>";
    }

    /**
     * Return a select box with WP categories
     * Uses built-in WP wp_dropdown_categories and accepts the same arguments
     *
     * @since       0.1
     * @see         http://codex.wordpress.org/Function_Reference/wp_dropdown_categories
     * @param       required array $args
     * @param       required string|array $selected_values selected values to match for selected=""
     * @return      string
    */
    public static function select_wp_categories( $args ) {
        // Set sensible defaults especially select NONE if we don't find a default later
        // THEY MUST PASS A NAME/ID in the arguments
        $defaults =  array(
            "show_option_none"   => "None",
            "selected"           => "-1",
            "orderby"            => "name",
            "hierarchical"       => 1,
            'echo'               => 0
        );
        $args = wp_parse_args( $args, $defaults );

        return wp_dropdown_categories( $args );
    }

    /**
     * Return a select box with WP pages
     * Uses built-in WP wp_dropdown_pages and accepts the same arguments
     *
     * @since       0.1
     * @see         http://codex.wordpress.org/Function_Reference/wp_dropdown_pages
     * @param       required array $args
     * @param       required string|array $selected_values selected values to match for selected=""
     * @return      string
    */
    public static function select_wp_pages( $args ) {
        // Set sensible defaults especially select NONE if we don't find a default later
        // THEY MUST PASS A NAME/ID in the arguments
        $defaults =  array(
            "show_option_none"   => "None",
            "selected"           => "-1",
            'echo'               => 0
        );
        $args = wp_parse_args( $args, $defaults );

        return wp_dropdown_pages( $args );
    }

    /**
     * Return a "typical" marked up definition list "row"
     * <dt><label></label></dt>
     * <dd><form element></dd>
     * <dd class="explanation">explanation content</dd>
     *
     * @since       0.1
     * @param       required string $id
     * @param       required string $name
     * @param       required string $element the form element
     * @param       required string $desc description/explanation text
     * @return      string
    */
    public static function block_form_element_typical($id, $name, $element, $desc) {
        // Block
        $output = self::dt( self::label( $id, $name ) );
        $output .= self::dd( $element );
        if ( !empty( $desc ) )
            $output .= self::dd( $desc, " class='explanation'" );
        // Return
        return $output;
    }

    /**
     * Return a "radio group" marked up definition list "row"
     * if you also use the method input_radio() ....
     * <dt></dt>
     * <dd><span class="radio"><input><label></label></span><span class="radio"><input><label></label></span></dd>
     * <dd class="explanation">explanation content</dd>
     *
     * @since       0.1
     * @param       required string $name
     * @param       required string $elements the radio button inputs and labels
     * @param       required string $desc description/explanation text
     * @return      string
    */
    public static function block_form_element_radio_group($name, $elements, $desc) {
        // Block
        $output = self::dt( $name ); // self::label( $id, $name )
        $output .= self::dd( $elements );
        if ( !empty( $desc ) )
            $output .= self::dd( $desc, " class='explanation'" );
        // Return
        return $output;
    }


    /**
     * block_might_close_section
     * Close previous section IF not first section AND a section already has been opened
     *
     * @since       0.1
     * @param       required boolean $kst_do_end_section
     * @param       required string $block_previous_type
     * @uses        KST_Options::block_might_close_dl()
     * @return      string
    */
    public static function block_might_close_section( $kst_do_end_section, $block_previous_type ) {
        if ( $kst_do_end_section ) {
            $output = self::block_might_close_dl( $block_previous_type );
            $output .= '</div>'; // Close .inside
            $output .= '</div>'; // Close .postexcerpt
            return $output;
        } else {
            return false;
        }
    }

    /**
     * block_might_close_dl
     * Close definition list </dl>
     *
     * @since       0.1
     * @param       required string $block_previous_type
     * @return      string
    */
    public static function block_might_close_dl( $block_previous_type ) {
        if ( $block_previous_type && ( $block_previous_type != 'section' &&  $block_previous_type != 'subsection' ) )
            return $output .= '</dl>'; // Close .form-table
        else
            return false;
    }

    /**
     * Outputs the html checked attribute.
     *
     * Compares the first two arguments and if identical marks as checked
     *
     * @since 0.1 From WordPress 3.0.4
     *
     * @param mixed $checked One of the values to compare
     * @param mixed $current (true) The other value to compare if not just true
     * @return string html attribute or empty string
    */
    public static function checked( $checked, $current = true ) {
        return self::_checked_selected_helper( $checked, $current, 'checked' );
    }

    /**
     * Outputs the html selected attribute.
     *
     * Compares the first two arguments and if identical marks as selected
     *
     * @since       1.0 From WordPress 3.0.4
     * @access      public
     * @param       mixed selected One of the values to compare
     * @param       mixed $current (true) The other value to compare if not just true
     * @return      string html attribute or empty string
    */
    public static function selected( $selected, $current = true ) {
        return self::_checked_selected_helper( $selected, $current, 'selected' );
    }

    /**
     * Outputs the html disabled attribute.
     *
     * Compares the first two arguments and if identical marks as disabled
     *
     * @since       0.1 From WordPress 3.0.4
     * @access      public
     * @param       mixed $disabled One of the values to compare
     * @param       mixed $current (true) The other value to compare if not just true
     * @return      string html attribute or empty string
    */
    private function disabled( $disabled, $current = true ) {
        return self::_checked_selected_helper( $disabled, $current, 'disabled' );
    }

    /**
     * Private helper function for checked, selected, and disabled.
     *
     * Compares the first two arguments and if identical marks as $type
     *
     * @since       0.1 From WordPress 3.0.4
     * @access      private
     * @param       any $helper One of the values to compare
     * @param       any $current (true) The other value to compare if not just true
     * @param       string $type The type of checked|selected|disabled we are doing
     * @return      string html attribute or empty string
    */
    function _checked_selected_helper( $helper, $current, $type ) {
        if ( (string) $helper === (string) $current )
            $result = " $type='$type'";
        else
            $result = '';

        return $result;
    }

}
