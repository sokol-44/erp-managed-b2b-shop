<?php
/**
 * Lang.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Lang
 *
 * Handles translation and localization within the application.
 *
 * @designPattern Singleton
 *
 * @todo Add visibility modifiers (public, protected, private) to all properties and methods.
 * @todo Implement strict type declarations (declare(strict_types=1)).
 * @todo Replace the custom singleton pattern with a proper Dependency Injection Container (PSR-11).
 * @todo Avoid dynamic global constant definitions via define(); use a translation array or PSR-3/PSR-15 compatible translation service.
 */
class Lang {
   /**
    * @var Lang|bool The singleton instance of the Lang class.
    */
   static $class = false;

   /**
    * @var array List of missing translation keys.
    */
   static $STR = array();

   /**
    * Lang constructor.
    *
    * Initializes the language class and loads translations.
    *
    * @return void
    * @todo Add public visibility modifier.
    */
   function __construct() {

      $this->load_translation();
      self::$class = $this;
   }

   /**
    * Returns the global instance of the Lang class.
    *
    * @return Lang The active Lang instance.
    * @todo Add public static visibility modifiers. Rename to getInstance() for standard naming conventions.
    */
   static function g_global() {
      if(self::$class == false) {
         self::$class = new Lang;
      }
      return self::$class;
   }

   /**
    * Loads translations from the framework and defines them as global constants.
    *
    * @return void
    * @todo Add public visibility modifier.
    * @todo Remove hardcoded Framework and Data static calls; inject dependencies instead.
    * @todo Refactor to avoid defining global constants dynamically, which is bad practice and hard to test.
    */
   function load_translation() {
      define('TEXT_SPLITPAGE_BUTTON_PREV', '<<');
      define('TEXT_SPLITPAGE_BUTTON_NEXT', '>>');
      $F = Framework::g_global();

      $arr_translation = Data::get_translation_all($F->com);
      foreach($arr_translation as $translation) {
         define($translation['definition'], $translation['translation']);
      }
   }

   /**
    * Retrieves a specific translation by name.
    *
    * @param string $name The name of the translation to load.
    * @return string The translated string.
    *
    * @todo Add public visibility modifier and type hints (string $name): string.
    * @todo Remove static dependency on Data class.
    */
   function get_translation_load( $name ) {
      $data = Data::get_translation('load', $name);
      return $data['translation'];
   }

   /**
    * Translates a given string.
    *
    * Looks up the translation constant. If not found, returns the original string wrapped in hashes.
    * Optionally escapes the output for JavaScript.
    *
    * @param string $string_in The string to translate.
    * @param bool $js Whether to escape the string for JavaScript usage. Defaults to false.
    * @return string The translated (and optionally escaped) string.
    *
    * @todo Add public visibility modifier and type hints (string $string_in, bool $js = false): string.
    * @todo Rename method to a more descriptive name or use standard __() helper.
    * @todo Replace addslashes() with a safer escaping mechanism like json_encode() or a dedicated escaping library.
    */
   function _($string_in, $js = false) {

      $key = 'TEXT_' . str_replace( ' ', '_', strtoupper( trim( $string_in ) ) );

      if ( defined( $key ) ) {
         $string = constant($key);
      } elseif( trim( $string_in ) == '' )  {
          $string = '';
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
