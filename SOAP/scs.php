<?
include "init.php";

include "scs_t_function.php";
include "scs_t_class.php";

$fp = fopen('C:\wamp\www\MS_test\soap\log\server2-'.time().'.log', 'a+');

$hdr = file_get_contents("php://input");

//fwrite($fp, "input \n" . $hdr . "\n\n");
//fwrite($fp, "GLOBALS\n" . print_r($GLOBALS  , true));
//fwrite($fp, "_SERVER\n" . print_r($_SERVER, true));

// fwrite($fp, "_REQUEST \n" . print_r($_REQUEST, true));
// fwrite($fp, "_ENV  \n" . print_r($_ENV , true));
// fwrite($fp, "_POST\n" . print_r($_POST, true));
// fwrite($fp, "HTTP_RAW_POST_DATA\n" . print_r($HTTP_RAW_POST_DATA, true));
//fwrite($fp, "_SERVER[HTTP_SOAPACTION]\n" . $_SERVER['HTTP_SOAPACTION'] . "\n\n");

// turn off the wsdl cache
ini_set('soap.wsdl_cache_enabled', '0');

//auth
//http://www.php.net/manual/en/soapserver.soapserver.php#78872

$param_array = array(
	'cache_wsdl'     => WSDL_CACHE_NONE,
	'features'     => SOAP_SINGLE_ELEMENT_ARRAYS,
	'login' => 'ala',
	'password' => 'ola'
);


$server = new SoapServer('shop_control.wsdl', $param_array);

$server->setClass('Soap_Server');
$server->setPersistence(SOAP_PERSISTENCE_SESSION);

$server->handle();

fclose($fp);
?>