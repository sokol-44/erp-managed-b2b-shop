<?php
/**
 * Mail.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 * @uses PHPMailer (Lite) http://phpmailer.worxware.com/ (LGPL)
 */

if( !defined('_I_INIT') ) die();

/**
 * Send email from system
 */

require('PHPMailer' . DS . 'class.phpmailer-lite.php');

class Mail extends PHPMailerLite {
   public $Subject_Begin         = '';

   function __construct() {
      self::$class = $this;
      parent::__construct();
      $this->_init();
      $this->_load_template();
   }

   static function g_global() {
      if(self::$class == false) {
         self::$class = new Price();
      }
      return self::$class;
   }

   function _load_template() {
      //Template
      $template_name = $GLOBALS['config']['TEMPLATES']['mail'];
      $template_dir = DIR_INC_TEMPLATES . DS . $template_name . DS;
      $template =  $template_dir . $template_name . '.php';
      if( $template != '' && is_file($template) ) {
         include_once($template);
      }
      
   }
   
   function _init() {
      $config_mail = $GLOBALS['config']['MAIL'];

      $this->From = $config_mail['main_from_address'];
      $this->FromName = $config_mail['main_from_name'];
      $this->$Subject_Begin =  $config_mail['main_from_subject'];

      switch ($config_mail['send_method']) {
         case 'sendmail':
            $this->IsSendmail();
            if ( isset($config_mail['send_method_sendmail_path']) ) {
               $this->Sendmail = $config_mail['send_method_sendmail_path'];
            }
         default:
         case 'mail':
            $this->IsMail();
            break;
      }
   }
    
   function SendAddSubject() {
      $this->Subject = $this->Subject_Begin . $this->Subject;
      $this->Send();
   }
    

}

?>