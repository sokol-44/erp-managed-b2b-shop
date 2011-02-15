<?
$fp = fopen('C:\wamp\www\MS_test\soap\log\server2-'.time().'.log', 'a+');

$hdr = file_get_contents("php://input");

fwrite($fp, "input \n" . $hdr . "\n\n");
// fwrite($fp, "_REQUEST \n" . print_r($_REQUEST, true));
// fwrite($fp, "_ENV  \n" . print_r($_ENV , true));
fwrite($fp, "GLOBALS\n" . print_r($GLOBALS  , true));
// fwrite($fp, "_POST\n" . print_r($_POST, true));
// fwrite($fp, "HTTP_RAW_POST_DATA\n" . print_r($HTTP_RAW_POST_DATA, true));
fwrite($fp, "_SERVER\n" . print_r($_SERVER, true));
//fwrite($fp, "_SERVER[HTTP_SOAPACTION]\n" . $_SERVER['HTTP_SOAPACTION'] . "\n\n");


class Soap_Server {

   function Authenticate($login) {

      // Authenticate the user
      //if ($login->username === "a.single.sign.on.user@your.organization.org" && $login->password === "the-password-of-that-user") {
      //return array('Authenticated'=>true);
      // } else {
      return array('Authenticated'=>false);
      // }
   }
   
   function getProductList() {
      $res_obj[0]['id_product'] = 1;
      $res_obj[0]['name'] = '0 name';
      $res_obj[0]['description'] = '0 description';
      $res_obj[0]['picture_small_url'] = '0 picture_small_url';
      $res_obj[0]['picture_big_url'] = ' 0 picture_big_url';
      $res_obj[0]['picture_id'] = 13;
      $res_obj[0]['price'] = 13;
      $res_obj[0]['vat'] = 12;
      $res_obj[0]['quantity'] = 11;
      $res_obj[0]['status'] = '0 status';

      return($res_obj);
   }
   
   function doClientAdd ($in) {
      return serialize($in);
      
//		`id_client_in` INT,
//		`name_in` TINYTEXT,
//		`description_in` TEXT,
//		`email_in` TINYTEXT,
//		`phone_in` TINYTEXT,
//		`state_in` TINYTEXT
   }
   
   function doClientUserAdd () {
      
//      `id_client_user_in` INT,
//      `id_client_in` INT,
//      `login_in` TINYTEXT,
//      `password_in` TINYTEXT,
//      `password_salt_in` BLOB,
//      `name_in` TINYTEXT,
//      `description_in` TEXT,
//      `email_in` TINYTEXT,
//      `state_in` TINYTEXT
   }
   
   
   //Client_Add, client_change, client_user_add, client_user_change, client_user_delete, client_user_set_password
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


$server = new SoapServer('shop_control.wsdl', $param_array);

$server->setClass('Soap_Server');
$server->setPersistence(SOAP_PERSISTENCE_SESSION);

$server->handle();

fclose($fp);

?>