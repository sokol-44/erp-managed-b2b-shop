<?php
/**
 * Breadcrumbs.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Breadcrumbs
 *
 * Manages the application's breadcrumb navigation trail using a singleton-like pattern.
 * Holds an array of trail steps containing names and hypermedia links.
 * Both Breadcrumbs.php and BackTrail.php function as collections that manage ordered sequences of navigational data.
 *
 * @designPattern: Singleton
 *
 * @todo Define explicit visibility modifiers (`public`, `protected`, `private`) for all properties and methods.
 * @todo Resolve structural bug: change `static $crumb` to an instance property (`protected array $crumb`) since it is consistently accessed via `$this->crumb`.
 * @todo Refactor global state management: replace custom singleton logic with proper Dependency Injection (DI) or a standard PSR-11 Container container wrapper.
 */
class Breadcrumbs {
   /**
    * Holds the singleton instance of the Breadcrumbs class.
    *
    * @var \Breadcrumbs|bool
    *
    * @todo Refactor to type hint `?Breadcrumbs` (nullable) instead of initializing with boolean `false`.
    */
   static $class = false;

   /**
    * List of current breadcrumb items.
    *
    * @var array<int, array{name: string, path: string}>
    *
    * @todo Convert this property to a standard non-static array to align with `$this->crumb` instance calls.
    */
   static $crumb = array();

   /**
    * Breadcrumbs constructor.
    *
    * Initializes the breadcrumb trail with the root category and binds the static instance reference.
    *
    * @return void
    *
    * @todo Add explicit `public` visibility modifier.
    */
   function __construct() {
      $this->reset();
      self::$class = $this;
   }

   /**
    * Breadcrumbs wakeup magic method.
    *
    * Restores the internal static instance reference when the object is unserialized.
    *
    * @return void
    *
    * @todo Add explicit `public` visibility modifier.
    */
   function __wakeup() {
      self::$class = $this;
   }

   /**
    * Gets the global singleton instance of the Breadcrumbs class.
    *
    * Instantiates the class if it has not yet been defined in the current runtime context.
    *
    * @return \Breadcrumbs The singleton instance.
    *
    * @todo Add explicit `public` visibility modifier and native `: self` return type hint.
    */
   static function g_global() {
      if( !is_object(self::$class) ) {
         self::$class = new Breadcrumbs;
      }
      return self::$class;
   }

   /**
    * Resets the breadcrumbs trail back to the default top-level category.
    *
    * @return void
    *
    * @todo Add explicit `public` visibility modifier and native `: void` return type hint.
    * @todo Remove tight coupling to global classes `Framework` and `Lang` via Dependency Injection.
    * @todo Change loading configuration from global defines to specialized class.
    */
   function reset() {
      $F = Framework::g_global();
      $this->crumb = array( array('name' => Lang::_('TOP_CATEGORY'),
       'path' => $F->make_link(CFG_COM_CATALOG) ) );
   }

   /**
    * Retrieves the current list of accumulated breadcrumbs.
    *
    * @return array<int, array{name: string, path: string}> List of breadcrumb links.
    *
    * @todo Add explicit `public` visibility modifier and native `: array` return type hint.
    */
   function get_list() {
      return $this->crumb;
   }

   /**
    * Adds a new crumb node to the current breadcrumbs trail.
    *
    * Accepts either a separate name string and a path string, or a unified associative array item.
    *
    * @param string|array<string, string> $in_crumb The string name of the crumb or an associative array with keys 'name' and 'path'.
    * @param string|bool $path The URL path for the crumb string, or false if not applicable. Defaults to false.
    * @return void
    *
    * @todo Add explicit `public` visibility modifier, native parameter type union/hints, and a `: void` return type.
    * @todo Clean up and remove the redundant, empty `else` block to enhance code readability.
    */
   function add_crumb( $in_crumb, $path = false ) {
      if( $path && !is_array($in_crumb)) {
         $this->crumb[] = array('name' => $in_crumb, 'path' => $path );
      } elseif( is_array($in_crumb) ) {
         $this->crumb[] = $in_crumb;
      } else {
      ;
      }
   }

}
