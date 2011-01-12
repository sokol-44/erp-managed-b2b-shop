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
   static $class;
   private $_template_dir = '';
   private $_template = array();
   private $_template_name = array();

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

   private function _load_template() {
      //Template
      $template_name = $GLOBALS['config']['TEMPLATES']['mail'];
      $template_dir = DIR_INC_TEMPLATES . DS . $template_name . DS;
      $template =  $template_dir . $template_name . '.php';
      $this->_template_dir = $template_dir;
      if( $template != '' && is_file($template) ) {
         include_once($template);
         $this->_template = $template;
      } else {
         $this->_template['MAIN'] = array('body' => '#text#', 'type' => 'text', 'img' => array());
      }
      $this->_template_name = 'MAIN';
   }

   private function _init() {
      $config_mail = $GLOBALS['config']['MAIL'];

      $this->From = $config_mail['main_from_address'];
      $this->FromName = $config_mail['main_from_name'];
      $this->Subject_Begin =  $config_mail['main_from_subject'];
      $this->CharSet = 'UTF-8';

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

   public function set_template( $name ) {
      if( Framework::not_null( $_template[$name] ) ) $this->_template_name = $name;
   }

   public function Send() {

      $template = $this->_template[$this->_template_name];

      if( $template['type'] == 'html' ) {
         $this->IsHTML(true);
         $this->AltBody = strip_tags(  $this->Body );
         $this->Body = str_replace('#text#', $this->Body, $template['body']);
         
         foreach($template['img'] as $img) {
             $this->AddEmbeddedImage($this->_template_dir . DS . $img[0], $img[1], $img[2]);
         }
      } else {
         $this->IsHTML(false);
      }
      return parent::Send();
   }

   public function SendAddSubject() {
      $this->Subject = $this->Subject_Begin . $this->Subject;
      return $this->Send();
   }


}

?>