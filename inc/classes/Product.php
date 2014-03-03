<?php
/**
 * Product.php Helper file for products
 * Copyright Michał Sokołowski 2013
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();


class Product {
   static $class = array();
   public $id_product = 0;
   public $id_product_subtype = array();
   public $info = array();
   
   public function __construct( $product_key = 0) {
		if( (int)$product_key > 0 && ( strpos($product_key, '_') || ctype_digit($product_key) ) )  {
      	
   		$res = Data::get_product_params_from_key($product_key);
   		$this->id_product = (int)$res['id_product'];
      	self::$class[$this->id_product] = $this;
   		$this->fill_product();
   	}
   	return $this;
   }
   
   private function fill_product() {
   	$this->info = Data::get_product_info( (int)$this->id_product );
   	print_debug($this->info);
   }
     
   static function get_product_info( $product_key = 0) {
		$Product = Product::get_product( $product_key );
		return $Product->info;
   }
     
   static function get_product( $product_key = 0) {
		if( (int)$product_key > 0 && ( strpos($product_key, '_') || ctype_digit($product_key) ) )  {
   		$res = Data::get_product_params_from_key($product_key);
			if( isset(self::$class[$res['id_product']]) ) {
				return self::$class[$res['id_product']];
			} else {
				return new Product($res['id_product']);
			}
				
		} else {
   		return new Product();
   	}
   }

   public function __wakeup() {
   }
   
   public function __destruct() {
   }
   
   static function get_search_product_list($filters) {
      
   }
   

}
?>