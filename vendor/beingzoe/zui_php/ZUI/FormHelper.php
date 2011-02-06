<?php
/**
 * Automatically create and manage WordPress pages
 *
 * @package     ZUI
 * @subpackage  HTML
 * @author      zoe somebody
 * @link        http://beingzoe.com/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
*/


/**
 * Class for creating forms programmatically
 * Creates dl style form pattern
 *
 * @version     0.1
 * @since       0.1
 * @todo        Add name/value pairs like WP custom fields
*/
class ZUI_FormHelper {

    /**#@+
     * @access private
     * @var array
    */
    private static $blocks_of_type_section;    // Array of form output blocks "of type section" (not a form element); Used for conditionally formatting output;
    private static $blocks_of_type_form;    // Array of form output blocks "of type section" (not a form element); Used for conditionally formatting output;
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
        return self::$blocks_of_type_section = array('section', 'subsection', 'label');
    }
    public static function get_blocks_of_type_form() {
        return self::$blocks_of_type_all = array('text', 'radio', 'checkbox',  'textarea', 'select', 'select_wp_categories', 'select_wp_pages' );
    }
    public static function get_blocks_of_type_all() {
        return self::$blocks_of_type_all =  array_merge(self::get_blocks_of_type_section(), self::get_blocks_of_type_form() );//array('section', 'subsection', 'text', 'radio', 'checkbox', 'textarea', 'select', 'select_wp_categories', 'select_wp_pages' );
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
    public static function dt( $content = NULL, $b_wrap_attr = NULL, $inner_wrap = NULL ) {
        // Allow inner wrap
        $inner_wrap_open = "";
        $inner_wrap_close = "";
        if ( NULL !== $inner_wrap && !is_array($inner_wrap) ) {
            $inner_wrap_open = "<{$inner_wrap}>";
            $inner_wrap_close = "</{$inner_wrap}>";
        } else if ( NULL !== $inner_wrap && is_array($inner_wrap) ) {
            // If array the first index is 'complete open tag' and second is 'complete close tag'
            $inner_wrap_open = $inner_wrap[0];
            $inner_wrap_close = $inner_wrap[1];
        }

        return "<dt{$b_wrap_attr}>{$inner_wrap_open}{$content}{$inner_wrap_close}</dt>";
    }


    /**
     * Markup dd
     *
     * @since       0.1
     * @param       optional string $content
     * @param       optional string $custom_attr any extra parameter="" to add - include a leading space
     * @return      string
    */
    public static function dd( $content = NULL, $b_wrap_attr = NULL, $inner_wrap = NULL ) {
        // Allow inner wrap
        $inner_wrap_open = "";
        $inner_wrap_close = "";
        if ( NULL !== $inner_wrap && !is_array($inner_wrap) ) {
            $inner_wrap_open = "<{$inner_wrap}>";
            $inner_wrap_close = "</{$inner_wrap}>";
        } else if ( NULL !== $inner_wrap && is_array($inner_wrap) ) {
            // If array the first index is 'complete open tag' and second is 'complete close tag'
            $inner_wrap_open = $inner_wrap[0];
            $inner_wrap_close = $inner_wrap[1];
        }

        return "<dd{$b_wrap_attr}>{$inner_wrap_open}{$content}{$inner_wrap_close}</dd>";
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
    public static function input_text($id, $value = NULL, $size = 23, $maxlength = 255, $b_form_attr = NULL ) {
        // attributes
        $value = " value='$value'";
        $size = " size='$size'";
        $maxlength = " maxlength='$maxlength'";
        // return
        return "<input type='text'" . $value . self::same_name_id($id) . $size . $maxlength . $b_form_attr . ' />';
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
    public static function input_radio($id, $key, $keyvalue, $select_this, $b_form_attr = NULL, $b_label_attr = NULL) {

        $checked = self::checked($select_this, $keyvalue);
        /* Radio buttons are exceptional
         * The 'id' must be unique but the 'name' the same for each.
         * Similar issue in getting the value.
         * And labels go after the radio.
         */
        $output = ' <span class="radio"><input  type="radio" name="' . $id . '" id="' . $id . $key . '" value="' . $keyvalue . '"' . $checked . $b_form_attr .'/> ';
        $output .= self::label( $id . $key, $keyvalue, $b_label_attr ) . "</span> ";
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
    public static function input_checkbox($id, $select_this, $b_form_attr = NULL) {
        $checked = self::checked($select_this);
        return '<input  type="checkbox"' . self::same_name_id($id) . ' value="1"' . $checked . $b_form_attr . '/> ';
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
    public static function textarea($id, $value = NULL, $cols = 50, $rows = 5, $b_form_attr = NULL) {
        // attributes
        $cols = ' cols="' . $cols . '"';
        $rows = ' rows="' . $rows . '"';
        // return
        return "<textarea" . self::same_name_id($id) . $cols . $rows . $b_form_attr . ">" . $value . "</textarea>";
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
    public static function select($id, $options, $size = 1, $multi = FALSE, $b_form_attr = NULL) {
        // attributes
        $size = " size='$size'";
        $do_as_array = "";
        if ( $multi ) { // multiple selector
            $do_as_array = '[]';
            $multi = "[]' multiple='multiple";
        }
        // Create output
        return "<select" . " name='" . $id . $multi . "' id='" . $id . "'" . $size . $b_form_attr . ">" . $options . "</select>";
    }


    /**
     * Markup option container - just one at a time
     * Call this in your loop for your options
     * Save the output of the loop and then send that as a
     * preformatted string of $options for self::select()
     *
     * @since       0.1
     * @see         ZUI_FormHelper::select()
     * @param       required string $value value=""
     * @param       required string $select_this what to match against for a selected=""
     * @param       optional string $text text shown in dropdown
     * @param       optional string $custom_attr any extra parameter="" to add - include a leading space
     * @return      string
    */
    public static function option($value = NULL, $select_this = NULL, $text = NULL, $b_form_attr = NULL) {
        // attributes
        if ( !empty($value) )
            $value_attr = " value='{$value}'";
        else if ( empty($value) )
            $value_attr = " value='{$text}'";

        $selected = self::selected($select_this, $value);

        // return
        return "<option {$value_attr}{$selected}{$b_form_attr}>{$text}</option>";
    }


    /**
     * Return a select box with WP categories
     * Uses built-in WP wp_dropdown_categories and accepts the same arguments
     *
     * Cannot accept b_form_attr
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
        // Removed the default of "selected"           => "-1", because it was causing problems with metabox as -1 is a value and caused otherwise empty groups to be saved - and honestly these types shouldn't be in a generic FormHelper anyway
        $defaults =  array(
            "show_option_none"   => "None",
            "orderby"            => "name",
            "hierarchical"       => 1,
            'echo'               => 0
        );
        $args = array_merge( $defaults, $args );

        return wp_dropdown_categories( $args );
    }


    /**
     * Return a select box with WP pages
     * Uses built-in WP wp_dropdown_pages and accepts the same arguments
     *
     * Cannot accept b_form_attr
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
        // Removed the default of "selected"           => "-1", because it was causing problems with metabox as -1 is a value and caused otherwise empty groups to be saved - and honestly these types shouldn't be in a generic FormHelper anyway
        $defaults =  array(
            "show_option_none"   => "None",
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
    public static function block_form_element_typical($id, $name, $element, $desc, $b_wrap_attr = NULL, $b_label_attr = NULL, $dt_inner_wrap = NULL , $dd_inner_wrap = NULL) {
        // Block
        $output = self::dt( self::label( $id, $name, $b_label_attr ), $b_wrap_attr, $dt_inner_wrap );
        $output .= self::dd( $element, $b_wrap_attr, $dd_inner_wrap );
        if ( !empty( $desc ) && !is_array( $desc ) ) {
            $output .= self::dd( $desc, " class='explanation'", $dd_inner_wrap );
        } else if ( !empty( $desc ) && is_array( $desc ) ) {
            foreach ( $desc as $dd ) {
                $output .= self::dd( $dd, " class='explanation'", $dd_inner_wrap );
            }
        }
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
    public static function block_form_element_radio_group($name, $elements, $desc, $b_wrap_attr = NULL, $dt_inner_wrap = NULL, $dd_inner_wrap = NULL) {
        // Block
        $output = self::dt( $name, $b_wrap_attr, $dt_inner_wrap ); // self::label( $id, $name )
        $output .= self::dd( $elements, $b_wrap_attr, $dd_inner_wrap );
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
     * @param       required boolean $do_close_section
     * @param       required string $b_type_previous
     * @uses        ZUI_FormHelper::block_might_close_dl()
     * @return      string
    */
    public static function block_might_close_section( $do_close_section, $b_type_previous, $section_close_template ) {
        if ( $do_close_section ) {
            $output = self::block_might_close_dl( $b_type_previous );
            $output = $section_close_template . "<!--Closed in might_close_section-->";
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
     * @param       required string $b_type_previous
     * @return      string
    */
    public static function block_might_close_dl( $b_type_previous ) {
        if ( $b_type_previous && ( $b_type_previous != 'section' &&  $b_type_previous != 'subsection' ) )
            return '</dl>'; // Close .form-table
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


    /**
     * Simple Templating for our sections and what not
     *
     * @since       0.1
    */
    protected static function simpleTemplateFilter($args, $template) {
        $defaults = array(
              "{section_name}" => "",
            );
        $args = array_merge($defaults, $args);
        foreach ($args as $tag => $value) {
          $replaced = str_replace($tag, $value, $template);
        }
        return $replaced;
    }


    /**
     * Build form by looping over form array
     *
     * @since       0.1
     * @param       required array $form_array
     *              'options' key being the most vital
     *
     * @return      string form output
    */
    public static function makeForm($form_array) {

        // Prep
        $b_type_previous = 'NEW';
        $output = "";
        $do_close_section = FALSE;

        // Templates for sections
        $section_open_template    = ( isset($form_array['section_open_template']) && !empty( $form_array['section_open_template']) )    ? $form_array['section_open_template']
                                                                                                                                        : '<fieldset class="zui_form_section"><legend>{section_name}</legend>'; // What to wrap form sections in
        $section_close_template   = ( isset($form_array['section_close_template']) && !empty( $form_array['section_close_template']) )  ? $form_array['section_close_template']
                                                                                                                                        : '</fieldset>'; // close section wrap
        $subsection_open_template    = ( isset($form_array['subsection_open_template']) && !empty( $form_array['subsection_open_template']) )    ? $form_array['subsection_open_template']
                                                                                                                                                 : '<h4>{section_name}</h4>'; // What to wrap form subsections in
        $subsection_close_template   = ( isset($form_array['subsection_close_template']) && !empty( $form_array['subsection_close_template']) )  ? $form_array['subsection_close_template']
                                                                                                                                                 : ''; // close subsection wrap

        foreach ($form_array['options'] as $key => $block) {

            // Loop prep
            $b_type             = $block['type'];
            $b_id               = $key;
            $b_name             = $block['name'];
            $b_desc             = $block['desc'];
            $b_label_attr       = ( isset($block['label_attr']) && !empty($block['label_attr']) )   ? $block['label_attr']
                                                                                                    : "";
            $b_wrap_attr        = ( isset($block['wrap_attr']) && !empty($block['wrap_attr']) )     ? $block['wrap_attr']
                                                                                                    : ""; // not used yet
            $b_wrap_as          = ( isset($block['wrap_as']) )                                      ? $block['wrap_as']
                                                                                                    : $b_type;

            // If we have form type of block then get the form type stuff
            if ( in_array( $b_type, self::get_blocks_of_type_form()) ) {

                // Kind of sucks
                if ( isset($block['value']) && !empty($block['value']) ) // Exists and is not empty
                    $b_value = $block['value'];
                else if ( !isset($block['value']) && isset($block['default']) ) //passed a null value or really didn't get passed but a default was
                    $b_value = $block['default'];
                else // Otheriwse we are safe just returning an empty string?
                    $b_value = "";

                $b_value_default = ( isset($block['default']) && !empty( $block['default']) )    ? $block['default']
                                                                                                 : NULL; // Value to fill/set the input with
                $b_size          = ( isset($block['size']) && !empty( $block['size']) )          ? $block['size']
                                                                                                 : FALSE; // can't set a default unless we know what block it is - so we'll wait
                $b_maxlength     = ( isset($block['maxlength']) && !empty($block['maxlength']) ) ? $block['maxlength']
                                                                                                 : 255;
                $b_cols          = ( isset($block['cols']) && !empty($block['cols']) )           ? $block['cols']
                                                                                                 : 30;
                $b_rows          = ( isset($block['rows']) && !empty($block['rows']) )           ? $block['rows']
                                                                                                 : 3;
                $b_form_attr     = ( isset($block['form_attr']) && !empty($block['form_attr']) ) ? $block['form_attr']
                                                                                                 : "";
                $dt_inner_wrap   = ( isset($block['dt_inner_wrap']) && !empty($block['dt_inner_wrap']) )      ? $block['dt_inner_wrap']
                                                                                                              : NULL;
                $dd_inner_wrap   = ( isset($block['dd_inner_wrap']) && !empty($block['dd_inner_wrap']) )      ? $block['dd_inner_wrap']
                                                                                                              : NULL;
            }

            // Start new dl IF -- First time through and first block is a form block -- OR -- if next block is NOT a section AND the PREVIOUS WAS a 'section' AND the next block is a known block type
            if ( ( 'NEW' === $b_type_previous && in_array($b_type, self::get_blocks_of_type_form()) && FALSE !== $b_wrap_as ) || ( !in_array($b_type, self::get_blocks_of_type_section()) && in_array( $b_type_previous, self::get_blocks_of_type_section() ) && in_array($b_type, self::get_blocks_of_type_all()) ) )
                $output .= '<dl>';

            switch ( $b_type ) {

                case 'label':
                    // This is a special use case when outputting unwrapped form elements i.e. custom block types or unwrapped forms
                    // It is never put in it's own dt/dd as it is considered a 'section' block not a form block

                    // Create form element && Output Block
                    $output .= self::label( $b_id, $b_name, $b_label_attr );

                break; // END text input block

                case 'text':

                    // set size
                    $size = ( !empty( $b_size ) )   ? $b_size
                                                    : 23;

                    // Create form element
                    $element = self::input_text($b_id, $b_value, $size, $b_maxlength, $b_form_attr);

                    // Output Block
                    if ( FALSE !== $b_wrap_as )
                        $output .= self::block_form_element_typical($b_id, $b_name, $element, $b_desc, $b_wrap_attr, $b_label_attr, $dt_inner_wrap, $dd_inner_wrap);
                    else
                        $output .= $element;

                break; // END text input block

                case 'textarea':

                    // Create form element
                    $element = self::textarea($b_id, $b_value, $b_cols, $b_rows, $b_form_attr);

                    // Output Block
                    if ( FALSE !== $b_wrap_as )
                        $output .= self::block_form_element_typical($b_id, $b_name, $element, $b_desc, $b_wrap_attr, $b_label_attr, $dt_inner_wrap, $dd_inner_wrap);
                    else
                        $output .= $element;

                break;

                case 'select':

                    // Is it a multiple select?
                    if ( isset($block['multi']) && $block['multi'] ) // Yup, so now prep the element
                        $multi = TRUE;
                    else
                        $multi = FALSE;

                    // set size
                    if ( isset($b_size) && !empty( $b_size ) )
                        $size = $b_size; // Use yours
                    else if ( $multi )
                        $size = "5";
                    else
                        $size = "1";

                    // Create form element
                    // Loop over the select options
                    $options = ""; // empty options string to build
                    foreach ($block['options'] as $key => $keyvalue) {

                        // Figure out what to select
                        if ( !isset($b_value) && !empty($b_value_default) && $keyvalue == $b_value_default  ) // If a default was sent and this value does NOT exist (!isset($b_value))
                            $select_this = $b_value_default;
                        else if (is_array( $b_value) && in_array($keyvalue, $b_value ) ) // If selected values stored as array it's multi so check the array and just the keyvalue if it matches
                            $select_this = $keyvalue;
                        else
                            $select_this = $b_value;

                        // Create and save option form element
                        $options .= self::option($keyvalue, $select_this, $keyvalue, $b_form_attr);

                    }

                    $element = self::select($b_id, $options, $size, $multi, $b_form_attr);

                    // Output Block
                    if ( FALSE !== $b_wrap_as )
                        $output .= self::block_form_element_typical($b_id, $b_name, $element, $b_desc, $b_wrap_attr, $b_label_attr, $dt_inner_wrap, $dd_inner_wrap);
                    else
                        $output .= $element;

                break;

                case 'select_wp_categories':

                    // Set the current name/id and selected
                    $args =  array(
                        "name"      => $b_id,
                        "selected"  => $b_value
                    );

                    // Merge in their args if they exist
                    if ( isset($block['args']) && !empty($block['args']) ) {
                        $args = array_merge( $args, $block['args'] );
                    }

                    // Create form element
                    $element = self::select_wp_categories( $args );

                    // Output Block
                    if ( FALSE !== $b_wrap_as )
                        $output .= self::block_form_element_typical($b_id, $b_name, $element, $b_desc, $b_wrap_attr, $b_label_attr, $dt_inner_wrap, $dd_inner_wrap);
                    else
                        $output .= $element;

                break;

                case 'select_wp_pages':

                    // Set the current name/id and selected
                    $args =  array(
                        "name"      => $b_id,
                        "selected"  => $b_value
                    );

                    // Merge in their args if they exist
                    if ( isset($block['args']) && !empty($block['args']) ) {
                        $args = wp_parse_args( $block['args'], $args );
                    }

                    // Create form element
                    $element = self::select_wp_pages( $args );

                    // Output Block
                    if ( FALSE !== $b_wrap_as )
                        $output .= self::block_form_element_typical($b_id, $b_name, $element, $b_desc, $b_wrap_attr, $b_label_attr, $dt_inner_wrap, $dd_inner_wrap);
                    else
                        $output .= $element;

                break;

                case 'radio':

                    // Create form element
                    // Works the same as select options
                    // Loop over the radio options
                    $options = ""; // empty options string to build
                    foreach ($block['options'] as $key=>$keyvalue) {

                        // Figure out what to select
                        if ( !isset($b_value) && !empty($b_value_default) && $keyvalue == $b_value_default  ) // If a default was sent and this value does NOT exist (!isset($b_value))
                            $select_this = $b_value_default;
                        else
                            $select_this = $b_value;

                        // Create and save option form element
                        $options .= self::input_radio($b_id, $key, $keyvalue, $select_this, $b_form_attr, $b_label_attr);

                    }

                    // Output Block
                    if ( FALSE !== $b_wrap_as )
                        $output .= self::block_form_element_radio_group($b_name, $options, $b_desc, $b_wrap_attr, $dt_inner_wrap, $dd_inner_wrap);
                    else
                        $output .= $options;

                break;

                case 'checkbox':

                     // Create form element
                    $element = self::input_checkbox($b_id, $b_value, $b_form_attr);

                    // Output Block
                    if ( FALSE !== $b_wrap_as )
                        $output .= self::block_form_element_typical($b_id, $b_name, $element, $b_desc, $b_wrap_attr, $b_label_attr, $dt_inner_wrap, $dd_inner_wrap);
                    else
                        $output .= $element;

                break;

                case 'section':

                    // Should we close previous opened fieldset/div/etc... wrapper containers?
                    $output .= self::block_might_close_section( $do_close_section, $b_type_previous, $section_close_template );

                    // Output the block
                    $section_open_formatted = self::simpleTemplateFilter( array('{section_name}'=> $b_name ), $section_open_template);
                    $output .= $section_open_formatted;
                        $output .= $b_desc;
                        // We MIGHT close this section later on - so yeah don't include closing tags in the open_template close here

                    // Cleanup
                    $do_close_section = TRUE;

                break;

                case 'subsection':

                    // Output the block
                    $output .= self::block_might_close_dl( $b_type_previous ); // Should we close dl?
                    $subsection_open_formatted = self::simpleTemplateFilter( array('{section_name}'=>$b_name ), $subsection_open_template);
                    $output .= $subsection_open_formatted;
                    $output .= $b_desc;
                    $output .= $subsection_close_template;

                break;

                default:
                    // Custom block type - tell us to wrap it as another existing block type to close containing elements properly
                    // Section type is not allowed
                    if ( 'section' == $b_wrap_as )
                        $b_wrap_as = 'subsection';

                    // Output the block
                    $output .= self::block_might_close_dl( $b_type_previous ); // Should we close dl?
                    $output .= $b_desc; // Just dump it - they know what they are doing

                break;

            } // End block switch

            // Cleanup
            $b_type_previous = $b_wrap_as;

        } // End inner foreach

        /* Close section if one was started */
         if ( $do_close_section ) {
            $output .= self::block_might_close_dl( $b_type_previous ); // Close .form-table
            $output .= self::block_might_close_section( $do_close_section, $b_type_previous, $section_close_template );
        }

        return $output;
    } // End makeForm() method

} // Close FormHelper Class
