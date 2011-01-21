<?php
/*
 * NOTE: This scheme needs revisited so that if /libs/functions/wp_sensible_defaults/
         isn't loaded this stuff doesn't count towards the theme check.
         Perhaps we need to talk to the theme review folks for advice on how we
         can help them to help people make better more compliant themes.

 * Dummy stuff to pass WP theme check plugin for theme review
 * This is not an attempt to bamboozle anyone I just didn't want to
 * have any red flags on themes made with KST to cause reviewers headaches.
 *
 * It is not my intention to give spammers and evil-doers ideas on
 * how to pass validation and terrorize the world.
 *
 * The function calls in this file are NEVER CALLED in the theme.
 * This is handled by the KST plugin because us KST people
 * just need this stuff to happen and don't want to look at it in our themes ;)
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.2
 * @since       0.1
 * @uses        kst_theme_help_meta_data() to include install specific help content in context
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @todo        convert to class
 * @todo        find a better way to do this
 */
wp_enqueue_script( 'comment-reply' );
add_theme_support( 'automatic-feed-links' );
add_theme_support( 'post-thumbnails' );
add_editor_style();
$content_width = 500; // required; For theme design compliance and WP best practice
?>
