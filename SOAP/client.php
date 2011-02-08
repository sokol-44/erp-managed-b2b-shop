<?
// turn off the WSDL cache
ini_set('soap.wsdl_cache_enabled', '0');

$param_array = array(
	'cache_wsdl'     => WSDL_CACHE_NONE,
	'features '     => SOAP_SINGLE_ELEMENT_ARRAYS
);

//$client = new SoapClient('http://127.0.0.1/MS_test/soap/scramble.wsdl', 'features' => SOAP_SINGLE_ELEMENT_ARRAYS);
$client = new SoapClient('http://127.0.0.1/MS_test/soap/scramble.wsdl', $param_array);

$origtext = 'mississippi';

print("The original text : $origtext" . '<br>');
$scramble = $client->getRot13($origtext);

print("The scrambled text : $scramble" . '<br>');

$mirror = $client->getMirror($scramble);
print("The mirrored text : $mirror" . '<br>');


$res = $client->getArrayMirror($scramble);
print("The mirrored text :" . print_r($res, true). '<br>');



$res = $client->getArrayMirror($scramble);
?>