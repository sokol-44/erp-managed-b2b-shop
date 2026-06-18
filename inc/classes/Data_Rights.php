<?php
/**
 * Data_Rights.php
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Data_Rights
 *
 * Manages authorization descriptors, permission lists, and access scope thresholds
 * across application user groups and administrative configurations.
 *
 * @todo Refactor instance methodologies into modern dependency-injected Security Services.
 * @todo Use explicit public/protected/private visibility modifiers for all methods.
 * @todo Add standard type hints for inputs and strict union/nullable return values.
 * @todo Migrate direct dynamic SQL strings processing to standard Prepared Statements or an ORM mapper.
 */
class Data_Rights extends Data_Category {

   /**
    * Data_Rights constructor.
    *
    * Boots up the system rights management mapping boundaries.
    *
    * @todo Remove legacy commented verification boilerplate (`//echo get_class();`).
    */
   function __construct() {
        //echo get_class();
      parent::__construct();
   }

   /**
    * Retrieves an array compilation containing system permissions filtered by a localized scope.
    *
    * @param string $scope Context isolation filter boundary target (e.g., 'CLIENT', 'ADMIN'). Defaults to an empty string.
    * @return array Matrix set displaying matched rights tracking records rows.
    *
    * @todo Fix notice crash generated if the input resolves to an empty string, causing `$where` to remain un-declared.
    */
   function get_rights_list($scope = '') {
      $F = Framework::g_global();
      if( $F->not_null( $scope ) ) $where = ' where scope = "' . db_escape($scope) . '"';

      $res = db_query('select r.id_rights, r.scope, r.name, r.description
             from ' . TBL_GLOBAL_RIGHTS . ' r' . $where);

      return db_result_array($res);

   }

   /**
    * Computes the absolute highest registration access level integer value indexed within the system.
    *
    * @return string|bool The maximum numeric level string identifier if resolved, or false if not found.
    *
    * @todo Eliminate direct dependency on string manipulation constants (e.g., hardcoded substring lengths) inside query statements.
    * @todo Replace custom string tables `global_rights` configuration lookups with explicit configuration models or constants.
    */
   function get_client_rights_max_level() {
      $res = db_query('select substring(name,7) as ml from global_rights
               where scope="CLIENT" and name like "LEVEL_%" and name!="LEVEL_99"
               order by substring(name,7) desc limit 1');
      if( db_rows($res) ) return db_fetch_result('ml', $res);
      return false;
   }


}
