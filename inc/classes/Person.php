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
   static $login, $logged_in, $role, $id;
   static $data;

   function __construct() {
      $this->login = false;
      $this->logged_in = false;
      $this->id = 0;
      $this->roles = array();
      $this->data = array();
      self::$class = $this;
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
      $this->session_id = session_id();
   }

   public function check_person_login($login, $password, $type) {
      $P_data = Data::get_login_data($login, $type);
      if( $P_data ) {
         echo '3';
         $res = gl_check_password($password, $P_data['password']);
         echo '4';
         if( $res ) {
            echo '5';
            $res_rights = Data::get_login_rights($P_data['id_admin'], $type);
            print_r($res_rights);
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

   public function check_pass() {
      //
      echo '#' . $this->id . '#';

   }

   public function function_name() {

   }

}














?>