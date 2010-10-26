<?php
/**
 * Data_Basket.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Data_Basket {


   static function get_basket_product_list( $id_shopping_basket = 0 ) {
      $F = Framework::g_global();
      $SP = SplitPage::g_global();

      $catpath = $F->request_split_array('catpath', ',', 'GET');
      $id_category = end($catpath);
      //$query = 'select p.id_product, p.name, SUBSTR(p.description,45) as description, p.picture_small_url,
      $query = 'select p.id_product, p.name, p.id_product as description, p.picture_small_url,
         p.picture_big_url, p.picture_id, p.price, p.vat, p.quantity, p.status
         from ' . TBL_SHOP_PRODUCT . ' p left join ' . TBL_SHOP_PRODUCT_TO_CATEGORY . ' p2c on
         ( p.id_product = p2c.id_product )
         where p2c.id_category = ' . (int)$id_category;
      $sp_query = $SP->prepare_sql( $query );

      $res = db_query( $sp_query );
      return db_result_array($res);
   }

}

?>