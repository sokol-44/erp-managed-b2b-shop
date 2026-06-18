<?php
/**
 * init.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

//deactivate magic GPC
/**
 * @todo Remove magic quotes deactivation entirely. Functions `get_magic_quotes_runtime()` and `set_magic_quotes_runtime()` were deprecated in PHP 5.3 and completely removed in PHP 7.0.
 */
if( function_exists('get_magic_quotes_runtime') && get_magic_quotes_runtime() ) {
   // Deactivate
   if( function_exists('set_magic_quotes_runtime') )
   set_magic_quotes_runtime(false);
}

/**
 * root for php scripts constant
 */
    if( isset($_SERVER['DOCUMENT_ROOT']) ) $root = $_SERVER['DOCUMENT_ROOT'];
elseif( isset($_SERVER['SCRIPT_FILENAME']) ) $root = pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_DIRNAME);
elseif( isset($_SERVER['PATH_TRANSLATED']) ) $root = pathinfo($_SERVER['PATH_TRANSLATED'], PATHINFO_DIRNAME);
elseif( isset($_ENV['HOME']) ) $root = $_ENV['HOME'];
else   $root = '.';
define('_I_ROOT_DIR', $root);
unset($root);

/**
 * root for www constant
 * @todo Sanitize or replace fallback logic relying on non-standard headers like `SCRIPT_URL` and `REQUEST_URI` to prevent unexpected pathing setups, transitioning to framework-driven request abstractions.
 */
    if( isset($_SERVER['SCRIPT_URL']) ) $root_www = pathinfo($_SERVER['SCRIPT_URL'], PATHINFO_DIRNAME);
elseif( isset($_SERVER['SCRIPT_NAME']) ) $root_www = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);
elseif( isset($_SERVER['PHP_SELF']) ) $root_www = $_SERVER['PHP_SELF'];
elseif( isset($_SERVER['REQUEST_URI']) ) $root_www = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_DIRNAME);
else   $root_www = '/';
define('_I_ROOT_WWW_DIR', $root_www);
unset($root_www);


/**
 * set init for blocking direct access
 */
define('_I_INIT', 'YES');

/**
 * define short directory separators
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
 *
 * @todo Migrated global structural function files into PSR-4 compliant namespaced utility classes or service providers.
 */
include(DIR_INC_FUNCTIONS . DS . 'database.php');
include(DIR_INC_FUNCTIONS . DS . 'session.php');
include(DIR_INC_FUNCTIONS . DS . 'global.php');

/**
 * Include global classes files:
 * database, session and other helpers
 *
 * @todo Implement a standard PSR-4 Composer Autoloader (`vendor/autoload.php`) to completely replace this long list of hardcoded manual `include` commands.
 */
include(DIR_INC_CLASSES . DS . 'Data_Contact.php');
include(DIR_INC_CLASSES . DS . 'Data_Article.php');
include(DIR_INC_CLASSES . DS . 'Data_Picture.php');
include(DIR_INC_CLASSES . DS . 'Data_Shop.php');
include(DIR_INC_CLASSES . DS . 'Data_Order.php');
include(DIR_INC_CLASSES . DS . 'Data_Basket.php');
include(DIR_INC_CLASSES . DS . 'Data_Product.php');
include(DIR_INC_CLASSES . DS . 'Data_Category.php');
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
include(DIR_INC_CLASSES . DS . 'Shopping_Basket_Favorite.php');
include(DIR_INC_CLASSES . DS . 'Price.php');
include(DIR_INC_CLASSES . DS . 'Breadcrumbs.php');
include(DIR_INC_CLASSES . DS . 'BackTrail.php');
include(DIR_INC_CLASSES . DS . 'Order.php');
include(DIR_INC_CLASSES . DS . 'Order_Chain.php');
include(DIR_INC_CLASSES . DS . 'Order_History.php');
include(DIR_INC_CLASSES . DS . 'Product.php');
include(DIR_INC_CLASSES . DS . 'Invoice.php');
include(DIR_INC_CLASSES . DS . 'Mail.php');
include(DIR_INC_CLASSES . DS . 'Mail2Send.php');
include(DIR_INC_CLASSES . DS . 'Rights.php');
include(DIR_INC_CLASSES . DS . 'Article.php');
include(DIR_INC_CLASSES . DS . 'Contact.php');
include(DIR_INC_CLASSES . DS . 'Shop.php');


/**
 * initialize engine
 */
db_init();
session_start();
gl_init();

/**
 * Load data from session
 *
 * @todo Remove this code injection routine block entirely. The `register_globals` directive was completely removed in PHP 5.4. Unchecked variable extraction from session arrays using variable references introduces severe global state pollution and security liabilities.
 */
if ( function_exists('ini_get') && (ini_get('register_globals') == false) ) {
   extract($_SESSION, EXTR_OVERWRITE+EXTR_REFS);
}

$F = new Framework();
$Page = new Page();
$Lang = new Lang();
$Rights = new Rights();

/**
 * session objects
 */
$session_object = array('P' => 'Person', 'Info' => 'Info',
		'Shopping_Basket_Chain' => 'Shopping_Basket_Chain', 'BackTrail' => 'BackTrail');



foreach($session_object as $var_name => $class_name ) {
//      echo $var_name.' '.(!session_check($var_name)?'t':'f').' o:'.(!is_object(${$var_name})?'t':'f')."<br>\n";
   if ( !session_check($var_name) || !is_object(${$var_name}) ) {
      ${$var_name} = new ${class_name}();
      //echo "$var_name => $class_name";
      session_put($var_name);
   }
}

/*
 * Needed for Person load
 */
$Data = new Data();


/*
 * TMP
 */
//
//$Shopping_Basket->update_person();
//$Shopping_Basket->restore_contents_db();
