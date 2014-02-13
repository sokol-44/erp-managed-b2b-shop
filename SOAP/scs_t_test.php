<?php

function test_helper_single_in($data_in, $method, $client ) {
   echo "test_$method REAL\n";
   $in_o = array( 'value_1' => $data_in->return_array() );
   $in_o_x = ArrayToXML::toXml($in_o, 'DocumentElement');
   print("IN:" . xml_b( $in_o_x ). '<br>');
   try { $resC = $client->$method($in_o_x); }
   catch (Exception $e) { /*var_dump($e);*/ print_lr( $client ); }
   print("RES:" . xml_b( $resC ). '<br>');
}

function test_helper_multiple_in($data_in, $method, $client ) {
   echo "test_$method REAL\n";
   $in_o = array();
   foreach($data_in as $key => $val) {
      $in_o['value_' . $key] =  $val->return_array();
   }
   print("IN:" . print_r($in_o, true) . '<br>');
   $in_o_x = ArrayToXML::toXml($in_o, 'DocumentElement');
   print("IN:" . xml_b( $in_o_x ). '<br>');
   
   try { $resC = $client->$method($in_o_x); }
   catch (Exception $e) { /*var_dump($e);*/ print_lr( $client ); }
   print("RES:" . xml_b( $resC ). '<br>');
}

function test_doClientAdd( $client ) {
   $in_oo = array();
   $in_oo[] = new ClientData(array('id_client' => '3', 'name' => 'name'.microtime(true), 'description' => 'desc',
          'email' => 'email', 'phone' => '555333444', 'state' => 'ACTIVE'));
   test_helper_multiple_in($in_oo, 'doClientAdd', $client);
}

function test_doClientChange( $client ) {
   $in_oo = array();
   $in_oo[] = new ClientData(array('id_client' => '3', 'name' => 'name'.microtime(true), 'description' => 'desc',
          'email' => 'email', 'phone' => '555333444', 'state' => 'ACTIVE'));
   $in_oo[] = new ClientData(array('id_client' => '5', 'name' => 'name'.microtime(true), 'description' => 'desc',
          'email' => 'email', 'phone' => '555333444', 'state' => 'ACTIVE'));
   test_helper_multiple_in($in_oo, 'doClientChange', $client);
}

function test_doClientUserAdd( $client ) {
   $in_oo = array();
   $in_oo[] = new ClientUserData(array('id_client_user' => '1', 'id_client' => '3', 'login' => '123xx', 'password' => 'xyz', 'password_salt' => '',
         'description' => 'desc', 'name' => 'name'.microtime(true), 'email' => 'email', 'state' => 'ACTIVE'));
   test_helper_multiple_in($in_oo, 'doClientUserAdd', $client);
}

function test_doClientUserChange( $client ) {
   $in_oo = array();
   $in_oo[] = new ClientUserData(array('id_client_user' => '11', 'id_client' => '3', 'login' => '123xx', 'password' => 'xyz', 'password_salt' => '',
         'description' => 'desc', 'name' => 'name'.microtime(true), 'email' => 'email', 'state' => 'ACTIVE'));
   test_helper_multiple_in($in_oo, 'doClientUserChange', $client);
}

function test_doClientUserSetPassword( $client ) {
   $in_oo = array();
   $in_oo[] = new ClientUserData(array('id_client_user' => '11', 'id_client' => '3', 'password' => 'xyz', 'password_salt' => ''), false);
   $in_oo[] = new ClientUserData(array('id_client_user' => '111', 'id_client' => '13', 'password' => 'xyz', 'password_salt' => ''), false);
   test_helper_multiple_in($in_oo, 'doClientUserSetPassword', $client);
}

function test_doClientUserDelete( $client ) {
   $in_oo = array();
   $in_oo[] = new ClientUserData(array('id_client_user' => '11', 'id_client' => '3'), false);
   $in_oo[] = new ClientUserData(array('id_client_user' => '111', 'id_client' => '13', 'password' => 'xyz', 'password_salt' => ''), false);
   test_helper_multiple_in($in_oo, 'doClientUserDelete', $client);
}


function test_getClientList( $client ) { //OK
   $in_oo = new ParamStartLength(array('id_start' => '1', 'length' => '5'));
   test_helper_single_in($in_oo, 'getClientList', $client );
}

function test_getClientUserList( $client ) {
   $in_oo = new ParamDoubleStartLength(array('id_start_one' => '1', 'id_start_two' => '1', 'length' => '5'));
   test_helper_single_in($in_oo, 'getClientUserList', $client );
}


//Produkty / Kategorie

function test_doProductAdd( $client ) {
   $in_oo = array();
   $in_oo[] = new ProductData(array('id_product' => 1, 'name' => 'jeden', 'description' => 'desc 1', 'picture_small_url' => '', 'picture_big_url' => '',
         'picture_id' => '', 'price' => '11.11', 'vat' => '11', 'quantity' => '11', 'status' => 'ACTIVE'));
   $in_oo[] = new ProductData(array('id_product' => 12, 'name' => 'jeden', 'description' => 'desc 1', 'picture_small_url' => '', 'picture_big_url' => '',
         'picture_id' => '', 'price' => '11.11', 'vat' => '11', 'quantity' => '11', 'status' => 'ACTIVE'));
   test_helper_multiple_in($in_oo, 'doProductAdd', $client);
}

function test_doProductChange( $client ) {
   $in_oo = array();
   $in_oo[] = new ProductData(array('id_product' => 1, 'name' => 'jeden', 'description' => 'desc 1', 'picture_small_url' => '', 'picture_big_url' => '',
         'picture_id' => '', 'price' => '11.11', 'vat' => '11', 'quantity' => '11', 'status' => 'ACTIVE'));
   $in_oo[] = new ProductData(array('id_product' => 212, 'name' => 'jeden', 'description' => 'desc 1', 'picture_small_url' => '', 'picture_big_url' => '',
         'picture_id' => '', 'price' => '11.11', 'vat' => '11', 'quantity' => '11', 'status' => 'ACTIVE'));
   test_helper_multiple_in($in_oo, 'doProductChange', $client);
}


function test_setProductClientPrice( $client ) {
   $in_oo = array();
   $in_oo[] = new ProductClientPriceData(array('id_product' => '1', 'id_client' => '1', 'price' => '12.12', 'vat' => '23'));
   $in_oo[] = new ProductClientPriceData(array('id_product' => '1', 'id_client' => '2', 'price' => '2.12',  'vat' => '22'));
   $in_oo[] = new ProductClientPriceData(array('id_product' => '45', 'id_client' => '1', 'price' => '12.12', 'vat' => '23'));
   test_helper_multiple_in($in_oo, 'setProductClientPrice', $client);
}

function test_doCategoryAdd( $client ) {
   $in_oo = array();
   $in_oo[] = new CategoryData(array('id_category' => '22', 'id_category_parent' => '1' , 'sort_order' => '2',  'name' => 'a22'  , 'description' => 'desc'.microtime(true)));
   $in_oo[] = new CategoryData(array('id_category' => time(), 'id_category_parent' => '22', 'sort_order' => '2',  'name' => '22a34', 'description' => 'desc'.microtime(true)));
   test_helper_multiple_in($in_oo, 'doCategoryAdd', $client);
}

function test_doCategoryEdit( $client ) {
   $in_oo = array();
   $in_oo[] = new CategoryData(array('id_category' => '22', 'id_category_parent' => '1' , 'sort_order' => '2',  'name' => 'a22'  , 'description' => 'desc'.microtime(true)));
   $in_oo[] = new CategoryData(array('id_category' => time(), 'id_category_parent' => '22', 'sort_order' => '2',  'name' => '22a34', 'description' => 'desc'.microtime(true)));
   test_helper_multiple_in($in_oo, 'doCategoryEdit', $client);

}

function test_doCategoryDelete( $client ) {
   $in_oo = array();
   $in_oo[] = new CategoryData(array('id_category' => '22'), false);
   $in_oo[] = new CategoryData(array('id_category' => '99'), false);
   $in_oo[] = new CategoryData(array('id_category' => time()), false);
   test_helper_multiple_in($in_oo, 'doCategoryDelete', $client);

}

function test_setProduct2Category( $client ) {
   $in_oo = array();
   echo "test_setProduct2Category\n";
   $in_oo[] = new Product2Category(array('id_product' => '1', 'id_category' => '98'));
   $in_oo[] = new Product2Category(array('id_product' => '1', 'id_category' => '2'));
   $in_oo[] = new Product2Category(array('id_product' => '45', 'id_category' => '1'));
   test_helper_multiple_in($in_oo, 'setProduct2Category', $client);
}


function test_getProductList( $client ) { //OK\
   $in_oo = new ParamStartLength(array('id_start' => '1', 'length' => '5'));
   test_helper_single_in($in_oo, 'getProductList', $client );
}

function test_getProductListFromCategory( $client ) { //OK
   $in_oo = new ParamDoubleStartLength(array('id_start_one' => '98', 'id_start_two' => '1', 'length' => '5'));
   test_helper_single_in($in_oo, 'getProductListFromCategory', $client );
}

function test_getCategoryList( $client ) { //OK
   $in_oo = new ParamStartLength(array('id_start' => '1', 'length' => '15'));
   test_helper_single_in($in_oo, 'getCategoryList', $client );
}

function test_getProductClientPriceList( $client ) {
   echo "test_getProductClientPriceList\n";
}

function test_getClientPriceList( $client ) {
   echo "test_getClientPriceList\n";

}

function test_getClientPriceProductList( $client ) {
   $in_oo = new ParamDoubleStartLength(array('id_start_one' => '1', 'id_start_two' => '1', 'length' => '5'));
   test_helper_single_in($in_oo, 'getClientPriceProductList', $client );
}

function test_getOrderList( $client ) {
   $in_oo = new ParamStartLength(array('id_start' => '1', 'length' => '15'));
   test_helper_single_in($in_oo, 'getOrderList', $client );
}

function test_getOrderListNew( $client ) {
   $in_oo = new ParamStartLength(array('id_start' => '1', 'length' => '15'));
   test_helper_single_in($in_oo, 'getOrderListNew', $client );
}


function test_setPicture( $client ) {
   echo "test_setPicture REAL\n";
   $in_o_x = file_get_contents('a.xml');
   try { $resC = $client->setPicture($in_o_x); }
   catch (Exception $e) { /*var_dump($e);*/ echo $e->faultstring; print_lr( $client ); }
   print("RES:" . xml_b( $resC ). '<br>');
}

function test_setOrderStatus( $client ) {
   echo "test_setOrderStatus\n";
}

function test_setOrderHiddenStatus( $client ) {
   $in_oo = array();
   $in_oo[] = new OrderData(array('id_order' => '1', 'id_client' => '1', 'hidden_status' => time()));
   $in_oo[] = new OrderData(array('id_order' => '1', 'id_client' => '3', 'hidden_status' => time()));
   $in_oo[] = new OrderData(array('id_order' => '13', 'id_client' => '1'));
   test_helper_multiple_in($in_oo, 'setOrderHiddenStatus', $client);
}


function test_getAddressList( $client ) {
	$in_oo = new ParamStartLength(array('id_start' => '1', 'length' => '15'));
	test_helper_single_in($in_oo, 'getAddressList', $client );
}

function test_getClientAddressList( $client ) {
   $in_oo = new ParamDoubleStartLength(array('id_start_one' => '3', 'id_start_two' => '1', 'length' => '5'));
   test_helper_single_in($in_oo, 'getClientAddressList', $client );
}

function test_getClientUserAddressList( $client ) {
   $in_oo = new ParamDoubleStartLength(array('id_start_one' => '1', 'id_start_two' => '1', 'length' => '5'));
   test_helper_single_in($in_oo, 'getClientUserAddressList', $client );
}

function test_doClientUserAddressAddOrUpdate( $client ) {
	$list = array('id_address' => 1, 'id_client_user' => 1, 'id_client' => 3, 
			'description' => 'description'.time(), 'name' => 'name'.time(), 'street' => 'street'.time(),
			 'city' => 'city'.time(), 'zip_code' => 'zip_code'.time(), 'country' => 'country'.time());
	$in_oo[] = new ClientUserAddressData($list);
	$list = array('id_address' => time(), 'id_client_user' => 1, 'id_client' => 3, 
			'description' => 'description'.time(), 'name' => 'name'.time(), 'street' => 'street'.time(),
			 'city' => 'city'.time(), 'zip_code' => 'zip_code'.time(), 'country' => 'country'.time());
	$in_oo[] = new ClientUserAddressData($list);
	test_helper_multiple_in($in_oo, 'doClientUserAddressAddOrUpdate', $client);
}


function test_doClientUserAddressDelete( $client ) {
	$nt = time();
	$list = array('id_address' => $nt, 'id_client_user' => 1, 'id_client' => 3,
			'description' => 'description'.$nt, 'name' => 'name'.$nt, 'street' => 'street'.$nt,
			'city' => 'city'.$nt, 'zip_code' => 'zip_code'.$nt, 'country' => 'country'.$nt);
	
	$in_oo[] = new ClientUserAddressData($list);
	test_helper_multiple_in($in_oo, 'doClientUserAddressAddOrUpdate', $client);
	
	$in_oo2 = new ParamDoubleStartLength(array('id_start_one' => '1', 'id_start_two' => $nt, 'length' => '5'));
	test_helper_single_in($in_oo2, 'getClientUserAddressList', $client );
	
	$in_oo3[] = new ClientUserAddressData($list);
	test_helper_multiple_in($in_oo3, 'doClientUserAddressDelete', $client);
}

function test_getClientAttributeList( $client ) {
	$nt = time();

	$in_oo = new ParamStartLength(array('id_start' => '2', 'length' => '15'));
	test_helper_single_in($in_oo, 'getClientAttributeList', $client );
}

function test_doClientAttributeAddOrUpdate( $client ) {
	$nt = time();
	$list = array('id_client' => 2, 'type' => 'name'.$nt, 'val' => 'val'.$nt);
	$in_oo[] = new ClientAttributeData($list);

	$list = array('id_client' => 2, 'type' => 'BALANCE_FREE_CREDIT', 'val' => $nt);
	$in_oo[] = new ClientAttributeData($list);
	
	test_helper_multiple_in($in_oo, 'doClientAttributeAddOrUpdate', $client);
}

function test_getOrderAttributeList( $client ) {
	$nt = time();

	$in_oo = new ParamStartLength(array('id_start' => '17', 'length' => '15'));
	test_helper_single_in($in_oo, 'getOrderAttributeList', $client );
}

function test_doOrderAttributeAddOrUpdate( $client ) {
	$nt = time();
	$list = array('id_order' => 17, 'type' => 'name'.$nt, 'val' => 'val'.$nt);
	$in_oo[] = new OrderAttributeData($list);

	$list = array('id_order' => 17, 'type' => 'BALANCE_FREE_CREDIT', 'val' => $nt);
	$in_oo[] = new OrderAttributeData($list);

	test_helper_multiple_in($in_oo, 'doOrderAttributeAddOrUpdate', $client);
}


function test_getOrderInvoiceList( $client ) {
	$nt = time();

	$in_oo = new ParamDoubleStartLength(array('id_start_one' => '17', 'id_start_two' => '1', 'length' => '5'));
	test_helper_single_in($in_oo, 'getOrderInvoiceList', $client );
}

function test_getClientInvoiceList( $client ) {
	$nt = time();

	$in_oo = new ParamDoubleStartLength(array('id_start_one' => '2', 'id_start_two' => '1', 'length' => '5'));
	test_helper_single_in($in_oo, 'getClientInvoiceList', $client );
}

function test_getInvoiceList( $client ) {
	$nt = time();

	$in_oo = new ParamStartLength(array('id_start' => '1', 'length' => '5'));
	test_helper_single_in($in_oo, 'getInvoiceList', $client );
}

function test_doInvoiceAddOrUpdate( $client ) {
	$nt = time();
	
	$list = array('id_invoice' => '3', 'id_order' => '18', 'id_client' => '2',
			 'invoice_number' => 'QQ', 'state' => 'ISSUE', 'net_value' => '11.22', 
			 'gross_value' => '15.45', 'date_issue' => '2013-11-01', 'date_pay' => '2013-11-08',
			 'description' => 'sdadasdasdas');
	$in_oo[] = new InvoiceData($list);

	$list = array('id_invoice' => $nt, 'id_order' => '18', 'id_client' => '2',
			'invoice_number' => 'QQ'. $nt, 'state' => 'ISSUE', 'net_value' => '11.22',
			'gross_value' => '15.45', 'date_issue' => '2013-11-01', 'date_pay' => '2013-11-08',
			'invoice_image' => str_repeat('w', (int)rand(10,100)), 'description' => 'sdadasdasdas'.$nt);
	$in_oo[] = new InvoiceData($list);
	
	test_helper_multiple_in($in_oo, 'doInvoiceAddOrUpdate', $client );
}

function test_setInvoiceStatus( $client ) {
	$nt = time();

	$list = array('id_invoice' => '3', 'id_order' => 18, 'state' => 'ISSUE');
	$in_oo[] = new OrderInvoiceData($list, 'UPDATE');

	$list = array('id_invoice' => $nt, 'id_order' => 18, 'state' => 'ISSUE');
	$in_oo[] = new OrderInvoiceData($list, 'UPDATE');

	test_helper_multiple_in($in_oo, 'setInvoiceStatus', $client );
}

function test_getClientAccountManagerList( $client ) {
	$nt = time();

	$in_oo = new ParamStartLength(array('id_start' => '1', 'length' => '5'));
	test_helper_single_in($in_oo, 'getClientAccountManagerList', $client );
}

function test_doClientAccountManagerAddOrUpdate( $client ) {
	$nt = time();

// 	$list = array('id_account_manager' => '1', 'id_client' => '2', 'id_client_user' => '4',
//          'account_manager_name' => 'MSO', 'fullname' => 'Michał Sokołowski', 'phone1' => '225552233',
// 			'phone2' => $nt, 'email' => 'michal.sokolowski@2m.net.pl');
// 	$in_oo[] = new AccountManagerData($list);

// 	$list = array('id_account_manager' => $nt, 'id_client' => '2', 'id_client_user' => '4',
// 			'account_manager_name' => $nt, 'fullname' => 'Michał Sokołowski', 'phone1' => '225552233',
// 			'phone2' => $nt, 'email' => 'michal.sokolowski@2m.net.pl');
// 	$in_oo[] = new AccountManagerData($list);

	$list = array('id_account_manager' => 0, 'id_client' => '2', 'id_client_user' => '4',
			'account_manager_name' => strrev($nt), 'fullname' => 'Michał Sokołowski', 'phone1' => '225552233',
			'phone2' => $nt, 'email' => 'michal.sokolowski@2m.net.pl');
	
	$in_oo[] = new AccountManagerData($list);
	
	test_helper_multiple_in($in_oo, 'doClientAccountManagerAddOrUpdate', $client );
}

function test_doClientUserAttributeAddOrUpdate( $client ) {
	$nt = time();
	
	$list = array('id_client' => 2, 'id_client_user' => 4, 'type' => 'name'.$nt, 'val' => 'val'.$nt);
	$in_oo[] = new ClientUserAttributeData($list);

	$list = array('id_client' => 2, 'id_client_user' => 4, 'type' => 'BALANCE_FREE_CREDIT', 'val' => $nt);
	$in_oo[] = new ClientUserAttributeData($list);

	test_helper_multiple_in($in_oo, 'doClientUserAttributeAddOrUpdate', $client);
}


function test_getClientUserAttributeList( $client ) {
	$nt = time();

	$in_oo = new ParamStartLength(array('id_start' => '4', 'length' => '15'));
	test_helper_single_in($in_oo, 'getClientUserAttributeList', $client );
}



function test_getShopAttributeList( $client ) {
	$nt = time();

	$in_oo = new ParamStartLength(array('id_start' => '4', 'length' => '15'));
	test_helper_single_in($in_oo, 'getShopAttributeList', $client );
}


function test_doShopAttributeAddOrUpdate( $client ) {
	$nt = time();

	$list = array('type' => 'name'.$nt, 'val' => 'val'.$nt);
	$in_oo[] = new ShopAttributeData($list);
	
	$list = array('type' => 'BALANCE_FREE_CREDIT', 'val' => $nt);
	$in_oo[] = new ShopAttributeData($list);
	
	test_helper_multiple_in($in_oo, 'doShopAttributeAddOrUpdate', $client);
}


function test_getClientNewList( $client ) {
	$nt = time();

	$in_oo = new ParamStartLength(array('id_start' => '1', 'length' => '15'));
	test_helper_single_in($in_oo, 'getClientNewList', $client );
}


function test_doClientNewIdUpdateList( $client ) {
	$nt = time();

	$in_oo[] = new ClientData(array('id_client' => '7396', 'description' => 'new_id:111117396'), 'UPDATE');
	$in_oo[] = new ClientData(array('id_client' => '111117396', 'description' => 'new_id:7396'), 'UPDATE');
	test_helper_multiple_in($in_oo, 'doClientNewIdUpdateList', $client );
}

function test_doClientUserNewIdUpdateList( $client ) {
	$nt = time();

// 	$in_oo[] = new ClientUserData(array('id_client' => '7396', 'id_client_user' => '7396', 
// 			'description' => 'new_id_client_user:111117396:new_id_client:111117396'), 'UPDATE');
	$in_oo[] = new ClientUserData(array('id_client' => '1943', 'id_client_user' => '20', 
			'description' => 'new_id_client_user:1002'), 'UPDATE');
	test_helper_multiple_in($in_oo, 'doClientUserNewIdUpdateList', $client );
}

// test_doShopProductAttributeAddOrUpdate( $client );
// test_getShopProductAttributeList( $client );



function test_getShopProductAttributeList( $client ) {
	$nt = time();

	$in_oo = new ParamStartLength(array('id_start' => '12599', 'length' => '0'));
	test_helper_single_in($in_oo, 'getShopProductAttributeList', $client );
}


function test_doShopProductAttributeAddOrUpdate( $client ) {
	$nt = time();

	$list = array('id_product' => 12599, 'type' => 'name'.$nt, 'val' => 'val'.$nt);
	$in_oo[] = new ShopProductAttributeData($list);

	$list = array('id_product' => 12599, 'type' => 'BALANCE_FREE_CREDIT', 'val' => 'val'.$nt);
	$in_oo[] = new ShopProductAttributeData($list);
	
	$list = array('id_product' => $nt, 'type' => 'BALANCE_FREE_CREDIT', 'val' => 'val'.$nt);
	$in_oo[] = new ShopProductAttributeData($list);

	test_helper_multiple_in($in_oo, 'doShopProductAttributeAddOrUpdate', $client);
}
?>