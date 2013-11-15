<?php
/**
 * Data_Person.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/**
 * Data class goint to provide data and operation on them
 * Application will get and save any data thru it
 */
/**
 * @author ms
 *
 */

/*
 * client attributes
 * 
      PRODUCT_VIEW_NAME
 * 
      ACCOUNT_MANAGER_ADDRESS
 * 
   	BALANCE_CREDIT_LIMIT
   	BALANCE_FREE_CREDIT
   	BALANCE_PUNCTUALITY
 */

class Data_Person extends Data_Rights {

   function __construct() {
      parent::__construct();
   }

   static function additional_addreses($type, $params) {
   
   	$ids = array('SHOP_BASKET_ORDER_ADDRESS_ADD' => -1,
   			'SHOP_BASKET_ORDER_ADDRESS_PERSONAL_COLLECTION' => -2,
   			'SHOP_BASKET_ORDER_ADDRESS_DEFAULT' => -3);
   
   	$id_client = (int) $params['id_client'];
   	$id_client_user = (int) $params['id_client_user'];
   
   	$addreses = array(
   			-1 => array('id_address' => -1,
   					'id_client' => (int)$id_client, 'id_client_user' => (int)$id_client_user,
   					'description' => Lang::_('address write in'),
   					'name' => Lang::_('write in description field')
   			),
   			-2 => array('id_address' => -2,
   					'id_client' => (int)$id_client, 'id_client_user' => (int)$id_client_user,
   					'description' => Lang::_('personal collection'),
   					'name' => Lang::_('write proposition in description field')
   			),
   			-3 => array('id_address' => -3,
   					'id_client' => (int)$id_client, 'id_client_user' => (int)$id_client_user,
   					'description' => Lang::_('default address'),
   					'name' => ''
   			)
   	);
   
   	if( isset($ids[$type]) ) return $addreses[$ids[$type]];
   	elseif( array_search($type, $ids) ) return $addreses[$type];
   	else return array();
   }   
   
   static function additional_account_manager($type, $params) {
   
   	$ids = array(
   			'SHOP_ACCOUNT_MANAGER_DEFAULT' => -3);
   
   	$id_client = (int) $params['id_client'];
   	$id_client_user = (int) $params['id_client_user'];
   
   	$account_managers = array(
   			-3 => array('id_account_manager' => -3,
   					'id_client' => (int)$id_client, 'id_client_user' => (int)$id_client_user,
   					'account_manager_name' => '000',
   					'fullname' => Lang::_('default_account_manager')
   			)
   	);
   
   	if( isset($ids[$type]) ) return $account_managers[$ids[$type]];
   	elseif( array_search($type, $ids) ) return $account_managers[$type];
   	else return array();
   }

   static function remove_person($table, $id_in) {

      $id = (int)$id_in;

      if( $id != $id_in ) return 'ID_ERROR';

      switch($table) {
         case 'ADMIN':
            return db_query('update ' . TBL_GLOBAL_ADMIN . " set state = 'ERASED' where id_admin=" . $id);
            break;
         case 'CLIENT_USERS':
            return db_query('update ' . TBL_GLOBAL_CLIENT_USER . " set state = 'ERASED' where id_client_user=" . $id);
            break;
         case 'CLIENT':
            return (
            db_query('update ' . TBL_GLOBAL_CLIENT . " set state = 'ERASED' where id_client=" . $id) &&
            db_query('update ' . TBL_GLOBAL_CLIENT_USER . " set state = 'ERASED' where id_client=" . $id) );
            break;
         default:
            return 'TABLE_ERROR';
            break;
      }
   }
    
   static function update_person_password($id, $table, $data) {
      $db_new_password =  gl_make_password($data['new_password'], $data['salt'], true);
      
      if( $table == 'ADMIN' ) {
         $sql='UPDATE ' . TBL_GLOBAL_ADMIN . ' set password = "' . db_escape($db_new_password) . '"
         where id_admin = ' . db_int($id);
         db_query($sql);
      } else if( $table == 'CLIENT' ) {
         $sql='UPDATE ' . TBL_GLOBAL_CLIENT_USER . ' set password = "' . db_escape($db_new_password) . '"
         where id_client_user = ' . db_int($id);
         db_query($sql);
      }
   }
    
   static function set_person_last_login($id, $table) {

      if( $table == 'ADMIN' ) {
         $sql='UPDATE ' . TBL_GLOBAL_ADMIN . ' set last_login = now()
         where id_admin = ' . db_int($id);
         db_query($sql);
      } else if( $table == 'CLIENT' ) {
         $sql='UPDATE ' . TBL_GLOBAL_CLIENT_USER . ' set last_login = now()
         where id_client_user = ' . db_int($id);
         db_query($sql);
      }
   }
         	
   static function insert_person_data($table, $data) {
      $F = Framework::g_global();
      $data_main_sql['login'] =  $data['login'];
      $data_main_sql['description'] =  $data['description'];
      $data_main_sql['email'] =  $data['email'];
      $data_main_sql['state'] =  $data['state'];
      $data_main_sql['created'] =  'now()';

      //      print_r($data_main_sql);
      //      die();

      $data_main_sql['password'] =  gl_make_password($data['new_password']);

      if( $table == 'ADMIN' ) {
         db_transaction_start();
         db_perform(TBL_GLOBAL_ADMIN, $data_main_sql, 'INSERT');
         $id_admin = db_insert_id();
         foreach($data['rights_ids'] as $id_rights) {
            db_query("insert into " . TBL_GLOBAL_RIGHTS2ADMIN . ' (id_admin, id_rights) values (' . (int)$id_admin . ', ' . (int)$id_rights . ')');
         }
         db_transaction_end();
      } else if( $table == 'CLIENT' ) {
         $data_main_sql['name'] =  $data['name'];
         $data_main_sql['id_client'] =  $data['id_client'];
         db_transaction_start();
         db_perform(TBL_GLOBAL_CLIENT_USER, $data_main_sql, 'INSERT');
         $id_client_user = db_insert_id();
         foreach($data['rights_ids'] as $id_rights) {
            db_query("insert into " . TBL_GLOBAL_RIGHTS2CLIENT . ' (id_client_user, id_rights) values (' . (int)$id_client_user . ', ' . (int)$id_rights . ')');
         }
         db_transaction_end();
      }
   }

   static function update_person_data($table, $id, $data, $data_org) {
      $F = Framework::g_global();
      $data_main_sql['login'] =  $data['login'];
      $data_main_sql['description'] =  $data['description'];
      $data_main_sql['email'] =  $data['email'];
      $data_main_sql['state'] =  $data['state'];

      //      print_r($data_main_sql);
      //      die();

      if( isset($data['new_password']) )
      $data_main_sql['password'] =  gl_make_password($data['new_password'], $data_org['password']);

      if( $table == 'ADMIN' ) {
         db_perform(TBL_GLOBAL_ADMIN, $data_main_sql, 'UPDATE', "id_admin=" . (int)$data_org['id_admin']);
         db_query("delete from " . TBL_GLOBAL_RIGHTS2ADMIN . ' where id_admin=' . (int)$data_org['id_admin']);
         foreach($data['rights_ids'] as $id_rights) {
            db_query("insert into " . TBL_GLOBAL_RIGHTS2ADMIN . ' (id_admin, id_rights) values (' . (int)$data_org['id_admin'] . ', ' . (int)$id_rights . ')');
         }
      } else if( $table == 'CLIENT' ) {
         $data_main_sql['name'] =  $data['name'];
         db_perform(TBL_GLOBAL_CLIENT_USER, $data_main_sql, 'UPDATE', "id_client_user=" . (int)$data_org['id_client_user']);
         db_query("delete from " . TBL_GLOBAL_RIGHTS2CLIENT . ' where id_client_user=' . (int)$data_org['id_client_user']);
         foreach($data['rights_ids'] as $id_rights) {
            db_query("insert into " . TBL_GLOBAL_RIGHTS2CLIENT . ' (id_client_user, id_rights) values (' . (int)$data_org['id_client_user'] . ', ' . (int)$id_rights . ')');
         }
      }
   }


   static function get_person_data($table, $id) {
      //      $F = Framework::g_global();

      if( $table == 'ADMIN' ) {
         $res = db_query('select distinct p.id_admin, p.login, p.password, p.description, p.email, p.created, p.last_login, p.state, GROUP_CONCAT(r.name) as rights_list, GROUP_CONCAT(r.id_rights) as rights_ids
         	from ' . TBL_GLOBAL_ADMIN . ' p,
         	' . TBL_GLOBAL_RIGHTS2ADMIN . ' gl,
         	' . TBL_GLOBAL_RIGHTS . ' r ' .
      		' where p.id_admin = gl.id_admin and gl.id_rights = r.id_rights and p.id_admin = ' . (int)$id );
         return db_fetch_array($res);
      } else if( $table == 'CLIENT' ) {
         $res = db_query('select distinct p.id_client_user, p.login, p.password, p.name, p.description, p.email, p.created, p.last_login, p.state, GROUP_CONCAT(r.name) as rights_list, GROUP_CONCAT(r.id_rights) as rights_ids
         	from ' . TBL_GLOBAL_CLIENT_USER . ' p,  ' . TBL_GLOBAL_RIGHTS2CLIENT . ' gl,  ' . TBL_GLOBAL_RIGHTS . ' r ' .
      		'where p.id_client_user = gl.id_client_user and gl.id_rights = r.id_rights and p.id_client_user = ' . (int)$id );
         return db_fetch_array($res);
      }
      
      return array();
   }

   static function get_person_account_state($type) {
      return array(
      array('name' => 'ACTIVE',
         'description' => 'ACTIVE'),
      array('name' => 'BLOCKED',
         'description' => 'BLOCKED'),
      array('name' => 'SUSPENDED',
         'description' => 'SUSPENDED'),
      array('name' => 'ERASED',
         'description' => 'ERASED')
      );
   }

   static function get_persons_list($table = '', $id = 0, $type) {
      $F = Framework::g_global();
      $SP = SplitPage::g_global();
      //FIXME
      //include type

      if( $table != 'ADMIN' && $table != 'CLIENT' ) die('get_persons_list');

      if( $table == 'ADMIN' ) {
         $tbl_person = constant('TBL_GLOBAL_ADMIN');
      } elseif( $table == 'CLIENT' ) {
         $tbl_person = constant('TBL_GLOBAL_CLIENT_USER');
      } else return false;

      if( defined('TBL_GLOBAL_RIGHTS2' . $table) ) $tbl_glue = constant('TBL_GLOBAL_RIGHTS2' . $table);
      else return false;

      if( defined('TBL_GLOBAL_RIGHTS') ) $tbl_rights = constant('TBL_GLOBAL_RIGHTS');
      else return false;

      if( $table == 'ADMIN' ) {
         $query = 'select distinct p.id_admin, p.login, p.description, p.email, p.state, p.created, p.last_login, GROUP_CONCAT(r.name) as rights_list  from ' .
         $tbl_person . ' p,  ' . $tbl_glue . ' gl,  ' . $tbl_rights . ' r ' .
      		'where p.id_admin = gl.id_admin and gl.id_rights = r.id_rights  group by (p.id_admin)';
         $sp_query = $SP->prepare_sql( $query );
         $res = db_query( $sp_query );
      } else if( $table == 'CLIENT' ) {
         $table_id = 'id_client_user';
         if( $id > 0) $client_where = ' and p.id_client = ' . (int)$id;
         $query = 'select distinct p.id_client_user, p.login, p.description, p.email, p.state, p.created, p.last_login, GROUP_CONCAT(r.name) as rights_list  from ' .
            '' . $tbl_person . ' p,  ' . $tbl_glue . ' gl,  ' . $tbl_rights . ' r ' .
      		'where p.id_client_user = gl.id_client_user and gl.id_rights = r.id_rights ' . $client_where . ' group by (p.id_client_user)';
         $sp_query = $SP->prepare_sql( $query );
         $res = db_query( $sp_query );
      }



      return db_result_array($res);
   }

   static function doClientUserDelete($param_array) {
       
      extract( $param_array );
   
      $query = 'select "' . db_int($id_client) . '" as id_one, "' . db_int($id_client_user) . '" as id_two,
          "" as additional_data,
          b_func_client_user_delete("' . db_int($id_client_user) . '", "' . db_int($id_client) . '") as status';
   
      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }
   
   static function doClientUserSetPassword($param_array) {
   
      extract( $param_array );

      $query = 'select "' . db_int($id_client) . '" as id_one, "' . db_int($id_client_user) . '" as id_two,
          "" as additional_data,
          b_func_client_user_set_password("' . db_int($id_client_user) . '", "' . db_int($id_client) . '",
          "' . db_escape($password) . '",  "' . db_escape($password_salt) . '") as status';

      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }
   
   static function doClientUserAdd($param_array, $add  = false) {
      
      extract( $param_array );
      
      if( $add ) {
         $query = 'select "' . db_int($id_client) . '" as id_one, "' . db_int($id_client_user) . '" as id_two,
          "" as additional_data,
          b_func_client_user_add("' . db_int($id_client_user) . '", "' . db_int($id_client) . '", "' . db_escape($login) . '",
          "' . db_escape($description) . '", "' . db_escape($name) . '",
          "' . db_escape($email) . '", "' . db_escape($phone) . '", "' . db_escape($phone_cell) . '", "' . db_escape($state) . '") as status';
      } else {
         $query = 'select "' . db_int($id_client) . '" as id_one, "' . db_int($id_client_user) . '" as id_two,
          "" as additional_data,
          b_func_client_user_change("' . db_int($id_client_user) . '", "' . db_int($id_client) . '", "' . db_escape($login) . '",
          "' . db_escape($description) . '", "' . db_escape($name) . '",
          "' . db_escape($email) . '", "' . db_escape($phone) . '", "' . db_escape($phone_cell) . '", "' . db_escape($state) . '") as status';
      }
      add_to_fp($query);
      $result = db_query( db_fetch_array($query) );
      
      if( $password!='' && substr_count($password, ':') < 1 ) {
         $res_pass = doClientUserSetPassword($param_array);
         $result['additional_data'] = 'ClientUserSetPassword:' . $res_pass['status'];
      }
      return $result;
   }
   
   static function doClientUserAddressAddOrUpdate($param_array) {
   	 
   	extract( $param_array );
   	
   	$query = 'select "' . db_int($id_client_user) . '" as id_one, "' . db_int($id_address) . '" as id_two,
          "" as additional_data,
          b_func_client_user_set_address("' . db_int($id_address) . '", "' . db_int($id_client_user) . '", "' . db_int($id_client) . '",
          "' . db_escape($description) . '", "' . db_escape($name) . '",  "' . db_escape($street) . '",
          "' . db_escape($city) . '", "' . db_escape($zip_code) . '",  "' . db_escape($country) . '") as status';
   	
   	add_to_fp($query);
   	$result = db_query( $query );
   	return db_fetch_array($result);
   }
   
   static function doClientUserAddressDelete($param_array) {
   	 
   	extract( $param_array );
   	 
   	$query = 'select "' . db_int($id_client_user) . '" as id_one, "' . db_int($id_address) . '" as id_two,
          "' . db_escape('id_client:',$id_client) . '" as additional_data,
          b_func_client_user_del_address("' . db_int($id_address) . '", "' . db_int($id_client_user) . '",
			 "' . db_int($id_client) . '") as status';
   
   	add_to_fp($query);
   	$result = db_query( $query );
   	return db_fetch_array($result);
   }
   
   static function getClientUserAddressList($id_client_user, $id_address, $length) {
   	list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);
   	
   	$query = 'select id_address, id_client, id_client_user, description, name, street, city, zip_code, country
   				from ' . TBL_GLOBAL_CLIENT_USER_ADDRESS . '
					where id_client_user = "' . db_int($id_client_user) . '" 
				   AND id_address ' . $comparision_dir . db_int($id_address) . '
				   ORDER BY id_address ' . $order_dir . ' LIMIT '. db_int($length);

   	add_to_fp($query);
   	$result = db_query( $query );
   	return db_result_array_full($result);
   }

   static function getClientAddressList($id_client, $id_address, $length) {
   	list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);
   
   	$query = 'select id_address, id_client, id_client_user, description, name, street, city, zip_code, country
   				from ' . TBL_GLOBAL_CLIENT_USER_ADDRESS . '
					where id_client = "' . db_int($id_client) . '"
				   AND id_address ' . $comparision_dir . db_int($id_address) . '
				   ORDER BY id_address ' . $order_dir . ' LIMIT ' . db_int($length);

   	add_to_fp($query);
   	$result = db_query( $query );
   	return db_result_array_full($result);
   }
      
   static function getAddressList($id_address, $length) {
   	list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);
   
   	$query = 'select id_address, id_client, id_client_user, description, name, street, city, zip_code, country
   				from ' . TBL_GLOBAL_CLIENT_USER_ADDRESS . '
					where id_address ' . $comparision_dir . db_int($id_address) . '
				   ORDER BY id_address ' . $order_dir . ' LIMIT '. db_int($length);

   	add_to_fp($query);
   	$result = db_query( $query );
   	return db_result_array_full($result);
   }
   /*
    * new end
    */
   static function doClientAdd($param_array, $add  = false) {
      
      extract( $param_array );
      
      if( $add ) {
         $query = 'select "' . db_int($id_client) . '" as id_one,
          "" as additional_data,
          b_func_client_add("' . db_int($id_client) . '", "' . db_escape($name) . '", "' . db_escape($description) . '",
          "' . db_escape($email) . '", "' . db_escape($phone) . '", "' . db_escape($state) . '") as status';
      } else {
         $query = 'select "' . db_int($id_client) . '" as id_one,
          "" as additional_data,
          b_func_client_change("' . db_int($id_client) . '", "' . db_escape($name) . '", "' . db_escape($description) . '",
          "' . db_escape($email) . '", "' . db_escape($phone) . '", "' . db_escape($state) . '") as status';
      }
      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }

   static function insert_client_data_id($data) {
      
      $data_sql['id_client'] =  $data['id_client'];
      $data_sql['name'] =  $data['name'];
      $data_sql['description'] =  $data['description'];
      $data_sql['email'] =  $data['email'];
      $data_sql['phone'] =  $data['phone'];
      $data_sql['state'] =  $data['state'];
      
      
      $return = db_call_proc('b_func_client_add', $data_sql);
   }
   
   static function insert_client_data($data) {

      $data_main_sql['name'] =  $data['name'];
      $data_main_sql['description'] =  $data['description'];
      $data_main_sql['email'] =  $data['email'];
      $data_main_sql['phone'] =  $data['phone'];
      $data_main_sql['state'] =  $data['state'];
      
      db_transaction_start();
      db_perform(TBL_GLOBAL_CLIENT, $data_main_sql, 'INSERT');
      $id_client = db_insert_id();
      db_transaction_end();

      return $id_client;
   }

   static function update_client_data($id, $data, $data_org) {
      $F = Framework::g_global();
      $data_main_sql['name'] =  $data['name'];
      $data_main_sql['description'] =  $data['description'];
      $data_main_sql['email'] =  $data['email'];
      $data_main_sql['phone'] =  $data['phone'];
      $data_main_sql['state'] =  $data['state'];


      db_perform(TBL_GLOBAL_CLIENT, $data_main_sql, 'UPDATE', "id_client=" . (int)$data_org['id_client']);
   }

   static function get_client_attribute( $id_client, $attribute_type = false) {
      $F = Framework::g_global();
		
   	$where_add = '';
   	
   	if( $F->not_null($attribute_type) ) {
   		$where_add = ' and ca.type = "' . db_escape($attribute_type) . '"';
   	}
   	
      $query = 'select ca.`id_client`, `type`, `val`
         	from ' . TBL_GLOBAL_CLIENT_ATTRIBUTES . ' ca ,
         	' . TBL_GLOBAL_CLIENT . ' c
         	where ca.id_client = c.id_client and ca.id_client = "' . db_int($id_client) . '"' . $where_add;
      
      if ( $attribute_type == 'PRODUCT_VIEW_NAME' ) {
         if ( Data_Products::$Data_Products_params['client_view'] ) return Data_Products::$Data_Products_params['client_view'];
         elseif ( defined('SHOP_CLIENT_PRODUCT_PRICE_VIEW') ) return constant('SHOP_CLIENT_PRODUCT_PRICE_VIEW');
         else return false;
      } else {
      	$result = db_query( $query );
      	$nrow = db_rows( $result );
      	if( $nrow == 1 && $F->not_null($attribute_type) ) {
      		return db_fetch_result('val', $result);
      	} elseif( $nrow > 1 ) {
      		return db_result_array_full($result);
      	} else {
      		return false;
      	}
      }
   }
   
   static function get_client_data( $id_client ) {
      $res = db_query('select distinct h.id_client, h.name, h.description, h.email, h.phone, h.created, h.state, count(hu.id_client_user) as count_users
         	from ' . TBL_GLOBAL_CLIENT . ' h ,
         	' . TBL_GLOBAL_CLIENT_USER . ' hu
         	where h.id_client = hu.id_client and h.id_client = ' . db_int($id_client) );

      return db_fetch_array($res);
   }

   static function get_address( $id_address ) {
   
   	$query = 'select id_address, id_client, id_client_user,
   				description, name, street, city, zip_code,country, date_created, date_modified,
   			   UNIX_TIMESTAMP(date_created) as ts_created, UNIX_TIMESTAMP(date_modified) as ts_modified,
   				rights_edit, rights_use
					from ' . TBL_GLOBAL_CLIENT_USER_ADDRESS . ' where id_address = ' . db_int($id_address);
   
   	return db_fetch_array( db_query( $query ) );
   }
      
   static function get_address_list( $id_client, $id_client_user = 0 ) { 
   	if( $id_client_user > 0 ) {
   		$where = ' ( id_client = ' . db_int($id_client) . ' AND rights_use = "CLIENT" ) OR
   				id_client_user = ' . db_int($id_client_user);
   	} else {
   		$where = ' id_client = ' . db_int($id_client);
   	}
   	
   	$query = 'select id_address, id_client, id_client_user,
   				description, name, street, city, zip_code,country, date_created, date_modified,
   			   UNIX_TIMESTAMP(date_created) as ts_created, UNIX_TIMESTAMP(date_modified) as ts_modified,
   				rights_edit, rights_use
					from ' . TBL_GLOBAL_CLIENT_USER_ADDRESS . ' where
					' . $where;
   	
   	return db_result_array_full_id( db_query( $query ) );
   }
      
   static function get_account_manage_list( $id_client, $id_client_user = 0 ) { 
   	if( $id_client_user > 0 ) {
   		$where = ' ( id_client = ' . db_int($id_client) . ' AND rights_use = "CLIENT" ) OR
   				id_client_user = ' . db_int($id_client_user);
   	} else {
   		$where = ' id_client = ' . db_int($id_client);
   	}
   	
   	$query = 'select id_account_manager, id_client, id_client_user, account_manager_name,
   				fullname, phone1, phone2, email, date_created, date_modified,
   			   UNIX_TIMESTAMP(date_created) as ts_created, UNIX_TIMESTAMP(date_modified) as ts_modified,
   				rights_edit, rights_use
					from ' . TBL_GLOBAL_CLIENT_USER_ACCOUNT_MANAGER . ' where
					' . $where;
   	
   	return db_result_array_full_id( db_query( $query ) );
   }  
   
   static function get_client_list() {
      $SP = SplitPage::g_global();

      $query = 'select distinct h.id_client, h.name, h.description, h.email, h.phone, h.created, h.state
         	from ' . TBL_GLOBAL_CLIENT . ' h';
      $sp_query = $SP->prepare_sql( $query );
      $res = db_query($sp_query);
      return db_result_array($res);
   }
   
   
   
   static function getClientUserList( $id_client = 0, $id_client_user_start = 0, $length = 1, $where = '' ) {
      list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);
      
      $query = 'select cu.id_client_user, cu.id_client, cu.name, cu.description,
      cu.login, cu.password, cu.email, cu.created, cu.last_login, cu.state
      from ' . TBL_GLOBAL_CLIENT_USER . ' cu
      where cu.id_client = ' . db_int($id_client) . '
      and  cu.id_client_user ' . $comparision_dir . db_int($id_client_user_start) . $where . '
      ORDER BY cu.id_client_user ' . $order_dir . ' LIMIT '. db_int($length);

      $result = db_query( $query );
      return db_result_array_full($result);
   }
   
   static function getClientList( $id_client_start = 0, $length = 1, $where = '' ) {
      list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);

      $query = 'select h.id_client, h.name, h.description, h.email, h.phone, h.created, h.state
       from ' . TBL_GLOBAL_CLIENT . ' h
      where h.id_client ' . $comparision_dir . db_int($id_client_start) . $where . '
      ORDER BY h.id_client ' . $order_dir . ' LIMIT '. db_int($length);

      $result = db_query( $query );
      return db_result_array_full($result);
   }
   
   static function getClientAttributeList( $id_client = 0, $length = 1 ) {
   	
   	$query = 'select ca.`id_client`, `type`, `val`
         	from ' . TBL_GLOBAL_CLIENT_ATTRIBUTES . ' ca ,
         	' . TBL_GLOBAL_CLIENT . ' c
         	where ca.id_client = c.id_client and ca.id_client = "' . db_int($id_client) . '"';
   	add_to_fp('$query ' . $query);
		$result = db_query( $query );
      return db_result_array_full($result);
   }
   
   static function doClientAttributeAddOrUpdate( $param ) {
   	
   	$query = 'select "' . db_int($param['id_client']) . '" as id_one, 
   			"' . db_escape($param['type'].','.$param['val']) . '" as additional_data,
          b_func_client_attribute_set("' . db_int($param['id_client']) . '", "' . db_escape($param['type']) . '",
          "' . db_escape($param['val']) . '") as status';

   	add_to_fp($query);
   	$result = db_query( $query );
   	return db_fetch_array($result);
   }

   static function doClientUserAttributeAddOrUpdate( $param ) {
   
   	$query = 'select "' . db_int($param['id_client']) . '" as id_one,
   			"' . db_int($param['id_client_user']) . '" as id_two,
   			"' . db_escape($param['type'].','.$param['val']) . '" as additional_data,
          b_func_client_user_attribute_set("' . db_int($param['id_client']) . '", "' . db_int($param['id_client_user']) . '",
          "' . db_escape($param['type']) . '", "' . db_escape($param['val']) . '") as status';
   
   	add_to_fp($query);
   	$result = db_query( $query );
   	return db_fetch_array($result);
   }
   
   static function getClientUserAttributeList( $id_client_user = 0, $length = 1 ) {
   
   	$query = 'select cua.`id_client`, cua.`id_client_user`, cua.`type`, cua.`val`
         	from ' . TBL_GLOBAL_CLIENT_USER_ATTRIBUTES . ' cua left join 
         	' . TBL_GLOBAL_CLIENT_USER . ' cu on 
         	(cua.id_client_user = cu.id_client_user and cua.id_client = cu.id_client)
         	where cua.id_client_user = "' . db_int($id_client_user) . '"';
   	add_to_fp('$query ' . $query);
   	$result = db_query( $query );
   	return db_result_array_full($result);
   }    
}
?>