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
   	//print_debug($this->info);
   }
     
   static function get_product_info( $product_key = 0) {
		$Product = Product::get_product( $product_key );
		return $Product->info;
   }

   static function get_product_image_type( $product_key = 0) {
   	$Product = Product::get_product( $product_key );
   	return Data::get_product_image_type( $Product->info );
   }

   static function get_attribute_display( $product_key = 0) {
   	$Product = Product::get_product( $product_key );
   	$ha = Data::$Data_Products_hidden_attributes;
   	$attribute_list = $Product->info['attribute'];
   	$attribute_list_tmp = $Product->info['attribute'];
   	foreach($attribute_list_tmp as $type => $value) {
   		foreach ($ha as $hidden_attr) {
				if ( strpos($value, $hidden_attr) == 0 && strpos($value, $hidden_attr) !== FALSE ) {
					unset($attribute_list[$type]);
				}
			}
   	}
   	return $attribute_group;
   }

   static function get_attribute_group_display( $product_key = 0) {
   	$Product = Product::get_product( $product_key );
   	$ha = Data::$Data_Products_hidden_attributes;
   	$attribute_group = $Product->info['attribute_group'];
   	$attribute_group_tmp = $Product->info['attribute_group'];
   	foreach($attribute_group_tmp as $id_group => $group) {
			foreach($group['attribute'] as $id_attribute => $attribute) {
				foreach ($ha as $hidden_attr) {
					if ( strpos($attribute['attribute_name'], $hidden_attr) == 0 &&
   					 	strpos($attribute['attribute_name'], $hidden_attr) !== FALSE ) {
						unset($attribute_group[$id_group]['attribute'][$id_attribute]);
					}
				}
				
			}
   	}
   	return $attribute_group;
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