<pre>
<?
// turn off the WSDL cache
ini_set('soap.wsdl_cache_enabled', '0');

$param_array = array(
	'cache_wsdl'     => WSDL_CACHE_NONE,
	'features'     => SOAP_SINGLE_ELEMENT_ARRAYS,
	'login' => 'ala',
	'password' => 'ola',
	'trace' => true
);

	// $wsu = 'http://schemas.xmlsoap.org/ws/2002/07/utility';
    // $usernameToken = array('login' => 'ala', 'password' => 'ola');
    // $soapHeaders[] = new SoapHeader($wsu, 'UsernameToken', $usernameToken);

$client = new SoapClient('http://127.0.0.1/MS_test/soap/scramble2-1.wsdl', $param_array);
//$client->__setSoapHeaders( $soapHeaders );

$origtext = 'mississippi';

print("The original text : $origtext\n");
$scramble = $client->getRot13( $origtext);

print("The scrambled text : $scramble\n");

$mirror = $client->getMirror($scramble);
print("The scrambled mirrored text : $mirror\n");

$mirror = $client->getMirror($origtext);
print("The mirrored text : $mirror\n");

$resA = $client->getArray($origtext);
print("The mirrored text :" . print_r($resA, true). '<br>');

//var_dump($resA);

$resB = $client->getArray2($origtext);
print("The mirrored text :" . print_r($resB, true). '<br>');

echo '<pre>';
echo "__getLastRequest\n" . print_r($client->__getLastRequest() , true) . "\n\n";
echo "__getLastRequestHeaders\n" . print_r($client->__getLastRequestHeaders() , true) . "\n\n";
echo "__getLastResponse\n" . print_r($client->__getLastResponse() , true) . "\n\n";
echo "__getLastResponseHeaders \n" . print_r($client->__getLastResponseHeaders () , true) . "\n\n";
?>