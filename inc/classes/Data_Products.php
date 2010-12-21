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

   function __construct() {
      parent::__construct();
      self::_load_client_params();
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

   static function get_categories_from_list( $list_in = false ) {
      $F = Framework::g_global();

      if( $list_in ) {
         if( is_array($list_in) ) {
            foreach($list_in as $val) { $list[] = (int)$val; }
         } else {
            $list_tmp = explode(',', $list_in);
            foreach($list_tmp as $val) { $list[] = (int)$val; }
         }
      } elseif( isset($F->GET['catpath']) ) {
         $list_tmp = $F->request_split_array('catpath', '_', 'GET');
         foreach($list_tmp as $val) { $list[] = (int)$val; }
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
            $where['p.id_client'] = (int)self::$Data_Products_params['id_client'];
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
      return '/product_image/' . end($img_arr);
   }
    
   static function change_product_quantity_list( array $product_list ) {
      $F = Framework::g_global();

      if( $F->not_null($product_list) ) {
         db_transaction_start();
         foreach( $product_list as $id_product => $details ) {
            $insert_query = 'update ' . TBL_SHOP_PRODUCT . ' set
            quantity = quantity - ' . db_int($details['quantity']) . '
            where id_product = ' . db_int($id_product) . '';
            //         print_debug($insert_query);
            db_query( $insert_query );
         }
         //      print_debug($basket_params);
         //      print_debug($basket_contents);
         $res = db_affected_rows();
         db_transaction_end();
      }

   }

   static function get_product_info_list( array $id_product_array ) {
      $F = Framework::g_global();

      if( $F->not_null(self::$Data_Products_params['client_view']) ) {
         $where = ' and p.id_client = ' . (int)self::$Data_Products_params['id_client'];
         $product_from = self::$Data_Products_params['client_view'];
      } else {
         $where = '';
         $product_from = TBL_SHOP_PRODUCT;
      }

      if( Framework::not_null($id_product_array) ) {
<<<<<<< HEAD
=======
          
         //MYSQL group_concat( column_name )
         //POSTGRESQL = array_to_string(array_agg( column_name ),',')
          
>>>>>>> 2120a21... dodanie stanow magazynowych + roznego rodzaju popraki
         $query = 'select p.id_product, p.name, p.description, p.picture_small_url,
            p.picture_big_url, p.picture_id, p.price, p.vat, p.quantity, p.status,
            group_concat(p2c.id_category) as id_category_list
            from ' . $product_from . ' p left join ' . TBL_SHOP_PRODUCT_TO_CATEGORY . ' p2c on
            ( p.id_product = p2c.id_product )
            where p.id_product IN (' . implode(',', $id_product_array) . ')' . $where . '
            group by p.id_product';
         $result = db_query( $query );
         return db_result_array( $result );
      } else {
         return array();
      }
   }

   static function get_product_info( $id_product = 0 ) {
      $F = Framework::g_global();
       
      if( $F->not_null(self::$Data_Products_params['client_view']) ) {
         $where = ' and p.id_client = ' . (int)self::$Data_Products_params['id_client'];
         $product_from = self::$Data_Products_params['client_view'];
      } else {
         $where = '';
         $product_from = TBL_SHOP_PRODUCT;
      }

<<<<<<< HEAD
=======
      //MYSQL group_concat( column_name )
      //POSTGRESQL = array_to_string(array_agg( column_name ),',')
>>>>>>> 2120a21... dodanie stanow magazynowych + roznego rodzaju popraki
      $query = 'select p.id_product, p.name, p.description, p.picture_small_url,
         p.picture_big_url, p.picture_id, p.price, p.vat, p.quantity, p.status,
         group_concat(p2c.id_category) as id_category_list
         from ' . $product_from . ' p left join ' . TBL_SHOP_PRODUCT_TO_CATEGORY . ' p2c on
         ( p.id_product = p2c.id_product )
         where p.id_product = ' . (int)$id_product . $where . ' group by p.id_product';
      $result = db_query( $query );
      return db_fetch_array( $result );
   }

   static function get_categories_list( array $filters ) {
      $F = Framework::g_global();
      
      if( $F->not_null(self::$Data_Products_params['client_view']) ) {
         $where = ' and p.id_client = ' . (int)self::$Data_Products_params['id_client'];
         $product_from = self::$Data_Products_params['client_view'];
      } else {
         $where = '';
         $product_from = TBL_SHOP_PRODUCT;
      }
      
<<<<<<< HEAD
      $query = 'SELECT c.id_category, c.name, c.description, c.id_category_parent, COUNT(p2c.id_category) AS products_in_category
=======
      //TODO p.quantity options
      //AND p.quantity > 0

      $query = 'SELECT c.id_category, c.name, c.description, c.id_category_parent,
      COUNT(p2c.id_category) AS products_in_category, c.sort_order
>>>>>>> 2120a21... dodanie stanow magazynowych + roznego rodzaju popraki
   	FROM ' . TBL_SHOP_CATEGORY . ' c LEFT OUTER JOIN (
   		SELECT p2c.id_category FROM ' . TBL_SHOP_PRODUCT_TO_CATEGORY . ' p2c, ' . $product_from . ' p
         WHERE p2c.id_product = p.id_product AND p.status = \'ACTIVE\' ' . $where . ') p2c
         ON (c.id_category = p2c.id_category)
   	GROUP BY c.id_category ORDER  BY c.sort_order, c.name';
       
      $res = db_query( $query );
      return db_result_array($res);
   }

   static function get_categorie_tree( $filters = array(), $purge_empty = false ) {
      $category_list = self::get_categories_list($filters);
      $category_tree = self::__categories_make_tree($category_list);
      //if( $purge_empty )
      return $category_tree;

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