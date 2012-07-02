<?php
/**
 * Data_Basket.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Data_Basket extends Data_Order {

   function __construct() {
      //echo 'Data_Basket';
      parent::__construct();
   }

   //
   //   $this->params = Data::get_basket_data($this->params);
   //   $this->contents = Data::get_basket_product_list($this->params);
   
   //LIST
   static function get_basket_chain_basket_list( $id_client ) {
      //TODO read rights
      // read good baskets
      $query = 'select id_shopping_basket, id_client, description, date_create, date_modified,
      UNIX_TIMESTAMP(date_create) as ts_create, UNIX_TIMESTAMP(date_modified) as ts_modified,
      using_id_client_user, using_session_id, using_date, UNIX_TIMESTAMP(using_date) as ts_using
      from ' . TBL_SHOP_SHOPPING_BASKET . '
      where id_client = ' . db_int($id_client) . '';
      $basket_list_raw = db_result_array( db_query( $query ) );
      $basket_list = array();
      foreach( $basket_list_raw as $basket ) {
         $basket_list[$basket['id_shopping_basket']] = $basket;
      }
      return $basket_list;
   }
    
   //BASKET

   static function get_basket_data($basket_params) {
      $query = 'select id_shopping_basket, id_client, description, date_create, date_modified,
     	UNIX_TIMESTAMP(date_create) as ts_create, UNIX_TIMESTAMP(date_modified) as ts_modified,
      using_id_client_user, using_session_id, using_date, UNIX_TIMESTAMP(using_date) as ts_using
      from ' . TBL_SHOP_SHOPPING_BASKET . ' where
      id_client = ' . db_int($basket_params['id_client']) . ' and
      id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']);
      return db_fetch_array( db_query( $query ) );
   }
   
   static function get_basket_version_list($basket_params) {
      $query = 'select id_shopping_basket_version, id_shopping_basket, id_client, id_client_user,
      date_created, date_modified,
      UNIX_TIMESTAMP(date_created) as ts_created, UNIX_TIMESTAMP(date_modified) as ts_modified
      from ' . TBL_SHOP_SHOPPING_BASKET_VERSION . ' where
      id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']) . '
      order by date_created asc, date_modified asc';
      return db_result_array_full( db_query( $query ) );
   }
   
   static function get_basket_version_product_list($basket_params) {
      $query = ' select id_shopping_basket_version, id_product, id_product_subtype,
      quantity, date_added
      from ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . ' where
      id_shopping_basket_version = ' . db_int($basket_params['id_shopping_basket_version']);
      $product_array = db_result_array( db_query( $query ) );
      $contents = array();
      foreach( $product_array as $product ) {
         $key = Data_Products::get_key_from_product_params( $product );
         $contents[$key] = array(
               'quantity' => (int)$product['quantity'],
               'id_product' => (int)$product['id_product'],
               'id_product_subtype' => (int)$product['id_product_subtype'],
               'id_shopping_basket_version' => (int)$product['id_shopping_basket_version']
               );
      }
      return $contents;
   }
    
   static function remove_basket_product( $product_key, $basket_params) {
      $product_params = Data_Products::get_product_params_from_key($product_key);
      $clear_query = 'delete from ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . ' where
      id_shopping_basket_version = ' . db_int($basket_params['id_shopping_basket_version']) . ' and
      id_product_subtype = ' . db_int($product_params['id_product_subtype']) . ' and
      id_product = ' . db_int($product_params['id_product']);

      db_transaction_start();
      db_query( $clear_query );
      $res = db_affected_rows();
      db_transaction_end();
   
      return $res;
   }
   
   static function create_new_basket( $basket_params ) {
      $P = Person::g_global();
      
//       db_transaction_start();

      $create_basket_query = 'insert into ' . TBL_SHOP_SHOPPING_BASKET . '
      set id_client = ' . db_int($basket_params['id_client']) . ',
      	description = "' . db_escape($basket_params['description']) . '",
      	date_create = now(), date_modified = NULL,
      	using_id_client_user = ' . db_int($P->id) . ',
      	using_session_id = "' . db_escape($P->session_id) . '",
      	using_date = now()';
      db_query( $create_basket_query );
      $id_shopping_basket = db_insert_id();

      $create_basket_history_query = 'insert into ' . TBL_SHOP_SHOPPING_BASKET_HISTORY . '
      	set id_shopping_basket = ' . db_int($id_shopping_basket) . ',
      	mode = "START"';
      db_query( $create_basket_history_query );

      $create_basket_version_query = 'insert into ' . TBL_SHOP_SHOPPING_BASKET_VERSION . '
      	set id_shopping_basket = ' . db_int($id_shopping_basket) . ',
      	id_client_user = ' . db_int($P->id) . ',
      	id_client = ' . db_int($basket_params['id_client']) . ',
      	date_created = now(), date_modified = NULL';
      db_query( $create_basket_version_query );
      $id_shopping_basket_version = db_insert_id();

//       db_transaction_end();
      
      return compact('id_shopping_basket', 'id_shopping_basket_version');
   }
   
   static function remove_basket( $basket_params, $basket_version_list) {
      //      self::remove_basket_product_list($id_nr_shopping_basket);
      //      self::remove_basket_pdata($id_nr_shopping_basket);
      db_transaction_start();
      
      if( count($basket_version_list) == 0 ) return false;
      
      foreach( $basket_version_list as $version ) {
         $basket_version_list_escape[] = db_int($version);
      }
      $sql_basket_version_list = implode(',', $basket_version_list_escape);
      
      $verified_basket_version = 'select GROUP_CONCAT(id_shopping_basket_version) as list
         from ' . TBL_SHOP_SHOPPING_BASKET_VERSION . ' where
         id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']) . ' and
         id_client = ' . db_int($basket_params['id_client']) . ' and
         id_shopping_basket_version IN (' . $sql_basket_version_list . ')';
      $verified_basket_version_list = db_fetch_result('list', db_query( $verified_basket_version ) );

      //main clear
      $clear_products_query = 'delete from ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . ' where
         id_shopping_basket_version IN (' . $verified_basket_version_list . ')';
      db_query( $clear_products_query );
      
      $clear_basket_version_query = 'delete from ' . TBL_SHOP_SHOPPING_BASKET_VERSION . ' where
         id_shopping_basket_version IN (' . $verified_basket_version_list . ')';
      db_query( $clear_basket_version_query );
      
      $clear_basket_history_query = 'delete from ' . TBL_SHOP_SHOPPING_BASKET_HISTORY . ' where
         id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']);
      db_query( $clear_basket_history_query );
      
      $clear_basket_query = 'delete from ' . TBL_SHOP_SHOPPING_BASKET. ' where
         id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']);
      db_query( $clear_basket_query );
      
      db_transaction_end();
      
      return true;
   }
   
   static function put_basket_version_product_list($basket_contents, $basket_params) {
   
      // db_transaction_start();
   
      $clear_query = 'delete from ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . ' where
      id_shopping_basket_version = ' . db_int($basket_params['id_shopping_basket_version']);
      db_query( $clear_query );
   
      foreach( $basket_contents as $key_product => $details ) {
         $insert_query = 'insert into ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . '
         set id_shopping_basket_version = ' . db_int($basket_params['id_shopping_basket_version']) . ',
         id_product = ' . db_int($details['id_product']) . ',
         id_product_subtype = ' . db_int($details['id_product_subtype']) . ',
         quantity = ' . db_int($details['quantity']) . ', date_added = now()';
         db_query( $insert_query );
      }
      //      print_debug($basket_params);
      //      print_debug($basket_contents);
      // db_transaction_end();
   }
   
   static function add_basket_new_version($basket_params, $id_client_user ) {
     
      $add_basket_new_version_query = 'insert into ' . TBL_SHOP_SHOPPING_BASKET_VERSION . ' set
      id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']) . ',
      id_client = ' . db_int($basket_params['id_client']) . ',
      id_client_user = ' . db_int($id_client_user) . ',
      date_created = now()';
      db_query( $add_basket_new_version_query );
      $id_shopping_basket_version = db_insert_id();
      
      $basket_new_version = 'select id_shopping_basket_version, id_shopping_basket,
      id_client, id_client_user, date_created, date_modified,
      UNIX_TIMESTAMP(date_created) as ts_created, UNIX_TIMESTAMP(date_modified) as ts_modified
      from ' . TBL_SHOP_SHOPPING_BASKET_VERSION . '
      where id_shopping_basket_version = ' . (int)$id_shopping_basket_version;
      $basket_new_version_res = db_query( $basket_new_version );
      $basket_new_version = db_fetch_array($basket_new_version_res, 0);
      
      $FD = File_Debug::g_global();
      $FD->s(array('id_shopping_basket_version' => $id_shopping_basket_version, '$basket_new_version' => $basket_new_version));
      
      return $basket_new_version;
   }
   
   //OLD
   /*
   static function put_basket_data($basket_params) {
      $F = Framework::g_global();
      $P = Person::g_global();

      //MySQL
      $query = 'insert into ' . TBL_SHOP_SHOPPING_BASKET . '
      	set id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']) . ',
      	id_client = ' . db_int($basket_params['id_client']) . ',
      	description = "' . db_escape($basket_params['description']) . '",
      	date_create = now(), date_modified = NULL,
      	using_id_client_user = ' . db_int($P->id) . ',
      	using_session_id = "' . db_escape($P->session_id) . '",
      	using_date = now()
      	ON DUPLICATE KEY update
			description = "' . db_escape($basket_params['description']) . '",
      	date_modified = now(),
      	using_id_client_user = ' . db_int($P->id) . ',
      	using_session_id = "' . db_escape($P->session_id) . '",
      	using_date = now()';
      $res = db_query( $query );
   }


   static function put_basket_product_list($basket_contents, $basket_params) {

      db_transaction_start();

      $clear_query = 'delete from ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . ' where
      id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']) . ' and
      id_client = ' . db_int($basket_params['id_client']);;
      db_query( $clear_query );

      foreach( $basket_contents as $id_product => $details ) {
         $insert_query = 'insert into ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . '
      	set id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']) . ',
      	id_client = ' . db_int($basket_params['id_client']) . ',
      	id_product = ' . db_int($id_product) . ',
      	quantity = ' . db_int($details['quantity']) . ', date_added = now()';
         db_query( $insert_query );
      }
      //      print_debug($basket_params);
      //      print_debug($basket_contents);
      db_transaction_end();
   }
   
   static function get_basket_product_list($basket_params) {
      $query = ' select id_product, quantity, date_added
      from ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . ' where
      id_client = ' . db_int($basket_params['id_client']) . ' and
      id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']);
      $product_array = db_result_array( db_query( $query ) );
      $contents = array();
      foreach( $product_array as $product ) {
         $contents[$product['id_product']] = array('quantity' => (int)$product['quantity']);
      }
      return $contents;
   }
   
   static function remove_basket_product( $id_product, $basket_params) {
    $clear_query = 'delete from ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . ' where
   id_client = ' . db_int($basket_params['id_client']) . ' and
   id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']) . ' and
   id_product = ' . db_int($id_product);
   
   db_transaction_start();
   db_query( $clear_query );
   $res = db_affected_rows();
   db_transaction_end();
   
   return $res;
   } */
   
   
}

?>