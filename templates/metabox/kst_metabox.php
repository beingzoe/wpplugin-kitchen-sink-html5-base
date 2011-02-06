<?php
/**
 * MetaBox template for outputting auto built forms using ZUI_FormHelper
 * for WPAlchemy_MetaBox
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink/
 * @link        https://github.com/beingzoe/wpplugin-kitchen-sink-html5-base/wiki/Docs_appliance_core_metaboxes
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  KitchenSinkMetaBoxes
 * @version     0.1
 * @since       0.1
 * @uses        WPAlchemy_MetaBox
*/

    // N.B.: For sanity sake we only use $mb to represent the property variable name for
    //       the actual WPAlchemy_MetaBox instance that it gives to us for us in posts/pages
    //       Everything else in this template is part of the abstraction to use WPAlchemy_MetaBox
    //       in KST context/syntax in AUTOMATIC mode

    // Retrieve the original (but modified) form_array
    $form_array = KST_Appliance_MetaBox::getPropertyArrays($mb->id); //$all_metaboxes[$mb->id];

    // Get form block types to test against
    $blocks_of_type_form = ZUI_FormHelper::get_blocks_of_type_form();
    $blocks_of_type_section = ZUI_FormHelper::get_blocks_of_type_section();

    // Set in_group flag to false unless we come across a group
    $in_group = FALSE;

    foreach ($form_array['options'] as $key => $block) {

        // Skip if we are in the middle of a group and anything that isn't a form element
        if ( !$in_group && !in_array($block['type'],$blocks_of_type_section) ) {

            $i = 0; // for while have_fields() and have_fields_and_multi()

            // if have_fields_length is present for an option[$id] then replicate the default WPAlchemy_MetaBox behavior for have_fields
            if ( isset($form_array['options'][$key]['have_fields_length']) && is_int($form_array['options'][$key]['have_fields_length']) ) {

                // Get the metabox id to update the 'options' 'id' key  for serialized array storage (like Namespacing the options keys (id's) so that the metabox data can be stored as serialized array
                $new_name_key = $mb->get_the_name($key);

                // Must move it to the end of our modified array for sequencing - rename 'id' key for serialized storage - does nothing if WPALCHEMY_MODE_EXTRACT is used
                $form_array['options'][$new_name_key] = $form_array['options'][$key];
                unset($form_array['options'][$key]);

                // Get the length (# of fields they explicitly requested)
                $length = $form_array['options'][$new_name_key]['have_fields_length'];

                while ( $mb->have_fields($key,$length)) {

                    $serialized_name_key = $mb->get_the_name();
                    $serialized_value = $mb->get_the_value();

                    $form_array['options'][$serialized_name_key] = $form_array['options'][$new_name_key];

                    // Set the value if there is one
                    if ( !empty($serialized_value) )
                        $form_array['options'][$serialized_name_key]['value'] = $serialized_value; //$meta[$key][$i][$key] //$mb->get_the_value($test_this_unscoped_have_fields_name_key);

                    $i++;
                }

                // Delete the original option id key with have_fields_length since we created the requested amount of fields
                unset($form_array['options'][$new_name_key]);

            // if we hit a group_open block...
            } else if ( 'group_open' == $form_array['options'][$key]['type'] ) {

                $in_group = TRUE; // We are in a group!

                // Save the desc from the group open to use for the explanation dd for the group
                $group_key = $key; // So not necessary except to make this more readable
                $group_desc = $form_array['options'][$group_key]['desc'];
                $group_name = $form_array['options'][$group_key]['name'];

                // Must move it to the end of our modified array for sequencing - unlike have_fields() the $form_array key is the same for all fields in the group so we don't rename it here
                $temp = $form_array['options'][$group_key];
                unset($form_array['options'][$group_key]);
                $form_array['options'][$group_key] = $temp;
                unset($temp);

                // Set the opening of our new custom block - dt where label would be - open dd for our grouped form elements
                $desc_output = "<dl>";
                $desc_output .= "<dt>" . $form_array['options'][$group_key]['name'] . "</dt>"; // Uses group_open block 'name' for dt (not as a label though) //$form_array['options'][$serialized_name_key]['name']
                $desc_output .= "<dd>";

                // Set up our have_fields_and_multi() loop output storage
                $hfam_output = "";

                // Loop the have_fields_and_multi
                while ( $mb->have_fields_and_multi($group_key) ) { //

                    // Take a snapshot of the current $form_array to use fresh for each have_fields_and_multi iteration (for populating $hfam_output accurately)
                    $form_array_temp = $form_array;
                    $group_field_key = key($form_array_temp['options']); // set the group_field_key for the first loop - set again at the end of each group loop

                    // Create our local form array for this have_fields_and_multi
                    $group_form_array = array();

                    $hfam_output .= $mb->get_the_group_open();

                    while ( 'group_close' != $form_array_temp['options'][$group_field_key]['type'] ) {

                        // If we have a form element next in our group
                        if ( in_array($form_array_temp['options'][$group_field_key]['type'],$blocks_of_type_form)) {

                            // Get the data from WPAlchemy_MetaBox once
                            $serialized_name_key = $mb->get_the_name($group_field_key);
                            $serialized_value = $mb->get_the_value($group_field_key);

                            // Transfer the current array element to our serialized name key element
                            $group_form_array['options'][$serialized_name_key] = $form_array_temp['options'][$group_field_key];
                            $group_form_array['options'][$serialized_name_key]['wrap_as'] = FALSE; // This tells the ZUI_FormHelper we just want raw form fields

                            // Set the value if there is one
                            if ( !empty($serialized_value) )
                                $group_form_array['options'][$serialized_name_key]['value'] = $serialized_value;


                        } // not a form element must be ignored - sorry if you didn't know that ;)

                        // Manually step through the $form_array['options'] until we find the end of our group
                        next($form_array_temp['options']);

                        // Get the new group_field_key for the next loop
                        $group_field_key = key($form_array_temp['options']);

                    } // End while loop looking for a group_close block

                    // Add requisite FormHelp array settings - and remove_button for this group
                    $group_form_array['options']['remove_button']['name'] = '';
                    $group_form_array['options']['remove_button']['type'] = 'custom';
                    $group_form_array['options']['remove_button']['wrap_as'] = FALSE;
                    $group_form_array['options']['remove_button']['desc'] = '<a href="#" class="dodelete button">Remove</a>';

                    // Build the hfam form output for this group
                    $hfam_output .= ZUI_FormHelper::makeForm($group_form_array);

                    // Close the group every loop
                    $hfam_output .= $mb->get_the_group_close();

                    $i++;
                } // END while have_fields_and_multi

                $desc_output .= $hfam_output; // Add the group form element(s) to the
                $desc_output .= '<p style="margin-bottom:15px; padding-top:5px;"><a href="#" class="docopy-' . $group_key . ' button">Add Another</a></p>';
                $desc_output .= "</dd>";
                $desc_output .= "<dd>" . $group_desc . "</dd>";
                $desc_output .= "</dl>";

                // Add back our description and a custom type
                $form_array['options'][$group_key]['type'] = 'custom';
                $form_array['options'][$group_key]['desc'] = $desc_output;

            } else { // just a plain form field

                $serialized_name_key = $mb->get_the_name($key);
                $serialized_value = $mb->get_the_value($key);

                // Rename key for serialized storage and move it to the end of our modified array for sequencing (two for one ;)
                $form_array['options'][$serialized_name_key] = $form_array['options'][$key];
                unset($form_array['options'][$key]);

                // If we have a value send add it to the form_array
                if ( $mb->have_value($key) ) {
                    $form_array['options'][$serialized_name_key]['value'] = $serialized_value;
                }

            } // End if a group_open

        } else if ( !$in_group ) { // section block (not a form element) and not currently in a group

            // Must move it to the end of our modified array for sequencing
            $temp = $form_array['options'][$key];
            unset($form_array['options'][$key]);
            $form_array['options'][$key] = $temp;
            $form_array['options'][$key]['type'] = 'subsection'; // MetaBoxes can't have a section
            unset($temp);

        } else { // delete all else because it was probably part of a group - how's that for confidence?
            if ( 'group_close' == $form_array['options'][$key]['type'] )
                $in_group = FALSE;

            unset($form_array['options'][$key]);

        } // Blocks of type form or section conditional

    } // End form_array loop
?>

<div class="kst kst_meta_box clearfix">
    <?php echo ZUI_FormHelper::makeForm($form_array); ?>
    <div style="clear: both;"></div>
</div>