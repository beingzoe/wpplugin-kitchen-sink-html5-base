<?php
/**
 * Classes for creating inflection both for display and consistent coding
 *
 * Based on Andreas Kollaros' Inflect.php class in his Aktive Merchant
 * @see     http://www.phpclasses.org/browse/file/32400.html
 *
 * Original thanks given to...
 * @see     http://www.eval.ca/articles/php-pluralize (MIT license)
 * @see     http://dev.rubyonrails.org/browser/trunk/activesupport/lib/active_support/inflections.rb (MIT license)
 * @see     http://www.fortunecity.com/bally/durrus/153/gramch13.html
 * @see     http://www2.gsu.edu/~wwwesl/egw/crump.htm
 *
 * Original changelog omitted
*/


/**
 * Methods for manipulating the inflection of string content
 *
 * @version     0.1
 * @since       0.1
 * @todo        Andreas had some extra methods for creating underscored, camelcased, and slugified strings that seem appropriate but in his implementation were far to project specific. Recreate in a more general and utile way
*/
class Inflect {

    /**#@+
     * @access public
     * @var array
    */
    public static $plural = array(
        '/(quiz)$/i'               => "$1zes",
        '/^(ox)$/i'                => "$1en",
        '/([m|l])ouse$/i'          => "$1ice",
        '/(matr|vert|ind)ix|ex$/i' => "$1ices",
        '/(x|ch|ss|sh)$/i'         => "$1es",
        '/([^aeiouy]|qu)y$/i'      => "$1ies",
        '/(hive)$/i'               => "$1s",
        '/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
        '/(shea|lea|loa|thie)f$/i' => "$1ves",
        '/sis$/i'                  => "ses",
        '/([ti])um$/i'             => "$1a",
        '/(tomat|potat|ech|her|vet)o$/i'=> "$1oes",
        '/(bu)s$/i'                => "$1ses",
        '/(alias)$/i'              => "$1es",
        '/(octop)us$/i'            => "$1i",
        '/(ax|test)is$/i'          => "$1es",
        '/(us)$/i'                 => "$1es",
        '/s$/i'                    => "s",
        '/$/'                      => "s"
    );

    public static $singular = array(
        '/(quiz)zes$/i'             => "$1",
        '/(matr)ices$/i'            => "$1ix",
        '/(vert|ind)ices$/i'        => "$1ex",
        '/^(ox)en$/i'               => "$1",
        '/(alias)es$/i'             => "$1",
        '/(octop|vir)i$/i'          => "$1us",
        '/(cris|ax|test)es$/i'      => "$1is",
        '/(shoe)s$/i'               => "$1",
        '/(o)es$/i'                 => "$1",
        '/(bus)es$/i'               => "$1",
        '/([m|l])ice$/i'            => "$1ouse",
        '/(x|ch|ss|sh)es$/i'        => "$1",
        '/(m)ovies$/i'              => "$1ovie",
        '/(s)eries$/i'              => "$1eries",
        '/([^aeiouy]|qu)ies$/i'     => "$1y",
        '/([lr])ves$/i'             => "$1f",
        '/(tive)s$/i'               => "$1",
        '/(hive)s$/i'               => "$1",
        '/(li|wi|kni)ves$/i'        => "$1fe",
        '/(shea|loa|lea|thie)ves$/i'=> "$1f",
        '/(^analy)ses$/i'           => "$1sis",
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i'  => "$1$2sis",
        '/([ti])a$/i'               => "$1um",
        '/(n)ews$/i'                => "$1ews",
        '/(h|bl)ouses$/i'           => "$1ouse",
        '/(corpse)s$/i'             => "$1",
        '/(us)es$/i'                => "$1",
        '/s$/i'                     => ""
    );

    public static $irregular = array(
        'move'   => 'moves',
        'foot'   => 'feet',
        'goose'  => 'geese',
        'sex'    => 'sexes',
        'child'  => 'children',
        'man'    => 'men',
        'tooth'  => 'teeth',
        'person' => 'people'
    );

    public static $uncountable = array(
        'sheep',
        'fish',
        'deer',
        'series',
        'species',
        'money',
        'rice',
        'information',
        'equipment'
    );
    /**#@-*/


    /**
     * Convert noun to plural form
     *
     * @param       required string $string     Noun to pluralize
    */
    public static function pluralize( $string ) {

        // save some time in the case that singular and plural are the same
        if ( in_array( strtolower( $string ), self::$uncountable ) )
            return $string;

        // check for irregular singular forms
        foreach ( self::$irregular as $pattern => $result ) {
            $pattern = '/' . $pattern . '$/i';

            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string);
        }

        // check for matches using regular expressions
        foreach ( self::$plural as $pattern => $result ) {
            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string );
        }

        return $string;
    }


    /**
     * Convert noun to singular form
     *
     * @param       required string $string     Noun to singularize
    */
    public static function singularize( $string ) {

        // save some time in the case that singular and plural are the same
        if ( in_array( strtolower( $string ), self::$uncountable ) )
            return $string;

        // check for irregular plural forms
        foreach ( self::$irregular as $result => $pattern ) {
            $pattern = '/' . $pattern . '$/i';

            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string);
        }

        // check for matches using regular expressions
        foreach ( self::$singular as $pattern => $result ) {
            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string );
        }

        return $string;
    }


    /**
     *
     *
     * @param       required string $count      How many of the noun do you have?
     * @param       required string $string     Noun to pluralize
    */
    public static function pluralizeIf($count, $string) {
        if ($count == 1)
            return "1 $string";
        else
            return $count . " " . self::pluralize($string);
    }


    /**
     * Convert string to camel case
     *
     *
    */
    /*
    static function camelize($string) {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }
    */


    /**
     *
     *
     *
    */
    /*
    static function underscore($string) {
        return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $string));
    }
    */

    /**
    * Modifies a string to remove al non ASCII characters and spaces.
    */
    /*
    static function slugify($string) {

        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $string);

        // trim
        $string = trim($string, '-');

        // lowercase
        $string = strtolower($string);

        // transliterate
        if (function_exists('iconv')) {
            $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
        }

        // remove unwanted characters
        $string = preg_replace('~[^-\w]+~', '', $string);

        if (empty($string)) {
            return false;
        }
        return $string;
    }
    */
}
