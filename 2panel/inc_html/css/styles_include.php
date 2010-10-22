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
define('_I_ROOT_DIR', '../../..');
define('_I_ADM_ROOT_DIR', '../..');

/**
 * set init for blocking direct access
 */
define('_I_INIT', 'YES');
define('_I_SHORT_INIT', 'YES');

/**
 * define short directory separatos
 */
define('DS', DIRECTORY_SEPARATOR );

$style_include = $_GET['inc'];
$style_include = preg_replace('/[^A-Za-z0-9\_\-]/','', trim($style_include));
if( $style_include != $_GET['inc'] ) die();


$style_include_filename = 'css_' . $style_include . '.css';

if( !file_exists($style_include_filename) ) die();

/**
 * Include global configuration file
 * Loading and setting configuration variables
 */
include(_I_ROOT_DIR . DS . 'config.php');

//TODO
//set data, size in heders and mayby merge files
include 'css_style_main.css';
include "$style_include_filename";
?>