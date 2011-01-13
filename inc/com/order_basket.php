<?php
//STR: tmp
//FIXME
$Shopping_Basket_Chain = Shopping_Basket_Chain::g_global();

$Page->head_title = Lang::_('Ordering basket');


//$get = $F->add_local_get( array('mode' => 'show_order', 'id_order' => (int)$id_order ));
//echo $F->output_string_html(( $F->make_link(CFG_COM_ORDER_BASKET, $get) ));

if( $F->check_get('id_nr_shopping_basket') ) {
   $Shopping_Basket = $Shopping_Basket_Chain->return_basket( (int)$F->GET['id_nr_shopping_basket'] );
} else {
   $Shopping_Basket = $Shopping_Basket_Chain->return_default_basket();
}

if( !$P->check_roles('ADMIN,OPERATOR') || !$Shopping_Basket->contents || sizeof($Shopping_Basket->contents) == 0 ) {
   $F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('mode') ) );
}

//STR: end

$BC->add_crumb( array( 'name' => Lang::_('Basket'), 'path' => $F->make_link(CFG_COM_BASKET) ) );
$BC->add_crumb( array( 'name' => Lang::_('ORDER_BASKET'), 'path' => $F->make_link(CFG_COM_ORDER_BASKET, $F->make_get()) ) );

if( $F->check_get('mode') ) {
   switch($F->GET['mode']) {
      case 'order_basket':
         list($id_order, $count_product) = Order::make_new_order($Shopping_Basket, $F->POST['order_description']);
         //FIXME - mail
         Mail2Send::order( (int)$Shopping_Basket->get_id_client(), (int)$id_order);
         ///$Shopping_Basket_Chain->remove_basket( $Shopping_Basket->id_nr_shopping_basket );
         //$Shopping_Basket_Chain->set_default_basket();
         $get = $F->add_local_get( array('mode' => 'show_order', 'id_order' => (int)$id_order ));
         $F->redirect( $F->make_link(CFG_COM_ORDER_BASKET, $get) );
         break;
      case 'show_order':
         if( $F->check_get('id_order') ) {
            $Order = new Order( (int)$F->GET['id_order'] );

            if( $Order->check_rights() ) {
               require 'order' . DS . 'show.php';
            } else {
               $F->redirect( $F->make_link(CFG_COM_BASKET), array());
            }
         } else {
            $F->redirect( $F->make_link(CFG_COM_BASKET));
         }

         break;
      default:
         $F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('mode')));
         break;
   }
} else {
   //display basket
   require 'order_basket' . DS . 'list.php';
}
?>