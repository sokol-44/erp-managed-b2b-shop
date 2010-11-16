<?php
/**
 * Data.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();


function db_init($config_db = false, $link = 'db_link') {
   global $$link;

   if( !$config_db )
   $config_db = $GLOBALS['config']['DB'];

   if ($config_db['plink'] == 'true') {
      $$link = mysql_pconnect($config_db['server'], $config_db['username'], $config_db['password']);
   } else {
      $$link = mysql_connect($config_db['server'], $config_db['username'], $config_db['password']);
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

function db_close($link = 'db_link') {
   global $$link;
   global $transaction_count;

   if( mysql_ping(${$link}) ) {
      if( $transaction_count > 0 ) db_transaction_end($link);
      mysql_close(${$link});
   }
}

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
         $res = db_query($query, $link);
      }
   } elseif ($action == 'UPDATE') {
      if( is_array($where) ) $where = db_unroll_conditions($where);
      $query = 'update ' . $table . ' set ' . implode(', ',  $query_array) . ' where ' . $where;
   } else {
      die();
   }

   return db_query($query, $link);
}

function db_transaction_start($link = 'db_link') {
   global $$link;
   global $transaction_count;
   db_query('SET AUTOCOMMIT=0;', $link);
   db_query('START TRANSACTION;', $link);
   $transaction_count++;
}

function db_transaction_end($link = 'db_link') {
   global $$link;
   global $transaction_count;
   db_query('COMMIT;', $link);
   db_query('SET AUTOCOMMIT=1;', $link);
   $transaction_count--;
}

function db_unroll_conditions($conditions_array, $type = 'and') {

   $return_str = '';
   if( is_array($conditions_array) ) {
      foreach( $conditions_array as $attr => $val ) {
         $return_array[] = $attr . '=\'' . $val . '\'';
      }
      $return_str = implode(' ' . $type . ' ', $return_array);
   } else {
      return '';
   }

   return $return_str;
}

function db_insert_id($link = 'db_link') {
   global $$link;
   return mysql_insert_id(${$link});
}

function db_int($value){
   if( strtoupper($value) == 'NULL' ) return 'null';
   else return (int)$value;

}

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

function db_query($query, $link = 'db_link') {
   global $$link;
   global $query_log;

   $result = mysql_query($query, $$link) or
   db_error($query, mysql_errno(), mysql_error(), debug_backtrace(), $$link);

   if (defined('DEBUG_DB_QUERIES') && (DEBUG_DB_QUERIES == 'true')) {
      $dbg = debug_backtrace();
      $query_log[] = array ('q' => $query, 'f' => $dbg[0]['file'], 'l' => $dbg[0]['line'], 't' => microtime(TRUE));
   }

   $db_result = $result;
   return $result;
}

function db_rows($result, $link = 'db_link') {
   global $$link;

   if ( is_resource($result) ) {
      return mysql_num_rows($result);
   } else {
      return 0;
   }
}


function db_error($sql_query, $errno, $error, $debug_backtrace = array(), $link = 'db_link') {
   global $$link;

   if( defined('TBL_CORE_DB_ERRORS') ) $table = TBL_CORE_DB_ERRORS;
   elseif( isset($GLOBALS['config']['TABLES']['CORE_DB_ERRORS']) && $GLOBALS['config']['TABLES']['CORE_DB_ERRORS'] != '')
   $table = $GLOBALS['config']['TABLES']['CORE_DB_ERRORS'];
   else die('DB fatal error (1)');

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
      echo 'DB fatal error (2)';
   }

   if (defined('DEBUG_DB_QUERIES') && (DEBUG_DB_QUERIES == 'true')) {
      echo '<p align="left">ERROR:' . "$errno $error<br>\r\n" .
      'QUERY:' . htmlspecialchars($sql_query) . "<br>\r\n" .
      'BACKTRACE:' . str_replace('  ', '&nbsp;', nl2br(print_r($debug_backtrace, true))) . "<br>\r\n" .
      '</p>';
   }

   //FIXME - don't die - propagate error - to other
   die('DB fatal error (SUCCESS)');
}

function db_result_array($result, $link = 'db_link') {
   global $$link;

   $result_data = array();
   if( db_rows($result)>0 ) {
      while( $row = db_fetch_array($result, '', $link) ) $result_data[] = $row;
   }

   return $result_data;
}

function db_fetch_array($result, $idx = '', $link = 'db_link') {
   global $$link;

   if( $idx != '' )
   mysql_data_seek($result, $idx) or
   db_error('_DATA SEEK', mysql_errno(), mysql_error(), debug_backtrace());

   $result_data = mysql_fetch_array($result, MYSQL_ASSOC);

   //echo mysql_stat($$link);

   return $result_data;
}

function db_fetch_result($name, $idx = 0) {
   global $$link;
   return mysql_result($result, (int)$idx, $name);
}
?>