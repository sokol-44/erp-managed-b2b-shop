<?php
/**
 * Shopping_Basket_Chain.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Shopping_Basket_Chain
 *
 * Manages a chain of shopping baskets for clients, handling creation,
 * switching, locking, and merging of multiple baskets.
 *
 * @designPattern Singleton
 * @designPattern Proxy / Decorator
 *
 * @todo Refactor static properties to non-static instance properties where appropriate to improve testability.
 * @todo Add strict type declarations (declare(strict_types=1);) and type hints for parameters and return values.
 * @todo Replace global state dependencies (e.g., Framework::g_global(), Person::g_global()) with Dependency Injection.
 * @todo Rename typo variables like `$contants` to `$contents` across the codebase.
 */
class Shopping_Basket_Chain {
   /**
    * @var Shopping_Basket_Chain|bool Singleton instance of the class.
    */
   static $class = false;

   /**
    * @var int The client ID.
    */
   static $id_client = 0;

   /**
    * @var int The client user ID.
    */
   static $id_client_user = 0;

   /**
    * @var int The current active basket ID.
    */
   static $id_basket_current = 0;

   /**
    * @var bool Whether the basket ID has been explicitly set.
    */
   static $id_basket_set = false;

   /**
    * @var int The number of the shopping basket.
    */
   static $id_nr_shopping_basket = 0;

   /**
    * @var array Map of basket numbers to basket IDs.
    */
   static $nr2id = array();

   /**
    * @var Shopping_Basket[] List of shopping baskets in the chain.
    */
   static $Basket_List = array();

   /**
    * @var int The basket number.
    */
   static $nr_basket = 0;

   /**
    * @var int Maximum number of baskets allowed in the chain.
    */
   private static $max_basket = 12;

   /**
    * Shopping_Basket_Chain constructor.
    *
    * Initializes the basket chain and reloads data for the current user.
    *
    * @return void
    *
    * @todo Use dependency injection instead of relying on global state in _reload_data.
    */
   public function __construct() {
      self::$class = $this;
      $this->_reload_data();

   }

   /**
    * Returns the parameters of the current object instance for debugging.
    *
    * @return array<string, mixed> Array of object properties and sizes of lists.
    */
   public function return_params() {
       $params = get_object_vars($this);
       $params['Basket_List'] = 'size:'.sizeof($this->Basket_List);
       $params['nr2id'] = 'size:'.sizeof($this->nr2id);
       return $params;
   }

   /**
    * Reloads basket data from the database or session based on the current user's login status.
    *
    * @return void
    */
   private function _reload_data() {
      $F = Framework::g_global();
      $P = Person::g_global();

      $this->id_client = (int)$P->data['id_client'];
      $this->id_client_user = (int)$P->id;
      $this->Basket_List = array();

      //print_debug($this->return_params());
      //FIXME
      //for not logged users
      if( $P->logged_in ) {
         $Basket_List = Data::get_basket_chain_basket_list( $this->id_client );
         if( $F->not_null($Basket_List) ) {
            $this->set_basket_list($Basket_List);
            //$this->set_default_basket_by_date();
         } else {
            $this->init_basket();
         }
      } else {
         $this->init_basket();
      }
   }

   /**
    * Merges the contents of a specified basket into the current main basket.
    *
    * @param int $id_shopping_basket The ID of the shopping basket to add. Defaults to 0.
    * @return bool True on success, false on failure.
    *
    * @todo Add explicit return type hint.
    */
   public function add_to_mainbasket( $id_shopping_basket = 0) {

      if( $this->_check_valid_basket( $id_shopping_basket ) && $this->_check_valid_basket( $this->id_basket_current ) ) {

         $right_basket_add  = $this->Basket_List[ $id_shopping_basket ]->check_rights('MODIFY_CONTENTS');
         $right_basket_main = $this->Basket_List[ $this->id_basket_current ]->check_rights('MODIFY_CONTENTS');

         if( $right_basket_add && $right_basket_main ) {
            $status = $this->Basket_List[$this->id_basket_current]->add_from_basket($this->Basket_List[$id_shopping_basket]);
         } else {
            return false;
         }
         if( $status ) {
            $this->remove_basket($id_shopping_basket);
            unset( $this->Basket_List[$id_shopping_basket] );
            return $status;
         } else {
            return false;
         }

      } else {
         return false;
      }
   }

   /**
    * Adds a new basket to the chain if the maximum limit has not been reached.
    *
    * @param bool $force Force creation of a basket beyond the limit. Defaults to false.
    * @return bool True if a basket was successfully added, false otherwise.
    */
   public function add_basket( $force = false ) {
      //foreach($this->Basket_List as $basket ) print_debug($basket->params);
      //echo sizeof($this->Basket_List)." < ".self::$max_basket ."<br>\n";
      if( $this->get_can_add_basket() ) {
         for( $id_sb = 1; $id_sb <= self::$max_basket ; $id_sb++ ){
            if( !$this->_check_valid_basket($id_sb) ) {
               $this->init_basket( $id_sb );
               return true;
            }
         }
         return false;
      } elseif ( $force ) {
         $this->init_basket(self::$max_basket+1, true);
         return true;
      } else {
         return false;
      }
   }

   /**
    * Removes a specified basket from the chain.
    *
    * @param int $id_shopping_basket The ID of the basket to remove. Defaults to 0.
    * @return bool|mixed Result of the removal operation, or false on failure.
    */
   public function remove_basket( $id_shopping_basket = 0) {
      if( $this->_check_valid_basket($id_shopping_basket) &&
            $this->Basket_List[ $id_shopping_basket ]->check_rights('MODIFY_CONTENTS') )
      {
         $res = $this->Basket_List[$id_shopping_basket]->remove_basket();
         unset($this->Basket_List[$id_shopping_basket]);
         if( (int)$id_shopping_basket == (int)$this->id_basket_current ) {
            $this->id_basket_set = false;
            $this->set_default_basket_by_date();
         }
         return $res;
      } else {
         return false;
      }
   }

   /**
    * Checks if a basket ID is valid and exists in the current chain.
    *
    * @param int $id_shopping_basket The ID of the basket to check.
    * @return bool True if valid, false otherwise.
    */
   public function _check_valid_basket( $id_shopping_basket ) {
      if ($id_shopping_basket > 0 &&
            isset($this->Basket_List[$id_shopping_basket]) &&
            is_object($this->Basket_List[$id_shopping_basket]) ) {
         return true;
      } else {
         return false;
      }
   }

   /**
    * Checks if more baskets can be added to the chain based on the maximum limit.
    *
    * @return bool True if a basket can be added, false otherwise.
    */
   public function get_can_add_basket() {
      if( $this->get_basket_list_count() < self::$max_basket ) {
         return true;
      }
      else {
          return false;
      }
   }

   /**
    * Gets the count of active baskets (excluding ordered ones) in the chain.
    *
    * @return int The number of active baskets.
    *
    * @todo Optimize count calculation and remove hardcoded 'ORDER' state check if possible.
    */
   public function get_basket_list_count() {
      //FIXME
      //params to get list of baskets belonging to specific ID
      $count=0;
      foreach($this->Basket_List as $Basket ) {
         if( $Basket->params['state'] != 'ORDER' ) $count++;
      }
      return $count;
   }

   /**
    * Initializes a new shopping basket.
    *
    * @param int $number The basket number. Defaults to 1.
    * @param bool $create Whether to create/save the basket. Defaults to true.
    * @param mixed $contants Initial contents for the basket (typo in original variable name). Defaults to false.
    * @return void
    *
    * @todo Rename $contants parameter to $contents to fix the typo.
    */
   public function init_basket( $number = 1, $create = true, $contants = false ) {
      $P = Person::g_global();
      $params = array(
            'id_client' => $this->id_client, 'id_shopping_basket' => 0, 'id_shopping_basket_version' => 0,
            'id_nr_shopping_basket' => $number, 'description' => '',
            'date_create' => date('Y-m-d H:i:s'), 'date_modified' => '', 'ts_create' => time(), 'ts_modified' => '',
            'using_id_client_user' => $this->id_client, 'using_session_id' => $P->session_id,
              'using_date' => date('Y-m-d H:i:s'),'ts_using' => time());
      $new_basket = new Shopping_Basket( $params, $create, $contants );
      $this->Basket_List[$new_basket->id_shopping_basket] = $new_basket;

      if( $create ) {
          if( $contants ) $this->id_basket_set = 1;
         else $this->id_basket_set = false;
         $this->id_basket_current = $new_basket->id_shopping_basket;
      }
   }

   /**
    * Removes all products from a specified basket.
    *
    * @param int $id_shopping_basket The ID of the basket to clean. Defaults to 0.
    * @return bool True on success, false on failure.
    */
   public function clean_basket( $id_shopping_basket = 0 ) {

      if( $this->_check_valid_basket($id_shopping_basket) ) {
         $this->Basket_List[ $id_shopping_basket ]->remove_all_product();
         return true;
      } else {
         return false;
      }
   }

   /**
    * Sets the list of baskets from an external array and switches to the working basket.
    *
    * @param array $Basket_List Array of basket data.
    * @return void
    */
   public function set_basket_list( $Basket_List ) {
      $ts_modified = 0;
      $id_nr_shopping_basket = 1;

      foreach( $Basket_List as $id_shopping_basket => $param) {
         $this->Basket_List[$id_shopping_basket] = new Shopping_Basket( $param );
         $this->nr2id[$id_nr_shopping_basket] = $id_shopping_basket;
         $id_nr_shopping_basket++;
      }
      $this->_switch_to_working_basket( );
   }

   /**
    * Switches the active basket to the current working basket if valid and accessible.
    *
    * @return bool True on success, or the result of setting the default basket by date.
    */
   public function _switch_to_working_basket(  ) {
      $ts_using = 0;
      $id_shopping_basket = 0;
      //print_debug($this->return_params());

      if( $this->id_basket_current > 0 && $this->_check_valid_basket($this->id_basket_current) &&
           $this->Basket_List[ $this->id_basket_current ]->check_rights('MODIFY_CONTENTS', false) &&
           $this->Basket_List[ $this->id_basket_current ]->currently_other_using() === FALSE &&
           $this->Basket_List[ $this->id_basket_current ]->currently_other_locked() === FALSE ) {

          if( $this->Basket_List[ $this->id_basket_current ]->basket_level_text() == 'FREE' )
              $this->Basket_List[ $this->id_basket_current ]->state_using_get();
          return true;
      } else {
          return $this->set_default_basket_by_date();
      }
   }

   /**
    * Locks a specified basket.
    *
    * @param int $id_shopping_basket The ID of the basket to lock. Defaults to 0.
    * @return bool True on success, false on failure.
    */
   public function set_lock_basket( $id_shopping_basket = 0 ) {

      if( $this->_check_valid_basket($id_shopping_basket) &&
            $this->Basket_List[ $id_shopping_basket ]->check_rights('MODIFY_CONTENTS') &&
            $this->Basket_List[ $id_shopping_basket ]->check_rights('LOCK') ) {
         //$this->id_basket_set = true;
         $this->Basket_List[ $id_shopping_basket ]->state_lock_set();
         return true;
      } else {
         return false;
      }
   }

   /**
    * Unlocks a specified basket.
    *
    * @param int $id_shopping_basket The ID of the basket to unlock. Defaults to 0.
    * @return bool True on success, false on failure.
    */
   public function set_unlock_basket( $id_shopping_basket = 0 ) {

      if( $this->_check_valid_basket($id_shopping_basket) &&
            $this->Basket_List[ $id_shopping_basket ]->check_rights('MODIFY_CONTENTS') &&
            $this->Basket_List[ $id_shopping_basket ]->check_rights('UNLOCK') ) {
         //$this->id_basket_set = true;
         $this->Basket_List[ $id_shopping_basket ]->state_lock_unset( $this->id_basket_current );
         return true;
      } else {
         return false;
      }
   }

   /**
    * Creates a new basket populated with products from an existing order.
    *
    * @param object $Order The order object containing product list and source basket.
    * @return bool True on success, false on failure.
    *
    * @todo Type-hint the $Order parameter with a specific Order class/interface.
    */
   public function basket_from_order( $Order ) {

       if( is_object($Order) && is_array($Order->product_list) &&
        Framework::not_null($Order->source_basket->contents) && $this->get_can_add_basket() ) {
         for( $id_sb = 1; $id_sb <= self::$max_basket ; $id_sb++ ) {
            if( !$this->_check_valid_basket($id_sb) ) {
               $this->init_basket( $id_sb, true, $Order->source_basket->contents );
               return true;
            }
         }
         return false;
      }
      return false;
   }

   /**
    * Creates a new basket populated with products from a favorite basket.
    *
    * @param object $Shopping_Basket_Favorite The favorite basket object.
    * @return bool True on success, false on failure.
    *
    * @todo Type-hint the $Shopping_Basket_Favorite parameter with a specific class/interface.
    */
   public function basket_from_favorite_basket( $Shopping_Basket_Favorite ) {

       if( is_object($Shopping_Basket_Favorite) &&
           is_array($Shopping_Basket_Favorite->product_list) && $this->get_can_add_basket() ) {
         for( $id_sb = 1; $id_sb <= self::$max_basket ; $id_sb++ ) {
            if( !$this->_check_valid_basket($id_sb) ) {
               $this->init_basket( $id_sb, true, $Shopping_Basket_Favorite->product_list );
               return true;
            }
         }
         return false;
      }
      return false;
   }

   /**
    * Sets a specified basket as the default active basket.
    *
    * @param int $id_shopping_basket The ID of the basket to set as default. Defaults to 0.
    * @return bool True on success, false on failure.
    */
   public function set_default_basket( $id_shopping_basket = 0 ) {

      if( $this->_check_valid_basket($id_shopping_basket) &&
            $this->Basket_List[ $id_shopping_basket ]->check_rights('MODIFY_CONTENTS') ) {
         $this->id_basket_set = true;
         $this->_switch_default_basket( $id_shopping_basket );
         return true;
      } else {
         $this->id_basket_set = false;
         $this->set_default_basket_by_date();
         return false;
      }
   }

   /**
    * Internal helper to switch the active basket to the specified default basket.
    *
    * @param int $id_shopping_basket The ID of the basket to switch to.
    * @return void
    */
   private function _switch_default_basket( $id_shopping_basket ) {
      if( $this->_check_valid_basket($id_shopping_basket)  &&
          $this->Basket_List[ $id_shopping_basket ]->check_rights('MODIFY_CONTENTS') ) {

         if( $this->Basket_List[ $id_shopping_basket ]->basket_level_text() == 'FREE' )
                 $this->Basket_List[ $id_shopping_basket ]->state_using_clear();

          $this->id_basket_current = $id_shopping_basket;
      } else {
          self::$id_basket_current = 0;
          self::$id_basket_set = false;
          $this->add_basket( true );
      }
      $this->Basket_List[ $this->id_basket_current ]->state_using_get();
   }


   /**
    * Sets the default active basket based on the last usage date.
    *
    * @param bool $recurrence Flag to prevent infinite recurrence. Defaults to false.
    * @return void
    *
    * @todo Remove dead code.
    */
   public function set_default_basket_by_date( $recurrence = false  ) {
      $ts_using = 0;
      $ts_using_lock = 0;
      $id_shopping_basket = 0;
      $id_shopping_basket_lock = 0;
      $this->id_basket_set = false;
      $this->id_basket_current = 0;

      foreach ($this->Basket_List as $Basket ) {
//           print_debug( array(
//           $this->_check_valid_basket($Basket->id_shopping_basket)
//           ,$Basket->params['ts_using'] > $ts_using
//           ,$Basket->currently_other_using() === FALSE
//           ,$Basket->currently_other_locked() === FALSE
//           ), true);
          if( $this->_check_valid_basket($Basket->id_shopping_basket) &&
          $Basket->check_rights('MODIFY_CONTENTS', false) &&
          $Basket->currently_other_using() === FALSE &&
          $Basket->currently_other_locked() === FALSE ) {

              if( $Basket->basket_level_text() == 'LOCK' && $Basket->params['ts_using'] > $ts_using_lock) {
                  $ts_using_lock = $Basket->param['ts_using'];
                  $id_shopping_basket_lock = $Basket->id_shopping_basket;
              } elseif( $Basket->params['ts_using'] > $ts_using ) {
                  $ts_using = $Basket->param['ts_using'];
                  $id_shopping_basket = $Basket->id_shopping_basket;
              }
          }
      }

        //print_debug(compact('ts_using', 'ts_using_lock', 'id_shopping_basket', 'id_shopping_basket_lock'));
      //die();
      if( $id_shopping_basket_lock > 0 ) $id_shopping_basket = $id_shopping_basket_lock;

      if( $id_shopping_basket > 0 ) {
          $this->_switch_default_basket( $id_shopping_basket );
      } else {
          if( $recurrence ) die('$recurrence');
          if( $recurrence ) $this->add_basket( true );
          else $this->add_basket( );
          $this->set_default_basket_by_date( true );
      }
   }

   /**
    * Returns the default basket, ensuring the user has modify rights.
    *
    * @return Shopping_Basket|bool The default shopping basket object, or false on failure.
    */
   public function return_default_basket_modify() {

      if( $this->id_basket_current > 0  ) {
         if( $this->_check_valid_basket($this->id_basket_current)  &&
              $this->Basket_List[ $this->id_basket_current ]->check_rights('MODIFY_CONTENTS') ) {
            return $this->Basket_List[ $this->id_basket_current ];
         } else {
            $this->id_basket_set = false;
            $this->set_default_basket_by_date();
            return $this->Basket_List[ $this->id_basket_current ];
         }
      } else {
         // var_dump($this);
         $this->id_basket_set = false;
         $this->set_default_basket_by_date();
         if( $this->_check_valid_basket($this->id_basket_current) ){
            return $this->Basket_List[ $this->id_basket_current ];
         } else {
            return false;
         }
      }
   }

   /**
    * Returns the default active basket.
    *
    * @return Shopping_Basket|bool The default shopping shopping basket object, or false on failure.
    */
   public function return_default_basket() {

      if( $this->_check_valid_basket($this->id_basket_current) ) {
         return $this->Basket_List[ $this->id_basket_current ];
      } else {
         // var_dump($this);
         $this->id_basket_set = false;
         $this->set_default_basket_by_date( false );
         return $this->Basket_List[ $this->id_basket_current ];
      }
   }

   /**
    * Returns a specified basket if the user has modify rights.
    *
    * @param int $id_shopping_basket The ID of the basket. Defaults to 0.
    * @return Shopping_Basket|bool The shopping basket object, or false on failure.
    */
   public function return_basket_modify( $id_shopping_basket = 0 ) {

      if( $this->_check_valid_basket($id_shopping_basket) ) {
         if( $this->Basket_List[ $id_shopping_basket ]->check_rights('MODIFY_CONTENTS') ) {
            return $this->Basket_List[ $id_shopping_basket ];
         } else {
            Info::g('add', Lang::_('SBC err'));
            return false;
         }
      } else {
         return false;
      }
   }

   /**
    * Returns a specified basket.
    *
    * @param int $id_shopping_basket The ID of the basket. Defaults to 0.
    * @return Shopping_Basket|bool The shopping basket object, or false on failure.
    */
   public function return_basket( $id_shopping_basket = 0 ) {
      if( $this->_check_valid_basket($id_shopping_basket) ) {
         return $this->Basket_List[ $id_shopping_basket ];
      } else {
         return false;
      }
   }

   /**
    * Resets the internal pointer of the basket list array.
    *
    * @return void
    */
   public function reset_basket_list() {
      reset($this->Basket_List);
   }

   /**
    * Returns the next basket in the list, optionally skipping the current active basket.
    *
    * @param bool $skip_current Whether to skip the current active basket. Defaults to false.
    * @return Shopping_Basket|bool The next shopping basket object, or false if end of list.
    */
   public function return_basket_next( $skip_current = false ) {
      $Shopping_Basket = current($this->Basket_List);
      if( $skip_current && $this->id_basket_current == $Shopping_Basket->id_shopping_basket ) {
         next($this->Basket_List);
         $Shopping_Basket = current($this->Basket_List);
      }

      next($this->Basket_List);
      return $Shopping_Basket;
   }

   /**
    * Returns the next basket in the list that the user has modify rights for.
    *
    * @param bool $skip_current Whether to skip the current active basket. Defaults to false.
    * @return Shopping_Basket|bool The next modifiable shopping basket object, or false if none.
    */
   public function return_basket_next_modify( $skip_current = false ) {

       $Shopping_Basket = current($this->Basket_List);

       if( $Shopping_Basket === false ) return false;

       while( $Shopping_Basket = next($this->Basket_List) ) {

           if( $Shopping_Basket->check_rights('MODIFY_CONTENTS', false) ) {
               return $Shopping_Basket;
           }
       }

       return $Shopping_Basket;
   }

   /**
    * Returns the next basket in the list that the user has view-only rights for.
    *
    * @param bool $skip_current Whether to skip the current active basket. Defaults to false.
    * @return Shopping_Basket|bool The next viewable shopping basket object, or false if none.
    */
   public function return_basket_next_view( $skip_current = false ) {

       $Shopping_Basket = current($this->Basket_List);

       if( $Shopping_Basket === false ) return false;

       while( $Shopping_Basket = next($this->Basket_List) ) {

           if( !$Shopping_Basket->check_rights('MODIFY_CONTENTS', false) &&
                $Shopping_Basket->check_rights('VIEW', false) ) {
               return $Shopping_Basket;
           }
       }

       return $Shopping_Basket;
   }


   /**
    * Clears the usage state of all baskets currently being used by the logging-out user.
    *
    * @return void
    *
    * @todo Remove dead code.
    */
   public function logout_user() {

       foreach( $this->Basket_List as $Shopping_Basket ) {
//            print_debug( array(
//            $Shopping_Basket->id_shopping_basket
//            ,$Shopping_Basket->currently_user_using()
//            ,$Shopping_Basket->check_rights('MODIFY_CONTENTS')
//            ,$Shopping_Basket->basket_level_text() == 'USE'
//            ), true);
           if(   $Shopping_Basket->currently_user_using() &&
                   $Shopping_Basket->check_rights('MODIFY_CONTENTS', false) &&
                  $Shopping_Basket->basket_level_text() == 'USE' ) {
               $Shopping_Basket->state_using_clear();
          }
       }

   }

   /**
    * Reloads data and merges non-logged-in session baskets upon user login.
    *
    * @return bool|void True on success, or void.
    *
    * @todo Fix undefined variable $Shopping_Basket and the 'FIXME' die statement.
    */
   public function login_user() {

      $this->_reload_data();

      //adding nonlogin basket - upt to 2 times baskets
      if( Shopping_Basket::_check_valid_basket($Shopping_Basket) &&
            sizeof($Shopping_Basket->contents) > 0 ) {
         //FIXME - new ID for basket!
         for( $id_sb = 1; $id_sb <= (self::$max_basket*2-1) ; $id_sb++ ){
            if( !$this->_check_valid_basket($id_sb) ) {
               $Shopping_Basket->add_person_save( $id_sb );
               die('FIXME');

               $this->Basket_List[$id_shopping_basket] = $Shopping_Basket;
               return true;
            }
         }
      }
   }

   /**
    * Returns the global singleton instance of the Shopping_Basket_Chain class.
    *
    * @return Shopping_Basket_Chain The singleton instance.
    */
   static function g_global() {
      if(self::$class == false) {
         self::$class = new Shopping_Basket_Chain();
      }
      return self::$class;
   }

   /**
    * Resets the object state. Currently unimplemented.
    *
    * @return void
    */
   function reset() {

   }

   /**
    * Prepares the object for serialization by conditionally clearing the basket list.
    *
    * @return array The list of property names to be serialized.
    */
   function __sleep() {
      if ( $this->id_client > 0 ) {
         unset($this->Basket_List);
      }
      return( array_keys( get_object_vars( $this ) ) );
   }

   /**
    * Restores the object state upon unserialization, reloading data if a client is set.
    *
    * @return void
    */
   function __wakeup() {
      if ( $this->id_client > 0 ) {
         $this->_reload_data();
      }
      self::$class = $this;
   }

}
