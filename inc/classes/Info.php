<?php
/**
 * Data.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();


class Info {
   static $class;
   public $messages;
   
   public function __construct() {
      self::$class = $this;
      $this->fill();
   }

   static function g_global() {
      if( !is_object(self::$class) ) {
         self::$class = new Info;
      }
      return self::$class;
   }
   
   static function g( $method, $args = array()) {
      if( !is_object(self::$class) ) {
         self::$class = new Info;
      }
      if(method_exists(self::$class, $method)) {
         return call_user_func_array(array(self::$class, $method), $args);
      } else {
         return false;
         //throw new Exception(sprintf('The required method "%s" does not exist for %s', $method, get_class($this)));
      }
   }


   public function __wakeup() {
      self::$class = $this;
      $this->fill();
   }
   
   public function __destruct() {
   }

   public function reset() {
      $this->messages = array(
         'error' => array(),
         'warning' => array(),
         'success' => array(),
         'other' => array(),
      );
   }
   
   public function fill() {
      if( !isset($this->messages) || !is_array($this->messages) )
         $this->reset();
   }

   static function sadd($message, $type = 'error') {
      if( $type != 'error' && $type != 'warning' && $type != 'success')
         $type = 'other';
      echo 'INFO sadd';
      $Info = Info::g_global();
      $Info->add($message, $type);
   }
    
   public function add($message, $type = 'error') {
      if( $type != 'error' && $type != 'warning' && $type != 'success')
      $type = 'other';

      $this->messages[$type][] = array('text' => $message);
   }

   public function output($show_type = '') {
      $output = array();

      if( Framework::is_null($show_type) ) $all = true;
      else $all = false;

      foreach ($this->messages as $type => $message_type_array) {
         if ($all || $type == $show_type) {
            foreach( $message_type_array as $message ) {
               $text = Framework::output_string( $message['text'] );
               switch( $type ) {
                  case 'error':
                     $output[] = '<div class="messageInfo messageInfoError">' . $text . '</div>';
                     break;
                  case 'warning':
                     $output[] = '<div class="messageInfo messageInfoWarning">' . $text . '</div>';
                     break;
                  case 'success':
                     $output[] = '<div class="messageInfo messageInfoSuccess">' . $text . '</div>';
                     break;
                  default:
                     $output[] = '<div class="messageInfo messageInfoOther">' . $text . '</div>';
                     break;
               }
            }
            $this->messages[$type] = array();
         }
      }

      return implode(NL, $output);
   }
}
?>