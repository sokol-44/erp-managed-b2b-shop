<?php
/**
 * Shop.php Helper for shop
 * Copyright Michał Sokołowski 2013
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();


class Shop {
   static $class;
   public $attributes;
   
   public function __construct() {
      self::$class = $this;
   }

   static function g_global() {
      if( !is_object(self::$class) ) {
         self::$class = new Shop;
      }
      return self::$class;
   }

   public function __wakeup() {
      self::$class = $this;
   }
   
   
   static function get_shop_attribute( $name ) {
   	$Shop = Shop::g_global();
   	$F = Framework::g_global();
   	
   	if( $F->is_null($Shop->attributes) ) {
   		$Shop->attributes = Data::get_shop_attributes();
   	} 
   	
   	if( isset( $Shop->attributes[$name] ) ) return  $Shop->attributes[$name];
   	
   	return false; 
   }
}
?>