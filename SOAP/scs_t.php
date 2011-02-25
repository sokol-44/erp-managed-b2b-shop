<html>
<head>
<title>Test - B2B Sklep</title>
</head>
<body>
Tests <?php  echo date('r'); ?><br>
<pre>
<?
include "scs_t_function.php";
include "scs_t_class.php";
include "scs_t_test.php";

$client = new SoapClient($soap_address, $soap_param_array);
//$client->__setSoapHeaders( $soapHeaders );


// Test Client/User
// test_doClientAdd( $client );
// test_doClientChange( $client );
// test_doClientUserAdd( $client );
// test_doClientUserChange( $client );
// test_doClientUserSetPassword( $client );
// test_doClientUserDelete( $client );
 test_getClientList( $client );
// test_getClientUserList( $client );


// test_getProductList( $client );



?>
</pre>
</body>
</html>