/********************
 * Additional Image Sizes javascript
********************/

jQuery.noConflict();//WordPress compatibility best practice

jQuery(document).ready(function($) { // noconflict wrapper to use shorthand $() for jQuery() inside of this function

    $('.show_hide').css({display:'none'});

    $('.show_hide_advanced').each(function() {
        input = "input[name=" + $(this).attr('id') + "]";
        target = "." + $(this).attr('id');
        if ( 'hide' == $(input).val()) {
            $(target).hide();
        } else {
            $(target).show();
        }
    });

    $('.show_hide_advanced').click(function() {
        input = "input[name=" + $(this).attr('id') + "]";
        target = "." + $(this).attr('id');
        if ( 'hide' == $(input).val()) {
            $(input).val('show');
            $(target).show();
        } else {
            $(target).hide();
            $(input).val('hide');
        }
    });

    $('#continue_creating').click(function() {
            $('#form_create_sizes').submit();
    });

    $('#continue_deleting').click(function() {
        $('#delete_images_for_deleted_sizes').click();
    });

    $('#delete_images_for_deleted_sizes').click(function() {
        result = true;
        if ( !$('#simulate_delete').attr('checked') && 0 == $('#offset_delete').val() ) {
            result = confirm('You are really going to delete some files this time.');
        }
        return result;
    });
});