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

function gl_check_password($password_in, $password_db) {
   list($pass_hash, $pass_salt) = explode(':', $password_db);
   $pass_hash_in = hash_hmac('ripemd320', $password_in . str_rot13($password_in), $pass_salt);

   if( $pass_hash_in == $pass_hash ) return true;
   else                              return false;
}


function gl_make_password($password_in, $salt_add = '', $pure_salt = false) {

   //if avaiable get 32 bytes of randomness
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
   
   $pass_hash = hash_hmac('ripemd320', $password_in . str_rot13($password_in), $pass_salt);

   return $pass_hash . ':' . $pass_salt;
}


//TODO
// set_error_handler

?>