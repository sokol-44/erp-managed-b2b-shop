<?php
/**
 * Shopping_Basket_Chain.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Shopping_Basket_Chain {
   static $class = false;
   static $id_client = 0, $id_client_user = 0;
   static $id_basket_current = 0;
   static $id_basket_set = false;
   static $Basket_List = array();
   private static $max_basket = 24;

   public function __construct() {
      self::$class = $this;

      $this->_reload_data();

   }

   private function _reload_data() {
      $F = Framework::g_global();
      $P = Person::g_global();

      $this->id_client = (int)$P->data['id_client'];
      $this->id_client_user = (int)$P->id;
      $this->Basket_List = array();

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

   public function add_to_mainbasket( $id_nr_shopping_basket = 0 ) {
      if( $this->_check_valid_basket($id_nr_shopping_basket) ) {
         $status = $this->Basket_List[$this->id_basket_current]->add_from_basket($this->Basket_List[$id_nr_shopping_basket]);
         if( $status ) {
            // Data::remove_basket( $this->id_client, $id_nr_shopping_basket );
            $this->Basket_List[$this->id_basket_current]->remove_basket();
            unset( $this->Basket_List[$id_nr_shopping_basket] );
         } else {
            return false;
         }
      } else {
         return false;
      }
   }

   public function add_basket( ) {
      if( sizeof($this->Basket_List) <= self::$max_basket ) {
         for( $id_sb = 1; $id_sb <= self::$max_basket ; $id_sb++ ){
            if( !$this->_check_valid_basket($id_sb) ) {
               $this->init_basket( $id_sb );
               return true;
            }
         }
         return false;
      } else {
         return false;
      }
   }

   public function remove_basket( $id_nr_shopping_basket = 0 ) {
      if( $this->_check_valid_basket($id_nr_shopping_basket) ) {
         Data::remove_basket( $this->id_client, $id_nr_shopping_basket );
         unset($this->Basket_List[$id_nr_shopping_basket]);
         return true;
      } else {
         return false;
      }
   }

   public function _check_valid_basket( $id_nr_shopping_basket ) {
      if ($id_nr_shopping_basket > 0 &&
      isset($this->Basket_List[$id_nr_shopping_basket]) &&
      is_object($this->Basket_List[$id_nr_shopping_basket]) ) {
         return true;
      } else {
         return false;
      }
   }

   public function get_can_add_basket() {
      if( $this->get_basket_list_count() < self::$max_basket ) { return true; }
      else { return false; }
   }

   public function get_basket_list_count() {
      //FIXME
      //params to get list of baskets belonging to specific ID
      return count($this->Basket_List);
   }

   public function init_basket( $number = 1, $create = true ) {
      $P = Person::g_global();
      $params = array(
      	'id_client' => $this->id_client, 'id_nr_shopping_basket' => $number, 'description' => '',
         'date_create' => date('Y-m-d H:i:s'), 'date_modified' => '', 'ts_create' => time(), 'ts_modified' => '',
      	'using_id_client_user' => $this->id_client, 'using_session_id' => 0, 'using_date' => 0);
      $this->Basket_List[$number] = new Shopping_Basket( $params, $create );
      if( $number == 1 && $create ) {
         $this->id_basket_set = false;
         $this->id_basket_current = 1;
      }
   }

   public function clean_basket( $id_nr_shopping_basket = 0 ) {
      if( $this->_check_valid_basket($id_nr_shopping_basket) ) {
         $this->Basket_List[$id_nr_shopping_basket]->remove_all_product();
         return true;
      } else {
         return false;
      }
   }

  public  function set_basket_list( $Basket_List ) {
      $ts_modified = 0;
      foreach( $Basket_List as $id => $param) {
         if( !$this->id_basket_set && ($ts_modified == 0 || $param['ts_modified'] > $ts_modified) ) {
            $this->id_basket_current = $param['id_nr_shopping_basket'];
            $ts_modified = $param['ts_modified'];
         }
         //LOAD Basket
         $this->Basket_List[$param['id_nr_shopping_basket']] = new Shopping_Basket( $param );
      }
   }

   public function set_default_basket( $id_nr_shopping_basket = 0 ) {
      if( $this->_check_valid_basket($id_nr_shopping_basket) ) {
         $this->id_basket_set = true;
         $this->id_basket_current = (int)$id_nr_shopping_basket;
         return true;
      } else {
         $this->id_basket_set = false;
         $this->set_default_basket_by_date();
         return false;
      }
   }

   public function set_default_basket_by_date() {
      $ts_modified = 0;
      foreach( $this->Basket_List as $id => $Basket) {
         if( !$this->id_basket_set && ($Basket->param['ts_modified'] > $ts_modified) ) {
            $this->id_basket_current = $Basket->param['id_nr_shopping_basket'];
            $ts_modified = $Basket->param['ts_modified'];
         }
      }
   }

   public function return_default_basket() {
      return $this->Basket_List[ $this->id_basket_current ];
   }

   public function return_basket( $id_nr_shopping_basket = 0 ) {
      if( $this->_check_valid_basket($id_nr_shopping_basket) ) {
         return $this->Basket_List[ $id_nr_shopping_basket ];
      } else {
         return false;
      }
   }
    
   public function reset_basket_list() {
      reset($this->Basket_List);
   }

   public function return_basket_next( $skip_current = false ) {
      $Shopping_Basket = current($this->Basket_List);
      if( $this->id_basket_current == $Shopping_Basket->id_nr_shopping_basket ) {
         next($this->Basket_List);
         $Shopping_Basket = current($this->Basket_List);
      }

      next($this->Basket_List);
      return $Shopping_Basket;
   }


   public function login_user() {
      $Shopping_Basket_tmp = reset($this->Basket_List);
      $Shopping_Basket = clone $Shopping_Basket_tmp;

      $this->_reload_data();

      //adding nonlogin basket - upt to 2 times baskets
      if( Shopping_Basket::_check_valid_basket($Shopping_Basket) &&
      sizeof($Shopping_Basket->contents) > 0 ) {
         for( $id_sb = 1; $id_sb <= (self::$max_basket*2-1) ; $id_sb++ ){
            if( !$this->_check_valid_basket($id_sb) ) {
               $Shopping_Basket->update_person( $id_sb );
               $this->Basket_List[$id_sb] = $Shopping_Basket;
               return true;
            }
         }
      }
   }

   static function g_global() {
      if(self::$class == false) {
         self::$class = new Shopping_Basket_Chain();
      }
      return self::$class;
   }

   function reset() {

   }

   function __sleep() {
      if ( $this->id_client > 0 ) {
         unset($this->Basket_List);
      }
      return( array_keys( get_object_vars( $this ) ) );
   }

   function __wakeup() {
      if ( $this->id_client > 0 ) {
         $this->_reload_data();
      }
      self::$class = $this;
   }

}

?>