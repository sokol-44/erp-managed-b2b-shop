<?php
/**
 * Data_Products.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Data_Products {

    
   static function get_categories_product_list(array $filters = array(), array $sort = array()) {
      global $category_tree;
      $F = Framework::g_global();
      $SP = SplitPage::g_global();
      
      if( false && SHOW_PRODUCTS_FROM_SUBCATEGORIES == 'true' ) {
         // TODO
         // Show products from subcategories
      } else {
         $catpath = $F->request_split_array('catpath', ',', 'GET');
         $id_category = end($catpath);
         //$query = 'select p.id_product, p.name, SUBSTR(p.description,45) as description, p.picture_small_url,
			$query = 'select p.id_product, p.name, p.id_product as description, p.picture_small_url,
         p.picture_big_url, p.picture_id, p.price, p.vat, p.quantity, p.status
         from ' . TBL_SHOP_PRODUCT . ' p left join ' . TBL_SHOP_PRODUCT_TO_CATEGORY . ' p2c on
         ( p.id_product = p2c.id_product )
         where p2c.id_category = ' . (int)$id_category;
			$sp_query = $SP->prepare_sql( $query );
      }
      $res = db_query( $sp_query );
      return db_result_array($res);
   }
    
    
   static function get_categories_list( array $filters ) {

      $query = 'SELECT c.id_category, c.name, c.description, c.id_category_parent, COUNT(p2c.id_category) AS products_in_category
   	FROM ' . TBL_SHOP_CATEGORY . ' c LEFT OUTER JOIN (
   		SELECT p2c.id_category FROM ' . TBL_SHOP_PRODUCT_TO_CATEGORY . ' p2c, ' . TBL_SHOP_PRODUCT . ' p
         WHERE  p2c.id_product = p.id_product AND p.status = \'ACTIVE\' AND p.quantity > 0) p2c
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