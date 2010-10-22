<?php
$error = false;

if( !$F->check_post('login') ) {
   $Info->add(Lang::_('Empty login'));
   $error = true;
}

if( !$F->check_post('description') ) {
   $Info->add(Lang::_('Empty description'));
   $error = true;
}

if( !$F->check_post('email') ) {
   $Info->add(Lang::_('Empty email'));
   $error = true;
}


if( $F->check_post('new_password') ) {
   if( $F->POST['new_password'] != $F->POST['new_password_retype']) {
      $Info->add(Lang::_('password don\'t match'));
      $error = true;
   }
}

if( $F->is_null($person_data) ) {
   if( $F->is_null( $F->POST['new_password'] ) ) {
      $Info->add(Lang::_('password not set'));
      $error = true;
   }
}

if( !$F->check_post('rights_ids') ) {
   $Info->add(Lang::_('Empty rights_ids') . '$' . print_r($F->POST['rights_ids'], true) . '$');
   $error = true;
}

if( !$F->check_post('state') ) {
   $Info->add(Lang::_('Empty state') . '$' . $F->POST['state'] . '$');
   $error = true;
}

if( !$error ) {
   if( $F->not_null($person_data) ) {
      Data::update_person_data('ADMIN',  $person_data['id_admin'], $F->POST,  $person_data);
      $Info->add(Lang::_('Edit success'), 'success');
      $F->redirect($F->self_link());
   } else {
      Data::insert_person_data('ADMIN', $F->POST);
      $Info->add(Lang::_('Add success'), 'success');
      $F->redirect( $F->make_link($F->com, $F->make_get('mode')));
   }

} else {
   print_r($Info);
}

?>