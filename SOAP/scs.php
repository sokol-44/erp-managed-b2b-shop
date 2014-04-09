<?
ob_start();
include "init.php";

include "scs_t_function.php";

$utime_array = explode(' ', microtime());

$ts=date('Ymd_Hi_s_').sprintf('%06x',(int)(($utime_array[0]*0xffffff)&0xffffff));

if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN')
	$find_del = exec('/usr/bin/find /home/company/ftp/b2b-sklep/ups_seller2/SOAP/log/ -type f -name "server2*" -cmin +2880 -delete -exec /bin/echo {} \; | wc -l', $output, $ret_find);

$hdr = file_get_contents('php://input');
$mthd_exp  = '/\:body><([a-z0-9\:]+)[\ >]/i';
$slt_exp = '/[^a-z0-9\-]/i';

$out = array();
preg_match($mthd_exp, substr($hdr, 0, 512), $out);

if( !empty($out) ) {
	$mthd_str = preg_replace($mthd_exp, '', $out[1]);
	if( substr_count($mthd_str, ':') == 1 ) list($tmp,$mthd_str) = explode(':', $mthd_str);
	$ts = $ts . '--' . preg_replace($slt_exp, '', $mthd_str);
}

if( !empty($_GET['s']) ) {
	$ts = $ts . '--' . preg_replace($slt_exp, '', $_GET['s']);
}


$fp = fopen('log/server2-'.$ts.'.log', 'a+');
$fp_xml = fopen('log/server2-'.$ts.'.xml', 'a+');

add_to_fp('ret_find ' . (($ret_find>0)?'ERROR':'OK') . ' cnt:' . trim($find_del) );

// add_to_fp("out \n" . print_r($out,true));
// add_to_fp("input \n" . substr($hdr, 0, 512) . "\n\n");
add_to_fp("URL: " . $_SERVER['REQUEST_URI'] . "\n");
add_to_fp("GET \n" . print_r($_GET,true));

if( $hdr=='' ) {
	add_to_fp("EMPTY input:\n" . print_r($_SERVER, true));
	//if( isset($_GET['wsdl']) ) {
	readfile('shop_control.wsdl');
	//} else {
	//}
	die();
}

// $fp_all = fopen('log/server2-'.date('Ymd_Hi_s_').$utime_array[0].'.txt', 'a+');
// fwrite($fp_all, $hdr);
file_put_contents('log/server2-'.$ts.'.txt', $hdr);

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

$server = new SoapServer('shop_control.wsdl', $soap_param_array);
//$s = new Soap_Server();
$server->setClass('Soap_Server');
$server->setPersistence(SOAP_PERSISTENCE_SESSION);

add_to_fp("server in\n" . print_r($server , true) . "\n\n");

use_soap_error_handler(true);
register_shutdown_function( "check_for_fatal" );

try {
	if( function_exists('xdebug_disable') ) xdebug_disable();
	ob_clean();
	$server->handle();
	ob_end_flush();
} catch (SoapFault $exc) {
	add_to_fp("SoapFault in\n" . print_r($exc , true) . "\n\n");
}

add_to_fp("server out\n" . print_r($server , true) . "\n\n");

fclose($fp);
fclose($fp_xml);
//fclose($fp_all);
?>