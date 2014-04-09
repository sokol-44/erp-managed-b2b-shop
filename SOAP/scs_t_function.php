<?php


$soap_param_array = array(
	'cache_wsdl'     => WSDL_CACHE_NONE,
	'features'     => SOAP_SINGLE_ELEMENT_ARRAYS,
	'authentication' => SOAP_AUTHENTICATION_SIMPLE,
	'login' => 'ala',
	'password' => 'ola',
	'compression' => true,
	'exceptions' => true,
	'trace' => true,
	'location' => ''
);

$soap_address = 'http://b2b_sklep.localhost/SOAP/shop_control.wsdl';
//$soap_address = 'http://sklep-b2b.pl/SOAP/shop_control.wsdl';
//$soap_address = 'http://ups_seller.sklep-b2b.pl/SOAP/shop_control.wsdl';

$soap_location = str_replace('shop_control.wsdl', 'scs.php', $soap_address);

function add_to_fp($str) {
   global $fp;

   if( isset($fp) && is_resource($fp) ) {
      //fwrite($fp, print_r(debug_backtrace(), true));
      fwrite($fp, "\n--> " . microtime(true) . "\n");
      fwrite($fp, trim($str));
      fwrite($fp, "\n<--\n");
   } else {

   }
   
}

function calculate_hash() {
	$sec = 'dfsfi8CTRJHnoOI243NvirtdsHMIU216asiudnfpou';
	$salt = round(microtime(true),2);
	$hf = 'md5';
	$u = 1;
	$hash = hash($hf, $sec . $salt);
	$ret = '?u='.urlencode($u).'&h='.urlencode($hash).'&s='.urlencode($salt).'&hf='.urlencode($hf);
	return $ret;
	//return compact('salt', 'hash', 'u');
}

function check_for_fatal() {
	$error = error_get_last();
	if ( $error["type"] == E_ERROR ) {
		add_to_fp('check_for_fatal nr: E_ERROR str '.$error["message"].', fl '.$error["file"].', ln '.$error["line"]);
		ob_clean();
		echo '<?xml version="1.0" encoding="UTF-8"?><SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"><SOAP-ENV:Body><SOAP-ENV:Fault>'.
				'<faultcode>SOAP-ENV:Server</faultcode>'.
				'<faultstring>'.
				$error["message"].', line '.$error["line"].' in '.pathinfo($error["file"], PATHINFO_BASENAME).
				'</faultstring></SOAP-ENV:Fault></SOAP-ENV:Body></SOAP-ENV:Envelope>';
		echo ob_end_flush();
	}
}


function print_lr( $obj ) {

   if( is_object($obj) ) {
      echo '<pre>';
      echo "__getLastRequest\n" . xml_b($obj->__getLastRequest()) . "\n\n";
      echo "__getLastRequestHeaders\n" . xml_b($obj->__getLastRequestHeaders()) . "\n\n";
      $last_resp = $obj->__getLastResponse();
      if( strpos($last_resp, 'xdebug-error') > 0 )
      	echo '</pre><div style="border: 2px dashed blue">'.$last_resp.'</div><pre>';
      else 
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