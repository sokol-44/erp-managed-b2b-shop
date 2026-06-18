<?php
/**
 * Price.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Price
 *
 * Data class going to provide data and operation on them.
 * Application will get and save any data thru it.
 *
 * @designPattern Singleton
 *
 * @todo Add visibility modifiers (public, protected, private) to all properties and methods.
 * @todo Enable strict types (declare(strict_types=1)) to ensure type safety.
 * @todo Add type hinting for all method parameters and return types.
 * @todo Replace the custom singleton pattern with a modern dependency injection container.
 */
class Price {
   /**
    * @var Price|bool The singleton instance of the Price class.
    */
   static $class = false;

   /**
    * @var string The currency code.
    */
   static $currency_code = 'pl';

   /**
    * @var string The currency symbol or string suffix.
    */
   static $currency_str = ' zł';

   /**
    * @var string The decimal separator for currency formatting.
    */
   static $currency_decimal = ',';

   /**
    * Price constructor.
    *
    * Assigns the current instance to the static class property.
    * @return void
    */
   function __construct() {

         self::$class = $this;
   }

   /**
    * Gets the global instance of the Price class, creating it if it does not exist.
    *
    * @return Price The global Price instance.
    */
   static function g_global() {
      if(self::$class == false) {
         self::$class = new Price();
      }
      return self::$class;
   }

   /**
    * Calculates the total price including VAT for a given quantity.
    *
    * @param float|int $price The unit price.
    * @param float|int $vat The VAT percentage.
    * @param int $quantity The quantity of items. Defaults to 1.
    * @return float|int The total price including VAT.
    */
   static function add_vat($price, $vat, $quantity = 1) {
         return ($price + self::calculate_tax($price, $vat)) * $quantity;
   }

   /**
    * Formats a number as a currency string.
    *
    * @param float|int $number The number to format.
    * @return string The formatted currency string.
    */
   static function val( $number ) {
      return number_format((float)$number, 2, self::$currency_decimal, ' ') . self::$currency_str;
   }

   /**
    * Rounds the tax value to two decimal places.
    *
    * @param float|int $number The tax value to round.
    * @return float The rounded tax value.
    *
    * @todo Rename method from rount_tax to round_tax to fix the typo.
    */
   static function rount_tax( $number ) {
      return round( $number, 2);
   }

   /**
    * Formats a number as a tax percentage string.
    *
    * @param float|int $number The tax rate.
    * @return string The formatted tax percentage string.
    */
   static function tax ($number) {
     return number_format((float)$number, 0, self::$currency_decimal, ' ') . '%';
   }

   /**
    * Calculates the tax amount for a given price and tax rate.
    *
    * @param float|int $price The base price.
    * @param float|int $tax The tax rate percentage.
    * @return float|int The calculated tax amount.
    */
   static function calculate_tax($price, $tax) {
      return $price * ($tax / 100);
   }
}
