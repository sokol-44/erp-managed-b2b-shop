<?php
/**
 * index.php Administrator Panel
 * Copyright MichaÅ‚ SokoÅ‚owski 2010
 *
 * @author Micha³ Soko³owski <msokolowski@example.com>
 */
/**
 * Include global init file
 */
include('init.php');


include(DIR_INC_CLASSES . DS . 'Pdb.php');

stream_wrapper_register('Pdb', 'Pdb');

if( $F->check_get('id') && (int)$F->GET['id']>0 ) {

   $array_in[] = 'id=' . (int)$F->GET['id'];
   if( $F->check_get('type') &&
   ($F->GET['type'] == 'ORIGINAL' || $F->GET['type'] == 'NORMAL' || $F->GET['type'] == 'SMALL') ) {
      $array_in[] = 'type=' . $F->GET['type'];
   }

   //Data::get_picture_imagetype((int)$F->GET['id'], $F->GET['type']);
   $pdb_filename = 'Pdb://' . implode(',', $array_in);
   $gis = getimagesize($pdb_filename);
//   print_r($gis);
   header("Content-type: {gis['mime']}");
   readfile($pdb_filename);
}
?>