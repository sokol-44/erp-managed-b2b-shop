<?php
/**
 * init.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

//deactivate magic GPC
if( function_exists('get_magic_quotes_runtime') && get_magic_quotes_runtime() ) {
   // Deactivate
   if( function_exists('set_magic_quotes_runtime') )
   set_magic_quotes_runtime(false);
}

/**
 * root for php scripts constant
 */
define('_I_ROOT_DIR', '..');

/**
 * set init for blocking direct access
 */
define('_I_INIT', 'YES');
/**
 * define short directory separatos
 */
define('DS', DIRECTORY_SEPARATOR );

/**
 * Include global configuration file
 * Loading and setting configuration variables
 */
include(_I_ROOT_DIR . DS . 'config.php');

/**
 * Include global functions files:
 * database, session and other helpers
 */
include(DIR_INC_FUNCTIONS . DS . 'database.php');
include(DIR_INC_FUNCTIONS . DS . 'session.php');
include(DIR_INC_FUNCTIONS . DS . 'global.php');

/**
 * Include global classes files:
 * database, session and other helpers
 */
include(DIR_INC_CLASSES . DS . 'Data_Picture.php');
include(DIR_INC_CLASSES . DS . 'Data_Order.php');
include(DIR_INC_CLASSES . DS . 'Data_Basket.php');
include(DIR_INC_CLASSES . DS . 'Data_Products.php');
include(DIR_INC_CLASSES . DS . 'Data_Rights.php');
include(DIR_INC_CLASSES . DS . 'Data_Person.php');
include(DIR_INC_CLASSES . DS . 'Data.php');
include(DIR_INC_CLASSES . DS . 'Person.php');
include(DIR_INC_CLASSES . DS . 'Html.php');
include(DIR_INC_CLASSES . DS . 'Framework_Data.php');
include(DIR_INC_CLASSES . DS . 'Framework.php');
include(DIR_INC_CLASSES . DS . 'Info.php');
include(DIR_INC_CLASSES . DS . 'SplitPage.php');
include(DIR_INC_CLASSES . DS . 'Page.php');
include(DIR_INC_CLASSES . DS . 'Lang.php');
include(DIR_INC_CLASSES . DS . 'Shopping_Basket.php');
include(DIR_INC_CLASSES . DS . 'Shopping_Basket_Chain.php');
include(DIR_INC_CLASSES . DS . 'Price.php');
include(DIR_INC_CLASSES . DS . 'Breadcrumbs.php');
include(DIR_INC_CLASSES . DS . 'BackTrail.php');
include(DIR_INC_CLASSES . DS . 'Order.php');
include(DIR_INC_CLASSES . DS . 'Mail.php');
include(DIR_INC_CLASSES . DS . 'Mail2Send.php');
include(DIR_INC_CLASSES . DS . 'Rights.php');
include(DIR_INC_CLASSES . DS . 'Soap_Server_class.php');
include(DIR_INC_CLASSES . DS . 'Soap_Server_worker.php');
include(DIR_INC_CLASSES . DS . 'Soap_Server.php');
include(DIR_INC_CLASSES . DS . 'ArrayToXML.php');

/**
 * initialize engine
 */
db_init();
session_start();
gl_init();

/**
 * Load data from session
 */
if ( function_exists('ini_get') && (ini_get('register_globals') == false) ) {
   extract($_SESSION, EXTR_OVERWRITE+EXTR_REFS);
}

$F = new Framework();
$Page = new Page();
$Lang = new Lang();

/**
 * session objects
 */
$session_object = array('P' => 'Person', 'Info' => 'Info',
		'Shopping_Basket_Chain' => 'Shopping_Basket_Chain', 'BackTrail' => 'BackTrail');

foreach($session_object as $var_name => $class_name ) {
   if ( !session_check($var_name) || !is_object(${$var_name}) ) {
      ${$var_name} = new ${class_name}();
      session_put($var_name);
   }
}

/*
 * Needs Person
 */
$Data = new Data();


/*
 * TMP
 */
//
//$Shopping_Basket->update_person();
//$Shopping_Basket->restore_contents_db();

?>