<?php
/**
 * Parent class
*/
require_once KST_DIR_LIB . '/KST.php';

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
class KST_Kitchen extends KST {
    /**
     * Init Theme/Plugin instance
    */
    protected function __construct( $settings, $preset=null ) {

        /**#@+
         * KST default settings
         *
         * @since       0.1
         */
        $default_settings = array(
            'friendly_name'             => 'Kitchen Sink',
            'prefix'                    => 'kst_0_2',
            'developer'                 => 'zoe somebody',
            'developer_url'             => 'http://beingzoe.com/'
        );
        $settings = array_merge( $default_settings, $settings );

        $this->_setFriendlyName( $settings['friendly_name'] );
        $this->_setPrefix( $settings['prefix'] );
        $this->_setDeveloper( $settings['developer'] );
        $this->_setDeveloper_url( $settings['developer_url'] );
        $this->namespace = $this->_formatNamespace( $this->getPrefix() );
        if ($preset) {
            KST::initPreset($preset);
        }
    }
}

