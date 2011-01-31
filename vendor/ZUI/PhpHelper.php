<?php
/**
 * Methods to do PHP-like functions
 *
 * @package     ZUI
 * @subpackage  PHP
 * @author      zoe somebody
 * @link        http://beingzoe.com/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
*/


/**
 * Class for doing PHP-like functions
 *
 * @version     0.1
 * @since       0.1
*/
class ZUI_PhpHelper {

    /**
     * array_search_recursive
     *
     * Searches haystack for needle and returns an array of the key path if
     * it is found in the (multidimensional) array, FALSE otherwise.
     *
     * @mixed array_searchRecursive ( mixed needle,
     * array haystack [, bool strict[, array path]] )
     *
     * @since       0.1
     * @see         http://greengaloshes.cc/2007/04/recursive-multidimensional-array-search-in-php/
     * @param       required string $needle
     * @param       required string $needle
     * @param       optional bool $strict
     * @param       optional array $path
     * @return      mixed array or false
    */
    public static function array_search_recursive( $needle, $haystack, $strict=false, $path=array() ) {
        if( !is_array($haystack) ) {
            return false;
        }

        foreach( $haystack as $key => $val ) {
            if( is_array($val) && $subPath = self::array_search_recursive($needle, $val, $strict, $path) ) {
                $path = array_merge($path, array($key), $subPath);
                return $path;
            } elseif( (!$strict && $val == $needle) || ($strict && $val === $needle) ) {
                $path[] = $key;
                return $path;
            }
        }
        return false;
    }


} // Close ZUI_PhpHelper Class
