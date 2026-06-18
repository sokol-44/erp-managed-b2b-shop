<?php
/**
 * Shop.php Helper for shop
 * Copyright Michał Sokołowski 2013
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Shop
 *
 * Helper class for managing shop attributes and maintaining a global instance.
 *
 * @designPattern Singleton
 *
 * @todo Add declare(strict_types=1); at the top of the file.
 * @todo Use Dependency Injection instead of static global accessors.
 * @todo Rename the class and methods to follow PSR-12 coding standards (e.g., camelCase).
 * @todo Implement a standard Singleton pattern if global access is strictly necessary.
 */
class Shop {
   /**
    * @var Shop|null The global instance of the Shop class.
    */
   static $class;

   /**
    * @var array|null List of shop attributes.
    */
   public $attributes;

   /**
    * Shop constructor.
    *
    * Initializes the Shop instance and registers it as the global instance.
    *
    * @return void
    *
    * @todo Avoid assigning $this to a static property inside the constructor.
    */
   public function __construct() {
      self::$class = $this;
   }

   /**
    * Retrieves the global instance of the Shop class.
    *
    * If the instance does not exist, it instantiates a new Shop object.
    *
    * @return Shop The global Shop instance.
    *
    * @todo Add public visibility modifier.
    * @todo Rename to getInstance() to follow standard naming conventions.
    * @todo Add return type hint (: self).
    */
   static function g_global() {
      if( !is_object(self::$class) ) {
         self::$class = new Shop;
      }
      return self::$class;
   }

   /**
    * Magic method called during unserialization.
    *
    * Restores the global instance reference and resets the attributes array.
    *
    * @return void
    */
   public function __wakeup() {
      self::$class = $this;
      $this->attributes = array();
   }

   /**
    * Retrieves a specific shop attribute by name.
    *
    * Loads attributes from the database/data source if they are not already loaded.
    *
    * @param string $name The name of the attribute to retrieve.
    * @return mixed The attribute value if found, or false if not found.
    *
    * @todo Add public visibility modifier.
    * @todo Add type hinting for the $name parameter (string) and return type (mixed).
    * @todo Refactor to avoid tight coupling with Framework and Data classes.
    */
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
