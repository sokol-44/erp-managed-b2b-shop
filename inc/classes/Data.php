<?php
/**
 * Data.php Global initialization file
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
class Data extends Data_Person {
   static $result_array;
   static $result_size;
   static $page, $com;


   /**
    *
    */
   function __construct() {
      parent::__construct();
   }

   static function get_translation_all($com_str) {
      $query = 'select definition, translation from ' . TBL_CORE_TRANSLATION . '
      	where com is NULL or com = "' . db_escape($com_str) . '"';
      return db_result_array( db_query($query) );
   }

   static function get_translation($com_str, $name) {
      $query = 'select definition, translation from ' . TBL_CORE_TRANSLATION . '
      	where com = "' . db_escape($com_str) . '" and definition = "' . db_escape($name) . '"';
      return db_fetch_array( db_query($query) );
   }


   /**
    * autoload magic object & method
    */
   //   function __call($name, array $arguments) {
   //      $name_array = explode('_', $name);
   //      $class_name = self::get_legalsubclass($name_array['1']);
   //
   //      if( !isset($this->ac[$class_name]) || !is_object($this->ac[$class_name]) ) {
   //         include_once('Data_' . $class_name . '.php');
   //         eval('$this->ac[$class_name] = new Data_' . $class_name . '();');
   //         $this->ac[$class_name]->$name($arguments);
   //      } else {
   //         $this->ac[$class_name]->$name($arguments);
   //      }
   //   }


   /**
    * Return a valid subclass name
    * @param string $get_legal_subclass
    * @return string|name
    */
   //FIXME real list of subclass
   static function get_legalsubclass($get_legal_subclass) {
      return $subclass_name;
   }

   static function get_currences_list() {
      $query = 'SELECT id_currency, currency_txt, currency_symbol FROM ' . TBL_GLOBAL_CURRENCES . ' ';
      return db_result_array( db_query($query) );
   }

   static function get_errors() {
      // $this->db->query("select * from t_db_errors");
      $res = db_query('select * from ' . TBL_CORE_DB_ERRORS);
      while( $row = db_fetch_array($res) ) {
         echo $row['id'] . "<br>\n";
      }

   }

   static function get_com_1() {}

   /**
    * Check validity of component name
    * @param string $component_name
    * @return string|valid component name
    */
   function check_com_legal($component_name) {
      return $component_name;

   }


   function get_admin_com_1() {}

   /**
    * Check validity of component name
    * @param string $component_name
    * @return string|valid component name
    */
   static function get_admin_com_menu_login () {

   }

   /**
    * Check validity of component name
    * @param string $component_name
    * @return string|valid component name
    */
   static function check_admin_com_legal($component_name) {
      return $component_name;

   }

   /**
    * Get default component name
    * @return string|valid component name
    */
   static function get_admin_com_default() {
      //FIXME - some logic
      return 'login';
   }

   /**
    * Get default component name
    * @return string|valid component name
    */
   static function get_admin_com_login() {
      //FIXME - some logic
      return 'login';
   }


   static function get_admin_com_menu_model_inc( $com_name ) {
      //FIXME - some logic
      return DIR_ADM_INC_MENUS . DS . $com_name . '_model.php';
   }

   static function get_admin_com_menu_view_inc( $com_name ) {
      return DIR_ADM_INC_MENUS . DS . $com_name . '_viewer.php';
   }

   static function get_admin_com_model_inc( $com_name ) {
      return DIR_ADM_INC_COMPONENTS . DS . $com_name . '_model.php';
   }

   static function get_admin_com_viewer_inc( $com_name ) {
      return DIR_ADM_INC_COMPONENTS . DS . $com_name . '_viewer.php';
   }


   function get_login_1() { }

   /**
    * Return data from provided login and table
    * @param string $login
    * @param string $table
    * @return array|name
    */
   static function get_login_data($login, $table) {
      
      if( $table == 'CLIENT' && defined('TBL_GLOBAL_CLIENT_USER') ) {
         $tbl_name = TBL_GLOBAL_CLIENT_USER;
         $table_id = 'id_client_user';
      } elseif( $table == 'ADMIN' && defined('TBL_GLOBAL_ADMIN') ) {
         $tbl_name = TBL_GLOBAL_ADMIN;
         $table_id = 'id_admin';
      } else {
         return false;
      }

      if( $tbl_name != '' ) {
         $res = db_query('select *, ' . $table_id . ' as id_table from ' . $tbl_name . ' where login = "' . db_escape($login) . '"');
         return db_fetch_array($res);
      } else {
         return false;
      }
   }

   static function get_login_rights($id, $table) {

      if( $table == 'CLIENT' ) {
         if( defined('TBL_GLOBAL_CLIENT_USER') ) $tbl_person = TBL_GLOBAL_CLIENT_USER;
      } elseif( $table == 'ADMIN' ) {
         if( defined('TBL_GLOBAL_ADMIN') ) $tbl_person = TBL_GLOBAL_ADMIN;
      } else {
         return false;
      }

      if( defined('TBL_GLOBAL_RIGHTS2' . $table) ) $tbl_glue = constant('TBL_GLOBAL_RIGHTS2' . $table);
      else return false;

      if( defined('TBL_GLOBAL_RIGHTS') ) $tbl_rights = constant('TBL_GLOBAL_RIGHTS');
      else return false;

      if( $table == 'ADMIN' ) {
         $table_id = 'id_admin';
      } else if( $table == 'CLIENT' ) {
         $table_id = 'id_client_user';
      }

      $res = db_query('select r.*, p.' . $table_id . ' as id_table from ' .
      $tbl_person . ' p,  ' . $tbl_glue . ' gl,  ' . $tbl_rights . ' r ' .
      'where p.' . $table_id . ' = gl.' . $table_id . ' and gl.id_rights = r.id_rights and ' .
      ' p.' . $table_id . ' = "' . db_escape($id) . '"');
      $ret_array = array();
      if( db_rows($res)>0 ) {
         while( $row = db_fetch_array($res) ) $ret_array[$row['name']] = $row['name'];
      }
      
      //FIXME - bandaid
      if( Framework::is_null($ret_array) ) {
      	$ret_array = array('LEVEL_0' => 'LEVEL_0', 'USER' => 'USER');
      }

      return $ret_array;
   }
   
   static function _length_dir($length ) {
      if( (int)$length  == 0 ) $length = 1;
      
      if( $length > 0 ) {
         $comparision_dir = ' >= ';
         $order_dir = ' ASC ';
      } else {
         $comparision_dir = ' <= ';
         $order_dir = ' DESC ';
      }
      $length = (int)abs($length);
      $res =  array($length, $comparision_dir, $order_dir);
      add_to_fp( var_export($res, true) );
      return $res;
   }

   static function get_component_rights( $component_name  ) {
   
      $query = 'SELECT `glp`.`place_name`, `glp`.`script`, `glp`.`type`, `glp`.`logged`, `glp`.`sequence`
             FROM ' . TBL_GLOBAL_TEMPLATE_PLACES . ' glp WHERE `enabled`="1" and `type`="COM" and
             `script` = "' . db_escape($component_name) . '" ORDER BY `glp`.`sequence` ASC';
   
      $result = db_query( $query );
      return db_result_array_full($result);
   
   }
    
   static function get_page_places( $list = array() ) {
      
      $query = 'SELECT `glp`.`place_name`, `glp`.`script`, `glp`.`type`, `glp`.`logged`, `glp`.`sequence`
             FROM ' . TBL_GLOBAL_TEMPLATE_PLACES . ' glp WHERE enabled="1" and type!="COM"
             ORDER BY `glp`.`sequence` ASC';

      $result = db_query( $query );
      $ret_tmp = db_result_array_full($result);
      
      $list_places = array_flip($list);
      $list_places['component_html'] = array( array('script' => '', 'type' => 'COM') );
      
      foreach($ret_tmp as $place ) {
         if( !isset($list_places[$place['place_name']]) ) {
            continue;
         } elseif( !is_array($list_places[$place['place_name']]) ) {
            $list_places[$place['place_name']] = array();
         }
         $list_places[$place['place_name']][] = $place;
      }
          
      return $list_places;
   }

}

?>