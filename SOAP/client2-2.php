<pre>
<?
// turn off the WSDL cache
ini_set('soap.wsdl_cache_enabled', '0');

$client = new SoapClient('http://127.0.0.1/MS_test/soap/scramble2-2.wsdl');

$origtext = 'mississippi';

print("The original text : $origtext\n");
$scramble = $client->getRot13( $origtext);

print("The scrambled text : $scramble\n");

?>