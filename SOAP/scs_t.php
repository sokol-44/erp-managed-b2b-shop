<html>
<body>
<pre>
<?
// turn off the WSDL cache
ini_set('soap.wsdl_cache_enabled', '0');

$param_array = array(
	'cache_wsdl'     => WSDL_CACHE_NONE,
	'features'     => SOAP_SINGLE_ELEMENT_ARRAYS,
	'authentication' => SOAP_AUTHENTICATION_SIMPLE,
	'login' => 'ala',
	'password' => 'ola',
	'trace' => true
);

$client = new SoapClient('http://b2b_sklep.localhost/SOAP/shop_control.wsdl', $param_array);
//$client->__setSoapHeaders( $soapHeaders );

$resC = $client->getProductList();
//print("The mirrored text :" . print_r($resC, true). '<br>');
//print_lr( $client );

$in = array( 0 =>
   array(
      'id_client_in' => 12,
      'name_in' => 'name_in',
      'description_in' => 'description_in',
      'email_in' => 'email_in',
      'phone_in' => 'phone_in',
      'state_in' => 'state_in'
   )
);

$resC = $client->doClientAdd($in);
print("" . print_r( unserialize($resC), true). '<br>');
print_lr( $client );

?>
</pre>
</body>
</html>
<?php

function print_lr( $obj ) {

   if( is_object($obj) ) {
      echo '<pre>';
      echo "__getLastRequest\n" . htmlspecialchars($obj->__getLastRequest()) . "\n\n";
      echo "__getLastRequestHeaders\n" . htmlspecialchars($obj->__getLastRequestHeaders()) . "\n\n";
      echo "__getLastResponse\n" . htmlspecialchars($obj->__getLastResponse()) . "\n\n";
      echo "__getLastResponseHeaders \n" . htmlspecialchars($obj->__getLastResponseHeaders ()) . "\n\n";
      echo '</pre>';
   }


}



?>