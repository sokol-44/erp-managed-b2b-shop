<?php
/**
 * Order.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/**
 * Data class goint to provide data and operation on them
 * Application will get and save any data thru it
 */

class Order {
   public $data = array();
   public $product_list = array();
   public $address = array();
   public $status_history = array();
   public $source_basket = array();
   public $mode = false;
   public $id_order = 0;

   function __construct( $id_order = 0, $mode = 'FULL',  $data = false) {
      $this->data = array();
      $this->product_list = array();
      $this->status_history = array();
      $this->set_mode( $mode );

      if( $id_order > 0 ) {
         return $this->load_data( (int)$id_order, $data );
      }

   }
   
   function set_mode( $mode_in ) {
   	 if( $this->mode ) {
   	 	if( $this->mode == 'SIMPLE' && $mode_in == 'FULL' ) {
   	 	  $this->load_data_full();
   	 	  $this->mode = 'FULL';
   	 	}
   	 } else {
   	 	if( $mode_in == 'FULL' && $mode_in == 'SIMPLE' )  $this->mode = $mode_in;
   	 	else $this->mode = 'FULL';
   	 }
   }

   function check_rights( $id_client = 0 ) {
      if( Framework::not_null($this->data) ) {
         $P = Person::g_global();
         if( $id_client == 0 ) $id_client = $P->data['id_client'];
         //echo $this->data['id_client'] .'=='. $id_client;die();
         return ( $this->data['id_client'] == $id_client );
      } else {
         return false;
      }
   }
    
   function calculate_total() {
      
      if ( Framework::not_null($this->product_list) && !isset($this->total['product_total']) ) {
      	 $this->total = array('product_total' => 0, 'product_types' => 0,
      			'sum_gross' => 0, 'sum_gross_split' => array(), 'sum_netto' => 0);
      	 
         foreach($this->product_list as $id_product => $product ) {
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
   
   function get_address() {
   	if( Framework::is_null($this->address) ) $this->address = Data::get_address( (int)$this->data['id_address'] );
   	return $this->address;
   }

   function load_data( $id_order, $data = false ) {
      $F = Framework::g_global();

      $this->id_order = $id_order;

      if( $data ) $this->data = $data;
      else  $this->data = Data::get_order_data( $id_order );
    
      
      if( $F->not_null($this->data) ) {
         $this->product_list = Data::get_order_product_list( $id_order );
         if( $this->mode == 'FULL' ) $this->load_data_full();
         return sizeof($this->product_list);
      } else {
         return false;
      }
   }
   
   function load_data_full() {
   	 $this->status_history = Data::get_order_status_history_list( (int)$this->id_order );
   	 $this->source_basket = Shopping_Basket::get_order_data( (int)$this->data['id_shopping_basket'] );	
   }

   static function make_new_order($Shopping_Basket, $param_in) {
      $F = Framework::g_global();
      $P = Person::g_global();

      if( $Shopping_Basket->params['id_client'] != $P->data['id_client'] || !$P->check_roles('LEVEL_99') ||
      !Shopping_Basket::_check_valid_basket($Shopping_Basket) )  {
         return false;
      }


      $product_list = $Shopping_Basket->get_all_product();

      if( !$F->not_null($param_in['order_description']) )  $param_in['order_description'] = 'NULL';
       
      $id_order = Data::put_order_data($P->data['id_client'], $Shopping_Basket->params, $param_in);
      if( $id_order > 0 ) {
      	$attributes = array();
      	
         $Shopping_Basket->state_archive_order();
         $count_product   = Data::put_order_product_list($id_order, $product_list);
         $count_product_2 = Data::change_product_quantity_list($product_list);
         $id_soh = (int)Order_History::text2id('OSH_START');
         Data::put_order_status($id_order, $id_soh, $param_in['order_description']);
         foreach(Data::$Data_order_params as $attr_key => $attr_val) {
         	if( isset($param_in[$attr_key]) && $F->not_null($param_in[$attr_key]) ) {
         		$attributes[$attr_key] = $param_in[$attr_key];
         	}
         }
         
         if( $F->not_null($attributes) ) {
         	Data::put_order_attributes_list($id_order, $attributes);
         }
         
         return array($id_order, $count_product);
      } else {
         return false;
      }
   }

}

?>