<?php
$com = array();
/* TODO
 * ala ma kota
 */


$userlist = array();


switch($F->GET['type']) {
   //###########################
   //ADMIN
   case 'ADMIN':
      $list_param ='ADMIN';
      switch($F->GET['mode']) {
         case 'add':
            if( $F->check_post('SAVE', true) ) {
               require 'user_management' . DS . 'admin_edit_save.php';
            }

            require 'user_management' . DS . 'admin_add.php';
            break;
         case 'edit':
            $person_data = $Data->get_person_data($F->GET['type'], $F->GET['id_admin']);

            if( $F->check_post('SAVE', true) ) {
               require 'user_management' . DS . 'admin_edit_save.php';
            }

            require 'user_management' . DS . 'admin_edit.php';
            break;
         case 'remove':
            $res = $Data->remove_person('ADMIN', $F->GET['id_admin']);
            if( $res ) {
               $Info->add(Lang::_('Remove success'), 'success');
            } else {
               $Info->add(Lang::_( $res), 'error');
            }
            $F->redirect( $F->make_link($F->com, $F->make_get('mode')));
            break;
         default:
            $SP = new SplitPage('ADMIN');
            $persons_list = $Data->get_persons_list($list_param);

            require 'user_management' . DS . 'admin_list_viewer.php';
            break;
      }
      break;
      //###########################
      //CLIENT
   case 'CLIENT':
      $list_param ='CLIENT';
      switch($F->GET['mode']) {
         case 'view_persons':
            //client id
            $SP = new SplitPage('CLIENT_view_persons');
            $id_client = (int)$F->GET['id_client'];
            $persons_list = $Data->get_persons_list($list_param, $id_client);
            require 'user_management' . DS . 'client_person_list.php';
            break;
         case 'edit_person':
            $person_data = $Data->get_person_data($F->GET['type'], $F->GET['id_client_user']);

            if( $F->check_post('SAVE', true) ) {
               require 'user_management' . DS . 'client_person_edit_save.php';
            }

            require 'user_management' . DS . 'client_person_edit.php';
            break;
         case 'add_person':
            if( $F->check_post('SAVE', true) ) {
               require 'user_management' . DS . 'client_person_edit_save.php';
            }

            require 'user_management' . DS . 'client_person_edit.php';
            break;
         case 'add':
            if( $F->check_post('SAVE', true) ) {
               require 'user_management' . DS . 'client_edit_save.php';
            }

            require 'user_management' . DS . 'client_add.php';
            break;
         case 'remove_person':
            $res = $Data->remove_person('CLIENT_USERS', $F->GET['id_client_user']);
            if( $res ) {
               $Info->add(Lang::_('Remove success'), 'success');
            } else {
               $Info->add(Lang::_( $res), 'error');
            }
            $F->redirect( $F->make_link($F->com, $F->add_local_get('mode', 'view_persons', $F->make_get('mode'))));
            break;
         case 'edit':
            $client_data = $Data->get_client_data($F->GET['id_client']);

            if( $F->check_post('SAVE', true) ) {
               require 'user_management' . DS . 'client_edit_save.php';
            }

            require 'user_management' . DS . 'client_edit.php';
            break;
         case 'remove':
            $res = $Data->remove_person('CLIENT', $F->GET['id_client']);
            if( $res ) {
               $Info->add(Lang::_('Remove success'), 'success');
            } else {
               $Info->add(Lang::_( $res), 'error');
            }
            $F->redirect( $F->make_link($F->com, $F->make_get('mode')));
            break;
         default:
            $SP = new SplitPage('CLIENT');
            $clients_list = $Data->get_client_list();

            require 'user_management' . DS . 'client_list_viewer.php';
            break;
      }
      break;
   case 'CLIENT_USER':
      $list_param ='CLIENT';
      switch($F->GET['mode']) {
         case 'add_person':

            if( $F->check_post('SAVE', true) ) {
               require 'user_management' . DS . 'client_person_edit_save.php';
            }

            require 'user_management' . DS . 'client_person_add.php';
            break;
         case 'edit_person':
            $person_data = $Data->get_person_data($list_param, $F->GET['id_client_user']);

            if( $F->check_post('SAVE', true) ) {
               require 'user_management' . DS . 'client_person_edit_save.php';
            }

            require 'user_management' . DS . 'client_person_edit.php';
            break;
         case 'remove_person':
            $res = $Data->remove_person('CLIENT_USERS', $F->GET['id_client_user']);
            if( $res ) {
               $Info->add(Lang::_('Remove success'), 'success');
            } else {
               $Info->add(Lang::_( $res), 'error');
            }
            $F->redirect( $F->make_link($F->com, $F->make_get('mode')));
            break;
         default:
            //client id
            $SP = new SplitPage('CLIENT_USERS');
            $persons_list = $Data->get_persons_list($list_param);
            require 'user_management' . DS . 'client_person_list.php';
            break;
      }
      break;
   default:
      die();
      break;
}
?>