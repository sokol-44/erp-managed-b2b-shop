<?php
/**
 * Price.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/**
 * Data class goint to provide data and operation on them
 * Application will get and save any data thru it
 */

class Price {
   static $class = false;
   static $currency_code = 'pl';
   static $currency_str = ' Zł';
   static $currency_decimal = ',';
   
   function __construct() {
      
         self::$class = $this;
   }

   static function g_global() {
      if(self::$class == false) {
         self::$class = new Price();
      }
      return self::$class;
   }
   
   static function add_vat($price, $vat, $quantity = 1) {
         return ($price + self::calculate_tax($price, $vat)) * $quantity;
   }
   
   static function val( $number ) {
      return number_format((float)$number, 2, $currency_decimal, ' ') . self::$currency_str;
   }
   
   static function rount_tax( $number ) {
      return round( $number, 2);
   }
   
   static function tax ($number) {
     return number_format((float)$number, 0, $currency_decimal, ' ') . '%';
   }
   
   static function calculate_tax($price, $tax) {
      return $price * ($tax / 100);
   }
}

?>