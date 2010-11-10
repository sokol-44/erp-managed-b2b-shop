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


}

?>