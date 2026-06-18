<?php
/**
 * Data_Picture.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Data_Picture
 *
 * Provides operations and management for processing, storing, and fetching images.
 * This class serves as an interface for managing image data and their variations.
 *
 *
 * @todo Refactor static class to a dependency-injected service.
 * @todo Implement explicit property visibility modifier (e.g., public static $Data_Picture_params).
 * @todo Add modern type hinting for class properties, parameters, and return types.
 * @todo Migrate database query procedures (`db_query`, `db_escape`, etc.) to PDO or an ORM mapper.
 */
class Data_Picture extends Data_Article {
   /**
    * @var array Configuration and temporary parameters for picture handling.
    */
   static $Data_Picture_params = array();

   /**
    * Data_Picture constructor.
    *
    * Initializes the parent Data_Article entity.
    * @todo Remove commented debug code (`//echo get_class();`).
    */
   function __construct() {
        //echo get_class();
       parent::__construct();
   }

   /**
    * Generates a web link or path to retrieve a specific picture variant by its ID.
    *
    * @param int|string $id_picture The unique identifier of the picture.
    * @param string $type The image variant size type ('ORIGINAL', 'NORMAL', or 'SMALL'). Defaults to 'NORMAL'.
    * @return string|bool Returns the URL path string to the image asset, or false if the ID is invalid.
    *
    * @todo Introduce a typed Backed Enum for the image type parameter instead of raw strings.
    * @todo Convert return type to a strict string nullable or union with false (`string|false`).
    */
   static function get_picture_id_link($id_picture, $type = 'NORMAL') {

      if( $type != 'ORIGINAL' && $type != 'NORMAL' && $type != 'SMALL' ) {
         $type = 'NORMAL';
      }

      $id_picture = (int)$id_picture;
      if( $id_picture > 0 ) {
//          return IMAGE_SCRIPT . '&id=' . $id_picture . '&type=' . $type;
         //return URL_FULL . IMAGE_SCRIPT . '?id=' . $id_picture . '&type=' . $type;
         return _I_ROOT_WWW_DIR . IMAGE_SCRIPT . '?id=' . $id_picture . '&type=' . $type;
      }

      return false;
   }

   /**
    * Automatically rescales a provided raw image dataset based on predefined size types.
    *
    * @param string $data Binary string containing raw image stream data.
    * @param string $type Target configuration standard size type ('NORMAL' or 'SMALL'). Defaults to 'NORMAL'.
    * @return array|bool Returns processed matrix payload from `rescale_image` or false on failure.
    *
    * @todo Replace conditional block constants (e.g. `IMAGE_NORMAL_WIDTH`) with class configuration parameters or DTOs.
    */
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

   /**
    * Analyzes raw binary stream magic bytes to determine image format validation and reads metadata attributes.
    *
    * @param string $data Binary image data payload string.
    * @return array Returns validation array structured with 'width', 'height', 'format', and 'image' status properties.
    *
    * @todo Replace custom magic byte signature parsing loops with internal standard `finfo_buffer()` or modern GD extensions.
    * @todo Use explicit `sys_get_temp_dir()` rather than fallback empty string inputs in `tempnam()`.
    */
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
    * Performs core mathematical canvas transformations and resampling configurations for raw image content streams.
    *
    * @param string $data Raw file pathway target or binary raw content string of image asset.
    * @param int $width Intended destination frame boundary canvas horizontal width profile.
    * @param int $height Intended destination frame boundary canvas vertical height profile.
    * @param string $type Geometric scaling behavior rules ('STRETCH', 'HEIGHT', 'WIDTH', 'INSIDE', 'INSIDEFILL', 'OUTSIDE'). Defaults to 'INSIDE'.
    * @param string $filetype Final image compression target wrapper standard encoding scheme ('AUTO', 'PNG', 'JPG'). Defaults to 'AUTO'.
    * @return array|bool Returns hash containing resampled standard data streams, measurements, and format specifications, or false on error.
    *
    * @todo Refactor the messy filesystem IO pipelines (`tempnam`/`unlink`) to in-memory buffers or stream primitives (`php://memory`).
    * @todo Implement modern try-catch error trapping structures for unexpected file handling exceptions.
    * @todo Eliminate commented visual testing outputs (`echo "<br> $filetype wm..."`) to cleanup pipeline side-effects.
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

   /**
    * Completely purges a core tracking record definition and its subsequent blob assets from storage models.
    *
    * @param int|string $id_picture target database dynamic identifier structure.
    * @return array Matrix hash detailing execution outcomes across 'main' index and binary data streams tracking metrics.
    *
    * @todo Standardize return values into clean domain entity result models or operational tracking classes.
    */
   static function remove_picture($id_picture) {
      $result = array('main' => 0, 'data' => 0);
      $query = 'delete from ' . TBL_GLOBAL_PICTURES . ' where `id_picture`=' . db_int($id_picture);
      db_query($query);
      $result['main'] = db_affected_rows();
      $result['data'] = self::delete_picture_data((int)$id_picture);
      return $result;
   }

   /**
    * Executes targeted cleanup procedures exclusively targeting storage metadata blocks.
    *
    * @param int $id_picture Primary identification key context target.
    * @return int Affected rows processing count feedback variable.
    */
   static function delete_picture_data($id_picture) {
      $query = 'delete from ' . TBL_GLOBAL_PICTURES_DATA . ' where `id_picture`=' . db_int($id_picture);
      db_query($query);
      return db_affected_rows();
   }

   /**
    * Updates basic metadata characteristics (name and description) for an established global image registry record.
    *
    * @param int|string $id_picture Reference identity pointing to core targets.
    * @param string $name Label identifier name.
    * @param string $description Detailed contextual documentation note block.
    * @return mixed Operation processing response statement wrapper context.
    *
    * @todo Implement transactional security locks around database modifications to prevent asynchronous state drift.
    */
   static function update_picture($id_picture, $name, $description) {
      $query = 'update ' . TBL_GLOBAL_PICTURES . ' set
      name = "' . db_escape($name) . '", description = "' . db_escape($description) . '", mtime = now()
      where id_picture = ' . (int)$id_picture;

      return db_query($query);
   }

   /**
    * Generates a structural base record entity inside master indexes.
    *
    * @param string $name Text identifier designation name.
    * @param string $description Context definition string.
    * @return int Last generated transactional autoincrement record primary key tracker ID.
    */
   static function add_picture($name, $description) {
      $query = 'insert into ' . TBL_GLOBAL_PICTURES . '
       (name, description, ctime, mtime) values
       ("' . db_escape($name) . '", "' . db_escape($description) . '", now(), now())';
      db_query($query);
      $id = db_insert_id();
      return $id;
   }

   /**
    * Forces structural explicit definitions insertion targets inside master indexes directly.
    *
    * @param int|string $id_picture Target fixed allocation identifier value.
    * @param string $name Identification target string label context.
    * @param string $description Data summary annotation parameter string.
    * @return mixed Structural database command execution pipeline feedback.
    */
   static function insert_picture($id_picture, $name, $description) {
      $query = 'insert into ' . TBL_GLOBAL_PICTURES . '
       (`id_picture`, `name`, `description`, `ctime`, `mtime` ) values
       ("' . db_int($id_picture) . '", "' . db_escape($name) . '", "' . db_escape($description) . '", now(), now())';

      return db_query($query);
   }

   /**
    * Commits explicit sub-resolution or file variation tracking payload segments into database data entities.
    *
    * @param array $parmeters Hash detailing target parameters context mapping data indexes.
    * @param string $data raw string content stream or binary structural file contents string.
    * @param array $meta Optional operational parameter attributes definitions mapper overrides tracking list.
    * @return bool Execution pipeline lifecycle compliance confirmation indicator.
    *
    * @todo Eliminate direct string substitution in relational database engines to mitigate injection vulnerability concerns.
    * @todo Fix typos inside variable designations (e.g. `$parmeters` should be `$parameters`).
    */
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

   /**
    * Retrieves structural image information metrics alongside binary object segments from standard datasets.
    *
    * @param int|string $id_in The target core picture tracking identification reference integer.
    * @param string $type Desired tracking variation resolution target layout category string rules context. Defaults to 'NORMAL'.
    * @param bool $check_owner Structural operational control validation verification rule option switch. Defaults to false.
    * @return array|bool Array representation matrix containing record characteristics on success, or false if not found.
    *
    * @todo Use specific implicit data mapping arrays instead of adding debug telemetry attributes directly to query elements (`$res_array['query']`).
    */
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

   /**
    * Assesses verification validity criteria confirming existence markers tracking instances within database layouts.
    *
    * @param int|string $id_picture Evaluation reference context identifier metrics parameter.
    * @return bool Truth assessment verification conclusion confirmation map indicator flag.
    */
   static function check_picture_exist( $id_picture ) {
      $query = 'select pic.id_picture from ' . TBL_GLOBAL_PICTURES . ' pic where pic.id_picture = ' . (int)$id_picture;
      if( db_rows( db_query($query) ) == 0 ) return false;
      return true;
   }

   /**
    * Pulls meta parameters mapping associated variations array profiles tracking across primary record sets.
    *
    * @param int|string $id_picture target database processing assignment profile reference key element.
    * @return array Array representation containing basic fields alongside grouped format maps.
    */
   static function get_picture_data($id_picture) {
      $query = 'select pic.id_picture, pic.name, pic.description,
      group_concat( concat(pic_d.type, \'=\' , pic_d.format ) ) as format
      from ' . TBL_GLOBAL_PICTURES . ' pic left outer join ' . TBL_GLOBAL_PICTURES_DATA . ' pic_d on
      ( pic.id_picture = pic_d.id_picture ) where pic.id_picture = ' . (int)$id_picture;
      return db_fetch_array( db_query($query) );
   }

   /**
    * Returns an unindexed list array targeting every instance mapped inside primary tables.
    *
    * @return array Full compilation mapping collection listing items attributes values matrix stack.
    */
   static function get_all_pictures() {
      $query = 'select pic.id_picture, pic.name, pic.description
      from ' . TBL_GLOBAL_PICTURES . ' pic';
      $res = db_query($query);
      return db_result_array($res);
   }

   /**
    * Generates a chunked, paginated tracking index overview containing explicit dimensional formats definitions.
    *
    * @return array Multidimensional database collection listing items mapping characteristics values.
    *
    * @todo Abstract global instantiation references (`SplitPage::g_global()`) to explicit parameter injection layers.
    * @todo Fix spelling.
    */
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

   /**
    * Replaces or overrides a structural picture implementation model block transactionally.
    *
    * @param array $param_array Configuration values package array mapping targets (`id_picture`, `name`, `description`, `data`).
    * @return array Status assessment metric configuration hash outlining operation results.
    *
    * @todo Clean up static parent calls referencing undefined external dependencies (`Data::check_image_format`).
    */
   static function setPicture($param_array) {

      $pic_rm = self::remove_picture((int)$param_array['id_picture']);
      self::insert_picture((int)$param_array['id_picture'], $param_array['name'], $param_array['description']);
      $data = base64_decode($param_array['data']);
      $new_meta = Data::check_image_format( $data );

      if(!$new_meta['image']) {
         $res_array['status'] = 'ERROR,WRONG_PICTURE_DATA';
         return $res_array;
      }

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
