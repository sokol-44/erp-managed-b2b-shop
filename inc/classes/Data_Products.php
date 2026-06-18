<?php
/**
 * Data_Products.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Data_Products
 *
 * Handles data mutations and queries for processing catalogs, structural categorization tree,
 * inventory controls, and tiered custom client pricing lists within the application framework.
 *
 * @todo Migrate static class state architecture to instance-based dependency injected Domain Services.
 * @todo Declare standard visibility attributes (public/protected/private) explicitly on all properties.
 * @todo Add native type-hintings for method arguments and formal union return scopes.
 * @todo Encapsulate raw direct database strings concat manipulations using secure Prepared Statements.
 */
class Data_Products extends Data_Basket {
   /**
    * @var array Configuration variables caching active customer contexts and custom display views.
    */
   static $Data_Products_params = array('id_client' => 0, 'client_view' => false);

   /**
    * @var int|bool The unique dynamic identifier for the active virtual directory root partition framework, or false if not assigned.
    */
   static $root_number = false;

   /**
    * Data_Products constructor.
    *
    * Sets up the baseline customer parameters structure alongside local target virtual partition locations.
    */
   function __construct() {
      parent::__construct();
      self::_load_client_params();
      self::_load_virtualdir_params();
   }

   /**
    * Resolves context access rules to determine custom user specific pricing logic displays options.
    *
    * @return void
    *
    * @todo Refactor implicit dependencies on global structural singletons like `Person::g_global()`.
    */
   static function _load_client_params() {
      $P = Person::g_global();

      if( $P->logged_in && !defined('SHOP_CLIENT_PRICE_MODE_SET') ) {
         self::$Data_Products_params['id_client'] = (int)$P->data['id_client'];
         $view_name = Data_Person::get_client_attribute((int)$P->data['id_client'], 'PRODUCT_VIEW_NAME');
         if( Framework::not_null($view_name) ) {
            self::$Data_Products_params['client_view'] = $view_name;
         } else {
             if( defined('SHOP_CLIENT_PRICE_MODE') && constant('SHOP_CLIENT_PRICE_MODE') != 'show_all' ) {
                 self::$Data_Products_params['client_view'] = constant('SHOP_CLIENT_PRICE_MODE');
             }
         }
         define('SHOP_CLIENT_PRICE_MODE_SET', true);
      }
   }

   /**
    * Binds active category constraints from virtual requests routes locations configuration setups.
    *
    * @return void
    */
   static function _load_virtualdir_params() {
      $F = Framework::g_global();

      //pasmanteria_i_dodatki_krawieckie = 1
      //produkty_medyczne = 2
      $root_number = $F->return_virtualdir_id();
      if( $root_number ) self::$root_number = $root_number;
   }

   /**
    * Resolves an array collection of categories by processing input data vectors or global query parameter definitions.
    *
    * @param array|string|bool $list_in Target item parameters list vector or string list values matrix. Defaults to false.
    * @return array Matrix array rows capturing structural database field values.
    *
    * @todo Safely remove disabled commented code fragments adjusting structural properties for Postgres frameworks.
    */
   static function get_categories_from_list( $list_in = false ) {
      $F = Framework::g_global();

      if( $list_in ) {
         if( is_array($list_in) ) {
            foreach($list_in as $val) {
               $list[] = (int)$val;
            }
         } else {
            $list_tmp = explode(',', $list_in);
            foreach($list_tmp as $val) {
               $list[] = (int)$val;
            }
         }
      } elseif( isset($F->GET['catpath']) ) {
         $list_tmp = $F->request_split_array('catpath', '_', 'GET');
         foreach($list_tmp as $val) {
            $list[] = (int)$val;
         }
      } else {
         $list = array(0);
      }

      $query =  'select * from ' . TBL_SHOP_CATEGORY . '
      where id_category IN (' . implode(',', $list) . ')
      order by FIND_IN_SET(id_category,"' . implode(',', $list) . '")';

      $result = db_query( $query );

      return db_result_array($result);

      //fix for postgresql
      //$array_res_tmp = db_result_array($result);

      // $array_res = array();
      // if( sizeof($array_res) > 1 ) {
      // foreach($list as $id_category) {
      // foreach($array_res_tmp as $key => $row) {
      // if( $row['id_category'] == $id_category ) {
      // $array_res[] = $row;
      // unset($array_res_tmp[$key]);
      // break 1;
      // }
      // }
      // }
      // } else {
      // $array_res = $array_res_tmp;
      // }

      //return $array_res;
   }

   /**
    * Recursively navigates an existing application navigation taxonomy structure map to pull down children tree nodes items.
    *
    * @param int|string $id_category Structural root criteria verification point.
    * @param array $category_tree Entire system dictionary directory map. Defaults to an empty array.
    * @return array Sequence collection list detailing unique matched folder index values.
    */
   static function get_children_of_category($id_category, $category_tree = array() ) {

       if( Framework::is_null($category_tree) ) {
           $category_tree = self::get_categorie_tree();
       }

       foreach( $category_tree as $id_category_new => $category_tree_new ) {
           if( $id_category_new == $id_category ) {
               return $category_tree_new['all_children'];
           } elseif( in_array($id_category, $category_tree_new['all_children']) !== FALSE ) {
               return self::get_children_of_category($id_category, $category_tree_new['children']);
           }
       }
       return array();
   }

   /**
    * Builds paginated lists of items filtering properties based on active criteria setups and configurations maps.
    *
    * @param int|string $id_category Reference sorting identification index criterion.
    * @param array $filters Additional filtering attributes parameter configurations array. Defaults to an empty array.
    * @param array $sort Operational dynamic ordering guidelines details. Defaults to an empty array.
    * @return array Matrix set displaying matched entity definitions attributes values rows.
    *
    * @todo Refactor out references targeting unused argument arrays (`$filters`, `$sort`) inside dynamic conditions generation blocks.
    * @todo Remove reference calls touching legacy global variables `global $category_tree;`.
    */
   static function get_categories_product_list($id_category, array $filters = array(), array $sort = array()) {
      global $category_tree;
      $F = Framework::g_global();
      $SP = SplitPage::g_global();

      $where = array('status' => 'ACTIVE');


      if( defined('SHOP_SHOW_PRODUCTS_FROM_SUBCATEGORIES') && constant('SHOP_SHOW_PRODUCTS_FROM_SUBCATEGORIES') == 'true'
              && $id_category!=0) {

          $children = array_merge(array($id_category), self::get_children_of_category((int)$id_category) );

          if( $F->is_null($children) ) $where['p2c.id_category'] = (int)$id_category;
            else $where['p2c.id_category'] = array('IN ('.implode(',',$children).')');

      } else {
         if( $id_category != 0 ) $where['p2c.id_category'] = (int)$id_category;
      }


      if( $F->not_null(self::$Data_Products_params['client_view']) ) {
          switch (constant('SHOP_CLIENT_PRICE_MODE')) {
              case 'product_with_client_price_only':
                  $query_pm = 'select distinct p.id_product, p.name, p.description, p.producer, p.catalog_index,
                     p.picture_small_url, p.picture_big_url, p.picture_id, IF( pcp.price>0, pcp.price, p.price) as price, p.vat, p.quantity, p.status
                     from ' . TBL_SHOP_PRODUCT . ' p join ' . TBL_SHOP_PRODUCT_CLIENT_PRICE . ' pcp on
                     ( p.id_product = pcp.id_product and pcp.id_client="' . (int)self::$Data_Products_params['id_client'] . '")
                     left join ' . TBL_SHOP_PRODUCT_TO_CATEGORY . ' p2c on
                     ( p.id_product = p2c.id_product )';
                  break;
              case 'product_with_client_price':
              default:
                $query_pm = 'select distinct p.id_product, p.name, p.description, p.producer, p.catalog_index,
                     p.picture_small_url, p.picture_big_url, p.picture_id, IF( pcp.price>0, pcp.price, p.price) as price, p.vat, p.quantity, p.status
                     from ' . TBL_SHOP_PRODUCT . ' p left outer join ' . TBL_SHOP_PRODUCT_CLIENT_PRICE . ' pcp on
                     ( p.id_product = pcp.id_product and pcp.id_client="' . (int)self::$Data_Products_params['id_client'] . '")
                     left join ' . TBL_SHOP_PRODUCT_TO_CATEGORY . ' p2c on
                     ( p.id_product = p2c.id_product )';
                  break;
          }
      } else {
          $query_pm = 'select distinct p.id_product, p.name, p.description, p.producer, p.catalog_index,
            p.picture_small_url, p.picture_big_url, p.picture_id, p.price as price, p.vat, p.quantity, p.status
            from ' . TBL_SHOP_PRODUCT . ' p left join ' . TBL_SHOP_PRODUCT_TO_CATEGORY . ' p2c on
             ( p.id_product = p2c.id_product )';
      }

      if( $F->not_null($where) ) $where_str = ' where ' . db_unroll_conditions($where);

      $query = $query_pm . $where_str;

      $sp_query = $SP->prepare_sql( $query );

      $res = db_query( $sp_query );
      return db_result_array($res);
   }

   /**
    * Returns processed filesystem resource string locations profiles mappings.
    *
    * @param string $raw_img Raw location address trace string input value.
    * @return string Normalized target asset location pathway string.
    *
    * @todo Implement systematic handling rules to replace old temporary code hack reminders (`FIXME - hack`).
    */
   static function get_product_image_path( $raw_img ) {
      $img_arr = explode('/', $raw_img);
      //FIXME - hack
      //return '/product_image/' . end($img_arr);
      return $raw_img;
   }

   /**
    * Alters structural inventory allocation boundaries sequentially using strict isolation locks.
    *
    * @param array $product_list Multidimensional collection mapping target processing variables details lists.
    * @return int Affected database storage tracking execution calculation results count.
    *
    * @todo Eliminate application failure crashes triggered by calling undefined parameters arrays variables structures (`$details['quantity']`).
    */
   static function change_product_quantity_list( array $product_list ) {
      $F = Framework::g_global();

      if( $F->not_null($product_list) ) {
         db_transaction_start();
         foreach( $product_list as $key => $product_params ) {
            $insert_query = 'update ' . TBL_SHOP_PRODUCT . ' set
            quantity = quantity - ' . db_int($details['quantity']) . '
            where id_product = ' . db_int($product_params['id_product']) . '';
            //         print_debug($insert_query);
            db_query( $insert_query );
         }
         //      print_debug($basket_params);
         //      print_debug($basket_contents);
         $res = db_affected_rows();
         db_transaction_end();
      }

      return $res;
   }

   /**
    * Aggregates explicit record parameters capturing traits, descriptions, and linked variants attributes collections.
    *
    * @param array $product_key_array Map collection detailing targeted verification string definitions indices.
    * @return array Formatted multi-layered structural records values data matrix.
    *
    * @todo Identify and fix notice conditions produced via typos handling variant items parameter values mapping references (`$product_array[$key]`, `$result_subtype`).
    */
   static function get_product_info_list( array $product_key_array ) {
      $F = Framework::g_global();

      if( $F->not_null(self::$Data_Products_params['client_view']) ) {
         if( defined('SHOP_CLIENT_PRICE_MODE') &&
               constant('SHOP_CLIENT_PRICE_MODE') == 'show_with_set_price_only_with') {
            $where = ' and p.id_client = ' . (int)self::$Data_Products_params['id_client'];
         } else {
            $where = ' and (p.id_client = ' . (int)self::$Data_Products_params['id_client'] . ' or p.id_client IS NULL)';
         }
         $product_from = self::$Data_Products_params['client_view'];
      } else {
         $where = '';
         $product_from = TBL_SHOP_PRODUCT;
      }

      $product_and_subproducts_array = self::get_product_and_subproducts_from_key_list( $product_key_array );

      $id_product_array = $product_and_subproducts_array['id_product'];

      if( Framework::not_null($product_and_subproducts_array) ) {

         //MYSQL group_concat( column_name )
         //POSTGRESQL = array_to_string(array_agg( column_name ),',')
         $query = 'select p.id_product, p.name, p.description, p.producer, p.catalog_index,
         p.picture_small_url, p.picture_big_url, p.picture_id, p.price, p.vat, p.quantity, p.status,
         group_concat(p2c.id_category) as id_category_list
         from ' . $product_from . ' p left join ' . TBL_SHOP_PRODUCT_TO_CATEGORY . ' p2c on
         ( p.id_product = p2c.id_product )
         where p.status = "ACTIVE" and
         p.id_product IN (' . implode(',', $id_product_array) . ') ' . $where . '
         group by p.id_product';
         $result = db_query( $query );
         $product_list_raw = db_result_array( $result );

         $product_subtypes = array();
         if( defined('TBL_SHOP_PRODUCT_SUBTYPE') && TBL_SHOP_PRODUCT_SUBTYPE=='') {
            $query_subtype = 'select pst.id_product_subtype, pst.id_product, pst.description,
            pst.picture_small_url, st.picture_big_url, pst.picture_id, pst.price_diff, pst.status
            from ' . TBL_SHOP_PRODUCT_SUBTYPE . ' pst
            where pst.status = "ACTIVE" and
            pst.id_product IN (' . implode(',', $id_product_array) . ')' .
            ' order by pst.description';
            $result_subtype = db_query( $query_subtype );
            $product_subtype_list = db_result_array_full( $result_subtype );
         }

         $product_list = array();
         foreach( $product_key_array as $key) {
            $product_param = self::get_product_params_from_key($key);

            $product_ret  = self::_get_product_data_from_array($product_list_raw, $product_param);

            if( $F->not_null($product_ret) ) {
               $product_list[$key] = $product_ret;

               if( $product_ret['id_product_subtype'] > 0 ) {
                  $product_subtype = self::_get_product_subtype_data_from_array( $product_subtype_list, $product_ret );
                  $product_list[$key] = self::_get_merge_info_subtype($product_array[$key], $product_subtype);
               }
            } else {
               continue;
            }
         }
         return $product_list;
      } else {
         return array();
      }
   }

   /**
    * Appends variant property parameters metadata specifications over parent inventory object hashes.
    *
    * @param array $product_array Primary baseline mapping items parameters array data.
    * @param array $product_subtype Child modifications elements configurations array list.
    * @return array Overwritten merged system traits dictionary fields.
    */
    static function _get_merge_info_subtype($product_array, $product_subtype) {
      $F = Framework::g_global();
      if( !$F->not_null($product_subtype['description']) )
         $product_array['description'] .= $product_subtype['description'];

      if( !$F->not_null($product_subtype['picture_small_url']) )
         $product_array['picture_small_url'] = $product_subtype['picture_small_url'];

      if( !$F->not_null($product_subtype['picture_big_url']) )
         $product_array['picture_big_url'] = $product_subtype['picture_big_url'];

      if( !$F->not_null($product_subtype['picture_id']) )
         $product_array['picture_id'] = $product_subtype['picture_id'];

      if( !$F->not_null($product_subtype['price_diff']) )
         $product_array['price'] += $product_subtype['price_diff'];

      return $product_array;
    }

   /**
    * Loops through matrix collections to fetch targeted subset structures definitions variables.
    *
    * @param array $product_subtype_list Un-sorted compilation data rows mapping variant parameters types.
    * @param array $product_param Target lookup specifications constraints map dictionary values lists.
    * @return array|null Returns matched subcategory traits properties list array or null if undetected.
    */
   static function _get_product_subtype_data_from_array( $product_subtype_list, $product_param ) {
      foreach( $product_subtype_list as $product_subtype ) {
         if( (int)$product_subtype['id_product_subtype'] == (int)$product_param['id_product_subtype'] ) {
            return $product_subtype;
         }
      }
   }

   /**
    * Builds standard uniform configuration parameter data arrays out from raw query results maps.
    *
    * @param array $product_list_raw Raw dataset rows collection matrix from database storage layers.
    * @param array $product_param Specific identifier lookup elements indicators tracking settings.
    * @return array Re-arranged clean structural properties map.
    */
   static function _get_product_data_from_array( $product_list_raw, $product_param ) {

      $product_ret = array();
      foreach( $product_list_raw as $product_info ) {
         if( (int)$product_info['id_product'] == (int)$product_param['id_product'] ) {
            $id_product_subtype = isset($product_param['id_product_subtype'])?(int)$product_param['id_product_subtype']:0;
            $product_ret = array('id_product' => (int)$product_param['id_product'],
                  'id_product_subtype' => (int)$id_product_subtype,
                  'name' => $product_info['name'],
                  'description' => $product_info['description'],
                  'producer' =>$product_info['producer'],
                  'catalog_index' => $product_info['catalog_index'],
                  'picture_small_url' => $product_info['picture_small_url'],
                  'picture_big_url' => $product_info['picture_big_url'],
                  'picture_id' => $product_info['picture_id'],
                  'price' => $product_info['price'],
                  'vat' => $product_info['vat'],
                  'quantity' => $product_info['quantity']
            );
         }
      }
      return $product_ret;
   }

   /**
    * Queries specific customer tier values listings sorting by offset constraints indicators markers.
    *
    * @param int $id_product_start Core page record offset key indicator pointer index. Defaults to 0.
    * @param int $id_client Target client reference identification tracker code. Defaults to 0.
    * @param int $length Operational maximum capacity boundary rows limit definition parameter. Defaults to 1.
    * @param string $where Supplementary filtering parameters phrases injection block strings. Defaults to empty string.
    * @return array Matrix dataset documenting specific items properties metrics rows.
    *
    * @todo Resolve processing query logic defects where comparison routines use mismatched identifier values variables mappings (`db_int($id_client)` vs baseline tracking offsets).
    */
   static function getProductClientPriceList( $id_product_start = 0, $id_client = 0, $length = 1, $where = '' ) {
      list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);

      $query = 'select pcp.id_product, pcp.id_client, pcp.price, pcp.vat
      from ' . TBL_SHOP_PRODUCT_CLIENT_PRICE . ' pcp
      where pcp.id_client = ' . db_int($id_client) . ' and pcp.id_product ' . $comparision_dir . db_int($id_client) . $where . '
      ORDER BY pcp.id_client ' . $order_dir . ' LIMIT '. db_int($length);
      add_to_fp($query);
      $result = db_query( $query );
      return db_result_array_full($result);
   }

   /**
    * Extracts custom adjusted customer item valuation structures organized across client parameter indexes.
    *
    * @param int $id_client Core profile reference tracking index context identifier values parameter settings. Defaults to 0.
    * @param int $id_product_start Cursor indicator tracker position reference. Defaults to 0.
    * @param int $length Capacity collection output boundaries thresholds settings definition. Defaults to 1.
    * @param string $where Optional ad-hoc logical condition specifications. Defaults to empty string.
    * @return array Collection listing items configuration details structures array rows.
    */
   static function getClientPriceProductList( $id_client = 0, $id_product_start = 0, $length = 1, $where = '' ) {
      list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);

      $query = 'select pcp.id_product, pcp.id_client, pcp.price, pcp.vat
      from ' . TBL_SHOP_PRODUCT_CLIENT_PRICE . ' pcp
      where pcp.id_client = ' . db_int($id_client) . ' and pcp.id_product ' . $comparision_dir . db_int($id_product_start) . $where . '
      ORDER BY pcp.id_product ' . $order_dir . ' LIMIT '. db_int($length);
      add_to_fp($query);
      $result = db_query( $query );
      return db_result_array_full($result);
   }

   /**
    * Returns product summaries records explicitly assigned to particular catalog taxonomy paths.
    *
    * @param int $id_category Target dynamic tracking catalog category structural directory location identifier value. Defaults to 0.
    * @param int $id_product_start Entry page navigation tracking baseline pointer marker indexing context. Defaults to 0.
    * @param int $length Total result item capacities thresholds definitions options constraints. Defaults to 1.
    * @param string $where Supplementary data properties criteria constraints filters strings. Defaults to empty string.
    * @return array Rows metrics list collection mapping standard framework dataset indicators.
    */
   static function getProductListFromCategory( $id_category = 0, $id_product_start = 0, $length = 1, $where = '' ) {
      list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);

      $query = 'select p.id_product, p.name, p.description, p.producer, p.catalog_index,
      p.picture_small_url, p.picture_big_url, p.picture_id, p.price, p.vat, p.quantity, p.status
      from ' . TBL_SHOP_PRODUCT . ' p left join ' . TBL_SHOP_PRODUCT_TO_CATEGORY .' p2c on
      ( p.id_product = p2c.id_product and p2c.id_category = ' . db_int($id_category) . ')
      where p.id_product ' . $comparision_dir . db_int($id_product_start) . $where . '
      ORDER BY p.id_product ' . $order_dir . ' LIMIT '. db_int($length);
      $result = db_query( $query );
      return db_result_array_full($result);
   }

   /**
    * Resolves baseline catalog records collections fields tracking options direct from core index mappings.
    *
    * @param int $id_product_start Sorting reference indication parameter start point index. Defaults to 0.
    * @param int $length Total listing capacity volume limits boundaries options indicators parameters. Defaults to 1.
    * @param string $where Custom dynamic condition formatting filters strings components blocks. Defaults to empty string.
    * @return array Collection matching properties dataset parameters logs array.
    */
   static function getProductList( $id_product_start = 0, $length = 1, $where = '' ) {
      list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);

      $query = 'select p.id_product, p.name, p.description, p.producer, p.catalog_index,
      p.picture_small_url, p.picture_big_url, p.picture_id, p.price, p.vat, p.quantity, p.status
      from ' . TBL_SHOP_PRODUCT . ' p
      where p.id_product ' . $comparision_dir . db_int($id_product_start) . $where . '
      ORDER BY p.id_product ' . $order_dir . ' LIMIT '. db_int($length);
      $result = db_query( $query );
      return db_result_array_full($result);
   }

   /**
    * Invokes storage procedural actions to completely destroy specific category layout definitions trees.
    *
    * @param array $param_array Configuration values attributes map containing reference target indices.
    * @return array Verification operational response message tracking markers hashes.
    *
    * @todo Fix syntactic execution bugs caused by parsing extraction calls omitting the structural dollar notation token (`extract( param_array )`).
    */
   static function doCategoryDelete($param_array) {

       extract( $param_array );

      $query = 'select "' . db_int($id_category) . '" as id_one,
          "" as additional_data,
          b_func_category_delete("' . db_int($id_category) . '") as status';
      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }

   /**
    * Overwrites text parameter attributes tracking specific dynamic catalog structure node elements.
    *
    * @param array $param_array Parameter properties updates collection array specifications checklist.
    * @return array Database procedural metrics collection tracking outcomes logs matrix.
    */
   static function doCategoryEdit($param_array) {

       extract( $param_array );

      $query = 'select "' . db_int($id_category) . '" as id_one,
          "" as additional_data,
          b_func_category_change("' . db_int($id_category) . '", "' . db_int($id_category_parent) . '", "' . db_int($sort_order) . '","' . db_int($root_number) . '",
          "' . db_escape($name). '", "' . db_escape($description). '") as status';
      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }

   /**
    * Inserts fresh structural taxonomy paths indicators inside relational data indexes indices spaces.
    *
    * @param array $param_array Properties tracking parameters collection details mapping criteria list.
    * @return array Core entry registration data operational response metrics hash map.
    */
   static function doCategoryAdd($param_array) {

       extract( $param_array );

      $query = 'select "' . db_int($id_category) . '" as id_one,
          "" as additional_data,
          b_func_category_add("' . db_int($id_category) . '", "' . db_int($id_category_parent) . '", "' . db_int($sort_order) . '","' . db_int($root_number) . '",
          "' . db_escape($name). '", "' . db_escape($description). '") as status';
      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }

   /**
    * Manages structural update configurations writing catalog categories parameters records safely.
    *
    * @param array $param_array Selection boundary properties array map tracking application options.
    * @return array Performance state validation analysis hashes logs context list.
    */
   static function doCategoryAddOrUpdate($param_array) {

      extract( $param_array );

      $query = 'select "' . db_int($id_category) . '" as id_one,
          "" as additional_data,
          b_func_category_set("' . db_int($id_category) . '", "' . db_int($id_category_parent) . '", "' . db_int($sort_order) . '","' . db_int($root_number) . '",
          "' . db_escape($name). '", "' . db_escape($description). '") as status';
      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }

   /**
    * Registers basic product data items parameters within primary index mappings blocks directly.
    *
    * @param array $param_array Configuration values updates criteria dictionary fields list array map.
    * @return array Processing result logs metrics indicators details array.
    */
   static function doProductAdd($param_array) {

       extract( $param_array );

      $query = 'select "' . db_int($id_product) . '" as id_one,
          "" as additional_data,
          b_func_product_add("' . db_int($id_product) . '", "' . db_escape($name). '", "' . db_escape($description). '",
          "' . db_escape($producer). '", "' . db_escape($catalog_index). '",
          "' . db_escape($picture_small_url). '", "' . db_escape($picture_big_url). '", "' . db_escape($picture_id). '",
          "' . db_escape($price). '", "' . db_escape($vat) . '", "' . db_escape($quantity) . '", "' . db_escape($status) . '") as status';
      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }

   /**
    * Adjusts core asset parameters mapping properties criteria matching individual catalog records IDs.
    *
    * @param array $param_array Update attributes parameters specifications mapping dictionary values array list.
    * @return array Performance status operational data metrics confirmations logs array.
    *
    * @todo Validate typing errors mapping fields inputs inside procedural calls references (e.g. `$quantity_salt`).
    */
   static function doProductChange($param_array) {

       extract( $param_array );

      $query = 'select "' . db_int($id_product) . '" as id_one,
          "" as additional_data,
          b_func_product_change("' . db_int($id_product) . '", "' . db_escape($name). '", "' . db_escape($description). '",
          "' . db_escape($producer). '", "' . db_escape($catalog_index). '",
          "' . db_escape($picture_small_url). '", "' . db_escape($picture_big_url). '", "' . db_escape($picture_id). '",
          "' . db_escape($price). '", "' . db_escape($vat) . '", "' . db_escape($quantity_salt) . '", "' . db_escape($status) . '") as status';
      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }

   /**
    * Directs processing instructions executing dynamic update mutations matching product definitions properties fields.
    *
    * @param array $param_array Configuration constraints checklist options metrics vector hash map.
    * @return array Output operational tracking validation criteria confirmation properties list.
    */
   static function doProductAddOrUpdate($param_array) {

       extract( $param_array );

      $query = 'select "' . db_int($id_product) . '" as id_one,
          "" as additional_data,
          b_func_product_set("' . db_int($id_product) . '", "' . db_escape($name). '", "' . db_escape($description). '",
          "' . db_escape($producer). '", "' . db_escape($catalog_index). '",
          "' . db_escape($picture_small_url). '", "' . db_escape($picture_big_url). '", "' . db_escape($picture_id). '",
          "' . db_escape($price). '", "' . db_escape($vat) . '", "' . db_int($quantity) . '", "' . db_escape($status) . '") as status';
      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }

   /**
    * Clears custom tiered price records mapped to single customer references values.
    *
    * @param int|string $id_client Reference identity targeting business profiles context tracking indexes.
    * @return int Affected database storage layout rows metrics tally.
    */
   static function doProductClientPriceClean( $id_client ) {
//       $query = 'select "' . db_int($id_client) . '" as id_one, "" as additional_data,
//        b_func_product_client_price_all_del("' . db_int($id_client) . '") as status';
      $query = 'delete from ' . TBL_SHOP_PRODUCT_CLIENT_PRICE . ' where id_client = "' . db_int($id_client) . '"';
      add_to_fp($query);
      $result = db_query( $query );
      return db_affected_rows();
   }

   /**
    * Commits collection listings of custom calculations entries using automated query iteration loops.
    *
    * @param int|string $id_client Target customer mapping unique key identifier parameter index value.
    * @param array $id_product_array Multidimensional configuration updates properties hashes vector maps list.
    * @return int Aggregated operational data written volume records rows confirmation index metrics.
    *
    * @todo Rectify tracking loops bugs produced via references inspecting un-incremented loops counters flags (`$count`).
    */
   static function setClientProductPriceList( $id_client, $id_product_array) {
//       $query = 'select "' . db_int($id_product) . '" as id_one, "' . db_int($id_client) . '" as id_two,
//        "" as additional_data,
//        b_func_product_client_price_add("' . db_int($id_product) . '", "' . db_int($id_client) . '", "' . db_float($price) . '", "' . db_float($vat) . '") as status';

      $query_start='insert ignore into  ' . TBL_SHOP_PRODUCT_CLIENT_PRICE . ' (`id_product`, `id_client`, `price`) values ';

      add_to_fp('setClientProductPriceList');

      $val_array = array();
      $count=1;
      $ins_count=0;
      $id_client_db = db_int($id_client);
      foreach( $id_product_array as $prod_val ) {
         $val_array[] = '(' . db_int($prod_val['id_product']) . ',' . $id_client_db . ',' . db_float($prod_val['price']) . ' )';
         if( $count%1000 == 0 ) {
            $query = $query_start . implode(',', $val_array);
            $val_array = array();

            add_to_fp($query);
            $result = db_query( $query );
            $ins_count += db_affected_rows();
         }
      }

      if( sizeof($val_array) > 0 ) {
         $query = $query_start . implode(',', $val_array);

         add_to_fp($query);
         $result = db_query( $query );
         $ins_count += db_affected_rows();
      }

      return $ins_count;
   }

   /**
    * Assigns specific customized pricing layouts configurations directly matching client identification profiles.
    *
    * @param int|string $id_product Target item structural identification code locator index.
    * @param int|string $id_client Target customer selection context indicator index parameter.
    * @param float|int|string $price Numerical cost measure unit configurations scale settings value.
    * @param float|int|string $vat Financial percentage tax metric descriptor parameters value.
    * @return array Dynamic procedural tracking validation logs details array records maps list.
    */
   static function setProductClientPrice( $id_product, $id_client, $price , $vat ) {
      $query = 'select "' . db_int($id_product) . '" as id_one, "' . db_int($id_client) . '" as id_two,
       "" as additional_data,
       b_func_product_client_price_add("' . db_int($id_product) . '", "' . db_int($id_client) . '", "' . db_float($price) . '", "' . db_float($vat) . '") as status';
      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }

   /**
    * Generates structural relationships links mapping specific database inventory rows to taxonomy configurations directories.
    *
    * @param int|string $id_product Primary registration data model access marker tracking index pointer.
    * @param int|string $id_category Structural categorization folder assignment reference key parameter index value.
    * @return array Output operational performance indicators metadata matrix.
    */
   static function setProduct2Category( $id_product, $id_category) {
      $query = 'select "' . db_int($id_product) . '" as id_one, "' . db_int($id_category) . '" as id_two,
       "" as additional_data,
       b_func_product_to_category_add("' . db_int($id_product) . '", "' . db_int($id_category) . '") as status';
      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }


   /**
    * Extracts simple classification parameters lists linked directly across target item data fields rows.
    *
    * @param int $id_product_start Pagination record tracking offset location key index indicator. Defaults to 0.
    * @param int $length Operational maximum boundary values allocation bounds limit constraints parameter. Defaults to 0.
    * @return array Collection mapping matching rows properties results dataset logs.
    *
    * @todo Resolve runtime failure notices generated via call attempts scanning undefined parameters elements variables (`$where`).
    */
   static function getShopProductAttributeList( $id_product_start = 0, $length = 0 ) {
       list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);
       $query = 'select `id_product`, `type`, `val`
             from ' . TBL_SHOP_PRODUCT_ATTRIBUTES . ' pa
        where pa.id_product ' . $comparision_dir . db_int($id_product_start) . $where . '
      ORDER BY pa.id_product ' . $order_dir . ' LIMIT ' . db_int($length);
       add_to_fp('$query ' . $query);
       $result = db_query( $query );
       return db_result_array_full($result);
   }

   /**
    * Writes technical attributes characteristics logs matching specific catalog items definitions criteria tracking layouts.
    *
    * @param array $param Core configuration parameter updates description matrices arrays properties checklist.
    * @return array Operations metadata compliance indicators logs reflecting compilation profiles states.
    */
   static function doShopProductAttributeAddOrUpdate( $param ) {

       $query_prod = 'select id_product from ' . TBL_SHOP_PRODUCT . ' where id_product = "' . db_int($param['id_product']) . '"';
       $result_prod = db_query( $query_prod );
       if( db_rows($result_prod) == 0 ) return array_merge($param, array('status' => 'ERROR,PRODUCT_DONT_EXIST'));

       $query = 'INSERT INTO ' . TBL_SHOP_PRODUCT_ATTRIBUTES . ' (`id_product`, `type`, `val`)
               VALUES ("' . db_int($param['id_product']) . '", "' . db_escape($param['type']) . '",
                         "' . db_escape($param['val']) . '")
                       ON DUPLICATE KEY UPDATE `val` = "' . db_escape($param['val']) . '"';

       add_to_fp($query);
       $result = db_query( $query );
       $ar = db_affected_rows( $result );

       if( $ar == 1 ) return array_merge($param, array('status' => 'SUCCESS,NEW'));
       elseif( $ar == 2 ) return array_merge($param, array('status' => 'SUCCESS,EXIST'));
       else return array_merge($param, array('status' => 'ERROR,UNKNOWN'));
   }

   /**
    * Scans available databases storage spaces tracking matches within specified properties values records loops phrases.
    *
    * @param int $length Volumetric capacity constraints options pagination boundaries thresholds indicators. Defaults to 1.
    * @param string $where Custom conditional filters segments parsing string blocks input. Defaults to empty string.
    * @return array Collection array rows detailing query results properties attributes rows list maps.
    *
    * @todo Identify and fix application errors caused by reading unassigned dynamic indicators tracking structures (`$comparision_dir`, `$id_product_start`, `$order_dir`).
    */
   static function get_product_search( $length = 1, $where = '' ) {
      ///list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);

      $query = 'select p.id_product, p.name, p.description, p.producer, p.catalog_index, p.picture_small_url,
      p.picture_big_url, p.picture_id, p.price, p.vat, p.quantity, p.status
      from ' . TBL_SHOP_PRODUCT . ' p
      where p.id_product ' . $comparision_dir . db_int($id_product_start) . $where . '
      ORDER BY p.id_product ' . $order_dir . ' LIMIT ' . db_int($length);
      $result = db_query( $query );

      return db_result_array_full($result);
   }

   /**
    * Assigns explicit file management pathways tracking records against asset variables configurations setups.
    *
    * @param array $product_info Hash array capturing single target item context details traits.
    * @return array|bool Address mappings path location collection criteria hashes, or false on missing options.
    */
   static function get_product_image_type( $product_info ) {
      $image_type = array('small_image_path' => false, 'big_image_path' => false);

      if( class_exists('Data_Picture') &&
            Framework::not_null($product_info['picture_id']) &&
            Data::check_picture_exist((int)$product_info['picture_id']) ) {

         $image_type['small_image_path'] = Data::get_picture_id_link($product_info['picture_id'], 'SMALL');
         //$image_type['small_image_path'] = Framework::image_db(($product_info['picture_id']), 'SMALL', $product_info['name']);
         $image_type['big_image_path'] = Data::get_picture_id_link($product_info['picture_id'], 'NORMAL');

      } elseif( Framework::not_null( $product_info['picture_small_url'] ) ) {
         $image_type['small_image_path'] = Data::get_product_image_path( $product_info['picture_small_url'] );
         $image_type['big_image_path'] = Data::get_product_image_path( $product_info['picture_big_url'] );
      } else {
         $image_type = false;
      }
      return $image_type;
   }

   /**
    * Builds a multi-layered description matrix mapping every configuration variant matching a unique product reference identifier value.
    *
    * @param int|string $id_product Core primary registration item parameter index identifier. Defaults to 0.
    * @return array|bool Consolidated structural traits criteria list hashes, or false if queries fail validation criteria.
    *
    * @todo Fix bugs where array processing statements reference typo indices parameters maps (e.g. `$result_subtype`).
    */
   static function get_product_info( $id_product = 0 ) {
      $F = Framework::g_global();

      if( $F->not_null(self::$Data_Products_params['client_view']) ) {
         switch (constant('SHOP_CLIENT_PRICE_MODE')) {
             case 'product_with_client_price_only':
                 $where = ' and p.id_client = ' . (int)self::$Data_Products_params['id_client'];
                 $product_from = self::$Data_Products_params['client_view'];
                 break;
             case 'product_with_client_price':
                 $where = ' and (p.id_client = ' . (int)self::$Data_Products_params['id_client'] . ' or p.id_client IS NULL)';
                 $product_from = self::$Data_Products_params['client_view'];
                 break;
             default:
                    $where = '';
                 $product_from = TBL_SHOP_PRODUCT;
         }
      } else {
         $where = '';
         $product_from = TBL_SHOP_PRODUCT;
      }

      //MYSQL group_concat( column_name )
      //POSTGRESQL = array_to_string(array_agg( column_name ),',')
      $query = 'select p.id_product, p.name, p.description, p.producer, p.catalog_index,
      p.picture_small_url, p.picture_big_url, p.picture_id, p.price, p.vat, p.quantity, p.status,
      group_concat(p2c.id_category) as id_category_list
      from ' . $product_from . ' p left join ' . TBL_SHOP_PRODUCT_TO_CATEGORY . ' p2c on
      ( p.id_product = p2c.id_product )
      where p.id_product = ' . (int)$id_product . $where . ' group by p.id_product';

      $result = db_query( $query );
      if( db_rows($result) == 0 ) return false;
      $product_info = db_fetch_array( $result );
      $product_info['subtype'] = array();

      if( defined('TBL_SHOP_PRODUCT_SUBTYPE') && TBL_SHOP_PRODUCT_SUBTYPE=='') {
         $query_subtypes = 'select pst.id_product_subtype, pst.id_product, pst.description,
         pst.picture_small_url, st.picture_big_url, pst.picture_id, pst.price_diff, p.status
         from ' . TBL_SHOP_PRODUCT_SUBTYPE . ' pst where pst.status = "ACTIVE" and
         pst.id_product = ' . (int)$id_product . ' order by pst.description';
         $result_subtypes = db_query( $query_subtypes );
         $product_subtypes = db_result_array_full( $result_subtype );
         foreach( $product_subtypes as $subtype ) {
            $product_info['subtype'][$subtype['id_product_subtype']] = $subtype;
         }
      }
      return $product_info;
   }

   /**
    * Filters available catalog entries matrices returning structural database configurations rows arrays lists.
    *
    * @param array $filters Selection guidelines parameter configurations array descriptions keys. Defaults to an empty array.
    * @param array $sort Operational dynamic ordering checklist context rules parameters options. Defaults to an empty array.
    * @return array Compilation outcome records details hashes displaying tracked system traits.
    */
   static function get_search_product_list(array $filters = array(), array $sort = array()) {
      global $category_tree;
      $F = Framework::g_global();
      $SP = SplitPage::g_global();

      if( false && SHOW_PRODUCTS_FROM_SUBCATEGORIES == 'true' ) {
         // TODO
         // Show products from subcategories
      } else {
         $where = $filters;
         $where['p.status'] = 'ACTIVE';

         if( $F->not_null($where) ) $where_str = ' where ' . db_unroll_conditions($where);

         if( $F->not_null(self::$Data_Products_params['client_view']) ) {
             switch (constant('SHOP_CLIENT_PRICE_MODE')) {
                 case 'product_with_client_price_only':
                     $query_pm = 'select distinct p.id_product, p.name, p.description, p.producer, p.catalog_index,
                         p.picture_small_url, p.picture_big_url, p.picture_id, pcp.price, p.vat, p.quantity, p.status
                           from `shop_product`  `p` join `shop_product_client_price` `pcp`
                         on (`p`.`id_product` = `pcp`.`id_product` AND pcp.id_client="' . (int)self::$Data_Products_params['id_client'] . '")';
                     break;
                 case 'product_with_client_price':
                     $query_pm = 'select distinct p.id_product, p.name, p.description, p.producer, p.catalog_index,
                         p.picture_small_url, p.picture_big_url, p.picture_id, IFNULL( pcp.price, p.price) as price, p.vat, p.quantity, p.status
                           from `shop_product`  `p` left outer join `shop_product_client_price` `pcp`
                         on (`p`.`id_product` = `pcp`.`id_product` AND (
                             pcp.id_client="' . (int)self::$Data_Products_params['id_client'] . '" OR pcp.id_client IS NULL ))';
                     break;
                 default:
                     $query_pm = 'select distinct p.id_product, p.name, p.description, p.producer, p.catalog_index,
                            p.picture_small_url, p.picture_big_url, p.picture_id, p.price, p.vat, p.quantity, p.status
                          FROM `shop_product` `p`';
                     break;
             }
         } else {
             $query_pm = 'select distinct p.id_product, p.name, p.description,  p.producer, p.catalog_index,
                    p.picture_small_url, p.picture_big_url, p.picture_id, p.price, p.vat, p.quantity, p.status
                  FROM `shop_product` `p`';
         }

         $query = $query_pm . $where_str;

         $sp_query = $SP->prepare_sql( $query );
      }
      $res = db_query( $sp_query );
      return db_result_array($res);
   }

   /**
    * Returns product summaries volumes counts distributed directly along catalog structural directories pathways maps.
    *
    * @param array $filters Processing subset requirements attributes lists parameters configurations map.
    * @return array Matrix collection array rows details summarizing matched directories items totals.
    */
   static function get_categories_list( array $filters ) {
      $F = Framework::g_global();


      $where = array();

      $product_from = TBL_SHOP_PRODUCT;

      if( defined('SHOP_CLIENT_PRICE_MODE') &&  constant('SHOP_CLIENT_PRICE_MODE') == 'show_with_set_price_only_with') {
          $query_pm = 'SELECT p2c.id_category, p2c.id_product
                   FROM `shop_product_to_category` `p2c` JOIN
                   `shop_product`  `p` ON (`p2c`.`id_product` = `p`.`id_product` AND p.status = "ACTIVE") JOIN
                   `shop_product_client_price` `pcp` on (`p`.`id_product` = `pcp`.`id_product` AND pcp.id_client="' . (int)self::$Data_Products_params['id_client'] . '")';
      } else {
          $query_pm = 'SELECT p2c.id_category, p2c.id_product
                  FROM `shop_product_to_category` `p2c` JOIN
                  `shop_product`  `p` ON (`p2c`.`id_product` = `p`.`id_product` AND p.status = "ACTIVE")';
      }

      $where_root_number = '';
      if( self::$root_number ) {
         $where_root_number = ' where c.root_number = ' . db_int(self::$root_number);
      }

      //TODO p.quantity options
      //AND p.quantity > 0

      $query = 'SELECT c.id_category, c.name, c.description, c.id_category_parent,
      COUNT(distinct p2c.id_product) AS products_in_category, c.sort_order
         FROM ' . TBL_SHOP_CATEGORY . ' c LEFT OUTER JOIN (' . $query_pm . ') p2c
         ON (c.id_category = p2c.id_category) ' . $where_root_number . '
         GROUP BY c.id_category, c.name, c.description, c.id_category_parent, c.sort_order
         ORDER BY c.sort_order, c.name';

      $res = db_query( $query );
      return db_result_array($res);
   }

   /**
    * Returns category directories structures trees options.
    *
    * @param array $filters Configurations restrictions variables criteria maps checking items list. Defaults to an empty array.
    * @param bool $purge_empty Filters configuration rules indicators controlling empty node logic loops options. Defaults to false.
    * @return array Hierarchical taxonomy model mapping parameters compilation dictionaries tree options.
    */
   static function get_categorie_tree( $filters = array(), $purge_empty = false ) {
      $category_list = self::get_categories_list($filters);
      $category_tree = self::__categories_make_tree($category_list);

      //if( $purge_empty )
      return $category_tree;

   }

   /**
    * Segregates serialized combined product lookup arrays structures splitting them into individual context integers.
    *
    * @param array $product_key_array Dynamic token identifiers components package lists variables matrix vector.
    * @return array Multi-tier configuration indices capturing explicit asset scopes indicators details.
    */
   static function get_product_and_subproducts_from_key_list( $product_key_array ) {
      $product_and_subproducts_array = array(
            'id_product' => array(),
            'id_product_subtype' => array(),
            'subtype2product' => array(),
             );
      foreach( $product_key_array  as $product_key ) {
         $product_params = self::get_product_params_from_key( $product_key );

         if( !array_search($product_params['id_product'], $product_and_subproducts_array['id_product']) ) {
            $product_and_subproducts_array['id_product'][] = $product_params['id_product'];
         }
         if( isset($product_params['id_product_subtype'])  ) {
            $product_and_subproducts_array['subtype2product'][$product_params['id_product_subtype']] = $product_params['id_product'];
            if( !array_search($product_params['id_product_subtype'], $product_and_subproducts_array['id_product_subtype']) ) {
               $product_and_subproducts_array['id_product_subtype'][] = $product_params['id_product_subtype'];
            }
         }
      }

      return $product_and_subproducts_array;
   }

   /**
    * Builds unique standard serialized string format keys parameters matching elementary inputs details attributes lists.
    *
    * @param array $product_params Attribute configuration properties values indices context definitions list.
    * @return string Serialized identification format layout key indicator token.
    */
   static function get_key_from_product_params( $product_params ) {
      $key = $product_params['id_product'] .
      (((int)$product_params['id_product_subtype']>0)?'_' . (int)$product_params['id_product_subtype']:'');
      return $key;
   }

   /**
    * Breaks up unique custom combined tracking codes strings recovering distinct reference parameters numbers list.
    *
    * @param string $key Serial string layout format identifier sequence.
    * @return array Indices properties map dictionary values capturing individual identity elements components.
    */
   static function get_product_params_from_key( $key ) {
      $product_params = array('id_product' => 0, 'id_product_subtype' => 0);
      $res = explode('_', $key);
      $product_params['id_product'] = (int)$res['0'];
      if( isset($res['1']) ) $product_params['id_product_subtype'] = (int)$res['1'];
      return $product_params;
   }

   /**
    * Trims hierarchical database elements structures to strip out structural folder paths lacking active items.
    *
    * @param int|string $id_category_parent Reference identifier pointer location mapping context validation marker index.
    * @param array $local_tree Multi-dimensional dynamic nested database segments rows compilation array.
    * @return array Pruned directory subset choices array detailing validated elements options.
    *
    * @todo Clean up execution processing state anomalies generated via updates touching un-declared tracking variables flags (`$category_purged`).
    */
   private static function __categories_purge_empty ($id_category_parent, $local_tree) {
      $tree = array();

      foreach($local_tree as $key => $categories) {
         if( $id_category_parent == $categories['id_category_parent'] ) {
            if($categories['products_in_category'] > 0 ||
                  (is_array($categories['children']) && sizeof($categories['children'])>0) ) {
               $tree[$key] = $categories;
               if( is_array($categories['children']) )
                  $tree[$key]['children'] =  __categories_purge_empty($id_category_parent, $categories['children']);
            } else {
               $category_purged = true;
            }
         }
      }
      return $tree;
   }

   /**
    * Compiles linear SQL results listing datasets into structured multidimensional tree model components.
    *
    * @param array $category_list Linear collection index array row values from relational query instances.
    * @param int $level Current depth level offset calculation tracking configuration measure indicator value. Defaults to 0.
    * @param int|string $id_category_parent Reference parent index identifier mapping context target. Defaults to 0.
    * @param string $path Unique serialization breadcrumb link sequence code tracker layout formatting. Defaults to empty string.
    * @return array Hierarchical taxonomy properties representation matrix configuration option tree.
    */
   private static function __categories_make_tree ($category_list, $level = 0, $id_category_parent = 0, $path = '') {

      if( $id_category_parent > 0 ) {
         $path .= $id_category_parent . '_';
      }

      $tree = array();

      foreach($category_list as $category) {
          if( $category['id_category'] == 0 ) continue;

         if( $id_category_parent == $category['id_category_parent'] ) {
            $products_in_subcategories = 0;

            $children = self::__categories_make_tree($category_list, ($level+1), $category['id_category'], $path);
            $all_children = array_keys($children);
            foreach($all_children as $children_keys) {
                foreach( $children[$children_keys]['all_children'] as $key_chl ) {
                    $all_children[] = $key_chl;
                }
                //$all_children = array_unique($all_children);
               //$all_children = array_unique(array_merge($all_children, $children[$children_keys]['children'] ));
               $products_in_subcategories += $children[$children_keys]['products_in_category'] + $children[$children_keys]['products_in_subcategories'];
            }

            $tree[$category['id_category']] = array('name' => $category['name'],
                  'name_long' => $category['description'],
                  'parent' => $category['id_category_parent'],
                  'level' => $level,
                  'all_children' => $all_children,
                  'products_in_subcategories' => $products_in_subcategories,
                  'products_in_category' => $category['products_in_category'],
                    'path' => $path . $category['id_category'],
                  'children' => $children);
         }
      }

      return $tree;
   }


}

?>