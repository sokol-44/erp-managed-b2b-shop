<html>
<body>
T
<pre>
<?
include "scs_t_function.php";
include "scs_t_class.php";
include "scs_t_test.php";

$client = new SoapClient($soap_address, $soap_param_array);
//$client->__setSoapHeaders( $soapHeaders );



// test_doClientAdd( $client );
// test_getProductList( $client );
$in_oo = new ParamStartLength('dupa');
//$in_oo->start = 1;
//$in_oo->length = 2;
//$in_oo->options = 'sasa';

print_r($in_oo);

$resC = $client->getProductList( $in_oo );
//print_lr( $client );
print_r($resC);


//$resC = $client->getParamStartLength( 'aa' );
//print_r($resC);

?>
</pre>
</body>
</html>