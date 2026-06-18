<?php
/**
 * session.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 */

if( !defined('_I_INIT') ) die();

/**
 * Copies targeted global variables into the native PHP $_SESSION superglobal registry array map.
 *
 * Iterates over a dynamic list of parameter name keys to mirror their data configurations.
 *
 * @param string ...$args Dynamic list of string key identifiers to copy into session tracking.
 * @return void
 * @todo Eliminate reliance on global storage mutation design patterns ($GLOBALS) in favor of decoupled state parameters.
 * @todo Add proper rest parameter type hints (`string ...$args`) and return type declaration (`void`).
 */
function session_put(){
   $args = func_get_args();
   foreach ($args as $key){
      $_SESSION[$key]=$GLOBALS[$key];
   }
}

/**
 * Validates if a specified identification key is mapped and populated inside active session collections.
 *
 * @param string $key Targeted session dictionary lookup key name descriptor.
 * @return bool True if key exists inside the session registry array context, false otherwise.
 * @todo Implement explicit type declaration metrics (`string $key`) and scalar return type hint (`bool`).
 */
function session_check($key){
   return isset($_SESSION[$key]);
}

/**
 * Safely unsets and eliminates specified storage parameters tracked inside active session frames.
 *
 * @param string $key Session dataset reference label context to destroy.
 * @return void
 * @todo Introduce strict type scalar parameters and void return declarations.
 */
function session_clear($key){
   unset($_SESSION[$key]);
}

/**
 * Directly polls underlying session storage backends to determine if a specific key mapping actively exists.
 *
 * Skips processing pipelines if configuration presets indicate non-database driver paradigms.
 *
 * @param string $key Target database session hash sequence token key identity to test.
 * @return bool True if record maps validly within data tables, false otherwise.
 * @todo Refactor implicit type dependencies to strict type standards validation parameters.
 */
function session_check_exist($key) {
   if ($GLOBALS['config']['global']['session_store'] != 'mysql') return false;
   if(empty($key)) return false;

   $ses = _sess_read($key);
   if( $ses ) return true;
   else return false;
}

if ($GLOBALS['config']['global']['session_store'] == 'mysql') {

   /**
    * Session open storage driver wrapper hook.
    *
    * @param string $save_path Standard physical runtime data serialization save path.
    * @param string $session_name Identified target registration engine marker descriptor.
    * @return bool True to fulfill operational connection criteria parameters.
    */
   function _sess_open($save_path, $session_name) {
      return true;
   }

   /**
    * Session close storage driver wrapper hook.
    *
    * @return bool True to fulfill operational connection criteria parameters.
    */
   function _sess_close() {
      return true;
   }

   /**
    * Queries the custom session database schema to retrieve encoded parameter maps.
    *
    * Automatically marks expired database entry indexes as inactive when data lifetime constraints are breached.
    *
    * @param string $key Session hash code identifier token string context.
    * @return string|false Enriched serialized payload text parameters string, or false if parsing failure states emerge.
    * @todo Fix structural defects where unparameterized table criteria inputs trigger SQL Injection exposure vectors.
    * @todo Replace risky structural text format heuristics (`substr_count` bracket testing matches) with robust error-proof serialization standards.
    */
   function _sess_read($key) {
      $value_res = db_query("select value, expiry from " . TBL_CORE_SESSIONS . " where sesskey = '" . db_escape($key) . "'  and active = '1'");
      if( db_rows($value_res) ) {
         $value = db_fetch_array($value_res);
         if( $value['expiry'] < time() ) {
            $expiry = time() +  $GLOBALS['config']['global']['session_active'];
            db_query("update " . TBL_CORE_SESSIONS . " set active = '0', sesskey = CONCAT(sesskey, '_old_', init) where active = '1' and expiry < '" . time() . "'");
            return false;
         } else {
            if ( isset($value['value']) &&
            ( substr_count($value['value'], '{') == substr_count($value['value'], '}') ) ) {
               return $value['value'];
            }
         }
      }
      return false;
   }

   /**
    * Commits active state configurations maps onto persistent database collections using unique duplicate key update workflows.
    *
    * @param string $key Unique storage identity code reference token string target.
    * @param string $val Serialized active user parameters mapping tracking values context.
    * @return resource|false Returns query result payload handle tracking metrics information maps.
    * @todo Fix major logical bug where an undefined variable `$value` is appended instead of payload parameter context `$val`.
    * @todo Avoid premature pipeline disruptions (`db_close()`) occurring directly inside internal transactional operational cycles.
    */
   function _sess_write($key, $val) {
      $expiry = time() +  $GLOBALS['config']['global']['session_active'];
      //MySQL
      return db_query("insert into " . TBL_CORE_SESSIONS . " (sesskey, active, init, expiry, value) values		('" . db_escape($key) . "', '1', '" . time() . "', '" . db_escape($expiry) . "', '" . db_escape($value) . "')		on duplicate key update expiry = '" . db_escape($expiry) . "', value = '" . db_escape($val) . "'");
      //PostgreSQL
      //return db_query("select save_session('" . db_escape($key) . "', " . db_int($expiry) . ", '" . db_escape($val) . "');");      db_close();   }

   /**
    * Marks a specific consumer session reference record token as disabled inside storage indexes.
    *
    * @param string $key Session transaction token registration signature key to neutralize.
    * @return resource|false Result data payload configurations statement handle context.
    */
   function _sess_destroy($key) {
      //return tep_db_query("delete from " . TBL_CORE_SESSIONS . " where sesskey = '" . db_escape($key) . "'");      return db_query("update " . TBL_CORE_SESSIONS . " set active = '0' where sesskey = '" . db_escape($key) . "'");   }

   /**
    * Garbage collection interface purging dead configuration variables entries and outmoded historical traces.
    *
    * Renames outdated operational keys to old reference formats, then executes structural deletions of expired database partitions.
    *
    * @param int $maxlifetime Maximum permissible time metric threshold index configuration value settings.
    * @return bool True on successful completion of pipeline sanitation cycles.
    */
   function _sess_gc($maxlifetime) {
      //tep_db_query("delete from " . TBL_CORE_SESSIONS . " where expiry < '" . time() . "'");      $purge = time() - $GLOBALS['config']['global']['session_purge'];
      db_query("update " . TBL_CORE_SESSIONS . " set active = '0', sesskey = CONCAT(sesskey, '_old_', init) where expiry < '" . time() . "' and active = '1'");
      db_query("delete from " . TBL_CORE_SESSIONS . " where active = '0' and expiry < '" . $purge . "'");
      return true;
   }

   session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
}
?>