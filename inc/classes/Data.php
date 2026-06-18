<?php
/**
 * Data.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 *
 * @todo Eliminate legacy conditional file guard initialization vectors (`_I_INIT`) and shift architecture toward PSR-4 namespaces.
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Data
 *
 * Primary repository entry point executing data orchestration workloads for the application.
 * Coordinates system-wide persistence operations by extending the specialized `Data_Person` core tracking layer.
 *
 * @designPattern Repository
 *
 * @todo Transition global state data variables away from static class scopes to instance properties injected dynamically via dependency injection container workflows.
 * @todo Migrate to PSR-4 namespaced structure and adopt modern PDO or Doctrine DBAL infrastructure instead of global procedural db_* functions.
 */
class Data extends Data_Person {
   /**
    * @var array Temporary query result matrix processing pipeline records.
    */
   static $result_array;

   /**
    * @var int Size monitoring context index reflecting captured record metrics.
    */
   static $result_size;

   /**
    * @var mixed General parameters capturing active tracking states.
    */
   static $page;

   /**
    * @var mixed Reference value detailing current component processing scope indicators.
    */
   static $com;

   /**
    * Data Constructor.
    *
    * Instantiates the core data access mapping framework and sets up underlying layer properties.
    */
   function __construct() {
      parent::__construct();
   }

   /**
    * Extracts all translation definitions mapping to an optional specific application system component context.
    *
    * @param string $com_str Target name parameter identifier string pointing to component domains.
    * @return array Matrix set collection capturing matched raw linguistic text strings.
    * @throws \RuntimeException If the underlying database execution encounters an error.
    *
    * @todo Refactor procedural direct references such as `db_query`, `db_escape`, and global constants `TBL_CORE_TRANSLATION` into decoupled database adapters.
    * @todo Type-hint string argument and specify array structure shape in return type-doc block.
    */
   static function get_translation_all($com_str) {
      $query = 'select definition, translation from ' . TBL_CORE_TRANSLATION . '
          where com is NULL or com = "' . db_escape($com_str) . '"';
      return db_result_array( db_query($query) );
   }

   /**
    * Fetches a localized language term record mapping structural key properties against defined components.
    *
    * @param string $com_str Targeting application context identification strings.
    * @param string $name Label identifier term sequence representing target textual translations.
    * @return array Multi-dimensional array collection breakdown representing payload node elements.
    * @throws \RuntimeException If database execution encounters an error.
    *
    * @todo Upgrade parameters and return signatures to scalar native data types.
    * @todo Implement parameterized prepared queries to eliminate security risks related to direct query interpolation.
    */
   static function get_translation($com_str, $name) {
      $query = 'select definition, translation from ' . TBL_CORE_TRANSLATION . '
          where com = "' . db_escape($com_str) . '" and definition = "' . db_escape($name) . '"';
      return db_fetch_array( db_query($query) );
   }

   /**
    * Validates user account parameters over administrative and base client schemas.
    *
    * @param string $login Authentication text identity parameters used to match operational entries.
    * @return int|bool Returns the count of found database columns on success, or boolean false if empty.
    * @throws \RuntimeException If database query yields communication failures.
    *
    * @todo Replace composite structure mixed returns with unified true boolean results or dedicated Response objects.
    * @todo Refactor standard dynamic SQL UNION logic into explicit distinct query structures or specialized lookup endpoints.
    */
   static function login_in_system( $login ) {
       $query = 'select "a" as w, login from ' . TBL_GLOBAL_ADMIN . ' where login = "' . db_escape($login) . '"
               UNION ALL
               select "cu" as w, login from ' . TBL_GLOBAL_CLIENT_USER . ' where login = "' . db_escape($login) . '"';

       $result = db_query($query);

       if( db_rows($result) == 0 ) return false;
       return db_rows($result);
   }

   /**
    * Checks system registry databases to verify existence of matching email accounts.
    *
    * @param string $email String identifier containing target mailbox configurations.
    * @return int|bool Matrix record totals indicating absolute matching rows found, or false if not located.
    * @throws \RuntimeException If processing database queries hits systemic timeouts or connection limits.
    *
    * @todo Introduce proper modern strong-typing attributes and validate email layouts using standard PHP filters (`filter_var`).
    */
   static function email_in_system( $email ) {
       $query = 'select "a" as w, email from ' . TBL_GLOBAL_ADMIN . ' where email = "' . db_escape($email) . '"
               UNION ALL
               select "c" as w, email from ' . TBL_GLOBAL_CLIENT . ' where email = "' . db_escape($email) . '"
               UNION ALL
               select "cu" as w, email from ' . TBL_GLOBAL_CLIENT_USER . ' where email = "' . db_escape($email) . '"';

       $result = db_query($query);

       if( db_rows($result) == 0 ) return false;
       return db_rows($result);
   }

   /**
    * Dynamic factory resolution router parsing structural entity calls targeting specialized subclasses.
    *
    * @param string $name Internal tracking parameter pointing to programmatic data access methods.
    * @param array $arguments Parameter dataset properties tracking operational parameters.
    * @return mixed Evaluation output results matching executed callback sequences.
    */
   function __call($name, array $arguments) {
      $name_array = explode('_', $name);
      $class_name = self::get_legalsubclass($name_array['1']);

      if( !isset($this->ac[$class_name]) || !is_object($this->ac[$class_name]) ) {
         include_once('Data_' . $class_name . '.php');
         eval('$this->ac[$class_name] = new Data_' . $class_name . '();');
         $this->ac[$class_name]->$name($arguments);
      } else {
         $this->ac[$class_name]->$name($arguments);
      }
   }

   /**
    * Resolves a structural dynamic class identifier text against verified domain module parameters.
    *
    * @param string $get_legal_subclass System input class label parameter targeting data access components.
    * @return string Validated module target directory class name mapping string representation.
    *
    * @todo Fix incomplete function tracking block logic which currently references an uninitialized variable `$subclass_name`.
    * @todo Ensure this throws an InvalidArgumentException or standard custom framework target mapping exception if string resolution parameters mismatch.
    */
   static function get_legalsubclass($get_legal_subclass) {
      return $subclass_name;
   }

   /**
    * Extracts the active globally configured transactional currencies list from registry repositories.
    *
    * @return array Multi-dimensional collection matrix containing financial configuration entities.
    * @throws \RuntimeException Under SQL configuration access failures.
    *
    * @todo Fix spelling of function name: change `get_currences_list` to `get_currencies_list`.
    * @todo Refactor procedural direct database execution layers toward standardized mapping repositories.
    */
   static function get_currences_list() {
      $query = 'SELECT id_currency, currency_txt, currency_symbol FROM ' . TBL_GLOBAL_CURRENCES . ' ';
      return db_result_array( db_query($query) );
   }

   /**
    * Traverses framework error storage units to parse and echo tracked database failure items.
    *
    * @return void
    * @throws \RuntimeException Under data query resolution communication failures.
    *
    * @todo Refactor procedural display output logic away from low-level models to specialized logging blocks or Error/Exception handlers.
    * @todo Suppress direct echoing of outputs inside infrastructure models to maintain clean separation of concerns.
    */
   static function get_errors() {
      // $this->db->query("select * from t_db_errors");
      $res = db_query('select * from ' . TBL_CORE_DB_ERRORS);
      while( $row = db_fetch_array($res) ) {
         echo $row['id'] . "<br>\n";
      }

   }

   /**
    * Placeholder method segment tracking framework components routing hooks.
    *
    * @return void
    *
    * @todo Implement required logic, define signature properties, or eliminate if redundant.
    */
   static function get_com_1() {}

   /**
    * Enforces structural string validations over system-provided component context labels.
    *
    * @param string $component_name Dynamic configuration text label argument identifying components.
    * @return string Secured formatting configuration tracking verified parameters.
    *
    * @todo Complete logic implementation to actually sanitize/validate values rather than performing a simple passthrough return.
    */
   function check_com_legal($component_name) {
      return $component_name;

   }


   /**
    * Placeholder method segment tracking administration system routing interfaces.
    *
    * @return void
    *
    * @todo Implement tracking hooks or remove dead code interfaces.
    */
   function get_admin_com_1() {}

   /**
    * Extract menu data structures mapping authenticated backend administrative portal user roles.
    *
    * @return mixed Structural application navigation properties.
    *
    * @todo Implement system logic and add standardized object return schema structures.
    */
   static function get_admin_com_menu_login () {

   }

   /**
    * Enforces syntax safety verifications over internal admin component identifier labels.
    *
    * @param string $component_name Text token identification label naming targeted admin features.
    * @return string Secure verification token.
    *
    * @todo Implement validation rules to check if administration tokens fall inside safe whitelist blocks.
    */
   static function check_admin_com_legal($component_name) {
      return $component_name;

   }

   /**
    * Evaluates context configurations to return the default admin routing component label.
    *
    * @return string Standard system component destination target label.
    *
    * @todo Migrate inline configuration items ('login') onto system configuration handlers or environment variables.
    */
   static function get_admin_com_default() {
      //FIXME - some logic
      return 'login';
   }

   /**
    * Evaluates criteria parameters to determine baseline administrative user profile view models.
    *
    * @return string Admin fallback system interface marker parameter.
    *
    * @todo Refactor the hardcoded fallback routing context string directly to centralized system routing config arrays.
    */
   static function get_admin_com_login() {
      //FIXME - some logic
      return 'login';
   }


   /**
    * Assembles file physical pathway descriptors resolving internal administrative logical model modules.
    *
    * @param string $com_name Text label parameter matching the targeted administration block directory.
    * @return string Fully qualified storage directory layout string mapping internal component parameters.
    *
    * @todo Transition away from global directory path constants like `DIR_ADM_INC_MENUS` and legacy separator tokens `DS` toward path resolution systems or PSR-4 autoload workflows.
    */
   static function get_admin_com_menu_model_inc( $com_name ) {
      //FIXME - some logic
      return DIR_ADM_INC_MENUS . DS . $com_name . '_model.php';
   }

   /**
    * Compiles file storage pathway strings tracing target admin viewer templates.
    *
    * @param string $com_name Target application context directory component tag string.
    * @return string Storage directory map pointing onto physical component scripts.
    *
    * @todo Integrate a modern templating adapter layer (like Twig or Blade) instead of requiring manual procedural path script segments.
    */
   static function get_admin_com_menu_view_inc( $com_name ) {
      return DIR_ADM_INC_MENUS . DS . $com_name . '_viewer.php';
   }

   /**
    * Generates system absolute path patterns targeting core functional model components.
    *
    * @param string $com_name Target context code configuration naming structural system elements.
    * @return string Pathway string indicating functional file positions.
    *
    * @todo Encapsulate file inclusion paths using asset loaders or relative framework routing configurations.
    */
   static function get_admin_com_model_inc( $com_name ) {
      return DIR_ADM_INC_COMPONENTS . DS . $com_name . '_model.php';
   }

   /**
    * Resolves storage location paths mapping targeted functional user visualization scripts.
    *
    * @param string $com_name Administrative directory element label matching script files.
    * @return string System path pointing onto the target layout properties file.
    *
    * @todo Shift presentation architecture toward proper decoupled View components.
    */
   static function get_admin_com_viewer_inc( $com_name ) {
      return DIR_ADM_INC_COMPONENTS . DS . $com_name . '_viewer.php';
   }


   /**
    * Internal interface routing placeholder anchor tracking portal connections.
    *
    * @return void
    *
    * @todo Remove if verified as non-operational dead infrastructure code hooks.
    */
   function get_login_1() { }

   /**
    * Isolates detailed authentication parameters, user parameters, and sub-client accounts from specified table definitions.
    *
    * @param string $login Target username reference payload.
    * @param string $table Role envelope boundary name tracking query paths ('CLIENT' vs 'ADMIN').
    * @return array|bool Comprehensive profile map collection metrics, or false if values are mismatching.
    * @throws \InvalidArgumentException When unsupported table validation targets are provided.
    * @throws \RuntimeException Under standard database server error exceptions.
    *
    * @todo Refactor recursive internal joins parsing nested client relationships into optimized single data calls.
    * @todo Drop dynamic multi-type returns (array|bool) and substitute with uniform empty arrays or specific Model objects on mismatch.
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
         $res_um = db_query('select *, ' . $table_id . ' as id_table from ' . $tbl_name . ' where login = "' . db_escape($login) . '"');
         $um_arr = db_fetch_array($res_um);
         if( $table == 'CLIENT' ) {
            $res_cli = db_query('select * from ' . TBL_GLOBAL_CLIENT . ' where id_client = "' . db_int($um_arr['id_client']) . '"');
            if( db_rows($res_cli) == 1 ) {
                $um_arr['CLIENT'] = db_fetch_array($res_cli);
            } else {
                return false;
            }
         }
         return $um_arr;
      } else {
         return false;
      }
   }

   /**
    * Aggregates access privilege permission keys assigned onto accounts mapping structured system scopes.
    *
    * @param int|string $id Target system identity token record tracking administrative accounts.
    * @param string $table Structural reference domain code context parsing table pathways ('ADMIN' or 'CLIENT').
    * @return array|bool Structural dictionary containing verified user authorization permission labels.
    * @throws \RuntimeException When global permission table constant configurations missing references.
    *
    * @todo Replace direct conditional fallback configuration bands and dynamic `constant()` definitions with runtime security policies.
    * @todo Abstract permission parsing to standard RBAC/ABAC middleware tracking layers.
    */
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
          $ret_array = array('LEVEL_0' => 'LEVEL_0', 'USER' => 'USER', '');
      }

      return $ret_array;
   }

   /**
    * Evaluates pagination segment tracking integers to parse compliant sorting directions and limit bounds.
    *
    * @param int $length Scalar tracking integer mapping data length limits or inverse sorting parameters.
    * @return array Operational mapping array capturing normalized sizes, comparison parameters, and sorting strings.
    *
    * @todo Fix spelling of parameter / logic references if needed.
    * @todo Eliminate direct custom diagnostic utility calls like `add_to_fp` in favor of PSR-3 compatible generic loggers (e.g., Monolog).
    */
   static function _length_dir($length ) {
      if( (int)$length  == 0 ) $length = 2147483647;

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

   /**
    * Returns template composition access parameters mapping targeted application system component fields.
    *
    * @param string $component_name Label string pointer targeting individual template elements.
    * @return array Matrix tracking matched visualization layouts and component script states.
    * @throws \RuntimeException Under SQL structure parsing exceptions.
    *
    * @todo Transition the inline static SQL injection prevention layers to modern standard parameterized bindings.
    */
   static function get_component_rights( $component_name  ) {

      $query = 'SELECT `glp`.`place_name`, `glp`.`script`, `glp`.`type`, `glp`.`logged`, `glp`.`sequence`
             FROM ' . TBL_GLOBAL_TEMPLATE_PLACES . ' glp WHERE `enabled`="1" and `type`="COM" and
             `script` = "' . db_escape($component_name) . '" ORDER BY `glp`.`sequence` ASC';

      $result = db_query( $query );
      return db_result_array_full($result);

   }

   /**
    * Builds structural layout allocations map blocks used to assemble dynamic skin frameworks.
    *
    * @param array $list Reference matrix cataloging specific view containers to capture and align.
    * @return array Re-ordered mapping table sorting interface template layouts according to sequence priority.
    * @throws \RuntimeException If compilation logic hits core structural database processing problems.
    *
    * @todo Apply definitive typing assertions to incoming lists arrays and decouple presentation structures out of raw persistence tiers.
    */
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
