<?php
$Page->head_title = Lang::_('account parameters');

$GET_tmp = $F->make_get();

//FIXME
//remember backtrack
if( !$P->logged_in ) $F->redirect(  );

$BC->add_crumb(Lang::_('Account'), $F->make_link(CFG_COM_ACCOUNT) );
$BC->add_crumb(Lang::_('account parameters'), $F->make_link(CFG_COM_ACCOUNT_PARAMETERS) );
$BC->add_crumb(Lang::_('account change password'), $F->make_link(CFG_COM_ACCOUNT_PARAMETERS, array('mode' => 'change_password')) );

?>
<div class="account_param_container">
<?php
if( $F->check_get('mode') ) {
   switch($F->GET['mode']) {
      case 'show_details':
         require 'account_parameters' . DS . 'view.php';
         break;
      case 'change_password':
         if( $F->check_post('UPDATE', true) ) {
            require 'account_parameters' . DS . 'change_password_save.php';
         }
         require 'account_parameters' . DS . 'change_password.php';
      case 'change_details':
      default:
          
         break;
   }
} else {
    
    
}
?>
</div>