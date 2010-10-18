<?php
/**
 * Data.php Global initialization file
 * Copyright MichaÅ‚ SokoÅ‚owski 2010
 *
 * @author Micha³ Soko³owski <msokolowski@example.com>
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
class Data {
   static $result_array;
   static $result_size;
   static $page, $com;



   /**
    *
    */
   function __construct() {
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
   //FIXME
   function get_legalsubclass($get_legal_subclass) {
      return $subclass_name;
   }

   function get_currences_list() {
      $query = 'SELECT id_currency, currency_txt, currency_symbol FROM ' . TBL_GLOBAL_CURRENCES . ' ';
      return db_result_array( db_query($query) );
   }

   function get_errors() {
      // $this->db->query("select * from t_db_errors");
      $res = db_query('select * from ' . TBL_CORE_DB_ERRORS);
      while( $row = db_fetch_array($res) ) {
         echo $row['id'] . "<br>\n";
      }

   }

   function get_com_1() {}

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
   function get_admin_com_menu_login () {

   }

   /**
    * Check validity of component name
    * @param string $component_name
    * @return string|valid component name
    */
   function check_admin_com_legal($component_name) {
      return $component_name;

   }

   /**
    * Get default component name
    * @return string|valid component name
    */
   function get_admin_com_default() {
      //FIXME - some logic
      return 'login';
   }

   /**
    * Get default component name
    * @return string|valid component name
    */
   function get_admin_com_login() {
      //FIXME - some logic
      return 'login';
   }


   function get_admin_com_menu_model_inc( $com_name ) {
      //FIXME - some logic
      return DIR_ADM_INC_MENUS . DS . $com_name . '_model.php';
   }

   function get_admin_com_menu_view_inc( $com_name ) {
      return DIR_ADM_INC_MENUS . DS . $com_name . '_viewer.php';
   }

   function get_admin_com_model_inc( $com_name ) {
      return DIR_ADM_INC_COMPONENTS . DS . $com_name . '_model.php';
   }

   function get_admin_com_viewer_inc( $com_name ) {
      return DIR_ADM_INC_COMPONENTS . DS . $com_name . '_viewer.php';
   }


   function get_login_1() { }

   /**
    * Return data from provided login and table
    * @param string $login
    * @param string $table
    * @return array|name
    */
   function get_login_data($login, $table) {

      if( defined('TBL_GLOBAL_' . $table) ) $tbl_name = constant('TBL_GLOBAL_' . $table);
      else return false;

      if( $tbl_name != '' ) {
         $res = db_query('select * from ' . $tbl_name . ' where login = "' . db_escape($login) . '"');
         return db_fetch_array($res);
      } else {
         return false;
      }
   }

   function get_login_rights($id, $table) {

      if( defined('TBL_GLOBAL_' . $table) ) $tbl_person = constant('TBL_GLOBAL_' . $table);
      else return false;

      if( defined('TBL_GLOBAL_RIGHTS2' . $table) ) $tbl_glue = constant('TBL_GLOBAL_RIGHTS2' . $table);
      else return false;

      if( defined('TBL_GLOBAL_RIGHTS') ) $tbl_rights = constant('TBL_GLOBAL_RIGHTS');
      else return false;

      if( $table == 'ADMIN' ) {
         $table_id = 'id_admin';
      } else if( $table == 'HOTEL' ) {
         $table_id = 'id_hotel_user';
      }

      $res = db_query('select r.* from ' .
      $tbl_person . ' p,  ' . $tbl_glue . ' gl,  ' . $tbl_rights . ' r ' .
      'where p.' . $table_id . ' = gl.' . $table_id . ' and gl.id_rights = r.id_rights and ' .
      ' p.' . $table_id . ' = "' . db_escape($id) . '"');

      $ret_array = array();
      if( db_rows($res)>0 ) {
         while( $row = db_fetch_array($res) ) $ret_array[$row['name']] = $row['name'];
   }

   return $ret_array;
}




}

?>