<?
$fp = fopen('server2-'.time().'.log', 'a+');
// fwrite($fp, "_REQUEST \n" . print_r($_REQUEST, true));
// fwrite($fp, "_ENV  \n" . print_r($_ENV , true));
// fwrite($fp, "GLOBALS\n" . print_r($GLOBALS  , true));
// fwrite($fp, "_FILES\n" . print_r($_FILES, true));
// fwrite($fp, "_POST\n" . print_r($_POST, true));
fwrite($fp, "HTTP_RAW_POST_DATA\n" . print_r($HTTP_RAW_POST_DATA, true));
// fwrite($fp, "_SERVER\n" . print_r($_SERVER, true));
fwrite($fp, "_SERVER[HTTP_SOAPACTION]\n" . $_SERVER['HTTP_SOAPACTION'] . "\n\n");

function getRot13($pInput){
$rot = str_rot13($pInput);return($rot);
}

function getMirror($pInput){
$mirror = strrev($pInput);

return($mirror);
}

function getArray($pInput){
$mirror = str_split( $pInput, ceil(strlen($pInput)/2));

//$res_obj = 
$res_obj['a'] = $mirror['0'];
$res_obj['b'] = $mirror['1'];

return($res_obj);
}

function getArray2($pInput){
$mirror = str_split( $pInput, ceil(strlen($pInput)/2));

$res_obj[0]['id'] = '0'. $mirror['0'];
$res_obj[0]['name'] = '0'. $mirror['1'];
$res_obj[0]['data'] = '0'. $mirror['1'];
$res_obj[1]['id'] = '1'. $mirror['0'];
$res_obj[1]['name'] = '1'. $mirror['1'];
$res_obj[1]['data'] = '1'. $mirror['1'];

return($res_obj);
}

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

function UsernameToken($Input){
	return serialize($Input) . 'a';
}

$server = new SoapServer('scramble2-1.wsdl', $param_array);

$server->addFunction('getRot13');
$server->addFunction('getMirror');
$server->addFunction('getArray');
$server->addFunction('getArray2');
$res = $server->addFunction('UsernameToken');

$server->handle();

//fwrite($fp, "UsernameToken\n" . $res . "\n\n");
fclose($fp);

class SubObject {
    public $value = '';
	public function __construct($txt = false) {
		if( $txt ) $value = $txt;
	}
}
?>