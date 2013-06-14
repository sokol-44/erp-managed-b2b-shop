<?php
/**
 * Data.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

function gl_init() {


}

function __autoload($name) {
   //echo '<pre>' . print_r(debug_backtrace(FALSE), TRUE). '</pre>';
}


function print_debug($var, $vd = false ) {
   if($vd) {
      $res = var_export($var, true);
   } else{
      $res = print_r($var, true);
   }

   echo '<p align="left" style="background-color: white; color: #606060;">' .
   str_replace('  ', '&nbsp;', nl2br(htmlspecialchars($res))) .
   '</p>';
}


function get_best_tmp_dir() {
   if( ini_get('upload_tmp_dir')!='' ) return ini_get('upload_tmp_dir');
   if( ini_get('session.save_path')!='' ) return ini_get('upload_tmp_dir');
   return sys_get_temp_dir();
}



class File_Debug {
   private $save_debug_fd = false;
   static $class = false;
   
   function __construct($tag = '') {
      self::$class = $this;
      if( is_dir('debug') ) {
         $name = 'debug/' . $tag . '_' . getmypid() . '_' . microtime(true) . '.log.html';
         //$name = 'debug/log.html';
         $this->save_debug_fd = fopen($name, 'a');
         if( $this->save_debug_fd ) {
            fwrite($this->save_debug_fd, '<html><body><pre>');
         }
      }
   }
   static function g_global($tag = '') {
      if(self::$class == false) {
         self::$class = new File_Debug($tag);
      }
      return self::$class;
   }
   
   function s($var) {
      if( $this->save_debug_fd ) {
         $res = stripslashes( var_export($var, true) );
         fwrite($this->save_debug_fd, htmlspecialchars($res) . "\n\n");
      };
   }
   
   function __destruct() {
      $this->c();
   }
   
   function c() {
      if( $this->save_debug_fd ) {
         fwrite($this->save_debug_fd, '</pre></body></html>');
         fclose($this->save_debug_fd);
      }
   }
}

register_shutdown_function('shutdown');

function shutdown() {
   global $query_log;
   
   $FD = File_Debug::g_global();
   $FD->s($_GET);
   $FD->s($_POST);
   $FD->s($query_log);
   $FD->c();
   
   //print_debug($BackTrail);
   //print_debug($Page);
   //print_debug($F);
   //print_debug($P);
   //print_debug($Info);
   //print_debug(Lang::$STR);
   //print_debug($_GET);
}



function gl_check_password($password_in, $password_db) {

   $pass_array = explode(':', $password_db);
   if( sizeof($pass_array) == 1 ) {
      $pass_hash_in = $password_db;
      $pass_salt = '';
      if( strlen($password_db) == 32 ) {
         $password_hash = 'MD5';
      } elseif( strlen($password_db) == 40 ) {
         $password_hash = 'SHA1';
      } else {
         return false;
      }
   } elseif( sizeof($pass_array) == 2 || sizeof($pass_array) == 3 ) {
      if( sizeof($pass_array) == 2 ) {
         $password_hash = 'HM_RMD320';
         $pass_hash_in = $pass_array[0];
         $pass_salt = $pass_array[1];
      } else {
         $password_hash = $pass_array[0];
         $pass_hash_in = $pass_array[1];
         $pass_salt = $pass_array[2];
      }
      $pass_hash = gl_compute_hash($password_hash, $password_in, $pass_salt);
   } else {
      return false;
   }
    
   if( $pass_hash_in == $pass_hash ) return true;
   else                              return false;
}


function gl_make_password($password_in, $salt_add = '', $pure_salt = false) {

   //if avaiable get 32 bytes of randomness
   $str_rand = 'RAND'; //placeholder
   $filename = '/dev/urandom';
   if ( is_readable($filename) ) {
      $h = fopen($filename, 'rb');
      $str_rand = fread($h,32);
   }

   //sum all random sourcess
   $random = $str_rand . microtime() . getmypid() . serialize($_ENV);

   //try to generate random hash
   if( $pure_salt ) $pass_salt = $salt_add;
   else $pass_salt = hash_hmac('ripemd160', $salt_add, $random );

   echo 'gl_make_password<br>:' . $password_in . '<br>s:' . $pass_salt . '<br>';
   
   if( defined('DEFAULT_PASSWORD_HASH_TYPE') ) $password_hash = DEFAULT_PASSWORD_HASH_TYPE;
   else $password_hash = 'HM_RMD320';
   
   $pass_hash = gl_compute_hash($password_hash, $password_in, $pass_salt);

   return $password_hash . ':' . $pass_hash . ':' . $pass_salt;
}

function gl_compute_hash($password_hash, $password_in, $pass_salt) {
   
   switch ($password_hash) {
      case 'MD5':
         $pass_hash = md5( $password_in );
         break;
      case 'SHA1':
         $pass_hash = sha1( $password_in );
         break;
      case 'SHA2S':
         $pass_hash = hash('SHA256', $password_in . $pass_salt);
         break;
      case 'MDSHA':
         $pass_md5 = md5( $password_in );
         $pass_hash = sha1( $pass_md5 . $pass_salt . strrev($pass_md5) );
         break;
      case 'MD53':
         $pass_hash = md5(
         md5( $password_in . $pass_salt, true) .
         $password_in .
         md5( $pass_salt . $password_in, true)
         );
         break;
      case 'SH52':
         $pass_hash_in = hash('SHA512', $password_in . $pass_salt .
         hash('SHA512', $password_in . $pass_salt) );
         break;
      case 'SH32':
         $pass_hash_in = hash('SHA384', $password_in . $pass_salt .
         hash('SHA384', $password_in . $pass_salt) );
         break;
      case 'SH22':
         $pass_hash_in = hash('SHA256', $password_in . $pass_salt .
         hash('SHA256', $password_in . $pass_salt) );
         break;
      case 'HM_RMD320':
      default:
         $pass_hash = hash_hmac('ripemd320', $password_in . str_rot13($password_in), $pass_salt);
         break;
   }
   return $pass_hash;
}

//TODO
// set_error_handler

?>