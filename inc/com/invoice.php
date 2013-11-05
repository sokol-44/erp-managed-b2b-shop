<?php
//STR: tmp
// $Invoices = Shopping_Invoices::g_global();


// print_debug($Shopping_Basket_Chain, true);
// print_debug($Shopping_Basket, true);
$BC->add_crumb( array( 'name' => Lang::_('Invoices'), 'path' => $F->make_link(CFG_COM_INVOICE) ) );

   //edit rights
   $Shopping_Basket = FALSE;

//    if ( $F->check_get('id_invoice') && $F->not_null($F->GET['id_invoice']) ) {
//       $Invoice = $Invoices->return_invoice((int)$F->GET['id_invoice']);
//    }


   if( $F->check_get('show') ) {
   //show
   $mode=$F->GET['show'];
   switch($F->GET['show']) {
      case 'all':
      case 'paid':
      case 'unpaid':
      case 'overdue':
      case 'pay':
         require 'invoice' . DS . 'i_list_all.php';
         break;
      case 'show_invoice_image':
      	$Rights = Rights::g_global();
      	if( $F->check_get('id_invoice') && (int)$F->GET['id_invoice'] > 0 ) {
      		$Invoice = new Invoice( (int)$F->GET['id_invoice'] );
      		list($res, $res_debug) = $Rights->invoice_rights($Invoice->params, 'SHOW', $show_info);
      		if( $F->not_null($Invoice->params['invoice_image']) && $res ) {
      			require 'invoice' . DS . 'i_show_invoice_image.php';
      			die();
      		}
      	}
      	$F->redirect( $F->make_link(CFG_COM_INVOICE));
      	//sprawdzić, pokazać
      	break;
      default:
      	$F->redirect( $F->make_link(CFG_COM_INVOICE));
         break;
   }
} else {
   //display basket
   require 'invoice' . DS . 'i_list_all.php';
}
?>