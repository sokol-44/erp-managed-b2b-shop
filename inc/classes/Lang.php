<?php
/**
 * Lang.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Lang {
   static $class = false;
   static $STR = array();

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
      define('TEXT_SPLITPAGE_BUTTON_PREV', '<<');
      define('TEXT_SPLITPAGE_BUTTON_NEXT', '>>');
      $F = Framework::g_global();

      $arr_translation = Data::get_translation($F->com);
      foreach($arr_translation as $translation) {
         define($translation['definition'], $translation['translation']);
      }
   }

   function _($string_in, $js = false) {

      $key = 'TEXT_' . str_replace( ' ', '_', strtoupper( trim( $string_in ) ) );

      if ( defined( $key ) ) {
         $string = constant($key);
      } else {
         $string = '#' . $string_in . '#';
         if( !in_array($key, Lang::$STR) ) Lang::$STR[] = $key;
      }

      if ($js) {
         $string = addslashes($string);
      }
      return $string;
   }

}





?>