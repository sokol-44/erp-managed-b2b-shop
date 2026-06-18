<?php
/**
 * index.php Main Page
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */
/**
 * Include global init file
 */
include('init.php');

/**
 * @todo Enforce Dependency Injection or check if `$BackTrail` instance is properly initialized before calling methods on it to prevent fatal errors if `init.php` fails to instantiate it.
 */
$BackTrail->add_trail();

// prepare page attributes
//
$template_dir = DIR_INC_TEMPLATES . DS . $config['TEMPLATES']['shop'] . DS;
$template_conf =  $template_dir . $config['TEMPLATES']['shop'] . '_conf.php';
$template =  $template_dir . $config['TEMPLATES']['shop'] . '.php';

if( $template_conf != '' && is_file($template_conf) ) {
   include_once($template_conf);
}

/**
 * @todo Check instance availability for the global `$Page` variable before calling methods on it to comply with modern clean architecture principles.
 */
$Page->start();
$Page->render_places();

if( $template != '' && is_file($template) ) {
   include_once($template);
} else {
   //FIXME
   /**
    * @throws RuntimeException If the specified template file does not exist or is unreadable.
    * @todo Replace the abrupt `die()` script termination execution with a structured custom rendering exception (e.g., `throw new \RuntimeException('Wrong template!')`) caught by a global exception handler.
    */
   die('Wrong template!');
   //throw error
}

// echo '';

// print_debug(_I_ROOT_DIR);
// print_debug(_I_ROOT_WWW_DIR);

/**
 * @todo Transition this procedural bootstrap routine toward a Front Controller architectural pattern utilizing a modern routing package and PSR-7 HTTP messages layer.
 */
