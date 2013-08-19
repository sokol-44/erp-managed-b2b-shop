<?php
if( !$P->logged_in ) $F->redirect( $F->make_link('main') );

// if( !$P->check_roles('ADMIN,OPERATOR') ) $F->redirect( $F->make_link('account') );

$BC->add_crumb(Lang::_('Account'), $F->make_link(CFG_COM_ACCOUNT) );
$BC->add_crumb(Lang::_('Orders list'), $F->make_link(CFG_COM_ORDER_BASKET_FAVORITE) );

if( $F->check_get('mode') ) {
   //single basket mode
   switch($F->GET['mode']) {
      case 'show_details':
         if( $F->check_get('id_shopping_basket_favorite') && (int)$F->GET['id_shopping_basket_favorite'] > 0  ) {
            $Shopping_Basket_Favorite = new Shopping_Basket_Favorite( (int)$F->GET['id_shopping_basket_favorite'] );
            //if( $Shopping_Basket_Favorite->check_rights() ) {
               require 'basket_favorite' . DS . 'bf_details.php';
            //} else {
            //   $F->redirect($F->make_link(CFG_COM_ORDER_LIST));
            //}
         } else {
            $F->redirect($F->make_link(CFG_COM_BASKET_FAVORITE));
         }
         break;
      case 'make_basket':
         if( $F->check_get('id_shopping_basket_favorite') && (int)$F->GET['id_shopping_basket_favorite'] > 0  ) {
            $Shopping_Basket_Favorite = new Shopping_Basket_Favorite( (int)$F->GET['id_shopping_basket_favorite'] );
            //if( $Shopping_Basket_Favorite->check_rights() ) {
            	$Shopping_Basket_Chain = Shopping_Basket_Chain::g_global();
            	$res = $Shopping_Basket_Chain->basket_from_favorite_basket( $Shopping_Basket_Favorite );
            	if( $res) $F->redirect($F->make_link(CFG_COM_BASKET));
            	print_debug($res, true);
            //} else {
            //   $F->redirect($F->make_link(CFG_COM_ORDER_LIST));
            //}
         } else {
            $F->redirect($F->make_link(CFG_COM_BASKET_FAVORITE));
         }
         break;
      case 'make_favorite_basket':
      	if( $F->check_get('id_order') && (int)$F->GET['id_order'] > 0  ) {
      		$Order = new Order($F->GET['id_order']);
      		if( $Order->check_rights() ) {
      			$Shopping_Basket_Chain = Shopping_Basket_Chain::g_global();
      			$res = $Shopping_Basket_Chain->basket_from_order( $Order );
      			if( $res) $F->redirect($F->make_link(CFG_COM_BASKET));
      		}
      		$F->redirect($F->make_link(CFG_COM_BASKET_FAVORITE));
      	} elseif( $F->check_get('id_shopping_basket') && (int)$F->GET['id_shopping_basket'] > 0  ) {
      			$Shopping_Basket_Chain = Shopping_Basket_Chain::g_global();
      			$Shopping_Basket = $Shopping_Basket_Chain->return_basket( (int)$F->GET['id_shopping_basket'] );
      			$id_shopping_basket_favorite = Shopping_Basket_Favorite::make_new_basket_basket($Shopping_Basket);
      			$arg = array('mode' => 'show_details', 'id_shopping_basket_favorite' => (int)$id_shopping_basket_favorite);
      			$F->redirect($F->make_link(CFG_COM_BASKET_FAVORITE, $arg));
      	} else {
      		$F->redirect($F->make_link(CFG_COM_BASKET_FAVORITE));
      	}
      	break;
      case 'all':
      default:
			$basket_favorite_list = Shopping_Basket_Favorite::get_basket_favorite_list();
   		require 'basket_favorite' . DS . 'bf_list_all.php';
         break;
   }
} else {
   //display basket
	$basket_favorite_list = Shopping_Basket_Favorite::get_basket_favorite_list();
   require 'basket_favorite' . DS . 'bf_list_all.php';
}
?>