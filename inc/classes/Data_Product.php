<?php
/**
 * Data_Product.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Data_Product
 *
 * Provides operations and comprehensive business logic workflow handling for ecommerce products,
 * category taxonomies, pricing matrices, inventory tiers, and attributes metadata layers.
 *
 * @todo Refactor static class architecture into dependency-injected Domain Services.
 * @todo Declare visibility modifiers (public/protected/private) explicitly on all class properties.
 * @todo Implement strictly defined native scalar types and union return hints across methods.
 * @todo Extract inline Raw-SQL mutations over to an encapsulated Data Mapper or PDO/ORM layer.
 */
class Data_Product extends Data_Basket {
   /**
    * @var array Configuration states controlling client scopes and price evaluation contexts.
    */
   static $Data_Products_params = array('id_client' => 0, 'client_view' => false);

   /**
    * @var int|bool Base virtual folder partition location index constraint, or false if unassigned.
    */
   static $root_number = false;

   /**
    * @var array List of text property prefixes masked from standard rendering loops.
    */
   static $Data_Products_hidden_attributes = array('KALK_');
  // static $subproduct_separator = '_';

   /**
    * Data_Product constructor.
    *
    * Boots context params parsing parameters against active global active user instances.
    *
    * @todo Remove commented debug boilerplate blocks (`//echo get_class();`).
    */
   function __construct() {
        //echo get_class();
      parent::__construct();
      self::_load_client_params();
      self::_load_virtualdir_params();
   }

   /**
    * Queries runtime identity records to bind pricing layouts based on authorization levels.
    *
    * @return void
    *
    * @todo Decouple global context interactions (`Person::g_global()`) and procedural constants setup.
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
    * Evaluates request tracking properties to determine active directory roots.
    *
    * @return void
    *
    * @todo Replace state manipulation using `Framework::g_global()` with an explicit application request context.
    */
   static function _load_virtualdir_params() {
      $F = Framework::g_global();

      //pasmanteria_i_dodatki_krawieckie = 1
      //produkty_medyczne = 2
      $root_number = $F->return_virtualdir_id();
      if( $root_number ) self::$root_number = $root_number;
   }

   /**
    * Resolves an array of complete category records preserving sorting instructions.
    *
    * @param array|string|bool $list_in Collection listing target ids or serial string representation. Defaults to false.
    * @return array Matrix compilation containing category structural attributes hashes.
    *
    * @todo Eliminate commented legacy diagnostic implementations for PostgreSQL structural adjustments.
    * @todo Replace unsafe input mapping routines using raw array values with parameters bound in PDO.
    * @todo Remove dead code.
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
      order by FIND_IN_SET(id_category,"' . implode(',', $list) . '")';  //PostgreSQL

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
    * Recursively navigates an existing category tree map to collect children nodes.
    *
    * @param int|string $id_category Reference identification criterion target.
    * @param array $category_tree Complete nested hierarchical database array. Defaults to an empty array.
    * @return array Flattened sequential vector index containing matched node target IDs.
    *
    * @todo Avoid using global fallback lookups within recursive methods; enforce explicit data passing rules.
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
    * Fetches paginated records of products within specific target parameters.
    *
    * @param int|string $id_category Target tracking category index.
    * @param array $filters Additional key-value filter parameters mapping query criteria. Defaults to an empty array.
    * @param array $sort Matrix formatting sorting criteria configurations. Defaults to an empty array.
    * @return array List representing active records datasets.
    *
    * @todo Restructure the dynamic conditional switch strings block to leverage polymorphism or strategy configurations.
    * @todo Remove reference calls touching legacy variables `global $category_tree;`.
    */
   static function get_categories_product_list($id_category, array $filters = array(), array $sort = array()) {
      global $category_tree;
      $F = Framework::g_global();
      $SP = SplitPage::g_global();

      $where = array('p.status' => 'ACTIVE');
      if( $F->not_null($filters) ) {
          $where = array_merge($where, $filters);
      }


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

      if( $F->not_null($sort) ) $order_str = ' order by ' . db_unroll_sort($sort);
      else $order_str = ' order by p.name ';

      $query = $query_pm . $where_str . $order_str;

      $sp_query = $SP->prepare_sql( $query );

      $res = db_query( $sp_query );
      return db_result_array($res);
   }

   /**
    * Processes raw storage pathways strings maps.
    *
    * @param string $raw_img Raw context asset pathway string definition.
    * @return string Validated or modified asset address location string identifier.
    *
    * @todo Address the legacy `FIXME - hack` notice and configure robust storage location parameters mapping.
    */
   static function get_product_image_path( $raw_img ) {
      $img_arr = explode('/', $raw_img);
      //FIXME - hack
      //return '/product_image/' . end($img_arr);
      return $raw_img;
   }

   /**
    * Modifies stock level tracking allocations across products sequentially within database transactions.
    *
    * @param array $product_list Collection mapping structural requirements attributes parameters.
    * @return int Count index value reflecting affected row totals from data persistence scopes.
    *
    * @todo Ensure appropriate return fallback variable declarations when input evaluations resolve empty arrays.
    */
   static function change_product_quantity_list( array $product_list ) {
      $F = Framework::g_global();

      if( $F->not_null($product_list) ) {
         db_transaction_start();
         foreach( $product_list as $key => $product_params ) {
            $insert_query = 'update ' . TBL_SHOP_PRODUCT . ' set
            quantity = quantity - ' . db_int($product_params['quantity']) . '
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
    * Compiles detailed records attributes, configurations, sub-items, and subtypes for a defined group of keys.
    *
    * @param array $product_key_array Collection list mapping target transaction identities strings indices.
    * @return array Indexed structural results hash matrix.
    *
    * @todo Resolve notice conditions caused by references targeting undefined variable parameters (`$product_array[$key]`, `$st.picture_big_url`).
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
                  $product_list[$key] = self::_get_merge_info_subtype($product_key_array[$key], $product_subtype);
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
    * Merges transactional structural variant definitions values over base parent records parameters blocks.
    *
    * @param array $product_array Master inventory item base details profile array map.
    * @param array $product_subtype Child variant properties descriptor matrix.
    * @return array Consolidated dataset mapping specifications matrix.
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
    * Filters an unindexed collection list to locate matched child identifiers records blocks.
    *
    * @param array $product_subtype_list Set of multiple raw subcategory structures options.
    * @param array $product_param Target lookup specifications configuration array.
    * @return array|null Returns matched properties dictionary values array or null if undetected.
    */
   static function _get_product_subtype_data_from_array( $product_subtype_list, $product_param ) {
      foreach( $product_subtype_list as $product_subtype ) {
         if( (int)$product_subtype['id_product_subtype'] == (int)$product_param['id_product_subtype'] ) {
            return $product_subtype;
         }
      }
   }

   /**
    * Normalizes database record fields arrays mappings into clean format models structures arrays.
    *
    * @param array $product_list_raw Unprocessed records collection database table mapping rows.
    * @param array $product_param Identity keys structural definition indices constraints.
    * @return array Cleaned standard data properties parameters list hash matrix.
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
    * Retrieves pricing structures associated with specific accounts filtered through location indices offsets.
    *
    * @param int $id_product_start Pagination cursor location ID. Defaults to 0.
    * @param int $id_client Target customer registration account lookup index. Defaults to 0.
    * @param int $length Operational maximum boundary record pull size constraint. Defaults to 1.
    * @param string $where Supplementary inline raw conditional tracking constraints phrases. Defaults to empty string.
    * @return array Complete structural result rows set maps context list.
    *
    * @todo Correct query logic error where verification uses incorrect variable references (`db_int($id_client)` instead of query start constraints).
    * @todo Fix spelling.
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
    * Resolves price modifications configurations lists mapped against client profile indexes structures.
    *
    * @param int $id_client Reference identity targeting business profiles context tracking indexes. Defaults to 0.
    * @param int $id_product_start Cursor indicator tracker position. Defaults to 0.
    * @param int $length Total item capacity return ceiling threshold constraints parameters. Defaults to 1.
    * @param string $where Supplementary criteria filter overrides options. Defaults to empty string.
    * @return array List representing specific custom items mappings calculations definitions.
    *
    * @todo Fix spelling.
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
    * Returns product sets associated with taxonomy categorization rules paths parameters mappings.
    *
    * @param int $id_category Target folder context criteria parameter identification index. Defaults to 0.
    * @param int $id_product_start Allocation search pointer key indexing. Defaults to 0.
    * @param int $length Total listing volume extraction restrictions constraints settings. Defaults to 1.
    * @param string $where Ad-hoc constraint filtering attributes injection string block. Defaults to empty string.
    * @return array Full dataset collection tracking dictionary matrix layout context rows array.
    *
    * @todo Fix spelling.
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
    * Extracts base inventory listings rows from database storage spaces directly.
    *
    * @param int $id_product_start Allocation sorting offset marker identification key index. Defaults to 0.
    * @param int $length Capacity layout limit definition criteria values constraints settings. Defaults to 1.
    * @param string $where Custom runtime constraint clause parameters modifications strings blocks. Defaults to empty string.
    * @return array Collection array set detailing query feedback array structures.
    *
    * @todo Fix spelling.
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
    * Invokes functional procedural routines to execute targeted category deletions directly inside database storage contexts.
    *
    * @param array $param_array Configuration values parameter map containing tracking indexes pointers.
    * @return array Status description result tracking mapping dictionaries.
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
    * Alters contextual text metadata parameter criteria maps tracking specified application nodes attributes values.
    *
    * @param array $param_array Parameter properties configuration array detailing updates guidelines specifications.
    * @return array Execution tracking indicators dictionary mapping collection outcomes.
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
    * Registers an original entry node definition tracking context inside structural relational database index spaces.
    *
    * @param array $param_array Param definitions configuration attributes array mapping specifications framework.
    * @return array Core registration operational output status logs values mapping hash.
    *
    * @todo Stop using extract() as it obscures variable origins and harms IDE code analysis.
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
    * Evaluates active taxonomies configurations options to establish or update node locations setups safely.
    *
    * @param array $param_array Operational constraints parameters array maps.
    * @return array Database procedural tracking performance state indicators hash map dictionary.
    *
    * @todo Stop using extract() as it obscures variable origins and harms IDE code analysis.
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
    * Validates duplication states prior to writing original entity values inside tracking storage frameworks.
    *
    * @param ProductData $ProductData Strongly-typed instance containing definitions specifications properties variables.
    * @return array Management processing response status tracking properties model matrix context.
    *
    * @todo Fix crash condition caused by calling missing reference parameter objects values mappings (`$SingleValueClass`).
    * @todo Correct typing mismatch on internal execution tracking values blocks routing to `Data::doProductChange`.
    */
   static function doProductAdd( ProductData $ProductData ) {
       add_to_fp('doProductAdd');

       $param_array = $SingleValueClass->return_array();

      $query = 'select id_product ' . TBL_SHOP_PRODUCT . ' where
         id_product = ' . db_int($param_array['id_product']);
      $res = array('id_one' => $param_array['id_product'], 'additiona_data' => '', 'status' => '');

      add_to_fp($query);
      $result = db_query( $query );
      $ar = db_rows($result);

      if( $ar > 0 ) {
          $res['status'] = 'ERROR,EXIST';
          return $res;
      }

      return Data::doProductChange( $ProductData );
   }

   /**
    * Delegates properties persistence configurations down through modification tracking mechanisms fields.
    *
    * @param ProductData $ProductData Data encapsulation class representation.
    * @return array Action compliance output tracking indicators metrics list.
    *
    * @todo Standardize naming rules targeting static reference redirects (`Data::doProductChange` vs `self::doProductChange`).
    */
   static function doProductAddOrUpdate( ProductData $ProductData ) {
       add_to_fp('doProductAddOrUpdate');
       return Data::doProductChange( $ProductData );
   }

   /**
    * Registers child variations configurations parameters specifications inside inventory data mappings models.
    *
    * @param array $param_array Attribute updates properties tracking specifications criteria collection maps.
    * @return void
    * @throws RuntimeException Triggered explicitly as method execution state continues to remain un-implemented.
    */
   static function doProductSubtypeAddOrUpdate($param_array) {
       trigger_error('Not implemented ' . __METHOD__, E_USER_ERROR);
   }

   /**
    * Clears auxiliary configuration records across specific modules linked to single master identifiers.
    *
    * @param string $what Execution segment selector definition code context identifier string rule.
    * @param int|string $id_product Target core identity reference data model mapping index value.
    * @return string|bool Returns status operational calculation statistics metadata details, or false on mapping faults.
    *
    * @todo Add complete implementations covering missing core features like `ProductSubtypeData`.
    */
   static function doProductCleanMethodData( $what, $id_product) {

       $id_product = db_int($id_product);

       switch( $what ) {
           case 'ProductSubtypeData':
               trigger_error('Not implemented ' . __METHOD__, E_USER_ERROR);
               break;
           case 'Product2CategoryData':
               $del_cat = "delete from " . TBL_SHOP_PRODUCT_TO_CATEGORY . ' where id_product = ' . $id_product;
               return 'CATEGORY:'.db_affected_rows();
               break;
           case 'ProductClientPriceData':
               $del_price = "delete from " . TBL_SHOP_PRODUCT_CLIENT_PRICE . ' where id_product = ' . $id_product;
               return 'PRICE:'.db_affected_rows();
               break;
           case 'ProductAttributeData':
               $del_attr = "delete from " . TBL_SHOP_PRODUCT_ATTRIBUTES . ' where id_product = ' . $id_product;
               return 'ATTRIBUTE:'.db_affected_rows();
               break;
           case 'ProductAttributeWGroupData':
               $del_attr_wg = "delete from " . TBL_SHOP_PRODUCT_ATTRIBUTES_W_GROUP . ' where id_product = ' . $id_product;
               return 'ATTRIBUTE_W_GROUP:'.db_affected_rows();
               break;
       }

       return false;
   }

   /**
    * Initiates database session isolation level locks across active connections layers.
    *
    * @param array $param_array Action processing options configurations data parameter parameters list array map.
    * @return mixed Transaction initializer lifecycle sequence result indicators metrics.
    */
   static function doBatchStart($param_array) {
       return db_transaction_start();
   }

   /**
    * Concludes active transactional modification batches committing storage actions logs definitions safely.
    *
    * @param array $param_array Lifecycle tracking process criteria attributes specifications mappings array lists.
    * @return mixed System persistence engine modification confirmation confirmation response statements logs.
    */
   static function doBatchStop($param_array) {
       return db_transaction_end();
   }

   /**
    * Persists product specifications using duplicate-key mutation queries.
    *
    * @param ProductData $ProductData Strong model tracking entity representation specifications package.
    * @return array Processing confirmation output metrics states log metadata context array.
    */
   static function doProductChange( ProductData $ProductData ) {
       $F = Framework::g_global();

       //extract( $param_array );
       add_to_fp('doProductChange');
       $db_in = $ProductData->get_inst_upd_arr();
        add_to_fp('$db_in'.print_r($db_in, true));

       $query = 'insert into ' . TBL_SHOP_PRODUCT . ' set '.implode(', ', $db_in['insert_array']).'
               ON DUPLICATE KEY UPDATE '.implode(', ', $db_in['update_array']);

       $res = array('id_one' => $ProductData->get_primary_key(), 'additiona_data' => '', 'status' => '');

       add_to_fp($query);
       $result = db_query( $query );
       $ar = db_affected_rows( $result );

       if( $ar == 1 ) $res['status'] = 'SUCCESS,NEW';
       elseif( $ar == 2 ) $res['status'] = 'SUCCESS,EXIST';
       elseif( $ar == 0 ) $res['status'] = 'SUCCESS,EXIST,NODIFF';
       else $res['status'] = 'ERROR,UNKNOW';

       return $res;
   }

   /**
    * Purges custom calculated user price tables assigned against client specifications contexts.
    *
    * @param int|string $id_client Reference account tracker identification criterion target index parameter.
    * @return array Status execution validation reporting properties dictionary collection values array.
    *
    * @todo Fix bugs where reference attempts touch undefined context scope configuration parameters (`$promotion_price`, `$id_product`).
    */
   static function doProductClientPriceClean( $id_client ) {
       $F = Framework::g_global();

//       $query = 'select "' . db_int($id_client) . '" as id_one, "" as additional_data,
//        b_func_product_client_price_all_del("' . db_int($id_client) . '") as status';
      $query = 'delete from ' . TBL_SHOP_PRODUCT_CLIENT_PRICE . ' where id_client = "' . db_int($id_client) . '"';
      add_to_fp($query);
      $result = db_query( $query );
      $status = db_fetch_array($result);
      if( $F->not_null($promotion_price) &&
          $F->not_null($promotion_date_start) && $F->not_null($promotion_date_end) &&
          strpos( $status['status'], 'ERROR') === FALSE  ) {

          $query_promotion = 'update ' . TBL_SHOP_PRODUCT . ' set
               `promotion_price` = "' .  db_float($promotion_price) . '",
               `promotion_date_start` = "' . db_escape($promotion_date_start) . '",
               `promotion_date_end` = "' . db_escape($promotion_date_end) . '"
                 where id_product = "' . db_int($id_product) . '"';
          $status['additional_data'] = 'PROMOTION_SET';
          $result = db_query( $query );
      }
      return $status;
   }

   /**
    * Commits large arrays of client-specific prices using batch chunking strategies.
    *
    * @param int|string $id_client Target customer mapping unique index identifier value parameter.
    * @param array $id_product_array Multidimensional package sorting collection specifications array hashes.
    * @return int Aggregated totals mapping count confirmation metrics for records written.
    *
    * @todo Remove unused local processing variable loops parameters (`$count`) to optimize operations cleanups.
    */
   static function setClientProductPriceList( $id_client, $id_product_array) {
//       $query = 'select "' . db_int($id_product) . '" as id_one, "' . db_int($id_client) . '" as id_two,
//        "" as additional_data,
//        b_func_product_client_price_add("' . db_int($id_product) . '", "' . db_int($id_client) . '", "' . db_float($price) . '", "' . db_float($vat) . '") as status';

      $query_start='insert ignore into  ' . TBL_SHOP_PRODUCT_CLIENT_PRICE . ' (`id_product`, `id_client`, `price`) values ';

      add_to_fp('Data::setClientProductPriceList');

      $val_array = array();
      $count=1;
      $ins_count=0;
      $id_client_db = db_int($id_client);
      foreach( $id_product_array as $prod_val ) {
         if( Framework::is_null($prod_val) ) continue;
         $val_array[] = '(' . db_int($prod_val['id_product']) . ',' . $id_client_db . ',' . db_float($prod_val['price']) . ' )';
         if( sizeof($val_array)%1000 == 0 ) {
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
    * Assigns specific customized value rules against targeted accounts profiles options fields parameters.
    *
    * @param int|string $id_product Target item dynamic identifier assignment code index.
    * @param int|string $id_client Target customer partition identifier index mapping criterion.
    * @param float|int|string $price Numerical value scale indicator measurement setting.
    * @return array Output operational mapping verification data metadata flags logs dictionary.
    */
   static function setProductClientPrice( $id_product, $id_client, $price) {
      $query = 'select "' . db_int($id_product) . '" as id_one, "' . db_int($id_client) . '" as id_two,
       "" as additional_data,
       b_func_product_client_price_add("' . db_int($id_product) . '", "' . db_int($id_client) . '", "' . db_float($price) . '") as status';
      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }

   /**
    * Maps unique relations associating item indices definitions across structural folders locations indexes safely.
    *
    * @param int|string $id_product Core primary entry identity context tracking pointer.
    * @param int|string $id_category Structural directory node context assignment key.
    * @return array Multi-value database procedural feedback response collection array items list.
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
    * Gathers complete listings mapping grouped dynamic attributes descriptors sets constraints fields.
    *
    * @param int $id_product_start Sorting threshold indicator value parameter layout context pointer. Defaults to 0.
    * @param int $length Extraction limit constraint parameters definitions options tracking indicators. Defaults to 0.
    * @return array Collection array detailing query results data attributes configuration rows.
    *
    * @todo Correct notice bugs generated via invocation attempts addressing non-existent configuration variable parameters (`$where`).
    * @todo Fix spelling.
    */
   static function getProductAttributeWGroupList( $id_product_start = 0, $length = 0 ) {
       list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);
       $query = 'select `id_product`,
               `id_attribute`, `attribute_order`, `attribute_name`, `attribute_value`,
               `id_group`, `group_name`, `group_order`
             from ' . TBL_SHOP_PRODUCT_ATTRIBUTES_W_GROUP . ' pa
        where pa.id_product ' . $comparision_dir . db_int($id_product_start) . $where . '
      ORDER BY pa.id_product ' . $order_dir . ' LIMIT ' . db_int($length);
       add_to_fp('$query ' . $query);
       $result = db_query( $query );
       echo '1111';
       return db_result_array_full($result);
   }

   /**
    * Executes transactional inserts or revisions handling technical fields grouped profiles layouts.
    *
    * @param array $param Attribute definitions criteria maps tracking structural changes parameters lists.
    * @return array Modification indicators array mapping transaction resolution states properties.
    */
   static function doProductAttributeWGroupAddOrUpdate( $param ) {

       $query_prod = 'select id_product from ' . TBL_SHOP_PRODUCT . ' where id_product = "' . db_int($param['id_product']) . '"';
       $result_prod = db_query( $query_prod );
       if( db_rows($result_prod) == 0 ) return array_merge($param, array('status' => 'ERROR,PRODUCT_DONT_EXIST'));

       $query = 'INSERT INTO ' . TBL_SHOP_PRODUCT_ATTRIBUTES_W_GROUP . ' (`id_product`, `id_attribute`,
               `attribute_order`, `attribute_name`, `attribute_value`,
               `id_group`, `group_name`, `group_order`)
               VALUES ("' . db_int($param['id_product']) . '", "' . db_int($param['id_attribute']) . '",
                       "' . db_int($param['attribute_order']) . '", "' . db_escape($param['attribute_name']) . '",
                       "' . db_escape($param['attribute_value']) . '",
                       "' . db_escape($param['id_group']) . '", "' . db_escape($param['group_name']) . '",
                      "' . db_int($param['group_order']) . '")
                       ON DUPLICATE KEY UPDATE `attribute_value` = "' . db_escape($param['attribute_value']) . '",
                       `attribute_order` = "' . db_int($param['attribute_order']) . '",
                       `group_order` = "' . db_int($param['group_order']) . '"';

       add_to_fp($query);
       $result = db_query( $query );
       $ar = db_affected_rows( $result );

       if( $ar == 1 ) return array_merge($param, array('status' => 'SUCCESS,NEW'));
       elseif( $ar == 2 ) return array_merge($param, array('status' => 'SUCCESS,EXIST'));
       else return array_merge($param, array('status' => 'ERROR,UNKNOWN'));
   }

   /**
    * Returns flat rows sets capturing simple item properties data components directly.
    *
    * @param int $id_product_start Pagination record pointer baseline index marker. Defaults to 0.
    * @param int $length Volumetric capacity pull ceiling definition restriction indicator variables. Defaults to 0.
    * @return array Collection array rows list mapping standard system metrics descriptors.
    *
    * @todo Clean up references targeting missing variables strings constraints parameters (`$where`).
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
    * Commits transactional entry logs registering simple specific dynamic classification fields values.
    *
    * @param array $param Core configuration parameters map dictionary properties specifications list.
    * @return array Compilation metadata output dictionary logs tracking resolution profiles states.
    */
   static function doProductAttributeAddOrUpdate( $param ) {

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
    * Executes search queries scanning indexes tables under custom specifications phrases.
    *
    * @param int $length Result metrics volume pagination capacity constraint threshold parameters. Defaults to 1.
    * @param string $where Custom conditional filter strings fragments injection parameters. Defaults to empty string.
    * @return array Matrix arrays detailing matching items database properties characteristics rows.
    *
    * @todo Fix major crash conditions produced by calling undefined structural values parameters (`$comparision_dir`, `$id_product_start`, `$order_dir`).
    * @todo Fix spelling.
    */
   static function get_product_search( $length = 1, $where = '' ) {
      trigger_error('Not implemented ' . __METHOD__, E_USER_ERROR);
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
    * Coordinates image path mapping configurations tracking against active item record properties.
    *
    * @param array $product_info Collection hash capturing single target item properties fields parameters mapping data.
    * @return array|bool Address mappings paths collection properties details, or false if attributes resolve missing configurations.
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
    * Compiles detailed records parameters attributes capturing every structural variable component for a single product target ID.
    *
    * @param int|string $id_product Primary registration access identifier target index. Defaults to 0.
    * @return array|bool Consolidated dictionary properties metrics mapping array, or false on mapping query delivery faults.
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

      if( defined('TBL_SHOP_PRODUCT_SUBTYPE') && TBL_SHOP_PRODUCT_SUBTYPE!='') {
          $product_info['subtype'] = Data_Product::get_product_subtype_info( (int)$id_product );
      }

      if( defined('TBL_SHOP_PRODUCT_ATTRIBUTES') && TBL_SHOP_PRODUCT_ATTRIBUTES!='') {
          $product_info['attribute'] = Data_Product::get_product_attributes_info( (int)$id_product );
      }

      if( defined('TBL_SHOP_PRODUCT_ATTRIBUTES_W_GROUP') && TBL_SHOP_PRODUCT_ATTRIBUTES_W_GROUP!='') {
          $product_info['attribute_group'] = Data_Product::get_product_attribute_group_info( (int)$id_product );
      }

      return $product_info;
   }

   /**
    * Normalizes multidimensional arrays of complex technical classification parameters grouping them by layout keys definitions.
    *
    * @param int|string $id_product Core dynamic entry identity tracking locator pointer. Defaults to 0.
    * @return array Organised structured array map parameters definitions collection dictionary variables list.
    *
    * @todo Refactor native recursive sorting callback logic blocks (`uasort`) to isolate comparison procedures into utility files.
    */
   static function get_product_attribute_group_info( $id_product = 0 ) {
       $F = Framework::g_global();
       $product_attributes = array();

       $query_attributes = 'select pag.id_product, pag.id_attribute,
               pag.attribute_name, pag.attribute_value, pag.attribute_order,
               pag.id_group, pag.group_name, pag.group_order
         from ' . TBL_SHOP_PRODUCT_ATTRIBUTES_W_GROUP . ' pag where
         pag.id_product = ' . (int)$id_product . ' order by pag.id_group, pag.id_attribute';
       $result_attributes = db_query( $query_attributes );
       $product_attributes = db_result_array_full( $result_attributes );

       $group = array();
       $group_order = array();

       foreach( $product_attributes as $attribute ) {
           if( !isset($group[$attribute['id_group']]) ) {
               $group[$attribute['id_group']] = array(
                   'id_group' => $attribute['id_group'],
                   'group_name' => $attribute['group_name'],
                   'group_order' => $attribute['group_order'],
                   'attribute' => array()
               );
               $group_order[$attribute['id_group']] = $attribute['group_order'];
           }
       }

       //print_debug($group_order);

       $group_tmp = $group;
       $attribute_order = array();

       foreach( $group_tmp as $id_group => $group_tmp2 ) {
           foreach( $product_attributes as $attribute ) {
               if( $id_group == $attribute['id_group'] ) {
                   $group[$id_group]['attribute'][$attribute['id_attribute']] = array(
                           'id_attribute' => $attribute['id_attribute'],
                           'attribute_name' => $attribute['attribute_name'],
                           'attribute_value' => $attribute['attribute_value'],
                           'attribute_order' => $attribute['attribute_order']
                   );
                   $attribute_order[$id_group][$attribute['id_attribute']] = $attribute['attribute_order'];
               }
           }
       }

      // FIXME - move to utility class or implement sorting inside SQL
       $group_tmp = $group;
       foreach( $group_tmp as $id_group => $group_tmp2 ) {
           $attribute = $group_tmp2['attribute'];
           //array_multisort( $attribute_order[$id_group], SORT_ASC, $attribute);
           uasort($attribute, function ($a, $b) { return $a['attribute_order'] - $b['attribute_order']; });
           $group[$id_group]['attribute']=$attribute;
       }

       uasort($group, function ($a, $b) { return $a['group_order'] - $b['group_order']; });
       //array_multisort($group_order, SORT_ASC, $group);

       return $group;
   }

   /**
    * Returns raw value attributes lists mapping against standard single identity keys fields.
    *
    * @param int|string $id_product Target primary reference sorting configuration indicator index. Defaults to 0.
    * @return array Keyed data configuration properties attributes fields parameters list hash dictionary.
    */
   static function get_product_attributes_info( $id_product = 0 ) {
      $F = Framework::g_global();
      $product_attributes = array();

      $query_attributes = 'select pa.id_product, pa.type, pa.val
         from ' . TBL_SHOP_PRODUCT_ATTRIBUTES . ' pa where
         pa.id_product = ' . (int)$id_product . ' order by pa.type';
      $result_attributes = db_query( $query_attributes );
      $product_attributes_tmp = db_result_array_full( $result_attributes );
      foreach( $product_attributes_tmp as $attribute ) {
          $product_attributes[$attribute['type']] = $attribute['val'];
      }
      return $product_attributes;
   }

   /**
    * Extracts structural modifications variants lists matching core items definitions profiles.
    *
    * @param int|string $id_product Reference item core validation constraint indicator tracking pointer index. Defaults to 0.
    * @return array Collection array mappings structural records rows parameters specifications hashes list.
    */
   static function get_product_subtype_info( $id_product = 0 ) {
      $F = Framework::g_global();
      $product_subtypes = array();

      $query_subtypes = 'select pst.id_product_subtype, pst.id_product, pst.description,
         pst.picture_small_url, pst.picture_big_url, pst.picture_id, pst.price_diff, pst.status
         from ' . TBL_SHOP_PRODUCT_SUBTYPE . ' pst where pst.status = "ACTIVE" and
         pst.id_product = ' . (int)$id_product . ' order by pst.description';
      $result_subtypes = db_query( $query_subtypes );
      $product_subtypes = db_result_array_full_id( $result_subtypes );
      return $product_subtypes;
   }

   /**
    * Compiles advanced filtered collections targeting items matrices records lists fields.
    *
    * @param array $filters Target validation conditional configurations parameters mapping dictionary. Defaults to an empty array.
    * @param array $sort Operational dynamic configuration data sorting layout choices. Defaults to an empty array.
    * @return array Result list representing processing target information profiles metrics.
    *
    * @todo Rectify notice errors caused by evaluation processing against undefined variables criteria flags (`$order`, `$sp_query`).
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

         if( $F->not_null($order) ) $order_str = ' order by p.name ';
         else $order_str = ' order by p.name ';

         $query = $query_pm . $where_str . $order_str;

         $sp_query = $SP->prepare_sql( $query );
      }
      $res = db_query( $sp_query );
      return db_result_array($res);
   }

   /**
    * Gathers complete summaries aggregating product counts distributions across structural directories paths maps.
    *
    * @param array $filters Processing selection boundaries properties dictionary parameter collection.
    * @return array Collection array rows list details representing mapped layout results.
    *
    * @todo Modernize the inline dynamic queries composition routines using structured schema aggregation layers.
    * @todo Remove dead code.
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
      COUNT(distinct p2c.id_product) AS products_in_category, c.sort_order,
      GROUP_CONCAT(distinct p2c.id_product) AS ids_products_in_category
         FROM ' . TBL_SHOP_CATEGORY . ' c LEFT OUTER JOIN (' . $query_pm . ') p2c
         ON (c.id_category = p2c.id_category) ' . $where_root_number . '
         GROUP BY c.id_category, c.name, c.description, c.id_category_parent, c.sort_order
         ORDER BY c.sort_order, c.name';

      $res = db_query( $query );
      return db_result_array($res);
   }

   /**
    * Returns category mapping directories trees.
    *
    * @param array $filters Configuration constraints criteria properties indices list. Defaults to an empty array.
    * @param bool $purge_empty Filters configuration rules options controlling empty node behaviors. Defaults to false.
    * @return array Multi-dimensional layout mapping hierarchy database collection dictionary array tree.
    *
    * @todo Fix spelling.
    */
   static function get_categorie_tree( $filters = array(), $purge_empty = false ) {
      $category_list = self::get_categories_list($filters);
      $category_tree = self::__categories_make_tree($category_list);

      //if( $purge_empty )
      return $category_tree;

   }

   /**
    * Decomposes sequential structural compound item identity tracking keys arrays maps into distinct parameter elements lists.
    *
    * @param array $product_key_array Dynamic keys identifiers vector indices list package.
    * @return array Multi-tier parameter mappings layout matrix capturing distinct items scopes tracking indicators.
    */
   /**
    * Decomposes sequential structural compound item identity tracking keys arrays maps into distinct parameter elements lists.
    *
    * @param array $product_key_array Dynamic keys identifiers vector indices list package.
    * @return array Multi-tier parameter mappings layout matrix capturing distinct items scopes tracking indicators.
    */
   static function get_product_and_subproducts_from_key_list($product_key_array)
   {
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
    * Compiles compound string key labels sequences concatenating master product identifiers and subtype options values.
    *
    * @param array $product_params Baseline models arrays fields enclosing identity variables constraints.
    * @return string Serial text key mapping catalog elements relationships.
    */
   static function get_key_from_product_params( $product_params ) {
      $key = $product_params['id_product'] .
      (((int)$product_params['id_product_subtype']>0)?'_' . (int)$product_params['id_product_subtype']:'');
      return $key;
   }

   /**
    * Breaks apart complex structural composite string keys to locate atomic product specifications details markers.
    *
    * @param string $key Complete compound reference code token tracking catalog items.
    * @return array Matrix structure containing broken down standard identification fields indices parameters.
    */
   static function get_product_params_from_key( $key ) {
      $product_params = array('id_product' => 0, 'id_product_subtype' => 0);
      $res = explode('_', $key);
      $product_params['id_product'] = (int)$res['0'];
      if( isset($res['1']) ) $product_params['id_product_subtype'] = (int)$res['1'];
      return $product_params;
   }

   /**
    * Eliminates structural branches from compiled hierarchies maps when nodes contain no items.
    *
    * @param int|string $id_category_parent Reference identifier tracking upper layer parent branches.
    * @param array $local_tree Segment mapping structural details configuration elements.
    * @return array Screened tree mapping updated non-empty entries contexts parameters models list.
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
    * Assembles relational multi-tier categories loops listings into structural tree representations graphs models.
    *
    * @param array $category_list Sequential database rows tracking simple category records matrices.
    * @param int $level Current hierarchical execution processing tier depth counter tracking loops. Defaults to 0.
    * @param int|string $id_category_parent Upper master folder entry identification reference parameter index code. Defaults to 0.
    * @param string $path Accumulated dynamic navigation pathway tracking unique location breadcrumbs. Defaults to empty string.
    * @return array Structured multi-dimensional grid displaying complete nodes connections profiles arrays maps.
    */
   private static function __categories_make_tree ($category_list, $level = 0, $id_category_parent = 0, $path = '') {
        $F = Framework::g_global();

      if( $id_category_parent > 0 ) {
         $path .= $id_category_parent . '_';
      }

      $tree = array();

      foreach($category_list as $category) {
          if( $category['id_category'] == 0 ) continue;

          if ( empty($category['ids_products_in_category']) ) $category['ids_products_in_category'] = array();
          elseif( !is_array($category['ids_products_in_category']) )
                  $category['ids_products_in_category'] = explode(',', $category['ids_products_in_category']);

          $ids_products_in_sub_categories = array();

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
              // $products_in_subcategories += $children[$children_keys]['products_in_category'] + $children[$children_keys]['products_in_subcategories'];
               $ids_products_in_sub_categories = array_values( array_unique( array_merge(
                       $ids_products_in_sub_categories,
                       $children[$children_keys]['ids_products_in_category'],
                       $children[$children_keys]['ids_products_in_sub_categories']
                          ) ) );
            }

            $tree[$category['id_category']] = array('name' => $category['name'],
                  'name_long' => $category['description'],
                  'parent' => $category['id_category_parent'],
                  'level' => $level,
                  'all_children' => $all_children,
                  'products_in_subcategories' =>
                        (($F->not_null($ids_products_in_sub_categories))?sizeof($ids_products_in_sub_categories):0),
                  'products_in_category' => $category['products_in_category'],
                    'ids_products_in_category' => $category['ids_products_in_category'],
                    'ids_products_in_sub_categories' => $ids_products_in_sub_categories,
                    'path' => $path . $category['id_category'],
                  'children' => $children);
         }
      }

      return $tree;
   }

}
