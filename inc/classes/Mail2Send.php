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
         self::$class = new Mail2Send();
      }
      return self::$class;
   }
   
   public static function to_set_email($email_data) {
   	$P = Person::g_global();
   	$F = Framework::g_global();
   	$Lang = Lang::g_global();
   	
   	if( $F->not_null($email_data) &&  $F->not_null($email_data['to_email']) ) {
   		$mail = new Mail();
   		$mail->AddAddress($email_data['to_email']);
   		$mail->Subject = $F->not_null($email_data['subject'])?$email_data['subject']:'SUBJECT'.time();
   		$mail->Body = $F->not_null($email_data['body'])?$email_data['body']:'BODY'.time();
   		return $mail->SendAddSubject();
   	}  
   	return false;
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
         $mail = new Mail();
         
         $email_body = $Lang->get_translation_load('LONG_EMAIL_TO_CUSTOMER');
         $mail->Subject = $Lang->get_translation_load('LONG_EMAIL_TO_CUSTOMER_SUBJECT');
         $array_rep = array('data' => $F->get_current_datetime(), 'id_order' => $id_order);

         $mail->AddAddress($client_email);
         $mail->Body = self::email_body_replace($email_body, $array_rep);
         $mail->SendAddSubject();
      }
   } 
   
   public static function to_global_contact( $post_data ) {
      $P = Person::g_global();
      $F = Framework::g_global();
      $Lang = Lang::g_global();
      $account_manager_data = $P->get_account_manager_address();

      if( $F->not_null($account_manager_data['email']) ) {
         $mail = new Mail();

         if ( $P->logged_in ) {
            $email_body = $Lang->get_translation_load('LONG_EMAIL_CONTACT_LOGIN');
            $mail->Subject = $Lang->get_translation_load('LONG_EMAIL_CONTACT_LOGIN_SUBJECT');
            $client_data = $P->get_client_data();
            $array_ad = array('client_name' => $client_data['name'],
                  'id_client' => $P->data['id_client'],
                  'id_user_client' => $P->id, 'login' => $P->login);
         } else {
            $array_ad = array();
            $email_body = $Lang->get_translation_load('LONG_EMAIL_CONTACT_LOGOUT');
            $mail->Subject = $Lang->get_translation_load('LONG_EMAIL_CONTACT_LOGOUT_SUBJECT');
         }
          
         $array_rep = array('data' => $F->get_current_datetime(),
               'cf_name' => $post_data['cf_name'], 'cf_email' => $post_data['cf_email'],
               'cf_telephone' => $post_data['cf_telephone'],
               'cf_second_telephone' => $post_data['cf_second_telephone'], 'cf_contents' => $post_data['cf_contents'],
               'ip_address' => $_SERVER['REMOTE_ADDR'], 'browser' => $_SERVER['HTTP_USER_AGENT'] );
         $array_rep = array_merge($array_rep, $array_ad);

         $mail->AddAddress($account_manager_data['email'], $account_manager_data['name']);
         $mail->Body = self::email_body_replace($email_body, $array_rep);
         $mail->SendAddSubject();
      }
   }
   



   public static function to_client_register_confirmation( $data, $SOAP = false ) {
   	$P = Person::g_global();
   	$F = Framework::g_global();
   	$Lang = Lang::g_global();
   	
   	$client_data = Data::get_client_data((int)$data['id_client']);
   	$client_user_data = Data::get_client_user_data((int)$data['id_client_user']);

   	add_to_fp('to_client_register_confirmation $client_data:'. print_r($client_data, true) );
   	add_to_fp('to_client_register_confirmation $$client_user_data:'. print_r($client_user_data, true) );
   	if( $F->not_null($client_data) && $F->not_null($client_user_data) &&
         $data['id_client'] == $client_user_data['id_client'] ) {
   		
   		$mail = new Mail();
   		 
   		$email_body = $Lang->get_translation_load('LONG_EMAIL_REGISTER_CLIENT_CONFIRMATION');
   		$mail->Subject = $Lang->get_translation_load('LONG_EMAIL_REGISTER_CLIENT_CONFIRMATION_SUBJECT');
   		 
   		$array_rep = array('data' => $F->get_current_datetime(),
   				'id_client' => $client_data['id_client'], 
   				'c_name' => $client_data['name'], 
   				'c_email' => $client_data['email'],
   				'id_client_user' => $client_user_data['id_client_user'],
   				'cu_name' => $client_user_data['name'],
   				'cu_lname' => $client_user_data['login'],
   				'cu_email' => $client_user_data['email'],
   				'cu_phone' => $client_user_data['phone']);
   		 
   		$mail->AddAddress($client_data['email'], $client_data['name']);
   		if( $client_data['email'] != $client_user_data['email'] ) {
   			$mail->AddCC($client_user_data['email'], $client_user_data['name']);
   		}
   		
   		$mail->AddBCC('michal.sokolowski@2m.net.pl');
   		$mail->AddBCC('konrad.iwan@2m.net.pl');
   		$mail->Body = self::email_body_replace($email_body, $array_rep);
   		$res1 =  $mail->SendAddSubject();
			
   		if( !$SOAP ) return $res1;
   		else {
   		return array('id' => (int)$data['id_client'],
   			'additional_data' => 'id_client_user:'.(int)$data['id_client_user'],
   			'status'  => ($res1?'SUCCESS':'ERROR'));
   		}
   	}
   	
   	if( !$SOAP ) return false;
   	else {
   		return array('id' => (int)$data['id_client'],
   				'additional_data' => 'id_client_user:'.(int)$data['id_client_user'],
   				'status'  => 'ERROR');
   	}
   }
   


   public static function to_client_user_register_confirmation( $data ) {
   	$P = Person::g_global();
   	$F = Framework::g_global();
   	$Lang = Lang::g_global();
   	
   	
   	
   	
   }
   
   public static function to_account_manager_register( $post_data, $client_data ) {
   	$P = Person::g_global();
   	$F = Framework::g_global();
   	$Lang = Lang::g_global();
   	$account_manager_data = $P->get_account_manager_address();

   	if( $F->not_null($account_manager_data['email']) ) {
         $mail = new Mail();
   		
   		$email_body = $Lang->get_translation_load('LONG_EMAIL_REGISTER');
   		$mail->Subject = $Lang->get_translation_load('LONG_EMAIL_REGISTER_SUBJECT');
   
   		$array_rep = array('data' => $F->get_current_datetime(),
   				'rf_name' => $post_data['rf_name'], 'rf_email' => $post_data['rf_email'],
   				'rf_telephone' => $post_data['rf_telephone'],
   				'rf_contents' => $post_data['rf_contents'],
   				'rf_uname' => $post_data['rf_uname'],
   				'rf_ulname' => $post_data['rf_ulname'],
   				'rf_uemail' => $post_data['rf_uemail'],
   				'rf_utelephone' => $post_data['rf_utelephone'],
   				'id_client' => $client_data['id_client'],
   				'id_client_user' => $client_data['id_client_user'],
   				'ip_address' => $_SERVER['REMOTE_ADDR'], 'browser' => $_SERVER['HTTP_USER_AGENT'] );
   
   		$mail->AddAddress($account_manager_data['email'], $account_manager_data['name']);
   		$mail->AddCC($post_data['rf_email'], $post_data['rf_name']);
   		$mail->AddBCC('michal.sokolowski@2m.net.pl');
   		$mail->AddBCC('konrad.iwan@2m.net.pl');
   		$mail->Body = self::email_body_replace($email_body, $array_rep);
   		$res1 =  $mail->SendAddSubject();
   		
   		$res2 = true;
//    		$mail = new Mail();
//    		$mail->AddAddress($post_data['rf_email'], $post_data['rf_name']);
//    		$mail->Body = self::email_body_replace($email_body, $array_rep);
//    		$res2 =  $mail->SendAddSubject();

   		return $res1 && $res2;
   	}
   	return false;
   }
   
   public static function to_account_manager_contact( $post_data ) {
      $P = Person::g_global();
      $F = Framework::g_global();
      $Lang = Lang::g_global();
      $account_manager_data = $P->get_account_manager_address();

      if( $F->not_null($account_manager_data['email']) ) {
      	$mail = new Mail();
      	 
         if ( $P->logged_in ) {
            $email_body = $Lang->get_translation_load('LONG_EMAIL_CONTACT_LOGIN');
            $mail->Subject = $Lang->get_translation_load('LONG_EMAIL_CONTACT_LOGIN_SUBJECT');
            $client_data = $P->get_client_data();
            $array_ad = array('client_name' => $client_data['name'],
                  'id_client' => $P->data['id_client'],
                  'id_user_client' => $P->id, 'login' => $P->login);
         } else {
            $array_ad = array();
            $email_body = $Lang->get_translation_load('LONG_EMAIL_CONTACT_LOGOUT');
            $mail->Subject = $Lang->get_translation_load('LONG_EMAIL_CONTACT_LOGOUT_SUBJECT');
         }
          
         $array_rep = array('data' => $F->get_current_datetime(),
               'cf_name' => $post_data['cf_name'], 'cf_email' => $post_data['cf_email'],
               'cf_telephone' => $post_data['cf_telephone'],
               'cf_second_telephone' => $post_data['cf_second_telephone'], 'cf_contents' => $post_data['cf_contents'],
               'ip_address' => $_SERVER['REMOTE_ADDR'], 'browser' => $_SERVER['HTTP_USER_AGENT'] );
         $array_rep = array_merge($array_rep, $array_ad);

         $mail->AddAddress($account_manager_data['email'], $account_manager_data['name']);
         $mail->Body = self::email_body_replace($email_body, $array_rep);
   		return $mail->SendAddSubject();
   	}
   	return false;
   }

   public static function to_account_manager_order( $id_client, $id_order) {
      $P = Person::g_global();
      $F = Framework::g_global();
      $Lang = Lang::g_global();
      $account_manager_data = $P->get_account_manager_address();

      if( $F->not_null($account_manager_data['email']) ) {
      	$mail = new Mail();
      	
      	$email_body = $Lang->get_translation_load('LONG_EMAIL_TO_ACCOUNT_MANAGER');
         $mail->Subject = $Lang->get_translation_load('LONG_EMAIL_TO_ACCOUNT_MANAGER_SUBJECT');
         $client_data = $P->get_client_data();
         $array_rep = array('data' => $F->get_current_datetime(),
               'id_order' => $id_order, 'client_name' => $client_data['name'],
               'id_client' => $P->data['id_client']);

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