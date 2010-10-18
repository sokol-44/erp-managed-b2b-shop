<?php
/**
 * Lang.php Global initialization file
 * Copyright MichaÅ‚ SokoÅ‚owski 2010
 *
 * @author Micha³ Soko³owski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Lang {
   static $class = false;
   static $POST = array(), $GET = array(), $REQUEST = array();
   static $com = '';
   static $GET_array = array();

   function __construct() {

      $this->load_translation();
      self::$class = $this;
   }

   static function g_global() {
      if(self::$class == false) {
         self::$class = new Lang;
      }
      return self::$class;
   }

   function load_translation() {

      define('TEXT_SPLITPAGE_BUTTON_PREV', '<');
      define('TEXT_SPLITPAGE_BUTTON_NEXT', '>');
   }

   function _($string_in, $js = false) {

      $key = 'TEXT_' . str_replace( ' ', '_', strtoupper( trim( $string_in ) ) );

      if ( defined( $key ) ) {
         $string = constant($key);
      } else {
         $string = '#' . $string_in . '#';
      }

      if ($js) {
         $string = addslashes($string);
      }
      return $string;
   }

}





?>