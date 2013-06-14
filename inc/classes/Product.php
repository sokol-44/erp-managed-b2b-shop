<?php
/**
 * Product.php Helper file for products
 * Copyright Michał Sokołowski 2013
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();


class Product {
   static $class;
   
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

   public function __wakeup() {
      self::$class = $this;
   }
   
   public function __destruct() {
   }
   
   static function get_search_product_list($filters) {
      
   }

}
?>