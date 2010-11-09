<?php
/**
 * BackTrail.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/**
 * Person holds and manipulates parameters of current user
 */

class BackTrail {
   static $class;
   private $trails = array();

   function __construct() {
      self::$class = $this;
      $this->trails = array();
   }

   static function g_global() {
      if(self::$class == false) {
         self::$class = new Person;
      }
      return self::$class;
   }

   function __wakeup() {
      self::$class = $this;
   }
   
   function reset() {
       $this->trails = array();
   }
   
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
   
   function prune() {
      if( count($this->trails) > 128 ) array_shift($this->trails);
   }
   
   function add_trail() {
      $F = Framework::g_global();
      $P = Person::g_global();
      
      $trail = end($this->trails);
      
      if( $trail['GET']  )
      
      $this->trails[] = array('GET' => $F->GET , 'POST' => $F->POST, 'com' => $F->com,
      'PERSON_LOGGED_IN' => $P->logged_in);
      $this->prune();
   }

}














?>