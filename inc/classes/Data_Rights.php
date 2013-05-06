<?php
/**
 * Data_Rights.php
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
class Data_Rights extends Data_Products {

   function __construct() {
      parent::__construct();
   }
    
   function get_rights_list($scope = '') {
      $F = Framework::g_global();
      if( $F->not_null( $scope ) ) $where = ' where scope = "' . db_escape($scope) . '"';

      $res = db_query('select r.id_rights, r.scope, r.name, r.description
         	from ' . TBL_GLOBAL_RIGHTS . ' r' . $where);

      return db_result_array($res);

   }
   
   function get_client_rights_max_level() {
      $res = db_query('select substring(name,7) as ml from global_rights
               where scope="CLIENT" and name like "LEVEL_%" and name!="LEVEL_99"
               order by substring(name,7) desc limit 1');
      if( db_rows($res) ) return db_fetch_result('ml', $res);
      return false;
   }


}

?>