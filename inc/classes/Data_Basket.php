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

   static function get_basket_data($basket_params) {
      $query = 'select id_client, id_nr_shopping_basket, description, date_create, date_modified,
     	UNIX_TIMESTAMP(date_create) as ts_create, UNIX_TIMESTAMP(date_modified) as ts_modified,
      using_id_client_user, using_session_id, using_date, UNIX_TIMESTAMP(using_date) as ts_using
      from ' . TBL_SHOP_SHOPPING_BASKET . ' where
      id_client = ' . db_int($basket_params['id_client']) . ' and
      id_nr_shopping_basket = ' . db_int($basket_params['id_nr_shopping_basket']);
      return db_fetch_array( db_query( $query ) );
   }

   static function remove_basket( $id_client, $id_nr_shopping_basket ) {
      //      self::remove_basket_product_list($id_nr_shopping_basket);
      //      self::remove_basket_pdata($id_nr_shopping_basket);
      db_transaction_start();

      $clear_products_query = 'delete from ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . ' where
      id_client = ' . db_int($id_client) . ' and
      id_nr_shopping_basket = ' . db_int($id_nr_shopping_basket);
      db_query( $clear_products_query );

      $delete_basket_query = 'delete from ' . TBL_SHOP_SHOPPING_BASKET . '
      	where id_client = ' . db_int($id_client) . ' and
      	id_nr_shopping_basket = ' . db_int($id_nr_shopping_basket) . '';
      db_query( $delete_basket_query );

      db_transaction_end();

   }
    
    
   static function remove_basket_product( $id_product, $basket_params) {
      $clear_query = 'delete from ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . ' where
      id_client = ' . db_int($basket_params['id_client']) . ' and
      id_nr_shopping_basket = ' . db_int($basket_params['id_nr_shopping_basket']) . ' and
      id_product = ' . db_int($id_product);
      
      db_transaction_start();
      db_query( $clear_query );
      $res = db_affected_rows();
      db_transaction_end();
      
      return $res;
   }

   static function get_basket_chain_basket_list( $id_client ) {
      $query = 'select id_client, id_nr_shopping_basket, description, date_create, date_modified,
      	UNIX_TIMESTAMP(date_create) as ts_create, UNIX_TIMESTAMP(date_modified) as ts_modified,
      	using_id_client_user, using_session_id, using_date, UNIX_TIMESTAMP(using_date) as ts_using
         from ' . TBL_SHOP_SHOPPING_BASKET . '
      	where id_client = ' . db_int($id_client) . '';
      return db_result_array( db_query( $query ) );
   }


   static function get_basket_product_list($basket_params) {
      $query = ' select id_product, quantity, date_added
      from ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . ' where
      id_client = ' . db_int($basket_params['id_client']) . ' and
      id_nr_shopping_basket = ' . db_int($basket_params['id_nr_shopping_basket']);
      $product_array = db_result_array( db_query( $query ) );
      $contents = array();
      foreach( $product_array as $product ) {
         $contents[$product['id_product']] = array('quantity' => (int)$product['quantity']);
      }
      return $contents;
   }

   static function put_basket_data($basket_params) {
      $F = Framework::g_global();
      $P = Person::g_global();

      $query = 'insert into ' . TBL_SHOP_SHOPPING_BASKET . '
      	set id_client = ' . db_int($basket_params['id_client']) . ',
      	id_nr_shopping_basket = ' . db_int($basket_params['id_nr_shopping_basket']) . ',
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
      //      print_debug($basket_params);
      //      print_debug($P);
      echo $query;
      $res = db_query( $query );
   }


   static function put_basket_product_list($basket_contents, $basket_params) {

      db_transaction_start();

      $clear_query = 'delete from ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . ' where
      id_client = ' . db_int($basket_params['id_client']) . ' and
      id_nr_shopping_basket = ' . db_int($basket_params['id_nr_shopping_basket']);
      db_query( $clear_query );

      foreach( $basket_contents as $id_product => $details ) {
         $insert_query = 'insert into ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . '
      	set id_client = ' . db_int($basket_params['id_client']) . ',
      	id_nr_shopping_basket = ' . db_int($basket_params['id_nr_shopping_basket']) . ',
      	id_product = ' . db_int($id_product) . ',
      	quantity = ' . db_int($details['quantity']) . ', date_added = now()';
         db_query( $insert_query );
      }
      //      print_debug($basket_params);
      //      print_debug($basket_contents);
      db_transaction_end();
   }

}

?>