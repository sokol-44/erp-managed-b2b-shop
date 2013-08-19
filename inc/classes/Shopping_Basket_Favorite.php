<?php
/**
 * Shopping_Basket.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Micha� Soko�owski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Shopping_Basket_Favorite {
   static $class = false;
   public $id_shopping_basket_favorite = 0 ;
   public $product_list = array();
   public $res_debug = '';
   public $total = array('product_total' => 0, 'product_types' => 0,
   		'sum_gross' => 0, 'sum_gross_split' => array(), 'sum_netto' => 0);
   public $params = array(
         'id_client' => 0, 'id_client_user' => 0, 'id_shopping_basket_favorite' => 0,
         'date_created' => '', 'date_modified' => '', 'ts_created' => 0, 'ts_modified' => 0,
         'rights_edit' => '', 'rights_use' => '');

   function __construct( $params = false) {
      self::$class = $this;
      
      if( is_numeric( $params ) && (int)$params>0 ) {
         //$this->params = $params;
      	//$this->id_shopping_basket_favorite = 
         $params_loc = Data::get_basket_favorite_data( (int)$params );
      	if( $this->check_rights($params_loc, 'USE', true) ) {
      		$this->product_list = $this->make_new_basket_db($params_loc);
      	}
      } elseif( is_object($params) && get_class($params) == 'Shopping_Basket' ) {
      	$this->make_new_basket( $params, $basket_favorite_description );
      } elseif( is_object($params) && get_class($params) == 'Order' ) {

      } elseif( is_array($params) && $this->check_valid($params) ) {
      	$this->params = $params;
      	$this->product_list = $this->make_new_basket_db( $params );
      }
      return false;
   }
   
   function check_valid( $params ) {
   	foreach( $this->params as $key => $val) {
   		if( !array_key_exists($key, $params) ) {
   			return false;
   		}
   	}
   	return true;
   }

   function make_new_basket_db( $params ) {
   	$product_list = unserialize($params['serialize']);
   	unset($params['serialize']);
   	$this->id_shopping_basket_favorite = (int)$params['id_shopping_basket_favorite'];
   	$this->params = $params;

   	$product_tmp = Data::get_product_info_list( array_keys($product_list) );
   	foreach($product_list as $prod_key => $product) {
   		if( isset($product_tmp[$prod_key]) ) {
   			$product_list[$prod_key]['price'] = $product_tmp[$prod_key]['price'];
   			$product_list[$prod_key]['vat'] = $product_tmp[$prod_key]['vat'];
   		} else {
   			$product_list[$prod_key]['price'] = 0;
   			$product_list[$prod_key]['vat'] = 0;
   			$product_list[$prod_key]['quantity'] = 0;
   		}
   	}
   	return $product_list;
   }

   static function get_basket_favorite_list() {
   	$F = Framework::g_global();
   	$P = Person::g_global();
   	
   	$obj_array = array();
   	
   	if( $P->logged_in ) {
   		$res = Data::get_basket_favorite_list((int)$P->data['id_client'], (int)$P->id);
   		foreach( $res as $params_in ) {
   			$obj_array[$params_in['id_shopping_basket_favorite']] = new Shopping_Basket_Favorite( $params_in );
   		}
   		return $obj_array;
   	} else {
   		return array();
   	}
		
   }
   
   function calculate_total() {
   
   	if ( Framework::not_null($this->product_list) && $this->total['product_total']==0 ) {
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
   
   
   static function make_new_basket_basket($Shopping_Basket, $basket_description = '') {
   	$F = Framework::g_global();
   	$P = Person::g_global();
   
   	if( $Shopping_Basket->params['id_client'] != $P->data['id_client'] &&
   	!Shopping_Basket::_check_valid_basket($Shopping_Basket) )  {
   		return false;
   	}
   
   	$product_list = $Shopping_Basket->get_all_product();
   
   	if( !$F->not_null($order_description) ) $order_description = 'NULL';
   	 
   	$id_shopping_basket_favorite = Data::make_new_basket_version_data($Shopping_Basket->params, $product_list, $basket_description);
   	if( $id_order > 0 ) {
   		return $id_shopping_basket_favorite;
   	} else {
   		return false;
   	}
   }
   

   function check_rights( $params, $action, $show_info = true) {
      $P = Person::g_global();
      $Rights = Rights::g_global();

      list($res, $res_debug) = $Rights->basket_favorite_rights($params, $action, $show_info);

      if( !$res && $show_info ) {
         Info::g('add', Lang::_('You dont have rights for (' . $action . ') favorite basket: ' . (int)$this->id_shopping_basket_favorite));
      }

      $this->res_debug .= $res_debug;

      return $res;
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