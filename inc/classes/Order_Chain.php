<?php
/**
 * Order_Chain.php
 * Copyright Michał Sokołowski 2013
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Order_Chain
 *
 * Data class going to provide data and operations on them.
 * Application will get and save any data through it.
 *
 * @designPattern Entity List
 *
 * @todo Rename class to PascalCase (e.g., OrderChain) to follow PSR-1/PSR-12 standards.
 * @todo Add type hinting and visibility modifiers to all properties and methods.
 */
class Order_Chain {
   /**
    * @var int|bool The client ID, or false if not set.
    */
   private $id_client = false;

   /**
    * @var array<int, Order> List of Order objects.
    */
   public $order_list = array();

   /**
    * @var int Total number of orders.
    */
   public $order_count = 0;

   /**
    * @var array{product_total: int, product_types: int, sum_gross: float|int, sum_gross_split: array, sum_netto: float|int} Summary of order totals.
    */
   public $total = array('product_total' => 0, 'product_types' => 0,
           'sum_gross' => 0, 'sum_gross_split' => array(), 'sum_netto' => 0);

   /**
    * Order_Chain constructor.
    *
    * Initializes the order chain for a specific client and loads their orders.
    *
    * @param int|bool $id_client Optional client ID. If false, attempts to load from the logged-in user session. Defaults to false.
    * @param array $param Optional parameters for loading data. Defaults to array().
    * @return void
    *
    * @todo Use dependency injection for Person and Framework classes instead of global/static access.
    * @todo Add strict type declarations for parameters.
    */
   function __construct( $id_client = false, $param = array()) {
      $P = Person::g_global();

      if( !$id_client && $P->logged_in )  {
             $this->id_client = $P->data['id_client'];
      } else {
          $this->id_client = (int)$id_client;
      }
      $this->load_param($param);

      $this->order_count = $this->load_data();
   }

   /**
    * Loads and processes configuration parameters.
    *
    * @param array $param Configuration parameters. Defaults to array().
    * @return void
    *
    * @todo Implement parameter processing logic or remove if unused.
    * @todo Add type hinting for the array parameter.
    */
   function load_param( $param = array() ) {
         /*
          * List:
          * date_create, date_modified, id_order_status
          * object with statuses?
          */

   }

   /**
    * Checks if the current user has rights to access the client's orders.
    *
    * @param int $id_client Client ID to check rights against. Defaults to 0.
    * @return void
    *
    * @todo Implement authorization logic and return a boolean value.
    * @todo Add type hinting for parameter and return type.
    */
   function check_rights( $id_client = 0 ) {

   }

   /**
    * Calculates the total values for all orders in the chain.
    *
    * @return array{product_total: int, product_types: int, sum_gross: float|int, sum_gross_split: array, sum_netto: float|int} Calculated totals.
    *
    * @todo Use dependency injection for Framework class.
    * @todo Add return type declaration.
    */
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

   /**
    * Loads order data from the database/data source.
    *
    * @return int Number of loaded orders.
    *
    * @todo Fix undefined variable $param (it is used but not defined in this scope or stored as a property).
    * @todo Use dependency injection for Framework and Data classes.
    * @todo Add return type declaration.
    */
   function load_data( ) {
      $F = Framework::g_global();

      $order_list = Data::get_order_list((int)$this->id_client, $param);

      if( $F->not_null($order_list) ) {
          foreach($order_list as $id_order => $order ) {
              $this->order_list[$id_order] = new Order($id_order, 'SIMPLE', $order);
          }
          return sizeof($this->order_list);
      } else {
         return 0;
      }
   }

}
