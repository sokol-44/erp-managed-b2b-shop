<?php
//STR: tmp
$Shopping_Basket_Chain = Shopping_Basket_Chain::g_global();


// print_debug($Shopping_Basket_Chain, true);
// print_debug($Shopping_Basket, true);
$BC->add_crumb( array( 'name' => Lang::_('Basket'), 'path' => $F->make_link(CFG_COM_BASKET) ) );

   //edit rights
   $Shopping_Basket = FALSE;
      
   if ( $Shopping_Basket_Chain->id_basket_set ) {
      $Shopping_Basket = $Shopping_Basket_Chain->return_basket_modify($Shopping_Basket_Chain->id_basket_current);
   }
   
   if( $Shopping_Basket === FALSE ) {
      $Shopping_Basket_Chain->set_default_basket_by_date();
      $Shopping_Basket = $Shopping_Basket_Chain->return_default_basket();
   }
   
   if( $Shopping_Basket === FALSE ) {
      print_debug($Shopping_Basket, true);
      die();
      //$F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('mode')));
   }
   
   //single basket mode
   if( $F->check_get('mode') ){
      $product_params = array(
            'id_product' => (int)$F->GET['id_product'],
            'id_product_subtype' => (int)$F->GET['id_product_subtype']
      );
      switch($F->GET['mode']) {
         //   case 'add_basket':
         //   case 'remove_basket':
         case 'add_to_basket':
            $Shopping_Basket->add_to_basket( $product_params );
            break;
         case 'remove_from_basket':
            $Shopping_Basket->remove_from_basket( $F->GET['product_key'] );
            break;
         case 'update_basket':
            $Shopping_Basket->update_basket_quantity_list($F->POST['product_quantity'], $F->POST['description']);
            if( $F->check_post('PREPARE ORDER_BASKET', true) ) {
               $GET_tmp = $F->make_get('mode');
               $GET_id  = $F->add_local_get('id_shopping_basket', (int)$Shopping_Basket->id_shopping_basket, $GET_id);
               $F->redirect( $F->make_link(CFG_COM_ORDER_BASKET, $GET_id) );
            }
         default:
            break;
      }
      $F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('mode')));
      
   //multi basket
   } elseif( $F->check_get('action') ) {
      //group basket action
      switch($F->GET['action']) {
         case 'remove_basket':
            //FIXME
            //trow some error
            if( $P->logged_in && $F->check_get('id_shopping_basket') )
               $Shopping_Basket_Chain->remove_basket( (int)$F->GET['id_shopping_basket'] );
            else
               ;
            break;
         case 'clean_basket':
            //FIXME
            //trow some error
            if( $F->check_get('id_shopping_basket') )
               $Shopping_Basket_Chain->clean_basket( (int)$F->GET['id_shopping_basket'] );
            else
               ;
            break;
         case 'add_to_mainbasket':
            //FIXME
            //trow some error
            if( $F->check_get('id_shopping_basket') )
               $Shopping_Basket_Chain->add_to_mainbasket( (int)$F->GET['id_shopping_basket'] );
            else
               ;
            break;
         case 'switch_basket':
            //FIXME
            //trow some error
            if( $P->logged_in && $F->check_get('id_shopping_basket') ) {
               $Shopping_Basket_Chain->set_default_basket( (int)$F->GET['id_shopping_basket'] );
            }
            if( $Shopping_Basket === FALSE ) {
               $Shopping_Basket_Chain->set_default_basket_by_date();
               $Shopping_Basket = $Shopping_Basket_Chain->return_default_basket();
            }
            else
               ;
            break;
         default:
            break;
      }
      $F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('action,id_shopping_basket', false)) );
} elseif( $F->check_get('show') ) {
   //show
   switch($F->GET['show']) {
      case 'all':
         require 'basket' . DS . 'list_all.php';
         break;
      case 'version_details':
         require 'basket' . DS . 'list.php';
         break;
      case 'history_details':
         require 'basket' . DS . 'list.php';
         break;
      default:
         $F->redirect( $F->make_link(CFG_COM_BASKET));
         break;
   }
} elseif( $F->check_get('add_basket') ) {
   if( $P->logged_in )
      $Shopping_Basket_Chain->add_basket();
   $F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('mode')));
} else {
   //display basket
   require 'basket' . DS . 'list.php';
}
?>