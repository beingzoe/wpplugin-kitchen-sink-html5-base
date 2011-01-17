<?php
/**
 * Parent class
 */
require_once KST_DIR_LIB . '/KST.php';

/**
 * Class for managing plugin through KST
 * 
 * @package     KitchenSinkHTML5Base
 * @subpackage  KitchenSinkWidgetClasses
 * @version     0.1 
 * @since       0.1
 * @author zoe somebody http://beingzoe.com/zui/
 */
class KST_Plugin extends KST {
    
   public  $testme;
    
    /**#@+
     * @since       0.1
     * @access      protected
     */
    protected $friendly_name;
    protected $prefix;
    protected $developer;
    protected $developer_url;
    /**#@-*/
    
    /**
     * @since       0.1
     */
    public function __construct($settings) {
        //parent::__construct($settings);
        
        $this->testme = "OH Yeah! PLUGIN DADDY!";
        
        /**#@+
         * KST default settings
         *
         * @since       0.1
         */
        $default_settings = array(
            'friendly_name'       => 'Kitchen Sink',
            'prefix'              => 'kst_0_2',
            'developer'           => 'zoe somebody',
            'developer_url'       => 'http://beingzoe.com/',
        );
        $settings = array_merge( $default_settings, $settings );
        
        /**#@+
         * KST plugin settings
         * 
         * @since 0.1
         */
        $this->set_friendly_name( $settings['friendly_name'] );
        $this->set_prefix( $settings['prefix'] );
        $this->set_developer( $settings['developer'] );
        $this->set_developer_url( $settings['developer_url'] );
        /**#@-*/
        
    }
    
}

