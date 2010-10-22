<?php
$com = array();




if( $F->check_login(PERSON_TYPE) ) {
//   list($login, $password) = $F->get_login_data(PERSON_TYPE);
   $status = $P->check_person_login($F->POST['lgn_' . PERSON_TYPE], $F->POST['pswrd_' . PERSON_TYPE], PERSON_TYPE);
   if( $status ) {
      //login
      $Page->redirect( $F->make_link('main') );
   } else {
      //error
      $Page->second_head_html;
   }
} else {
   //nothing

}

/* TODO
 * ala ma kota
 */

$Page->head_title = 'TITLE';
$Page->head_keywords = '';
$Page->head_description = '';
$Page->masterhead_html = '<p align="center">PUT MASTHEAD CODE HERE</p>';
$Page->second_head_html = '<p align="center">PUT SECONDARY HEAD CODE HERE</p>';
$Page->component_html = '<p align="center">PUT MAIN COLUMN CODE HERE</p>';
$Page->bottom_html = '<p align="center">PUT BOTTOM ROW CODE HERE</p>';
$Page->footer_html = '<p align="center">PUT FOOTER CODE HERE</p>';


 require 'login_viewer.php';
?>