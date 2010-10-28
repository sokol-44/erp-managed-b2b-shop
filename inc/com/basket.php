<?php
//STR: tmp
//FIXME
if( DEBUG_DB_QUERIES == 'false' && false) $Shopping_Basket = new Shopping_Basket();
else $Shopping_Basket = Shopping_Basket::g_global();
//STR: end

$BC->add_crumb( array( 'name' => Lang::_('Basket'), 'path' => $F->make_link(CFG_COM_BASKET) ) );

if( $F->check_get('mode') ) {
   switch($F->GET['mode']) {
      //   case 'add_basket':
      //   case 'remove_basket':
      case 'add_to_basket':
         $Shopping_Basket->add_to_basket($F->GET['id_product']);
         break;
      case 'remove_from_basket':
         $Shopping_Basket->remove_from_basket($F->GET['id_product']);
         break;
      case 'update_basket':
         $Shopping_Basket->update_basket_quantity_list($F->POST['product_quantity']);
      default:
         
         break;
   }
   $F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('mode')));
} else {
   //display basket
   require 'basket' . DS . 'list.php';
}
?>