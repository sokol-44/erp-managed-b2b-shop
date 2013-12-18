<?php
/**
 * Data_Order.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/*
 * order attribute
 * 
 * sposób płatności, czy częściowa realizacja jest dopuszczalna
 * przy zamówieniu ma być możliwość podania życzonego terminu dostawy
 * przy składaniu zamówienia opcja: odbiór osobisty
 * 
 * PAYMENT_METHOD
 * DELIVERY_PARTIAL
 * DELIVERY_DATE
 * DELIVERY_PERSONAL
 * 
 */


class Data_Shop extends Data_Picture {
	
   function __construct() {
      //echo 'Data_Shop';
      parent::__construct();

   }


   static function getShopAttributeList( $id_type = 0, $length = 1 ) {
   	$query = 'select `type`, `val`
         	from ' . TBL_SHOP_ATTRIBUTES . ' oa order by `type`';
   	add_to_fp('$query ' . $query);
   	$result = db_query( $query );
   	return db_result_array_full($result);
   }
    
   static function doShopAttributeAddOrUpdate( $param ) {
   
   	$query = 'INSERT INTO ' . TBL_SHOP_ATTRIBUTES . ' (`type`, `val`)
   			VALUES ("' . db_escape($param['type']) . '", "' . db_escape($param['val']) . '")
   					ON DUPLICATE KEY UPDATE `type` = "' . db_escape($param['type']) . '",
   					`val` = "' . db_escape($param['val']) . '"';
   
   	add_to_fp($query);
   	$result = db_query( $query );
   	$ar = db_affected_rows( $result );
   
   	if( $ar ) return array_merge($param, array('status' => 'SUCCESS,NEW'));
   	else return array_merge($param, array('status' => 'SUCCESS,EXIST'));
   }
    
   static function get_shop_attributes($attribute_type = false) {
   	$F = Framework::g_global();
   	
   	$where_add = '';
   	
   	if( $F->not_null($attribute_type) ) {
   		$where_add = ' where sa.type = "' . db_escape($attribute_type) . '"';
   	}
   	
   	$query = 'select `type`, `val`
		from ' . TBL_SHOP_ATTRIBUTES . ' sa ' . $where_add;
   	
   	$result = db_query( $query );
   	$nrow = db_rows( $result );
   	if( $nrow == 1 && $F->not_null($attribute_type) ) {
   		return db_fetch_result('val', $result);
   	} elseif( $nrow > 0 && $F->is_null($attribute_type)) {
   		$res_arr = db_result_array_full($result);
   		$ret_arr = array();
   		foreach($res_arr as $res_one) {
   			$ret_arr[$res_one['type']] = $res_one['val'];
   		}
   		return $ret_arr;
   	} else {
   		return false;
   	}

   }
   
   
}
?>