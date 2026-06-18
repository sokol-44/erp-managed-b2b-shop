<?php
/**
 * Order.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Order
 * Data class going to provide data and operations on them.
 * Application will get and save any data through it.
 *
 * @designPattern Entity
 *
 * @todo Declare standard type definitions for class properties (e.g., public array $data = []).
 * @todo Shift database layer interactions out of the `Data` static god-class into a dedicated Order Repository.
 */
class Order {
   /**
    * @var array Array containing core order information.
    */
   public $data = array();

   /**
    * @var array List of products assigned to the order.
    */
   public $product_list = array();

   /**
    * @var array Address data for the order shipment/billing.
    */
   public $address = array();

   /**
    * @var array History transitions of order states.
    */
   public $status_history = array();

   /**
    * @var array Raw origin shopping basket transactional data.
    */
   public $source_basket = array();

   /**
    * @var string|bool Operational depth strategy configuration flag ('FULL', 'SIMPLE', or false).
    */
   public $mode = false;

   /**
    * @var array Metadata attribute sets attached to the order.
    */
   public $attributes = array();

   /**
    * @var int The unique primary key identifier for the order entity.
    */
   public $id_order = 0;

   /**
    * @var array Calculated total aggregates cache.
    */
   public $total;

   /**
    * Order constructor.
    *
    * @param int $id_order Primary identification key. Defaults to 0.
    * @param string $mode Extraction profile depth mode ('FULL' or 'SIMPLE'). Defaults to 'FULL'.
    * @param array|bool $data Pre-loaded raw data array if already retrieved, otherwise false.
    * @return int|bool Returns size of product list or status state indicators.
    *
    * @todo Constructors should not return values; refactor data hydration logic outside initialization routines.
    */
   function __construct( $id_order = 0, $mode = 'FULL',  $data = false) {
      $this->data = array();
      $this->product_list = array();
      $this->status_history = array();
      $this->set_mode( $mode );

      if( $id_order > 0 ) {
         return $this->load_data( (int)$id_order, $data );
      }

   }

   /**
    * Configures internal operational loading depths strategy workflows.v
    *
    * @param string $mode_in Desired loading structure configuration ('FULL' or 'SIMPLE').
    * @return void
    *
    * @todo Fix logical contradiction bug in conditional statement: `if ($mode_in == 'FULL' && $mode_in == 'SIMPLE')` can never resolve to true.
    */
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

   /**
    * Confirms if a specified client has access ownership rights over the record.
    * @param int $id_client Optional custom client identifier. Defaults to 0 (evaluates active global user context).
    * @return bool True if ownership constraints pass, false otherwise.
    *
    * @todo Remove commented line anomalies (`//echo ... die();`) to enforce clean production-ready coding practices.
    */
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

   /**
    * Aggregates and calculates pricing totals across item entries.
    *
    * @return array Matrix breakdown structure storing aggregated price points.
    *
    * @todo Fix array warning bug: initialize `$this->total['sum_gross_split'][$product['vat']]` before executing `+=` arithmetic modifications.
    * @todo Spelling correction suggestion: change static method call `Price::rount_tax()` to `Price::round_tax()`.
    */
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

   /**
    * Retrieves meta attribute array lists filtered by type parameters.
    *
    * @param string|bool $type Unique target attribute identifier string key, or false to grab all. Defaults to false.
    * @return array|bool Formatted records matching filtering attributes or boolean tracking updates.
    */
   function get_attributes($type = false) {
       if( Framework::is_null($this->attributes) ) $this->attributes = Data::get_order_attribute($this->id_order);

         if( $type ) {
             foreach( $this->attributes as $attribute ) {
                 if( $attribute['type'] == $type ) return $attribute;
             }
             return false;
         } else {
             return $this->attributes;
         }
   }

   /**
    * Retrieves key-value configurations extracted across object attributes lists.
    *
    * @param string|bool $type Unique target identification string key, or false for map results. Defaults to false.
    * @return mixed Extracted attribute value mapping metrics or false flag defaults.
    */
   function get_attribute_val($type = false) {
       if( Framework::is_null($this->attributes) ) $this->attributes = Data::get_order_attribute($this->id_order);

       if( $type ) {
           foreach( $this->attributes as $attribute ) {
               if( $attribute['type'] == $type ) return $attribute['val'];
           }
           return false;
       } else {
           $res_array = array();
           foreach( $this->attributes as $attribute ) {
               $res_array[$attribute['type']] = $attribute['val'];
           }
           return $res_array;
       }
   }

   /**
    * Retrieves address information related to this order instance.
    *
    * @return array Dataset properties array representing localized address points.
    */
   function get_address() {
       if( Framework::is_null($this->address) ) $this->address = Data::get_address( (int)$this->data['id_address'] );
       return $this->address;
   }

   /**
    * Extracts raw relational reference indices associated with addresses.
    *
    * @return int Address database identification key context.
    */
   function get_address_id() {
       return (int)$this->data['id_address'];
   }

   /**
    * Hydrates state parameters by pulling context blocks out from database indexes.
    *
    * @param int $id_order Target entity identifier parameter index.
    * @param array|bool $data Pre-loaded raw info structure datasets when available. Defaults to false.
    * @return int|bool Integer size representing array totals or false tracking errors.
    */
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

   /**
    * Executes auxiliary depth queries fetching history metrics and tracking variables.
    *
    * @return void
    */
   function load_data_full() {
        $this->status_history = Data::get_order_status_history_list( (int)$this->id_order );
        $this->source_basket = Shopping_Basket::get_order_data( (int)$this->data['id_shopping_basket'] );
   }

   /**
    * Generates, verifies, converts, and persist-pipes basket items over into order contexts.
    *
    * @param object $Shopping_Basket Instance of Shopping_Basket holding targeted selection states.
    * @param array $param_in External variable configurations mapped down across persistence steps.
    * @return array|bool Array pairing identifier index with processing element totals, false if checks fail.
    *
    * @todo Change the param doc string definition `object $Shopping_Basket` to a precise type hint definition like `Shopping_Basket $Shopping_Basket`.
    * @todo Eliminate dead code blocks like `$attributes = array();` that immediately overwrite state values downstream without utilization.
    */
   static function make_new_order($Shopping_Basket, $param_in) {
      $F = Framework::g_global();
      $P = Person::g_global();

      if( $Shopping_Basket->params['id_client'] != $P->data['id_client'] || !$P->check_roles('LEVEL_99') ||
      !Shopping_Basket::_check_valid_basket($Shopping_Basket) )  {
         return false;
      }


      $product_list = $Shopping_Basket->get_all_product();

      $id_order = Data::put_order_data($P->data['id_client'], $Shopping_Basket->params, $param_in);
      if( $id_order > 0 ) {
          $attributes = array();

         $Shopping_Basket->state_archive_order();
         $count_product   = Data::put_order_product_list($id_order, $product_list);
         $count_product_2 = Data::change_product_quantity_list($product_list);
         $id_soh = (int)Order_History::text2id('OSH_N');
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
