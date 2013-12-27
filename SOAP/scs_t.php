<html>
<head>

  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Test - B2B Sklep</title>
</head>
<body>
Tests <?php  echo date('r'); ?><br>
<pre>
<?
include "scs_t_function.php";
include "scs_t_test.php";
include "../inc/classes/ArrayToXML.php";
include "../inc/classes/Soap_Server_class.php";

$fp = false;
$fp_xml = false;

// turn off the wsdl cache
ini_set('soap.wsdl_cache_enabled', '0');
// var_dump($soap_param_array);
// var_dump($soap_address);
$client = new SoapClient($soap_address, $soap_param_array);
//$client->__setSoapHeaders( $soapHeaders );


// Test Client/User
//actualy do
// test_getProductList( $client );
// test_getCategoryList( $client );
// test_getProductListFromCategory( $client );
// test_getClientList( $client );
// test_getClientUserList( $client );
// test_getClientPriceProductList( $client );
// test_getOrderList( $client );
// test_getOrderListNew( $client );
// test_setProductClientPrice( $client );
// test_doClientAdd( $client );
// test_doClientChange( $client );
// test_doClientUserAdd( $client );
// test_doClientUserChange( $client );
// test_doClientUserSetPassword( $client );
// test_doClientUserDelete( $client );
// test_doProductAdd( $client );
// test_doProductChange( $client );
// test_doCategoryAdd( $client );
// test_doCategoryEdit( $client );
// test_doCategoryDelete( $client );
// test_setProduct2Category( $client );
// test_setPicture( $client );

// test_getAddressList( $client );
// test_getClientAddressList( $client );
// test_getClientUserAddressList( $client );
// test_doClientUserAddressAddOrUpdate( $client );
// test_doClientUserAddressDelete( $client );

// test_doClientAttributeAddOrUpdate( $client );
// test_getClientAttributeList( $client );
// test_doOrderAttributeAddOrUpdate( $client );
// test_getOrderAttributeList( $client );

// test_getOrderInvoiceList( $client );
// test_getClientInvoiceList( $client );
// test_getInvoiceList( $client );

// test_doInvoiceAddOrUpdate( $client );
// test_setInvoiceStatus( $client );

// test_doClientAccountManagerAddOrUpdate( $client );
// test_getClientAccountManagerList( $client );
// test_doClientUserAttributeAddOrUpdate( $client );
// test_getClientUserAttributeList( $client );

// test_doShopAttributeAddOrUpdate( $client );
// test_getShopAttributeList( $client );

// test_getClientNewList( $client );
// test_doClientNewIdUpdateList( $client );

//PLACEHOLDERS
// test_setOrderStatus( $client );
// test_setOrderHiddenStatus( $client );

// test_getClientUserList( $client );
test_doClientUserNewIdUpdateList( $client );
?>
</pre>
</body>
</html>