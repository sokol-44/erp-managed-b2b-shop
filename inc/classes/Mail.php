<?php
/**
 * Mail.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 * @uses PHPMailer (Lite) http://phpmailer.worxware.com/ (LGPL)
 */

if( !defined('_I_INIT') ) die();

/**
 * Send email from system
 */

require('PHPMailer' . DS . 'class.phpmailer.php');
require('PHPMailer' . DS . 'class.smtp.php');

/**
 * Class Mail
 *
 * Custom Mail class extending PHPMailer to handle system emails, templates, and configuration.
 *
 * @designPattern Factory + Adapter
 *
 * @todo Refactor to use modern dependency injection instead of global $GLOBALS['config'].
 * @todo Use PSR-4 autoloading instead of manual require statements.
 * @todo Update PHPMailer to a modern version via Composer.
 * @todo Add strict type declarations (declare(strict_types=1)) and type hinting for properties and methods.
 */
class Mail extends PHPMailer {
   /**
    * @var string Prefix for the email subject.
    */
   public $Subject_Begin         = '';

   /**
    * @var string Directory path of the email templates.
    */
   private $_template_dir = '';

   /**
    * @var array Array containing template configurations.
    */
   private $_template = array();

   /**
    * @var string Name of the currently active template.
    */
   private $_template_name = array();

   /**
    * Mail constructor.
    *
    * Initializes PHPMailer, loads configuration, and templates.
    *
    * @return void
    * @todo Call parent constructor with exception handling if needed.
    */
   function __construct() {
      parent::__construct();

      $this->_init();
      $this->_load_template();
   }

   /**
    * Loads the email template configuration from the global config and template files.
    *
    * @return void
    *
    * @todo Avoid using $GLOBALS. Implement a template engine or a dedicated template loader class.
    * @todo Replace constant DIR_INC_TEMPLATES and DS with modern path helpers.
    */
   private function _load_template() {
      //Template
      $template_name = $GLOBALS['config']['TEMPLATES']['mail'];
      $template_dir = DIR_INC_TEMPLATES . DS . $template_name . DS;
      $template_file =  $template_dir . $template_name . '.php';
      $this->_template_dir = $template_dir;
      if( $template_file != '' && is_file($template_file) ) {
         include($template_file);
         $this->_template = $template;
      } else {
         $this->_template['MAIN'] = array('body' => '#text#', 'type' => 'text', 'img' => array());
      }
      $this->_template_name = 'MAIN';
   }

   /**
    * Initializes PHPMailer settings based on the global configuration.
    *
    * @return void
    *
    * @todo Avoid using $GLOBALS. Use a configuration manager.
    * @todo Fix typo 'send_method_smpt_host' (should probably be 'smtp').
    * @todo Use modern SMTP security options and secure credential storage.
    */
   private function _init() {
      $config_mail = $GLOBALS['config']['MAIL'];

      $this->From = $config_mail['main_from_address'];
      $this->FromName = $config_mail['main_from_name'];
      $this->Subject_Begin =  $config_mail['main_from_subject'];
      $this->CharSet = 'UTF-8';

      switch ($config_mail['send_method']) {
         case 'smtp':
            if ( isset($config_mail['send_method_smpt_host']) ) {
                $this->isSMTP();
                $this->SMTPAuth = true;
                $this->SMTPKeepAlive = true;
                $this->Host = $config_mail['send_method_smpt_host'];
                $this->Username = $config_mail['send_method_smpt_username'];
                $this->Password = $config_mail['send_method_smpt_password'];
                $pos = strpos($this->Host, ':');
                if( $pos > 0 ) $this->Hostname = substr($this->Host, 0, $pos);
                else $this->Hostname = $this->Host;
//                 print_debug( $config_mail);
//                 $this->SMTPDebug = 2;
//                 $this->Debugoutput = 'html';
                if ( isset($config_mail['send_method_smpt_secure']) &&
                    ( $config_mail['send_method_smpt_secure'] == 'ssl' || $config_mail['send_method_smpt_secure'] == 'tls' ) ) {
                        $this->SMTPSecure = $config_mail['send_method_smpt_secure'];
                }
            }
            break;
         case 'sendmail':
            $this->IsSendmail();
            if ( isset($config_mail['send_method_sendmail_path']) ) {
               $this->Sendmail = $config_mail['send_method_sendmail_path'];
            }
            break;
         case 'mail':
         default:
            $this->IsMail();
            break;
      }
   }

   /**
    * Sets the active template name.
    *
    * @param string $name The name of the template to set.
    * @return void
    *
    * @todo Add type hinting for the parameter.
    */
   public function set_template( $name ) {
      if( Framework::not_null($this->_template[$name] ) ) $this->_template_name = $name;
   }

   /**
    * Converts plain text body to HTML by replacing newlines with <br> tags if no HTML tags are present.
    *
    * @param string $body The email body text.
    * @return string The formatted body text.
    *
    * @todo Use a more robust HTML detection or a proper Markdown/HTML converter.
    * @todo Add string type hinting for parameter and return type.
    */
   private function _body_htmlizer( $body ) {

      if( stripos($body, '<br>') ) {
         return $body;
      } else {
         return nl2br($body);
      }
   }

   /**
    * Sends the email, applying the selected template and embedding images if applicable.
    *
    * @return bool True on success, false otherwise.
    * @throws phpmailerException Thrown when PHPMailer encounters a transmission error.
    *
    * @todo Add return type declaration.
    * @todo Handle exceptions properly instead of just returning parent::Send().
    */
   public function Send() {

      $template = $this->_template[$this->_template_name];
      if( $template['type'] == 'html' ) {
         $this->IsHTML(true);
         $this->AltBody = strip_tags( $this->Body );
         $this->Body = self::_body_htmlizer($this->Body);

         $this->Body = str_replace('#text#', $this->Body, $template['body']);

         foreach($template['img'] as $img) {
             $this->AddEmbeddedImage($this->_template_dir . DS . $img[0], $img[1], $img[2]);
         }
      } else {
         $this->IsHTML(false);
      }
      return parent::Send();
   }

   /**
    * Prepends the subject prefix to the subject and sends the email.
    *
    * @return bool True on success, false otherwise.
    * @throws phpmailerException Thrown when PHPMailer encounters a transmission error.
    *
    * @todo Add return type declaration.
    */
   public function SendAddSubject() {
      if( Framework::not_null($this->Subject) ) $this->Subject = $this->Subject_Begin . ' - ' . $this->Subject;
      else $this->Subject = $this->Subject_Begin;

      return $this->Send();
   }

}
