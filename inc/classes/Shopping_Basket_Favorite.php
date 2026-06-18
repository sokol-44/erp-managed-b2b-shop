<?php
/**
 * Shopping_Basket.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Micha Sokoowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Shopping_Basket_Favorite
 *
 * Represents a favorite shopping basket for a client, allowing users to save,
 * retrieve, and manage predefined sets of products.
 *
 * @designPattern Singleton
 *
 * @todo Add strict type declarations (declare(strict_types=1)).
 * @todo Use dependency injection instead of static global accessors (e.g., Framework::g_global()).
 * @todo Define explicit property types and visibility modifiers (PHP 7.4+ / 8.0+).
 * @todo Modernize array syntax to short array bracket notation `[]`.
 * @todo Rework to extend Shopping_Basket.
 */
class Shopping_Basket_Favorite {
   /**
    * @var Shopping_Basket_Favorite|false Static reference to the last instantiated class instance.
    * @todo Transition away from singleton/global state tracking pattern to proper dependency management.
    */
   static $class = false;

   /**
    * @var int The unique identifier of the favorite shopping basket.
    */
   public $id_shopping_basket_favorite = 0 ;

   /**
    * @var array List of products in the basket.
    */
   public $product_list = array();

   /**
    * @var string Debugging information.
    */
   public $res_debug = '';

   /**
    * @var array Calculated totals including product count, types, gross sum, and net sum.
    */
   public $total = array('product_total' => 0, 'product_types' => 0,
           'sum_gross' => 0, 'sum_gross_split' => array(), 'sum_netto' => 0);

   /**
    * @var array Parameters associated with the favorite basket.
    */
   public $params = array(
         'id_client' => 0, 'id_client_user' => 0, 'id_shopping_basket_favorite' => 0,
         'date_created' => '', 'date_modified' => '', 'ts_created' => 0, 'ts_modified' => 0,
         'rights_edit' => '', 'rights_use' => '');

   /**
    * Shopping_Basket_Favorite constructor.
    *
    * Initializes the favorite basket based on the provided parameters.
    *
    * @param mixed $params Optional initialization parameters (int ID, Shopping_Basket object, Order object, or array). Defaults to false.
    * @return void
    * @throws \Exception If unexpected data structures are passed or external dependencies fail.
    *
    * @todo Remove the redundant 'return false;' statement from the constructor.
    * @todo Replace class name string checks with 'instanceof' operator.
    * @todo Use strict type hinting/union types for the $params parameter (e.g., int|object|array|bool).
    * @todo Fix logical bugs (e.g., commented assignments and undefined variable $basket_favorite_description).
    */
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

   /**
    * Checks if the provided parameters array contains all required keys matching internal schema templates.
    *
    * @param array $params The parameters array to validate.
    * @return bool True if all required keys exist, false otherwise.
    *
    * @todo Add type hinting for the array parameter and explicit boolean return type hint.
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
    * Reconstructs the basket product list from database parameters.
    *
    * Extracts serialized items and updates pricing/VAT data from an external tracking source.
    *
    * @param array $params Database parameters containing a serialized product list.
    * @return array The reconstructed product list with updated prices and VAT.
    *
    * @todo Add type hinting for array parameters and array return type.
    * @todo Avoid direct calls to static Data class; inject a data repository dependency instead.
    * @todo Replace native `unserialize()` with safe options or JSON formats to prevent PHP Object Injection vulnerabilities.
    */
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

   /**
    * Retrieves the list of favorite baskets for the currently logged-in user.
    *
    * @return Shopping_Basket_Favorite[] Array of favorite basket objects indexed by their IDs.
    *
    * @todo Implement dependency injection for Framework and Person classes instead of static tracking.
    * @todo Add return type hint (`array`).
    */
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

   /**
    * Calculates the total quantities, net sum, and gross sum split by VAT.
    *
    * @return array The calculated totals array context.
    *
    * @todo Add return type hint (`array`).
    * @todo Fix typo in Price::rount_tax (should probably be round_tax).
    * @todo Use dependency injection for Framework and Price classes.
    * @todo Resolve undefined index notices (e.g., initializing `$this->total['sum_gross_split'][$product['vat']]` before compound assignment).
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
    * Creates a new favorite basket from an existing shopping basket.
    *
    * @param Shopping_Basket $Shopping_Basket The shopping basket instance.
    * @param string $basket_description Optional description for the basket. Defaults to ''.
    * @return int|false The ID of the created favorite basket, or false on failure.
    *
    * @todo Type-hint the $Shopping_Basket parameter explicitly.
    * @todo Fix undefined variables $order_description and $id_order which seem to be bugs in the original code.
    * @todo Use dependency injection instead of static global accessors.
    */
   static function make_new_basket_basket($Shopping_Basket, $basket_description = '') {
       $F = Framework::g_global();
       $P = Person::g_global();

       if( $Shopping_Basket->params['id_client'] != $P->data['id_client'] &&
       !Shopping_Basket::_check_valid_basket($Shopping_Basket) )  {
           return false;
       }

       $product_list = $Shopping_Basket->get_all_product();

       if( !$F->not_null($basket_description) ) $order_description = 'NULL';

       $id_shopping_basket_favorite = Data::make_new_basket_version_data($Shopping_Basket->params, $product_list, $basket_description);
       if( $Shopping_Basket->id_shopping_basket > 0 ) {
           return $id_shopping_basket_favorite;
       } else {
           return false;
       }
   }

   /**
    * Checks if the current user has rights to perform a specific action on the basket.
    *
    * @param array $params The basket parameters data.
    * @param string $action The action to check (e.g., 'USE', 'EDIT').
    * @param bool $show_info Whether to display an info message on failure. Defaults to true.
    * @return bool True if the user has rights, false otherwise.
    *
    * @todo Add scalar type hints for parameters and explicit boolean return type hint.
    * @todo Replace static global accessors with modern dependency injection.
    */
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

   /**
    * Calculates and returns the total values of the basket.
    *
    * @return array The total tracking data metrics array.
    *
    * @todo Add return type hint (`array`).
    * @todo Check if $this->calculate() is defined or if it should call $this->calculate_total() instead.
    */
   function get_total() {
      $this->calculate();

      return $this->total;
   }

   /**
    * Magic method called during serialization via serialize().
    *
    * Unsets temporary properties and returns the list of properties to serialize.
    *
    * @return array List of property names to serialize.
    * @todo Add return type hint (`array`).
    * @todo Declare $product_array property explicitly if it is intended to be used within the class scope.
    */
   function __sleep() {
      unset($this->product_array);
      return( array_keys( get_object_vars( $this ) ) );
   }

   /**
    * Magic method called during unserialization via unserialize().
    * Restores the static class execution state environment references.
    *
    * @return void
    *
    * @todo Add return type hint (`void`).
    */
   function __wakeup() {
      self::$class = $this;
   }

}
