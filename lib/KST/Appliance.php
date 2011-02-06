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
    protected $_type_of_kitchen; // core, plugin, theme
    /**#@-*/

    /**#@+
     * @since       0.1
     * @access      protected
     * @var         boolean
    */
    protected $_is_core_appliance = FALSE; // Flag for core/bundled appliances
    /**#@-*/

    /**
     * Subclass constructor calls parent::_init()
     *
     * @since       0.1
    */
    protected function _init(&$kitchen, $appliance_settings = NULL, $appliance_options = NULL, $appliance_help = NULL) {

        // Common to all pages for this kitchen
        $this->_kitchen = $kitchen;
        $this->_type_of_kitchen = $this->_kitchen->getTypeOfKitchen();

        // Initialize as kitchen so we can use other appliances
        if ( NULL !== $appliance_settings) {
            $this->_appliance = ($this->_is_core_appliance) ? new KST_Kitchen_Core($appliance_settings)
                                                            : new KST_Kitchen_Plugin($appliance_settings);
        }

        // Add options
        if ( NULL !== $appliance_options) {
            $this->_appliance->load('options');
            $this->_appliance->options->add($appliance_options);
        }

        // Add help
        if ( NULL !== $appliance_help) {
            $this->_appliance->load('help');
            $this->_appliance->help->add($appliance_help);
        }

    }

}
