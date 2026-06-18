<?php
/**
 * database.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 */

if( !defined('_I_INIT') ) die();


/**
 * Initializes and establishes a connection to the MySQL database server.
 *
 * Automatically checks configuration options, structures server strings with specified ports,
 * establishes a standard or persistent connection, chooses the database schema context,
 * and configures communication character encodings to UTF-8.
 *
 * @param array|false $config_db Optional custom configuration settings array containing server credentials. Defaults to false.
 * @param string $link The identifier name of the global variable assigned to store the connection resource handler. Defaults to 'db_link'.
 * @return resource|false Returns the established database connection resource handle on success, or false on mapping failures.
 * @todo Migrate from the deprecated, completely removed `ext/mysql` API extension to secure `PDO` or `mysqli`.
 * @todo Eliminate unsafe dynamic variable-variable connections definitions (`$$link`).
 * @todo Refactor the hardcoded port fallback constraints (`'3305'`) out of initialization routines.
 */
function db_init($config_db = false, $link = 'db_link') {
   global $$link;

   if( !$config_db )
   $config_db = $GLOBALS['config']['DB'];

   if( !empty($config_db['port']) && $config_db['port']!='3305' && (int)$config_db['port']>1024 ) {
	$server = $config_db['server'] . ':' . $config_db['port'];
   } else {
    $server = $config_db['server'];
   }

   if ($config_db['plink'] == 'true') {
      $$link = mysql_pconnect($server, $config_db['username'], $config_db['password']);
   } else {
      $$link = mysql_connect($server, $config_db['username'], $config_db['password']);
   }

   if ($$link && is_resource($$link)) {
      mysql_select_db($config_db['database']);
      if (PHP_VERSION >= '5.2.3') mysql_set_charset('UTF8');
      else 						mysql_query('SET names "UTF8"', $$link);
   } else {
   	  echo mysql_errno() . " " . mysql_error();
      return false;
   }
   return $$link;
}

/**
 * Safely terminates open transaction locks and closes an existing active database connection.
 *
 * @param string $link The identifier name of the target global connection reference key to disconnect. Defaults to 'db_link'.
 * @return void
 * @todo Replace dynamic variable references with structured connection management repositories.
 */
function db_close($link = 'db_link') {
   global $$link;
   global $transaction_count;

   if( mysql_ping(${$link}) ) {
      if( $transaction_count > 0 ) db_transaction_end($link);
      mysql_close(${$link});
   }
}

/**
 * Compiles and dispatches dynamic non-parameterized INSERT or UPDATE SQL statements from data maps.
 *
 * Sanitizes array value blocks before generating execution statements and applying conditions.
 *
 * @param string $table Target database table name string.
 * @param array $data_array Associative array dictionary mapping database column fields to unescaped input parameters.
 * @param string $action Determines data writing operations criteria ('INSERT' or 'UPDATE'). Defaults to 'insert'.
 * @param string|array $where Dynamic filtering conditions context applied on targets during updates. Defaults to ''.
 * @param string $link The connection resource handler variable identifier name. Defaults to 'db_link'.
 * @return resource|false Result payload handle returned from statement operations, or false when execution anomalies arise.
 * @todo Refactor the unparameterized structural rendering blocks to eliminate high SQL Injection vulnerabilities.
 * @todo Implement the non-functional `FIXME` block tracking unique duplicate constraints checks inside insertion passes.
 * @todo Remove the fatal `die()` routine triggered by unmapped execution parameters with structured exception components.
 */
function db_perform($table, $data_array, $action = 'insert', $where = '', $link = 'db_link') {
   global $$link;

   $query_array = array();

   foreach( $data_array as $col_name => $value) {
      switch ( strtoupper($value) ) {
         case 'NOW()':
            $query_array[] = $col_name . '=now()';
            break;
         case 'NULL':
            $query_array[] = $col_name . '=null';
            break;
         default:
            $query_array[] = $col_name . '=\'' . db_escape($value) . '\'';
            break;
      }
   }

   $action = strtoupper($action);

   if ($action == 'INSERT') {
      if( is_array($where) && Framework::not_null($where) ) {
         //INSERT on duplikate key - update
         //FIXME
      } else {
         $query = 'insert into ' . $table . ' set ' . implode(', ',  $query_array);
      }
   } elseif ($action == 'UPDATE') {
      if( is_array($where) ) $where = db_unroll_conditions($where);
      $query = 'update ' . $table . ' set ' . implode(', ',  $query_array) . ' where ' . $where;
   } else {
      die();
   }

   return db_query($query, $link);
}

/**
 * Halts auto-commit state triggers and launches a physical engine transaction isolation layer.
 *
 * @param string $link Active global database resource token reference target identity. Defaults to 'db_link'.
 * @return resource|false Returns query result status payload configurations or false when queries break.
 * @todo Replace tracking counters flags (`$transaction_count++`) with native database nested tracking mechanisms.
 */
function db_transaction_start($link = 'db_link') {
   global $$link;
   global $transaction_count;
   db_query('SET AUTOCOMMIT=0;', $link);
   $res = db_query('START TRANSACTION;', $link);
   $transaction_count++;
   return $res;
}

/**
 * Flushes database changes, ends execution barriers, and reenables automated commit rules.
 *
 * @param string $link Active global database resource token reference target identity. Defaults to 'db_link'.
 * @return resource|false Target operational results handle context.
 */
function db_transaction_end($link = 'db_link') {
   global $$link;
   global $transaction_count;
   $res = db_query('COMMIT;', $link);
   db_query('SET AUTOCOMMIT=1;', $link);
   $transaction_count--;
   return $res;
}

/**
 * Transforms an associative parameters array mapping attributes into sorted ORDER BY SQL string segments.
 *
 * @param array|false $conditions_array Key-value properties map indexing columns against ordering rules ('ASC'/'DESC'). Defaults to false.
 * @return string Safe compiled sorting segment parameters statement block, or an empty string.
 * @todo Address loose array append indicators structures triggering tracking exceptions when uninitialized.
 */
function db_unroll_sort($conditions_array = false) {

	$return_str = '';
	if( is_array($conditions_array) ) {
		foreach( $conditions_array as $attr => $val ) {
			if( Framework::not_null($val) &&
				( strtoupper($val) == 'ASC' ||  strtoupper($val) == 'DESC' )   ) {
				$return_array[] = db_escape($attr) . ' ' . $val;
			} else {
				$return_array[] = db_escape($attr);
			}
			$return_str = implode(', ', $return_array);
		}
	} else {
		return '';
	}

	return $return_str;
}

/**
 * Translates multi-dimensional logical criteria array matrices into unparameterized WHERE SQL constraint strings.
 *
 * Iterates across arrays evaluating values to match dynamic constraints like LIKE wildcards, IN bounds, NULL values, or inequalities.
 *
 * @param array $conditions_array Nested constraints mapping parameters tracking column values logic.
 * @param string $type The logical connector join clause separating independent array criteria ('and' / 'or'). Defaults to 'and'.
 * @param string|bool $field_name Optional implicit parent field fallback descriptor tag context. Defaults to false.
 * @return string Compiled WHERE statement logic segment string.
 * @todo Fix logical processing anomalies (e.g., duplicate condition flags and risky index checks like `strpos(...) == 0`).
 * @todo Transition string matching filters (e.g., `>=`) into dynamic prepared query arrays.
 */
function db_unroll_conditions($conditions_array, $type = 'and', $field_name = false) {

   $return_str = '';
   if( is_array($conditions_array) ) {
      foreach( $conditions_array as $attr => $val ) {
         if( is_array($val) ) {
            if( strtoupper($attr) == 'OR' ) {
               $return_array[] = ' (' . db_unroll_conditions($val, 'or') . ')';
            } elseif( strtoupper($attr) == 'AND' ) { //FIXME - WTF ?
               $return_array[] = ' (' . db_unroll_conditions($val, 'and') . ')';
            } else {
               $return_array[] = ' (' . db_unroll_conditions($val, 'or', $attr) . ')';
            }
         } else {
            if( Framework::not_null($field_name) ) $attr = $field_name;
            if( strtoupper(trim($val)) == 'NULL' || strtoupper(trim($val)) == 'NOT NULL') {
               $return_array[] = db_escape($attr) . ' IS ' . trim($val);
            } elseif( strpos($val, 'IN') == 0 && strpos($val, 'IN') !== FALSE ) {
            	$return_array[] = db_escape($attr) . ' ' . $val . ' ';
            } elseif( (strpos($val, '%') == 0 || strpos(strrev($val), '%') == 0 ) && strpos($val, '%') !== FALSE ) {
               $return_array[] = db_escape($attr) . ' LIKE \'' . db_escape($val) . '\'';
            } elseif( strpos(trim($val), '>=') == 0 && strpos(trim($val), '>=') !== FALSE ) {
               $return_array[] = db_escape($attr) . ' >= \'' . db_escape(trim(substr(trim($val), 2))) . '\'';
            } elseif( strpos(trim($val), '<=') == 0 && strpos(trim($val), '<=') !== FALSE ) {
               $return_array[] = db_escape($attr) . ' <= \'' . db_escape(trim(substr(trim($val), 2))) . '\'';
            } elseif( (strpos($val, '!=') == 0 && strpos($val, '!=') !== FALSE ) ||
            			 ( strpos(trim($val), '<>') == 0 && strpos(trim($val), '<>') !== FALSE ) ) {
               $return_array[] = db_escape($attr) . ' != \'' . db_escape(trim(substr(trim($val), 2))) . '\'';
            } elseif( strpos(trim($val), '=') == 0 && strpos(trim($val), '=') !== FALSE ) {
               $return_array[] = db_escape($attr) . ' = \'' . db_escape(trim(substr(trim($val), 1))) . '\'';
            } elseif( strpos(trim($val), '>') == 0 && strpos(trim($val), '>') !== FALSE ) {
               $return_array[] = db_escape($attr) . ' > \'' . db_escape(trim(substr(trim($val), 1))) . '\'';
            } elseif( strpos(trim($val), '<') == 0 && strpos(trim($val), '<') !== FALSE ) {
               $return_array[] = db_escape($attr) . ' < \'' . db_escape(trim(substr(trim($val), 1))) . '\'';
            } else {
               $return_array[] = db_escape($attr) . '=\'' . db_escape($val) . '\'';
            }
         }
      }
      $return_str = implode(' ' . $type . ' ', $return_array);
   } else {
      return '';
   }

   return $return_str;
}

/**
 * Returns the maximum unique primary index counter discovered inside a specific table.
 *
 * If no explicit column target parameter is configured, it queries table status variables
 * to capture upcoming auto-increment limits.
 *
 * @param string $table Target table name to query.
 * @param string|bool $row_name Specific target row identifier, or false to evaluate meta indicators. Defaults to false.
 * @param string|array $where Variable constraint rules array or raw query string modifier blocks. Defaults to ''.
 * @param string $link The identifier designation of the targeted storage handle resource global variable name. Defaults to 'db_link'.
 * @return int|string The calculated maximum numerical value or system attribute.
 * @todo Eliminate hardcoded table structures definitions (`"global_client_user"`) embedded inside logic fallbacks.
 */
function db_max($table, $row_name = false, $where = '', $link = 'db_link') {
   global $$link;

   $where_str = '';
   if( Framework::not_null($where) ) $where_str = ' where ' . db_unroll_conditions($where);

   $query_array = array();

   if( Framework::is_null($row_name) ) {
   	$res_ai = db_query('SHOW TABLE STATUS LIKE "global_client_user"');
   	$max = db_fetch_result('Auto_increment', $res_ai)-1;
   } else {
   	$query = 'SELECT max(`' . db_escape($row_name) . '`) as max from `' . db_escape($table) . '`' . $where_str;
   	$res_ai = db_query($query);
   	$max = db_fetch_result('max', $res_ai);
   }

   return $max;
}

/**
 * Grabs the unique numeric primary identifier generated during the most recent insertion query transaction.
 *
 * @param string $link The target database communication stream global identifier token. Defaults to 'db_link'.
 * @return int Unique row insert sequence value.
 */
function db_insert_id($link = 'db_link') {
   global $$link;
   return mysql_insert_id(${$link});
}

/**
 * Converts value attributes into standard floating-point datatypes while keeping SQL string markers.
 *
 * @param mixed $value Raw input parameter numerical target.
 * @return float|string Normalized numeric datatype or structural string value marker.
 */
function db_float($value){
   if( strtoupper($value) == 'NULL' ) return 'null';
   else {
      return (float)str_replace(',', '.', $value);
   }
}

/**
 * Enforces integer datatype transformations on inputs while keeping SQL NULL markers.
 *
 * @param mixed $value Raw variable numerical target.
 * @return int|string Transformed output integer value or structural string value marker.
 */
function db_int($value){
   if( strtoupper($value) == 'NULL' ) return 'null';
   else return (int)$value;
}

/**
 * Recursively applies string escaping transformations across nested array collections.
 *
 * @param array|string $array_in Core targets data dataset container holding items requiring escaping rules.
 * @param string $link The connection resource handle designation tag identifier name. Defaults to 'db_link'.
 * @return array|string Fully neutralized data parameter collection map or variable string.
 */
function db_escape_array($array_in, $link = 'db_link') {
   global $$link;

   $ret = array();
   if( is_array($array_in) ) {
      foreach($array_in as $key_in => $val_in) {
         if( is_array($val_in) ) {
            $ret[$key_in] = db_escape_array($val_in, $link);
         } else {
            $ret[$key_in] = db_escape($val_in, $link);
         }
      }
      return $ret;
   } else {
      return db_escape($array_in, $link);
   }
}

/**
 * Neutralizes control character strings utilizing core engine extension escapers or local system wrappers.
 *
 * @param string $string Raw target text segment containing data to format.
 * @param string $link Target database stream connection handler global variable locator name. Defaults to 'db_link'.
 * @return string Safe, modified output data text parameter.
 */
function db_escape($string, $link = 'db_link') {
   global $$link;

   $ret = '';

   if (function_exists('mysql_real_escape_string')) {
      $ret = mysql_real_escape_string($string, $$link);
   } elseif (function_exists('mysql_escape_string')) {
      $ret = mysql_escape_string($string);
   } else {
      $ret = addslashes($string);
   }

   return $ret;
}

/**
 * Executes a specific custom SQL database function, passing escaped data rows as parameters.
 *
 * @param string $name Target identifier name of the system function to call.
 * @param array $data Ordered sequential dataset values passed into parameters.
 * @param string $link Core connection resource token variable designation identifier name. Defaults to 'db_link'.
 * @return string Converted result data output context.
 */
function db_call_func($name, $data, $link = 'db_link') {
   global $$link;

   $data = db_escape_array($data);

   $data_str = '"' . implode('", "', $data) . '"';
   $sql = 'select ' . $name . '(' . $data_str . ') as func_res;';
   $res =  db_fetch_result('func_res', db_query($sql));
   return $res;
}

/**
 * Triggers a stored database procedure execution layout block, forwarding parameter maps.
 *
 * @param string $name Target name identifying the routine to trigger.
 * @param array $data Input dataset contents mapped as execution settings.
 * @param string $link Core connection resource token variable designation identifier name. Defaults to 'db_link'.
 * @return resource|false Dynamic statement results tracking descriptor.
 * @todo Finalize dynamic sorting configuration criteria parameters highlighted in architectural comments.
 */
function db_call_proc($name, $data, $link = 'db_link') {
   global $$link;

   $data = db_escape_array($data);

   // TODO kolejność, moze parametr to regulujący

   $data_str = '"' . implode('", "', $data) . '"';
   $sql = $name . '(' . $data_str . ');';
   return db_query($sql);
}

/**
 * Routes a raw unparameterized SQL command statement through to active connection handles.
 *
 * Automatically attempts lazy initialization connections when inactive handlers are monitored,
 * captures performance trace durations metrics, and redirects compilation faults towards logging engines.
 *
 * @param string $query Complete target SQL query command statement context.
 * @param string $link Core connection resource global target identity indicator. Defaults to 'db_link'.
 * @return resource|false Statement response metrics handle descriptor context.
 * @todo Modernize error tracking routines and remove unsafe conditional runtime termination overrides (`die()`).
 */
function db_query($query, $link = 'db_link') {
   global $$link;
   global $query_log;

   if( !is_resource($$link) ) db_init(false, $link);

   $tstart = microtime(true);

   $result = mysql_query($query, $$link) or
   db_error($query, mysql_errno(), mysql_error(), debug_backtrace(), $$link);

   if( mysql_errno() > 0  ) {
   	if( defined('SOAP_ENVIRONMENT') && constant('SOAP_ENVIRONMENT') ) add_to_fp("db_error:\n" . $query);
   	die('q:' . $query . ' ' . $result);
   }


   if ( ( defined('DEBUG_DB_QUERIES') && DEBUG_DB_QUERIES == 'true' ) ||
        ( isset($_GET['DEBUG_DB']) && $_GET['DEBUG_DB']=='true')
      ) {

   	$dbg = debug_backtrace();
      $l = array();
      foreach($dbg as $idx => $ar) {
      	$fl = str_replace('C:\\Users\\ms.2M\\Dropbox\\Projects\\eclipse\\workspaceB2B', '', $ar['file']);
      	if($fl == '') $l[$idx] = $ar['function'];
      	else $l[$idx] = $fl . ' (' . $ar['function'] . ') : '. $ar['line'];
      }
      // C:\Users\ms.2M\Dropbox\Projects\eclipse\workspaceB2B\B2B\inc\classes\
      //$query_log[] = array ('q' => $query, 'f' => $dbg[0]['file'], 'l' => $dbg[0]['line'],  't' => microtime(TRUE));
      // $query_log[] = array ('q' => $query, 'f0' => $dbg[0]['file'], 'l0' => $dbg[0]['line'], 'f1' => $dbg[1]['file'], 'l1' => $dbg[1]['line'], 'ts' => (microtime(TRUE)-$tstart), 'ta' => microtime(TRUE));
      $query_log[] = array ('q' => $query,  'l' => $l, 'ts' => round(microtime(TRUE)-$tstart,6), 'ta' => microtime(TRUE));
//       $query_log[] = array ('q' => $query, 'f' => $dbg[0]['file'], 'l' => $dbg[0]['line'], 't' => microtime(TRUE), 'b' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
   }

   $db_result = $result;
   return $result;
}

/**
 * Returns the exact numeric total of structural database rows held inside an execution result handle.
 *
 * @param resource $result Database execution query resource envelope handle tracking entries.
 * @param string $link Connection resource handle designation variable identity marker name. Defaults to 'db_link'.
 * @return int Total number of rows located inside results array blocks.
 */
function db_rows($result, $link = 'db_link') {
   global $$link;

   if ( is_resource($result) ) {
      return mysql_num_rows($result);
   } else {
      return 0;
   }
}

/**
 * Computes the total quantity of field rows modified during the preceding write, delete, or modify operation query context.
 *
 * @return int Quantitative count mapping rows modified by the engine.
 * @todo Fix broken side-effect dependencies where global connection tokens tracking references (`$$link`) evaluate missing contexts.
 */
function db_affected_rows() {
   global $$link;
   return mysql_affected_rows();
}

/**
 * Core exception fallback engine intercepting failed statements to register diagnostic reports.
 *
 * Gathers system arrays, active backtrace elements, and environment variables blocks,
 * logging structured diagnostic payloads into dedicated persistent tracking registers.
 *
 * @param string $sql_query Complete string context of the failed SQL query block statement.
 * @param int $errno Specific error code index returned by storage systems.
 * @param string $error Text summary explanation detailing compilation failures.
 * @param array $debug_backtrace Captured call execution state snapshots trace data. Defaults to array().
 * @param string $link The dynamic connection instance variable name string target. Defaults to 'db_link'.
 * @return string|void Returns structural system message details or aborts thread activities.
 * @todo Refactor nested copy-paste termination parameters and eliminate structural security log leakage risks.
 */
function db_error($sql_query, $errno, $error, $debug_backtrace = array(), $link = 'db_link') {
   global $$link;
   global $query_log;

   if( !is_resource($$link) ) db_init(false, $link);

   $error_data =  "\n". $sql_query . "\n" . $db_error_code . "\n" . print_r($debug_backtrace, true);

   if( defined('TBL_CORE_DB_ERRORS') ) $table = TBL_CORE_DB_ERRORS;
   elseif( isset($GLOBALS['config']['TABLES']['CORE_DB_ERRORS']) && $GLOBALS['config']['TABLES']['CORE_DB_ERRORS'] != '')
   	$table = $GLOBALS['config']['TABLES']['CORE_DB_ERRORS'];
   elseif( defined('SOAP_ENVIRONMENT') && constant('SOAP_ENVIRONMENT') ) {
     if( function_exists('add_to_fp') ) add_to_fp("DB fatal error (1)\n". $error_data);
	 return 'DB fatal error (1)'. $error_data;
   } else die('DB fatal error (1)');

   if(is_resource($$link) && substr_count($sql_query, $table)==0 ) {
      $db_date = date('Y-m-d H:m:s');
      $db_sql = db_escape($sql_query);
      $db_error_code = db_escape("$errno $error");
      $db_backtrace = db_escape(serialize($debug_backtrace));
      $db_environment = db_escape(serialize(
      array('GET' => $_GET,
						'POST' => $_POST,
						'SESSION' => $_SESSION,
						'SERVER' => $_SERVER) ) );

      $query = "insert into $table
		(      `sql`,       `error_code`, `backtrace`      , `environment`      , `date` )
		values
		(\"$db_sql\", \"$db_error_code\", \"$db_backtrace\", \"$db_environment\", \"$db_dat\")";
      $db_result = db_query( $query );
   } else {
     if( defined('SOAP_ENVIRONMENT') && constant('SOAP_ENVIRONMENT') )  {
      if( function_exists('add_to_fp') ) add_to_fp("DB fatal error (2)\n". $error_data);
	   return 'DB fatal error (2)'. $error_data;
     } else die('DB fatal error (2)');
   }

   if ( ( defined('DEBUG_DB_QUERIES') && DEBUG_DB_QUERIES == 'true' ) ||
        ( isset($_GET['DEBUG_DB']) && $_GET['DEBUG_DB']=='true')
      ) {
      echo '<p align="left">ERROR:' . "$errno $error<br>\r\n" .
      'QUERY:' . htmlspecialchars($sql_query) . "<br>\r\n" .
      'BACKTRACE:' . str_replace('  ', '&nbsp;', nl2br(print_r($debug_backtrace, true))) . "<br>\r\n" .
      '</p>';
      if( ( isset($_GET['DEBUG_DB']) && $_GET['DEBUG_DB']=='true') ) {
      	echo '<pre>'.print_r($query_log, true).'</pre>';
      }
   }

   //FIXME - don't die - propagate error - to other

   if( defined('SOAP_ENVIRONMENT') && constant('SOAP_ENVIRONMENT') )  {
     if( function_exists('add_to_fp') ) add_to_fp("DB fatal error (SUCCESS)\n". $error_data);
	 return 'DB fatal error (SUCCESS)'. $error_data;
   } else die('DB fatal error (SUCCESS)');
}

/**
 * Formats a complete multi-row database result collection, indexing row parameters by an explicit unique identifier value.
 *
 * If no explicit index field identity key parameter gets targeted, the method extracts column settings from the leading array index position.
 *
 * @param resource $result Query response tracking source data records.
 * @param string|bool $id Specific data column key identifier targeted as key map indicators. Defaults to false.
 * @param string $link Database stream reference indicator global target name string. Defaults to 'db_link'.
 * @return array Multi-dimensional associative parameters array block indexed by row key attributes.
 */
function db_result_array_full_id($result, $id = false, $link = 'db_link') {
	global $$link;

	$result_data = array();
	if( db_rows($result)>0 ) {
		while( $row = db_fetch_array($result, '', $link) ) {
			if( !$id || !isset($row[$id]) ) $id = key($row);
			$result_data[$row[$id]] = $row;
		}
	}

	return $result_data;
}

/**
 * Returns a sequential multi-dimensional associative parameters array tree containing all elements from database results.
 *
 * @param resource $result Active database statement execution handler tracking properties.
 * @param string $link Connection instance variable tag identifier string name. Defaults to 'db_link'.
 * @return array Sequential array map packing associative dataset rows.
 */
function db_result_array_full($result, $link = 'db_link') {
   global $$link;

   $result_data = array();
   if( db_rows($result)>0 ) {
      while( $row = db_fetch_array($result, '', $link) ) $result_data[] = $row;
   }

   return $result_data;
}

/**
 * Alias wrapper pointing directly onto db_result_array_full execution routines.
 *
 * @param resource $result Active database statement execution handler tracking properties.
 * @param string $link Connection instance variable tag identifier string name. Defaults to 'db_link'.
 * @return array Sequential array map packing associative dataset rows.
 */
function db_result_array($result, $link = 'db_link') {
   global $$link;

   return db_result_array_full($result, $link);
}

/**
 * Fetches a single associative data row parameters mapping dictionary out of statement execution structures.
 *
 * Allows seeking across target matrix lines through position indices settings parameters.
 *
 * @param resource $result Operational execution statement resource tracking parameters.
 * @param int|string $idx Optional matrix location line offset indicator context to target. Defaults to ''.
 * @param string $link Global database connector locator token name string. Defaults to 'db_link'.
 * @return array|false Mapping array containing row parameters string values, or false when rows resolve empty.
 */
function db_fetch_array($result, $idx = '', $link = 'db_link') {
   global $$link;

   if( $idx != '' )
   mysql_data_seek($result, $idx) or
   db_error('_DATA SEEK', mysql_errno(), mysql_error(), debug_backtrace());

   $result_data = mysql_fetch_array($result, MYSQL_ASSOC);

   //echo mysql_stat($$link);

   return $result_data;
}

/**
 * Extracts a single isolated cell context located at an intersection of rows indices and columns identifiers tags.
 *
 * @param string $name Targeted target column key property attribute designation descriptor name.
 * @param resource $result Complete dataset query statement tracking handles context.
 * @param int $idx Numerical row offset positioning setting target parameter. Defaults to 0.
 * @return string Extracted data payload string value context matching chosen cell locations.
 */
function db_fetch_result($name, $result, $idx = 0) {
   global $$link;
   return mysql_result($result, (int)$idx, $name);
}
?>