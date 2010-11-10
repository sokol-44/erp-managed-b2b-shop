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

   function prune_trail() {
      if( count($this->trails) > 128 ) array_shift($this->trails);
   }

   function add_title( $title ) {
      $this->trails[(count($this->trails)-1)]['PAGE_TITLE'] = $title;
   }

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
            if( !$going_back  ) {
               $this->trails[] = array('GET' => $F->GET , 'POST' => $F->POST, 'com' => $F->com,
         		'PERSON_LOGGED_IN' => $P->logged_in, 'PAGE_TITLE' => '');
               $this->prune_trail();
            }
         }
      }

   }

}


?>