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




?>