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
 * Class Info
 *
 * Provides a standardized operational collection and template notification output pipeline
 * for sorting runtime operational notices, failure messages, and success logs.
 *
 * @todo Declare standard visibility attributes (public/protected/private) explicitly over all static fields.
 * @todo Implement modern native type hinting for method parameters and strictly defined method scalar return types.
 * @todo Migrate procedural logging artifacts (`echo 'INFO sadd' . $message;`) over to a clean standard PSR-3 Logger channel.
 * @todo Abstract the static state management architecture to clean up state side-effects during runtime lifecycle routines.
 */
class Info {
   /**
    * @var Info|null Cached active operational class reference context singleton mapping wrapper.
    */
   static $class;

   /**
    * @var array Categorized repository caching collection data blocks mapping messages types.
    */
   public $messages;

   /**
    * Info constructor.
    *
    * Boots standard internal tracking arrays maps configurations.
    */
   public function __construct() {
      self::$class = $this;
      $this->fill();
   }

   /**
    * Resolves and provides the standard active application validation storage container singleton.
    *
    * @return Info Managed instance layer tracking active notification parameters messages.
    */
   static function g_global() {
      if( !is_object(self::$class) ) {
         self::$class = new Info;
      }
      return self::$class;
   }

   /**
    * Acts as an instance proxies router forwarding parameters definitions directly to active instance contexts.
    *
    * @param string $method Target operational implementation method selector name keyword.
    * @param array|string $args Parameters collection lists parameters definitions values array. Defaults to an empty array.
    * @return mixed Operational result statement output from execution pipeline mapping feedback parameters, or false on errors.
    *
    * @todo Re-enable standard modern exception tracking models inside missing configuration execution evaluation forks.
    */
   static function g( $method, $args = array()) {
      if( !is_object(self::$class) ) {
         self::$class = new Info;
      }
      if(method_exists(self::$class, $method)) {
          if( !is_array($args) ) $args = array($args);
         return call_user_func_array(array(self::$class, $method), $args);
      } else {
         return false;
         //throw new Exception(sprintf('The required method "%s" does not exist for %s', $method, get_class($this)));
      }
   }

   /**
    * Restores active parameters bindings structures upon context hydration sequences executions.
    *
    * @return void
    */
   public function __wakeup() {
      self::$class = $this;
      $this->fill();
   }

   /**
    * Clear operations routine destructor endpoint layer hook.
    */
   public function __destruct() {
   }

   /**
    * Purges internal notification maps resetting all storage keys definitions to baseline empty structures.
    *
    * @return void
    */
   public function reset() {
      $this->messages = array(
         'error' => array(),
         'warning' => array(),
         'success' => array(),
         'other' => array(),
      );
   }

   /**
    * Assesses validation verification criteria verifying array state health prior to writing data rows.
    *
    * @return void
    */
   public function fill() {
      if( !isset($this->messages) || !is_array($this->messages) )
         $this->reset();
   }

   /**
    * Proxy helper writing original messages logs straight to global registry frameworks parameters.
    *
    * @param string $message Alphanumeric character sequence applied to structural descriptions files.
    * @param string $type Selection classification standard strategy indicator rule ('error', 'warning', 'success', 'other'). Defaults to 'error'.
    * @return void
    */
   static function sadd($message, $type = 'error') {
      if( $type != 'error' && $type != 'warning' && $type != 'success')
         $type = 'other';
      echo 'INFO sadd' . $message;
      $Info = Info::g_global();
      $Info->add($message, $type);
   }

   /**
    * Appends error descriptors payloads inside targeted classification keys storage spaces.
    *
    * @param string $message Narrative text description content applied onto view layout fields.
    * @param string $type Selection option rule constraint category assignment mapping rule status code. Defaults to 'error'.
    * @return void
    */
   public function add($message, $type = 'error') {
      if( $type != 'error' && $type != 'warning' && $type != 'success')
      $type = 'other';

      $this->messages[$type][] = array('text' => $message);
   }

   /**
    * Iterates available structural lists compiling HTML layout presentation rows components strings blocks.
    *
    * @param string $show_type Filtering type context parameter used to restrict collection scope selections. Defaults to ''.
    * @return string View template data content layout placeholder stream string format block.
    */
   public function output($show_type = '') {
      $output = array();

      if( Framework::is_null($show_type) ) $all = true;
      else $all = false;

      foreach ($this->messages as $type => $message_type_array) {
         if ($all || $type == $show_type) {
            foreach( $message_type_array as $message ) {
               $text = Framework::output_string( $message['text'] );
               switch( $type ) {
                  case 'error':
                     $output[] = '<div class="messageInfo messageInfoError">' . $text . '</div>';
                     break;
                  case 'warning':
                     $output[] = '<div class="messageInfo messageInfoWarning">' . $text . '</div>';
                     break;
                  case 'success':
                     $output[] = '<div class="messageInfo messageInfoSuccess">' . $text . '</div>';
                     break;
                  default:
                     $output[] = '<div class="messageInfo messageInfoOther">' . $text . '</div>';
                     break;
               }
            }
            $this->messages[$type] = array();
         }
      }

      return implode(NL, $output);
   }
}
