
<div class="wrap">
<h2>Your Plugin Name</h2>

<form method="post" action="options.php">

<table class="form-table">

<tr valign="top">
<th scope="row">background_color</th>
<td><input type="text" name="background_color" value="<?php echo get_option('background_color'); ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">footer_copyright_text</th>
<td><input type="text" name="footer_copyright_text" value="<?php echo get_option('footer_copyright_text'); ?>" /></td>
</tr>

</table>

<?php settings_fields( 'settings_from_file' ); ?>

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>
