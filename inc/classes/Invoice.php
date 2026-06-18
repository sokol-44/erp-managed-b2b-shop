<?php
/**
 * Invoice.php
 * Copyright Michał Sokołowski 2013
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Invoice
 *
 * Data class going to provide data and operations on them.
 * Application will get and save any data through it.
 *
 * @designPattern Entity
 *
 * @todo Implement strict typing (declare(strict_types=1)).
 * @todo Add visibility modifiers to all properties and methods.
 * @todo Use dependency injection instead of static global accessors (e.g., Rights::g_global()).
 * @todo Define missing properties like $product_list and $total to avoid dynamic property creation deprecation in PHP 8.2+.
 * @todo Refactor static method check_rights() which incorrectly uses $this.
 */
class Invoice {
   /**
    * @var bool Temporary class flag or identifier.
    */
   static $class = false;

   /**
    * @var string Debugging information resource.
    */
   public $res_debug = '';

   /**
    * @var array Default parameters for the invoice.
    */
   public $params = array(
         'id_client' => 0,  'id_order' => 0, 'id_invoice' => 0, 'state' => '',
           'net_value' => 0, 'gross_value' => '', 'invoice_number' => '', 'invoice_image' => '',
         'date_issue' => '', 'date_pay' => '', 'ts_issue' => 0, 'ts_pay' => 0);

   /**
    * Invoice constructor.
    *
    * Initializes the invoice object. If an integer is passed, it attempts to load
    * the invoice data from the database. If an array is passed, it validates and assigns it.
    *
    * @param int|array|bool $params Optional initialization parameters (ID or data array). Defaults to false.
    * @return $this
    *
    * @todo Add type hinting for the constructor parameter.
    * @todo Remove explicit return $this from constructor as it is not standard PHP practice.
    */
   function __construct( $params = false) {

      if( is_numeric( $params ) && (int)$params>0 ) {
          $Rights = Rights::g_global();
          $this->params = Data::get_invoice_data( (int)$params );
      } elseif( is_array($params) && $this->check_valid($params) ) {
          $this->params = $params;
      }
      return $this;
   }

   /**
    * Validates if the provided parameters array contains all required keys.
    *
    * @param array $params The parameters array to validate.
    * @return bool True if all required keys exist, false otherwise.
    *
    * @todo Add array type hint to the parameter and bool return type hint.
    */
   function check_valid( $params ) {
       foreach( $this->params as $key => $val) {
           if( !array_key_exists($key, $params) ) {
               return false;
           }
       }
       return true;
   }

   /**
    * Retrieves a list of invoices for a specific client.
    *
    * Checks user rights and returns an array containing total statistics and the list of Invoice objects.
    *
    * @param int $id_client The client ID. Defaults to 0.
    * @return array An array containing 'total' summary and 'obj_array' of Invoice objects.
    *
    * @todo Add type hinting for parameters and return types.
    * @todo Fix undefined variable $show_info passed to invoice_rights().
    * @todo Refactor global state dependencies (Person, Framework, Rights, Data).
    */
   static function get_invoice_client_list($id_client = 0) {
       $P = Person::g_global();
       $F = Framework::g_global();

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
           $res = Data::get_invoice_client_list($params);
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

   /**
    * Calculates the total gross and net values of the invoice products.
    *
    * @return array The calculated totals array.
    *
    * @todo Add return type hint.
    * @todo Declare $product_list and $total properties on the class to avoid dynamic property deprecation.
    * @todo Fix typo in Price::rount_tax (should probably be round_tax).
    */
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

   /**
    * Checks user rights for a specific action.
    *
    * @param array $params Action parameters.
    * @param string $action The action to check.
    * @param bool $show_info Whether to show info messages on failure. Defaults to true.
    * @return bool True if the user has rights, false otherwise.
    *
    * @todo Critical: Fix the use of $this inside a static method ($this->id_shopping_basket_favorite and $this->res_debug).
    * @todo Add type hinting for parameters and return types.
    */
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
