<?php
/**
 * Data_Order.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Data_Order extends Data_Picture {

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
   static function getOrderList( $id_order_start = 0, $length = 1, $where = '' ) {
      list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);
      
      $query = 'select o.id_order, o.id_client, o.date_create, o.date_modified, o.id_order_status,
      o.description, o.description_basket, o.id_shopping_basket
      from ' . TBL_SHOP_ORDER . ' o
      where o.id_order ' . $comparision_dir . db_int($id_order_start) . $where . '
      ORDER BY o.id_order ' . $order_dir . ' LIMIT '. db_int($length);
      
      $result = db_query( $query );
      $ret_tmp = db_result_array_full($result);
      
      $ret_array = array();
      foreach($ret_tmp as $order ) {
         $order['ProductOrder'] = self::getProductOrder((int)$order['id_order']);
         $ret_array[] = $order;
      }
      return $ret_array;
   }
   
   static function getProductOrder( $id_order ) {
      $query = 'select op.id_product, op.name, op.price, op.vat, op.quantity
      from ' . TBL_SHOP_ORDER_PRODUCT . ' op where op.id_order = ' . db_int($id_order);
      
      $result = db_query( $query );
      $ret_tmp = db_result_array_full($result);
      
      $ret_array = array();
      foreach($ret_tmp as $product ) {
         $ret_array['value_'.$product['id_product']] = $product;
      }
      return $ret_array;
   }
   
   static function getOrderListRest( $id_order_start = 0, $length = 1, $where = '' ) {
      add_to_fp("getOrderListRest:");
      list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);
      
      $query = 'select o.id_order, o.id_client, o.date_create, o.date_modified, o.id_order_status,
      o.description, o.description_basket, o.id_shopping_basket
      from ' . TBL_SHOP_ORDER . ' o
      where o.id_order ' . $comparision_dir . db_int($id_order_start) . $where . '
      ORDER BY o.id_order ' . $order_dir;
      add_to_fp($query);
      $result = db_query( $query );
      return db_rows($result)-1;
   }
   
   static function setOrderHiddenStatus( $id_order, $id_client, $hidden_status) {
      $query = 'select "' . db_int($id_order) . '" as id_one, "' . db_int($id_client) . '" as id_two,
       "" as additional_data,
       b_func_order_hidden_status_change("' . db_int($id_order) . '", "' . db_int($id_client) . '", "' . db_escape($hidden_status) . '") as status';
      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }
   
   static function setOrderStatus( $id_order, $id_order_status, $status) {
   	  $res = self::put_order_status((int)$id_order, (int)$id_order_status, $status);
   	  switch($res) {
   	  	case 1:
   	  		$status = 'SUCCESS,NEW_STATUS';
   	  		break;
   	  	case 2:
   	  		$status = 'SUCCESS,UPDATE_STATUS';
   	  		break;
   	  	//case false:
   	  	//case 0:
   	  	default:
   	  		$status = 'ERROR,WRONG_ORDER_ID';
   	  		break;
   	  }
      return array( 'id' => $id_order, 
      				'additional_data' => 'id_order_status=' . $id_order_status,
      				'status' => $status );
   }
   
   static function get_order_history_list() {

   	$query = 'select os.id_order_status, concat("OSH_", os.name) as name from ' . TBL_SHOP_ORDER_HISTORY;
   	$res = db_result_array( db_query( $query ) );
   	$ret_array = array();
   	foreach( $res as $osh ) {
   		$ret_array[$osh['id_order_status']] = $osh['name'];
   	}
   	
   	return $ret_array;
   }
   
   static function get_order_list( $id_client = 0, $where = '' ) {
      $SP = SplitPage::g_global();
      
      if( $id_client > 0 ) {
         $where =  ' where o.id_client = ' . db_int($id_client);
      }

      $query = 'select o.id_order, o.id_client, o.date_create, o.date_modified, o.id_order_status,
      o.description, o.description_basket, o.id_shopping_basket, concat("OSH_", os.name) as name,
     	UNIX_TIMESTAMP(o.date_create) as ts_create, UNIX_TIMESTAMP(o.date_modified) as ts_modified
      from ' . TBL_SHOP_ORDER . ' o left join ' . TBL_SHOP_ORDER_STATUS . ' os
      on (o.id_order_status = os.id_order_status)' . $where;
      $query_fast = 'select count(o.id_order) as total from ' . TBL_SHOP_ORDER . ' o ' . $where;
      $sp_query = $SP->prepare_sql( $query, $query_fast);
      $res = db_query( $sp_query );
      $ret_tmp = db_result_array( $res );
      $ret_array = array();
      foreach($ret_tmp as $order ) {
         $ret_array[$order['id_order']] = $order;
      }
      return $ret_array;
   }



   static function get_order_data( $id_order ) {
      $query = 'select o.id_order, o.id_client, o.date_create, o.date_modified, o.id_order_status,
      o.description, o.description_basket, o.id_shopping_basket, concat("OSH_", os.name) as name,
     	UNIX_TIMESTAMP(o.date_create) as ts_create, UNIX_TIMESTAMP(o.date_modified) as ts_modified
      from ' . TBL_SHOP_ORDER . ' o left join ' . TBL_SHOP_ORDER_STATUS . ' os
      on (o.id_order_status = os.id_order_status)
      where id_order = ' . db_int($id_order);
      return db_fetch_array( db_query( $query ) );
   }

   static function get_order_product_list( $id_order ) {
      $query = 'select op.id_product, op.name, op.price, op.vat, op.quantity,
      p.name as p_name, p.description, p.picture_small_url, p.picture_big_url, p.picture_id
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
      $query = 'select osh.id_order_status, osh.timestamp, osh.description, concat("OSH_", os.name) as name
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



   static function put_order_data($id_client, $basket_params, $order_description) {
      $query = 'insert into ' . TBL_SHOP_ORDER . '
      	set id_client = ' . db_int($id_client) . ',
      	description = "' . db_escape($order_description) . '",
      	description_basket = "' . db_escape($basket_params['description']) . '",
      	id_shopping_basket = "' . db_int($basket_params['id_shopping_basket']) . '",
      	date_create = now(), date_modified = NULL,
      	id_order_status = 1';

      //      print_debug($query);
       
      db_query( $query );
      $id_order = db_insert_id();
      //$id_order = 1;
      return $id_order;
   }


   static function put_order_status($id_order, $id_order_status, $description) {
   	$query_chk = 'select `id_order` from ' . TBL_SHOP_ORDER . '
      	where id_order = ' . db_int($id_order);
   	
   	  if( db_rows( db_query($query_chk) ) == 1 ) {
	   	$query = 'insert into ' . TBL_SHOP_ORDER_STATUS_HISTORY . '
	      	set id_order = ' . db_int($id_order) . ',
	   		id_order_status = ' . db_int($id_order_status) . ',
	      	description = "' . db_escape($description) . '" 
	      	on duplicate keys update description = "' . db_escape($description) . '"';
	      $res = db_query( $query );
	      $af_rows = db_affected_rows();
	      $query = 'update ' . TBL_SHOP_ORDER . ' 
	        set id_order_status = ' . db_int($id_order_status) . '
	      	where id_order = ' . db_int($id_order);	
	      $res = db_query( $query );
	      return  $af_rows;
      } else {
      	return false;
      }
      
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