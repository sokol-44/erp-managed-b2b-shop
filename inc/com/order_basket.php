<?php
//STR: tmp
//FIXME
$Shopping_Basket_Chain = Shopping_Basket_Chain::g_global();

$Page->head_title = Lang::_('Ordering basket');


//$get = $F->add_local_get( array('mode' => 'show_order', 'id_order' => (int)$id_order ));
//echo $F->output_string_html(( $F->make_link(CFG_COM_ORDER_BASKET, $get) ));

if( $F->check_get('id_shopping_basket') ) {
   $Shopping_Basket = $Shopping_Basket_Chain->return_basket( (int)$F->GET['id_shopping_basket'] );
} else {
   $Shopping_Basket = $Shopping_Basket_Chain->return_default_basket();
}


//FIXME magic mode for order LEVEL_99 -> move to CLASS::Rights
if( !$P->check_roles('LEVEL_99') ) {
   Info::sadd(Lang::_('NOT_ENOUGH_RIGHTS'));
   $F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('mode') ) );
} elseif( !$Shopping_Basket->contents || sizeof($Shopping_Basket->contents) == 0 ) {
	Info::sadd(Lang::_('BASKET_EMPTY'));
	$F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('mode') ) );
}
// die();
//STR: end

$BC->add_crumb( array( 'name' => Lang::_('Basket'), 'path' => $F->make_link(CFG_COM_BASKET) ) );
$BC->add_crumb( array( 'name' => Lang::_('ORDER_BASKET'), 'path' => $F->make_link(CFG_COM_ORDER_BASKET, $F->make_get()) ) );

if( $F->check_get('mode') ) {
   switch($F->GET['mode']) {
      case 'prepare_order_basket':
         require 'order_basket' . DS . 'prepare_order_basket.php';
         break;
      case 'order_basket':
      	$param_in = array('order_description' => $F->POST['order_description'],
      	'id_address' => (int)$F->POST['order_address'], 'id_account_manager' => (int)$F->POST['account_manager']);
      	foreach(Data::$Data_order_params as $attr_key => $attr_val) {
      		$attr_name = 'attr_' . $attr_key;
      		if( isset($F->POST[$attr_name]) && $F->not_null($F->POST[$attr_name]) ) {
      			$param_in[$attr_key] = $F->POST[$attr_name];
      		}
      	}
         list($id_order, $count_product) = Order::make_new_order($Shopping_Basket, $param_in);
         //FIXME - mail
         if(!$id_order) {
            Info::sadd(Lang::_('NOT_ENOUGH_RIGHTS'));
         }
         Mail2Send::order( (int)$Shopping_Basket->get_id_client(), (int)$id_order);
         
         $get = $F->add_local_get( array('mode' => 'show_order', 'id_order' => (int)$id_order ));
         //die($F->GET['mode']);
         $F->redirect( $F->make_link(CFG_COM_ORDER_BASKET, $get) );
         break;
      case 'show_order':
         if( $F->check_get('id_order') ) {
            $Order = new Order( (int)$F->GET['id_order'] );

            if( $Order->check_rights() ) {
               require 'order' . DS . 'show.php';
            } else {
               Info::sadd(Lang::_('NOT_ENOUGH_RIGHTS'));
               $F->redirect( $F->make_link(CFG_COM_BASKET), array());
            }
         } else {
            Info::sadd(Lang::_('NOT_ENOUGH_DATA'));
            $F->redirect( $F->make_link(CFG_COM_BASKET));
         }
         break;
      default:
         Info::sadd(Lang::_('NOT_ENOUGH_DATA'));
         $F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('mode')));
         break;
   }
} else {
   //display basket
   require 'order_basket' . DS . 'list.php';
}
?>