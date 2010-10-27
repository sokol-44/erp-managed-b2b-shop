<?php
/**
 * Tax.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/**
 * Data class goint to provide data and operation on them
 * Application will get and save any data thru it
 */

class Tax {
   
   static function add_vat($price, $vat, $quantity = 1) {
      echo "VAT: $price, $vat, $quantity";
         return ($price + self::calculate_tax($price, $vat)) * $quantity;
   }
   
   static function calculate_tax($price, $tax) {
      return $price * ($tax / 100);
   }
}

?>