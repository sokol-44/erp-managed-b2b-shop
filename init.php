<?php
/**
 * init.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Micha� Soko�owski <msokolowski@example.com>
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
define('_I_ROOT_DIR', '.');

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
db_init();

include(DIR_INC_FUNCTIONS . DS . 'session.php');
session_start();

include(DIR_INC_FUNCTIONS . DS . 'global.php');
gl_init();

/**
 * Include global classes files:
 * database, session and other helpers
 */
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


$F = new Framework();
$Page = new Page();
$Data = new Data();
?>