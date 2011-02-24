<?php

function test_doClientAdd( $client ) {
   //$in_o = array( 0 => new ClientData('dupa'), new ClientData('dupaq'));
   $in_o = new stdClass();
   $in_o->values = array( 0 => new ClientData('dupa'), new ClientData('dupaq'));
   print_r($in_o);
   $resC = $client->doClientAdd($in_o);
   //print_lr( $client );
   //print("" . print_r( unserialize($resC), true). '<br>');
   print("" . print_r( $resC, true). '<br>');
}

?>