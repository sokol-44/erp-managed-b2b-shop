<?php
/**
 * Shopping_Basket_Chain.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/**
 * @author ms
 *
 */
class Shopping_Basket_Chain {
   static $class = false;
   public $contents = array();
   public $id_nr_shopping_basket = 0;
   public $params = array(
      	'id_client' => 0, 'description' => 0, 'using_id_client_user' => 0,
      	'using_id_client_user' => 0, 'using_session_id' => 0, 'using_date' => 0
      );
   //   static $GET_raw = '', $GET_array = array();

   function __construct($id_nr_shopping_basket = 0, $id_client = 0) {
      $this->reset();
      self::$class = $this;
      $this->id_nr_shopping_basket = (int)$id_nr_shopping_basket;
      $this->params['id_client'] = (int)$id_client;
   }

   static function g_global() {
      if(self::$class == false) {
         self::$class = new Shopping_Basket();
      }
      return self::$class;
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