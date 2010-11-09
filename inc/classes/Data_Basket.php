<?php
/**
 * Data_Basket.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Data_Basket {


   static function get_basket_product_list( $id_shopping_basket = 0, $id_client = 0 ) {
      $F = Framework::g_global();
      
      $query = 'select sbp.id_product, sbp.product_count
         from ' . SHOP_SHOPPING_BASKET . ' sb left join ' . SHOP_SHOPPING_BASKET_PRODUCTS . ' sbp on
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