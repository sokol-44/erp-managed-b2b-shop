<?php
/**
 * Data_Order.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Data_Order {

   function __construct() {
      //echo 'Data_Basket';
      //parent::__construct();

      //$Data_Products_params = array('id_client' => 0, 'client_view' => false);
   }
    
    
   /*
    $this->data = Data::get_order_data( $id_order );
    if( $this->data ) {
    $this->product_list = Data::get_order_product_list( $id_order );
    $this->status_history = Data::get_order_status_history_list( $id_order );
     
    */
    
   static function get_order_data( $id_order ) {
      $query = 'select o.id_order, o.id_client, o.date_create, o.date_modified, o.id_order_status,
      o.description, o.description_basket, os.name,
     	UNIX_TIMESTAMP(o.date_create) as ts_create, UNIX_TIMESTAMP(o.date_modified) as ts_modified
      from ' . TBL_SHOP_ORDER . ' o left join ' . TBL_SHOP_ORDER_STATUS . ' os
      on (o.id_order_status = os.id_order_status)
      where id_order = ' . db_int($id_order);
      return db_fetch_array( db_query( $query ) );
   }
    
   static function get_order_product_list( $id_order ) {
      $query = 'select op.id_product, op.name, op.price, op.vat, op.quantity,
      p.name as p_name, p.description, p.picture_small_url, p.picture_big_url
      from ' . TBL_SHOP_ORDER_PRODUCT . ' op left outer join ' . TBL_SHOP_PRODUCT . ' p
      on (op.id_product = p.id_product and p.status = "ACTIVE")
      where id_order = ' . db_int($id_order);
      $ret_tmp = db_result_array( db_query( $query ) );
      $ret_array = array();
      foreach($ret_tmp as $product ) {
         $ret_array[$product['id_product']] = $product;
      }
      return $ret_array;
   }
    
   static function get_order_status_history_list( $id_order ) {
      $query = 'select osh.id_order_status, osh.timestamp, osh.description, os.name
      from ' . TBL_SHOP_ORDER_STATUS_HISTORY . ' osh left join ' . TBL_SHOP_ORDER_STATUS . ' os
      on (osh.id_order_status = os.id_order_status)
      where id_order = ' . db_int($id_order);
      $ret_tmp = db_result_array( db_query( $query ) );
      $ret_array = array();
      foreach($ret_tmp as $status ) {
         $ret_array[$status['id_order_status']] = $status;
      }
      return $ret_array;
   }
    
    

   static function put_order_data($id_client, $description_basket, $order_description) {
      $query = 'insert into ' . TBL_SHOP_ORDER . '
      	set id_client = ' . db_int($id_client) . ',
      	description = "' . db_escape($order_description) . '",
      	description_basket = "' . db_escape($description_basket) . '",
      	date_create = now(), date_modified = NULL,
      	id_orders_status = 1';

      //      print_debug($query);
       
      db_query( $query );
      $id_order = db_insert_id();
      //$id_order = 1;
      return $id_order;
   }

    
   static function put_order_status($id_order, $id_order_status, $description) {
      $query = 'insert into ' . TBL_SHOP_ORDER_STATUS_HISTORY . '
      	set id_order = ' . db_int($id_order) . ',
      	id_order_status = ' . db_int($id_order_status) . ',
      	description = "' . db_escape($description) . '"';
      db_query( $query );
   }

   static function put_order_product_list($id_order, $product_list) {

      db_transaction_start();
      foreach( $product_list as $id_product => $details ) {
         $insert_query = 'insert into ' . TBL_SHOP_ORDER_PRODUCT . '
      	set id_order = ' . db_int($id_order) . ',
      	id_product = ' . db_int($id_product) . ',
      	name = "' . db_escape($details['name']) . '",
      	price = "' . db_escape($details['price']) . '",
      	vat = "' . db_int($details['vat']) . '",
      	quantity = ' . db_int($details['quantity']) . '';
         //         print_debug($insert_query);
         db_query( $insert_query );
      }
      //      print_debug($basket_params);
      //      print_debug($basket_contents);
      $res = db_affected_rows();
      db_transaction_end();

      return $res;
   }

}

?>