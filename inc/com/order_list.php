<?php
if( !$P->logged_in ) $F->redirect( $F->make_link('main') );

// if( !$P->check_roles('ADMIN,OPERATOR') ) $F->redirect( $F->make_link('account') );

$BC->add_crumb(Lang::_('Account'), $F->make_link(CFG_COM_ACCOUNT) );
$BC->add_crumb(Lang::_('Orders list'), $F->make_link(CFG_COM_ORDER_LIST) );

if( $F->check_get('mode') ) {
   //single basket mode
   switch($F->GET['mode']) {
      case 'show_details':
         if( $F->check_get('id_order') && $F->GET['id_order'] > 0  ) {
            $Order = new Order($F->GET['id_order']);
            if( $Order->check_rights() ) {
               $total = $Order->calculate_total();
               $version_list = $Order->source_basket->get_all_version();
               $history_list = $Order->source_basket->get_all_history();
               require 'order_list' . DS . 'view_details.php';
            } else {
               $F->redirect($F->make_link(CFG_COM_ORDER_LIST));
            }
         } else {
            $F->redirect($F->make_link(CFG_COM_ORDER_LIST));
         }
         break;
      case 'all':
      default:
         //display orders
      	 $order_chain = new Order_Chain((int)$P->data['id_client']);
      	 $order_chain->calculate_total();
         require 'order_list' . DS . 'list.php';
         break;
   }
} else {
   //display basket
	$order_chain = new Order_Chain((int)$P->data['id_client']);
	$order_chain->calculate_total();
   require 'order_list' . DS . 'list.php';
}
?>