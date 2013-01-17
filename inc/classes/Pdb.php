<?php
/**
 * Pdb.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/**
 * Data class goint to provide data and operation on them
 * Application will get and save any data thru it
 */

class Pdb {
   var $position;
   var $varname;
   var $parameters;
   var $mode;
   var $options;
   var $content;
   var $write;
   var $meta;


   function __construct() {
      //      echo "#__construct\r";
   }


   static private function __split_path($path) {

      $array_res = array();
      list(,$p1) = split('://', $path);
      $part = split(',', $p1);
      foreach($part as $key_val) {
         $key_val_arr = split('=', $key_val);
         if( isset($key_val_arr[1]) && $key_val_arr[1] != '' ) {
            $array_res[$key_val_arr[0]] = $key_val_arr[1];
         } else {
            $array_res[$key_val_arr[0]] = $key_val_arr[0];
         }
      }
      return $array_res;

   }

   static private function __extract_parameters($parameters) {
      $array_parameters = Pdb::__split_path($parameters);

      //'id' => 'int', 'type' => 'NORMAL,ORIGINAL,SMALL', 'size' => 'widthXheightXaspect'

      if( Framework::not_null($array_parameters['id']) && ctype_digit($array_parameters['id']) ) {
         $parameters_out['id'] = (int)$array_parameters['id'];
         if( Framework::is_null($array_parameters['type']) ) { // TYPE
            if( Framework::not_null($array_parameters['size']) ) { // SIZE
               $size_split = split('X', $array_parameters['size']);
               if( ctype_digit($size_split[0]) && ctype_digit($size_split[1]) && ctype_digit($size_split[2]) ) {
                  $parameters_out['size'] = $size_split;
                  $parameters_out['type'] = 'ORIGINAL';
               } else {
                  return false;
               }
            } else {
               $parameters_out['type'] = 'NORMAL';
            }
         } else {
            if( $array_parameters['type'] == 'NORMAL' || $array_parameters['type'] == 'ORIGINAL' || $array_parameters['type'] == 'SMALL' ) {
               $parameters_out['type'] = $array_parameters['type'];
            } else {
               return false;
            }
         }
      } else {
         return false;
      }

      return $parameters_out;
   }

   private function __scale_image() {
      //TODO
      //tempnam('','pdb');
      return $picture_data;
   }

//   private function __delete_picture() {
//      Data::delete_picture_data($this->meta);
//      echo '__delete_picture';
//   }


   private function __insert_picture() {
      //check data params ?
      $new_meta = Data::check_image_format($this->content);
//      print_r($this->meta);

      if(!$new_meta['image'] && $this->parameters['type'] != 'ORIGINAL') return false;

      Data::insert_subpicture_data($this->parameters, $this->content, $new_meta);
//      echo '__insert_picture';
   }

   private function __read_picture() {

      $pic_res = Data::get_subpicture_data($this->parameters['id'], $this->parameters['type']);
      if( $this->parameters['type'] == 'ORIGINAL' ) {
         if( $pic_res ) {
            if( isset($this->parameters['size']) ) {
               return $this->__scale_image();
            } else {
               return $pic_res;
            }
         } else {
            return false;
         }
      } elseif ( !$pic_res ) {
         $pic_res = Data::get_subpicture_data($this->parameters['id'], 'ORIGINAL');
         if( $pic_res ) {
            $pic_auto_scale = Data::autorescale_image($pic_res['data'], $this->parameters['type']);
            if( $pic_auto_scale ) {
               $pic_auto_scale['type'] = $this->parameters['type'];
               $pic_auto_scale['id'] = $this->parameters['id'];
               Data::insert_subpicture_data($pic_auto_scale, $pic_auto_scale['data']);
               return $this->__read_picture();
            }
         }
      }
      return $pic_res;
   }

   static private function __get_meta($input) {
      //      if( is_array($input) )
      unset($input['data']);
      return $input;
      //      return  array_diff_assoc($input, array('data' => '') );
      //      else return $input;
   }


   function stream_open($path, $mode, $options, &$opened_path) {
      $this->parameters = $this->__extract_parameters($path);
      //$this->parameters = Pdb::__split_path($parameters);
      //      var_dump($opened_path);
      $this->position = 0;
      $this->size = 0;
      $this->content = false;
      $this->mode = $mode;
      $this->options = $options;
      $this->write = false;
      $this->meta = false;

      //      print_r($this);echo "\r";

      switch($mode) {
         case 'rb':
         case 'rb+':
         case 'r':
         case 'r+':
            $res = $this->__read_picture();
            if( $res ) {
               $this->content = $res['data'];
               $this->size = $res['filesize'];
               $this->meta = Pdb::__get_meta($res);
//               echo '#load#' . $this->size. "\r";
               return true;
           } else {
//               echo '$1$';
//               print_r($res);
               return false;
            }
            break;
         case 'wb':
         case 'wb+':
         case 'w':
         case 'w+':
//            $res = $this->__delete_picture();
            $this->content = '';
            $this->write = true;
            return true;
            break;
         case 'x':
         case 'x+':
            $res = $this->read_picture();
            if( $res ) {
               $this->content = $res['data'];
               $this->size = $res['filesize'];
               $this->meta = Pdb::__get_meta($res);
//               echo '#load#' . $this->size. "\r";
               return false;
            } else {
               echo '$1$';
               $this->write = true;
               $this->content = '';
//               print_r($res);
               return true;
            }
            break;
         default:
            return false;
      }

      //open db_
      //get contents

      //check mode - read or write

      return true;
   }

   function stream_read($count) {
//      echo "#read$count#\r";
      //a mozie tu ?
      if( $this->content ) {
         if($this->position >= $this->size) {
            return false;
         } else {
            $ret_array = substr($this->content, $this->position, $count);
            $this->position += $count;
            return $ret_array;
         }
      } else {
         //read
         //return
         return NULL;
      }
      return $ret;
   }

   function stream_stat() {
//      echo "#stat#\r";
      $return = array(
         'dev' => $this->parameters['id'], // device number
         'ino' => $this->parameters['id'], // inode number
         'mode' => 33188, // inode protection mode
         'nlink' => 1, // number of links
         'uid' => 0,
         'gid' => 0,
         'rdev' => 0, // device type, if inode device
         'size' => $this->size,
         'atime' => 1061067181, // time of last access (Unix timestamp)
         'mtime' => 1056136526, // time of last modification (Unix timestamp)
         'ctime' => 1056136526, // time of last inode change (Unix timestamp)
         'blksize' => 8192, // blocksize of filesystem IO **
         'blocks' => ceil($this->size/8192) //  	number of blocks allocated **
      );
      //     print_r($return);
      return $return;
   }

   function stream_write($data)  {
      $data_lenght = strlen($data);
      $left = substr($this->content, 0, $this->position);
      $right = substr($this->content, $this->position + $data_lenght);
      $this->content = $left . $data . $right;
      $this->position += strlen($data);
      $this->size = strlen($this->content);
//      echo '#write l:' . strlen($left) . ' d:' . $data_lenght . ' r:' . strlen($right) . "\r";
      return $data_lenght;
   }

   function stream_tell() {
//      echo "#tell $this->position\r";
      return $this->position;
   }

   function stream_eof()  {
      //      echo "#eof\r";
      if($this->position >= $this->size) {
//         echo  "#eof-true\r";
         return true;
      } else {
//         echo  "#eof-false\r";
         return false;
      }
   }

   function stream_close() {
//      print_r($this->meta);
      //      echo bin2hex($this->content);
      if( $this->write ) {
         $this->__insert_picture();
      }
//      echo "#close\r";
   }

   function __destruct() {
//      echo "#destruct\r";
   }

   function stream_seek($offset, $whence) {
      if( !$this->content ) return -1;
//      echo "#seek $offset, $whence\r";

      switch ($whence) {
         case SEEK_SET:
            if ($offset < $this->size && $offset >= 0) {
               $this->position = $offset;
               return true;
            } else {
               return false;
            }
            break;

         case SEEK_CUR:
            if ($offset >= 0 && ($this->position + $offset) < $this->size) {
               $this->position += $offset;
               return true;
            } else {
               return false;
            }
            break;

         case SEEK_END:
            if (($this->size + $offset) >= 0) {
               if( $offset > 0 ) {
                  $this->content .= str_repeat(chr(0), $offset);
                  $this->size += $offset;
               }
               $this->position = $this->size + $offset;
               return true;
            } else {
               return false;
            }
            break;

         default:
            return false;
      }
   }
}

?>