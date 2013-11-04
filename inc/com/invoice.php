<?php
//STR: tmp
// $Invoices = Shopping_Invoices::g_global();


// print_debug($Shopping_Basket_Chain, true);
// print_debug($Shopping_Basket, true);
$BC->add_crumb( array( 'name' => Lang::_('Invoices'), 'path' => $F->make_link(CFG_COM_INVOICE) ) );

   //edit rights
   $Shopping_Basket = FALSE;

   if ( $F->check_get('id_invoice') && $F->not_null($F->GET['id_invoice']) ) {
      $Invoice = $Invoices->return_invoice((int)$F->GET['id_invoice']);
   }


   if( $F->check_get('show') ) {
   //show
   $mode=$F->GET['show'];
   switch($F->GET['show']) {
      case 'all':
      case 'paid':
      case 'unpaid':
      case 'overdue':
      case 'pay':
         require 'basket' . DS . 'i_list_all.php';
         break;
      case 'show_invoice_image':
      	//sprawdzić, pokazać
      	$F->redirect( $F->make_link(CFG_COM_INVOICE));
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