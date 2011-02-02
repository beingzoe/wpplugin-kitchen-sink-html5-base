<?php
/**
 * Class for managing plugin through KST
 *
 * @package     KitchenSinkHTML5Base
 * @subpackage  Core
 * @version     0.1
 * @since       0.1
 * @author      zoe somebody
 * @link        http://beingzoe.com/
 * @author      Scragz
 * @link        http://scragz.com/
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
*/


/**
 *
 *
 *
*/
class KST_Kitchen_Core extends KST_Kitchen {

    /**
     * @since       0.1
    */
    public function __construct($settings) {
        $this->type_of_kitchen = 'core';
        parent::__construct($settings);
    }

}
