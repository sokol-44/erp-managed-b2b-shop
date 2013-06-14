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
   if( !$F->check_valid_email( $F->POST['cf_email'] ) ) {
      $send = false;
      Info::sadd(Lang::_('wrong email adress'));
   }
   if( !$F->check_post('cf_name') ) {
      $send = false;
      Info::sadd(Lang::_('empty firstname and secondname'));
   }
   if( !$F->check_post('cf_telephone') ) {
      $send = false;
      Info::sadd(Lang::_('empty telephone'));
   }
   if( !$F->check_post('cf_contents') ) {
      $send = false;
      Info::sadd(Lang::_('empty contents'));
   }
   if( $send ) {
      $res = Mail2Send::to_account_manager_contact($F->array_recursive_strip_tags($F->POST));
      Info::sadd(Lang::_('Message send'), 'success');
   }
   $Page->redirect( $F->make_link(CFG_COM_CONTACT) );
} else {
   //display basket
   require 'contact' . DS . 'form.php';
}
?>