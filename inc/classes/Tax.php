<?php
/**
 * Tax.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Tax
 *
 * Data class going to provide data and operations on them.
 * Application will get and save any data through it.
 *
 * @designPattern Service / Manager
 *
 * @todo Add declare(strict_types=1); at the top of the file to enforce strict typing.
 * @todo Add explicit visibility modifiers (e.g., public) to all methods.
 * @todo Implement strict type hinting for method parameters and return types (e.g., float, int).
 * @todo Consider using BCMath or a Money pattern library to prevent floating-point precision issues.
 */
class Tax {

   /**
    * Calculates the total price including VAT for a given quantity.
    *
    * @param float|int $price    The base price of the item.
    * @param float|int $vat      The VAT percentage (e.g., 23 for 23%).
    * @param int       $quantity The quantity of items. Defaults to 1.
    *
    * @return float|int The total price including VAT for the specified quantity.
    *
    * @todo Add public visibility modifier.
    * @todo Add type hints: public static function add_vat(float $price, float $vat, int $quantity = 1): float.
    */
   static function add_vat($price, $vat, $quantity = 1) {
         return ($price + self::calculate_tax($price, $vat)) * $quantity;
   }

   /**
    * Calculates the tax amount for a given price and tax rate.
    *
    * @param float|int $price The base price.
    * @param float|int $tax   The tax percentage (e.g., 23 for 23%).
    *
    * @return float|int The calculated tax amount.
    *
    * @todo Add public visibility modifier.
    * @todo Add type hints: public static function calculate_tax(float $price, float $tax): float.
    */
   static function calculate_tax($price, $tax) {
      return $price * ($tax / 100);
   }
}
