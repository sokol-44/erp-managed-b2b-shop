<?php
/**
 * Order.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/**
 * Data class goint to provide data and operation on them
 * Application will get and save any data thru it
 */

class Order {
   public $data = array();
   public $product_list = array();
   public $status_history = array();

   function __construct( $id_order = 0) {
      $this->data = array();
      $this->product_list = array();
      $this->status_history = array();


      if( $id_order > 0 ) {
         return self::_load_data( (int)$id_order );
      }

   }
    
   function check_rights() {
      if( Framework::not_null($this->data) ) {
         $P = Person::g_global();
         return ( $this->data['id_client'] == $P->data['id_client'] );
      } else {
         return false;
      }
   }

   function _load_data( $id_order ) {
      $F = Framework::g_global();
      
      $this->id_order = $id_order;

      $this->data = Data::get_order_data( $id_order );
      if( $F->not_null($this->data) ) {
         $this->product_list = Data::get_order_product_list( $id_order );
         $this->status_history = Data::get_order_status_history_list( $id_order );
         return sizeof($this->product_list);
      } else {
         return false;
      }
   }


   static function make_new_order($Shopping_Basket, $order_description) {
      $F = Framework::g_global();
      $P = Person::g_global();

      if( $Shopping_Basket->params['id_client'] != $P->data['id_client'] || !$P->check_roles('ADMIN,OPERATOR') ||
      !Shopping_Basket::_check_valid_basket($Shopping_Basket) )  {
         return false;
      }

      $product_list = $Shopping_Basket->get_all_product();

      if( !$F->not_null($order_description) ) $order_description = 'NULL';
       
      $id_order = Data::put_order_data($P->data['id_client'], $Shopping_Basket->params['description'], $order_description);
      if( $id_order > 0 ) {
         $count_product = Data::put_order_product_list($id_order, $product_list);
         Data::put_order_status($id_order, '1', $order_description);
         return array($id_order, $count_product);
      } else {
         return false;
      }
   }

}

?>