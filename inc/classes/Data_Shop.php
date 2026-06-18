<?php
/**
 * Data_Shop.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/*
 * order attribute
 * payment method; is partial fulfillment allowed?
 * the option to specify a preferred delivery date should be available when placing an order
 * when placing an order, the option: in-person pickup
 * PAYMENT_METHOD
 * DELIVERY_PARTIAL
 * DELIVERY_DATE
 * DELIVERY_PERSONAL
 * */

/**
 * Class Data_Shop
 *
 * Handles shop-specific data operations and attributes by extending base picture capabilities.
 *
 * @todo Rename class to PascalCase (e.g., DataShop) to comply with PSR-12.
 * @todo Implement dependency injection instead of relying on global functions and states.
 * @todo Add strict type declarations to the file (`declare(strict_types=1);`).
 */
class Data_Shop extends Data_Picture {

   /**
    * Data_Shop constructor.
    *
    * Initializes the parent Data_Picture class.
    *
    * @return void
    *
    * @todo Remove commented out debug code.
    */
   function __construct() {
        //echo get_class();
      parent::__construct();

   }


   /**
    * Retrieves a list of shop attributes ordered by type.
    *
    * @param int $id_type Optional type identifier (currently unused).
    * @param int $length Optional length parameter (currently unused).
    * @return array|bool Returns a multi-dimensional array of attributes or false on failure.
    *
    * @todo Remove unused parameters $id_type and $length, or implement them in the query logic.
    * @todo Use prepared statements instead of direct query execution.
    * @todo Add native type hinting (e.g., `public static function getShopAttributeList(int $id_type = 0, int $length = 1): array|bool`).
    */
   static function getShopAttributeList( $id_type = 0, $length = 1 ) {
       $query = 'select `type`, `val`
             from ' . TBL_SHOP_ATTRIBUTES . ' oa order by `type`';
       add_to_fp('$query ' . $query);
       $result = db_query( $query );
       return db_result_array_full($result);
   }

   /**
    * Adds a new shop attribute or updates an existing one if the key duplicates.
    *
    * @param array $param Associative array containing 'type' and 'val' keys.
    * @return array The input parameters merged with a 'status' key indicating the operation result.
    *
    * @todo Use prepared statements instead of manual escaping with db_escape to prevent SQL injection vulnerability.
    * @todo Add array type hinting for the $param argument and define a native return type.
    */
   static function doShopAttributeAddOrUpdate( $param ) {

       $query = 'INSERT INTO ' . TBL_SHOP_ATTRIBUTES . ' (`type`, `val`)
               VALUES ("' . db_escape($param['type']) . '", "' . db_escape($param['val']) . '")
                       ON DUPLICATE KEY UPDATE `type` = "' . db_escape($param['type']) . '",
                       `val` = "' . db_escape($param['val']) . '"';

       add_to_fp($query);
       $result = db_query( $query );
       $ar = db_affected_rows( $result );

       if( $ar == 1 ) return array_merge($param, array('status' => 'SUCCESS,NEW'));
       elseif( $ar == 2 ) return array_merge($param, array('status' => 'SUCCESS,EXIST'));
       else return array_merge($param, array('status' => 'ERROR,UNKNOWN'));
   }

   /**
    * Retrieves shop attributes, optionally filtered by a specific type.
    *
    * @param string|bool $attribute_type Optional attribute type to filter by. Defaults to false.
    * @return string|array|bool Returns the attribute value if a specific type is requested and found,
    * an associative array of all attributes, or false if no results are found.
    *
    * @todo Rename method to camelCase (getShopAttributes) to comply with PSR-12 naming conventions.
    * @todo Avoid using global framework instances (Framework::g_global) inside static methods; inject dependencies instead.
    * @todo Use prepared statements for database queries.
    * @todo Add strict type hinting for parameters and native union return types.
    */
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
