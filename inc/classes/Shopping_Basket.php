<?php
/**
 * Shopping_Basket.php Global initialization file
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
   public $id_nr_shopping_basket = 0;
   public $params = array(
      	'id_client' => 0, 'id_nr_shopping_basket' => 0, 'description' => 0,
         'date_create' => null, 'date_modified' => null, 'ts_create' => 0, 'ts_modified' => 0,
      	'using_id_client_user' => 0, 'using_session_id' => 0, 'using_date' => 0,  'ts_using' => 0);
   //   static $GET_raw = '', $GET_array = array();

   function __construct($params = false, $create = false) {
      $this->reset();
      self::$class = $this;
      //      echo '$params'.(($params)?'_t_':'_f_') . ' $create'.(($create)?'_t_':'_f_')."<br>";
      if( $params ) {
         $this->params = $params;
         if( $create ) {
            $this->db_save_contents();
         } else {
            $this->db_restore_contents();
         }
      }
      $this->id_nr_shopping_basket = $this->params['id_nr_shopping_basket'];
   }

   static function g_global() {
      if(self::$class == false) {
         self::$class = new Shopping_Basket();
      }
      return self::$class;
   }

   static function _check_valid_basket( $Shopping_Basket ) {
      if ( is_a($Shopping_Basket, 'Shopping_Basket') ) {
         return true;
      } else {
         return false;
      }
   }

   function add_from_basket( $Shopping_Basket ) {
      if( $this->_check_valid_basket($Shopping_Basket) ) {
         $in_contents = $Shopping_Basket->contents;
         foreach( $in_contents as $id_product => $product_array ) {
            $this->add_to_basket($id_product, $product_array['quantity'], false);
         }
         $in_description = $Shopping_Basket->params['description'];
         if( Framework::not_null($in_description) ) {
            $this->params['description'] .= "\n" . $in_description;
         }
         self::db_save_contents();
         return true;
      } else {
         return false;
      }
   }

   function update_person( $id_nr_shopping_basket = 0 , $save = true) {
      $P = Person::g_global();
      $this->params['id_client'] = (int)$P->data['id_client'];
      if( $id_nr_shopping_basket > 0 ) {
         $this->params['id_nr_shopping_basket'] = (int)$id_nr_shopping_basket;
      }
      if( $save ) self::db_save_contents();
   }

   function currently_other_using( $params = array() ) {
      $P = Person::g_global();
      //global param
      $time_diff_ok = time()-1800;

      //FIXME
      //check in db
      //echo $this->params['using_id_client_user']." != ".$P->id.' && '.$this->params['ts_using'].'  > '.$time_diff_ok;
      if( $this->params['using_id_client_user'] != $P->id && $this->params['ts_using'] > $time_diff_ok ) {
         return true;
      }

      return false;
   }

   function db_restore_contents() {
      //FIXME
      //DB stuff
      // and merge with DB
      $F = Framework::g_global();
      $P = Person::g_global();

      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client']) {
         $this->params = Data::get_basket_data($this->params);
         $this->contents = Data::get_basket_product_list($this->params);
      }
   }

   function db_save_contents() {
      //DB SAVE
      $F = Framework::g_global();
      $P = Person::g_global();
      
//     print_debug($this); die();

      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client']) {
         Data::put_basket_data($this->params);
         Data::put_basket_product_list($this->contents, $this->params);
      }
   }
    
   function db_remove_product( $id_product = 0 ) {
      $P = Person::g_global();

      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client'] && $id_product > 0 ) {
         Data::remove_basket_product( (int)$id_product, $this->params);
      }
   }

   function db_clean_contents() {

   }

   function reset($reset_database = false) {
      global $customer_id;

      $this->contents = array();
      $this->total = array('product_total' => 0, 'product_types' => 0, 'sum_gross' => 0, 'sum_netto' => 0);

      if ( $reset_database == true ) {
         //remove from DB
      }
   }

   function add_to_basket($id_product, $quantity = 1, $save = true) {
      $id_product = (int)$id_product;
      if( isset($this->contents[$id_product]) ) {
         $this->contents[$id_product]['quantity'] = $this->contents[$id_product]['quantity'] + (int)$quantity;
      } else {
         $this->contents[$id_product]['quantity'] = (int)$quantity;
      }
      if( $save ) self::db_save_contents();
      return $this->contents[$id_product]['quantity'];
   }

   function update_basket_quantity_list($array, $description = false) {
      foreach($array as $id_product => $array_quantity) {
         $this->update_product_quantity((int)$id_product, (int)$array_quantity['quantity'], false);
      }
      if( $description ) $this->params['description'] = $description;
      //TODO
      //only update in DB
      self::db_save_contents();
   }

   function update_product_quantity($id_product, $quantity = 0, $save = true) {
      $id_product = (int)$id_product;
      if( isset($this->contents[$id_product]) ) {
         $this->contents[$id_product]['quantity'] = (int)$quantity;
         if( $save ) self::db_save_contents();
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
      //TODO
      //only delete 1 row
      self::db_remove_product( $id_product );
   }

   function remove_all_product() {
      $this->reset();
      //TODO
      //only delete
      self::db_save_contents();
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

      if( !Framework::not_null($this->product_array) ) {

         $product_id_array = $this->get_product_id_list();

         $product_info_array = Data::get_product_info_list( $product_id_array );

         $product_array = array();
         foreach($product_info_array as $product_info ) {
            $id_product = (int)$product_info['id_product'];
            if ( Framework::not_null($this->contents[$id_product]) ) {
               $product_array[$id_product] = array('id_product' => $id_product,
                                    'name' => $product_info['name'],
                                    'description' => $product_info['description'],
                                    'picture_small_url' => $product_info['picture_small_url'],
                                    'picture_big_url' => $product_info['picture_big_url'],
                                    'picture_id' => $product_info['picture_id'],
                                    'price' => $product_info['price'],
            								'vat' => $product_info['vat'],
                                    'quantity' => $this->contents[$id_product]['quantity']
               );
            }
         }
         //      foreach ($product_array as $key => $row) {
         //         $customers_username[$key] = strtolower($row['customers_username']);
         //         $products_name[$key] = strtolower($row['name']);
         //      }
         //      @array_multisort($customers_username, SORT_ASC, $products_name, SORT_ASC, $product_array);
         $this->product_array = $product_array;
      }

      return $this->product_array;
   }

   function calculate_total() {
      $this->total = array('product_total' => 0, 'product_types' => 0, 'sum_gross' => 0, 'sum_netto' => 0);

      if ( Framework::not_null($this->contents) ) {
         $this->get_all_product();
         foreach($this->product_array as $id_product => $product ) {
            $this->total['product_total'] += $product['quantity'];
            $this->total['product_types'] ++;
            $this->total['sum_gross'] += Price::add_vat($product['price'], $product['vat'], $product['quantity']);
            $this->total['sum_netto'] += ($product['price'] * $product['quantity']);
         }
      }
      return $this->total;
   }

   function get_total() {
      $this->calculate();

      return $this->total;
   }

   function __sleep() {
      unset($this->product_array);
      return( array_keys( get_object_vars( $this ) ) );
   }

   function __wakeup() {
      self::$class = $this;
   }

}

?>