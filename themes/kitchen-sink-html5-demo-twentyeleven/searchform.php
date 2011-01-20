<?php 
/**
 * Display the search form 
 * Default WP DRY include/partial
 * 
 * Typically appears in widgetized sidebar and the search page
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     WordPress
 * @subpackage  kitchenSinkTheme
 * @version     0.4
 * @since       0.1
 */
?>
<form action="<?php echo home_url(); ?>" class="searchform" method="get">
	<div>
        <!--<label for="search" class="screen-reader-text">Search</label>-->
        <input type="text" name="s" value="" size="15" />
        <input type="submit" value="search" class="input_image_button sprite_button_search" />
	</div>
</form>
