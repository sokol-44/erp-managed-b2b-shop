<?php
/**
 * Framework.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Micha� Soko�owski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Framework extends Framework_Data {
   static $class = false;
   static $POST = array(), $GET = array(), $REQUEST = array();
   static $com = '';
   static $virtualdir = '';
   static $going_back = false;
   static $GET_array = array();

   //   static $GET_raw = '', $GET_array = array();

   function __construct() {
      $this->GET = array();
      $this->POST = array();
      $this->REQUEST = array();
      $this->RSA = array();
      $this->form = 0;
      $this->virtualdir = false;

      $this->_request_normalize();

      self::$class = $this;
   }

   static function g_global() {
      if(self::$class == false) {
         self::$class = new Framework;
      }
      return self::$class;
   }

   static function json_string($data) {
      return addslashes(json_encode($data));
   }
   
   
   static function output_string_html($string, $lenght = false) {
      if( $lenght && strlen($string) > $lenght ) {
         $substring = substr($string,0, $lenght);
         $cut_pos = strrpos($substring,' ');
         if( $cut_pos == 0 ) {
             $cut_pos = strrpos($substring,"\n");
             if( $cut_pos == 0 ) {
                $cut_pos = $lenght;
             }
         }
         $substring = substr($substring,0,$lenght);
         return htmlspecialchars($substring);
      } else {
         return htmlspecialchars($string);
      }
   }

   static function output_string($string) {
      return htmlspecialchars($string);
   }

   static function redirect($page) {
      header('Location: ' . $page, true);
      self::put_js_redirect($page);
      exit();
   }

   static function not_null($input) {
      if (is_array($input)) {
         if (sizeof($input) > 0) return true;
         else return false;
      } elseif ( is_object($input) ) {
         return true;
      } elseif (($input != '') && (strlen(trim($input)) > 0) && (strtolower($input) != 'null')) {
         return true;
      }
      return false;
   }

   static function is_null($input) {
      return !self::not_null($input);
   }

   function make_link($component = '', $arguments = '', $connection = 'NONSSL', $add_session_id = true, $seo = false) {

      if( $this->not_null($component) ) {
         $parameters_array_raw = array('com' => $component);
      } else {
         $parameters_array_raw = array('com' => DEFAULT_COM);
      }

      if ($this->not_null($arguments)) {
         if( is_array($arguments) ) {
            unset($arguments['com']);
            $parameters_array_raw = array_merge($parameters_array_raw, $arguments);
         } else {
            $parameters_array_raw[] = self::__prepare_params($arguments);
         }
      }

      $parameters_array = array();
      foreach($parameters_array_raw as $key => $val ) {
         $parameters_array[] = urlencode($key) . '=' . urlencode($val);
      }


      return 'index.php?' . implode('&', $parameters_array);
   }

   function self_link() {
      return $this->make_link($this->com, $this->GET);
   }

   //FIXME for arrays in GET
   function make_get ($elements = '', $mode = false) {

      if ( !is_array($elements)) {
         if( $mode ) {
            $elements = array($elements);
         } else {
            $elements = explode(',', $elements);
         }
      }

      $elements = array_merge($elements, array('com'));

      $this->GET_array = array();
      foreach ($this->GET as $key => $val) {
         if ( $mode == in_array($key,$elements)  )
         $this->GET_array[$key] = $val;
      }

      return $this->GET_array;
   }


   function request_split_array($name, $delimeter = ',', $method = 'REQUEST') {
      if( $this->check_get($name) && $this->not_null( $delimeter ) &&
      ( $method == 'GET' || $method == 'POST' || $method == 'REQUEST' ) ) {
         $tmp_array = $this->${method};
         $this->RSA[$name] = explode($delimeter, $tmp_array[$name]);
      } else {
         $this->RSA[$name] = array();
      }
      return $this->RSA[$name];
   }

   function check_request_split_array($name, $value) {
      if( isset($this->RSA[$name]) ) {
         return in_array($value, $this->RSA[$name]);
      } else {
         return false;
      }

   }

   /**
    * @param input elements or name of element $elements
    * @param element value $value
    * @param array to merge $array
    * @return array:|multitype:
    */
   function add_local_get($elements = '', $value = '', $array = '') {
      if ( !is_array($elements)) {
         if( self::not_null($value) ) {
            $elements = array($elements => $value);
         } else {
            $elements = array($elements => $elements);
         }
      }

      if( is_array($array) ) {
         return array_merge($array, $elements);
      } else {
         if( self::not_null($this->GET_array) ) {
            return array_merge($this->GET_array, $elements);
         } else {
            return $elements;
         }
      }
   }


   function add_get($elements = '', $value = '') {
      $this->GET_array = $this->add_local_get($elements, $value);
      return $this->GET_array;
   }


   function check_get($text, $translate = false) {
      if($translate) $text = Lang::_($text);

      //      if( isset($this->GET[$text]) ) {
      return self::not_null($this->GET[$text]);
      //      } else {
      //         return false;
      //      }
   }

   function check_post($text, $translate = false) {
      if($translate) $text = str_replace(' ', '_', Lang::_($text));

      return self::not_null($this->POST[$text]);
   }

   static function check_valid_email( $str ) {
      if(eregi("^[a-zA-Z0-9]+[_a-zA-Z0-9-]*(\.[_a-z0-9-]+)*@[a-z??????0-9]+(-[a-z??????0-9]+)*(\.[a-z??????0-9-]+)*(\.[a-z]{2,4})$", $str)) {
         return TRUE;
      }
      return FALSE;
   }

   function check_login($where = false) {

      if( !self::not_null($where) ) return false;

      if( $where != 'ADMIN' && $where != 'CLIENT' ) return false;

      if( $this->not_null($this->POST['lgn_' . $where]) &&
      $this->not_null($this->POST['pswrd_' . $where]) ) {
         return true;
      }
      return false;
   }

   //   function get_login_data($type) {
   //      return ($this->POST['lgn_' . $type], )
   //
   //   }
   
   
   function check_virtualdir() {
      if( $this->virtualdir ) return true;
      else return false;
   }
   
   static function get_current_date( $timestamp = false ) {
      if (!$timestamp) $timestamp = time();
      return date($GLOBALS['config']['DATE']['date'], $timestamp);
   }
   
   static function get_current_datetime( $timestamp = false ) {
      if (!$timestamp) $timestamp = time();
      return date($GLOBALS['config']['DATE']['datetime'], $timestamp);
   }
   
   
   function return_virtualdir_id() {
      $template_array = $GLOBALS['config']['TEMPLATES'];
      //print_debug($template_array);
      foreach( $template_array as $key => $val ) {
         if( $this->virtualdir == $key ) return $val;
      }
      return false;
   }

   function _request_normalize() {
      //$_GET, $_POST

      $res_array_get = self::_request_normalize_rec($_GET);
      if( is_array($res_array_get) ) $this->GET = $res_array_get;
      //$_GET, $_POST
      $res_array_post = self::_request_normalize_rec($_POST, 'POST');
      if( is_array($res_array_post) ) $this->POST = $res_array_post;
      $this->REQUEST = array_merge_recursive($this->POST, $this->GET);

      if( self::not_null($this->GET['com']) ) {
         $this->com = $this->GET['com'];
         //unset($this->GET['com']);
      }

      if( self::not_null($this->GET['virtualdir']) ) {
         $this->virtualdir = $this->GET['virtualdir'];
         unset($this->GET['virtualdir']);
      }

      if( self::not_null($this->GET['going_back']) ) {
         $this->going_back = (($this->GET['going_back']==1)?true:false);
         unset($this->GET['going_back']);
      }
   }

   function js_escape( $str, $str_separator = '"') {
      $search = array('\\', $str_separator);
      $replace = array('\\\\', '\\' . $str_separator);
      $str = str_replace($search, $replace, $str);
      return $str;
   }
   
   static function array_recursive_strip_tags( $array ) {
      array_walk_recursive($array, 'strip_tags');
      return $array;
   }

   static function array_recursive_compare($array1, $array2) {
      //FIXME
      $diff_count = 0;
      foreach($array1 as $key1 => $val1) {
         if( isset($array2[$key1]) ) {
            if( is_array( $val1 ) ) {
               if( count($val1) == 0 ) {
                  $diff_count++;
                  // echo "a1= $val1 != $array2[$key1]\n";
               } else {
                  $diff_count += $this->array_recursive_compare($val1, $array2[$key1]);
                  // echo "b1= $val1 != $array2[$key1]\n";
               }
            } else {
               if( $val1 != $array2[$key1] ) {
                  $diff_count++;
                  // echo "c1= $val1 != $array2[$key1]\n";
               }

            }
         } else {
            if( is_array( $val1 ) ) {
               if( count($val1) == 0 ) {
                  $diff_count++;
                  // echo "d1= $val1 != $array2[$key1]\n";
               } else {
                  $diff_count += $this->array_recursive_compare($val1, array());
                  // echo "e1= $val1 != $array2[$key1]\n";
               }
            } else {
               if( $val1 != $array2[$key1] ) {
                  $diff_count++;
                  // echo "f1= $val1 != $array2[$key1]\n";
               }
            }
         }
      }
      foreach($array2 as $key2 => $val2) {
         if( !isset($array1[$key2]) ) {
            //count all
            if( is_array( $val2 ) ) {
               if( count($val2) == 0 ) {
                  $diff_count++;
                  // echo "a2= $val2 != $array1[$key2]\n";
               } else {
                  $diff_count += $this->array_recursive_compare(array(), $val2);
                  // echo "b2= $val2 != $array1[$key2]\n";
               }
            } else {
               $diff_count++;
               // echo "c2= $val2 != $array1[$key2]\n";
            }
         }
      }
      // echo "$diff_count \n";
      return $diff_count;
      //array_diff
   }

   private function _request_normalize_rec(array $array, $type = 'GET') {
      //$_GET, $_POST

      $ret_array = array();
      foreach( $array as $key => $val ) {
         if( get_magic_quotes_gpc() === TRUE ) $key = stripslashes($key);
         if( get_magic_quotes_gpc() === TRUE ) $val = stripslashes($val);
         
         if( is_array($val) ) {
            $ret_array[$key] = self::_request_normalize_rec($val, $type);
         } else {
            $val = (string)trim($val);
            if( strlen($val) > 0
               && !(($key == 'x' || $key == 'y'))
//                && !( $type == 'POST' && ($key == 'x' || $key == 'y'))
               //&& !( $type == 'GET' && ($key == 'virtualdir'))
               ) {
               $ret_array[$key] = $val;
            }
         }
      }

      if( sizeof($ret_array) > 0 )  return $ret_array;
      else return array();
   }

}





?>