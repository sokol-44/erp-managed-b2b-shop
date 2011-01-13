<?php
/**
 * Mail2Send.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/**
 * Send email from system
 */


class Mail2Send {
   static $class;

   function __construct() {
      self::$class = $this;
      parent::__construct();
   }

   static function g_global() {
      if(self::$class == false) {
         self::$class = new Price();
      }
      return self::$class;
   }
    
   public static function to_admin_order() {

   }

    
   public static function email_body_replace($body, $array) {

      $search = array();
      $replace = array();

      foreach($array as $key => $val ) {
         $search[] = '#' . $key . '#';
         $replace[] = $val;
      }
      //$search = array_keys( $array );
      //$replace = array_values( $array );
      return str_replace($search, $replace, $body);
   }
    
   public static function to_client_order( $id_client, $id_order) {
      $P = Person::g_global();
      $F = Framework::g_global();
      $Lang = Lang::g_global();
      $client_email = $P->get_client_email_address();
      
      if( $F->not_null($client_email) ) {
         $email_body = $Lang->get_translation_load('LONG_EMAIL_TO_CUSTOMER');
         $array_rep = array('data' => $F->get_current_datetime(), 'id_order' => $id_order);
          
         $mail = new Mail();
         $mail->AddAddress($client_email);
         $mail->Body = self::email_body_replace($email_body, $array_rep);
         $mail->SendAddSubject();
      }
   }
    
   public static function to_account_manager_order( $id_client, $id_order) {
      $P = Person::g_global();
      $F = Framework::g_global();
      $Lang = Lang::g_global();
      $account_manager_data = $P->get_account_manager_address();
      
      if( $F->not_null($account_manager_data['email']) ) {
         $email_body = $Lang->get_translation_load('LONG_EMAIL_TO_ACCOUNT_MANAGER');
         $client_data = $P->get_client_data();
         $array_rep = array('data' => $F->get_current_datetime(),
         	'id_order' => $id_order, 'client_name' => $client_data['name'],
         	'id_client' => $P->data['id_client']);

         $mail = new Mail();
         $mail->AddAddress($account_manager_data['email'], $account_manager_data['name']);
         $mail->Body = self::email_body_replace($email_body, $array_rep);
         $mail->SendAddSubject();
      }
   }
   
   static function order( $id_client, $id_order) {
      //echo '1';
      //to admin
      //TODO options in account manager to recive so
      self::to_account_manager_order( $id_client, $id_order);
      //echo '2<br><br>';
      //to client - confirmation of order
      //TODO options in client to recive so
      self::to_client_order( $id_client, $id_order);

   }


}

?>