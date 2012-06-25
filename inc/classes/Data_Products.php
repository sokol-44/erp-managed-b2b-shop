<?php
/**
 * Data_Products.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Data_Products extends Data_Basket {
   static $Data_Products_params = array('id_client' => 0, 'client_view' => false);
   static $root_number = false;

   function __construct() {
      parent::__construct();
      self::_load_client_params();
      self::_load_virtualdir_params();
   }

   static function _load_client_params() {
      $P = Person::g_global();
      if( $P->logged_in ) {
         self::$Data_Products_params['id_client'] = (int)$P->data['id_client'];
         $view_name = Data_Person::get_client_attribute((int)$P->data['id_client'], 'PRODUCT_VIEW_NAME');
         if( $view_name ) {
            self::$Data_Products_params['client_view'] = $view_name;
         }
      }
   }

   static function _load_virtualdir_params() {
      $F = Framework::g_global();

      //pasmanteria_i_dodatki_krawieckie = 1
      //produkty_medyczne = 2
      $root_number = $F->return_virtualdir_id();
      if( $root_number ) self::$root_number = $root_number;
   }

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

   static function get_categories_product_list($id_category, array $filters = array(), array $sort = array()) {
      global $category_tree;
      $F = Framework::g_global();
      $SP = SplitPage::g_global();

      if( false && SHOW_PRODUCTS_FROM_SUBCATEGORIES == 'true' ) {
         // TODO
         // Show products from subcategories
      } else {
         $where = array();

         if( $id_category != 0 ) $where['p2c.id_category'] = (int)$id_category;

         if( $F->not_null(self::$Data_Products_params['client_view']) ) {
            if( defined('DEFAULT_CLIENT_PRICE_MODE') &&
                  constant('DEFAULT_CLIENT_PRICE_MODE') == 'show_with_set_price_only_with') {
               $where['p.id_client'] = (int)self::$Data_Products_params['id_client'];
            } else {
               $where['p.id_client'] = array(db_escape((int)self::$Data_Products_params['id_client']), 'NULL');
            }
            $product_from = self::$Data_Products_params['client_view'];
         } else {
            $product_from = TBL_SHOP_PRODUCT;
         }

         if( $F->not_null($where) ) $where_str = ' where ' . db_unroll_conditions($where);

         $query = 'select p.id_product, p.name, p.description, p.picture_small_url,
         p.picture_big_url, p.picture_id, p.price, p.vat, p.quantity, p.status
         from ' . $product_from . ' p left join ' . TBL_SHOP_PRODUCT_TO_CATEGORY . ' p2c on
         ( p.id_product = p2c.id_product ) ' . $where_str;
         $sp_query = $SP->prepare_sql( $query );
      }
      $res = db_query( $sp_query );
      return db_result_array($res);
   }

   static function get_product_image_path( $raw_img ) {
      $img_arr = explode('/', $raw_img);
      //FIXME - hack
      //return '/product_image/' . end($img_arr);
      return $raw_img;
   }

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

   }

   static function get_product_info_list( array $product_key_array ) {
      $F = Framework::g_global();

      if( $F->not_null(self::$Data_Products_params['client_view']) ) {
         if( defined('DEFAULT_CLIENT_PRICE_MODE') &&
               constant('DEFAULT_CLIENT_PRICE_MODE') == 'show_with_set_price_only_with') {
            $where = ' and p.id_client = ' . (int)self::$Data_Products_params['id_client'];
         } else {
            $where = ' and (p.id_client = ' . (int)self::$Data_Products_params['id_client'] . ' or p.id_client IS NULL)';
         }
         $product_from = self::$Data_Products_params['client_view'];
      } else {
         $where = '';
         $product_from = TBL_SHOP_PRODUCT;
      }
      
//       $product_and_subproducts_array = array(
//             'id_product' => array(),
//             'id_product_subtype' => array(),
//             'subtype2product' => array(),
//       );
      
      
      $product_and_subproducts_array = self::get_product_and_subproducts_from_key_list( $product_key_array );
      //$id_product_array = $product_and_subproducts_array['id_product'];
      $id_product_subtype_array = $product_and_subproducts_array['id_product_subtype'];
      
      if( Framework::not_null($id_product_array) ) {

         //MYSQL group_concat( column_name )
         //POSTGRESQL = array_to_string(array_agg( column_name ),',')
         $query = 'select p.id_product, p.name, p.description, p.picture_small_url,
         p.picture_big_url, p.picture_id, p.price, p.vat, p.quantity, p.status,
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
            $product_subtype = db_result_array_full( $result_subtype );
         }
         
         $product_list = array();
         foreach( $product_key_array as $key) {
            $product_param = self::get_product_params_from_key($key);
            $id_product_subtype = isset($product_param['id_product_subtype'])?(int)$product_param['id_product_subtype']:0;
            $product_array[$key] = array('id_product' => (int)$product_param['id_product'],
                                 'id_product_subtype' => (int)$id_product_subtype,
                                 'name' => $product_info['name'],
                                 'description' => $product_info['description'],
                                 'picture_small_url' => $product_info['picture_small_url'],
                                 'picture_big_url' => $product_info['picture_big_url'],
                                 'picture_id' => $product_info['picture_id'],
                                 'price' => $product_info['price'],
         								'vat' => $product_info['vat'],
                                 'quantity' => $this->contents[$key]['quantity']
            );
            $id_product_subtype = (int)$this->contents[$key]['id_product_subtype'];
            if( $id_product_subtype > 0 && isset($product_info['subtype'][$id_product_subtype])) {
               $product_subtype = $product_info['subtype'][$id_product_subtype];
               
                  if( !$F->not_null($product_subtype['description']) )
                     $product_array[$key]['description'] .= $product_subtype['description'];
                  
                  if( !$F->not_null($product_subtype['picture_small_url']) )
                     $product_array[$key]['picture_small_url'] = $product_subtype['picture_small_url'];
                  
                  if( !$F->not_null($product_subtype['picture_big_url']) )
                     $product_array[$key]['picture_big_url'] = $product_subtype['picture_big_url'];
                  
                  if( !$F->not_null($product_subtype['picture_id']) )
                     $product_array[$key]['picture_id'] = $product_subtype['picture_id'];
                  
                  if( !$F->not_null($product_subtype['price_diff']) )
                     $product_array[$key]['price'] += $product_subtype['price_diff'];
               
            }
//             $product_list[$product_key] = $product_info;
//             $product_list[$product_key]['subtype'] = array();
//             $product_param = self::get_product_params_from_key($product_key);
//             if( isset($product_param['id_product_subtype']) ) {
//                foreach( $product_subtypes as $subtype) {
//                   if( (int)$subtype['id_product'] == (int)$product_info['id_product'] &&
//                         (int)$product_param['id_product'] == (int)$product_info['id_product'] ) {
//                      $product_list[$product_key]['subtype'] = $subtype;
//                   }
//                }
//             }
         }

         return $product_list;
      } else {
         return array();
      }
   }

   static function get_product_info( $id_product = 0 ) {
      $F = Framework::g_global();
       
      if( $F->not_null(self::$Data_Products_params['client_view']) ) {
         if( defined('DEFAULT_CLIENT_PRICE_MODE') &&
               constant('DEFAULT_CLIENT_PRICE_MODE') == 'show_with_set_price_only_with') {
            $where = ' and p.id_client = ' . (int)self::$Data_Products_params['id_client'];
         } else {
            $where = ' and (p.id_client = ' . (int)self::$Data_Products_params['id_client'] . ' or p.id_client IS NULL)';
         }
         $product_from = self::$Data_Products_params['client_view'];
      } else {
         $where = '';
         $product_from = TBL_SHOP_PRODUCT;
      }

      //MYSQL group_concat( column_name )
      //POSTGRESQL = array_to_string(array_agg( column_name ),',')
      $query = 'select p.id_product, p.name, p.description, p.picture_small_url,
      p.picture_big_url, p.picture_id, p.price, p.vat, p.quantity, p.status,
      group_concat(p2c.id_category) as id_category_list
      from ' . $product_from . ' p left join ' . TBL_SHOP_PRODUCT_TO_CATEGORY . ' p2c on
      ( p.id_product = p2c.id_product )
      where p.id_product = ' . (int)$id_product . $where . ' group by p.id_product';
      $result = db_query( $query );
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

   //FIXME - implements proper filters
   static function get_categories_list( array $filters ) {
      $F = Framework::g_global();


      $where = array();

      if( $F->not_null(self::$Data_Products_params['client_view']) ) {
         if( defined('DEFAULT_CLIENT_PRICE_MODE') &&
               constant('DEFAULT_CLIENT_PRICE_MODE') == 'show_with_set_price_only_with') {
            $where['p.id_client'] = (int)self::$Data_Products_params['id_client'];
         } else {
            $where['p.id_client'] = array(db_escape((int)self::$Data_Products_params['id_client']), 'NULL');
         }
         $product_from = self::$Data_Products_params['client_view'];
      } else {
         $where_client = '';
         $product_from = TBL_SHOP_PRODUCT;
      }

      $where_root_number = '';
      if( self::$root_number ) {
         $where_root_number = ' where c.root_number = ' . db_int(self::$root_number);
      }

      //TODO p.quantity options
      //AND p.quantity > 0

      $query = 'SELECT c.id_category, c.name, c.description, c.id_category_parent,
      COUNT(p2c.id_category) AS products_in_category, c.sort_order
   	  FROM ' . TBL_SHOP_CATEGORY . ' c LEFT OUTER JOIN (
   	  SELECT p2c.id_category FROM ' . TBL_SHOP_PRODUCT_TO_CATEGORY . ' p2c, ' . $product_from . ' p
   	  WHERE p2c.id_product = p.id_product AND p.status = \'ACTIVE\' ' . $where_client . ') p2c
   	  ON (c.id_category = p2c.id_category) ' . $where_root_number . '
   	  GROUP BY c.id_category, c.name, c.description, c.id_category_parent, c.sort_order
     	ORDER BY c.sort_order, c.name';
       
      $res = db_query( $query );
      return db_result_array($res);
   }

   static function get_categorie_tree( $filters = array(), $purge_empty = false ) {
      $category_list = self::get_categories_list($filters);
      $category_tree = self::__categories_make_tree($category_list);
      //if( $purge_empty )
      return $category_tree;

   }
   
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
    
   static function get_key_from_product_params( $product_params ) {
      $key = $product_params['id_product'] .
      (((int)$product_params['id_product_subtype']>0)?'_' . (int)$product_params['id_product_subtype']:'');
      return $key;
   }
    
   static function get_product_params_from_key( $key ) {
      $product_params = array('id_product' => 0, 'id_product_subtype' => 0);
      $res = explode('_', $key);
      $product_params['id_product'] = (int)$res['0'];
      if( isset($key['1']) )$product_params['id_product_subtype'] = (int)$key['i'];
      return $product_params;
   }
   
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

   private static function __categories_make_tree ($category_list, $level = 0, $id_category_parent = 0, $path = '') {

      if( $id_category_parent > 0 ) {
         $path .= $id_category_parent . '_';
      }

      $tree = array();

      foreach($category_list as $category) {
         if( $id_category_parent == $category['id_category_parent'] ) {
            $products_in_subcategories = 0;

            $children = self::__categories_make_tree($category_list, ($level+1), $category['id_category'], $path);
            $all_children = array_keys($children);
            foreach($all_children as $children_keys) {
               $all_children = array_merge($all_children, array_keys($children[$children_keys]['children']) );
               $products_in_subcategories += $children[$children_keys]['products_in_category'] + $children[$children_keys]['products_in_subcategories'];
            }
             
            $tree[$category['id_category']] = array('name' => $category['name'],
                  'name_long' => $category['description'],
                  'parent' => $category['id_category_parent'],
                  'level' => $level,
                  'all_children' => $all_children,
                  'products_in_subcategories' => $products_in_subcategories,
                  'products_in_category' => $category['products_in_category'],
                  'children' => $children,
                  'path' => $path . $category['id_category']);
         }
      }
      return $tree;
   }


}

?>