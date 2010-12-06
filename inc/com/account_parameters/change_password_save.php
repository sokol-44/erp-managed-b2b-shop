<?php
if( !$P->logged_in ) $F->redirect(  );

$Info = Info::g_global();

$error = false;

if( !$F->check_post('old_password') ) {
   $Info->add(Lang::_('Empty old password'));
   $error = true;
}

if( !$F->check_post('new_password') || !$F->check_post('new_password_retype') ) {
   $Info->add(Lang::_('Empty new password(s)'));
   $error = true;
} else {

   if( $F->POST['new_password'] != $F->POST['new_password_retype'] ) {
      $Info->add(Lang::_('new password and retype dosn\'t mach'));
      $error = true;
   }
}

if( !$error && !$P->update_person_password($F->POST['old_password'], $F->POST['new_password'], 'CLIENT')  ) {
   $Info->add(Lang::_('old password incorect'));
   $error = true;
}

if( !$error ) {
   $Info->add(Lang::_('old password incorect'), 'success');
}

$F->redirect($F->self_link());

?>