<?php
/**
 * Shopping_Basket.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Micha� Soko�owski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Shopping_Basket {
   static $class = false;
   public $contents = array();
   public $product_info_array = array();
   public $version = array();
   public $history = array();
   public $id_shopping_basket = 0;
   public $id_shopping_basket_version = 0;
   public $params = array(
      	'id_client' => 0, 'id_shopping_basket' => 0, 'id_shopping_basket_version' => 0,
         'id_nr_shopping_basket' => 0, 'description' => 0,
         'date_create' => null, 'date_modified' => null, 'ts_create' => 0, 'ts_modified' => 0,
      	'using_id_client_user' => 0, 'using_session_id' => 0, 'using_date' => 0,  'ts_using' => 0);

   function __construct($params = false, $create = false) {
      $this->reset();
      self::$class = $this;
       if( $params ) {
         $this->params = $params;
         if( $create ) {
            $new_params = $this->db_create_basket();
            $this->params['id_shopping_basket'] = $new_params['id_shopping_basket'];
            $this->params['id_shopping_basket_version'] = $new_params['id_shopping_basket_version'];
            $this->db_save_contents();
         } else {
            $this->db_restore_contents();
            $this->calculate_total();
         }
      }
      $this->id_shopping_basket = $this->params['id_shopping_basket'];
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
            $this->add_to_basket($product_array, false);
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
      if( $this->params['using_id_client_user'] != $P->id && $this->params['ts_using'] < $time_diff_ok ) {
         return true;
      }

      return false;
   }

   function db_restore_contents() {
      //FIXME
      //DB stuff and merge with DB
      $F = Framework::g_global();
      $P = Person::g_global();

      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client']) {
         //$this->params = Data::get_basket_data($this->params);
         
         //VERSIONS
         $version_list = Data::get_basket_version_list($this->params);
//          var_dump($version_list);
         $last = 0;
         foreach( $version_list as $key => $basket_version ) {
            if( $basket_version['ts_create'] > $last ) {
                 $last = $basket_version['ts_create'];
                 $this->id_shopping_basket_version = $basket_version['id_shopping_basket_version'];
                 $this->params['id_shopping_basket_version'] = $this->id_shopping_basket_version;
            }
            $this->version[$basket_version['id_shopping_basket_version']] = $basket_version;
         }
         //CONTENTS OF "NEWEST" VER.
         if( Framework::not_null($this->version) )
            $this->contents = Data::get_basket_version_product_list( $this->version[$this->id_shopping_basket_version] );
      }
   }

   function db_create_basket() {
      //DB CREATE
      $F = Framework::g_global();
      $P = Person::g_global();
      
      $res = array('id_shopping_basket' => 0, 'id_shopping_basket_version' => 0);
      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client']) {
         $res = Data::create_new_basket($this->params);
      }
      return $res;
   }

   function db_save_contents() {
      //DB SAVE
      $F = Framework::g_global();
      $P = Person::g_global();

      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client']) {
         Data::put_basket_version_product_list($this->contents, $this->params);
      }
   }
    
   function db_remove_product( $product_params ) {
      $P = Person::g_global();

      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client'] && $product_params['id_product'] > 0 ) {
         Data::remove_basket_product( $product_params, $this->params);
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

   function add_to_basket($product_params, $quantity = 1, $save = true) {
      $id_product = (int)$product_params['id_product'];
      $id_product_subtype = (int)$product_params['id_product_subtype'];
      $key_product = Data::get_key_from_product_params($product_params);
      if( isset($this->contents[$key_product]) ) {
         $this->contents[$key_product]['quantity'] = $this->contents[$key_product]['quantity'] + (int)$quantity;
      } else {
         $this->contents[$key_product] = array(
               'quantity' => (int)$quantity,
               'id_product' => $id_product,
               'id_product_subtype' => $id_product_subtype,
               'id_shopping_basket_version' => (int)$this->id_shopping_basket_version
               );
      }
      if( $save ) self::db_save_contents();
      return $this->contents[$key_product]['quantity'];
   }

   function update_basket_quantity_list($product_array, $description = false) {
      
      foreach($product_array as $key_product => $product_params) {
         $this->update_product_quantity($product_params, (int)$product_params['quantity'], false);
      }
      if( $description ) $this->params['description'] = $description;
      //TODO
      //only update in DB
      self::db_save_contents();
   }

   function update_product_quantity($product_params, $quantity = 0, $save = true) {
      $id_product = (int)$id_product;
      $key_product = Data::get_key_from_product_params($product_params);
      if( isset($this->contents[$key_product]) ) {
         $this->contents[$key_product]['quantity'] = (int)$quantity;
         if( $save ) self::db_save_contents();
         return $this->contents[$key_product]['quantity'];
      } else {
         return 0;
      }
   }

   function get_product_quantity($product_params) {
      $key_product = Data::get_key_from_product_params($product_params);
      if( isset($this->contents[$key_product]) ) {
         return $this->contents[$key_product]['quantity'];
      } else {
         return 0;
      }
   }
   
   function get_id_client() {
      return $this->params['id_client'];
   }

   function check_product_in($key_product) {
      if (isset($this->contents[$key_product])) {
         return true;
      } else {
         return false;
      }
   }

   function remove_from_basket( $product_params ) {
      $key = Data::get_key_from_product_params($product_params);
      if( isset($this->contents[$key]) ) {
         unset( $this->contents[$key] );
      }
      
      self::db_remove_product( $product_params );
   }

   function remove_all_product() {
      $this->reset();
      //TODO
      //only delete
      self::db_save_contents();
   }
   
   function get_version_list() {
      return array_keys($this->version);
   }

   function get_product_id_list() {
      return array_keys($this->contents);
   }

   function get_quantity( $product_params ) {
      $key = Data::get_key_from_product_params($product_params);
       if( isset($this->contents[$key]) ) {
         return $this->contents[$key]['quantity'];
      } else {
         return 0;
      }
   }

   function get_all_product() {
      $F = Framework::g_global();
      
      if (!is_array($this->contents)) return false;

      if( !$F::not_null($this->product_array) ) {

         $product_id_array = $this->get_product_id_list();

         $product_info_array = Data::get_product_info_list( $product_id_array );

         $product_array = array();
         foreach($product_info_array as $product_info ) {
            $key = Data::get_key_from_product_params($product_info);
            if ( Framework::not_null($this->contents[$key]) ) {
               $product_array[$key] = array('id_product' => (int)$this->contents[$key]['id_product'],
                                    'id_product_subtype' => (int)$this->contents[$key]['id_product_subtype'],
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
                  
                     if( !$F::not_null($product_subtype['description']) )
                        $product_array[$key]['description'] .= $product_subtype['description'];
                     
                     if( !$F::not_null($product_subtype['picture_small_url']) )
                        $product_array[$key]['picture_small_url'] = $product_subtype['picture_small_url'];
                     
                     if( !$F::not_null($product_subtype['picture_big_url']) )
                        $product_array[$key]['picture_big_url'] = $product_subtype['picture_big_url'];
                     
                     if( !$F::not_null($product_subtype['picture_id']) )
                        $product_array[$key]['picture_id'] = $product_subtype['picture_id'];
                     
                     if( !$F::not_null($product_subtype['price_diff']) )
                        $product_array[$key]['price'] += $product_subtype['price_diff'];
                  
               }
            }
         }
         //      foreach ($product_array as $key => $row) {
         //         $customers_username[$key] = strtolower($row['customers_username']);
         //         $products_name[$key] = strtolower($row['name']);
         //      }
         //      @array_multisort($customers_username, SORT_ASC, $products_name, SORT_ASC, $product_array);
         $this->product_info_array = $product_array;
      }

      return $this->product_info_array;
   }

   function remove_basket() {
      // Data::remove_basket( $this->id_client, $id_nr_shopping_basket );
      $version_list = $this->get_version_list();
      Data::remove_basket( $this->params, $version_list );
   }
   
   
   function calculate_total() {
      $this->total = array('product_total' => 0, 'product_types' => 0,
      'sum_gross' => 0, 'sum_gross_split' => array(), 'sum_netto' => 0);

      if ( Framework::not_null($this->contents) && $this->total['product_total'] == 0 ) {
         $this->get_all_product();
         foreach($this->product_array as $key_product => $product ) {
            $this->total['product_total'] += $product['quantity'];
            $this->total['product_types'] ++;
            $this->total['sum_gross_split'][$product['vat']] += Price::add_vat($product['price'], $product['vat'], $product['quantity']);
            $this->total['sum_netto'] += ($product['price'] * $product['quantity']);
         }
         foreach( $this->total['sum_gross_split'] as $vat => $vat_value ) {
            $this->total['sum_gross_split'][$vat] = Price::rount_tax($vat_value);
            $this->total['sum_gross'] += Price::rount_tax($vat_value);
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