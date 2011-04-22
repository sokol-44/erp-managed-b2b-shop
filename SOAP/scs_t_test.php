<?php

function test_doClientAdd( $client ) {
   echo "test_doClientAdd\n";
   //$in_o = array( 0 => new ClientData('dupa'), new ClientData('dupaq'));
   $in_o = new stdClass();
   $in_o->values = array( 0 => new ClientData('dupa'), new ClientData('dupaq'));
   print_r($in_o);
   $resC = $client->doClientAdd($in_o);
   // print_lr( $client );
   //print("" . print_r( unserialize($resC), true). '<br>');
   //echo 'res:';
   print("" . print_r( $resC, true). '<br>');
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
   $in_o->values = array( 0 => new ClientUserData('dupa'), new ClientUserData('dupaq'));
   print_r($in_o);
   $resC = $client->doClientUserAdd($in_o);
   //print_lr( $client );
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
   $in_o->values = array( 0 => new ProductData('dupa'), new ProductData('dupaq'));
   print_r($in_o);
   $resC = $client->doProductAdd($in_o, 'test_doProductChange');
   //print_lr( $client );
   print("" . print_r( $resC, true). '<br>');
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




function test_getProductList( $client ) {
   echo "test_getProductList\n";
   $in_oo = new ParamStartLength('dupa');
   //print_r($in_oo);
   $resC = $client->getProductList( $in_oo );
   //print_lr( $client );
   print_r($resC);
}




?>