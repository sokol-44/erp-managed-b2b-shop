<?php
/**
 * Invoice.php
 * Copyright Michał Sokołowski 2013
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/**
 * Data class goint to provide data and operation on them
 * Application will get and save any data thru it
 */

class Invoice {
   static $class = false;
   public $res_debug = '';
   public $params = array(
         'id_client' => 0,  'id_order' => 0, 'id_invoice' => 0, 'state' => '',
   		'net_value' => 0, 'gross_value' => '', 'invoice_number' => '', 'invoice_image' => '',
         'date_issue' => '', 'date_pay' => '', 'ts_issue' => 0, 'ts_pay' => 0);

   function __construct( $params = false) {

      if( is_numeric( $params ) && (int)$params>0 ) {
         $params_loc = Data::get_invoice_data( (int)$params );
      } elseif( is_array($params) && $this->check_valid($params) ) {
      	$this->params = $params;
      }
      return $this;
   }
   
   function check_valid( $params ) {
   	foreach( $this->params as $key => $val) {
   		if( !array_key_exists($key, $params) ) {
   			return false;
   		}
   	}
   	return true;
   }
   
   static function get_invoice_list($id_client = 0) {
   	$F = Framework::g_global();
   	$P = Person::g_global();
   	$Rights = Rights::g_global();
   	
   	$obj_array = array();
   	$total_array = array('count' => 0, 'sum_gross' => 0, 'sum_netto' => 0);

   	if( $F->is_null($id_client) ) {
   		if( $P->logged_in ) {
   			$id_client = (int)$P->data['id_client'];
   		} else {
   			return array();
   		}
   	}

   	$params = compact('id_client');
   	
   	list($res, $res_debug) = $Rights->invoice_rights($params, 'SHOW', $show_info);
   
   	if( $res ) {
   		$res = Data::get_invoice_list($params);
   		foreach( $res as $params_in ) {
   			$obj_array[$params_in['id_invoice']] = new Invoice( $params_in );
   			if( $F->not_null($obj_array[$params_in['id_invoice']]) ) {
   				$total_array['count']++;
   				$total_array['sum_gross'] += $params_in['net_value'];
   				$total_array['sum_netto'] += $params_in['gross_value'];
   			}
   		}
   		return array('total' => $total_array, 'obj_array' => $obj_array);
   	} else {
   		return array('total' => $total_array, 'obj_array' => array());
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
       
   static function check_rights( $params, $action, $show_info = true) {
   	$P = Person::g_global();
   	$Rights = Rights::g_global();
   
   	list($res, $res_debug) = $Rights->basket_favorite_rights($params, $action, $show_info);
   
   	if( !$res && $show_info ) {
   		Info::g('add', Lang::_('You dont have rights for (' . $action . ') favorite basket: ' . (int)$this->id_shopping_basket_favorite));
   	}
   
   	$this->res_debug .= $res_debug;
   
   	return $res;
   }
   
}

?>