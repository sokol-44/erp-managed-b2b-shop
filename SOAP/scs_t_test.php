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

function test_doClientAdd( $client ) {
  
}

function test_doClientChange( $client ) {
   echo "test_doClientChange\n";
}

function test_doClientUserAdd( $client ) {
   echo "test_doClientUserAdd\n";
}

function test_doClientUserChange( $client ) {
   echo "test_doClientUserChange\n";
}

function test_doClientUserDelete( $client ) {
   echo "test_doClientUserDelete\n";
}

// test_getClientList( $client );
// test_getClientUserList( $client );

function test_getClientList( $client ) { //OK
   $in_oo = new ParamStartLength(array('id_start' => '1', 'length' => '5'));
   test_helper_single_in($in_oo, 'getClientList', $client );
}

function test_getClientUserList( $client ) {
   $in_oo = new ParamDoubleStartLength(array('id_start_one' => '1', 'id_start_two' => '1', 'length' => '5'));
   test_helper_single_in($in_oo, 'getClientUserList', $client );
}

function test_doClientUserSetPassword( $client ) {
   echo "test_doClientUserSetPassword\n";
}

//Produkty / Kategorie


function test_doProductAdd( $client ) {
   echo "test_doProductAdd\n";
}

function test_doProductChange( $client ) {
   echo "test_doProductChange\n";
   $in_oo = new ParamStartLength(array('id_start' => '1', 'length' => '5'));
   test_helper_single_in($in_oo, 'getProductList', $client );
}


function test_setProductClientPrice( $client ) {
   echo "test_setProductClientPrice\n";
   $in_o1 = new ProductClientPriceData(array('id_product' => '1', 'id_client' => '1', 'price' => '12.12', 'vat' => '23'));
   $in_o2 = new ProductClientPriceData(array('id_product' => '1', 'id_client' => '2', 'price' => '2.12',  'vat' => '22'));
   $in_o3 = new ProductClientPriceData(array('id_product' => '45', 'id_client' => '1', 'price' => '12.12', 'vat' => '23'));
   $in_o = array( 'value_1' => $in_o1->return_array(), 'value_2' => $in_o2->return_array(), 'value_3' => $in_o3->return_array() );
   //var_dump($in_o);
   $in_o_x = ArrayToXML::toXml($in_o, 'DocumentElement');
   print("IN:" . xml_b( $in_o_x ). '<br>');
   try { $resC = $client->setProductClientPrice($in_o_x); }
   catch (Exception $e) { /*var_dump($e);*/ print_lr( $client ); }
   print("RES:" . xml_b( $resC ). '<br>');
}


function test_doCategoryAdd( $client ) {
   echo "test_doCategoryAdd\n";

}

function test_doCategoryEdit( $client ) {
   echo "test_doCategoryEdit\n";

}

function test_doCategoryDelete( $client ) {
   echo "test_doCategoryDelete\n";

}

function test_setProduct2Category( $client ) {
   echo "test_setProduct2Category\n";

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
   echo "test_setOrderHiddenStatus\n";
}




?>