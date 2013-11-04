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
   public $data, $client_data, $address_list;

   function __construct() {
      $this->login = false;
      $this->logged_in = false;
      $this->id = 0;
      $this->roles = array();
      $this->data = array();
      $this->client_data = array();
      $this->address_list = array();
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
      $this->client_data = array();
      $this->address_list = array();
      $BackTrail = BackTrail::g_global();
      $BackTrail->reset();
      session_regenerate_id();
   }

   public function get_public_data() {
      
      $ar_flt = array('id_client_user' => '', 'id_client' => '', 'name' => '', 'description' => '',
                      'login' => '', 'email' => '', 'phone' => '', 'phone_cell' => '');
      //array_intersect_key
      return array(
           'logged_in' => $this->logged_in,
           'id' => $this->id,
           'login' => $this->login,
           'roles' => $this->roles,
           'data' => array_intersect_key($this->data, $ar_flt)
            );
      
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

   function get_client_balance( $id_client = false ) {
   	$F = Framework::g_global();
   	$balance = array('credit_limit' => 0, 'free_credit' => 0, 'punctuality' => '');
/*
 *    	BALANCE_CREDIT_LIMIT
   	BALANCE_FREE_CREDIT
   	BALANCE_PUNCTUALITY
 */
   	
   	if( $F->is_null($id_client) ) {
   		if( $this->logged_in ) {
   			$id_client = (int)$this->data['id_client'];
   		} else {
   			return $balance;
   		}
   	} else {
   		$id_client = (int)$id_client;
   	}
   	
   	$balance = array(
   			'credit_limit' => (float)Data::get_client_attribute($id_client, 'BALANCE_CREDIT_LIMIT'), 
   			'free_credit'  => (float)Data::get_client_attribute($id_client, 'BALANCE_FREE_CREDIT'), 
   			'punctuality' => (string)Data::get_client_attribute($id_client, 'BALANCE_PUNCTUALITY') );

   	return $balance;
   }
    
   public function get_client_email_address( $id_client = false ) {
      if( $id_client === false ) $id_client = (int)$this->data['id_client'];
      
      $client_data = Data::get_client_data( (int)$id_client );
      return $client_data['email'];
   }

   public function get_client_data( $id_client = false ) {
      if( $id_client === false ) $id_client = (int)$this->data['id_client'];
      else return Data::get_client_data( (int)$id_client );
      
      if( Framework::is_null($this->client_data) ) $this->client_data = Data::get_client_data( (int)$id_client );
      return $this->client_data;
   }
   
   
   public function get_client_address_list( $id_client = false ) {
   	if( $id_client === false ) $id_client = (int)$this->data['id_client'];
   	return $this->get_address_list($id_client);
   }
   
   public function get_address_list( $id_client = false, $id_client_user = false ) {

   	if( $id_client === false ) {
      	$id_client = (int)$this->data['id_client'];
      	$id_client_user = (int)$this->id;
      } else {
      	return Data::get_address_list( (int)$id_client );
      }

      $this->address_list = Data::get_address_list( (int)$id_client, (int)$id_client_user );
      return $this->address_list;
   }
   
   public function get_order_address_list() {
   	$id_client = (int)$this->data['id_client'];
      $id_client_user = (int)$this->id;
 	
      $params = array('id_client' => $id_client, 'id_client_user' => $id_client_user);
      
   	$addres_list_tmp = array();
   	if( defined('SHOP_BASKET_ORDER_ADDRESS_ADD') && constant('SHOP_BASKET_ORDER_ADDRESS_ADD') == 'true' ) {
   		$adr = Data::additional_addreses('SHOP_BASKET_ORDER_ADDRESS_ADD', $params);
   		$addres_list_tmp[$adr['id_address']] = $adr;
   	}
   	if( defined('SHOP_BASKET_ORDER_ADDRESS_PERSONAL_COLLECTION') && 
   		constant('SHOP_BASKET_ORDER_ADDRESS_PERSONAL_COLLECTION') == 'true' ) {
   		$adr = Data::additional_addreses('SHOP_BASKET_ORDER_ADDRESS_PERSONAL_COLLECTION', $params);
   		$addres_list_tmp[$adr['id_address']] = $adr;
   	}
   	return array_merge($addres_list_tmp, Data::get_address_list( (int)$id_client ));
   }
   
   static public function get_address( $id_address ) {
   	return Data::get_address( (int)$id_address );
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