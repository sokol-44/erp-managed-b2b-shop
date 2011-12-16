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
$Page->start();
$Page->render_places();
//$M->create($Page);

//include menu part of page
//if( $F->not_null($Page->com_model_inc) ) include($Page->com_model_inc);

//$M->add_to_page();

//display it in template
$template_dir = DIR_INC_TEMPLATES . DS . $config['TEMPLATES']['shop'] . DS;
$template =  $template_dir . $config['TEMPLATES']['shop'] . '.php';

if( $template != '' && is_file($template) ) {
   include_once($template);
} else {
   //FIXME
   die('Wrong template!');
   //throw error
}

//print_debug($Shopping_Basket_Chain);
print_debug($query_log);
//print_debug($BackTrail);
//print_debug($Page);
print_debug($F);
print_debug($P);
//print_debug($Info);
//print_debug(Lang::$STR);
print_debug($_GET);
?>