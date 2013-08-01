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

class Order_History {
	static $class;
   static public $order_history_list = array();
   
   function __construct() {
   	self::$class = $this;
   	
   	$this->load_data();
   	
   	return self::$class;
   }
   
   static function g_global() {
   	if(self::$class == false) {
   		self::$class = new Person;
   	}
   	return self::$class;
   }
    
   function __wakeup() {
   	self::$class = $this;
   }
   
   static function text2id($text) {
		self::load_data();
		
		return array_search($text, self::$order_history_list);
   }
   
   static function id2text($id) {
		self::load_data();
		
		if( isset(self::$order_history_list[$id]) ) return self::$order_history_list[$id];
		return false;
   }

   static function load_data( ) {
      $F = Framework::g_global();
      
      if( Framework::is_null(self::order_history_list) ) {
      	self::$order_history_list = Data::get_order_history_list();
      }
   }

}

?>