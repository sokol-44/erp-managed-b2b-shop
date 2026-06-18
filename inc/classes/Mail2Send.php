<?php
/**
 * Mail2Send.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Mail2Send
 * Handles system-wide email dispatching for various events including customer orders,
 * registrations, contact forms, and manager notifications.
 *
 * @designPattern Strategy
 *
 * @todo Convert the class into a proper Dependency Injection wrapper instead of using global singleton state via g_global().
 * @todo Implement strict type hinting (e.g., declare(strict_types=1);) and native type declarations for methods and parameters.
 */
class Mail2Send {
   /**
    * @var Mail2Send|bool Holds the static instance of the Mail2Send class.
    */
   static $class;

   /**
    * Mail2Send constructor.
    *
    * @todo Remove parent::__construct() if this class does not extend any base class, or explicitly declare the inheritance using 'extends'.
    * @todo Refactor away from assigning $this to a static variable inside the constructor.
    */
   function __construct() {
      self::$class = $this;
      parent::__construct();
   }

   /**
    * Retrieves or initializes the global static instance of this class.
    * @return Mail2Send The singleton instance of the Mail2Send class.
    *
    * @todo Deprecate this custom singleton lookup pattern in favor of PSR-11 Container interface or standard dependency injection containers.
    */
   static function g_global() {
      if(self::$class == false) {
         self::$class = new Mail2Send();
      }
      return self::$class;
   }

   /**
    * Sends an email to a specific address based on raw array data.
    * @param array $email_data Array containing keys 'to_email', 'subject' (optional), and 'body' (optional).
    * @return bool True if the email was successfully sent, false otherwise.
    *
    * @todo Refactor the array parameter to use a strongly-typed Value Object or Data Transfer Object (DTO).
    * @todo Replace the custom framework null check '$F->not_null()' with native PHP null coalescing or 'empty()' operations.
    */
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

   /**
    * Sends an order notification email to the administrator.
    * @return void
    *
    * @todo Implement the missing logic for administrator order notifications or remove the dead method placeholder.
    */
   public static function to_admin_order() {

   }

   /**
    * Replaces placeholder tags inside the email body text with actual values.
    * @param string $body The raw email body containing placeholder text.
    * @param array $array Associative array where keys correspond to placeholders and values to replacement strings.
    * @return string The processed email body with placeholders substituted.
    *
    * @todo Utilize a proper templating engine (like Twig or Blade) instead of manual array substitution via str_replace.
    */
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

   /**
    * Sends an order confirmation email directly to the client.
    * @param mixed $id_client The unique identifier of the client.
    * @param mixed $id_order The unique identifier of the order.
    * @return void
    *
    * @todo Add native type hinting for $id_client and $id_order parameters.
    * @todo Abstract the translation layer to use standard PSR-3 logging/PSR-4 translation practices.
    */
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

   /**
    * Sends a general contact form submission email to the assigned account manager.
    * @param array $post_data Array containing user-submitted contact form fields.
    * @return void
    *
    * @todo Avoid directly using the superglobal $_SERVER arrays inside business logic; inject request attributes or use a PSR-7 Request object instead.
    */
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

   /**
    * Sends a client registration confirmation email. Supports standard or SOAP responses.
    * @param array $data Struct containing elements like 'id_client' and 'id_client_user'.
    * @param bool $SOAP Specifies if the response format should comply with internal SOAP expectations. Defaults to false.
    * @return bool|array Standard execution returns boolean state, while SOAP routines return status structures.
    *
    * @todo Replace custom tracing mechanism 'add_to_fp()' with standard PSR-3 Logger interfaces.
    */
   public static function to_client_register_confirmation( $data, $SOAP = false ) {
       $P = Person::g_global();
       $F = Framework::g_global();
       $Lang = Lang::g_global();

       $client_data = Data::get_client_data((int)$data['id_client']);

       if( (int)$data['id_client_user'] > 0 ) {
           $client_user_data = Person::get_client_user_data((int)$data['id_client_user']);
       } elseif( (int)$data['id_client_user'] == -2 ) { // first user (role="admin") to this client
           $client_user_data = Person::get_client_first_client_user_data((int)$data['id_client'], 'ADMIN');
       } elseif( (int)$data['id_client_user'] == -1 ) {
           $client_user_data = false;
       } else {
           $client_user_data = false;
       }

       add_to_fp('to_client_register_confirmation $client_data:'. print_r($client_data, true) );
       add_to_fp('to_client_register_confirmation $client_user_data:'. print_r($client_user_data, true) );
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

           //$mail->AddBCC('#############');
           $mail->Body = self::email_body_replace($email_body, $array_rep);
           $res1 =  $mail->SendAddSubject();

           if( !$SOAP ) return $res1;
           else {
           return array('id' => (int)$data['id_client'],
               'additional_data' => 'id_client_user:'.(int)$data['id_client_user'],
               'status'  => ($res1?'SUCCESS':'ERROR'));
           }
       } elseif( $F->not_null($client_data) && $F->is_null($client_user_data) ) {
           $mail = new Mail();

           $email_body = $Lang->get_translation_load('LONG_EMAIL_REGISTER_CLIENT_ONLY_CONFIRMATION');
           $mail->Subject = $Lang->get_translation_load('LONG_EMAIL_REGISTER_CLIENT_ONLY_CONFIRMATION_SUBJECT');

           $array_rep = array('data' => $F->get_current_datetime(),
                   'id_client' => $client_data['id_client'],
                   'c_name' => $client_data['name'],
                   'c_email' => $client_data['email']);

           $mail->AddAddress($client_data['email'], $client_data['name']);

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

   /**
    * Sends a registration confirmation message explicitly to an individual client user.
    * @param array $data Contains metadata identifiers, primarily 'id_client_user'.
    * @param bool $SOAP True to return an array payload optimized for SOAP context. Defaults to false.
    * @return bool|array Execution output parameter status depending on the context flag.
    *
    * @todo Transition hardcoded system string configurations to global dynamic configurations.
    */
   public static function to_client_user_register_confirmation( $data, $SOAP = false ) {
       $P = Person::g_global();
       $F = Framework::g_global();
       $Lang = Lang::g_global();

       $client_user_data = Person::get_client_user_data((int)$data['id_client_user']);

       add_to_fp('to_client_register_confirmation $client_user_data:'. print_r($client_user_data, true) );
       if( $F->not_null($client_user_data) ) {

           $mail = new Mail();

           $email_body = $Lang->get_translation_load('LONG_EMAIL_REGISTER_CLIENT_USER_CONFIRMATION');
           $mail->Subject = $Lang->get_translation_load('LONG_EMAIL_REGISTER_CLIENT_USER_CONFIRMATION_SUBJECT');

           $array_rep = array('data' => $F->get_current_datetime(),
                   'id_client_user' => $client_user_data['id_client_user'],
                   'cu_name' => $client_user_data['name'],
                   'cu_lname' => $client_user_data['login'],
                   'cu_email' => $client_user_data['email'],
                   'cu_phone' => $client_user_data['phone']);

           $mail->AddAddress($client_user_data['email'], $client_user_data['name']);

           $mail->AddBCC('michal.sokolowski@2m.net.pl');
           $mail->AddBCC('konrad.iwan@2m.net.pl');
           $mail->Body = self::email_body_replace($email_body, $array_rep);
           $res1 =  $mail->SendAddSubject();

           if( !$SOAP ) return $res1;
           else {
               return array('id' => (int)$data['id_client_user'],
                       'additional_data' => '',
                       'status'  => ($res1?'SUCCESS':'ERROR'));
           }
       }

       if( !$SOAP ) return false;
       else {
           return array('id' => (int)$data['id_client_user'],
                   'additional_data' => '',
                   'status'  => 'ERROR');
       }


   }

   /**
    * Alerts an account manager when a new user client transitions registration setup processes.
    * @param array $post_data Transmitted client input array strings.
    * @param array $client_data Extracted processing dataset arrays context.
    * @return bool Returns validation status states for mail processing outputs.
    *
    * @todo Refactor the logical combination logic ($res1 && $res2) since the $res2 logic portion is currently commented out and set blindly to true.
    */
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
//            $mail = new Mail();
//            $mail->AddAddress($post_data['rf_email'], $post_data['rf_name']);
//            $mail->Body = self::email_body_replace($email_body, $array_rep);
//            $res2 =  $mail->SendAddSubject();

           return $res1 && $res2;
       }
       return false;
   }

   /**
    * Forwards targeted submission contents explicitly over to the dedicated context account manager.
    * @param array $post_data System interaction structures arrays mapping input parameters.
    * @return bool True if successfully verified and passed onto the sender mechanisms.
    *
    * @todo Standardize the array keys handling into structured domain objects to prevent runtime index errors.
    */
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

   /**
    * Dispatches order informational summaries straight into processing system account managers.
    * @param mixed $id_client The reference identifier linking back to user structures.
    * @param mixed $id_order Key index reference values targeting relevant data parameters.
    * @return void
    *
    * @todo Enable explicit tracking hooks to monitor notification dispatch reliability rates.
    */
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

   /**
    * Main entry pipeline to trigger order transactional notification routines across both parts.
    * @param mixed $id_client Targeted database primary reference identification indexing parameters.
    * @param mixed $id_order Database mapping reference indexing strings identifying targeted logs.
    * @return void
    *
    * @todo Implement the options check systems explicitly specified inside the standard internal documentation comments.
    * @todo Strip legacy out commented execution segments ('echo 1;') to keep code pipelines clean.
    */
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
