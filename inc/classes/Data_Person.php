<?php
/**
 * Data_Person.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Data_Person
 * Data class going to provide data and operations on them.
 * Application will get and save any data through it.
 *
 * @todo Refactor static methods into a proper dependency-injected service layer.
 * @todo Implement strict type hinting for properties, parameters, and return types (PHP 8+).
 * @todo Convert implicit array structures into Data Transfer Objects (DTOs) or Value Objects.
 * @todo Replace legacy global functions like db_query, db_int, db_escape with PDO or an ORM like Doctrine.
 */

/*
 * client attributes
 * PRODUCT_VIEW_NAME
 * ACCOUNT_MANAGER_ADDRESS
 * BALANCE_CREDIT_LIMIT
       BALANCE_FREE_CREDIT
       BALANCE_PUNCTUALITY
 */

class Data_Person extends Data_Rights {

   /**
    * Data_Person constructor.
    * @todo Remove commented-out code (echo get_class();).
    */
   function __construct() {
        //echo get_class();
      parent::__construct();
   }

   /**
    * Retrieves placeholder address structures based on type.
    *
    * @param string $type The address type identifier.
    * @param array $params Contains context keys like 'id_client' and 'id_client_user'.
    * @return array The contextual address array, or an empty array if type is not recognized.
    *
    * @todo Use an Enum for $type instead of hardcoded strings.
    * @todo Fix potential typo in method name: additional_addreses -> additional_addresses.
    * @todo Fix typo in 'addreses' array variable name.
    */
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

   /**
    * Retrieves default account manager structures based on type.
    *
    * @param string $type The account manager type identifier.
    * @param array $params Contains context keys like 'id_client' and 'id_client_user'.
    * @return array The account manager configuration mapping, or an empty array.
    *
    * @todo Refactor type matching and array searching logic into cleaner lookup operations.
    */
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

   /**
    * Soft-deletes a person record from the specified internal table configuration.
    *
    * @param string $table The target entity mapping label ('ADMIN', 'CLIENT_USERS', 'CLIENT').
    * @param mixed $id_in The incoming raw identifier.
    * @return mixed Query resource/result representation, or string error status code.
    *
    * @todo Replace the direct SQL string concatenation with parameterized prepared statements.
    * @todo Convert return type to a consistent structure (e.g., boolean or exceptions instead of status strings).
    */
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

   /**
    * Updates passwords for admin or client records using custom cryptographic functions.
    *
    * @param int|string $id Target unique row identifier.
    * @param string $table The target type mapping identifier ('ADMIN', 'CLIENT').
    * @param array $data Contains keys 'new_password' and 'salt'.
    * @return void
    *
    * @todo Modernize hashing to use standard password_hash() instead of legacy custom hashing routines.
    * @todo Restructure if-else conditions into a clean strategy pattern or polymorphic structure.
    */
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

   /**
    * Refreshes the last login date time indicator for an application actor.
    *
    * @param int|string $id Target identity record pointer.
    * @param string $table Entity structural mapping context indicator ('ADMIN', 'CLIENT').
    * @return void
    *
    * @todo Standardize timestamp handling to use UTC across database platforms.
    */
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

   /**
    * Performs database creation operations for individual person records including associated access rights.
    *
    * @param string $table Entity structural mapping target context label.
    * @param array $data Set of demographic data attributes and authentication flags.
    * @return int|string Autoincrement row identification code or zero on failure.
    *
    * @todo Remove commented print_r and die debugging hooks.
    * @todo Enclose the entire execution chain within standard database transactions across both scopes.
    * @todo Replace the manual iteration inserts with a singular batch statement execution loop.
    */
   static function insert_person_data($table, $data) {
      $F = Framework::g_global();
      $data_main_sql['login'] =  $data['login'];
      $data_main_sql['description'] =  $data['description'];
      $data_main_sql['email'] =  $data['email'];
      $data_main_sql['phone'] =  $data['phone'];
      $data_main_sql['state'] =  $data['state'];
      $data_main_sql['created'] =  'now()';

      //      print_r($data_main_sql);
      //      die();

      $data_main_sql['password'] =  gl_make_password($data['new_password']);

      if( $table == 'ADMIN' ) {
         db_transaction_start();
         db_perform(TBL_GLOBAL_ADMIN, $data_main_sql, 'INSERT');
         $id_insert_id = db_insert_id();
         foreach($data['rights_ids'] as $id_rights) {
            db_query("insert into " . TBL_GLOBAL_RIGHTS2ADMIN . ' (id_admin, id_rights) values (' . (int)$id_insert_id . ', ' . (int)$id_rights . ')');
         }
         db_transaction_end();
      } else if( $table == 'CLIENT' ) {
         $data_main_sql['name'] =  $data['name'];
         $data_main_sql['id_client'] =  $data['id_client'];
         $id_client_user_max = db_max(TBL_GLOBAL_CLIENT_USER, 'id_client_user', array('id_client_user' => '< 1000') );
         $data_main_sql['id_client_user'] = $id_client_user_max+1;
         db_perform(TBL_GLOBAL_CLIENT_USER, $data_main_sql, 'INSERT');
         $id_insert_id = db_insert_id();
         foreach($data['rights_ids'] as $id_rights) {
            db_query("insert into " . TBL_GLOBAL_RIGHTS2CLIENT . ' (id_client_user, id_rights) values (' . (int)$id_insert_id . ', ' . (int)$id_rights . ')');
         }
      }
      return $id_insert_id;
   }

   /**
    * Executes an inline record manipulation statement to alter basic user info details.
    *
    * @param string $table Profile type definition filter constraint context.
    * @param int|string $id Entity record pointer (unused parameter).
    * @param array $data Set of variable updates targeting matching attributes.
    * @param array $data_org Initial historical mapping indicators for entity targeting.
    * @return void
    *
    * @todo Clean up unused parameters like `$id`.
    * @todo Optimize rights management by checking if diff exists instead of sweeping delete-then-insert.
    */
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


   /**
    * Pulls administrative structural data profile indices combined with active operational privileges.
    *
    * @param string $table Profile type category selector tag.
    * @param int|string $id Primary resource lookup identity parameter.
    * @return array Hydrated data attributes map schema array structure.
    *
    * @todo Avoid using implicit cross joins (comma separated tables); replace with explicit JOIN syntax.
    */
   static function get_person_data($table, $id) {
      //      $F = Framework::g_global();

      if( $table == 'ADMIN' ) {
         $res = db_query('select distinct p.id_admin, p.login, p.password, p.description, p.email,
                 p.created, p.last_login, p.state, GROUP_CONCAT(r.name) as rights_list, GROUP_CONCAT(r.id_rights) as rights_ids
             from ' . TBL_GLOBAL_ADMIN . ' p,
             ' . TBL_GLOBAL_RIGHTS2ADMIN . ' gl,
             ' . TBL_GLOBAL_RIGHTS . ' r ' .
              ' where p.id_admin = gl.id_admin and gl.id_rights = r.id_rights and p.id_admin = ' . (int)$id );
         return db_fetch_array($res);
      } else if( $table == 'CLIENT' ) {
         $res = db_query('select distinct p.id_client_user, p.login, p.password, p.name, p.description, p.email,
                 p.created, p.last_login, p.state, GROUP_CONCAT(r.name) as rights_list, GROUP_CONCAT(r.id_rights) as rights_ids
             from ' . TBL_GLOBAL_CLIENT_USER . ' p,  ' . TBL_GLOBAL_RIGHTS2CLIENT . ' gl,  ' . TBL_GLOBAL_RIGHTS . ' r ' .
              'where p.id_client_user = gl.id_client_user and gl.id_rights = r.id_rights and p.id_client_user = ' . (int)$id );
         return db_fetch_array($res);
      }

      return array();
   }

   /**
    * Returns a standardized lookup mapping list of static state flags.
    *
    * @param mixed $type Typification context filter query attribute (unused).
    * @return array Multi-dimensional layout of valid account states.
    *
    * @todo Convert this to a native PHP Backed Enum if these are static lookup statuses.
    */
   static function get_account_state($type) {
      return array(
      array('name' => 'ACTIVE',
         'description' => 'ACTIVE'),
      array('name' => 'BLOCKED',
         'description' => 'BLOCKED'),
      array('name' => 'SUSPENDED',
         'description' => 'SUSPENDED'),
      array('name' => 'NEW',
         'description' => 'NEW'),
      array('name' => 'ERASED',
         'description' => 'ERASED')
      );
   }

   /**
    * Fetches paginated matrix summaries of users tied to target organizational domains.
    *
    * @param string $table Entity structural context lookup descriptor tag.
    * @param int $id Base identity filtering parameter context tag.
    * @param mixed $type Operational filtering constraint type (unused).
    * @return array Array collection enclosing selected entity layout elements.
    *
    * @todo Fix incomplete parameter tracking code tagged with 'FIXME' and implement type checking.
    * @todo Eliminate the termination runtime command (die) on validation failure; throw exceptions instead.
    */
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

   /**
    * Executes an inline delete function database sequence routine targeting a client's user.
    *
    * @param array $param_array Map containing configuration entries ('id_client', 'id_client_user').
    * @return array Status description outcome configuration keys.
    *
    * @todo Stop using extract() as it obscures variable origins and harms IDE code analysis.
    */
   static function doClientUserDelete($param_array) {

      extract( $param_array );

      $query = 'select "' . db_int($id_client) . '" as id_one, "' . db_int($id_client_user) . '" as id_two,
          "" as additional_data,
          b_func_client_user_delete("' . db_int($id_client_user) . '", "' . db_int($id_client) . '") as status';

      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }

   /**
    * Invokes a structural stored routine script assignment modification operation targeting credentials.
    *
    * @param array $param_array Map data payload parameters ('id_client', 'id_client_user', 'password', 'password_salt').
    * @return array Status operation tracking array data structure.
    *
    * @todo Refactor implicit input array keys out for standard model properties or specific objects.
    */
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


   /**
    * Upserts client-user entity objects by binding structured input schemas onto conditional updates.
    *
    * @param ClientUserData $ClientUserData Standard structural client transaction user model definition blueprint.
    * @return array Process execution output feedback mapping flags.
    *
    * @todo Fix potential typo key inside return mapping results array definition: 'additiona_data' -> 'additional_data'.
    */
   static function doClientUserAddOrUpdate(ClientUserData $ClientUserData) {

       add_to_fp('doClientUserAddOrUpdate');
       $db_in = $ClientUserData->get_inst_upd_arr();
       add_to_fp('$db_in'.print_r($db_in, true));

       $query = 'insert into ' . TBL_GLOBAL_CLIENT . ' set '.implode(', ', $db_in['insert_array']).'
               ON DUPLICATE KEY UPDATE '.implode(', ', $db_in['update_array']);

       $res = array('id_one' => $ClientUserData->get_primary_key(), 'additiona_data' => '', 'status' => '');

       add_to_fp($query);
       $result = db_query( $query );
       $ar = db_affected_rows( $result );

       if( $ar == 1 ) $res['status'] = 'SUCCESS,NEW';
       elseif( $ar == 2 ) $res['status'] = 'SUCCESS,EXIST';
       elseif( $ar == 0 ) $res['status'] = 'SUCCESS,EXIST,NODIFF';
       else $res['status'] = 'ERROR,UNKNOW';

       return $res;
   }

   /**
    * Creates or updates a application profile via procedure execution loops and links default rights lists.
    *
    * @param ClientUserData $ClientUserData Data transfer definition blueprint tracking input components.
    * @param bool $add Flag determining structural operation pathway choices.
    * @return array Evaluation tracking data matrices layout arrays.
    *
    * @todo Fix code bug where extract() is run against an unassigned variable `$param_array`.
    * @todo Remove hardcoded access authorization roles indices array mask `array(4,5,6,7,8,9)`.
    * @todo Stop using extract() as it obscures variable origins and harms IDE code analysis.
    */
   static function doClientUserAdd(ClientUserData $ClientUserData, $add  = false) {

      extract( $param_array );

      $query = 'select id_client_user from ' . TBL_GLOBAL_CLIENT_USER . '
                where id_client = "'.db_int($param_array['id_client']).'" and
                id_client_user = "'.db_int($param_array['id_client_user']).'"';


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
      $result = db_fetch_array( db_query($query) );

      if( $password!='' && substr_count($password, ':') > 1 ) {
         $res_pass = self::doClientUserSetPassword($param_array);
         $result['additional_data'] = 'ClientUserSetPassword:' . $res_pass['status'];
      }

      $rights_ids = array();
      $rights_arr = array();
      if( Framework::not_null($rights) && substr_count($rights, ':') > 1 ) {
          $rights_ids = explode(':', $rights);
      } elseif( $add ) {
          $rights_ids = array(4,5,6,7,8,9);
      }
      foreach($rights_ids as $id_rights) {
          $rights_arr[] = '(' . (int)$id_client_user . ', ' . (int)$id_rights . ')';
      }
      if( Framework::not_null($rights_arr) ) {
         db_query("insert ignore into " . TBL_GLOBAL_RIGHTS2CLIENT . ' (id_client_user, id_rights) values '.implode(',', $rights_arr));
      }

      return $result;
   }

   /**
    * Upserts detailed localized configuration items for clients using parameter tracking blocks.
    *
    * @param array $param_array Set of profile mapping flags and parameters.
    * @return array Operational mapping context results feedback collection.
    *
    * @todo Fix code bugs related to undefined variable executions (`$ClientData`).
    * @todo Eliminate dead or unreachable configuration statement tracks below return mappings.
    * @todo Stop using extract() as it obscures variable origins and harms IDE code analysis.
    */
   static function doClientUserAddressAddOrUpdate($param_array) {

       add_to_fp('doClientUserAddressAddOrUpdate');
       $db_in = $ClientData->get_inst_upd_arr();
       add_to_fp('$db_in'.print_r($db_in, true));

       $query = 'insert into ' . TBL_GLOBAL_CLIENT . ' set '.implode(', ', $db_in['insert_array']).'
               ON DUPLICATE KEY UPDATE '.implode(', ', $db_in['update_array']);

       $res = array('id_one' => $ClientData->get_primary_key(), 'additiona_data' => '', 'status' => '');

       add_to_fp($query);
       $result = db_query( $query );
       $ar = db_affected_rows( $result );

       if( $ar == 1 ) $res['status'] = 'SUCCESS,NEW';
       elseif( $ar == 2 ) $res['status'] = 'SUCCESS,EXIST';
       elseif( $ar == 0 ) $res['status'] = 'SUCCESS,EXIST,NODIFF';
       else $res['status'] = 'ERROR,UNKNOW';

       return $res;


       extract( $param_array );

       $query = 'select "' . db_int($id_client_user) . '" as id_one, "' . db_int($id_address) . '" as id_two,
          "" as additional_data,
          b_func_client_user_set_address("' . db_int($id_address) . '", "' . db_int($id_client_user) . '", "' . db_int($id_client) . '",
          "' . db_escape($description) . '", "' . db_escape($name) . '",  "' . db_escape($street) . '",
          "' . db_escape($city) . '", "' . db_escape($zip_code) . '",  "' . db_escape($country) . '",
          "' . db_escape($state) . '") as status';

       add_to_fp($query);
       $result = db_query( $query );
       return db_fetch_array($result);
   }

   /**
    * Executes address wiping commands based on incoming parameters.
    *
    * @param array $param_array Set of matching structural constraints keys.
    * @return array Verification output maps metadata values tracking arrays.
    *
    * @todo Avoid using multi-argument calls on singular format filters (`db_escape('id_client:',$id_client)`).
    * @todo Stop using extract() as it obscures variable origins and harms IDE code analysis.
    */
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

   /**
    * Returns collection of user address structures matching directional bounds.
    *
    * @param int|string $id_client_user Target client account user unique ID.
    * @param int|string $id_address Offset baseline pointer index element.
    * @param int $length Total limit configuration length property.
    * @return array Multidimensional database output array records.
    *
    * @todo Refactor indirect parsing calls (`Data::_length_dir`) to improve class encapsulation.
    */
   static function getClientUserAddressList($id_client_user, $id_address, $length) {
       list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);

       $query = 'select id_address, id_client, id_client_user, description, name, street, city, zip_code,
                   country, state
                   from ' . TBL_GLOBAL_CLIENT_USER_ADDRESS . '
                    where id_client_user = "' . db_int($id_client_user) . '"
                   AND id_address ' . $comparision_dir . db_int($id_address) . '
                   ORDER BY id_address ' . $order_dir . ' LIMIT '. db_int($length);

       add_to_fp($query);
       $result = db_query( $query );
       return db_result_array_full($result);
   }

   /**
    * Selects localized profile shipping information structures tracking client definitions.
    *
    * @param int|string $id_client Corporate level target client indexing key.
    * @param int|string $id_address Baseline tracking index point marker.
    * @param int $length Pagination numeric row count sizing indicator.
    * @return array Structured array collection enclosing selected data elements.
    */
   static function getClientAddressList($id_client, $id_address, $length) {
       list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);

       $query = 'select id_address, id_client, id_client_user, description, name, street, city, zip_code,
                   country, state
                   from ' . TBL_GLOBAL_CLIENT_USER_ADDRESS . '
                    where id_client = "' . db_int($id_client) . '"
                   AND id_address ' . $comparision_dir . db_int($id_address) . '
                   ORDER BY id_address ' . $order_dir . ' LIMIT ' . db_int($length);

       add_to_fp($query);
       $result = db_query( $query );
       return db_result_array_full($result);
   }

   /**
    * Returns unfiltered systemic indices collection tracking layout indicators.
    *
    * @param int|string $id_address Core structural alignment offset tracking pointer.
    * @param int $length Numerical batch tracking boundary index variable.
    * @return array Collection enclosing selected schema rows.
    */
   static function getAddressList($id_address, $length) {
       list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);

       $query = 'select id_address, id_client, id_client_user, description, name, street, city, zip_code,
                   country, state
                   from ' . TBL_GLOBAL_CLIENT_USER_ADDRESS . '
                    where id_address ' . $comparision_dir . db_int($id_address) . '
                   ORDER BY id_address ' . $order_dir . ' LIMIT '. db_int($length);

       add_to_fp($query);
       $result = db_query( $query );
       return db_result_array_full($result);
   }

   /**
    * Upserts core client accounts profile setups via input parameter models.
    *
    * @param ClientData $ClientData Core target layout structure parameter mapping block.
    * @return array Status execution response variables checklist matrix.
    */
   static function doClientAddOrUpdate( ClientData $ClientData ) {

       add_to_fp('doClientAddOrUpdate');
       $db_in = $ClientData->get_inst_upd_arr();
       add_to_fp('$db_in'.print_r($db_in, true));

       $query = 'insert into ' . TBL_GLOBAL_CLIENT . ' set '.implode(', ', $db_in['insert_array']).'
               ON DUPLICATE KEY UPDATE '.implode(', ', $db_in['update_array']);

       $res = array('id_one' => $ClientData->get_primary_key(), 'additiona_data' => '', 'status' => '');

       add_to_fp($query);
       $result = db_query( $query );
       $ar = db_affected_rows( $result );

       if( $ar == 1 ) $res['status'] = 'SUCCESS,NEW';
       elseif( $ar == 2 ) $res['status'] = 'SUCCESS,EXIST';
       elseif( $ar == 0 ) $res['status'] = 'SUCCESS,EXIST,NODIFF';
       else $res['status'] = 'ERROR,UNKNOW';

       return $res;
   }

   /**
    * Clears child data configurations for users based on entity domain labels.
    *
    * @param string $what Data type classification targeting descriptor keys.
    * @param array $param_array Data constraints criteria keys context collections.
    * @return string|bool Execution data tracker string, or false on mapping failure.
    */
   static function doClientUserCleanMethodData( $what, $param_array ) {
       $id_client = db_int($param_array['id_client']);
       $id_client_user = db_int($param_array['id_client_user']);

       switch( $what ) {
           case 'ClientUserAddressData':
               $del_cat = "update " . TBL_GLOBAL_CLIENT_USER_ADDRESS . ' set state="NA"
                       where id_client = ' . $id_client . ' and id_client_user = ' . $id_client_user;
               db_query($del_cat);
               return 'ADDRESS:'.db_affected_rows();
               break;
           case 'ClientUserAttributeData':
               $del_att = "delete from " . TBL_GLOBAL_CLIENT_USER_ATTRIBUTES . '
                       where id_client = ' . $id_client . ' and id_client_user = ' . $id_client_user;
               db_query($del_att);
               return 'ATTRIBUTE:'.db_affected_rows();
               break;
           case 'AccountManagerData':
               $del_acmgr = "update " . TBL_GLOBAL_CLIENT_USER_ACCOUNT_MANAGER . ' set state="NA"
                       where id_client = ' . $id_client . ' and id_client_user = ' . $id_client_user;
               db_query($del_acmgr);
               return 'ACCOUNT_MANAGER:'.db_affected_rows();
               break;
           case 'ClientUserPasswordData':
               return 'RECURSION';
               break;
       }

       return false;
   }


   /**
    * Wipes system pricing matrices or profile options matching target client indices.
    *
    * @param string $what Target criteria structure definition tag context marker.
    * @param int|string $id_client Unique data record lookup key variable configuration.
    * @return string|bool Status validation tracking output tracker metric, or false.
    */
   static function doClientCleanMethodData( $what, $id_client ) {

       $id_client = db_int($id_client);

       switch( $what ) {
           case 'ClientProductPriceListData':
           case 'ProductClientPriceData':
               $del_prc = "delete from " . TBL_SHOP_PRODUCT_CLIENT_PRICE . ' where id_client = ' . $id_client;
               db_query($del_prc);
               return 'PRICE:'.db_affected_rows();
               break;
           case 'ClientAttributeData':
               $del_attr = "delete from " . TBL_GLOBAL_CLIENT_ATTRIBUTES . ' where id_client = ' . $id_client;
               db_query($del_attr);
               return 'ATTRIBUTE:'.db_affected_rows();
               break;
           case 'ClientUserData':
               return 'RECURSION';
               break;
       }

       return false;
   }


   /**
    * Completely purges a client profile configuration along with dependent structural links.
    *
    * @param array $param_array Standard structured dictionary tracking identity fields.
    * @return array Status reporting results maps indicators.
    */
   static function doClientRemovePermanently( array $param_array ) {

       $id_client = (int)$param_array['id_client'];
       //count
       $nr_basket = Data::remove_permanently_basket_client( $id_client );
       //array
       $client_user = Data::client_remove_client_user( $id_client );

       $tmp_cli = array();
       $tmp_cli['ProductClientPriceData'] = Data::doClientCleanMethodData('ProductClientPriceData', $id_client);
       $tmp_cli['ClientAttributeData'] = Data::doClientCleanMethodData('ClientAttributeData', $id_client);

       $del_cli = "delete from " . TBL_GLOBAL_CLIENT . ' where id_client = ' . db_int($id_client);
       add_to_fp('doClientRemovePermanently:'.$del_cli);
       db_query($del_cli);
       $res_cli_del = db_affected_rows();

       $additional_data = array(
               'BASKET' => $nr_basket,
               'CLIENT_USER' => $client_user,
               'CLIENT' => $tmp_cli
       );

       $return = array('id' => (int)$id_client,
               'additional_data' => $additional_data,
               'status'  => (($res_cli_del>0)?'SUCCESS':'ERROR'));

       return $return;
   }

   /**
    * Loops through user lists linked to target clients to perform structural sub-cleansings.
    *
    * @param int|string $id_client Primary target structural context filter index variable.
    * @return array Wiping process execution tracker metric results mapping blocks.
    */
   static function client_remove_client_user( $id_client ) {
       $user_client_list = self::get_persons_list('CLIENT', $id_client);

       $res = array();
       foreach( $user_client_list as $user_client ) {
           $id_client_user = (int)$user_client['id_client_user'];
           $param_array = array('id_client' => $id_client, 'id_client_user' => $id_client_user);
           $tmp = array();
           $tmp[] = Data::doClientUserCleanMethodData('ClientUserAddressData', $param_array);
           $tmp[] = Data::doClientUserCleanMethodData('ClientUserAttributeData', $param_array);
           $tmp[] = Data::doClientUserCleanMethodData('AccountManagerData', $param_array);
           $del_cliusr = "delete from " . TBL_GLOBAL_CLIENT_USER . '
                   where id_client = "' . db_int($id_client) . '" and id_client_user = "' . db_int($id_client_user) . '"';
           add_to_fp('client_remove_client_user:'.$del_cliusr);
           db_query($del_cliusr);
           $tmp[] = 'CLIENT:'.db_affected_rows();

           $res['CLIENT_'.$id_client_user] = implode(',',$tmp);
       }
       return $res;
   }

   /**
    * Adds an entirely new corporate entity layout profile structure when validation checks pass.
    *
    * @param ClientData $ClientData Core operational parameters model mapping data context structure.
    * @param bool $add Flag determining structural behavior controls (unused).
    * @return array Process execution confirmation metrics flags maps.
    */
   static function doClientAdd(ClientData $ClientData, $add = false) {

       add_to_fp('doClientAdd');
       $id_client = (int)$ClientData->get_primary_key();

       $res = array('id_one' => $id_client, 'additiona_data' => '', 'status' => '');
      $query = 'select id_client from ' . TBL_GLOBAL_CLIENT . ' where id_client = "'.db_int($id_client).'"';
      $result = db_query( $query );
      if( db_rows($result) > 0 ) {
          $res['status'] = 'ERROR,EXIST';
          return $res;
      } else {
          return Data_person::doClientAddOrUpdate($ClientData, false);
      }

   }

   /**
    * Modifies a primary record identity pointer mapping via custom migration routines.
    *
    * @param int|string $id_client Historical reference primary tracking variable configuration.
    * @param int|string $id_client_new Updated reference system identity assignment identifier variable.
    * @return array Status execution response validation framework arrays elements.
    */
   static function doClientNewIdUpdateList($id_client, $id_client_new) {

      $query = 'select "' . db_int($id_client) . '" as id_one,
          "NEW_ID_CLIENT:' . db_int($id_client_new) . '" as additional_data,
          b_func_client_id_change("' . db_int($id_client) . '", "' . db_int($id_client_new) . '") as status';

      add_to_fp($query);
      $result = db_query( $query );
      return db_fetch_array($result);
   }


   /**
    * Modifies compound target client-user system indices using custom procedures.
    *
    * @param array $param_array Base identity parameter sets definitions trackers.
    * @param array $new_ids_array Target updated migration values parameters index mapping collection.
    * @return array Matrix validation response collection entries.
    *
    * @todo Clean up commented parameter trackers (`// $new_ids_array`).
    * @todo Stop using extract() as it obscures variable origins and harms IDE code analysis.
    */
   static function doClientUserNewIdUpdateList($param_array, $new_ids_array) {

       extract($param_array);
//        $new_ids_array
       $query = 'select "' . db_int($id_client) . '" as id_one,
            "' . db_int($id_client_user) . '" as id_two,
          "NEW_ID_CLIENT_USER:' . db_int($new_ids_array['id_client_user']) .
          (($id_client!=$new_ids_array['id_client'])?':NEW_ID_CLIENT:'.db_int($new_ids_array['id_client']):'') .
          '" as additional_data,
          b_func_client_user_id_change("' . db_int($id_client) . '", "' . db_int($id_client_user)  . '",
          "' . db_int($new_ids_array['id_client']) . '", "' . db_int($new_ids_array['id_client_user']) . '") as status';

       add_to_fp($query);
       $result = db_query( $query );
       return db_fetch_array($result);
   }


   /**
    * Creates a profile definition index record structure utilizing specialized backend scripts.
    *
    * @param array $data Component criteria parameters set dictionary collection properties.
    * @return void
    *
    * @todo Return the results from db_call_proc instead of discarding them in an unused variable `$return`.
    */
   static function insert_client_data_id($data) {

      $data_sql['id_client'] =  $data['id_client'];
      $data_sql['name'] =  $data['name'];
      $data_sql['description'] =  $data['description'];
      $data_sql['email'] =  $data['email'];
      $data_sql['phone'] =  $data['phone'];
      $data_sql['state'] =  $data['state'];


      $return = db_call_proc('b_func_client_add', $data_sql);
   }

   /**
    * Inserts data properties into standard corporate indices tracking tables maps.
    *
    * @param array $data Structural attributes mappings collection flags definitions.
    * @return int|string Newly generated system tracking identifier index value.
    */
   static function insert_client_data($data) {

      $data_main_sql['name'] =  $data['name'];
      $data_main_sql['description'] =  $data['description'];
      $data_main_sql['email'] =  $data['email'];
      $data_main_sql['phone'] =  $data['phone'];
      $data_main_sql['state'] =  $data['state'];
      $data_main_sql['created'] =  'now()';

      db_perform(TBL_GLOBAL_CLIENT, $data_main_sql, 'INSERT');
      $id_client = db_insert_id();

      return $id_client;
   }

   /**
    * Updates matching operational attributes linked to specified client indexes.
    *
    * @param int|string $id Target system reference location index mapping parameter (unused).
    * @param array $data Set of variable updates targeting matching attributes.
    * @param array $data_org Context configuration historical records definitions trackers.
    * @return void
    */
   static function update_client_data($id, $data, $data_org) {
      $F = Framework::g_global();
      $data_main_sql['name'] =  $data['name'];
      $data_main_sql['description'] =  $data['description'];
      $data_main_sql['email'] =  $data['email'];
      $data_main_sql['phone'] =  $data['phone'];
      $data_main_sql['state'] =  $data['state'];


      db_perform(TBL_GLOBAL_CLIENT, $data_main_sql, 'UPDATE', "id_client=" . (int)$data_org['id_client']);
   }

   /**
    * Pulls explicit option variables or configurations linked to user data layers.
    *
    * @param int|string $id_client Root level parent corporate client mapping index tracker key.
    * @param int|string $id_client_user Target application context row identification code index variables.
    * @param string|bool $attribute_type Targeting specific settings label identifiers.
    * @return mixed Extracted attribute scalar contents, properties array collection mapping, or false.
    */
   static function get_client_user_attribute( $id_client, $id_client_user, $attribute_type = false) {
       $F = Framework::g_global();

       $where_add = '';

       if( $F->not_null($attribute_type) ) {
           $where_add = ' and cua.type = "' . db_escape($attribute_type) . '"';
       }

       $query = 'select cua.`id_client`, cua.`id_client_user`, `type`, `val`
             from ' . TBL_GLOBAL_CLIENT_USER_ATTRIBUTES . ' cua ,
             ' . TBL_GLOBAL_CLIENT_USER . ' cu
             where cua.id_client = cu.id_client and
             cua.id_client = "' . db_int($id_client) . '" and
             cua.id_client_user = "' . db_int($id_client_user) . '" ' . $where_add;

       $result = db_query( $query );
       $nrow = db_rows( $result );
       if( $nrow == 1 && $F->not_null($attribute_type) ) {
           return db_fetch_result('val', $result);
       } elseif( $nrow > 0 && $F->is_null($attribute_type)) {
           $res_arr = db_result_array_full($result);
           $ret_arr = array();
           foreach($res_arr as $res_one) {
               $ret_arr[$res_one['type']] = $res_one['val'];
           }
           return $ret_arr;
       } else {
           return false;
       }
   }

   /**
    * Pulls systemic option properties variables linked to client database contexts.
    *
    * @param int|string $id_client Target corporate level system configuration tracking index row reference.
    * @param string|bool $attribute_type Specific configuration classification option key code tag.
    * @return mixed Target scalar attribute mapping representation variable value, or false.
    *
    * @todo Refactor static parameter tracking check (`Data_Product::$Data_Products_params`) to remove cross-dependency issues.
    */
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
         if ( Data_Product::$Data_Products_params['client_view'] ) return Data_Product::$Data_Products_params['client_view'];
         elseif ( defined('SHOP_CLIENT_PRODUCT_PRICE_VIEW') ) return constant('SHOP_CLIENT_PRODUCT_PRICE_VIEW');
         else return false;
      } else {
          $result = db_query( $query );
          $nrow = db_rows( $result );
          if( $nrow == 1 && $F->not_null($attribute_type) ) {
              return db_fetch_result('val', $result);
       } elseif( $nrow > 0 && $F->is_null($attribute_type)) {
              $res_arr = db_result_array_full($result);
              $ret_arr = array();
              foreach($res_arr as $res_one) {
                  $ret_arr[$res_one['type']] = $res_one['val'];
              }
              return $ret_arr;
          } else {
              return false;
          }
      }
   }

   /**
    * Selects localized overview details summarizing user statistics tied to clients.
    *
    * @param int|string $id_client Core target row reference system tracking parameter variable.
    * @return array Mapping attributes array containing summary parameters metrics.
    */
   static function get_client_data( $id_client ) {
      $res = db_query('select distinct h.id_client, h.name, h.description, h.email, h.phone, h.created, h.state,
              count(hu.id_client_user) as count_users, group_concat(hu.id_client_user) as ids_client_user
             from ' . TBL_GLOBAL_CLIENT . ' h ,
             ' . TBL_GLOBAL_CLIENT_USER . ' hu
             where h.id_client = hu.id_client and h.id_client = ' . db_int($id_client) );

      return db_fetch_array($res);
   }

   /**
    * Returns structural profile data properties matching the earliest user record linked to a client.
    *
    * @param int|string $id_client Parent level system tracking client index configuration pointer.
    * @param string|bool $right Specific application access authorization privilege filtering rule choice.
    * @return array Array structure tracking individual rows data attributes elements maps.
    *
    * @todo Remove commented line documentation blocks (`//'CLIENT', 'ADMIN'`).
    */
   static function get_client_first_client_user_data( $id_client, $right = false ) {
       //'CLIENT', 'ADMIN'

       $where = '';
       if( Framework::not_null($right) ) {
           $where = ' and p.id_client_user IN (SELECT gl.id_client_user from
           ' . TBL_GLOBAL_RIGHTS2CLIENT . ' gl join  ' . TBL_GLOBAL_RIGHTS . ' r
           on (gl.id_rights = r.id_rights and r.scope = "CLIENT" )
           where r.name = "' . db_escape($right) . '")';
       }

       $query = 'select p.id_client, p.id_client_user, p.login, p.password, p.name, p.description, p.email,
                p.created, p.last_login, p.state, GROUP_CONCAT(r.name) as rights_list, GROUP_CONCAT(r.id_rights) as rights_ids
                from ' . TBL_GLOBAL_CLIENT_USER . ' p,  ' . TBL_GLOBAL_RIGHTS2CLIENT . ' gl,  ' . TBL_GLOBAL_RIGHTS . ' r
              where p.id_client_user = gl.id_client_user and gl.id_rights = r.id_rights and
               p.id_client = ' . (int)$id_client . ' ' . $where . '
              group by p.id_client_user order by p.created, p.id_client_user asc limit 1';
       $res = db_query($query);
       return db_fetch_array($res);
   }

   /**
    * Pulls basic data overview options tracking individual client user profiles.
    *
    * @param int|string $id_client_user Unique record selection location index parameter variable.
    * @return array Systemic properties dataset array row record layout attributes map.
    */
   static function get_client_user_data( $id_client_user ) {
      $res = db_query('select cu.id_client_user, cu.id_client, cu.name, cu.description, cu.login, cu.email,
              cu.phone, cu.phone_cell, cu.state
             from ' . TBL_GLOBAL_CLIENT_USER . ' cu
             where cu.id_client_user = ' . db_int($id_client_user) );

      return db_fetch_array($res);
   }

   /**
    * Selects detailed individual localization data variables from structural shipping tables.
    *
    * @param int|string $id_address Target record tracking pointer locator option values.
    * @return array Hydrated data schema details maps variables collection tracking arrays.
    */
   static function get_address( $id_address ) {

       $query = 'select id_address, id_client, id_client_user,
                   description, name, street, city, zip_code,country, date_created, date_modified,
                  UNIX_TIMESTAMP(date_created) as ts_created, UNIX_TIMESTAMP(date_modified) as ts_modified,
                   rights_edit, rights_use, state
                    from ' . TBL_GLOBAL_CLIENT_USER_ADDRESS . ' where id_address = ' . db_int($id_address);

       return db_fetch_array( db_query( $query ) );
   }

   /**
    * Pulls grouped sub-collections containing localized addresses matching selection rules.
    *
    * @param int|string $id_client Parent identifier filter context property.
    * @param int|string $id_client_user Specific secondary profile identifier selector parameter value.
    * @return array Multi-dimensional array collection of active shipping indicators maps.
    */
   static function get_address_list( $id_client, $id_client_user = 0 ) {
       if( $id_client_user > 0 ) {
           $where = ' ( id_client = ' . db_int($id_client) . ' AND rights_use = "CLIENT" ) OR
                   id_client_user = ' . db_int($id_client_user) . ' AND state="ACTIVE"';
       } else {
           $where = ' id_client = ' . db_int($id_client) . ' AND state="ACTIVE"';
       }

       $query = 'select id_address, id_client, id_client_user,
                   description, name, street, city, zip_code,country, date_created, date_modified,
                  UNIX_TIMESTAMP(date_created) as ts_created, UNIX_TIMESTAMP(date_modified) as ts_modified,
                   rights_edit, rights_use, state
                    from ' . TBL_GLOBAL_CLIENT_USER_ADDRESS . ' where
                    ' . $where;

       return db_result_array_full_id( db_query( $query ) );
   }

   /**
    * Extracts detailed organizational workflow manager options mappings.
    *
    * @param int|string $id_client Parent level lookup key criteria parameters variable.
    * @param int|string $id_client_user Optional subordinate filter parameter index tracking variable.
    * @return array Hydrated data structural attributes mappings tracking indices.
    */
   static function get_account_manage_list( $id_client, $id_client_user = 0 ) {
       if( $id_client_user > 0 ) {
           $where = ' ( id_client = ' . db_int($id_client) . ' AND rights_use = "CLIENT" ) OR
                   id_client_user = ' . db_int($id_client_user) . ' AND state="ACTIVE"';
       } else {
           $where = ' id_client = ' . db_int($id_client) . ' AND state="ACTIVE"';
       }

       $query = 'select id_account_manager, id_client, id_client_user, account_manager_name,
                   fullname, phone1, phone2, email, date_created, date_modified,
                  UNIX_TIMESTAMP(date_created) as ts_created, UNIX_TIMESTAMP(date_modified) as ts_modified,
                   rights_edit, rights_use, state
                    from ' . TBL_GLOBAL_CLIENT_USER_ACCOUNT_MANAGER . ' where
                    ' . $where;

       return db_result_array_full_id( db_query( $query ) );
   }

   /**
    * Selects complete set summaries covering active operational clients.
    *
    * @return array Collection containing unrolled query output items array.
    */
   static function get_client_list() {
      $SP = SplitPage::g_global();

      $query = 'select distinct h.id_client, h.name, h.description, h.email, h.phone, h.created, h.state
             from ' . TBL_GLOBAL_CLIENT . ' h';
      $sp_query = $SP->prepare_sql( $query );
      $res = db_query($sp_query);
      return db_result_array($res);
   }



   /**
    * Pulls targeted chunks from user database layers matching specific limit markers.
    *
    * @param int|string $id_client Scope targeting tracking identifier context parameter.
    * @param int|string $id_client_user_start Pagination base index position indicator variables.
    * @param int $length Operational processing batch row limit constraints flags.
    * @param string $where Custom query conditions to inject.
    * @return array Nested data array structures representing matching context rows.
    *
    * @todo Fix spelling.
    */
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

   /**
    * Retrieves paginated collections of corporate clients using dynamic filter injections.
    *
    * @param int|string $id_client_start Baseline numeric key pagination indicator tracking variable.
    * @param int $length Sizing row length limits processing flags configuration.
    * @param string $where Custom query criteria filter tracking parameter statement script.
    * @return array Hydrated data summary schemas mappings matrices arrays.
    *
    * @todo Avoid using custom dynamic wrapper filters (`db_unroll_conditions`) to prevent potential SQL injections.
    * @todo Fix spelling.
    */
   static function getClientList( $id_client_start = 0, $length = 1, $where = '' ) {
      list($length, $comparision_dir, $order_dir) = Data::_length_dir($length);

      if( Framework::not_null($where) ) $where = ' and ' . db_unroll_conditions($where);

      $query = 'select h.id_client, h.name, h.description, h.email, h.phone, h.created, h.state
       from ' . TBL_GLOBAL_CLIENT . ' h
      where h.id_client ' . $comparision_dir . db_int($id_client_start) . $where . '
      ORDER BY h.id_client ' . $order_dir . ' LIMIT '. db_int($length);
      add_to_fp('$query ' . $query);

      $result = db_query( $query );
      return db_result_array_full($result);
   }

   /**
    * Pulls flat attributes list collections linked onto a corporate profile reference.
    *
    * @param int|string $id_client Core row level lookup locator key values options variables.
    * @param int $length Pagination numeric chunk tracking parameters variables (unused).
    * @return array Database mapping attributes output list items.
    */
   static function getClientAttributeList( $id_client = 0, $length = 1 ) {

       $query = 'select ca.`id_client`, `type`, `val`
             from ' . TBL_GLOBAL_CLIENT_ATTRIBUTES . ' ca ,
             ' . TBL_GLOBAL_CLIENT . ' c
             where ca.id_client = c.id_client and ca.id_client = "' . db_int($id_client) . '"';
       add_to_fp('$query ' . $query);
        $result = db_query( $query );
      return db_result_array_full($result);
   }

   /**
    * Assigns custom attribute settings configurations onto client entities.
    *
    * @param array $param Map enclosing configuration options ('id_client', 'type', 'val').
    * @return array Verification output tracking status options arrays.
    */
   static function doClientAttributeAddOrUpdate( $param ) {

       $query = 'select "' . db_int($param['id_client']) . '" as id_one,
               "' . db_escape($param['type'].','.$param['val']) . '" as additional_data,
          b_func_client_attribute_set("' . db_int($param['id_client']) . '", "' . db_escape($param['type']) . '",
          "' . db_escape($param['val']) . '") as status';

       add_to_fp($query);
       $result = db_query( $query );
       return db_fetch_array($result);
   }

   /**
    * Assigns unique attribute settings markers onto specific client users.
    *
    * @param array $param Configuration options collection map ('id_client', 'id_client_user', 'type', 'val').
    * @return array Stored procedure response collection layout fields.
    */
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

   /**
    * Returns variable attributes listings maps indexing target user options.
    *
    * @param int|string $id_client_user Unique key targeting user mapping layouts database pointers.
    * @param int $length Pagination limit value (unused).
    * @return array Complete output parameters tracking response collection array.
    */
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
