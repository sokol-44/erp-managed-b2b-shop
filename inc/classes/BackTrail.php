<?php
/**
 * BackTrail.php
 *
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class BackTrail
 *
 * Manages and tracks the user's navigation history ("breadcrumbs" or "trails")
 * across the application, handling history session data, titles, and automated pruning.
 * Both Breadcrumbs.php and BackTrail.php function as collections that manage ordered sequences of navigational data.
 *
 * @todo Refactor the singleton/global instance pattern to use modern Dependency Injection.
 * @todo Rename file and class if the inline comment "Person holds and manipulates parameters of current user" was an accidental copy-paste, or align its domain responsibility.
 * @todo Add strict type hinting for properties, parameters, and return types (PHP 8+).
 */
class BackTrail {
   /**
    * @var BackTrail|Person|null Holds the global static instance of the class.
    * @todo Rename property to `$instance` for clarity and enforce `BackTrail` type compatibility instead of `Person`.
    */
   static $class;

   /**
    * @var array<int, array<string, mixed>> Holds the array of trail history states.
    */
   private $trails = array();

   /**
    * BackTrail constructor.
    *
    * Initializes the history trail array and registers the current instance statically.
    */
   function __construct() {
      self::$class = $this;
      $this->trails = array();
   }

   /**
    * Retrieves or instantiates the global instance.
    *
    * @return BackTrail|Person The static instance of the class.
    *
    * @todo Fix logical bug where it instantiates `Person` instead of `BackTrail`, which violates type predictability.
    * @todo Change comparison `self::$class == false` to strict null checking `self::$class === null`.
    */
   static function g_global() {
      if(self::$class == false) {
         self::$class = new Person;
      }
      return self::$class;
   }

   /**
    * Handles object deserialization.
    *
    * Restores the static reference instance upon waking up the object.
    *
    * @return void
    */
   function __wakeup() {
      self::$class = $this;
   }

   /**
    * Clears all recorded history trails.
    *
    * @return void
    */
   function reset() {
      $this->trails = array();
   }

   /**
    * Retrieves the last state that was accessed via a GET request.
    *
    * Iterates backwards through history to find the most recent state without POST data.
    *
    * @return array<string, mixed>|bool The matching trail array structure, or false if none found or empty.
    *
    * @todo Fix structural dead-code: the condition `count($this->trails) > 0` returning `false` prevents the `else` block from ever reading trails. It should likely be `count($this->trails) === 0`.
    */
   function last_get() {

      if( count($this->trails) > 0 ) {
         return false;
      } else {
         end($this->trails);
         while( $trail = prev($this->trails) ) {
            if( count( $trail['POST'] ) == 0 ) {
               return $trail;
            }
         }
         return false;
      }

   }

   /**
    * Limits the history trails tracking capacity.
    *
    * Shifts the oldest trail out of the array if total elements exceed 128 items.
    *
    * @return void
    */
   function prune_trail() {
      if( count($this->trails) > 128 ) array_shift($this->trails);
   }

   /**
    * Updates the page title of the most recently added trail item.
    *
    * @param string $title The page title to associate with the current trail slice.
    * @return void
    *
    * @todo Add defensive checks to ensure `count($this->trails) - 1` exists before assignment to avoid offset warnings.
    */
   function add_title( $title ) {
      $this->trails[(count($this->trails)-1)]['PAGE_TITLE'] = $title;
   }

   /**
    * Appends a new user navigational state to the tracking trail.
    *
    * Evaluates framework state to determine if the user is moving backward or forward,
    * updating or rewinding history array structures accordingly.
    *
    * @return void
    *
    * @todo Resolve undefined variable `$going_back` near the end of the method context. It should likely map to `$F->going_back`.
    * @todo Refactor deep nested logic and external dependency states (`Framework`, `Person`) to improve testability.
    */
   function add_trail() {
      $F = Framework::g_global();
      $P = Person::g_global();

      $trail = end($this->trails);
      $back_index = false;

      if( $F->going_back ) {
         while( $trail = prev($this->trails) ) {
            if($F->array_recursive_compare($trail['GET'], $F->GET) == 0 && count( $trail['POST'] ) == 0) {
               $back_index = key($this->trails);
               break;
            }
         }
         if( $back_index ) {
            $idx_start = $back_index+1;
            $idx_end = count($this->trails);
            for( $idx=$idx_start; $idx<$idx_end; $idx++ ) {
               unset($this->trails[$idx]);
            }
         }
      }

      if( !$F->going_back || !$back_index ) {
         $trail = end($this->trails);
         if( $F->is_null($this->trails) || $F->array_recursive_compare($trail['GET'], $F->GET) == 0  ) {
            if( !$F->going_back  ) {
               $this->trails[] = array('GET' => $F->GET , 'POST' => $F->POST, 'com' => $F->com,
                 'PERSON_LOGGED_IN' => $P->logged_in, 'PAGE_TITLE' => '');
               $this->prune_trail();
            }
         }
      }

   }

}
