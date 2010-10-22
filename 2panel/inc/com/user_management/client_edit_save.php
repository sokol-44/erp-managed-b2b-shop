<?php
$error = false;

if( !$F->check_post('name') ) {
   $Info->add(Lang::_('Empty name'));
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


if( !$F->check_post('state') ) {
   $Info->add(Lang::_('Empty state') . '$' . $F->POST['state'] . '$');
   $error = true;
}

if( !$error ) {
   if( $F->not_null($client_data) ) {
      Data::update_client_data($client_data['id_client'], $F->POST,  $client_data);
      $Info->add(Lang::_('Edit success'), 'success');
      $F->redirect($F->self_link());
   } else {
      Data::insert_client_data($F->POST);
      $Info->add(Lang::_('Add success'), 'success');
      $F->redirect( $F->make_link($F->com, $F->make_get('mode')));
   }
   //print_r($F->POST);
} else {
   print_r($Info);
}

?>