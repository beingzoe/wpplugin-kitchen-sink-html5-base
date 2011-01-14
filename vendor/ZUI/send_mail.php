<?php
/**
 * Email class abstraction
 * Very simple emailer currently accepts (1) attachment
 * Currently using NO CLASS and thus this abstraction library is broken 
 * Was using email classes by Manuel Lemos from PHPClasses.org
 * 
 * Can't use some function I can't remember right now that encodes data because 
 * of problems with WP community and idiots abusing the GPL of WP. And unfortunately 
 * Manuel Lemos' class used one of these functions
 * 
 * @author		zoe somebody
 * @link        http://beingzoe.com/zui/wordpress/kitchen_sink_theme
 * @copyright	Copyright (c) 2011, zoe somebody, http://beingzoe.com
 * @license		http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package     KitchenSinkHTML5Base
 * @subpackage  KitchenSinkThemeLibraries
 * @uses        kst_clean_user_input($str) to protect from XSS attack (in functions.php_
 * @todo        everything!!!!!
 * @todo        Find an email class (or just use sendmail) and finish this so WordPress Theme Review will like it
 * @todo        html email, multiple file attachements, alt reply/error_delivery addresses, Return-Path
 * 
 * $to_address          =
 * $from_address        = also sets $reply_address, $error_delivery_address
 * $subject             = 
 * $text_message        = body message
 * $response_message    = message to display/flash on success; returns error message if problem
 * $file_to_attach      = OPTIONAL path to file
 *
 * Returns null string on success or error message (this seems wrong)
 * 
 */
 

### EMAIL FUNCTIONS ###

/**
 * Send email
 * 
 * Abstraction to do some powerful email stuff ???? Ha.
 */
function zui_send_mail($to_address, $from_address, $subject, $text_message, $response_message, $file_to_attach = NULL) {
    require TEMPLATEPATH . '/_application/classes/EMAIL_MESSAGE.php'; //Email message class by Manuel Lemos from PHPClasses.org
    
    #Addresses
        //$from_address="";
        //$from_name="";
        $reply_address=clean_user_input($from_address);
        //$reply_name=$from_name;
        $error_delivery_address=clean_user_input($from_address);
        //$error_delivery_name=$from_name;
        //$to_name="Manuel Lemos";
        //$to_address="mlemos@acm.org";

    #Instantiate the email object
        $email_message=new email_message_class;
        $email_message->SetEncodedEmailHeader("To",clean_user_input($to_address),NULL); //,$to_name
        $email_message->SetEncodedEmailHeader("From",clean_user_input($from_address),NULL); //,$from_name
        $email_message->SetEncodedEmailHeader("Reply-To",clean_user_input($reply_address),NULL); //,$reply_name
        $email_message->SetHeader("Sender",clean_user_input($from_address));
    
    /*
     *  Set the Return-Path header to define the envelope sender address to which bounced messages are delivered.
     *  If you are using Windows, you need to use the smtp_message_class to set the return-path address.
     */
        if(defined("PHP_OS")
        && strcmp(substr(PHP_OS,0,3),"WIN"))
            //$email_message->SetHeader("Return-Path",$error_delivery_address);
    
    #SUBJECT, MESSAGE(s)
        $email_message->SetEncodedHeader("Subject",clean_user_input($subject));
        $email_message->AddQuotedPrintableTextPart($email_message->WrapText( clean_user_input($text_message) ));
        
    #SEND
        $error=$email_message->Send();
        if(strcmp($error,""))
            $response_message = "Error: $error\n";
            
    return $response_message;
}
if ( !function_exists('kst_clean_user_input') ) {
    function kst_clean_user_input($string) {
        return $string;
    }
}

