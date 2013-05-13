<?php
/**
 * Data.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();


class Article {
   static $class;
   public $param = array();
   
   public function __construct($key = false) {
      self::$class = $this;
      
      $this->load_article($key);
   }
   
   private function load_article($key) {
      
      $this->param = Data::get_article($key);
      return $this->param;
   }

   static function g_global() {
      if( !is_object(self::$class) ) {
         self::$class = new Article; //get_called_class()
      }
      return self::$class;
   }
   
   static function g( $method, $args = array()) {
      if( !is_object(self::$class) ) {
         self::$class = new Article;
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
   }
}
?>