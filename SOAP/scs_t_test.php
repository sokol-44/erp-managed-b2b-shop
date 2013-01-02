<?php

function test_doClientAdd( $client ) {
   echo "test_doClientAdd\n";
   //$in_o = array( 0 => new ClientData('dupa'), new ClientData('dupaq'));
   //$in_o = new stdClass();
   //$in_o = array( 'value_1' => new ClientData('dupa') ,  'value_2' => new ClientData('foobar_dupa'));
   $c1 = new ClientData('dupa');
   $c2 = new ClientData('foobar_dupa');
   $in_o = array( 'value_1' => $c1->return_array(),  'value_2' => $c2->return_array());
   //$in_o->value = new ClientData('dupa');
   print_r($in_o);
   $in_o_x = ArrayToXML::toXml($in_o, 'DocumentElement');
   //echo $in_o_x;
   //print("" . xml_b( $in_o_x ). '<br>');
   try {
	$resC = $client->doClientAdd($in_o_x);
	} catch (Exception $e) {
    var_dump($e);
    var_dump($client->__last_response);
}
   //$resC = $client->doClientAdd('dupa');
   // print_lr( $client );
   //print("" . print_r( unserialize($resC), true). '<br>');
   //echo 'res:';
   //var_dump($client->__last_response);
   print("" . xml_b( $resC ). '<br>');
}


function test_doClientChange( $client ) {
   echo "test_doClientChange\n";
   $in_o = new stdClass();
   $in_o->values = array( 0 => new ClientData('dupa'), new ClientData('dupaq'));
   print_r($in_o);
   $resC = $client->doClientChange($in_o);
   //print_lr( $client );
   print("" . print_r( $resC, true). '<br>');
}

function test_doClientUserAdd( $client ) {
   echo "test_doClientUserAdd\n";
   $in_o = new stdClass();
   //$in_o->DocumentElement = array( 'value' => new ClientUserData('dupa'), 'element_2' => new ClientUserData('dupaq'));
   $c1 = new ClientUserData('dupa');
   $c2 = new ClientUserData('foobar_dupa');
   //var_dump($c1);
   $in_o = array( 'value_1' => $c1->return_array(),  'value_2' => $c2->return_array());
   //print_r($in_o);
   $in_o_x = ArrayToXML::toXml($in_o);
   print("" . xml_b( $in_o_x ). '<br>');
   $resC = $client->doClientUserAdd($in_o_x);
   //print_lr( $client );
   //print("" . print_r( $resC, true). '<br>');
   print("" . print_r( $resC, true). '<br>');
}

function test_doClientUserChange( $client ) {
   echo "test_doClientUserChange\n";
   $in_o = new stdClass();
   $in_o->values = array( 0 => new ClientUserData('dupa'), new ClientUserData('dupaq'));
   print_r($in_o);
   $resC = $client->doClientUserChange($in_o);
   //print_lr( $client );
   print("" . print_r( $resC, true). '<br>');
}

function test_doClientUserDelete( $client ) {
   echo "test_doClientUserDelete\n";
   $in_o = new stdClass();
   $in_o->values = array( 0 => new ClientUserData('dupa'), new ClientUserData('dupaq'));
   print_r($in_o);
   $resC = $client->doClientUserDelete($in_o);
   //print_lr( $client );
   print("" . print_r( $resC, true). '<br>');
}

// test_getClientList( $client );
// test_getClientUserList( $client );

function test_getClientList( $client ) {
   echo "test_getClientList\n";
   $in_o = new ParamStartLength();
   print_r($in_o);
   $resC = $client->getClientList($in_o);
   //print_lr( $client );
   print("" . print_r( $resC, true). '<br>');
}

function test_getClientUserList( $client ) {
   echo "test_getClientUserList\n";
   $in_o = new stdClass();
   $in_o = new ParamDoubleStartLength();
   print_r($in_o);
   $resC = $client->getClientUserList($in_o);
   //print_lr( $client );
   print("" . print_r( $resC, true). '<br>');
}

function test_doClientUserSetPassword( $client ) {
   echo "test_doClientUserSetPassword\n";
   $in_o = new stdClass();
   $in_o->values = array( 0 => new ClientUserPassword('dupa'), new ClientUserPassword('dupaq'));
   print_r($in_o);
   $resC = $client->doClientUserSetPassword($in_o);
   //print_lr( $client );
   print("" . print_r( $resC, true). '<br>');
}



//Produkty / Kategorie


function test_doProductAdd( $client ) {
   echo "test_doProductAdd\n";
   $in_o = new stdClass();
   $in_o->values = array( 'element_1' => new ProductData('dupa'), 'element_2' => new ProductData('dupaq'));
   //print_r($in_o);
   $in_o_x = ArrayToXML::toXml($in_o);
   print("" . xml_b( $in_o_x ). '<br>');
   $resC = $client->doProductAdd($in_o_x, 'test_doProductAdd');
   //print_lr( $client );
   //print("" . print_r( $resC, true). '<br>');
   print("" . xml_b( $resC ). '<br>');
}

function test_doProductChange( $client ) {
   echo "test_doProductChange\n";
   $in_o = new stdClass();
   $in_o->values = array( 0 => new ProductData('dupa'), new ProductData('dupaq'));
   print_r($in_o);
   $resC = $client->doProductChange($in_o);
   //print_lr( $client );
   print("" . print_r( $resC, true). '<br>');
}


function test_setProductClientPrice( $client ) {
   echo "test_setProductClientPrice\n";
   $in_o = new stdClass();
   $in_o->values = array( 0 => new ProductClientPriceData('dupa'), new ProductClientPriceData('dupaq'));
   //$in_o = 'test';
   print_r($in_o);
   $resC = $client->setProductClientPrice($in_o);
   //print_lr( $client );
   print("" . print_r( $resC, true). '<br>');
}


function test_doCategoryAdd( $client ) {
   echo "test_doCategoryAdd\n";
   $in_o = new stdClass();
   $in_o->values = array('element_1' => new CategoryData('dupa'), 'element_2' => new CategoryData('dupaq'));
   //$in_o = 'test';
   //print_r($in_o);
   $in_o_x = ArrayToXML::toXml($in_o);
   print("" . xml_b( $in_o_x ). '<br>');
   $resC = $client->doCategoryAdd($in_o_x);
   //print_lr( $client );
   //print("" . print_r( $resC, true). '<br>');
   print("" . xml_b( $resC ). '<br>');
}

function test_doCategoryEdit( $client ) {
   echo "test_doCategoryEdit\n";
   $in_o = new stdClass();
   $in_o->values = array( 0 => new CategoryData('dupa'), new CategoryData('dupaq'));
   //$in_o = 'test';
   print_r($in_o);
   $resC = $client->doCategoryEdit($in_o);
   //print_lr( $client );
   print("" . print_r( $resC, true). '<br>');
}

function test_doCategoryDelete( $client ) {
   echo "test_doCategoryDelete\n";
   $in_o = new stdClass();
   $in_o->values = array( 0 => new CategoryData('dupa'), new CategoryData('dupaq'));
   //$in_o = 'test';
   print_r($in_o);
   $resC = $client->doCategoryDelete($in_o);
   //print_lr( $client );
   print("" . print_r( $resC, true). '<br>');
}

function test_setProduct2Category( $client ) {
   echo "test_setProduct2Category\n";
   $in_o = new stdClass();
   $in_o->values = array( 0 => new Product2Category('dupa'), new Product2Category('dupaq'));
   //$in_o = 'test';
   //print_r($in_o);
   $in_o_x = ArrayToXML::toXml($in_o);
   print("" . xml_b( $in_o_x ). '<br>');
   $resC = $client->setProduct2Category($in_o_x);
   //print_lr( $client );
   //print("" . print_r( $resC, true). '<br>');
   print("" . xml_b( $resC ). '<br>');
}


function test_getProductList( $client ) {
   echo "test_getProductList\n";
   //$in_oo = new ParamStartLength('dupa');
   $in_oo = new ParamStartLength(array('id_start' => '1', 'length' => '5'));
   //var_dump($in_oo);
   if( $in_oo->is_error() ) print_r(array('error' => $in_oo->return_error(), 'warning' => $in_oo->return_warning()) );
   //$resC = $client->getOrderList( $in_oo);
   //print_r("IN:".$in_oo->return_array());
   $in_o = array( 'value_1' => $in_oo->return_array() );
   $in_o_x = ArrayToXML::toXml($in_o, 'DocumentElement');
   //echo $in_o_x;
   print("IN:" . xml_b( $in_o_x ). '<br>');
   try {
      $resC = $client->getProductList($in_o_x);
   } catch (Exception $e) {
      var_dump($e);
      var_dump($client->__last_response);
   }
   //print_r($in_oo);
   ///$resC = $client->getProductList( $in_oo );
   //print_lr( $client );
   print("RES:" . xml_b( $resC ). '<br>');
}

function test_getProductListFromCategory( $client ) {
   echo "test_getProductListFromCategory\n";
   $in_oo = new ParamDoubleStartLength('dupa');
   print_r($in_oo);
   $resC = $client->getProductListFromCategory( $in_oo );
   //print_lr( $client );
   print_r($resC);
}

function test_getCategoryList( $client ) {
   echo "test_getCategoryList\n";
   $in_oo = new ParamStartLength('dupa');
   print_r($in_oo);
   $resC = $client->getCategoryList( $in_oo );
   //print_lr( $client );
   print_r($resC);
}

function test_getProductClientPriceList( $client ) {
   echo "test_getProductClientPriceList\n";
   $in_oo = new ParamStartLength('dupa');
   print_r($in_oo);
   $resC = $client->getProductClientPriceList( $in_oo );
   //print_lr( $client );
   print_r($resC);
   
   
}

function test_getClientPriceList( $client ) {
   echo "test_getClientPriceList\n";
   $in_oo = new ParamStartLength('dupa');
   print_r($in_oo);
   $resC = $client->getClientPriceList( $in_oo );
   //print_lr( $client );
   print_r($resC);
}

function test_getClientPriceProductList( $client ) {
   echo "test_getClientPriceProductList\n";
   $in_oo = new ParamStartLength('dupa');
   print_r($in_oo);
   $resC = $client->getClientPriceProductList( $in_oo );
   //print_lr( $client );
   print_r($resC);
}

function test_getOrderListNew( $client ) {
   echo "test_getOrderListNew\n";
   $in_oo = new ParamStartLength('dupa');
   print_r($in_oo);
   $resC = $client->getOrderListNew( $in_oo );
   //print_lr( $client );
   print_r($resC);
}

function test_getOrderList( $client ) {
   echo "test_getOrderList\n";
   $in_oo = new ParamStartLength('dupa');
  //print_r($in_oo);
   //$resC = $client->getOrderList( $in_oo);
   print_r($in_oo->return_array());
   $in_o = array( 'value_1' => $in_oo->return_array() );
   $in_o_x = ArrayToXML::toXml($in_o, 'DocumentElement');
   //echo $in_o_x;
   print("" . xml_b( $in_o_x ). '<br>');
   try {
	$resC = $client->getOrderList($in_o_x);
	} catch (Exception $e) {
    var_dump($e);
    var_dump($client->__last_response);
}
   print("" . xml_b( $resC ). '<br>');
   //print_lr( $client );
   //print_r($resC);
}

function test_setOrderStatus( $client ) {
   echo "test_setOrderStatus\n";
   $in_o = new stdClass();
   $in_o->values = array( 0 => new OrderStatus('dupa'), new OrderStatus('dupaq'));
   print_r($in_o);
   $resC = $client->setOrderStatus( $in_o );
   //print_lr( $client );
   print_r($resC);
}

function test_setOrderHiddenStatus( $client ) {
   echo "test_setOrderHiddenStatus\n";
   $in_o = new stdClass();
   $in_o->values = array( 0 => new OrderData('dupa'), new OrderData('dupaq'));
   print_r($in_o);
   $resC = $client->setOrderHiddenStatus( $in_o );
   //print_lr( $client );
   print_r($resC);
}




?>