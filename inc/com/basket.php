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
      if( !$Shopping_Basket->check_rights('MODIFY_CONTENTS') ) {
         Info::sadd('NOT_ENOUGH_RIGHTS');
         $F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('mode')));
      }
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
            //FIXME
            $Shopping_Basket->update_basket_quantity_list($F->POST['product_quantity'], $F->POST['description']);
            if( $F->check_post('PREPARE_ORDER_BASKET') ) {
               $GET_tmp = $F->make_get('mode');
               $GET_tmp = $F->add_local_get('mode', 'prepare_order_basket', $GET_tmp);
               $GET_tmp = $F->add_local_get('id_shopping_basket', (int)$Shopping_Basket->id_shopping_basket, $GET_tmp);
               $F->redirect( $F->make_link(CFG_COM_ORDER_BASKET, $GET_tmp) );
            } elseif ( $F->check_post('CHANGE_LEVEL_UP') ) {
               if( $Shopping_Basket->check_move('UP') ) {
                  //$Shopping_Basket->change_level('UP');
                  $GET_tmp = $F->make_get(array('mode', 'show'));
                  $GET_tmp = $F->add_local_get('show', 'change_level_up', $GET_tmp);
                  $GET_tmp = $F->add_local_get('id_shopping_basket', (int)$Shopping_Basket->id_shopping_basket, $GET_tmp);
                  $F->redirect( $F->make_link(CFG_COM_BASKET, $GET_tmp) );
               } else {
                  Info::sadd('NOT_ENOUGH_RIGHTS');
               }
            } elseif ( $F->check_post('CHANGE_LEVEL_DOWN') ) {
               if( $Shopping_Basket->check_move('DOWN') ) {
                  //$Shopping_Basket->change_level('DOWN');
                  $GET_tmp = $F->make_get(array('mode', 'show'));
                  $GET_tmp = $F->add_local_get('show', 'change_level_down', $GET_tmp);
                  $GET_tmp = $F->add_local_get('id_shopping_basket', (int)$Shopping_Basket->id_shopping_basket, $GET_tmp);
                  $F->redirect( $F->make_link(CFG_COM_BASKET, $GET_tmp) );
               } else {
                  Info::sadd('NOT_ENOUGH_RIGHTS');
               }
            }
         default:
            break;
      }
      $F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('mode')));
      
   //multi basket
   } elseif( $F->check_get('action') ) {
      //group basket action
      if( !$Shopping_Basket->check_rights('MODIFY_CONTENTS') ) {
         Info::sadd('NOT_ENOUGH_RIGHTS');
         $F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('mode')));
      }
      switch($F->GET['action']) {
         case 'remove_basket':
            //FIXME
            //trow some error
            if( $P->logged_in && $F->check_get('id_shopping_basket') ) {
               $res = $Shopping_Basket_Chain->remove_basket( (int)$F->GET['id_shopping_basket'] );
               if( $res ) Info::sadd('BASKET DELETED', 'success');
               else Info::sadd('DELETE ERROR', 'success');
            } else {
               Info::sadd('NOT_ENOUGH_RIGHTS');
            }
            $info = Info::g_global();
            print_debug($info);
            die();
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
         case 'lock_basket':
            //FIXME
            //trow some error
            if( $F->check_get('id_shopping_basket') )
               $Shopping_Basket_Chain->set_lock_basket( (int)$F->GET['id_shopping_basket'] );
            else
               ;
            break;
         case 'unlock_basket':
            //FIXME
            //trow some error
            if( $F->check_get('id_shopping_basket') ) {
               $Shopping_Basket_Chain->set_unlock_basket( (int)$F->GET['id_shopping_basket'] );
               die();
            } else
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
         case 'change_level_up':
            $new_id = $Shopping_Basket->basket_level_change('UP', $F->POST['history_description']);
            break;
         case 'change_level_down':
            $new_id = $Shopping_Basket->basket_level_change('DOWN', $F->POST['history_description']);
            break;
         default:
            break;
      }
      $F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('action,id_shopping_basket', false)) );
//       $F->redirect( $F->make_link(CFG_COM_BASKET, $F->make_get('action,id_shopping_basket', false)) );
} elseif( $F->check_get('show') ) {
   //show
   switch($F->GET['show']) {
      case 'all':
         require 'basket' . DS . 'list_all.php';
         break;
      case 'version_details':
         require 'basket' . DS . 'details.php';
         break;
      case 'history_details':
         require 'basket' . DS . 'details.php';
         break;
      case 'change_level_up':
      case 'change_level_down':
         require 'basket' . DS . 'basket_change_level.php';
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
   require 'basket' . DS . 'details.php';
}
?>