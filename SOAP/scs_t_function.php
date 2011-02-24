<?php


$soap_param_array = array(
	'cache_wsdl'     => WSDL_CACHE_NONE,
	'features'     => SOAP_SINGLE_ELEMENT_ARRAYS,
	'authentication' => SOAP_AUTHENTICATION_SIMPLE,
	'login' => 'ala',
	'password' => 'ola',
	'trace' => true
);

$soap_address = 'http://b2b_sklep.localhost/SOAP/shop_control.wsdl';

function add_to_fp($str) {
   global $fp;
   if( isset($fp) && is_resource($fp) ) {
      //fwrite($fp, print_r(debug_backtrace(), true));
      fwrite($fp, ' ------------- add_to_fd');
      fwrite($fp, $str);
   } else {
      
      die();
   }
   
}

function print_lr( $obj ) {

   if( is_object($obj) ) {
      echo '<pre>';
      echo "__getLastRequest\n" . xml_b($obj->__getLastRequest()) . "\n\n";
      echo "__getLastRequestHeaders\n" . xml_b($obj->__getLastRequestHeaders()) . "\n\n";
      echo "__getLastResponse\n" . xml_b($obj->__getLastResponse()) . "\n\n";
      echo "__getLastResponseHeaders \n" . xml_b($obj->__getLastResponseHeaders ()) . "\n\n";
      echo '</pre>';
   }
}

function xml_b($str) {
   return
   htmlspecialchars(
      str_replace("\n</", "</",
         str_replace('<', "\n<", $str)
      )
   );
}


function data_int($in) { return 10000 + strlen($in); }

function data_data($time = '') {
   if( $time == '' ) $time = time();
   return date('Y-m-d H:i:s', $time);
}

?>