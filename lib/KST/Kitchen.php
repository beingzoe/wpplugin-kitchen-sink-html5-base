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

