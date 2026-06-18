<?php
/**
 * index.php
 *
 * Loads image using a URL wrapper implemented as a PHP class.
 *
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */
/**
 * Include global init file
 */
include('init.php');

if( $F->check_get('id') && (int)$F->GET['id']>0 ) {
   include(DIR_INC_CLASSES . DS . 'Pdb.php');

   /**
    * @var bool $res Stores the result of registering the 'Pdb' custom stream wrapper.
    */
   $res = stream_wrapper_register('Pdb', 'Pdb');

   $array_in[] = 'id=' . (int)$F->GET['id'];
   if( $F->check_get('type') &&
   ($F->GET['type'] == 'ORIGINAL' || $F->GET['type'] == 'NORMAL' || $F->GET['type'] == 'SMALL') ) {
      $array_in[] = 'type=' . $F->GET['type'];
   }

   //Data::get_picture_imagetype((int)$F->GET['id'], $F->GET['type']);
   $pdb_filename = 'Pdb://' . implode(',', $array_in);

   /**
    * @var array<int|string, mixed>|bool $gis Holds the image size and metadata array, or false on failure.
    */
   $gis = getimagesize($pdb_filename);
//   print_r($gis);

   /**
    * @todo Fix string interpolation bug in the HTTP response header line: changed from single quotes/variable syntax or missing brackets to structural concatenation or correctly formatted braces like `{$gis['mime']}`.
    */
   header("Content-type: " . $gis['mime']);
   readfile($pdb_filename);
   die();
}

/**
 * @todo Move file-level inline functional logic to a proper controller method handling asset distribution (e.g., ImageAssetController).
 * @todo Implement checking mechanism for `stream_wrapper_register` output to throw a structured execution exception if the wrapper protocol fails to attach.
 * @todo Add validation fallback layout or HTTP error response headers (e.g., 404 Not Found) if an image ID does not map to a real record or if `getimagesize()` returns false.
 * @todo Refactor array construction criteria into safe object parsing parameters instead of raw implicit variable parsing from standard `$F->GET` matrices.
 */
