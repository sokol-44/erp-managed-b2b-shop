<?php
/**
 * Order.php
 * Copyright MichaÅ‚ SokoÅ‚owski 2013
 *
 * @author Micha³ Soko³owski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/**
 * Data class goint to provide data and operation on them
 * Application will get and save any data thru it
 */

class Order_Chain {
   private $id_client = false;
   public $order_list = array();
   public $total = array('product_total' => 0, 'product_types' => 0,
   		'sum_gross' => 0, 'sum_gross_split' => array(), 'sum_netto' => 0);

   function __construct( $id_client = false, $param = array()) {
      $P = Person::g_global();

      if( !$id_client && $P->logged_in )  {
     		$this->id_client = $P->data['id_client'];
      } else {
      	$this->id_client = (int)$id_client;
      }
      $this->load_param($param);
      
      $this->order_list = $this->load_data();
   }

   function load_param( $param = array() ) {
   	  /*
   	   * List:
   	   * date_create, date_modified, id_order_status
   	   * 
   	   * obiekt ze statusami ?
   	   */
   	  
   }
   	
   function check_rights( $id_client = 0 ) {

   }
    
   function calculate_total() {
      $this->total = array('product_total' => 0, 'product_types' => 0,
      'sum_gross' => 0, 'sum_gross_split' => array(), 'sum_netto' => 0);

      if ( Framework::not_null($this->order_list) && $this->total['product_total'] == 0 ) {
         foreach($this->order_list as $id_order => $order ) {
         	$ord_total = $order->calculate_total();
            $this->total['product_total'] += $ord_total['product_total'];
            $this->total['product_types'] += $ord_total['product_types'];
            //$this->total['sum_gross_split'] += $ord_total['sum_gross_split'];
            $this->total['sum_netto']  += $ord_total['sum_netto'];
            $this->total['sum_gross']  += $ord_total['sum_gross'];
         }
      }
      return $this->total;
   }

   function load_data( ) {
      $F = Framework::g_global();
      
      $order_list = Data::get_order_list((int)$this->id_client, $param);

      if( $F->not_null($order_list) ) {
      	foreach($order_list as $id_order => $order ) {
      		$this->order_list[$id_order] = new Order($id_order, 'SIMPLE', $order);
      	}
      	return $this->order_list;
      } else {
         return false;
      }
   }

}

?>