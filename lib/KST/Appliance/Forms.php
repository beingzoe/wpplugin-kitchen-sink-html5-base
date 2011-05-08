<?php
/**
 * DEPRECATED and slated to be delete prior to official Version 0.1.0
 * DO NOT USE!!!!!
 *
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink/
 * @link        https://github.com/beingzoe/wpplugin-kitchen-sink-html5-base/wiki/Docs_appliance_core_forms
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @version     0.1
 * @since       0.1
*/

class KST_Appliance_Forms extends KST_Appliance {

    /**#@+
     *
     * @since       0.1
     * @access      protected
     * @var         array
    */
    protected $_form_settings = array();
    protected static $_all_form_ids = array(); // used to access child objects singleton-like
    protected static $_form_defaults = array(); // Common form values
    /**#@-*/


    /**#@+
     *
     * @since       0.1
     * @access      protected
     * @var         string
    */
    protected   $_result = NULL;
    /**#@-*/


    /**
     * Constructor
     *
     * @since       0.1
     * @param       required object $kitchen
    */
    public function __construct(&$kitchen) {

        // Every kitchen needs the basic settings
        $appliance_settings = array(
                    'friendly_name'       => 'KST Appliance: Core: Forms',
                    'prefix'              => 'kst_forms',
                    'developer'           => 'zoe somebody',
                    'developer_url'       => 'http://beingzoe.com/'
                );

        // Declare as core
        $this->_is_core_appliance = TRUE;
        // Common appliance
        parent::_init($kitchen, $appliance_settings);

    }


    /**
     * Add a new form to process
     *
     * Creates an object for the new form but we store the form_id in a static array
     * So the templates continue to call on the main forms object (same style as metabox for consistency)
     *
    */
    public function add($args) {

        // Merge with defaults to set this form's values
        $args = array_merge(self::$_form_defaults, $args);

        // And merge in anything missing that we must have
        $defaults = array(
                'form_id'                   => 'contact_form'
            );
        $args = array_merge($defaults, $args);

        // Use form_id to name shortcode and property object
        $property = $args['form_id'];
        $this->{$property} =& new KST_Appliance_Forms($this->_kitchen); // Simulate the initial load();

        // Store these form settings in this form
        $this->$property->_form_settings = $args;
        self::$_all_form_ids[$property] =& $this->$property->_form_settings; // Do we really need this?


        // Merge our form tags_values with the $_POST for the template
        if ( 0 < count($_POST) ) { // There wouldn't be a post on a failure (redirected)
            $this->$property->_form_settings['tags_values'] = array_merge($this->$property->_form_settings['tags_values'], $_POST);
        } else if ( 0 < count($_GET) ) {
            $this->$property->_form_settings['tags_values'] = array_merge($this->$property->_form_settings['tags_values'], $_GET);
        }

        // Handle processing if necessary for this form - looking for a post 'form_id'
        if ( isset($_POST['form_id']) && in_array($_POST['form_id'], $this->$property->_form_settings) ) {
            $property = $_POST['form_id'];
            $this->$property->process();
        }

        // Make optional shortcode
        $this->$property->_addOutputHookForFormProperty($property);

    }


    /**
     * Prep the form for output via [shortcode] OR $this->output('form_id');
     *
     * This method can't be used in the parent object (defaults and 'singleton-like')
     * but only on the child objects (individual forms). Indeed this function only exists
     * to tell WordPress which $this to callback to
     *
     * @since       0.1
     * @param       required array $args All the required settings to build a from (defaults should already be merged in during add() )
    */
    protected function _addOutputHookForFormProperty($property = 'contact_form') {
        // Make a shortcode and object member to display the form
        add_shortcode($property, array(&$this, 'output'));
    }


    /**
     * Prep the form for output via [shortcode] OR $this->same_name_as_shortcode();
     *
     * Only occurs in child objects
     *
     * @since       0.1
     * @return      string
    */
    public function output($form_id = NULL) {
        // Actual output code goes here - must contain values and messages if this is a repeat send

        // Let them output the form and results manually (without shortcode) but we need to proxy to the object for that
        if ( NULL != $form_id ) {
            $property = $form_id;
            $this->$property->output();
            return;
        }

        $output = "";

        // What state are we in?
        if ( isset($_GET['result']) && 'success' == $_GET['result'] ) {
            // Yay! We submitted a form and sent it off
            $output .= $this->_form_settings['success_template'];
        } else {
            // Attempted to submit but failed somehow
            if ( isset($_GET['result']) && 'success' == $_GET['result'] ) {
                // Flat out mail failure
                $output .= $this->_form_settings['failure_template'];
            } else if ( 'revalidate' == $this->_result ) {
                // Failed validation
                $output .= $this->_form_settings['revalidate_template'];
            }

            // Add the template to the output
            $output .= $this->_form_settings['form_template'];

        }


        // Add hidden fields to make form templates simpler
        $hidden_fields = '<input type="hidden" name="form_id" id="form_id" value="' . $this->_form_settings['form_id'] .  '" />';
        $hidden_fields .= wp_nonce_field( $this->_createNonceName(), $this->_createNonceName() ,TRUE, FALSE);
        $hidden_fields .= '</form>';

        // Filter and return the template
        $output = $this->_simpleTemplateFilter( $output );
        $output = str_replace("</form>", $hidden_fields, $output);
        return $output;

    }


    /**
     * Add a new form to process
     *
     * Creates an object for the new form but we store the form_id in a static array
     * So the templates continue to call on the main forms object (same style as metabox for consistency)
     *
     * Only happens in child object
     *
     * @since       0.1
     *
    */
    public function process() {

        // Not actually used as a property object in this method - just the form_id
        $property = $_POST['form_id'];

        // Validate nonce
        $nonce = ( isset($_POST[$this->_createNonceName()]) ) ? $_POST[$this->_createNonceName()]
                                                              : "";
        if ( !wp_verify_nonce($nonce, $this->_createNonceName()) )
            die('<p>We could not verify the validity of your form submission.<br />Please Try again.</p>');

        // Validate form
        $does_validate = $this->validate(); // return result from validate()

        // If we validate then try to send the email
        if ( $does_validate ) {

            // Send form
            $this->_result = ( $this->send() ) ? 'success'
                                               : 'failure'; // return result from send() //$this->send();

            $post_as_query_string = $this->_convertAndAddPostVariablesToQueryString();

            // Add the message to the query string if they want to display it
            if ( isset($this->_form_settings['message']) )
                $post_as_query_string .= "&message=" . urlencode($this->_form_settings['message']);

            // Redirect
            if ( array_key_exists('redirect_to', self::$_all_form_ids[$property]) ) {
                $redirect_to = self::$_all_form_ids[$property]['redirect_to'] . "?result={$this->_result}&form_id={$property}&{$post_as_query_string}";
            } else {
                $redirect_to = "http://" . $_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . "?result={$this->_result}&form_id={$property}&{$post_as_query_string}";
            }
            header("Location: " . $redirect_to);
            exit;

        } else {
            $this->_result = 'revalidate'; // Else fails validation so just reload the form - output() uses $this->_result to fill out the form
        }
    }


    /**
     * Validate the form
     *
     * @since       0.1
    */
    public function validate() {
        return TRUE;
        // We aren't validating anything yet. Let's send emails first
    }


    /**
     * Send the form as email
     *
     * @since       0.1
     * @return      boolean whether the mail was sent or not
    */
    public function send() {

//echo "<pre>";
//print_r($this->_form_settings);
//echo "<pre><br />";

        $to = $this->_form_settings['to'];
        $cc = (isset($this->_form_settings['cc']))      ? $this->_form_settings['cc']
                                                        : NULL;
        $bcc = (isset($this->_form_settings['bcc']))    ? $this->_form_settings['bcc']
                                                        : NULL;
        $subject = (isset($this->_form_settings['subject']))    ? $this->_form_settings['subject']
                                                        : "New message from your website.";

        // Just text emails for now
        $text_output = $this->_simpleTemplateFilter( $this->_form_settings['text_template'] );
        $message = $text_output;
        $this->_form_settings['message'] = $message; //esc_attr();
        //$headers = "From: $this->_form_settings['from']\r\nReply-To: $this->_form_settings['reply_to']";

        //echo "To = $to <br /> CC = $cc <br /> bcc = $bcc <br /> subject = $subject <br /><pre> $message";

        //send the email
        $mail_sent = mail( $to, $subject, $message, NULL );

        return $mail_sent;
    }


    /**
     * Set default values for all forms
     *
     * @since       0.1
     * @param       required array $args
    */
    public function setDefaults($args) {
        self::$_form_defaults = array_merge(self::$_form_defaults, $args);
    }


    /**
     * Simple Templating for outputting email content and success/failure messages
     *
     * @since       0.1
     * @param       required array $args
     *              -template
     *              -tags_values array of sections and values
     * @return      string template with values replaced
    */
    protected function _simpleTemplateFilter($template) {

        $replaced = $template;

        $search_pattern          = "/\{([a-zA-Z0-9_]+)\}/i";
        $replaced = preg_replace_callback($search_pattern, array($this, '_replace_value'), $replaced); //str_replace($tag, $value, $replaced);

        return $replaced;
    }


    protected function _replace_value($matches) {
        if ( isset($this->_form_settings['tags_values'][$matches[1]]) )
            return $this->_form_settings['tags_values'][$matches[1]];
        else
            return '';
    }


    protected function _convertAndAddPostVariablesToQueryString() {
        $query_string = ( $_SERVER['QUERY_STRING'] )    ? $_SERVER['QUERY_STRING'] . "&"
                                                        : "";
        if ($_POST) {
            $kv = array();
            foreach ($_POST as $key => $value) {
                $kv[] = "$key=" . urlencode($value);
            }
            $query_string .= join("&", $kv);
        }
        return  $query_string;
    }



    /**
     * Unique nonce name for forms
     *
     * Only used in child objects
     *
     * @since       0.1
     * @return      string
    */
    protected function _createNonceName() {
        return 'kst_forms_nonce_' . $this->_form_settings['form_id'];
    }

}
