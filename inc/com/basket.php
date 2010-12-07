<?php
//STR: tmp
//FIXME
$Shopping_Basket_Chain = Shopping_Basket_Chain::g_global();
$Shopping_Basket = $Shopping_Basket_Chain->return_default_basket();
//STR: end

$BC->add_crumb( array( 'name' => Lang::_('Basket'), 'path' => $F->make_link(CFG_COM_BASKET) ) );

if( $F->check_get('mode') ) {
   //single basket mode
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
         $Shopping_Basket->update_basket_quantity_list($F->POST['product_quantity'], $F->POST['description']);
         if( $F->check_post('PREPARE ORDER_BASKET', true) ) {
            $GET_tmp = $F->make_get('mode');
            $GET_id  = $F->add_local_get('id_nr_shopping_basket', (int)$Shopping_Basket->id_nr_shopping_basket, $GET_id);
            print_r( $GET_id);
            $F->redirect( $F->make_link(CFG_COM_ORDER_BASKET, $GET_id) );
         }
      default:

         break;
   }
   $F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('mode')));
} elseif( $F->check_get('action') ) {
   //group basket action
   switch($F->GET['action']) {
      case 'add_basket':
         if( $P->logged_in )
         $Shopping_Basket_Chain->add_basket();
         break;
      case 'remove_basket':
         //FIXME
         //trow some error
         if( $P->logged_in && $F->check_get('id_nr_shopping_basket') )
         $Shopping_Basket_Chain->remove_basket($F->GET['id_nr_shopping_basket']);
         else
         ;
         break;
      case 'clean_basket':
         //FIXME
         //trow some error
         if( $F->check_get('id_nr_shopping_basket') )
         $Shopping_Basket_Chain->clean_basket($F->GET['id_nr_shopping_basket']);
         else
         ;
         break;
      case 'add_to_mainbasket':
         //FIXME
         //trow some error
         if( $F->check_get('id_nr_shopping_basket') )
         $Shopping_Basket_Chain->add_to_mainbasket($F->GET['id_nr_shopping_basket']);
         else
         ;
         break;
      case 'switch_basket':
         //FIXME
         //trow some error
         if( $P->logged_in && $F->check_get('id_nr_shopping_basket') )
         $Shopping_Basket_Chain->set_default_basket($F->GET['id_nr_shopping_basket']);
         else
         ;
         break;
      default:
         break;
   }
   $F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('action,id_nr_shopping_basket', false)) );
} elseif( $F->check_get('show') ) {
   //show
   switch($F->GET['show']) {
      case 'all':
         require 'basket' . DS . 'list_all.php';
         break;
      default:
         $F->redirect( $F->make_link(CFG_COM_BASKET));
         break;
   }
} else {
   //display basket
   require 'basket' . DS . 'list.php';
}
?>