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
      using_id_client_user, using_session_id, using_date, UNIX_TIMESTAMP(using_date) as ts_using,
      state
      from ' . TBL_SHOP_SHOPPING_BASKET . '
      where state != "ORDER" and id_client = ' . db_int($id_client) . '';
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
      using_id_client_user, using_session_id, using_date, UNIX_TIMESTAMP(using_date) as ts_using,
      state
      from ' . TBL_SHOP_SHOPPING_BASKET . ' where
      id_client = ' . db_int($basket_params['id_client']) . ' and
      id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']);
      return db_fetch_array( db_query( $query ) );
   }

   static function put_basket_history($basket_params, $params_in) {
      $mode = $params_in['now'] . '_' . $params_in['new'];
      $update_query = 'insert ' . TBL_SHOP_SHOPPING_BASKET_HISTORY . ' set
         id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']) . ',
         id_client_user = ' . db_int($basket_params['using_id_client_user']) . ',
         id_address = ' .  db_int($params_in['id_address']) . ', 
         mode = "' . db_escape($mode) . '", date = now(),
         description = "' . db_escape($params_in['history_description']) . '"';
      db_query( $update_query );
      return db_insert_id();
   }
   
   static function get_basket_history_last($basket_params) {
      $query = 'select bh.id_shopping_basket_history, bh.id_shopping_basket, bh.id_client_user,` bh.id_address,
      cu.id_client, cu.login, bh.date, bh.mode, bh.description, UNIX_TIMESTAMP(bh.date) as ts_date
      from ' . TBL_SHOP_SHOPPING_BASKET_HISTORY . ' bh
      left join ' . TBL_GLOBAL_CLIENT_USER . ' cu on
      (bh.id_client_user = cu.id_client_user)
      where
      bh.id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']) . '
      order by bh.date asc LIMIT 1';
      return db_fetch_array( db_query( $query ) );
   }

   static function get_basket_history_list($basket_params) {
      $query = 'select bh.id_shopping_basket_history, bh.id_shopping_basket, bh.id_client_user, bh.id_address,
      cu.id_client, cu.login, bh.date, bh.mode, bh.description, UNIX_TIMESTAMP(bh.date) as ts_date
      from ' . TBL_SHOP_SHOPPING_BASKET_HISTORY . ' bh
      left join ' . TBL_GLOBAL_CLIENT_USER . ' cu on
      (bh.id_client_user = cu.id_client_user)
      where
      bh.id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']) . '
      order by bh.date asc';
      return db_result_array_full( db_query( $query ) );
   }
   
   static function get_basket_version_list($basket_params) {
      $query = 'select bv.id_shopping_basket_version, bv.id_shopping_basket, bv.id_client, bv.id_client_user,
      bv.date_created, bv.date_modified,
      UNIX_TIMESTAMP(bv.date_created) as ts_created, UNIX_TIMESTAMP(bv.date_modified) as ts_modified,
      count(bp.id_shopping_basket_version) as count_product_types, sum(bp.quantity) as product_count
      from ' . TBL_SHOP_SHOPPING_BASKET_VERSION . ' bv
      left join ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . ' bp on
      (bv.id_shopping_basket_version = bp.id_shopping_basket_version)
      where
      bv.id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']) . '
      group by bp.id_shopping_basket_version
      order by bv.date_created asc, bv.date_modified asc ';
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
      //$P = Person::g_global();
      
//       db_transaction_start();

      $create_basket_query = 'insert into ' . TBL_SHOP_SHOPPING_BASKET . '
      set id_client = ' . db_int($basket_params['id_client']) . ',
      	description = "' . db_escape($basket_params['description']) . '",
      	date_create = now(), date_modified = NULL,
      	using_id_client_user = ' . db_int($basket_params['using_id_client_user']) . ',
      	using_session_id = "' . db_escape($basket_params['using_session_id']) . '",
      	using_date = now(),
      	state = "USE_0"';
      db_query( $create_basket_query );
      $id_shopping_basket = db_insert_id();

      $create_basket_history_query = 'insert into ' . TBL_SHOP_SHOPPING_BASKET_HISTORY . '
      	set id_shopping_basket = ' . db_int($id_shopping_basket) . ',
      	id_client_user = ' . db_int($basket_params['using_id_client_user']) . ',
      	mode = "START"';
      db_query( $create_basket_history_query );

      $create_basket_version_query = 'insert into ' . TBL_SHOP_SHOPPING_BASKET_VERSION . '
      	set id_shopping_basket = ' . db_int($id_shopping_basket) . ',
      	id_client_user = ' . db_int($basket_params['using_id_client_user']) . ',
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
      
      if( count($basket_version_list) ) {
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
       }
              
      $clear_basket_history_query = 'delete from ' . TBL_SHOP_SHOPPING_BASKET_HISTORY . ' where
         id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']);
      db_query( $clear_basket_history_query );
      
      $clear_basket_query = 'delete from ' . TBL_SHOP_SHOPPING_BASKET. ' where
         id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']);
      db_query( $clear_basket_query );
      
      db_transaction_end();
      
      return true;
   }
   
   static function put_basket_update_lock_data($basket_params) {
       return self::put_basket_update_use_data( $basket_params );
   }
   
   static function put_basket_update_use_data($basket_params) {

      if( (int)$basket_params['using_id_client_user']>0 ) {
         $sql_set_m = ', date_modified = now() ';
      } else {
         $sql_set_m = '';
      }
      
      
      $update_query = 'update ' . TBL_SHOP_SHOPPING_BASKET . ' set
         using_id_client_user = ' . db_int($basket_params['using_id_client_user']) . ',
         using_session_id = "' . db_escape($basket_params['using_session_id']) . '",
         state = "' . db_escape($basket_params['state']) . '"
         ' . $sql_set_m . ' where id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']);
      db_query( $update_query );
      
      return true;
   }
   

   static function put_basket_info($basket_params, $id_shopping_basket_history) {

//       $update_query = 'update ' . TBL_SHOP_SHOPPING_BASKET_HISTORY . ' set
//       description = "' . db_escape($basket_params['description'])  . '"
//       where id_shopping_basket_history = ' . db_int($id_shopping_basket_history) . ' and
//       id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']);
//       db_query( $update_query );
       
      $update_query2 = 'update ' . TBL_SHOP_SHOPPING_BASKET . ' set
      description = "' . db_escape($basket_params['description'])  . '"
      where id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']);
      db_query( $update_query2 );
   
   }
     
   static function put_basket_version_product_list($basket_contents, $basket_params) {

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
      where id_shopping_basket_version = ' . db_int($id_shopping_basket_version);
      $basket_new_version_res = db_query( $basket_new_version );
      $basket_new_version = db_fetch_array($basket_new_version_res, 0);
      
      $FD = File_Debug::g_global();
      $FD->s(array('id_shopping_basket_version' => (int)$id_shopping_basket_version, '$basket_new_version' => $basket_new_version));
      
      return $basket_new_version;
   }
   
   
   //basket favorite
   
   static function make_new_basket_version_data($params, $product_list, $description) {

   	if( $description == '' ) $description = $params['description'];
   	$serialize = serialize($product_list);
   	
   	$insert_query = 'insert into ' . TBL_SHOP_SHOPPING_BASKET_FAVORITE . '
      	set id_client = ' . db_int($params['id_client']) . ',
     	 	id_client_user = ' . db_int($params['using_id_client_user']) . ',
      	description = "' . db_escape($description) . '",
      	serialize = "' . db_escape($serialize) . '",
      	rights_edit = "CLIENT",
      	rights_use = "CLIENT"';
   	$res = db_query( $insert_query );

   	$id_shopping_basket_favorite = (int)db_insert_id();
   
   	return $id_shopping_basket_favorite;
   }

   static function get_basket_favorite_data($id_shopping_basket_favorite) {

   
   	$query = 'select id_shopping_basket_favorite, id_client, id_client_user,
   				description, serialize, date_created, date_modified,
   			   UNIX_TIMESTAMP(date_created) as ts_created, UNIX_TIMESTAMP(date_modified) as ts_modified,
   				rights_edit, rights_use
					from ' . TBL_SHOP_SHOPPING_BASKET_FAVORITE . ' where
					id_shopping_basket_favorite = ' . db_int($id_shopping_basket_favorite);
   
//    	($result)
   	return db_fetch_array( db_query( $query ) );
   }
   
   static function get_basket_favorite_list($id_client, $id_client_user = 0) {

   	if( $id_client_user > 0 ) {
   		$where = ' ( id_client = ' . db_int($id_client) . ' AND rights_use = "CLIENT" ) OR
   				id_client_user = ' . db_int($id_client_user);
   	} else {
   		$where = ' id_client = ' . db_int($id_client);
   	}
   	
   	$query = 'select id_shopping_basket_favorite, id_client, id_client_user, 
   				description, serialize, date_created, date_modified, 
   			   UNIX_TIMESTAMP(date_created) as ts_created, UNIX_TIMESTAMP(date_modified) as ts_modified,
   				rights_edit, rights_use
					from ' . TBL_SHOP_SHOPPING_BASKET_FAVORITE . ' where
					' . $where;
   			
   	return db_result_array_full( db_query( $query ) );
   }

}

?>