<?php
/**
 * Shopping_Basket_Chain.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/**
 * @author ms
 *
 */
class Shopping_Basket_Chain {
   static $class = false;
   static $id_client = 0, $id_client_user = 0;
   static $id_basket_current = 0;
   static $Basket_List = array();

   function __construct() {
      self::$class = $this;

      $this->_reload_data();

   }

   function _reload_data() {
      $F = Framework::g_global();
      $P = Person::g_global();

      $this->id_client = (int)$P->data['id_client'];
      $this->id_client_user = (int)$P->id;
      $this->id_basket_current = 0;
      $this->Basket_List = array();

      //FIXME
      //for not logged users
      $Basket_List = Data::get_basket_chain_basket_list( $this->id_client );
      if( $F->not_null($Basket_List) ) {
         $this->set_basket_list($Basket_List);
         //$this->set_default_basket_by_date();
      } else {
         $this->init_basket();
      }
   }

   function init_basket() {
      $params = array(
      	'id_client' => $this->id_client, 'id_nr_shopping_basket' => 1, 'description' => '',
         'date_create' => date('Y-m-d H:i:s'), 'date_modified' => '', 'ts_create' => time(), 'ts_modified' => '',
      	'using_id_client_user' => $this->id_client, 'using_session_id' => 0, 'using_date' => 0);
      $this->Basket_List[1] = new Shopping_Basket( $param, true );
   }

   function set_basket_list( $Basket_List ) {
      $ts_modified = 0;
      foreach( $Basket_List as $id => $param) {
         if( $ts_modified == 0 || $param['ts_modified'] > $ts_modified ) {
            $this->id_basket_current = $param['id_nr_shopping_basket'];
            $ts_modified = $param['ts_modified'];
         }
         $this->Basket_List[$param['id_nr_shopping_basket']] = new Shopping_Basket( $param );
      }
   }

   function set_default_basket_by_date() {
      $ts_modified = 0;
      foreach( $this->Basket_List as $id => $Basket) {
         if( $Basket->param['ts_modified'] > $ts_modified ) {
            $this->id_basket_current = $Basket->param['id_nr_shopping_basket'];
            $ts_modified = $Basket->param['ts_modified'];
         }
      }
   }

   function return_default_basket() {
      return $this->Basket_List[ $this->id_basket_current ];
   }
    
   function reset_basket_list() {
      reset($this->Basket_List);
   }
    
   function return_basket( $skip_current = false ) {
      $Shopping_Basket = current($this->Basket_List);
      next($this->Basket_List);
      return $Shopping_Basket;
   }


   function set_user() {
      $P = Person::g_global();

      $this->id_client = (int)$P->data['id_client'];
      $this->id_client_user = (int)$P->id;
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
      unset($this->product_array);
      return( array_keys( get_object_vars( $this ) ) );
   }

   function __wakeup() {
      $this->_reload_data();
      self::$class = $this;
   }

}

?>