<?php
/**
 * MP3 player for audio uploads
 *
 * Adds shortcode: [mp3player] e.g. [mp3player mp3="path/url/to/file.mp3" class="custom_class_name"]
 * 
 * N.B. SHORTCODE IS USED IN attachment.php and should be removed if this library is not used
 * 
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  Plugins:Media
 * @version     0.1
 * @since       0.1
 */

/**
 * WP hook
 */
add_shortcode('mp3player', 'kst_shortcode_mp3_player');
 
/**
 * Execute [mp3player] shortcode
 * 
 * Uses player_mp3_maxi.swf 
 * 
 * @since   0.1
 * @param   string mp3 required path/url/to/file.mp3
 * @param   string class optional custom class
 * @return  string  
 * @todo    Get links and include some documentation on kitchen sink theme site
 * @todo    This could probably be made better as I just whipped it up because the fact that you could upload mp3's with WP but couldn't play them in your theme was annoying
 */
function kst_shortcode_mp3_player($atts, $content = NULL) {
    extract(shortcode_atts(array(
        'mp3'    => '',
        'class'	 => 'mp3_player',
        'width'  => '300',
        'height' => '20'
    ), $atts));

    if ( empty($mp3) )
        return false; //nothing to do
    
    $player_uri = KST_URI_ASSETS . '/swf/player_mp3_maxi.swf';
    
    $output = 
<<< EOD
    <object type="application/x-shockwave-flash" data="{$player_uri}" width="{$width}" height="{$height}" class="{$class}">
    <param name="movie" value="{$player_uri}" />
    <param name="bgcolor" value="#ffffff" />
    <param name="FlashVars" value="mp3={$mp3}&amp;width={$width}&amp;autoplay=0&amp;autoload=1&amp;showstop=1&amp;showinfo=1&amp;showvolume=1&amp;showloading=always&amp;buttonwidth=25&amp;sliderwidth=10&amp;volumewidth=35&amp;volumeheight=8&amp;loadingcolor=888888&amp;buttonovercolor=888888" />
</object>	
EOD;
    
    return $output;
}

