<?php
/**
 * Data_Basket.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Data_Basket {

   function __construct() {
      //echo 'Data_Basket';
      //parent::__construct();
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
      //      echo $query;
      $res = db_query( $query );
   }


   static function put_basket_product_list($basket_contents, $basket_params) {
      $F = Framework::g_global();
      $P = Person::g_global();

      db_transaction_start();

      $cleare_query = 'delete from ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . ' where
      id_client = ' . db_int($basket_params['id_client']) . ' and
      id_nr_shopping_basket = ' . db_int($basket_params['id_nr_shopping_basket']);
      db_query( $cleare_query );

      foreach( $basket_contents as $id_product => $details ) {
         $insert_query = 'insert into ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . '
      	set id_client = ' . db_int($basket_params['id_client']) . ',
      	id_nr_shopping_basket = ' . db_int($basket_params['id_nr_shopping_basket']) . ',
      	id_product = ' . db_int($id_product) . ',
      	product_count = ' . db_int($details['quantity']) . ', date_added = now()';
         print_debug( $insert_query);
         db_query( $insert_query );
      }
//      print_debug($basket_params);
//      print_debug($basket_contents);
      db_transaction_end();
   }

   static function get_basket_product_list( $id_shopping_basket = 0, $id_client = 0 ) {
      $F = Framework::g_global();

      $query = 'select sbp.id_product, sbp.product_count
         from ' . TBL_SHOP_SHOPPING_BASKET . ' sb left join ' . TBL_SHOP_SHOPPING_BASKET_PRODUCTS . ' sbp on
         ( sbp.id_shopping_basket = p2c.id_shopping_basket )
         where sbp.id_shopping_basket = ' . db_int($id_shopping_basket) . ' and
         sb.id_client = ' . db_int($id_client);

      $res = db_query( $query );
      return db_result_array($res);
   }

   static function set_basket_contents($params_array, $contents) {
      $F = Framework::g_global();
      $P = Person::g_global();

      $params_array = array(
      	'id_client' => 0, 'description' => 0, 'using_id_client_user' => 0,
      	'using_id_client_user' => 0, 'using_session_id' => 0, 'using_date' => 0
      );


   }

}

?>