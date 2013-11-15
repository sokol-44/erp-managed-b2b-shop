<?php
/**
 * Shopping_Basket.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
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
   public $id_shopping_basket_history = 0;
   public $res_debug;
   public $params = array(
         'id_client' => 0, 'id_shopping_basket' => 0, 'id_shopping_basket_version' => 0, 'id_shopping_basket_history' => 0,
         'id_nr_shopping_basket' => 0, 'description' => '', 'state' => '',
         'date_create' => null, 'date_modified' => null, 'ts_create' => 0, 'ts_modified' => 0,
         'using_id_client_user' => 0, 'using_session_id' => 0, 'using_name' => '', 'using_date' => 0,  'ts_using' => 0);

   function __construct($params = false, $create = false, $contents = false) {
      $this->reset();
      self::$class = $this;
      if( $params ) {
         $this->params = $params;
         if( $create ) {
            $new_params = $this->db_create_basket();
            $this->params['id_shopping_basket'] = $new_params['id_shopping_basket'];
            $this->params['id_shopping_basket_version'] = $new_params['id_shopping_basket_version'];
            if( $contents ) { 
            	$this->check_contents( $contents );
            }
            $this->id_shopping_basket=$this->params['id_shopping_basket'];
            $this->db_save_contents();
         } else {
            $this->id_shopping_basket=$this->params['id_shopping_basket'];
            $this->db_restore_contents();
            $this->calculate_total();
         }
      }
      $this->id_shopping_basket = $this->params['id_shopping_basket'];
   }
	
   public function check_contents( $contents ) {
   	
   	foreach( $contents as $product ) {
   		$key = Data_Products::get_key_from_product_params( $product );
   		if( (int)$product['id_product'] > 0 && (int)$product['quantity'] > 0 ) {
   			$this->contents[$key] = array(
   				'quantity' => (int)$product['quantity'],
   				'id_product' => (int)$product['id_product'],
   				'id_product_subtype' => (int)$product['id_product_subtype'],
   				'id_shopping_basket_version' => (int)$this->params['id_shopping_basket_version']
   			);
   		}
   	}
   }
	
   static function get_order_data($id_shopping_basket) {
   	  $P = Person::g_global();
   	
   	  if( $P->logged_in ) {
   	  	$params = array('id_shopping_basket' => (int)$id_shopping_basket, 'id_client' => $P->data['id_client']);	
   		$params = Data::get_basket_data($params);
   		if( $P->data['id_client'] == $params['id_client'] && $params['state'] == 'ORDER' ) {
   			return new Shopping_Basket($params);
   		}
   	  }
   	  return false;
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
    
   function state_using_get() {
      $P = Person::g_global();

      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client']) {
         $this->params['using_id_client_user'] = (int)$P->id;
         $this->params['using_session_id'] = $P->session_id;
      } elseif( $this->params['state'] == 'ORDER' ) {
         $this->params['using_id_client_user'] = NULL;
         $this->params['using_session_id'] = NULL;
      } else {
         $this->params['using_id_client_user'] = NULL;
         $this->params['using_session_id'] = NULL;
      }

      return Data::put_basket_update_use_data( $this->params );
   }
   
   function state_change_level($new_lvl) {
      $this->params['using_id_client_user'] = NULL;
      $this->params['using_session_id'] = NULL;
      $this->params['state'] = 'FREE_'  . $new_lvl;

      return Data::put_basket_update_use_data( $this->params );
   }
   
   function state_using_clear() {
      $st = Rights::split_state( $this->params['state'] );
      
      $this->params['using_id_client_user'] = NULL;
      $this->params['using_session_id'] = NULL;
      $this->params['state'] = 'FREE_'  . $st['state_lvl'];

      return Data::put_basket_update_use_data( $this->params );
   }
    
   function state_lock_set() {
      $st = Rights::split_state( $this->params['state'] );

      $this->params['state'] = 'LOCK_'  . $st['state_lvl'];

      return Data::put_basket_update_lock_data( $this->params );
   }
    
   function state_lock_unset( $id_basket_default = 0 ) {
      $P = Person::g_global();
      $st = Rights::split_state( $this->params['state'] );
      
      if( $id_basket_default == $this->id_shopping_basket ) { //$this->params['id_client'] = (int)$P->id;
         $this->params['state'] = 'USE_'  . $st['state_lvl'];
      } else {
         $this->params['state'] = 'FREE_' . $st['state_lvl'];
      }

      return Data::put_basket_update_lock_data( $this->params );
   }

   function state_archive_order() {
      if( $this->check_rights('MAKE_ORDER') ) {
      
         $this->params['using_id_client_user'] = NULL;
         $this->params['using_session_id'] = NULL;
         $this->params['state'] = 'ORDER';
      
         return Data::put_basket_update_use_data( $this->params );
      } else {
         return false;
      }
   }
   
   function add_from_basket( $Shopping_Basket ) {

      $res = $this->check_rights('MODIFY_CONTENTS');

      if( !$res ) {
         Info::g('add', Lang::_('You don\'t have rights for ADD FROM BASKET to basket: ' . $this->id_shopping_basket));
         return false;
      }

      if( $this->_check_valid_basket($Shopping_Basket) ) {
         $in_contents = $Shopping_Basket->contents;
         foreach( $in_contents as $key_product => $product_array ) {
            $this->add_to_basket($product_array, $product_array['quantity']);
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

   function basket_level_change($direction, $param_in = array() ) {
   	$P = Person::g_global();
      if( ( $direction == 'UP'   && $this->check_move('UP') ) ||
          ( $direction == 'DOWN' && $this->check_move('DOWN') ) ) {

         $param_in['now'] = $this->basket_level_nr();
         if( $direction == 'UP' ) {
            $param_in['new'] = $param_in['now'] + 1;
         } else {
            $param_in['new'] = $param_in['now'] - 1;
         }
         
         $this->params['using_id_client_user'] = (int)$P->id;

         $new_id = Data::put_basket_history($this->params, $param_in);
         $this->state_change_level($param_in['new']);
         return $new_id;
      } else {
         return false;
      }
   }

   function basket_level_text( ) {
      $st = Rights::split_state($this->params['state']);
      return $st['state_type'];
   }
   
   function basket_level_nr( ) {
      $st = Rights::split_state($this->params['state']);
      return (int)$st['state_lvl'];
   }

   function basket_level_rights( ) {
      $Rights = Rights::g_global();
      list($level_diff, $res_debug) = $Rights->basket_level_rights( $this->params );
      
      $this->res_debug .= $res_debug;
     
      return $level_diff;
   }
    
   function currently_other_locked( $params = array() ) {
      $P = Person::g_global();

      if( $this->params['using_id_client_user'] != $P->id && $this->basket_level_text() == 'LOCK' ) {
         return true;
      }

      return false;
   }
    
   function currently_user_locked( $params = array() ) {
      $P = Person::g_global();
       
      if( $this->params['using_id_client_user'] == $P->id && $this->basket_level_text() == 'LOCK' ) {
         return true;
      }
       
      return false;
   }
    
   function currently_other_using( $params = array() ) {
      $P = Person::g_global();
      //global param
      $time_diff_ok = time()-1800;

      if( $this->params['using_id_client_user'] != 0 && $this->params['using_session_id'] &&
            ($this->params['using_id_client_user'] != $P->id && $this->params['ts_using'] < $time_diff_ok) ) {
         return true;
      }

      return false;
   }
   

   function check_move( $direction = 'UP') {
      $Rights = Rights::g_global();
      
      if( !$this->check_rights('MODIFY_CONTENTS') ) return false;
      
      if( $direction == 'UP' ) {
         return ($Rights->get_client_max_level() > $this->basket_level_nr());
      } elseif ( $direction == 'DOWN' ) {
         return ($this->basket_level_nr()>0);
      } else {
         return false;
      }
   }
    

   function check_rights( $action, $show_info = true) {
      $P = Person::g_global();
      $Rights = Rights::g_global();

      list($res, $res_debug) = $Rights->basket_rights($this->params, $action, $show_info);

      if( !$res && $show_info ) {
         Info::g('add', Lang::_('You dont have rights for (' . $action . ') basket: ' . $this->id_shopping_basket));
      }

      $this->res_debug .= $res_debug;

      return $res;
   }

   function db_restore_contents() {
      //FIXME
      //DB stuff and merge with DB
      $F = Framework::g_global();
      $P = Person::g_global();

      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client']) {
         $this->params = Data::get_basket_data($this->params);
         
         if( (int)$this->params['using_id_client_user'] > 0 ) {
         	$client_user_data = Person::get_client_user_data( (int)$this->params['using_id_client_user'], 'CLIENT');
         	$this->params['using_name'] = $client_user_data['name'];
         }
         
         //VERSIONS
         $version_list = Data::get_basket_version_list($this->params);
         $last = 0;
         foreach( $version_list as $key => $basket_version ) {
            if( $basket_version['ts_created'] > $last ) {
               $last = $basket_version['ts_created'];
               $this->id_shopping_basket_version = $basket_version['id_shopping_basket_version'];
               $this->params['id_shopping_basket_version'] = $this->id_shopping_basket_version;
            }
            if( $basket_version['id_client_user'] == $P->id ) {
               $basket_version['client_user_name'] = $P->data['name'];
            } else {
               $client_user_data = Person::get_client_user_data( (int)$basket_version['id_client_user'], 'CLIENT');
               $basket_version['client_user_name'] = $client_user_data['name'];
            }
            $this->version[$basket_version['id_shopping_basket_version']] = $basket_version;
         }
         //CONTENTS OF "NEWEST" VER.
         if( Framework::not_null($this->id_shopping_basket_version) ) 
            $this->contents = Data::get_basket_version_product_list( $this->params );
          
         //HISTORY
         $history_list = Data::get_basket_history_list($this->params);

         $last = 0;
         foreach( $history_list as $basket_history ) {
         	if( $basket_history['ts_date'] > $last ) {
         		$last = $basket_history['ts_date'];
         		$this->id_shopping_basket_history = $basket_history['id_shopping_basket_history'];
         		$this->params['id_shopping_basket_history'] = $this->id_shopping_basket_history;
         	}
         	$this->history[$basket_history['id_shopping_basket_history']] = $basket_history;
         }
      }
   }

   function db_create_basket() {
      //DB CREATE
      $F = Framework::g_global();
      $P = Person::g_global();
      $Rights = Rights::g_global();

      $res = array('id_shopping_basket' => 0, 'id_shopping_basket_version' => 0);
      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client']) {
         $this->params['state'] = 'USE_' . $Rights->get_lowest_level();
         $this->state_using_get();
         $res = Data::create_new_basket($this->params);
      }
      return $res;
   }
    
   function db_save_using() {
      //DB SAVE
      $P = Person::g_global();
       
      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client']) {
         Data::put_basket_version_product_list($this->contents, $this->params);
      }
   }
    
   function db_save_contents() {
      //DB SAVE
      $F = Framework::g_global();
      $P = Person::g_global();
      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client']) {
         Data::put_basket_version_product_list($this->contents, $this->params);
         //$history_last = Data::get_basket_history_last($this->params);
         Data::put_basket_info($this->params, (int)$this->id_shopping_basket_history);
      }
   }
    
   function db_remove_product( $product_key ) {
      $P = Person::g_global();

      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client'] && Framework::not_null($product_key) ) {
         Data::remove_basket_product( $product_key, $this->params);
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
    
   function add_check_version() {
      $P = Person::g_global();

      if( $P->logged_in && $P->data['id_client'] == $this->params['id_client']) {
         $current_client_user_id = (int)$this->version[(int)$this->id_shopping_basket_version]['id_client_user'];
         if( (int)$P->data['id_client_user'] != (int)$current_client_user_id &&
               Framework::not_null($this->contents) ) {

            $basket_new_version = Data::add_basket_new_version($this->params, (int)$P->data['id_client_user']);

            $this->id_shopping_basket_version = (int)$basket_new_version['id_shopping_basket_version'];
            $this->version[$this->id_shopping_basket_version] = $basket_new_version;
            $this->params['id_shopping_basket_version'] = $this->id_shopping_basket_version;

            foreach( $this->contents as $key_product => $product_params ) {
               $this->contents[$key_product]['id_shopping_basket_version'] = $this->id_shopping_basket_version;
            }
            $FD = File_Debug::g_global();
            $FD->s('ADD');
            $FD->s(array('$this->version' => $this->version, '$this->id_shopping_basket_version' => $this->id_shopping_basket_version));

            return true;
         }
      }
      $FD = File_Debug::g_global();
      $FD->s('NOT');
      $FD->s(array('$this->version' => $this->version, '$this->id_shopping_basket_version' => $this->id_shopping_basket_version));

      return false;
   }

   function add_to_basket($product_params, $quantity = 1, $save = true) {
      $res = $this->check_rights('MODIFY_CONTENTS');

      if( !$res ) {
         Info::g('add', Lang::_('You don\'t have rights for ADD to basket: ' . $this->id_shopping_basket));
         return false;
      }

      $this->add_check_version();

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

   function update_basket_quantity_list($product_quantity_array, $description = false) {

      $res = $this->check_rights('MODIFY_CONTENTS');

      if( !$res ) {
         Info::g('add', Lang::_('You don\'t have rights for UPDATE QUANTITY LIST to basket: ' . $this->id_shopping_basket));
         return false;
      }
       
      foreach($product_quantity_array as $key_product => $quantity) {
         $this->update_product_quantity($key_product, (int)$quantity, false);
      }
      if( $description ) $this->params['description'] = $description;
      //TODO
      //only update in DB
      self::db_save_contents();
   }

   function update_product_quantity($key_product, $quantity = 0, $save = true) {

      $res = $this->check_rights('MODIFY_CONTENTS');

      if( !$res ) {
         Info::g('add', Lang::_('You don\'t have rights for UPDATE QUANTITY to basket: ' . $this->id_shopping_basket));
         return false;
      }

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
    
   function get_id_nr_shopping_basket() {
      return $this->params['id_nr_shopping_basket'];
   }

   function check_product_in($key_product) {
      if (isset($this->contents[$key_product])) {
         return true;
      } else {
         return false;
      }
   }

   function remove_from_basket( $product_key ) {

      $res = $this->check_rights('MODIFY_CONTENTS');

      if( !$res ) {
         Info::g('add', Lang::_('You don\'t have rights for REMOVE to basket: ' . $this->id_shopping_basket));
         return false;
      }

      if( isset($this->contents[$key]) ) {
         unset( $this->contents[$key] );
      }

      if( $this->add_check_version() ) {
         $this->db_save_contents();
      }
       
      return $this->db_remove_product( $product_key );
   }

   function remove_all_product() {

      $res = $this->check_rights('MODIFY_CONTENTS');

      if( !$res ) {
         Info::g('add', Lang::_('You don\'t have rights for REMOVE ALL to basket: ' . $this->id_shopping_basket));
         return false;
      }

      $this->reset();
      //TODO
      //only delete
      $this->db_save_contents();
   }
    
   function get_version_list() {
      return array_keys($this->version);
   }

   function get_product_key_list( $contents = false ) {
      if( $contents === FALSE ) return array_keys($this->contents);
      else return array_keys($contents);
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
    
   function get_all_version() {
      return $this->version;
   }
    
   function get_all_history() {
      return $this->history;
   }
   
   function get_last_history() {
      return $this->history[$this->id_shopping_basket_history];
   }
    
    
   function get_all_product() {
      $F = Framework::g_global();

      $this->product_info_array = array();

      if ( is_array($this->contents) && $F->not_null($this->contents) ) {
         $product_id_array = $this->get_product_key_list();
         $product_info_array = Data::get_product_info_list( $product_id_array );

         foreach ($product_info_array  as $key => $product_info) {
         	$product_info_array[$key]['db_quantity'] =  $product_info_array[$key]['quantity'];
            $product_info_array[$key]['quantity'] = $this->contents[$key]['quantity'];
         }
          
         $this->product_info_array = $product_info_array;
      }

      return $this->product_info_array;
   }
    
   function get_all_product_version( $id_shopping_basket_version = 0 ) {
      $F = Framework::g_global();

      $product_info_array = array();

      $basket_params = $this->params;
      $basket_params['id_shopping_basket_version'] = (int)$id_shopping_basket_version;

      $contents = Data::get_basket_version_product_list( $basket_params );

      if ( is_array($contents) && $F->not_null($contents) ) {
         $product_id_array = $this->get_product_key_list( $contents );
         $product_info_array = Data::get_product_info_list( $product_id_array );
          
         foreach ($product_info_array  as $key => $product_info) {
            $product_info_array[$key]['quantity'] = $contents[$key]['quantity'];
         }
      }

      return $product_info_array;
   }

   function remove_basket() {

      $res = $this->check_rights('MODIFY_CONTENTS');

      if( !$res ) {
         Info::sadd(Lang::_('You don\'t have rights for REMOVE BASKET to basket: ' . $this->id_shopping_basket));
         return false;
      }

      // Data::remove_basket( $this->id_client, $id_nr_shopping_basket );
      $version_list = $this->get_version_list();
      return Data::remove_basket( $this->params, $version_list );
   }
    
    
   function calculate_total() {
      $this->total = array('product_total' => 0, 'product_types' => 0,
            'sum_gross' => 0, 'sum_gross_split' => array(), 'sum_netto' => 0);

      if ( Framework::not_null($this->contents) && $this->total['product_total'] == 0 ) {
         $this->get_all_product();
          
         foreach($this->product_info_array as $key_product => $product ) {
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