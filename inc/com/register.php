<?php
//STR: tmp

if( $F->check_get('mode') ){

} elseif( $F->check_get('action') && $F->GET['action'] == 'send' ) {
   /*
    * czeck if has godd data
    *
    * sanityze data
    */
   $send = true;
   if( !$F->check_post('rf_name') ) {
      $send = false;
      Info::sadd(Lang::_('empty name'));
   }
   if( !$F->check_post('rf_telephone') ) {
      $send = false;
      Info::sadd(Lang::_('empty telephone'));
   }
   if( !$F->check_post('rf_contents') ) {
      $send = false;
      Info::sadd(Lang::_('empty contents'));
   }
   
   if( !$F->check_valid_email( $F->POST['rf_email'] ) ) {
   	$send = false;
   	Info::sadd(Lang::_('wrong email adress') . ' ' . $F->output_string( $F->POST['rf_email'] ) );
   }
   if( Data::email_in_system($F->POST['rf_email'])  ) {
   	$send = false;
   	Info::sadd(Lang::_('email address in system') . ' ' . $F->output_string( $F->POST['rf_email'] ) );
   }
   
   if( !$F->check_post('rf_uname') ) {
      $send = false;
      Info::sadd(Lang::_('empty name'));
   }
   
   if( !$F->check_post('rf_ulname') ) {
      $send = false;
      Info::sadd(Lang::_('empty login'));
   }  
   if( Data::login_in_system($F->POST['rf_ulname'])  ) {
   	$send = false;
   	Info::sadd(Lang::_('login in system') . ' ' . $F->output_string( $F->POST['rf_ulname'] ) );
   }
   
   if( !$F->check_post('rf_upassword') ) {
      $send = false;
      Info::sadd(Lang::_('empty password'));
   }
   if( $F->POST['rf_upassword'] != $F->POST['rf_upass2'] ) {
      $send = false;
      Info::sadd(Lang::_('diffrent passwords'));
   }
   
   if( !$F->check_valid_email( $F->POST['rf_uemail'] ) ) {
   	$send = false;
   	Info::sadd(Lang::_('wrong email adress') . ' ' . $F->output_string( $F->POST['rf_uemail'] ) );
   }
   if( Data::email_in_system($F->POST['rf_uemail'])  ) {
   	$send = false;
   	Info::sadd(Lang::_('email address in system') . ' ' . $F->output_string( $F->POST['rf_uemail'] ) );
   }
   
   if( !$F->check_post('rf_utelephone') ) {
      $send = false;
      Info::sadd(Lang::_('empty telephone'));
   }
   
   if( $send ) {
   	$data = $F->array_recursive_strip_tags($F->POST);
   	$res_person = Person::add_new_client_and_user($data);
   	//$res_person = array('id_client' => 'id_client', 'id_client_user' => 'id_client_user');
   	if( $F->not_null($res_person) ) {
      	$res = Mail2Send::to_account_manager_register($data, $res_person);
      	if( $res ) {
      		Info::sadd(Lang::_('Message send'), 'success');
      	} else {
      		Info::sadd(Lang::_('cannot send email'));
      	}
   	} else {
   		Info::sadd(Lang::_('cannot add client'));
   	}
   }
   $Page->redirect( $F->make_link(CFG_COM_REGISTER) );
} else {
   //display basket
   require 'register' . DS . 'form.php';
}
?>