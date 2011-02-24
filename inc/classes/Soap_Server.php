<?php
/**
 * Soap_Server.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Soap_Server {

   function Authenticate($login) {

      // Authenticate the user
      //if ($login->username === "a.single.sign.on.user@your.organization.org" && $login->password === "the-password-of-that-user") {
      //return array('Authenticated'=>true);
      // } else {
      return array('Authenticated'=>false);
      // }
   }
    
   function getProductList( $input ) {

      $start = $input->start;
      $end = $input->start+$input->length;
      for($idx = $start; $idx<$end ; $idx++) {
         $res_obj[] = array (
            'id_product' => $idx,
         'name' => '0 name'.$idx,
         'description' => '0 description'.$idx,
         'picture_small_url' => '0 picture_small_url.$idx',
        'picture_big_url' => ' 0 picture_big_url'.$idx,
         'picture_id' => 7+$idx,
         'price' => 8+$idx,
         'vat' => 9+$idx,
         'quantity' => 10+$idx,
         'status' => '0 status' . serialize($input)
         );
      }


      return($res_obj);
   }
    
   function getClientList ( $in ) {
      $start = 1;
      $length = 1;
      if( $in && is_object($in) ) {
         if( $in->start > 0 ) $start = $in->start;
         if( $in->length > 0 ) $length = $in->length;
      }
      return serialize($in);
   }
    
   function doClientAdd ( $input ) {
      // return serialize($input);
      add_to_fp(print_r($input, true));
      
      $response = $this->_fill_response( $input, 'StatusData');
      
      add_to_fp(print_r($response, true));
      
      return $response;
      //		`id_client_in` INT,
      //		`name_in` TINYTEXT,
      //		`description_in` TEXT,
      //		`email_in` TINYTEXT,
      //		`phone_in` TINYTEXT,
      //		`state_in` TINYTEXT
   }
    
   function doClientUserAdd () {

      //      `id_client_user_in` INT,
      //      `id_client_in` INT,
      //      `login_in` TINYTEXT,
      //      `password_in` TINYTEXT,
      //      `password_salt_in` BLOB,
      //      `name_in` TINYTEXT,
      //      `description_in` TEXT,
      //      `email_in` TINYTEXT,
      //      `state_in` TINYTEXT
   }
   
   
   function getParamStartLength( $input ) {
      $in_o = array(
         'start' => 1,
         'length' => 2,
         'options' => 'asasa'
      );
      return $in_o;
   }
   
   function _fill_response( $input, $classname ) {
//      $count = sizeof($input);
      //add_to_fp(print_r($input, true));
      $response = array();
      foreach($input->values as $key => $val) {
         //eval('$response["' . $key . '"] = ' . $classname . '::_fill_response();');
         add_to_fp(print_r($val, true));
         $response[$key] = new $classname($val);
      }
      return $response;
      
      
   }
    
    
   //Client_Add, client_change, client_user_add, client_user_change, client_user_delete, client_user_set_password
}


?>