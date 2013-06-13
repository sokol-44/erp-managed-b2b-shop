<?php
/**
 * Person.php Class for any person in the system (logged in or not)
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/**
 * Person holds and manipulates parameters of current user
 */

class Person {
   static $class;
   var $all;
   static $person_rights_cache = array();
   public $login, $logged_in, $roles, $id, $session_id;
   public $data;

   function __construct() {
      $this->login = false;
      $this->logged_in = false;
      $this->id = 0;
      $this->roles = array();
      $this->data = array();
      self::$class = $this;
      $this->session_id = session_id();
   }

   static function g_global() {
      if(self::$class == false) {
         self::$class = new Person;
      }
      return self::$class;
   }

   function __wakeup() {
      self::$class = $this;
      $this->session_id = session_id();
   }

   function logout() {
      $this->login = false;
      $this->logged_in = false;
      $this->id = 0;
      $this->session_id = session_id();
      $this->roles = array();
      $this->data = array();
      $BackTrail = BackTrail::g_global();
      $BackTrail->reset();
   }

   public function set_person_data(array $P_data, array $P_rights) {

      $this->logged_in = true;

      $this->id = $P_data['id_table'];
      $this->login = $P_data['login'];

      $this->data = $P_data;
      unset($this->data['password']);

      $this->roles = $P_rights;
      natsort( $this->roles );
      $this->session_id = session_id();
   }

   public function check_roles($roles) {

      if( Framework::not_null($roles) ) {
         if( !is_array($roles) ) {
            $roles = explode(',', $roles);
         }

         foreach($roles as $role) {
            if( array_search($role, $this->roles) ) {
               return true;
            }
         }
      }

      return false;
   }

   public function update_person_password($old_password, $new_password, $type) {
      if( $this->logged_in ) {
         $P_data = Data::get_login_data($this->login, $type);
         $res = gl_check_password($old_password, $P_data['password']);
         if( $res ) {
            list($pw,$salt) = explode(':', $P_data['password']);
            $data = compact('old_password', 'new_password', 'salt');
            Data::update_person_password( $this->id, $type, $data);
            return true;
         } else {
            return false;
         }
      } else {
         return false;
      }
   }

   public function check_person_login($login, $password, $type) {
      $P_data = Data::get_login_data($login, $type);
      if( $P_data ) {
         $res = gl_check_password($password, $P_data['password']);
         if( $res ) {
            $res_rights = Data::get_login_rights($P_data['id_table'], $type);
            Data::set_person_last_login($P_data['id_table'], $type);
            $this->set_person_data($P_data, $res_rights);
            return true;
         } else {
            return false;
         }
      } else {
         $res = gl_check_password($password, ':');
         return false;
      }

   }

   public function check_session_admin_login() {
      if($this->logged_in && isset($this->roles['2PANEL'])
      && (isset($this->roles['SUPER_ADMIN']) || isset($this->roles['ADMIN']))
      ) {
         return true;
      } else {
         return false;
      }
   }
    
   public function get_account_manager_address( $id_client = false ) {

      //TODO add field and data to client: account_manager
      //Data::get_account_manager_address( (int)$this->data['id_client'] )
      $cfg_mail = $GLOBALS['config']['MAIL'];

      if( $id_client === true ) {
         if( is_numeric($id_client) ) {
            $email_addres = Data_Person::get_client_attribute((int)$id_client, 'ACCOUNT_MANAGER_ADDRESS');
         } elseif ( is_bool($id_client) ) {
            $email_addres = Data_Person::get_client_attribute((int)$P->data['id_client'], 'ACCOUNT_MANAGER_ADDRESS');
         }
         if( $email_address ) return array('email' => $email_address,'name' =>  '');
      }
      
      if( Framework::not_null($cfg_mail['default_to_address']) ) {
         return array('email' => $cfg_mail['default_to_address'], 'name' => $cfg_mail['default_to_name']);
      } elseif( Framework::not_null($cfg_mail['main_from_address']) ) {
         return array('email' => $cfg_mail['main_from_address'], 'name' => $cfg_mail['main_from_name']);
      } else {
         return array('email' => '','name' =>  '');
      }
   }

   public function get_client_email_address( $id_client = false ) {
      if( $id_client === false ) $id_client = (int)$this->data['id_client'];
      $client_data = Data::get_client_data( (int)$id_client );
      return $client_data['email'];
   }

   public function get_client_data( $id_client = false ) {
      if( $id_client === false ) $id_client = (int)$this->data['id_client'];
      $client_data = Data::get_client_data( (int)$id_client );
      return $client_data;
   }

   static public function get_client_user_data( $id, $table = 'CLIENT') {
      //'CLIENT', 'ADMIN'
      $idx=$id.'.'.$table;
      if( isset(self::$person_rights_cache[$idx]) ) $client_client_data = $person_rights_cache[$idx];
      else {
         $client_client_data = Data::get_person_data( $table, $id );
         self::$person_rights_cache[$idx] = $client_client_data;
      }

      return $client_client_data;
   }

   public function check_pass() {
      //
      echo '#' . $this->id . '#';

   }

   public function function_name() {

   }

}














?>