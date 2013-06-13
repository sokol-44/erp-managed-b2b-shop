<?php
//STR: tmp
$Shopping_Basket_Chain = Shopping_Basket_Chain::g_global();


// print_debug($Shopping_Basket_Chain, true);
// print_debug($Shopping_Basket, true);

if( $F->check_get('mode') ){

} elseif( $F->check_get('action') && $F->GET['action'] == 'send' ) {
   /*
    * czeck if has godd data
    *
    * sanityze data
    */
   $res = Mail2Send::to_account_manager_contact($F->POST);
} else {
   //display basket
   require 'contact' . DS . 'form.php';
}
?>