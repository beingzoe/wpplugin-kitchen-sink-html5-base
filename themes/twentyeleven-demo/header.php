<!doctype html>
<?php
/**
 * HTML5 head and top of body partial include
 *
 * Loads <DOCTYPE />, <head />, <body>, <div id="doc">, <div id="hd" />
 * All unclosed tags are closed in footer.php
 *
 * Using HTML5 Boilerplate concepts and zui
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Themes
 * @subpackage  TwentyEleven
 * @version     0.1
 * @since       0.1
 */
?>
<!--[if lt IE 7 ]>              <html <?php language_attributes(); ?> class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>                 <html <?php language_attributes(); ?> class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>                 <html <?php language_attributes(); ?> class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>                 <html <?php language_attributes(); ?> class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->  <html <?php language_attributes(); ?> class="no-js"> <!--<![endif]-->
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title><?php wp_title( "", true, 'right'); ?></title>

<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_head(); /* Everything else: CSS, JS, feeds, meta tags etc... */ ?>

</head>
<body <?php body_class('wp'); ?>>

<div id="doc">
<header id="hd" class="clearfix" role="banner">
    <hgroup class="clearfix">
        <h1 id="hd_logo"><a href="<?php echo home_url(); ?>/" title="<?php bloginfo('name'); ?> Home" rel="home"><?php bloginfo('name'); ?></a></h1>
        <h2 id="hd_tag"><?php bloginfo('description'); ?></h2>
    </hgroup>
    <?php
        /* WP custom header image ala TwentyTen */
        if ( is_singular() && has_post_thumbnail( $post->ID )
                && ( /* $src, $width, $height */ $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'post-thumbnail' ) )
                    && $image[1] >= HEADER_IMAGE_WIDTH ) { // Use Post Thumbnail (not working yet because of my changes to featured image support)
                    echo get_the_post_thumbnail( $post->ID, 'post-thumbnail' );
                } else { // Use "Appearance > Background"
                    echo '<img id="hd_image" src="' . get_header_image() . '" width="' . HEADER_IMAGE_WIDTH . '" height="' . HEADER_IMAGE_HEIGHT . '" alt="" />' . "\n";
        }

        /* Output masthead menu e.g. container => false*/
        echo "<nav id='hd_menu' class='clearfix' rel='navigation'>"; //manually creating container for ARIA landmark roles
        wp_nav_menu( array(
                    'theme_location'    => 'hd_menu',
                    'container'         => false,
                    'container_class'   => false,
                    'menu_id'           => 'hd_menu_list',
                    'sort_column'       => 'menu_order',
                    'depth'             => '3',
                    'fallback_cb'     => 'kstWpNavMenuFallbackCb'
                    ) );
        echo "</nav>";
        ?>

</header>
<div id="pg" class="clearfix">
