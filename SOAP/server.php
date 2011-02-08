<?
$fp = fopen('server-'.time().'.log', 'a+');
// fwrite($fp, "_GET\n" . print_r($_GET, true));
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

function getArrayMirror($pInput){
$mirror = explode('', $pInput);

return($mirror);
}

// turn off the wsdl cache
ini_set('soap.wsdl_cache_enabled', '0');

$server = new SoapServer('scramble.wsdl');

$server->addFunction('getRot13');
$server->addFunction('getMirror');
$server->addFunction('getArrayMirror');

$server->handle();


?>