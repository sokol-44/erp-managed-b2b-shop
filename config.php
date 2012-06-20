<?php
/**
 * init.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

define('NONE', false);

//loading config file into array
$config = parse_ini_file('config.ini', true);

if( $config === FALSE ) {
	mail('msokolowski@example.com', 'Fatal Error: config_init', "\n\n" . addslashes( serialize($_SERVER) ));
	die('Fatal Config Error!');
}

//DB table names
foreach( $config['TABLES'] as $cnf_table => $local_table) {
	define('TBL_' . $cnf_table, $local_table);
}

//COM names
foreach( $config['CFG_COM'] as $cnf_com => $com_name) {
	define('CFG_COM_' . $cnf_com, $com_name);
}

//directorie structure
foreach( $config['DIRECTORIES'] as $cnf_dir => $local_dir) {
	define('DIR_INC_' . $cnf_dir, _I_ROOT_DIR . DS . str_replace('#', DS, $local_dir) );
}

//define constans for admin
if( defined('_I_ADM_ROOT_DIR') ) {
   foreach( $config['ADMIN_DIRECTORIES'] as $cnf_dir => $local_dir) {
      define('DIR_ADM_INC_' . $cnf_dir, _I_ADM_ROOT_DIR . DS . str_replace('#', DS, $local_dir) );
   }

   $dir_www = $config['ADMIN_WWW'];
} else {
   $dir_www = $config['WWW'];
}

//image structure
foreach( $config['IMAGE'] as $cnf => $value) {
	define('IMAGE_' . $cnf, $value);
}


//image structure
foreach( $config['DEFAULT'] as $cnf => $value) {
	define('DEFAULT_' . $cnf, $value);
}

$dir_www_inc = $dir_www['HTTP_SERVER'] . $dir_www['HTTP_ROOT_CATALOG'] . $dir_www['HTTP_INC_CATALOG'];

define('URL_HTTP_SERVER', $dir_www['HTTP_SERVER']);
define('URL_FULL', $dir_www['HTTP_SERVER'] . $dir_www['HTTP_ROOT_CATALOG']);

define('DIR_HTTP_ROOT_CATALOG', $dir_www['HTTP_ROOT_CATALOG']);
define('DIR_WWW_CSS', $dir_www_inc . $dir_www['HTTP_CSS_CATALOG']);
define('DIR_WWW_JS', $dir_www_inc . $dir_www['HTTP_JS_CATALOG']);
define('DIR_WWW_IMG', $dir_www_inc . $dir_www['HTTP_IMG_CATALOG']);
define('DIR_WWW_TEMPLATE', $dir_www_inc . $dir_www['HTTP_TEMPLATES_CATALOG']);
define('DIR_LOCAL_CSS',  '..\\' . $dir_www['HTTP_INC_CATALOG'] . $dir_www['HTTP_CSS_CATALOG']);
define('DIR_LOCAL_JS', '..\\' . $dir_www['HTTP_INC_CATALOG'] . $dir_www['HTTP_JS_CATALOG']);
define('DIR_LOCAL_IMG', $dir_www['HTTP_INC_CATALOG'] . $dir_www['HTTP_IMG_CATALOG']);

unset($dir_www, $dir_www_inc);

define('DEBUG_DB_QUERIES', 'true');
define('DEFAULT_PAGE', 'index.php');
define('DEFAULT_COM', 'main');
define('NL', "\r\n");
?>