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
define('_I_ROOT_DIR', '../..');
define('_I_ADM_ROOT_DIR', '..');
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
include(DIR_INC_CLASSES . DS . 'Data_Basket.php');
include(DIR_INC_CLASSES . DS . 'Data_Products.php');
include(DIR_INC_CLASSES . DS . 'Data_Rights.php');
include(DIR_INC_CLASSES . DS . 'Data_Person.php');
include(DIR_INC_CLASSES . DS . 'Data.php');
include(DIR_INC_CLASSES . DS . 'Person.php');
include(DIR_INC_CLASSES . DS . 'Html.php');
include(DIR_INC_CLASSES . DS . 'Framework_Data.php');
include(DIR_INC_CLASSES . DS . 'Framework.php');
include(DIR_INC_CLASSES . DS . 'Lang.php');

/**
 * Include local classes files:
 * page
 */
include(DIR_ADM_INC_CLASSES . DS . 'Page.php');
include(DIR_ADM_INC_CLASSES . DS . 'Menu.php');
include(DIR_ADM_INC_CLASSES . DS . 'Info.php');
include(DIR_ADM_INC_CLASSES . DS . 'SplitPage.php');

/**
 * initialize engine
 */
db_init();
session_start();
gl_init();

if ( function_exists('ini_get') && (ini_get('register_globals') == false) ) {
   extract($_SESSION, EXTR_OVERWRITE+EXTR_REFS);
}

$F = new Framework();
$M = new Menu();
$Page = new Page();
$Data = new Data();
$Lang = new Lang();

/**
 * session objects
 */
if ( !session_check('P') || !is_object($P)) {
   $P = new Person();
   session_put('P');
}

if ( !session_check('Info') || !is_object($Info)) {
   $Info = new Info();
   session_put('Info');
}
?>