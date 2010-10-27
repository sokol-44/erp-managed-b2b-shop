<?php
/**
 * Szhopping_Cart.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Micha� Soko�owski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/**
 * @author ms
 *
 */
class Shopping_Basket {
   static $class = false;
   public $contents = array();

   //   static $GET_raw = '', $GET_array = array();

   function __construct() {
      $this->reset();
      self::$class = $this;
   }

   static function g_global() {
      if(self::$class == false) {
         self::$class = new Shopping_Basket();
      }
      return self::$class;
   }



   function restore_contents() {
   }

   function reset($reset_database = false) {
      global $customer_id;

      $this->contents = array();
      $this->total = array('product_total' => 0, 'product_count' => 0, 'sum_gross' => 0, 'sum_netto' => 0);

      if ( $reset_database == true ) {
         //remove from DB
      }
   }

   function add_to_basket($id_product, $quantity = 1) {
      $id_product = (int)$id_product;
      if( isset($this->contents[$id_product]) ) {
         $this->contents[$id_product]['quantity'] = $this->contents[$id_product]['quantity'] + (int)$quantity;
      } else {
         $this->contents[$id_product]['quantity'] = (int)$quantity;
      }
      return $this->contents[$id_product]['quantity'];
   }

   function update_basket_quantity_list($array) {
      foreach($array as $id_product => $array_quantity) {
         $this->update_product_quantity((int)$id_product, (int)$array_quantity['quantity']);
      }
   }

   function update_product_quantity($id_product, $quantity = 0) {
      $id_product = (int)$id_product;
      if( isset($this->contents[$id_product]) ) {
         $this->contents[$id_product]['quantity'] = (int)$quantity;
         return $this->contents[$id_product]['quantity'];
      } else {
         return 0;
      }
   }

   function get_product_quantity($id_product) {
      if (isset($this->contents[$id_product])) {
         return $this->contents[$id_product]['quantity'];
      } else {
         return 0;
      }
   }

   function check_product_in($id_product) {
      if (isset($this->contents[(int)$id_product])) {
         return true;
      } else {
         return false;
      }
   }

   function remove_from_basket($id_product) {
      if (isset($this->contents[$id_product])) {
         unset( $this->contents[$id_product] );
      }
   }

   function remove_all_product() {
      $this->reset();
   }

   function get_product_id_list() {
      return array_keys($this->contents);
   }

   function get_quantity($id_product) {
      if (isset($this->contents[$id_product])) {
         return $this->contents[$id_product]['quantity'];
      }
   }

   function get_all_product() {

      if (!is_array($this->contents)) return false;

      $product_id_array = $this->get_product_id_list();

      $product_info_array = Data::get_product_info_list( $product_id_array );

      $products_array = array();
      foreach($product_info_array as $product_info ) {
         if ( Framework::not_null($this->contents[$product_info['id_product']]) ) {
            $product_array[] = array('id_product' => $product_info['id_product'],
                                    'name' => $product_info['name'],
                                    'description' => $product_info['description'],
                                    'picture_small_url' => $product_info['picture_small_url'],
                                    'picture_big_url' => $product_info['picture_big_url'],
                                    'picture_id' => $product_info['picture_id'],
                                    'price' => $product_info['price'],
            								'vat' => $product_info['vat'],
                                    'quantity' => $this->contents[$product_info['id_product']]['quantity']
            );
         }
      }
      //      foreach ($product_array as $key => $row) {
      //         $customers_username[$key] = strtolower($row['customers_username']);
      //         $products_name[$key] = strtolower($row['name']);
      //      }
      //      @array_multisort($customers_username, SORT_ASC, $products_name, SORT_ASC, $product_array);

      return $product_array;
   }

   function calculate() {
      $sums_product_total = 0;
      $sums_product_count = 0;
      if ( Framework::not_null($this->contents) ) {
         foreach($this->contents as $id_product => $product_data ) {
            $sums_product_total = $sums_product_total + $product_data['quantity'];
            $sums_product_count++;
         }
      }
      $this->total = array('product_total' => $sums_product_total,
      						'product_count' => $sums_product_count);

   }

   function count_contents() {  // get total number of items in cart

      return $this->total;
   }

   function show_total() {
      $this->calculate();

      return $this->total;
   }

   function __sleep() {
      unset($this->sellers);
      unset($this->delivery_opt);
      return( array_keys( get_object_vars( $this ) ) );
   }

   function __wakeup() {
      self::$class = $this;
   }

}

?>