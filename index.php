<?php
/**
 * index.php Main Page
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */
/**
 * Include global init file
 */
include('init.php');

$BackTrail->add_trail();

// prepare page attributes
//
$template_dir = DIR_INC_TEMPLATES . DS . $config['TEMPLATES']['shop'] . DS;
$template_conf =  $template_dir . $config['TEMPLATES']['shop'] . '_conf.php';
$template =  $template_dir . $config['TEMPLATES']['shop'] . '.php';

if( $template_conf != '' && is_file($template_conf) ) {
   include_once($template_conf);
}

$Page->start();
$Page->render_places();

if( $template != '' && is_file($template) ) {
   include_once($template);
} else {
   //FIXME
   die('Wrong template!');
   //throw error
}

// echo '<!--';
//print_debug($Shopping_Basket_Chain);
// print_debug($_SESSION);
print_debug($_COOKIE);
print_debug(Lang::$STR);
print_debug($query_log);
//print_debug($BackTrail);
//print_debug($Page);
// print_debug($F);
// print_debug($P);
//print_debug($Info);
//print_debug($_GET);
// $Info->reset();
// echo '-->';

// print_debug(_I_ROOT_DIR);
// print_debug(_I_ROOT_WWW_DIR);
?>