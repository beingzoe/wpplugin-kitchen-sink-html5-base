<?php
/**
 * Parent class for KST Appliances
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
 * @uses        KST_Kitchen
 */

/**
 * KST_Appliance class
 *
 * @since       0.1
*/
class KST_Appliance {

    /**#@+
     * @since       0.1
     * @access      protected
     * @var         object
    */
    protected $_kitchen; // current kitchen instance
    protected $_appliance; // self property object in KST context
    /**#@-*/


    /**#@+
     * @since       0.1
     * @access      protected
     * @var         string
    */
    protected $_type_of_kitchen;
    /**#@-*/


    /**
     * Helper method so subclasses can just load and use options in one call
     *
     * @since       0.1
    */
    protected function addOptionsGroup($options_array) {
        $this->_appliance->load('options');
        $this->_appliance->options->addGroup($options_array);
    }


}
