<?php
$Page->head_title = Lang::_('account parameters');

$GET_tmp = $F->make_get();

//FIXME
//remember backtrack
if( !$P->logged_in ) $F->redirect(  );

$BC->add_crumb(Lang::_('Account'), $F->make_link(CFG_COM_ACCOUNT) );
$BC->add_crumb(Lang::_('account parameters'), $F->make_link(CFG_COM_ACCOUNT_PARAMETERS) );


if( $F->check_get('mode') ) {
   switch($F->GET['mode']) {
      case 'show_details':
         require 'account_parameters' . DS . 'view.php';
         break;
      case 'change_password':
         
      case 'show_details':
      default:
         
         break;
   }
} else {
   
   
}


?>