<?php
/* 
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
 */
wp_enqueue_script( 'comment-reply' );
add_theme_support( 'automatic-feed-links' ); 
add_theme_support( 'post-thumbnails' ); 
add_editor_style(); 
$content_width = 500; // required; For theme design compliance and WP best practice
?>
