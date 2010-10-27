<?php
$com = array();
/* TODO
 * ala ma kota
 */


$userlist = array();


switch($F->GET['type']) {
   //###########################
   //pictures by hotel
   //HOTEL
   case 'HOTEL':
      $list_param ='HOTEL';
      switch($F->GET['mode']) {
         case 'view_pictures':
            //hotel id
            $SP = new SplitPage('HOTEL_view_pictures');
            $id_hotel = (int)$F->GET['id_hotel'];
            $pictures_list = $Data->get_pictures_list($id_hotel);
            require 'pictures' . DS . 'list.php';
            break;
         case 'edit_picture':
            $picture_data = $Data->get_picture_data($F->GET['id_picture']);

            if( $F->check_post('SAVE', true) ) {
               require 'pictures' . DS . 'save.php';
            }

            require 'pictures' . DS . 'edit.php';
            break;
         case 'add_picture':
            if( $F->check_post('SAVE', true) ) {
               require 'user_management' . DS . 'hotel_person_edit_save.php';
            }

            require 'user_management' . DS . 'hotel_person_edit.php';
            break;
         default:
            $SP = new SplitPage('CLIENT');
            $hotels_list = $Data->get_hotels_list();

            require 'user_management' . DS . 'hotel_list_viewer.php';
            break;
      }
      break;
   case 'PICTURES':
      $list_param ='PICTURES';
      switch($F->GET['mode']) {
         case 'add_picture':

            if( $F->check_post('SAVE', true) ) {
               require 'pictures' . DS . 'save.php';
            }

            require 'pictures' . DS . 'add.php';
            break;
         case 'edit_picture':
            $picture_data = $Data->get_picture_data($F->GET['id_picture']);

            if( $F->check_post('SAVE', true) ) {
               require 'pictures' . DS . 'save.php';
            }

            require 'pictures' . DS . 'edit.php';
            break;
         default:
            //hotel id
            $SP = new SplitPage('PICTURES');
            $pictures_list = $Data->get_pictures_list();
            require 'pictures' . DS . 'list.php';
            break;
      }
      break;
   default:
      die();
      break;
}
?>