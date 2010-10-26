<?php
$com = array();

if( $F->check_get('mode') ) {
   switch($F->GET['mode']) {
      //   case 'add_basket':
      //   case 'remove_basket':
      case 'add_to_basket':
         break;
      case 'remove_from_basket':
         break;
      default:
         // $F->redirect( $F->make_link($F->com, $F->make_get('mode')));
         break;
   }
} else {
    //display baket
    require 'basket' . DS . 'list.php';
}

?>