<?php
/**
 * Data_Basket.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 *
 * @todo Eliminate legacy application entry point guards (`_I_INIT`) and integrate modern PSR-4 autoloading mechanisms.
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Data_Basket
 *
 * Orchestrates data access, persistence layer updates, and operational workflow history logging
 * for consumer shopping baskets, favorite product configurations, and version management.
 *
 * @todo Declare visibility modifiers explicitly (e.g., `public`, `protected`) for all class methods to adhere to current PHP programming standards.
 * @todo Move static context database operations away from raw procedural global wrappers (like `db_query`) into encapsulated, dependency-injected repository models.
 */
class Data_Basket extends Data_Order {

   /**
    * Data_Basket constructor.
    *
    * Passes initialization up to the parent data order layer orchestration framework.
    */
   function __construct() {
        //echo get_class();
      parent::__construct();
   }

   /**
    * Fetches all active shopping baskets for a client that have not been finalized into processing orders.
    *
    * @param int $id_client The primary identification key tracking target customer records.
    * @return array Matrix tracking matched customer basket parameters indexed by their database primary keys.
    *
    * @todo Add proper input argument signature and scalar return typing (`int`, `array`).
    * @todo Address the "read rights" placeholder todo to incorporate proper ACL permission scopes onto queries.
    */
   static function get_basket_chain_basket_list( $id_client ) {
      //TODO read rights
      // read good baskets
      $query = 'select id_shopping_basket, id_client, description, date_create, date_modified,
      UNIX_TIMESTAMP(date_create) as ts_create, UNIX_TIMESTAMP(date_modified) as ts_modified,
      using_id_client_user, using_session_id, using_date, UNIX_TIMESTAMP(using_date) as ts_using,
      state
      from ' . TBL_SHOP_SHOPPING_BASKET . '
      where state != "ORDER" and id_client = ' . db_int($id_client) . '';
      $basket_list = db_result_array_full_id( db_query( $query ) );
      return $basket_list;
   }

   /**
    * Permanently removes all historical tracking footprints and shopping baskets mapped to a customer.
    *
    * Checks customer existence, iterates through found elements, and counts successfully processed deletes.
    *
    * @param int $id_client Client database entity record index pointer.
    * @return int|bool Returns total number of deleted records on success, or boolean false if the client is invalid.
    *
    * @todo Standardize loose fallback types to return an integer zero or throw a clean `InvalidArgumentException`.
    */
   static function remove_permanently_basket_client( $id_client ) {
       $nr_basket = 0;

       if( (int)$id_client>0 && Framework::not_null(Data::get_client_data( (int)$id_client ) ) ) {
           $basket_list = Data::get_basket_chain_basket_list( (int)$id_client );
           foreach( $basket_list as $id_shopping_basket => $basket_params ) {
               $nr_basket += Data::remove_basket($basket_params);
           }
           return $nr_basket;
       } else {
           return false;
       }
   }

   /**
    * Retrieves isolated data parameters tracking a specific shopping basket entity index row.
    *
    * @param array $basket_params Dictionary array housing `id_client` and `id_shopping_basket` reference keys.
    * @return array Dictionary array collection mapping targeted row fields.
    */
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

   /**
    * Logs a historical transition entry recording changes in state boundaries for shopping baskets.
    *
    * @param array $basket_params Baseline reference parameters mapping data dimensions of active elements.
    * @param array $params_in Input transition matrix containing state modifiers ('now', 'new') and context records.
    * @return int Returns the auto-increment primary record key tracking the new historical log entry.
    */
   static function put_basket_history($basket_params, $params_in) {
      $mode = $params_in['now'] . '_' . $params_in['new'];
      $update_query = 'insert into ' . TBL_SHOP_SHOPPING_BASKET_HISTORY . ' set
         id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']) . ',
         id_client_user = ' . db_int($basket_params['using_id_client_user']) . ',
         id_address = ' . db_int($params_in['id_address']) . ',
         mode = "' . db_escape($mode) . '", date = now(),
         description = "' . db_escape($params_in['history_description']) . '"';
      db_query( $update_query );
      return db_insert_id();
   }

   /**
    * Pulls the earliest chronological log entry associated with a specific shopping basket resource.
    *
    * @param array $basket_params Scope selector parameter map containing target identifier metrics.
    * @return array Single row data profile capturing initial user workflow attributes.
    *
    * @todo Fix syntax error token bug `bh.id_client_user,` bh.id_address` inside query definition (notice the stray backtick).
    */
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

   /**
    * Returns the complete chronological history of state logs tracking changes to a shopping basket.
    *
    * @param array $basket_params Target tracking parameter array mapping criteria requirements.
    * @return array Collection matrix tracking sequenced log modifications.
    */
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

   /**
    * Aggregates version snapshots saved across basket iterations, computing content weights and unique line item sizes.
    *
    * @param array $basket_params Base model parameters mapping identification properties.
    * @return array Structured collection of version parameters organized chronologically.
    */
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

   /**
    * Returns all products associated with a specific saved shopping basket version snapshot.
    *
    * Normalizes records using unique composite product item hashes.
    *
    * @param array $basket_params Query boundary map containing the targeted `id_shopping_basket_version`.
    * @return array Map array tracking items indexed by unique configuration compound keys.
    */
   static function get_basket_version_product_list($basket_params) {
      $query = ' select id_shopping_basket_version, id_product, id_product_subtype,
      quantity, date_added
      from ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . ' where
      id_shopping_basket_version = ' . db_int($basket_params['id_shopping_basket_version']);
      $product_array = db_result_array( db_query( $query ) );
      $contents = array();
      foreach( $product_array as $product ) {
         $key = Data_Product::get_key_from_product_params( $product );
         $contents[$key] = array(
               'quantity' => (int)$product['quantity'],
               'id_product' => (int)$product['id_product'],
               'id_product_subtype' => (int)$product['id_product_subtype'],
               'id_shopping_basket_version' => (int)$product['id_shopping_basket_version']
               );
      }
      return $contents;
   }

   /**
    * Drops an individual item variant assignment matching dynamic configuration signatures from basket lists.
    *
    * @param string $product_key Text configuration signature token parsed to determine object criteria indexes.
    * @param array $basket_params Parent container map variables defining operational tracking constraints.
    * @return int Count of rows affected from database operations.
    *
    * @todo Clean up and refactor procedural debugging hooks (`print_debug`) into standardized PSR-3 log mechanisms.
    */
   static function remove_basket_product( $product_key, $basket_params) {
       print_debug($product_key);
      $product_params = Data_Product::get_product_params_from_key($product_key);
      $clear_query = 'delete from ' . TBL_SHOP_SHOPPING_BASKET_PRODUCT . ' where
      id_shopping_basket_version = ' . db_int($basket_params['id_shopping_basket_version']) . ' and
      id_product_subtype = ' . db_int($product_params['id_product_subtype']) . ' and
      id_product = ' . db_int($product_params['id_product']);

      db_query( $clear_query );
      $res = db_affected_rows();

      return $res;
   }

   /**
    * Provisions a new shopping basket, setting up its history trackers and initialization version records.
    *
    * @param array $basket_params Configuration payload identifying owners, sessions, and baseline descriptions.
    * @return array Compacted summary dictionary tracking both `id_shopping_basket` and `id_shopping_basket_version`.
    */
   static function create_new_basket( $basket_params ) {
      //$P = Person::g_global();

// db_transaction_start();

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

// db_transaction_end();

      return compact('id_shopping_basket', 'id_shopping_basket_version');
   }

   /**
    * Completely purges a shopping basket from the database, cascading the deletion through all associated historical logs and product mappings.
    *
    * Executed within a transactional isolation block to preserve data integrity.
    *
    * @param array $basket_params Reference target metrics tracking baseline attributes required to resolve parent items.
    * @return int Affected rows calculation summarizing absolute deletion counts returned by operations.
    *
    * @todo Remove dead code.
    */
   static function remove_basket( $basket_params) {
      // self::remove_basket_product_list($id_nr_shopping_basket);
      // self::remove_basket_pdata($id_nr_shopping_basket);
      db_transaction_start();
      //print_debug($basket_params);

         $verified_basket_version = 'select GROUP_CONCAT(id_shopping_basket_version) as list
            from ' . TBL_SHOP_SHOPPING_BASKET_VERSION . ' where
            id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']) . ' and
            id_client = ' . db_int($basket_params['id_client']) . '';
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

// print_debug(
// compact('sql_basket_version_list', 'verified_basket_version',
// 'clear_products_query', 'clear_basket_version_query', 'clear_basket_history_query', 'clear_basket_query')
// );

        $del_basket = db_affected_rows();

        db_transaction_end();

        return $del_basket;
   }

   /**
    * Proxy gateway operation targeting active session access updates.
    *
    * @param array $basket_params State definitions capturing modifications flags.
    * @return bool Returns true upon successful execution.
    */
   static function put_basket_update_lock_data($basket_params) {
       return self::put_basket_update_use_data( $basket_params );
   }

   /**
    * Refreshes dynamic runtime metrics tracking active customer assignments, modifying states, and tracking sessions.
    *
    * @param array $basket_params Parameter schema dictionary outlining target updates metrics.
    * @return bool True upon successful query execution loops.
    */
   static function put_basket_update_use_data($basket_params) {

      if( (int)$basket_params['using_id_client_user']>0 ) {
         $sql_set_m = ', date_modified = now() ';
      } else {
         $sql_set_m = '';
      }


      $update_query = 'update ' . TBL_SHOP_SHOPPING_BASKET . ' set
         using_id_client_user = ' . db_int($basket_params['using_id_client_user']) . ',
         using_session_id = "' . db_escape($basket_params['using_session_id']) . '",
         using_date = now(),
         state = "' . db_escape($basket_params['state']) . '"
         ' . $sql_set_m . ' where id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']);

      db_query( $update_query );

      return true;
   }

   /**
    * Updates descriptive annotation fields for an active shopping basket.
    *
    * @param array $basket_params Parameter dataset capturing updated descriptive texts.
    * @param int $id_shopping_basket_history Primary reference index mapping contextual logging markers (unused).
    * @return void
    *
    * @todo Remove dead code.
    */
   static function put_basket_info($basket_params, $id_shopping_basket_history) {

// $update_query = 'update ' . TBL_SHOP_SHOPPING_BASKET_HISTORY . ' set
// description = "' . db_escape($basket_params['description']) . '"
// where id_shopping_basket_history = ' . db_int($id_shopping_basket_history) . ' and
// id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']);
// db_query( $update_query );

      $update_query2 = 'update ' . TBL_SHOP_SHOPPING_BASKET . ' set
      description = "' . db_escape($basket_params['description']) . '"
      where id_shopping_basket = ' . db_int($basket_params['id_shopping_basket']);
      db_query( $update_query2 );

   }

   /**
    * Synchronizes a set of product records with a targeted shopping basket version snapshot.
    *
    * Clears existing item allocations for that version before executing sequential inserts.
    *
    * @param array $basket_contents Multi-dimensional dataset parsing product quantities and subtype specifications.
    * @param array $basket_params Scoping dictionary mapping target snapshot keys.
    * @return void
    */
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

   /**
    * Appends a new revision snapshot block onto specified baskets, logging tracking parameters via global file log tools.
    *
    * @param array $basket_params Baseline contextual mappings targeting parent tracking containers.
    * @param int $id_client_user User identity token tracking structural mod creators.
    * @return array Dictionary map collection outlining generated snapshot parameters.
    */
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

   /**
    * Serializes an active product item array configuration and stores it as a reusable, favorite shopping template.
    *
    * @param array $params Mapping dictionary establishing identity owners and client pointers.
    * @param array $product_list Dataset collection parsing line items to compile and preserve.
    * @param string $description Annotation string text label describing the favorite template.
    * @return int Returns the newly created primary record key tracking the saved template.
    */
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

   /**
    * Restores historical configurations details from individual template identifiers.
    *
    * @param int $id_shopping_basket_favorite Primary record entry code matching saved favorite profiles.
    * @return array Array structure describing target configuration records.
    */
   static function get_basket_favorite_data($id_shopping_basket_favorite) {


       $query = 'select id_shopping_basket_favorite, id_client, id_client_user,
                   description, serialize, date_created, date_modified,
                  UNIX_TIMESTAMP(date_created) as ts_created, UNIX_TIMESTAMP(date_modified) as ts_modified,
                   rights_edit, rights_use
                    from ' . TBL_SHOP_SHOPPING_BASKET_FAVORITE . ' where
                    id_shopping_basket_favorite = ' . db_int($id_shopping_basket_favorite);

       return db_fetch_array( db_query( $query ) );
   }

   /**
    * Extracts all favorite template allocations matching designated consumer visibility limitations.
    *
    * Filters templates based on corporate hierarchies or direct user account links.
    *
    * @param int $id_client Corporate account identifier mapping company nodes.
    * @param int $id_client_user Individual contact parameter context index filters. Defaults to 0.
    * @return array Multi-dimensional collection matrix containing matched template configurations.
    */
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
