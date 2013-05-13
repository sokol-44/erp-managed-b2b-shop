<?php
/**
 * Data.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/**
 * Data class goint to provide data and operation on them
 * Application will get and save any data thru it
 */
/**
 * @author ms
 *
 */
class Data_Picture extends Data_Article {


   static function autorescale_image($data, $type = 'NORMAL') {
      if( $type == 'NORMAL' ) {
         $width = IMAGE_NORMAL_WIDTH;
         $height = IMAGE_NORMAL_HEIGHT;
      } else { //SMALL
         $width = IMAGE_SMALL_WIDTH;
         $height = IMAGE_SMALL_HEIGHT;
      }
      return self::rescale_image($data, $width, $height);
   }
   
   static function check_image_format($data) {
   
      $stamp['pdf'] = chr(0x25) . chr(0x50) . chr(0x44) . chr(0x46);
      $stamp['swf'] = chr(0x43) . chr(0x57) . chr(0x53);
      $stamp['jpg'] = chr(0xFF) . chr(0xD8) . chr(0xFF);
      $stamp['gif'] = chr(0x47) . chr(0x49) . chr(0x46) . chr(0x38);
      $stamp['png'] = chr(0x89) . chr(0x50) . chr(0x4E) . chr(0x47) . chr(0x0D) . chr(0x0A) . chr(0x1A) . chr(0x0A);
   
      $fdata_tmp = substr($data, 0, 10);
      foreach($stamp as $format_name_tmp => $format_stamp_tmp ) {
         $res = strpos($fdata_tmp, $format_stamp_tmp);
         if( $res !== FALSE && $res == 0 ) {
            $format_name = $format_name_tmp;
            break;
         }
      }
   
      $meta = array('width' => false, 'height' => false, 'format' => false, 'image' => false);
   
   
      if( $format_name ) {
         switch( $format_name ) {
            case 'jpg':
            case 'gif':
            case 'png':
               //convert & save
               $tmpname = tempnam('','pdb_');
               $fh = fopen($tmpname, 'wb');
               fwrite($fh, $data);
               fclose($fh);
               $gis = getimagesize($tmpname);
               unlink($tmpname);
               if( $gis ) {
                  $meta['width'] = $gis[0];
                  $meta['height'] = $gis[1];
                  $meta['format'] = strtoupper($format_name);
                  $meta['image'] = true;
               }
               break;
            case 'pdf':
            case 'swf':
               //save
               $format = strtoupper($format_name);
               break;
            default:
               break;
         }
      }
   
      return $meta;
   }
      
   /**
    * @param image data OR image path $data
    * @param image width $width
    * @param image height $height
    * @param type(HEIGHT WIDTH OUTSIDE INSIDE INSIDEFILL) $type
    * @param return $filetype = array('data', 'width', 'height', 'format');
    * @return string
    */
   static function rescale_image($data, $width, $height, $type = 'INSIDE', $filetype = 'AUTO') {

      if( ctype_print($data) && is_readable($data) ) {
         $filename_tmp = $data;
         $unlink = false;
      } else {
         $tmpname = tempnam('','pdb_');
         $fh = fopen($tmpname, 'wb');
         fwrite($fh, $data);
         fclose($fh);
         $filename_tmp = $tmpname;
         $unlink = true;
      }

      if( $filetype == 'AUTO') {
         if( $type == 'INSIDEFILL' || ($width + $height) < 202) {
            $filetype = 'PNG';
         } else {
            $filetype = 'JPG';
         }
      }

      $ratio_wh_dest = $width / $height;
      $quality = 80;

      // Get new dimensions
      list($width_orig, $height_orig, $image_type) = getimagesize($filename_tmp);
      $ratio_wh_orig = $width_orig/$height_orig;

      switch($image_type) {
         case IMAGETYPE_GIF:
            $image = imagecreatefromgif($filename_tmp);
            break;
         case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($filename_tmp);
            break;
         case IMAGETYPE_PNG:
            $image = imagecreatefrompng($filename_tmp);
            break;
         default:
            return false;
            break;
      }

      $width_move = 0;
      $height_move = 0;
      $width_r = $width;
      $height_r = $height;

      switch($type) {
         case 'STRETCH':
            $width_d = $width;
            $height_d = $height;
            break;
         case 'HEIGHT':
            $width_d = $height * $ratio_wh_orig;
            $height_d = $height;
            //$width_move = floor(($height-$height_d)/2);
            $width_r = $width_d;
            break;
         case 'WIDTH':
            $width_d = $width;
            $height_d = $width / $ratio_wh_orig;
            //$height_move = floor(($height-$height_d)/2);
            $height_r = $height_d;
            break;
         case 'INSIDE':
            if ($ratio_wh_dest > $ratio_wh_orig) {
               $width_d  = floor($height * $ratio_wh_orig);
               $height_d = $height;
               // $width_move = floor(($width-$width_d)/2);
            } else {
               $width_d  = $width;
               $height_d = floor($width/$ratio_wh_orig);
               // $height_move = floor(($height-$height_d)/2);
            }
            $width_r = $width_d;
            $height_r = $height_d;
            break;
         case 'INSIDEFILL':
            if ($ratio_wh_dest > $ratio_wh_orig) {
               $width_d  = floor($height * $ratio_wh_orig);
               $height_d = $height;
               $width_move = floor(($width-$width_d)/2);
            } else {
               $width_d  = $width;
               $height_d = floor($width/$ratio_wh_orig);
               $height_move = floor(($height-$height_d)/2);
            }
            break;
         case 'OUTSIDE':
            if ($ratio_wh_dest < $ratio_wh_orig) {
               $width_d  = floor($height * $ratio_wh_orig);
               $height_d = $height;
               $width_move = floor(($width-$width_d)/2);
            } else {
               $width_d  = $width;
               $height_d = floor($width/$ratio_wh_orig);
               $height_move = floor(($height-$height_d)/2);
            }
            break;
      }


      // Resample
      $image_p = imagecreatetruecolor($width_r, $height_r);
      if( $filetype == 'PNG' ) {
         $alfa = imagecolorallocatealpha($image_p, 255, 255, 255, 127);
         imagealphablending($image_p, false);
         imagesavealpha($image_p, true);
         imagefilledrectangle($image_p, 0, 0, $width_r, $height_r, $alfa);
      }

      imagecopyresampled($image_p, $image,
      $width_move, $height_move, //destination
      0, 0, //source
      $width_d, $height_d, //destination
      $width_orig, $height_orig); //source

//       echo "<br> $filetype wm:
//       $width_move, $height_move, //destination <br>
//       0, 0, //source<br>
//       $width_d, $height_d, //destination<br>
//       $width_orig, $height_orig<br>";

      //$rfn = 'rr_' . rand(1,65000) . '.jpg';
      //imagejpeg($image_p, $rfn , $quality);

      $tmpname_res = tempnam('','pdb_');

      if( $filetype == 'PNG' ) {
         imagepng($image_p, $tmpname_res , 8);
      } else {
         imagejpeg($image_p, $tmpname_res , 90);
      }
      $fp_res = fopen($tmpname_res, 'rb');

      $data_res = stream_get_contents($fp_res);
      fclose($fp_res);

      unlink($tmpname_res);
      if( $unlink ) unlink($filename_tmp);

      return array('data' => $data_res, 'width' => $width_r, 'height' => $height_r, 'format' => $filetype);
   }

   static function remove_picture($id_picture) {
      $result = array('main' => 0, 'data' => 0);
      $query = 'delete from ' . TBL_GLOBAL_PICTURES . ' where `id_picture`=' . db_int($id_picture);
      db_query($query);
      $result['main'] = db_affected_rows();
      $result['data'] = self::delete_picture_data((int)$id_picture);
      return $result;
   }

   static function delete_picture_data($id_picture) {
      $query = 'delete from ' . TBL_GLOBAL_PICTURES_DATA . ' where `id_picture`=' . db_int($id_picture);
      db_query($query);
      return db_affected_rows();
   }

   static function update_picture($id_picture, $name, $description) {
      $query = 'update ' . TBL_GLOBAL_PICTURES . ' set
      name = "' . db_escape($name) . '", description = "' . db_escape($description) . '", mtime = now()
      where id_picture = ' . (int)$id_picture;

      return db_query($query);
   }

   static function add_picture($name, $description) {
      $query = 'insert into ' . TBL_GLOBAL_PICTURES . '
       (name, description, ctime, mtime) values
       ("' . db_escape($name) . '", "' . db_escape($description) . '", now(), now())';
      db_query($query);
      $id = db_insert_id();
      return $id;
   }

   static function insert_picture($id_picture, $name, $description) {
      $query = 'insert into ' . TBL_GLOBAL_PICTURES . '
       (`id_picture`, `name`, `description`, `ctime`, `mtime` ) values
       ("' . db_int($id_picture) . '", "' . db_escape($name) . '", "' . db_escape($description) . '", now(), now())';
      
      return db_query($query);
   }
   
   static function insert_subpicture_data($parmeters, $data, $meta = array()) {
      foreach($meta as $key => $val) {
         if( $val ) {
            $parmeters[$key] = db_escape($val);
         } else {
            $parmeters[$key] = 'NULL';
         }
      }
      
      $query = 'insert into ' . TBL_GLOBAL_PICTURES_DATA . '
      (id_picture, type, format, width, height, ctime) values
      (' . $parmeters['id'] . ', "' . db_escape($parmeters['type']) . '", "' . $parmeters['format'] . '",
        ' . $parmeters['width'] . ', ' . $parmeters['height'] . ', now())
      on duplicate key update
      format = "' . $parmeters['format'] . '", ctime = now(),
      width = ' . $parmeters['width'] . ', height = ' . $parmeters['height'] . '';
      //      echo $query;
      $res = db_query($query);
      if( $res ) {
         return db_query('update ' . TBL_GLOBAL_PICTURES_DATA . ' pic_d, ' . TBL_GLOBAL_PICTURES . ' pic
      	set pic_d.data = "' . db_escape($data) . '", pic.mtime = now()
      	where pic.id_picture = pic_d.id_picture and pic.id_picture = ' . $parmeters['id'] . ' and type = "' . db_escape($parmeters['type']) . '"');
      } else {
         return false;
      }
      return true;
   }

   static function get_subpicture_data($id_in, $type = 'NORMAL', $check_owner = false) {
      $query = 'select pic.id_picture, pic.name, pic.description, pic_d.id_picture_data,
      pic_d.type, pic_d.format, pic_d.data, pic_d.width, pic_d.height, OCTET_LENGTH(pic_d.data) as filesize
      from ' . TBL_GLOBAL_PICTURES . ' pic join ' . TBL_GLOBAL_PICTURES_DATA . ' pic_d on
      ( pic.id_picture = pic_d.id_picture )
      where pic.id_picture = ' . (int)$id_in . ' and pic_d.type="' . $type . '"';
      //      echo $query;
      $res_array = db_fetch_array( db_query($query) );
      if( $res_array ) {
         $res_array['query'] = $query;
         return $res_array;
      } else {
         return false;
      }
      //return db_result_array($res);

   }

   static function get_picture_data($id_picture) {
      $query = 'select pic.id_picture, pic.name, pic.description,
      group_concat( concat(pic_d.type, \'=\' , pic_d.format ) ) as format
      from ' . TBL_GLOBAL_PICTURES . ' pic left outer join ' . TBL_GLOBAL_PICTURES_DATA . ' pic_d on
      ( pic.id_picture = pic_d.id_picture ) where pic.id_picture = ' . (int)$id_picture;
      return db_fetch_array( db_query($query) );
   }

   static function get_all_pictures() {
      $query = 'select pic.id_picture, pic.name, pic.description
      from ' . TBL_GLOBAL_PICTURES . ' pic';
      $res = db_query($query);
      return db_result_array($res);
   }

   static function get_pictures_list() {
      $SP = SplitPage::g_global();

      $query = 'select pic.id_picture, pic.id_hotel, pic.name, pic.description,
      group_concat( concat(pic_d.type, \'=\' , pic_d.format) ) as format
      from ' . TBL_GLOBAL_PICTURES . ' pic left outer join ' . TBL_GLOBAL_PICTURES_DATA . ' pic_d on
      ( pic.id_picture = pic_d.id_picture ) group by pic.id_picture';
      $quert_fast = 'select count(pic.id_picture) as total from ' . TBL_GLOBAL_PICTURES . ' pic ';
      $sp_query = $SP->prepare_sql($query, $quert_fast);
      $res = db_query($sp_query);
      return db_result_array($res);
   }
   
   static function setPicture($param_array) {

      $pic_rm = self::remove_picture((int)$param_array['id_picture']);
      self::insert_picture((int)$param_array['id_picture'], $param_array['name'], $param_array['description']);
      $data = base64_decode($param_array['data']);
      $new_meta = Data::check_image_format( $data );

      if(!$new_meta['image']) return false;
      
      $parameters = array('id' => (int)$param_array['id_picture'], 'type' => 'ORIGINAL');
      
      Data::insert_subpicture_data($parameters, $data, $new_meta);
      $res_array = array('id' => (int)$param_array['id_picture'], 'additional_data' => '', 'status' => '');
      
      if($pic_rm['main'] == 0 ) {
         $res_array['status'] = 'SUCCESS,PICTURE_NOT_EXIST';
      } else {
         $res_array['status'] = 'SUCCESS,PICTURE_EXIST';
         $res_array['additional_data'] = 'main:' . $pic_rm['main'] . ',sub:' . $pic_rm['data'];
      }
      return $res_array;
      
   }

}

?>