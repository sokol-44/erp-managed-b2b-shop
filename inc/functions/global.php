<?php
/**
 * Data.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Global initialization handler.
 * @return void
 */
function gl_init() {


}

/**
 * Fallback magic class auto-loader subroutine.
 * @param string $name Targeted class configuration mapping name requiring auto-loading.
 * @return void
 * @todo Replace this legacy magic function with standard PSR-4 compliance via a modern Composer autoloader.
 */
function __autoload($name) {
   //echo '<pre>' . print_r(debug_backtrace(FALSE), TRUE). '</pre>';
}


/**
 * Renders structured tracking variable parameters into clean HTML layout blocks for debugging.
 * * Includes the calling filename location, method context, and execution line metrics.
 * @param mixed $var Parameter data context target to evaluate.
 * @param bool $var_export Controls if output falls back onto raw script declarations definitions (`var_export`). Defaults to false.
 * @return void
 */
function print_debug($var, $var_export = false ) {
   if($var_export) {
      $res = var_export($var, true);
   } else{
      $res = print_r($var, true);
   }
   $db = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );

   echo '<p align="left" style="background-color: white; color: #606060;">' .
   ':' . $db[0]['file'] . ' [' . $db[1]['function'] . '] (' . $db[0]['line'] . ') ' .
   str_replace('  ', '&nbsp;', nl2br(htmlspecialchars($res))) .
   '</p>';
}


/**
 * Evaluates execution environment parameters to establish the most ideal system temporary data folder path.
 * @return string Verified folder path string.
 * @todo Fix logical typo bug where fallback checking for `session.save_path` incorrectly returns `upload_tmp_dir`.
 */
function get_best_tmp_dir() {
   if( ini_get('upload_tmp_dir')!='' ) return ini_get('upload_tmp_dir');
   if( ini_get('session.save_path')!='' ) return ini_get('upload_tmp_dir');
   return sys_get_temp_dir();
}

/**
 * Callback utility function mapping command execution escaping operations across scalar values.
 * @param string $str Raw character text segment.
 * @param int $idx Index collection tracker position.
 * @return string Safe shell argument parameter segment string.
 */
function gl_escapeshellarg_walk($str, $idx) {
   	return escapeshellarg($str);
}

/**
 * Class File_Debug
 * * Manages atomic local file recording pipelines to trace system interactions securely.
 * @package Core
 * @subpackage Logging
 * @psr-5
 * @todo Upgrade class internal attributes to use modern explicit type modifiers (PHP 7.4+).
 */
class File_Debug {
   /**
    * @var resource|false Active log target file handle wrapper, or false when writing streams unprovisioned.
    */
   private $save_debug_fd = false;

   /**
    * @var File_Debug|false Static tracking variable singleton pointer handle.
    */
   static $class = false;

   /**
    * File_Debug constructor.
    * * Initializes unique log file streams under folder containers if directory pathways resolve valid.
    * @param string $tag Custom operational label context appended onto file parameters names layouts. Defaults to ''.
    * @return void
    */
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

   /**
    * Resolves static tracking locators to implement dynamic pseudo-singleton designs patterns.
    * @param string $tag Identification categorization suffix. Defaults to ''.
    * @return File_Debug Active context operational instance handle tracker.
    */
   static function g_global($tag = '') {
      if(self::$class == false) {
         self::$class = new File_Debug($tag);
      }
      return self::$class;
   }

   /**
    * Writes arbitrary variables maps safely into standard output streams.
    * @param mixed $var Parameter dataset targeted for local printing updates.
    * @return void
    */
   function s($var) {
      if( $this->save_debug_fd ) {
         $res = stripslashes( var_export($var, true) );
         fwrite($this->save_debug_fd, htmlspecialchars($res) . "\n\n");
      };
   }

   /**
    * File_Debug destructor.
    * @return void
    */
   function __destruct() {
      $this->c();
   }

   /**
    * Terminates open layout envelopes and explicitly forces storage file stream closures.
    * @return void
    */
   function c() {
      if( $this->save_debug_fd ) {
         fwrite($this->save_debug_fd, '</pre></body></html>');
         fclose($this->save_debug_fd);
         $this->save_debug_fd = false;
      }
   }
}

register_shutdown_function('shutdown');

/**
 * Intercepts engine thread execution closure operations to generate complete metric dump logs.
 * @global array $query_log Global storage data tracking historical database query interactions.
 * @return void
 */
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



/**
 * Challenges plaintext user passwords against custom persistent complex multi-hash configurations blocks.
 * Automatically resolves structural parameters offsets to extract legacy MD5/SHA1 schemas,
 * or routes checks into advanced custom HMAC algorithms based on format metrics.
 * @param string $password_in Plaintext password string input context collected from login requests.
 * @param string $password_db Cryptographic verification template retrieved from persistent data stores.
 * @return bool True if generated cryptographic tokens align completely, false otherwise.
 * @todo Transition custom multi-hash parsing mechanics completely over to unified `password_verify` engines.
 */
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
   } else {
      return false;
   }

   $pass_hash = gl_compute_hash($password_hash, $password_in, $pass_salt);

   if( $pass_hash_in == $pass_hash ) return true;
   else                              return false;
}


/**
 * Formulates a complete multi-segmented persistent password string layout tracking unique salt maps.
 * Leverages secure stream readers `/dev/urandom` where available to inject high entropy seeds elements.
 * @param string $password_in Unescaped target credential plaintext string context.
 * @param string $salt_add Optional supplementary tracking payload tag used as a modifier. Defaults to ''.
 * @param bool $pure_salt Overrides system random generations to utilize the string parameter directly as a static salt marker. Defaults to false.
 * @return string Fully structured compound credential payload tracking format types, codes, and salts.
 * @todo Migrate credential production layouts over to secure standard `password_hash` with Argon2id frameworks.
 */
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

   //echo 'gl_make_password<br>:' . $password_in . '<br>s:' . $pass_salt . '<br>';

   if( defined('DEFAULT_PASSWORD_HASH_TYPE') ) $password_hash_type = DEFAULT_PASSWORD_HASH_TYPE;
   else $password_hash_type = 'HM_RMD320';

   $pass_hash = gl_compute_hash($password_hash_type, $password_in, $pass_salt);

   return $password_hash_type . ':' . $pass_hash . ':' . $pass_salt;
}

/**
 * Dynamic internal calculation loop choosing specified hashing modifiers.
 * @param string $password_hash_type Chosen verification technique code matching core case checks.
 * @param string $password_in Target credential plaintext content string.
 * @param string $pass_salt Unique cryptographic text modifier.
 * @return string Compiled string payload calculation output.
 * @todo Notice conditions exist inside cases SH52, SH32, and SH22 since variables assignments define a local `$pass_hash_in` instead of output `$pass_hash`.
 * @todo Enforce modern constant-time evaluation models to completely neutralize timing attack vectors.
 */
function gl_compute_hash($password_hash_type, $password_in, $pass_salt) {

   $pass_hash = '';
   switch ($password_hash_type) {
      case 'MD5':
         $pass_hash = md5( $password_in );
         break;
      case 'SHA1':
	     if( empty($pass_salt) ) $pass_hash = sha1( $password_in );
		 else $pass_hash = sha1( $password_in . $pass_salt );
         break;
      case 'SHA12':
         $pass_hash = sha1( sha1( $password_in . $pass_salt ) . $pass_salt );
		 break;
      case 'SHA2S':
         $pass_hash = hash('SHA256', $password_in . $pass_salt);
         break;
      case 'MDSHA':
         $pass_md5 = md5( $password_in ); //md5(haslo)
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
         $pass_hash = $pass_hash_in;
         break;
      case 'SH32':
         $pass_hash_in = hash('SHA384', $password_in . $pass_salt .
         hash('SHA384', $password_in . $pass_salt) );
         $pass_hash = $pass_hash_in;
         break;
      case 'SH22':
         $pass_hash_in = hash('SHA256', $password_in . $pass_salt .
         hash('SHA256', $password_in . $pass_salt) );
         $pass_hash = $pass_hash_in;
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