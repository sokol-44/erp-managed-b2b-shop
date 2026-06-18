<?php
/**
 * index.php Administrator Panel
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 */
/**
 * Include global init file
 */
include('init.php');


define('PERSON_TYPE', 'ADMIN');

// prepare page attributes
//
$Page->start();
$M->create($Page);


//include menu part of page
if( $F->not_null($Page->com_model_inc) ) include($Page->com_model_inc);

$M->add_to_page();

//display it in template
$filename = $config['TEMPLATES']['admin'];

if( $filename != '' && is_file($filename) ) {
   include_once($filename);
} else {
   //FIXME
   //throw error
}

print_debug($query_log);
print_debug($Page);
print_debug($F);
print_debug($P);
print_debug($Info);
?>